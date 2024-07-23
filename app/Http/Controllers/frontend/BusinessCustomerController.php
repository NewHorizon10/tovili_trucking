<?php

namespace App\Http\Controllers\frontend;

use App\Models\TruckTypeDescription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lookup;
use App\Models\TruckType;
use App\Models\Truck;
use App\Models\Chat;
use App\Models\UserCompanyInformation;
use App\Models\UserVerificationCode;
use App\Models\Shipment;
use App\Models\ShipmentStop;
use App\Models\ShipmentStopAttchement;
use App\Models\ShipmentOffer;
use App\Models\ShipmentPrivateCustomerExtraInformation;
use App\Models\LookupDiscription;
use App\Models\ShipmentAttchement;
use App\Models\Notification;
use App\Models\RatingReview;
use App\Models\Contact;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use App\Models\NotificationTemplate;
use App\Models\NotificationAction;
use App\Models\NotificationTemplateDescription;
use App\Models\ShipmentOfferRequestRejected;
use App\Models\ShipmentDriverSchedule;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Session, DB;
use Carbon\Carbon;

class BusinessCustomerController extends Controller
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

		$notifications					=	Notification::leftjoin('shipments', 'shipments.id', 'notifications.shipment_id')
			->where('user_id', Auth::user()->id)
			->where("language_id", getAppLocaleId())
			->select(
				'notifications.*',
				'shipments.status as shipments_status',
				'shipments.request_number as request_number'
			)
			->orderByDesc("notifications.id")->get()->take(5);


		return View("frontend.customers.business.dashboard", compact('user_company_informations', 'user', 'companyType', 'ShipmentRequestList', 'ShipmentList', 'totalRequest', 'notifications'));
	}

	public function ProfileUpdate(Request $request)
	{
		$user = Auth::user();


		$validator                    =   Validator::make(
			$request->all(),

			array(
				'company_name'                  => 'required',
				'company_number'         		=> 'required|regex:' . Config('constants.COMPANY_HP_NUMBER_STRING'),
				'contact_person_name'           => 'required',
				'contact_person_phone_number'   => 'required|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
				'contact_person_email'          => 'required|email:rfc,dns',
				'company_location'              => 'required',
				'company_type'                  => 'required',
				'owner_person_name'             => 'required',
				'owner_person_phone_number'     => 'required|unique:users,phone_number,' . $user->id . '|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
				'owner_person_email'           	=> 'required|email:rfc,dns',
				'owner_person_picture'        	=> 'nullable|mimes:jpg,jpeg,png',
				'contact_person_picture'        => 'nullable|mimes:jpg,jpeg,png',
				'company_logo'                  => 'nullable|mimes:jpg,jpeg,png'
			),
			array(
				"company_name.required"                 => trans("messages.This field is required"),
				"company_number.required"        => trans("messages.This field is required"),
				"contact_person_phone_number.required"  => trans("messages.This field is required"),
				"contact_person_phone_number.regex" 	=> trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
				"contact_person_name.required"          => trans("messages.This field is required"),
				"contact_person_email.required"         => trans("messages.This field is required"),

				"owner_person_name.required"         	=> trans("messages.This field is required"),
				"owner_person_phone_number.required"    => trans("messages.This field is required"),
				"owner_person_phone_number.unique"      => trans("messages.The phone number must be unique"),
				"owner_person_phone_number.regex" 		=> trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
				"owner_person_email.required"         	=> trans("messages.This field is required"),
				"owner_person_email.email"            => trans("messages.The email must be a valid email address"),
				"owner_person_picture.mimes"         	=> trans("messages.File must be jpg, jpeg, png only"),

				"contact_person_email.email"            => trans("messages.The email must be a valid email address"),
				"contact_person_email.regex"            => trans("messages.The email must be a valid email address"),
				"company_location.required"             => trans("messages.This field is required"),
				"company_type.required"                 => trans("messages.This field is required"),
				"contact_person_picture.required"       => trans("messages.This field is required"),
				"contact_person_picture.mimes"          => trans("messages.File must be jpg, jpeg, png only"),
				"company_logo.required"                 => trans("messages.This field is required"),
				"company_logo.mimes"                    => trans("messages.File must be jpg, jpeg, png only"),
			)
		);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		} else {
			$userCompanyInformations 								= UserCompanyInformation::where('user_id', Auth::user()->id)->first();
			$userCompanyInformations->company_name 				 	= $request->company_name;
			$userCompanyInformations->company_hp_number 	 		= $request->company_number;
			$userCompanyInformations->contact_person_name 		 	= $request->contact_person_name;
			$userCompanyInformations->contact_person_email 		 	= $request->contact_person_email;
			$userCompanyInformations->contact_person_phone_number	= $request->contact_person_phone_number;
			$userCompanyInformations->company_location 			 	= $request->company_location;
			$userCompanyInformations->company_type 				 	= $request->company_type;

			if ($request->hasFile('contact_person_picture')) {
				$file = rand() . '.' . $request->contact_person_picture->getClientOriginalExtension();
				$request->file('contact_person_picture')->move(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH'), $file);
				$userCompanyInformations->contact_person_picture =  $file;
			}
			$user                                   =   User::find(Auth::user()->id);
			$user->name                         	=   $request->owner_person_name;
			$user->phone_number						=   $request->owner_person_phone_number;
			$user->email							=   $request->owner_person_email;
			$user->current_lat						=   $request->current_lat;
			$user->current_lng						=   $request->current_lng;
			if ($request->hasFile('owner_person_picture')) {
				$extension 				=	$request->file('owner_person_picture')->getClientOriginalExtension();
				$original_image_name 	=	$request->file('owner_person_picture')->getClientOriginalName();
				$fileName				=	time() . '-owner_person_picture.' . $extension;

				$folderName     		= 	strtoupper(date('M') . date('Y')) . "/";
				$folderPath				=	Config('constants.CUSTOMER_IMAGE_ROOT_PATH') . $folderName;
				if (!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777, true);
				}
				if ($request->file('owner_person_picture')->move($folderPath, $fileName)) {
					$user->image					=	$folderName . $fileName;
				}
			}
			$user->save();
			$this->last_activity_date_time(Auth::user()->id);

			if ($request->hasFile('company_logo')) {
				$file = rand() . '.' . $request->company_logo->getClientOriginalExtension();
				$request->file('company_logo')->move(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH'), $file);
				$userCompanyInformations->company_logo = $file;
			}

			if (!$userCompanyInformations->save()) {
				Session()->flash('error', trans("messages.Something went wrong"));
				return Redirect()->back();
			} else {
				Session()->flash('success', trans("messages.your_account_has_been_updated_successfully"));
				return Redirect()->back();
			}
		}
	}

	public function ShipmentRequest(Request $request)
	{
		if ($request->isMethod('POST')) {
			if ($request->multiple_stop_allow_input == 1) {
				$lstarr = $request->lst;
			} else {
				$lstarr = array(array(
					"dropoff_city" 				=> $request->dropoff_city,
					"dropoff_zip_code" 			=> $request->dropoff_zip_code,
					"dropoff_latitude" 			=> $request->dropoff_latitude,
					"dropoff_longitude" 		=> $request->dropoff_longitude,
					"recipients_phone_number" 	=> $request->recipients_phone_number,
					"name_of_the_receiver" 		=> $request->name_of_the_receiver,
					"destination_address" 		=> $request->destination_address,
					"location_feedback" 		=> $request->location_feedback,
				));
				if ($request->delivery_note == "physical_certificate") {
					$lstarr[0]['request_certificate_type'] = "physical";
				} else if ($request->delivery_note == "digital_certificate") {
					$lstarr[0]['request_certificate_type'] = "digital";
				} else if ($request->delivery_note == "no") {
					$lstarr[0]['request_certificate_type'] = "no";
				} else {
					$lstarr[0]['request_certificate_type'] = NULL;
				}

				if ($request->hasFile('certificate_number')) {
					$file = rand() . '.' . $request->certificate_number->getClientOriginalExtension();
					$request->file('certificate_number')->move(Config('constants.GALLERY_MEDIA_IMAGE_ROOT_PATH'), $file);
					$lstarr[0]['certificate_number'] = $file;
				}
			}
			$obj    =
				array(
					"shipments" =>
					array(
						"customer_id"               => Auth::user()->id,
						"status"                    => 'new',
						"shipment_type"             => $request->truck_type,
						"shipment"              	=> $request->shipment,
						"request_date"              => $request->date_of_transport,
						"request_time"              => $request->choose_time,
						"request_date_flexibility"  => $request->qty ?? 0,
						"pickup_address"            => $request->company_address,
						"request_pickup_details"    => $request->ans ? ($request->ans[$request->truck_type] ?? []) : [],
						"company_city"   			=> $request->company_city,
						"company_zip_code"   		=> $request->company_zip_code,
						"company_latitude"   		=> $request->company_latitude,
						"company_longitude"   		=> $request->company_longitude,
						"delivery_note"   			=> $request->delivery_note,
						"description"   			=> $request->description,
					),

					"shipment_stops" 					=> $lstarr ?? null,
					"shipment_attchements" 				=>
					array(
						"shipment_attchements"      => $request['image'] ?? [],
					),
				);
			$already_created_array  =   Session::get("bussiness_current_session_shipment_request_details");
			if (!empty($already_created_array) && count($already_created_array) > 0) {
				$already_created_array =   $obj;
				Session::put("bussiness_current_session_shipment_request_details", $already_created_array);
			} else {
				$current_obj  =   $obj;
				Session::put("bussiness_current_session_shipment_request_details", $current_obj);
			}
			$already_created_array  =   Session::get("bussiness_current_session_shipment_request_details");

			$objShipment 						      = new Shipment;
			$objShipment->customer_id                 = $already_created_array['shipments']['customer_id'] ?? '';
			$objShipment->status                      = $already_created_array['shipments']['status'] ?? '';
			$objShipment->request_type                = $already_created_array['shipments']['request_type'] ?? '';
			$objShipment->shipment_type                = $already_created_array['shipments']['shipment_type'] ?? '';
			$objShipment->request_date                = Carbon::createFromFormat('d-m-Y', $already_created_array['shipments']['request_date'])->format('Y-m-d');
			$objShipment->request_time                = $already_created_array['shipments']['request_time'] ?? '';
			$objShipment->pickup_address              = $already_created_array['shipments']['pickup_address'] ?? '';
			$objShipment->request_pickup_details      = json_encode($already_created_array['shipments']['request_pickup_details']);
			$objShipment->request_date_flexibility    = $already_created_array['shipments']['request_date_flexibility'] ?? '';
			$objShipment->pickup_city    			  = $already_created_array['shipments']['company_city'] ?? '';
			$objShipment->pickup_zipcode    		  = $already_created_array['shipments']['company_zip_code'] ?? '';
			$objShipment->company_latitude    		  = $already_created_array['shipments']['company_latitude'] ?? '';
			$objShipment->company_longitude    		  = $already_created_array['shipments']['company_longitude'] ?? '';
			$objShipment->description    		  	  = $already_created_array['shipments']['description'] ?? '';
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
			$objShipmentPrivateCustomerExtraInformation->location                = $already_created_array['shipments']['location'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->floor                   = $already_created_array['shipments']['floor'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->how_many_floor          = $already_created_array['shipments']['how_many_floor'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->elevators               = $already_created_array['shipments']['elevators'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->how_many_rooms          = $already_created_array['shipments']['how_many_rooms'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->need_packaging          = $already_created_array['shipments']['need_packaging'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->drop_location           = $already_created_array['shipments']['drop_location'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->drop_floor              = $already_created_array['shipments']['drop_floor'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->drop_how_many_floor     = $already_created_array['shipments']['drop_how_many_floor'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->drop_elevators          = $already_created_array['shipments']['drop_elevators'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->drop_how_many_rooms     = $already_created_array['shipments']['drop_how_many_rooms'] ?? 0;
			$objShipmentPrivateCustomerExtraInformation->save();


			if (isset($already_created_array['shipment_stops'])) {
				foreach ($already_created_array['shipment_stops'] as $shipment_stops) {
					$shipment_stops_obj													= new ShipmentStop;
					$shipment_stops_obj->shipment_id									= $objShipment->id;
					$shipment_stops_obj->dropoff_address								= $shipment_stops['destination_address'];
					$shipment_stops_obj->dropoff_zip_code								= $shipment_stops['dropoff_zip_code'];
					$shipment_stops_obj->dropoff_city									= $shipment_stops['dropoff_city'];
					$shipment_stops_obj->dropoff_latitude								= $shipment_stops['dropoff_latitude'];
					$shipment_stops_obj->dropoff_longitude								= $shipment_stops['dropoff_longitude'];
					$shipment_stops_obj->request_dropoff_contact_person_name			= $shipment_stops['name_of_the_receiver'];
					$shipment_stops_obj->request_certificate							= $shipment_stops['certificate_number'] ?? NULL;
					$shipment_stops_obj->request_certificate_type						= $shipment_stops['request_certificate_type'] ?? NULL;
					$shipment_stops_obj->request_dropoff_contact_person_phone_number	= $shipment_stops['recipients_phone_number'];
					$shipment_stops_obj->location_feedback								= $shipment_stops['location_feedback'];
					$shipment_stops_obj->save();

					if (!empty($shipment_stops['destinationImg'])) {
						foreach ($shipment_stops['destinationImg'] as $value) {
							$destinationImg_obj					= new ShipmentStopAttchement;
							$destinationImg_obj->shipment_id	= $objShipment->id;
							$destinationImg_obj->shipment_stops_id	= $shipment_stops_obj->id;
							$destinationImg_obj->attachment		= $value;
							$destinationImg_obj->save();
						}
					}
				}
			}
			
			/////////////////////Notification/////////////////////
			$this->new_shipment_request_to_company($objShipment, $already_created_array);

			Session()->flash('success', trans("messages.shipment_request_has_been_generated_successfully"));
			return redirect()->route(Auth::user()->customer_type . ".customer-dashboard");
		}

		$shipment = Lookup::where('lookup_type', 'shipment')->with(['lookupDiscription' => function ($query) {
			$query->where('language_id', getAppLocaleId());
		}])->get();

		$shipmentTime    = Lookup::where('lookup_type', "shipment-time")->with(['lookupDiscription' => function ($query) {
			$query->where(['language_id' => getAppLocaleId()]);
		}])->get();

		$truckTypeQuestionnaire = TruckType::where(['is_active' => '1', 'is_deleted' => '0', 'for_private_customers' => '0'])
			->with(
				[
					'truckTypeDiscription' => function ($query) {
						$query->where('language_id', getAppLocaleId());
					},
					'TruckTypeQuestionsList.TruckTypeQuestionDiscription' => function ($query) {
						$query->where('language_id', getAppLocaleId());
					}
				]
			)
			->get();
		return View("frontend.customers.business.shipment-request", compact('shipment', 'shipmentTime', 'truckTypeQuestionnaire'));
	}

	public function ShipmentOtpRequest(Request $request)
	{

		$already_created_array  =   Session::get("bussiness_current_session_shipment_request_details");
		if (empty($already_created_array)) {
			return redirect::route('business-shipment-request');
		}

		return View("frontend.customers.business.shipment-verify-otp");
	}

	public function checkShipmentOtpRequest(Request $request)
	{
		if ($request->isMethod('POST')) {
			$already_created_array  =   Session::get("bussiness_current_session_shipment_request_details");

			if (empty($already_created_array)) {
				return redirect::route('business-shipment-request');
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
				$UserVerificationCode = UserVerificationCode::where('phone_number', Auth::user()->phone_number)
					->where("verification_code", $otpData)
					->where("type", "shipment_request")
					->first();
				if (!$UserVerificationCode) {
					Session()->flash('error', trans("messages.Invalid_otp"));
					return Redirect()->back()->withInput();
				}
				$already_created_array  =   Session::get("bussiness_current_session_shipment_request_details");

				$objShipment 						      = new Shipment;
				$objShipment->customer_id                 = $already_created_array['shipments']['customer_id'] ?? '';
				$objShipment->status                      = $already_created_array['shipments']['status'] ?? '';
				$objShipment->request_type                = $already_created_array['shipments']['request_type'] ?? '';
				$objShipment->shipment_type                = $already_created_array['shipments']['shipment_type'] ?? '';
				$objShipment->request_date                = Carbon::createFromFormat('d-m-Y', $already_created_array['shipments']['request_date'])->format('Y-m-d');
				$objShipment->request_time                = $already_created_array['shipments']['request_time'] ?? '';
				$objShipment->pickup_address              = $already_created_array['shipments']['pickup_address'] ?? '';
				$objShipment->request_pickup_details      = json_encode($already_created_array['shipments']['request_pickup_details']);
				$objShipment->request_date_flexibility    = $already_created_array['shipments']['request_date_flexibility'] ?? '';
				$objShipment->pickup_city    			  = $already_created_array['shipments']['company_city'] ?? '';
				$objShipment->pickup_zipcode    		  = $already_created_array['shipments']['company_zip_code'] ?? '';
				$objShipment->company_latitude    		  = $already_created_array['shipments']['company_latitude'] ?? '';
				$objShipment->company_longitude    		  = $already_created_array['shipments']['company_longitude'] ?? '';
				$objShipment->delivery_note    		  	  = $already_created_array['shipments']['delivery_note'] ?? '';
				$objShipment->description    		  	  = $already_created_array['shipments']['description'] ?? '';
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
				$objShipmentPrivateCustomerExtraInformation->location                = $already_created_array['shipments']['location'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->floor                   = $already_created_array['shipments']['floor'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->how_many_floor          = $already_created_array['shipments']['how_many_floor'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->elevators               = $already_created_array['shipments']['elevators'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->how_many_rooms          = $already_created_array['shipments']['how_many_rooms'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->need_packaging          = $already_created_array['shipments']['need_packaging'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->drop_location           = $already_created_array['shipments']['drop_location'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->drop_floor              = $already_created_array['shipments']['drop_floor'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->drop_how_many_floor     = $already_created_array['shipments']['drop_how_many_floor'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->drop_elevators          = $already_created_array['shipments']['drop_elevators'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->drop_how_many_rooms     = $already_created_array['shipments']['drop_how_many_rooms'] ?? 0;
				$objShipmentPrivateCustomerExtraInformation->save();


				if (isset($already_created_array['shipment_stops'])) {
					foreach ($already_created_array['shipment_stops'] as $shipment_stops) {
						$shipment_stops_obj							= new ShipmentStop;
						$shipment_stops_obj->shipment_id			= $objShipment->id;
						$shipment_stops_obj->dropoff_address		= $shipment_stops['destination_address'];
						$shipment_stops_obj->dropoff_zip_code		= $shipment_stops['dropoff_zip_code'];
						$shipment_stops_obj->dropoff_city			= $shipment_stops['dropoff_city'];
						$shipment_stops_obj->dropoff_latitude		= $shipment_stops['dropoff_latitude'];
						$shipment_stops_obj->dropoff_longitude		= $shipment_stops['dropoff_longitude'];
						$shipment_stops_obj->request_dropoff_contact_person_name			= $shipment_stops['name_of_the_receiver'];
						$shipment_stops_obj->request_certificate	= $shipment_stops['certificate_number'] ?? NULL;;
						$shipment_stops_obj->request_dropoff_contact_person_phone_number	= $shipment_stops['recipients_phone_number'];
						$shipment_stops_obj->save();

						if (!empty($shipment_stops['destinationImg'])) {
							foreach ($shipment_stops['destinationImg'] as $value) {
								$destinationImg_obj					= new ShipmentStopAttchement;
								$destinationImg_obj->shipment_id	= $objShipment->id;
								$destinationImg_obj->shipment_stops_id	= $shipment_stops_obj->id;
								$destinationImg_obj->attachment		= $value;
								$destinationImg_obj->save();
							}
						}
					}
				}
				Session()->flash('success', trans("messages.shipment_request_has_been_generated_successfully"));
				return redirect()->route(Auth::user()->customer_type . ".customer-dashboard");
			}
		}
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


		$shipment = Shipment::where("request_number", $request_number)
			->where("customer_id", Auth::user()->id)

			->with(
				[
					'ShipmentOffers' => function ($query) {
						$query->where(['status' => 'waiting']);
						$query->where(['is_deleted' => '0']);
					},
					'ShipmentOffers.companyUser',
					'ShipmentOffers.companyUser.userCompanyInformation',
					'ShipmentOffers.TruckDetail',
					'ShipmentStop' => function ($query) {
					},
					'ShipmentStop.ShipmentStopAttchements',
					'TruckTypeDescriptions' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'RequestTimeDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'SelectedShipmentOffers' => function ($query) {
						$query->where(['status' => 'selected']);
					},
					'SelectedShipmentOffers.TruckDetail.truckDriver',
					'SelectedShipmentOffers.TruckDetail.truckDriver.userDriverDetail',
					'shipmentDriverScheduleDetails',
					'shipmentDriverScheduleDetails.truckDriver',
				]
			)
			->whereIn('status', ['new', 'offers', 'offer_chosen', 'shipment'])
			->orderBy('request_date', 'desc')
			->first();
				// dd($shipment);
		if ($shipment == null) {
			Session()->flash('error', trans("messages.shipment_request_not_found"));
			return redirect::route('business.customer-dashboard');
		}
		if ($shipment->status == "shipment") {
			Session()->flash('error', trans("messages.request_status_is_shipment"));
			return redirect::route('business.customer-dashboard');
		} else if ($shipment->status == "end") {
			Session()->flash('error', trans("messages.request_status_is_end"));
			return redirect::route('business.customer-dashboard');
		} else if ($shipment->status == "cancelled") {
			Session()->flash('error', trans("messages.request_status_is_cancelled"));
			return redirect::route('business.customer-dashboard');
		}
		$shipmentOffer = null;
		if ($shipment->SelectedShipmentOffers) {
			$shipmentOffer = ShipmentOffer::where([
				"shipment_id"	=> $shipment->id,
				"id" 			=> $shipment->SelectedShipmentOffers->id
				])
				->with([
					'companyUser',
					'companyUser.userCompanyInformation',
					'companyUser.userCompanyInformation.getCompanyRefuelingDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'TruckTypeDetail',
					'TruckDetail',

					'TruckDetail.truckDriver',
					'TruckDetail.truckDriver.userDriverDetail',
				])
				->first();
			$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk = LookupDiscription::where(['parent_id' => $shipmentOffer->companyUser->userCompanyInformation->company_tidaluk, 'language_id' => getAppLocaleId()])->first()->code ?? "";
			if($shipmentOffer->TruckDetail){
				$shipmentOffer->TruckDetail->type_of_truck = TruckTypeDescription::where(['parent_id' => $shipmentOffer->TruckDetail->type_of_truck, 'language_id' => getAppLocaleId()])->first()->name ?? "";
			}
		}
		if ($shipmentOffer && $shipmentOffer->TruckDetail && $shipmentOffer->TruckDetail->truckDriver) {
			$driver_picture = $shipmentOffer->TruckDetail->truckDriver->userDriverDetail->driver_picture;
			if (!empty($driver_picture)) {
				$driver_picture = Config('constants.DRIVER_PICTURE_PATH') . $driver_picture;
			} else {
				$driver_picture = Config('constants.NO_IMAGE_PATH');
			}

			$shipmentOffer->TruckDetail->truckDriver->userDriverDetail->driver_picture = $driver_picture ?? '';
		}


		if ($shipment->SelectedShipmentOffers) {

			$receiver_id 	= $shipment->SelectedShipmentOffers->truck_company_id;
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

				$shipment->SelectedShipmentOffers->show_chat_icon = 1;
			} else {
				$shipment->SelectedShipmentOffers->show_chat_icon = 0;
			}
			$shipment->SelectedShipmentOffers->show_chat_icon = 1;
		} else if ($shipment->ShipmentOffers->count()) {
			foreach ($shipment->ShipmentOffers as &$shipmentOffer) {

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


		return View("frontend.customers.business.shipment-request-details", compact('user', 'shipment', 'shipmentOffer'));
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
				return redirect::route('business.customer-dashboard');
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
			return redirect::route('business.customer-dashboard');
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

		Session()->flash('success', trans("messages.shipment_request_offer_removed_successfully"));
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
			->where("customer_id", Auth::user()->id)
			->with([
				'shipmentDriverScheduleDetails',
				'shipmentDriverScheduleDetails.truckDriver',

			])
			->first();




		if ($shipment == null) {
			Session()->flash('error', trans("messages.shipment_request_not_found"));
			return redirect::route('business.customer-dashboard');
		}
		if ($shipment->status == "shipment") {
			Session()->flash('error', trans("messages.request_status_is_shipment"));
			return redirect::route('business.customer-dashboard');
		} else if ($shipment->status == "end") {
			Session()->flash('error', trans("messages.request_status_is_end"));
			return redirect::route('business.customer-dashboard');
		} else if ($shipment->status == "cancelled") {
			Session()->flash('error', trans("messages.request_status_is_cancelled"));
			return redirect::route('business.customer-dashboard');
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
				'TruckTypeDetail' => function ($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				}
			])
			->first();

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

		$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk = LookupDiscription::where(['parent_id' => $shipmentOffer->companyUser->userCompanyInformation->company_tidaluk, 'language_id' => getAppLocaleId()])->first()->code ?? "";
		if($shipmentOffer->TruckDetail){
			$shipmentOffer->TruckDetail->type_of_truck = TruckTypeDescription::where(['parent_id' => $shipmentOffer->TruckDetail->type_of_truck, 'language_id' => getAppLocaleId()])->first()->name ?? "";
		}
		return View("frontend.customers.business.shipment-offer-details", compact('shipmentOffer', 'user', 'shipment'));
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
			return redirect::route('business.customer-dashboard');
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
		$shipmentOffer = ShipmentOffer::where("system_id", $system_id)
			->first();

		$shipmentOffer->status = 'selected';
		$shipmentOffer->save();

		$objShipment->status = 'offer_chosen';
		$objShipment->save();

		/////////////////////Notification/////////////////////
		$this->chosen_offer($objShipment, $objShipmentOffer);

		Session()->flash('success', trans("messages.shipment_request_offer_approved_successfully"));
		return redirect::route('business-shipment-request-details', $objShipment->request_number);
	}

	public function shipmentRequestsViewAll(Request $request)
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


		$companyType = $user_company_informations = false;

		if ($user->user_role_id == 3) {

			$user_company_informations = UserCompanyInformation::where('user_id', $user->id)->get();
			$companyType = Lookup::where('lookup_type', "company-type")->with('')->get('lookupDiscription');
		} else if ($user->user_role_id == 4) {
			$companyType = $user_company_informations = false;
		}

		$whereShipmentStatusIs = array('new', 'offers', 'offer_chosen');
		if ($request->status) {
			$whereShipmentStatusIs = array($request->status);
		}

		$ShipmentLists  =  Shipment::where("customer_id", $user->id)
			->with(
				[
					'ShipmentOffers' => function ($query) {
					},
					'ShipmentStop' => function ($query) {
					},
					'TruckTypeDescriptions' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'RequestTimeDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					}
				]
			)
			->whereIn('status', $whereShipmentStatusIs)
			->orderBy('id', 'desc');

		$inputGet				=	$request->all();
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page  =   ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
		$results = $ShipmentLists->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string =  $request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string  =   http_build_query($complete_string);
		$results->appends($inputGet)->render();

		$ShipmentList = $results;

		return View("frontend.customers.business.shipment-request-view-all", compact('user_company_informations', 'user', 'companyType', 'ShipmentList'));
	}

	public function transportationAll(Request $request)
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


		$companyType = $user_company_informations = false;

		if ($user->user_role_id == 3) {

			$user_company_informations = UserCompanyInformation::where('user_id', $user->id)->get();
			$companyType = Lookup::where('lookup_type', "company-type")->with('')->get('lookupDiscription');
		} else if ($user->user_role_id == 4) {
			$companyType = $user_company_informations = false;
		}

		$whereShipmentStatusIs = array('shipment', 'end', 'cancelled');
		$whereShipmentStatusIs1 = array('shipment');
		$whereShipmentStatusIs2 = array('end','cancelled');
		$whereShipmentStatusIs3 = array('shipment');


		if ($request->status) {
			$whereShipmentStatusIs = array($request->status);
		}

		if($request->type=='requests' && $request->type == null){
			$ShipmentLists  =  Shipment::where("customer_id", $user->id)
				->with(
					[
						'ShipmentOffers' => function ($query) {
						},
						'ShipmentStop' => function ($query) {
						},
						'TruckTypeDescriptions' => function ($query) {
							$query->where(['language_id' => getAppLocaleId()]);
						},
						'RequestTimeDescription' => function ($query) {
							$query->where(['language_id' => getAppLocaleId()]);
						}
					]
				)
				->whereIn('status', $whereShipmentStatusIs)
				->orderBy('id', 'desc');
		}elseif($request->type=='upcoming'){
				$ShipmentLists  =  Shipment::where("customer_id", $user->id)
				->with(
					[
						'ShipmentOffers' => function ($query) {
						},
						'ShipmentStop' => function ($query) {
						},
						'TruckTypeDescriptions' => function ($query) {
							$query->where(['language_id' => getAppLocaleId()]);
						},
						'RequestTimeDescription' => function ($query) {
							$query->where(['language_id' => getAppLocaleId()]);
						}
					]
				)
				->whereIn('status', $whereShipmentStatusIs1)
				->orderBy('id', 'desc');
		}elseif($request->type=='active'){
			$ShipmentLists  =  Shipment::where("customer_id", $user->id)
				->select('shipments.*')
				->with(
					[
						'ShipmentOffers', 'ShipmentStop',
						'TruckTypeDescriptions' => function ($query) {
							$query->where(['language_id' => getAppLocaleId()]);
						},
						'RequestTimeDescription' => function ($query) {
							$query->where(['language_id' => getAppLocaleId()]);
						},
					]
				)
				->leftjoin('shipment_driver_schedules', 'shipments.id', 'shipment_driver_schedules.shipment_id')
				->where('shipment_driver_schedules.shipment_status', 'start')
				->whereIn('status', $whereShipmentStatusIs3)
				->orderBy('shipments.id', 'desc');
		}elseif($request->type=='past'){
			$ShipmentLists  =  Shipment::where("customer_id", $user->id)
				->with(
					[
						'ShipmentOffers' => function ($query) {
						},
						'ShipmentStop' => function ($query) {
						},
						'TruckTypeDescriptions' => function ($query) {
							$query->where(['language_id' => getAppLocaleId()]);
						},
						'RequestTimeDescription' => function ($query) {
							$query->where(['language_id' => getAppLocaleId()]);
						}
					]
				)
				->whereIn('status', $whereShipmentStatusIs2)
				->orderBy('id', 'desc');
		}else{
			$ShipmentLists  =  Shipment::where("customer_id", $user->id)
			->with(
				[
					'ShipmentOffers' => function ($query) {
					},
					'ShipmentStop' => function ($query) {
					},
					'TruckTypeDescriptions' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'RequestTimeDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					}
				]
			)
			->whereIn('status', $whereShipmentStatusIs)
			->orderBy('id', 'desc');
		}

		$inputGet				=	$request->all();
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page  =   ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
		$results = $ShipmentLists->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string =  $request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string  =   http_build_query($complete_string);
		$results->appends($inputGet)->render();

		$ShipmentList = $results;

		return View("frontend.customers.business.transportation", compact('user_company_informations', 'user', 'companyType', 'ShipmentList'));
	}


    public function ShipmentRequestCancel(Request $request,$shipment_id){
        if(empty($shipment_id)){
			Session()->flash('error', trans("messages.invalid_requests"));
			return Redirect()->back();
        }
        
        $objShipment                                    = Shipment::where('request_number',$shipment_id)->first();
        if($objShipment == null){
			Session()->flash('error', trans("messages.invalid_requests"));
			return Redirect()->back();
        }
        if($objShipment->status == 'cancelled'){
            Session()->flash('error', trans("messages.invalid_requests"));
			return Redirect()->back();
        }
        $objShipment->status                 = 'cancelled';
        $objShipment->save();

		$objShipmentOffer =   DB::table("shipment_offers")
            ->where("shipment_id",$objShipment->id)
            ->where("status",'approved_from_company')
			->first();


        $this->shipment_cancelled_by_customer($objShipment,$objShipmentOffer);
		Session()->flash('success', trans("messages.shipment_has_been_cancelled_successfully"));
		return Redirect()->back();
    }


    public function sendProposal(Request $request){
        $formData    =    $request->all();
        if (!empty($formData)) {
            $request->replace($this->arrayStripTags($request->all()));
            $validator = Validator::make(
                $request->all(),
                [
                    'message'             => '',
                ],
                [
					'message.required'  => trans('messages.the_message_field_is_required'),
                ]
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }
			$user 					  =		Auth::user();
			if($request->proposalEmail != null){
				$user->name			  	  = 	$request->proposalName;
				$user->email			  = 	$request->proposalEmail;
				$user->save();	
			}
			$obj                      =     new Contact();
            $obj->name                =     $user->name;
            $obj->email               =     $user->email;
            $obj->phone_number        =     $user->phone_number;
            $obj->comments            =     $request->proposalMessage;
            $obj->save();

			$language_id = $this->current_language_id();
			$language_system_id = $this->language_system_id();
			$settingsEmail              =  Config('Site.email');
			$emailActions = EmailAction::where('action', '=', 'contact_enquiry_to_admin')->get()->toArray();
			$emailTemplates = EmailTemplate::where('action', '=', 'contact_enquiry_to_admin')->select(
				"name",
				"action",
				DB::raw("(select subject from email_template_descriptions where parent_id=email_templates.id AND language_id=$language_system_id) as subject"),
				DB::raw("(select body from email_template_descriptions where parent_id=email_templates.id AND language_id=$language_system_id) as body")
			)
			->get()
			->toArray();
			
			$cons = explode(',', $emailActions[0]['options']);
			$constants = array();
			foreach ($cons as $key => $val) {
				$constants[] = '{' . $val . '}';
			}
			$subject                    = $emailTemplates[0]['subject'];
            $full_name                  = $obj->name;
			$email                      = Config('Site.to_email');
			$sendData                   = array($full_name, $email, $request->proposalMessage);
			$messageBody                = str_replace($constants, $sendData, $emailTemplates[0]['body']);
			$this->sendMail($email, $full_name, $subject,$messageBody, $settingsEmail);

			Session()->flash('success', trans("messages.send_proposal_successfully"));
        } else {
			Session()->flash('error', trans("messages.Invalid Request"));
        }
		return Redirect()->back();
    }




}