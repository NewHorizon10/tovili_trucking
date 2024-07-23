<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\ShipmentOfferRequestRejected;
use App\Models\ShipmentStopAttchement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Config;
use App\Models\Lookup;
use App\Models\Shipment;
use App\Models\ShipmentAttchement;
use App\Models\ShipmentPrivateCustomerExtraInformation;
use App\Models\User;
use App\Models\UserVerificationCode;
use App\Models\UserCompanyInformation;
use App\Models\ShipmentOffer;
use App\Models\ShipmentStop;
use App\Models\LookupDiscription;
use App\Models\Truck;
use App\Models\TruckType;
use App\Models\TruckTypeDescription;
use App\Models\Chat;
use App\Models\Notification;
use App\Models\NotificationAction;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateDescription;

use Carbon\Carbon;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use App\Models\RatingReview;
use Redirect, Session, Auth;
// namespace Carbon;
class PrivateCustomerControllers extends Controller
{
	public function __construct(Request $request)
	{
		parent::__construct();
		$this->request              =   $request;
	}
	public function customerDashboard(Request $request)
	{
		$totalRequest = array();
		$totalRequest['New']	=	DB::table("shipments")
			->whereIn('status', ['new'])
			->where('customer_id', Auth::user()->id)
			->count();

		$totalRequest['Shipment']	=	DB::table("shipments")
			->whereIn('status', ['shipment', 'end', 'cancelled'])
			->where('customer_id', Auth::user()->id)
			->count();

		if ($request->wantsJson()) {
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
		} else {
			if (Auth::user()) {
				$user = Auth::user();
			}
		}

		$companyType = $user_company_informations = false;

		if ($user->user_role_id == 3) {
			$user_company_informations = UserCompanyInformation::where('user_id', $user->id)->first();
			$companyType    = Lookup::where('lookup_type', "company-type")->with('lookupDiscription')->get();
		} else if ($user->user_role_id == 4) {
			$companyType = $user_company_informations = false;
		}
		$ShipmentRequestList = Shipment::where("customer_id", $user->id)
			->with(
				[
					'ShipmentOffers' => function ($query) {
						$query->whereIn('status', ['selected', 'waiting']);
					},
					'ShipmentStop' => function ($query) {
					},
					'TruckTypeDescriptions' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'RequestTimeDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					
					'shipmentDriverScheduleDetails'

				]
			)
			->whereIn('status', ['new', 'offers', 'offer_chosen'])
			->orderBy('id', 'desc')
			->limit(10)->get();

			$ShipmentList = Shipment::where("customer_id", $user->id)
			->with(
				[
					'ShipmentOffers' => function ($query) {
						$query->where(['status' => 'approved_from_company']);
					},
					'ShipmentStop' => function ($query) {
					},
					'TruckTypeDescriptions' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'RequestTimeDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
				

					'shipmentDriverScheduleDetails'
				]
			)
			->whereIn('status', ['shipment', 'end'])
			->orderBy('id', 'desc')
			->limit(10)->get();

		$last_shipment = Shipment::where("customer_id", $user->id)
			->orderBy('id', 'DESC')->first();


		if ($last_shipment) {

			$notifications					=	Notification::leftjoin('shipments', 'shipments.id', 'notifications.shipment_id')
				->where('user_id', Auth::user()->id)
				->where("language_id", getAppLocaleId())
				->where("notifications.shipment_id", $last_shipment->id)
				->select(
					'notifications.*',
					'shipments.status as shipments_status',
					'shipments.request_number as request_number'
				)->orderByDesc("notifications.id")->get()->take(5);
		} else {
			$notifications = [];
		}
		return View("frontend.customers.private.dashboard", compact('user_company_informations', 'user', 'companyType', 'ShipmentRequestList', 'ShipmentList', 'totalRequest', 'notifications'));
	}

	public function ProfileEdit(Request $request)
	{
		$userDetails = User::where('id', Auth::user()->id)->first();
		return  View("frontend.private_customer.update_profile", compact('userDetails'));
	}

	public function ProfileUpdate(Request $request)
	{

		if ($request->isMethod('POST')) {
			$thisData = $request->all();
			$userId = Auth::user()->id;
			$user    = Auth::user();
			$validator                    =   Validator::make(
				$request->all(),
				array(
					'name'                  => "required",
					'email'                 => "nullable|email:rfc,dns",
					'phone_number'          => 'required|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
					'location'              => 'required',
				),
				array(
					"name.required"                     	=> trans("messages.The field is required"),
					'email.email'    					=> trans('messages.the_email_address_should_be_in_valid_format'),
					"phone_number.required"             	=> trans("messages.The field is required"),
					"phone_number.regex" 				=> trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
					"location.required"                 	=> trans("messages.The field is required"),
				)
			);
			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			} else {
				$user->name                         =   $request->input('name');
				$user->email                        =   isset($request->email) ? $request->email : '';
				$user->phone_number                 =   $request->phone_number;
				$user->location                     =   $request->location;
				$user->save();

				Session()->flash('success', "Your profile has been updated successfully");
			}
		}
	}
	public function CreateShipmentRequest($already_created_array)
	{
		$objShipment 						      = new Shipment;
		$objShipment->customer_id                 = $already_created_array['shipments']['customer_id'];
		$objShipment->status                      = $already_created_array['shipments']['status'];
		$objShipment->request_type                = $already_created_array['shipments']['request_type'];
		$objShipment->shipment_type               = $already_created_array['shipments']['shipment_type'];
		$objShipment->request_date                = Carbon::createFromFormat('d-m-Y', $already_created_array['shipments']['request_date'])->format('Y-m-d');
		$objShipment->request_time                = $already_created_array['shipments']['request_time'];
		$objShipment->pickup_address              = $already_created_array['shipments']['pickup_address'];
		$objShipment->request_pickup_details      = json_encode($already_created_array['shipments']['request_pickup_details']);
		$objShipment->request_date_flexibility    = $already_created_array['shipments']['request_date_flexibility'];
		$objShipment->pickup_city    			  = $already_created_array['shipments']['company_city'] ?? '';
		$objShipment->pickup_zipcode    		  = $already_created_array['shipments']['company_zip_code'] ?? '';
		$objShipment->company_latitude    		  = $already_created_array['shipments']['company_latitude'] ?? '';
		$objShipment->company_longitude    		  = $already_created_array['shipments']['company_longitude'] ?? '';
		$objShipment->save();
		$objShipment->request_number              = (100000 + $objShipment->id);
		$objShipment->save();
		if (isset($already_created_array['shipment_attchements']['shipment_attchements'])) {
			foreach ($already_created_array['shipment_attchements']['shipment_attchements'] as $value) {
				$objShipmentAttchement 				      = new ShipmentAttchement;
				$objShipmentAttchement->shipment_id       = $objShipment->id;
				$objShipmentAttchement->attachment        = $value;
				$objShipmentAttchement->save();
			}
		}

		$objShipmentPrivateCustomerExtraInformation 						 = new ShipmentPrivateCustomerExtraInformation;
		$objShipmentPrivateCustomerExtraInformation->shipment_id             = $objShipment->id;
		$objShipmentPrivateCustomerExtraInformation->location                = $already_created_array['shipment_private_customer_extra_informations']['location'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->floor                   = $already_created_array['shipment_private_customer_extra_informations']['floor'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->how_many_floor          = $already_created_array['shipment_private_customer_extra_informations']['how_many_floor'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->elevators               = $already_created_array['shipment_private_customer_extra_informations']['elevators'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->how_many_rooms          = $already_created_array['shipment_private_customer_extra_informations']['how_many_rooms'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->need_packaging          = $already_created_array['shipment_private_customer_extra_informations']['need_packaging'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->drop_location           = $already_created_array['shipment_private_customer_extra_informations']['drop_location'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->drop_floor              = $already_created_array['shipment_private_customer_extra_informations']['drop_floor'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->drop_how_many_floor     = $already_created_array['shipment_private_customer_extra_informations']['drop_how_many_floor'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->drop_elevators          = $already_created_array['shipment_private_customer_extra_informations']['drop_elevators'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->drop_how_many_rooms     = $already_created_array['shipment_private_customer_extra_informations']['drop_how_many_rooms'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->drop_city               = $already_created_array['shipment_private_customer_extra_informations']['drop_city'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->drop_zip_code           = $already_created_array['shipment_private_customer_extra_informations']['drop_zip_code'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->drop_latitude           = $already_created_array['shipment_private_customer_extra_informations']['drop_latitude'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->drop_longitude          = $already_created_array['shipment_private_customer_extra_informations']['drop_longitude'] ?? 0;
		$objShipmentPrivateCustomerExtraInformation->save();


		$shipment_stops_obj							= new ShipmentStop;
		$shipment_stops_obj->shipment_id			= $objShipment->id;
		$shipment_stops_obj->dropoff_address		= $already_created_array['shipment_stops']['dropoff_address'];
		$shipment_stops_obj->dropoff_zip_code		= $already_created_array['shipment_stops']['dropoff_zip_code'];
		$shipment_stops_obj->dropoff_city			= $already_created_array['shipment_stops']['dropoff_city'];
		$shipment_stops_obj->dropoff_latitude		= $already_created_array['shipment_stops']['dropoff_latitude'];
		$shipment_stops_obj->dropoff_longitude		= $already_created_array['shipment_stops']['dropoff_longitude'];
		$shipment_stops_obj->save();

		$new_already_created_array = array(
			"shipment_stops" => array(
				array(
					"destination_address"   	=> $already_created_array['shipment_stops']['dropoff_address'],
					"dropoff_zip_code"    		=> $already_created_array['shipment_stops']['dropoff_zip_code'],
					"dropoff_city"    			=> $already_created_array['shipment_stops']['dropoff_city'],
					"recipients_phone_number"   => Auth::user()->name,
					"name_of_the_receiver"    	=> Auth::user()->phone_number,
				)
			),
			"shipments" => array(
				"shipment_type" => $already_created_array['shipments']['shipment_type']
			)

		);

		/////////////////////Notification/////////////////////
		$this->new_shipment_request_to_company($objShipment, $new_already_created_array);
	}

	public function ShipmentRequest(Request $request)
	{

		if ($request->isMethod('POST')) {
			$obj    =
				array(
					"shipments" =>
					array(
						"status"                    => 'new',
						"request_type"              => $request->shipment_servise,
						"shipment_type"             => TruckType::find($request->shipment_servise)->map_truck_type_id,
						"request_date"              => $request->request_date,
						"request_time"              => $request->request_time,
						"pickup_address"            => $request->pickup_location,
						"request_pickup_details"    => $request->number_of_item_description ?? [],
						"request_date_flexibility"  => $request->qty ?? 0,
						"company_city"   			=> $request->company_city,
						"company_zip_code"   		=> $request->company_zip_code,
						"company_latitude"   		=> $request->company_latitude,
						"company_longitude"   		=> $request->company_longitude,
					),
					"shipment_attchements" =>
					array(
						"shipment_attchements"      => $request['image'] ?? [],
					),
					"shipment_private_customer_extra_informations" =>
					array(
						"location"                  => $request->pickup_location,
						"floor"                     => $request->pickup_floor,
						"how_many_floor"            => $request->how_many_floor,
						"elevators"                 => $request->elevators,
						"how_many_rooms"            => $request->how_many_rooms,
						"need_packaging"            => $request->need_packaging,
						"drop_location"             => $request->drop_location,
						"drop_floor"                => $request->drop_floor,
						"drop_how_many_floor"       => $request->drop_how_many_floor,
						"drop_elevators"            => $request->drop_elevator,
						"drop_how_many_rooms"       => $request->drop_how_many_rooms,
						"drop_city"   	            => $request->drop_city,
						"drop_zip_code"             => $request->drop_zip_code,
						"drop_latitude"   	        => $request->drop_latitude,
						"drop_longitude"            => $request->drop_longitude,
					),
					"shipment_stops" 					=>
					array(
						"dropoff_address"             => $request->drop_location,
						"dropoff_city"   	            => $request->drop_city,
						"dropoff_zip_code"             => $request->drop_zip_code,
						"dropoff_latitude"   	        => $request->drop_latitude,
						"dropoff_longitude"            => $request->drop_longitude,
					),
				);
			$already_created_array  =   Session::get("current_session_shipment_request_details");
			if (!empty($already_created_array) && count($already_created_array) > 0) {
				$already_created_array =   $obj;
				Session::put("current_session_shipment_request_details", $already_created_array);
			} else {
				$current_obj  =   $obj;
				Session::put("current_session_shipment_request_details", $current_obj);
			}
			$already_created_array  =   Session::get("current_session_shipment_request_details");

			if (Auth::user()) {

				$shipment = Shipment::where("customer_id", Auth::user()->id)
					->whereIn("status", ["new", "offers", "offer_chosen"])
					->first();
				if ($shipment) {
					Session()->flash('error', trans("messages.please_complete_previous_shipment_request_first"));
					return Redirect()->back()->withInput();
				}

				$already_created_array['shipments']['customer_id'] = Auth::user()->id;
				$this->CreateShipmentRequest($already_created_array);

				return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
					->withSuccess(trans('messages.shipment_request_has_been_generated_successfully'));

			} else {
				return redirect::route('private.shipment.verify.mobile');
			}
		}
		if (Auth::user()) {
			if (Auth::user()->customer_type == 'private') {
				$shipmentDetail = Shipment::where("customer_id", Auth::user()->id)
					->WhereIn("status", ["new", "offers", "offer_chosen"])->get();

				if ($shipmentDetail->count() > 0) {
					return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
						->withError(trans("messages.please_complete_previous_shipment_request_first"));
				}
			}
		}
		Session::forget("current_session_shipment_request_details");
		$products = [];
		$request->session()->put('shipment_request_details', $products);

		$shipmentTime    = Lookup::where('lookup_type', "shipment-time")->with(['lookupDiscription' => function ($query) {
			$query->where(['language_id' => getAppLocaleId()]);
		}])->get();

		$truckTypes    = TruckType::where(['is_active' => '1', 'is_deleted' => '0'])
			->where('for_private_customers', 1)
			->with(
				[
					'truckTypeDiscription' => function ($query) {
						$query->where('language_id', getAppLocaleId());
					}
				]
			)
			->get();

		return View("frontend.customers.private.shipment-request", compact('shipmentTime', 'truckTypes'));
	}

	public function ShipmentRequestMobileVerify(Request $request)
	{
		$already_created_array  =   Session::get("current_session_shipment_request_details");
		if (empty($already_created_array)) {
			return redirect::route('private-shipment-request');
		}

		if ($request->isMethod('post')) {
			$validator = Validator::make(
				array(
					'phone_number' 							=>  $request->phone_number
				),
				array(
					'phone_number'     						=> 'required|not_in:0000000000,2222222222,1111111111,3333333333,4444444444,5555555555,6666666666,7777777777,8888888888,9999999999|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
				),
				array(
					'phone_number.required'                	=> trans('messages.the_phone_number_field_is_required'),
					'phone_number.not_in'                	=> trans('messages.the_selected_phone_number_is_invalid'),
					"phone_number.regex"                    => trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
				)
			);
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			} else {
				$phone_number	=	$request->input('phone_number');
				$userDetail  	=	User::where('phone_number', $phone_number)->where("is_deleted", 0)->first();
				if ($userDetail) {
					if ($userDetail->user_role_id != 2) {
						Session()->flash('error', trans("messages.you_cannot_create_a_private_shipment_request_with_this_number_because_it_is_registered_with_another_business_or_company"));
						return redirect::back();
					} else if ($userDetail->customer_type == "business") {
						Session()->flash('error', trans("messages.you_cannot_create_a_private_shipment_request_with_this_number_because_it_is_registered_with_another_business_or_company"));
						return redirect::back();
					}
					$phone_number = $userDetail->phone_number;

					$shipment = Shipment::where("customer_id", $userDetail->id)
						->whereIn("status", ["new", "offers", "offer_chosen"])
						->first();
					if ($shipment) {
						Session()->flash('error', trans("messages.please_complete_previous_shipment_request_first"));
						return Redirect()->back()->withInput();
					}
				}




				Session::put('userTypePhoneData', $phone_number);
				$verification_code					=	'9999'; //$this->getVerificationCodes();//'9999';//$this->getVerificationCodes();
				UserVerificationCode::where('phone_number', $phone_number)->where("type", "shipment_request")->delete();

				$obj 								= 	new UserVerificationCode;
				$obj->phone_number  				= 	$phone_number;
				$obj->type   						= 	'shipment_request';
				$obj->verification_code				= 	$verification_code;
				$obj->save();

				Session()->flash('success', "OTP send successfully");
				return redirect::route('private.shipment.otp.request');
			}
		}

		if ($request->resend_otp) {
			$phone_number	=	Session::get('userTypePhoneData');
			$userDetail  	=	User::where('phone_number', $phone_number)->where("is_deleted", 0)->first();
			$verification_code					=	'9999';
			UserVerificationCode::where('phone_number', $phone_number)->where("type", "shipment_request")->delete();
			$obj 								= 	new UserVerificationCode;
			$obj->phone_number  				= 	$phone_number;
			$obj->type   						= 	'shipment_request';
			$obj->verification_code				= 	$verification_code;
			$obj->save();

			Session()->flash('success', "OTP send successfully");
			return redirect::route('private.shipment.otp.request');
		}
		return view('frontend.customers.private.shipment-verify-mobile');
	}

	public function ShipmentOtpRequest(Request $request)
	{
		$already_created_array  =   Session::get("current_session_shipment_request_details");
		if (empty($already_created_array)) {
			return redirect::route('private-shipment-request');
		}
		return View("frontend.customers.private.shipment-verify-otp");
	}
	public function checkShipmentOtp(Request $request)
	{
		$already_created_array  =   Session::get("current_session_shipment_request_details");
		if (empty($already_created_array)) {
			return redirect::route('private-shipment-request');
		}
		$otpData = array(
			'fullOtp' => $request->otp1 . $request->otp2 . $request->otp3 . $request->otp4
		);

		$validator = Validator::make(
			$otpData,
			[
				'fullOtp' => 'required|digits:4'
			],
			[
				'fullOtp.required' => trans('messages.this_otp_field_is_required'),
				'fullOtp.digits' => trans('messages.please_fill_all_otp_field'),
			]
		);
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		} else {
			$otpData = $request->otp1 . $request->otp2 . $request->otp3 . $request->otp4;
			$UserVerificationCode = UserVerificationCode::where('phone_number', Session::get('userTypePhoneData'))
				->where("verification_code", $otpData)
				->where("type", "shipment_request")
				->first();
			if (!$UserVerificationCode) {
				Session()->flash('error', trans("messages.Invalid_otp"));
				return Redirect()->back()->withInput();
			}

			$phone_number	=	Session::get('userTypePhoneData');
			$userDetail  	=	User::where('phone_number', $phone_number)->where("is_deleted", 0)->first();

			if ($userDetail) {
				$already_created_array  =   Session::get("current_session_shipment_request_details");

				$already_created_array['shipments']['customer_id'] = $userDetail->id;
				Auth::login($userDetail);

				$this->CreateShipmentRequest($already_created_array);

				return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
					->withSuccess(trans('messages.shipment_request_has_been_generated_successfully'));
			} else {
				return redirect::route('private.shipment-create-password')
					->withSuccess(trans('messages.otp_has_been_successfully_verified'));
			}
		}
	}
	public function shipmentCreatePassword(Request $request)
	{
		$already_created_array  =   Session::get("current_session_shipment_request_details");
		if (empty($already_created_array)) {
			return redirect::route('private-shipment-request');
		}
		if ($request->isMethod('POST')) {
			$request->validate(
				[
					'new_password'     => 'required|string|min:4',
					'confirm_password' => 'required|same:new_password',
				],
				[
					"new_password.required"		=> trans("messages.new_password_field_is_required"),
					"new_password.between"		=> trans("messages.Password should be 4 digits"),
					"new_password.min"		=> trans("messages.password_should_be_minimum_4_characters"),
					"confirm_password.required"	=> trans("messages.confirm_password_field_is_required"),
					"confirm_password.same"		=> trans("messages.The confirm password must be the same as the password"),
				]
			);
			$userDetails 				=	User::where(['phone_number' => Session::get('userTypePhoneData')])->first();
			if (!$userDetails) {
				$userDetails  			= 	new User;
			}
			$userDetails->user_role_id 	=  	2;
			$userDetails->customer_type	=  	'private';
			$userDetails->name			=  	'';
			$userDetails->email			=  	'';
			$userDetails->phone_number 	=  	Session::get('userTypePhoneData');
			$userDetails->password     	=  	Hash::make($request->new_password);
			if (!$userDetails->save()) {
				Session()->flash('error', trans("messages.something_went_wrong"));
				return Redirect()->back();
			}
			$system_id  				=   1000 + $userDetails->id;
			$userDetails->system_id 	=  	$system_id;
			$userDetails->save();
			if (Auth::attempt(['phone_number' => $userDetails->phone_number, 'password' => $request->new_password])) {

				$already_created_array['shipments']['customer_id'] = Auth::user()->id;
				$this->CreateShipmentRequest($already_created_array);

				return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
					->withSuccess(trans('messages.your_account_has_been_successfully_created'));
			} else {
				return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
					->withError(trans('messages.sorry_you_have_entered_invalid_credentials'));
			}
		}
		return view('frontend.customers.private.shipment-create-password');
	}

	public function ShipmentRequestDetails(Request $request, $request_number)
	{
		if ($request->wantsJson()) {
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
		} else {
			if (Auth::user()) {
				$user = Auth::user();
			}
		}

		$ShipmentDetails = Shipment::where('customer_id', $user->id)
			->where("request_number", $request_number)
			->with(
				[
					'ShipmentOffers' => function ($query) {
						$query->where(['status' => 'waiting']);
						$query->where(['is_deleted' => '0']);
					},
					'ShipmentOffers.TruckDetail',
					'ShipmentPrivateCustomer_ExtraInformation' => function ($query) {
					},
					'RequestTimeDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'TruckTypeDescriptions' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'TruckTypeDescriptionsPrivate' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'SelectedShipmentOffers' => function ($query) {
						$query->where(['status' => 'selected']);
					},
					'shipmentDriverScheduleDetails',
					'shipmentDriverScheduleDetails.truckDriver',
					'shipment_attchement'
				]
			)
			->whereIn('status', ['new', 'offers', 'offer_chosen', 'shipment'])
			->first();


		if ($ShipmentDetails->status == "shipment") {
			Session()->flash('error', trans("messages.request_status_is_shipment"));
			return redirect::route('private.customer-dashboard');
		} else if ($ShipmentDetails->status == "end") {
			Session()->flash('error', trans("messages.request_status_is_end"));
			return redirect::route('private.customer-dashboard');
		} else if ($ShipmentDetails->status == "cancelled") {
			Session()->flash('error', trans("messages.request_status_is_cancelled"));
			return redirect::route('private.customer-dashboard');
		}

		$shipmentOffer = null;
		if ($ShipmentDetails->SelectedShipmentOffers) {
			$shipmentOffer = ShipmentOffer::where([
				"shipment_id"	=> $ShipmentDetails->id,
				"id" 			=> $ShipmentDetails->SelectedShipmentOffers->id
			])
				->with([
					'companyUser',
					'companyUser.userCompanyInformation',
					'companyUser.userCompanyInformation.getCompanyRefuelingDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'TruckTypeDetail' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'TruckDetail.truckDriver',
				])
				->first();
			$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk = LookupDiscription::where(['parent_id' => $shipmentOffer->companyUser->userCompanyInformation->company_tidaluk, 'language_id' => getAppLocaleId()])->first()->code ?? "";
			if($shipmentOffer->TruckDetail){
				$shipmentOffer->TruckDetail->type_of_truck = TruckTypeDescription::where(['parent_id' => $shipmentOffer->TruckDetail->type_of_truck, 'language_id' => getAppLocaleId()])->first()->name ?? "";
			}
		}


		if ($ShipmentDetails == null) {
			Session()->flash('error', trans("messages.shipment_request_not_found"));
			return redirect::route('private.customer-dashboard');
		}

		if ($ShipmentDetails->SelectedShipmentOffers) {

			$receiver_id 	= $ShipmentDetails->SelectedShipmentOffers->truck_company_id;
			$modelId 		= $user->id;

			$chat_intiate_status = Chat::where(function ($query) use ($modelId, $receiver_id) {
				$query->orWhere(function ($query) use ($modelId, $receiver_id) {
					$query->where("chats.sender_id", $modelId);
					$query->where("chats.receiver_id", $receiver_id);
				});
				$query->orWhere(function ($query) use ($modelId, $receiver_id) {
					$query->where("chats.receiver_id", $modelId);
					$query->where("chats.sender_id", $receiver_id);
				});
			})
				->where('channel_id', '0')
				->select('chats.*')
				->orderBy('chats.id', 'DESC')
				->first();

			if (!empty($chat_intiate_status)) {

				$ShipmentDetails->SelectedShipmentOffers->show_chat_icon = 1;
			} else {
				$ShipmentDetails->SelectedShipmentOffers->show_chat_icon = 0;
			}
			$ShipmentDetails->SelectedShipmentOffers->show_chat_icon = 1;
		} else if ($ShipmentDetails->ShipmentOffers->count()) {
			foreach ($ShipmentDetails->ShipmentOffers as &$shipmentOffer) {
				$receiver_id 	= $shipmentOffer->truck_company_id;
				$modelId 		= $user->id;

				$chat_intiate_status = Chat::where(function ($query) use ($modelId, $receiver_id) {
					$query->orWhere(function ($query) use ($modelId, $receiver_id) {
						$query->where("chats.sender_id", $modelId);
						$query->where("chats.receiver_id", $receiver_id);
					});
					$query->orWhere(function ($query) use ($modelId, $receiver_id) {
						$query->where("chats.receiver_id", $modelId);
						$query->where("chats.sender_id", $receiver_id);
					});
				})
					->where('channel_id', '0')
					->select('chats.*')
					->orderBy('chats.id', 'DESC')
					->first();

				if (!empty($chat_intiate_status)) {

					$shipmentOffer->show_chat_icon = 1;
				} else {
					$shipmentOffer->show_chat_icon = 0;
				}
				$shipmentOffer->show_chat_icon = 1;
				$shipmentOffer->rating = RatingReview::where("truck_company_id", $receiver_id)
					->selectRaw("SUM(overall_rating) as overall_rating")
					->first();

				if ($shipmentOffer->rating->overall_rating) {
					$count  = RatingReview::where("truck_company_id", $receiver_id)->count();
					$shipmentOffer->rating->overall_rating = $shipmentOffer->rating->overall_rating / $count;
				}
				$shipmentOffer->rating->overall_rating = round($shipmentOffer->rating->overall_rating);
			}
		}

		return View("frontend.customers.private.shipment-request-details", compact('user', 'ShipmentDetails', 'shipmentOffer'));
	}

	public function ShipmentRequestDetailsDelete(Request $request, $request_number)
	{
		$Shipment = Shipment::where('request_number', $request_number)
			->where('customer_id', Auth::user()->id)
			->first();
		if ($Shipment == null) {
			Session()->flash('error', trans("messages.shipment_request_not_found"));
			return redirect::route('private.customer-dashboard');
		} else {

			if ($Shipment->status == "new") {
				ShipmentOfferRequestRejected::where("shipment_id", $Shipment->id)->delete();
				ShipmentStop::where("shipment_id", $Shipment->id)->delete();
				ShipmentStopAttchement::where("shipment_id", $Shipment->id)->delete();
				ShipmentPrivateCustomerExtraInformation::where("shipment_id", $Shipment->id)->delete();
				ShipmentAttchement::where("shipment_id", $Shipment->id)->delete();
				ShipmentOffer::where("shipment_id", $Shipment->id)->delete();
				Shipment::where('id', $Shipment->id)->delete();
				Session()->flash('success', trans("messages.shipment_request_removed_successfully"));
				return redirect::route('private.customer-dashboard');
			} else {
				Session()->flash('error', trans("messages.you_cannot_delete_this_request_because_offers_have_been_generated_on_it"));
				return Redirect()->back();
			}
		}
	}

	public function ShipmentRequestDestroy(Request $request, $system_id)
	{
		if ($request->wantsJson()) {
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
		} else {
			if (Auth::user()) {
				$user = Auth::user();
			}
		}
		$shipmentOffer = ShipmentOffer::where("system_id", $system_id)->first();

		$shipment = Shipment::where("id", $shipmentOffer->shipment_id)
			->where("customer_id", Auth::user()->id)
			->first();

		if ($shipment == null) {
			Session()->flash('error', trans("messages.shipment_request_not_found"));
			return redirect::route('private.customer-dashboard');
		}

		$shipmentOffer->status = 'rejected';
		$shipmentOffer->save();

		$shipmentOffers = ShipmentOffer::where(
			[
				"shipment_id"	=>	$shipmentOffer->shipment_id,
				"is_deleted"	=>	0,
				"status"		=>	"waiting",

			]
		)->get();
		if ($shipmentOffers->count() == 0) {
			$shipment->status = 'new';
			$shipment->save();
		}

		$this->shipment_rejected_by_user($shipment,$shipmentOffer);

		Session()->flash('success', trans("messages.shipment_request_deleted_successfully"));
		return Redirect()->back();
	}

	public function ShipmentOfferDetails(Request $request, $system_id)
	{
		if ($request->wantsJson()) {
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
		} else {
			if (Auth::user()) {
				$user = Auth::user();
			}
		}
		$shipmentOffer = ShipmentOffer::where("system_id", $system_id)->first();

		$shipment = Shipment::where("id", $shipmentOffer->shipment_id)
			->with(
				[
					'TruckTypeDescriptionsPrivate' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'shipmentDriverScheduleDetails',
					'shipmentDriverScheduleDetails.truckDriver',
				]
			)
			->where("customer_id", Auth::user()->id)
			->first();


		if ($shipment->status == "shipment") {
			Session()->flash('error', trans("messages.request_status_is_shipment"));
			return redirect::route('private.customer-dashboard');
		} else if ($shipment->status == "end") {
			Session()->flash('error', trans("messages.request_status_is_end"));
			return redirect::route('private.customer-dashboard');
		} else if ($shipment->status == "cancelled") {
			Session()->flash('error', trans("messages.request_status_is_cancelled"));
			return redirect::route('private.customer-dashboard');
		}


		if ($shipment == null) {
			Session()->flash('error', trans("messages.shipment_request_not_found"));
			return redirect::route('private.customer-dashboard');
		}

		$shipmentOffer = ShipmentOffer::where("system_id", $system_id)
			->with([
				'companyUser',
				'companyUser.userCompanyInformation',
				'companyUser.userCompanyInformation.getCompanyRefuelingDescription' => function ($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'TruckDetail',
				'TruckDetail.truckDriver',
			])
			->first();
		$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk = LookupDiscription::where(['parent_id' => $shipmentOffer->companyUser->userCompanyInformation->company_tidaluk, 'language_id' => getAppLocaleId()])->first()->code ?? "";
		if($shipmentOffer->TruckDetail){
			$shipmentOffer->TruckDetail->type_of_truck = TruckTypeDescription::where(['parent_id' => $shipmentOffer->TruckDetail->type_of_truck, 'language_id' => getAppLocaleId()])->first()->name ?? "";
		}

		if ($shipmentOffer) {

			$receiver_id 	= $shipmentOffer->truck_company_id;
			$modelId 		= $user->id;

			$chat_intiate_status = Chat::where(function ($query) use ($modelId, $receiver_id) {
				$query->orWhere(function ($query) use ($modelId, $receiver_id) {
					$query->where("chats.sender_id", $modelId);
					$query->where("chats.receiver_id", $receiver_id);
				});
				$query->orWhere(function ($query) use ($modelId, $receiver_id) {
					$query->where("chats.receiver_id", $modelId);
					$query->where("chats.sender_id", $receiver_id);
				});
			})
				->where('channel_id', '0')
				->select('chats.*')
				->orderBy('chats.id', 'DESC')
				->first();

			if (!empty($chat_intiate_status)) {

				$shipmentOffer->show_chat_icon = 1;
			} else {
				$shipmentOffer->show_chat_icon = 0;
			}
			$shipmentOffer->show_chat_icon = 1;
		}

		return View("frontend.customers.private.shipment-offer-details", compact('shipmentOffer', 'user', 'shipment'));
	}

	public function ShipmentOfferApproved(Request $request, $system_id)
	{
		if ($request->wantsJson()) {
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
		} else {
			if (Auth::user()) {
				$user = Auth::user();
			}
		}
		$objShipmentOffer = ShipmentOffer::where("system_id", $system_id)->first();
		$objShipment = Shipment::where("id", $objShipmentOffer->shipment_id)
			->where("customer_id", Auth::user()->id)
			->first();
		if ($objShipment->status == "offer_chosen") {
			Session()->flash('error', trans("messages.offer_has_already_been_selected_on_this_shipment"));
			return redirect::back();
		} 
		if ($objShipment == null) {
			Session()->flash('error', trans("messages.shipment_request_not_found"));
			return redirect::route('private.customer-dashboard');
		} else if ($objShipmentOffer->status == 'rejected') {
			Session()->flash('error', trans("messages.this_offer_has_been_rejected"));
			return redirect::back();
		} else if ($objShipmentOffer->status == 'approved_from_company') {
			Session()->flash('error', trans("messages.this_offer_is_already_approved_from_company"));
			return redirect::back();
		} else if ($objShipmentOffer->status == 'selected') {
			Session()->flash('error', trans("messages.this_offer_is_already_selected"));
			return redirect::back();
		}

		$objShipmentOffer = ShipmentOffer::where("system_id", $system_id)
			->first();
		$objShipmentOffer->status = 'selected';
		$objShipmentOffer->save();

		$objShipment->status = 'offer_chosen';
		$objShipment->save();

		/////////////////////Notification/////////////////////
		$this->chosen_offer($objShipment, $objShipmentOffer);

		Session()->flash('success', trans("messages.shipment_request_offer_approved_successfully"));
		return redirect::route('private-shipment-request-details', $objShipment->request_number);
	}
}
