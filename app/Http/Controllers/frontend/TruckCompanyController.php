<?php

namespace App\Http\Controllers\frontend;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DatePeriod;
use DateTime;
use PDF;
use DateInterval;
use App\Models\User;
use App\Models\UserCompanyInformation;
use App\Models\Lookup;
use App\Models\UserVerificationCode;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use App\Models\UserDeviceToken;
use App\Models\HomepageSlider;
use App\Models\HomepageSliderDescriptions;
use App\Models\TruckCompanySubscription;
use App\Models\UserDriverDetail;
use App\Models\OurServices;
use App\Models\AboutUs;
use App\Models\Achievment;
use App\Models\Client;
use App\Models\Team;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\TruckType;
use App\Models\TruckCompanyRequestSubscription;
use App\Models\Truck;
use App\Models\Shipment;
use App\Models\ShipmentDriverSchedule;

use App\Jobs\SendMail;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Helper, Config;
use Stripe;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Cache, Cookie, Input, Mail, mongoDate, Response, Session, URL;
use Illuminate\Validation\Rules\Password;

class TruckCompanyController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		// $this->request              =   $request;
	}		

	public function index(Request $request){
		session()->forget('truck_registration_data');
		session()->forget('userTypePhoneData');
		
		$companyType   = Lookup::where('lookup_type', "company-type")->with(['lookupDiscription' => function($query) {
	        $query->where(['language_id' => getAppLocaleId()]);
        }])->get(); 
		$tidalukCompanyType   = Lookup::where('lookup_type', "tidaluk-company-type")->where('is_active', 1)->with(['lookupDiscription' => function($query) {
	        $query->where(['language_id' => getAppLocaleId()]);
        }])->get(); 
		$fuelingType   = Lookup::where('lookup_type', "fueling-methods")->where('is_active', 1)->with(['lookupDiscription' => function($query) {
	        $query->where(['language_id' => getCurrentLanguage()]);
        }])->get();
		
		
		return View("frontend.truck-company-registration.login",compact('companyType','tidalukCompanyType','fuelingType'));
	}

	public function truckRegistrationstep2(Request $request,){
		session()->put('admin_applocale',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $thisData = $request->all();
            $validator                    =   Validator::make(
                $request->all(), 
                array(
                    'Contactname'                   => "required",
                    'email'                         => "nullable|email",
                    'Password'                      => 'required|string|min:4',
                    'ConfirmPassword'              	=> 'required|same:Password',
                    'Contactphone'                  => 'required|unique:users,phone_number|digits:10', 
                    'company_name'                  => 'required',
                    'company_number'        		=> 'required',

                    'Contactemail'          		=> 'required|email',
                    'company_address'              	=> 'required',
                    'picture__input1'        		=> 'nullable|mimes:jpg,jpeg,png',
                    'picture__input'                => 'nullable|mimes:jpg,jpeg,png'
                ), 
                array( 
    
                    "Password.required"                     => trans("messages.The field is required"),
                    "Password.min"                          => trans("messages.password_should_be_minimum_4_characters"),
					"Password.between"          			=> trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "Password.regex"          				=> trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),
                    "ConfirmPassword.required"             	=> trans("messages.The field is required"),
                    "ConfirmPassword.same"                 	=> trans("messages.The confirm password not matched with password"),
                    "company_name.required"                 => trans("messages.The field is required"),
                    "Contactphone.required"  				=> trans("messages.The field is required"),
					"Contactphone.digits"           	=> trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
                    "company_number.required"  				=> trans("messages.The field is required"),
                    "Contactname.required"         		    => trans("messages.The field is required"),
                    "Contactemail.required"         		=> trans("messages.This field is required"),
                    "Contactemail.email"            		=> trans("messages.The email must be a valid email address"),
                    "Contactemail.regex"            		=> trans("messages.The email must be a valid email address"),
                    "company_address.required"             	=> trans("messages.The field is required"),
                    "Company_type.required"                 => trans("messages.The field is required"),
                    "email.email"                           => trans("messages.The email must be a valid email addres"),
                    "email.unique"                          => trans("messages.The email must be unique"),
                    "picture__input1.required"       		=> trans("messages.The field is required"),
                    "Contactphone.unique"  					=> trans("messages.Mobile number already in use"),

                    "picture__input1.mimes"          		=> trans("messages.File must be jpg, jpeg, png only"),
                    "picture__input.required"               => trans("messages.The field is required"),
                    "picture__input.mimes"                  => trans("messages.File must be jpg, jpeg, png only"),
                )
            );
           
		if ($validator->fails()) {
	        return response()->json(['errors' => $validator->errors()], 422);
		}else{
						
			$file1 = $request->file('picture__input1');
			$file2 = $request->file('picture__input');

			if ($file1) {
				$filename1 = rand() . '.' . $file1->getClientOriginalExtension();
				$file1->move(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH'), $filename1);
				$request->merge(['contact_person' => $filename1]);
			}

			if ($file2) {
				$filename2 = rand() . '.' . $file2->getClientOriginalExtension();
				$file2->move(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH'), $filename2);
				$request->merge(['company_logo' => $filename2]);
			}

			$request->flash();
			session(['truck_registration_data' => $request->except(['picture__input1', 'picture__input'])]);
			$truckRegistrationData = session('truck_registration_data');
			$phone_number = $truckRegistrationData['Contactphone'];

			Session::put('userTypePhoneData', $phone_number);
			$userDetail  	=	User::where('phone_number', $phone_number)->where("is_deleted", 0)->first();
			if(!empty($userDetail)){
			}
			$verification_code					=	'9999';
			UserVerificationCode::where('phone_number', $phone_number)->where("type", "account_verification")->delete();
			$obj 								= 	new UserVerificationCode;
			$obj->phone_number  				= 	$phone_number;
			$obj->type   						= 	'account_verification';
			$obj->verification_code				= 	$verification_code;
			$obj->save();
			
			$redirectUrl = '/verify-otp-truck-company?num='.$phone_number; // Replace with your desired URL
			return response()->json(['redirectUrl' => $redirectUrl]);

		}
	}

	public function truckRegistrationstep2checkMobile(Request $request){
		session()->put('admin_applocale',$_SERVER['HTTP_ACCEPT_LANGUAGE']);

            $validator                    =   Validator::make(
                $request->all(), 
                array(
                    'company_number'        			=> 'required|unique:users,phone_number|regex:'.Config('constants.COMPANY_HP_NUMBER_STRING'),
                ), 
                array( 
                    "company_number.required"  			=> trans("messages.The field is required"),
                    "company_number.unique"  			=> trans("messages.Mobile number already in use"),
                )
            );
           
		if ($validator->fails()) {
	        return response()->json(['errors' => $validator->errors()], 422);
		}
	}

	public function verifyOtptruck(Request $request)
	{	

		if($request->num){
			Session::put('userTypePhoneData', $request->num);

			$truckRegistrationData = session('truck_registration_data');
			$truckRegistrationData = collect($truckRegistrationData); 
			session(['truck_registration_data' => $truckRegistrationData ]);

			return redirect::route('verifyOtptruck');
		}
		if (!Session::get('userTypePhoneData')) {
			return redirect::route('sign-up');
		}
		if (!$request->session()->get('userTypePhoneData')) {
			Session()->flash('error', trans("messages.Otp expired"));
			return redirect::route('login');
		}
		return View("frontend.truck-company-registration.verify-otp");
	}

	public function verifyMobiletruck(Request $request)
	{	
		if (!Session::get('userTypePhoneData')) {
			return redirect::route('sign-up');
		}
		if ($request->isMethod('post')) {
			$validator = Validator::make(
				$request->all(),
				array(
					'phone_number'     						=> 'required|numeric|digits:10|unique:users',
				),
				array(
					'phone_number.required'                	=> 'The phone number field must be required',
					"phone_number.digits"           	=> trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
				)
			);
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			} else {
				$phone_number	=	$request->input('phone_number');
				Session::put('userTypePhoneData', $phone_number);
				$userDetail  	=	User::where('phone_number', $phone_number)->where("is_deleted", 0)->first();
				$verification_code					=	'9999';
				UserVerificationCode::where('phone_number', $phone_number)->where("type", "account_verification")->delete();
				$obj 								= 	new UserVerificationCode;
				$obj->phone_number  				= 	$phone_number;
				$obj->type   						= 	'account_verification';
				$obj->verification_code				= 	$verification_code;
				$obj->save();

				return redirect::route('verify-mobile-truck-company')->withSuccess(trans("messages.otp_has_been_sent_successfully_on_your_phone_number"));
			}
		} else {
			return View("frontend.truck-company-registration.verify-otp");
		}
	}

	public function checkOtp(Request $request)
	{
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
				->where("type", "account_verification")
				->first();

			if (!$UserVerificationCode) {
				Session()->flash('error', trans("messages.Invalid_otp"));
				return Redirect()->back()->withInput();
			}

			Session()->flash('success', trans("messages.otp_has_been_successfully_verified"));
			return redirect()->route("truck-company-registration-step-4");
		}
	}

	public function truckcompanyregistrationstep4(Request $request){
		$truckRegistrationData = session('truck_registration_data');
		if(!$truckRegistrationData){
			return redirect()->route("truckCcompanyRegistration");
		}
		return View("frontend.truck-company-registration.truck-company-registration-step-4");
	}

	public function truckCompanyRegistration(Request $request){
		if ($request->isMethod('post')) {
			$validator                    =   Validator::make(
				$request->all(), 
				array(
					'number_of_trucks'                   => "required",
				), 
				array( 
					"number_of_trucks.required"                     => trans("messages.This field is required"),
				)
			);
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}
			
			$truckRegistrationData = session('truck_registration_data');
			if(!$truckRegistrationData){
				return redirect()->route("plan");
			}
		
			$truckRegistrationData = collect($truckRegistrationData); 
			$truckRegistrationData = $truckRegistrationData->merge(['number_of_trucks' => $request->number_of_trucks]);
	
			session(['truck_registration_data' => $truckRegistrationData ]);
			return redirect()->route("truck-company-registration");
		}


		$truckRegistrationData = session('truck_registration_data');
		if(!$truckRegistrationData){
			return redirect()->route("plan");
		}
		$truckRegistrationData = collect($truckRegistrationData); 

		$number_of_trucks = $truckRegistrationData['number_of_trucks'];

		$truckTypeQuestionnaire = TruckType::where(
				[
					'is_active'			=> '1',
					'is_deleted'		=> '0',
					'map_truck_type_id'	=> '0'
				]
			)
		->with(
			[
				'truckTypeDiscription' => function ($query) {
					$query->where('language_id', getAppLocaleId());
				},
				'TruckTypeQuestionsList.TruckTypeQuestionDiscription'=>function ($query) {
					$query->where('language_id', getAppLocaleId());
				}
			]
		)
		->get();	

		return View("frontend.truck-company-registration.truck-company-registration",compact('number_of_trucks','truckTypeQuestionnaire'));
	}

	public function truckcompanyregistrationstep5(Request $request){
		$truckRegistrationData = session('truck_registration_data');
		if(!$truckRegistrationData){
			return redirect()->route("plan");
		}
		$truckRegistrationData = collect($truckRegistrationData); 
		$inpurData = [];
		if($request->isMethod('post')){
			foreach ($request->truck_system_number as $key => $value) {
				if (
					$value === null ||
					$request->type_of_truck[$key] === null 
				) {
					continue; 
				}
			
				// File upload and move
				$file1 = $request->file('truck_insurance_picture.' . $key);
				$file2 = $request->file('truck_licence_number.' . $key);
				$filename1 = null;
				$filename2 = null;
			
				if ($file1) {
					$filename1 = rand() . '.' . $file1->getClientOriginalExtension();
					$file1->move(Config('constants.TRUCK_INSURANCE_IMAGE_ROOT_PATH'), $filename1);
				}
			
				if ($file2) {
					$filename2 = rand() . '.' . $file2->getClientOriginalExtension();
					$file2->move(Config('constants.TRUCK_LICENCE_NUMBER_IMAGE_ROOT_PATH'), $filename2);
				}
			
				// Prepare data for insertion
				$inpurData[] = [
					'truck_system_number' 					=> $value,
					'type_of_truck' 						=> $request->type_of_truck[$key],
				
					'truck_insurance_expiration_date' 		=> $request->truck_insurance_expiration_date[$key],
					'truck_licence_expiration_date' 		=> $request->truck_licence_expiration_date[$key],
					'basketman' 							=> isset($request->basketman[$key]) ? 1 : 0,
					'truck_insurance_picture' 				=> $filename1,
					'truck_licence_number' 					=> $filename2,
					"questionnaire"    								=> ( isset($request->ans[$key]) && $request->ans[$key] ? ($request->ans[$key][$request->type_of_truck[$key]] ?? []) : []) ,
				];

			}
		}

		$truckRegistrationData = $truckRegistrationData->merge(['number_of_trucks_details' => $inpurData]);
		$truckRegistrationData = session(['truck_registration_data' => $truckRegistrationData ]);
		$truckRegistrationData = session('truck_registration_data')->toArray();
		
		
		$truckRegistrationData = session('truck_registration_data')->toArray();
		
			
		$userData                               =   User::where(["phone_number"=>$truckRegistrationData['Contactphone']])->first();
		if(!$userData){
			$user                               =   new User;
		}else{
			$user                               =   User::where(["phone_number"=>$truckRegistrationData['Contactphone']])->first();
		}
		$user->user_role_id                 =   Config('constants.ROLE_ID.TRUCK_COMPANY_ID');
		$user->name                         =   $truckRegistrationData['Contactname'];
		$user->email                        =   $truckRegistrationData['Contactemail'];
		$user->phone_number                 =   $truckRegistrationData['Contactphone'];
		$user->customer_type                =   'business';
		$user->password                     =   Hash::make($truckRegistrationData['Password']);
		$user->image 						= 	$truckRegistrationData['contact_person']??"";
		$user->language 					= 	getAppLocaleId();

		$user->save();

		$system_id  =   1000+$user->id;
		User::where("id",$user->id)->update(array("truck_company_id"=>$user->id,"system_id"=>$system_id));


		if(!$userData){
			$driverDetails                      =   new UserDriverDetail;
		}else{
			$driverDetails                      =   UserDriverDetail::where(['user_id'=>$user->id])->first();
			if(!$driverDetails){
				$driverDetails                      =   new UserDriverDetail;
			}
		}
		
		
		$driverDetails->user_id             =   $user->id;
		$driverDetails->driver_picture      =   '';
		$driverDetails->licence_picture     =   '';
		$driverDetails->save();

		if(!$userData){
			$companyObj                      =   new UserCompanyInformation;
		}else{
			$companyObj                      =   UserCompanyInformation::where(['user_id'=>$user->id])->first();
			if(!$companyObj){
				$companyObj                      =   new UserCompanyInformation;
			}
		}

		$companyObj->company_name                       = $truckRegistrationData['company_name'];
		$companyObj->company_hp_number              	= $truckRegistrationData['company_number'];
		$companyObj->contact_person_name                = $truckRegistrationData['Contactname'];
		$companyObj->contact_person_email               = $truckRegistrationData['Contactemail'];
		$companyObj->company_location                   = $truckRegistrationData['company_address'];
		$companyObj->latitude          					= $truckRegistrationData['lat'];
		$companyObj->longitude         					= $truckRegistrationData['lng'];
		$companyObj->company_type                       = $truckRegistrationData['Company_type'] ?? 0;
		$companyObj->user_id                            = $user->id;
		$companyObj->contact_person_phone_number        = $truckRegistrationData['Contactphone'];
		$companyObj->contact_person_picture 			= $truckRegistrationData['contact_person'] ?? '';
		$companyObj->company_logo 						= $truckRegistrationData['company_logo'] ?? '';
		$companyObj->company_description 				= $truckRegistrationData['company_description'];
		$companyObj->company_refueling 					= $truckRegistrationData['ContactRefueling'];
		$companyObj->company_tidaluk 					= $truckRegistrationData['ContactTidaluk'];
		$companyObj->company_trms 						= $truckRegistrationData['company_terms'];
		$companyObj->save();

		if(!$userData){
			DB::table('trucks')->where(["truck_company_id"=>$user->id])->delete();
		}

		
		foreach($truckRegistrationData['number_of_trucks_details'] as $key => $value){
			DB::table('trucks')->insert(
				array(
					'truck_company_id' 					=> $user->id,
					'truck_system_number' 				=> $value['truck_system_number'],
					'company_refueling' 				=> $truckRegistrationData['ContactRefueling'],
					'company_tidaluk' 					=> $truckRegistrationData['ContactTidaluk'],
					'type_of_truck' 					=> $value['type_of_truck'],
					'basketman' 						=> $value['basketman'],
					'truck_licence_number' 				=> $value['truck_licence_number'] ?? "" ,
					'truck_licence_expiration_date' 	=> ($value['truck_licence_expiration_date'] ? Carbon::createFromFormat('m/d/Y', ($value['truck_licence_expiration_date']))->format('Y-m-d') : null),
					'truck_insurance_picture' 			=> $value['truck_insurance_picture'] ?? "",
					'truck_insurance_expiration_date' 	=> ($value['truck_insurance_expiration_date'] ? Carbon::createFromFormat('m/d/Y', ($value['truck_insurance_expiration_date']))->format('Y-m-d') : null),
					'is_active' 						=> 1,
					'is_deleted' 						=> 0,
					'driver_id' 						=> 0,
					'questionnaire'						=> json_encode($value['questionnaire']) ,
				)
			);
		}

		$PlanDetails                    = Plan::where("id", 1)->where('is_deleted', 0)->where('is_active', 1)->first();
		if($PlanDetails){
			if(!$userData){
				TruckCompanySubscription::where(["truck_company_id"=>$user->id])->delete();
			}
	
			$startTime = Carbon::now();
			if ($PlanDetails->type == '0') {
				$Months = 1;
			} elseif ($PlanDetails->type == '1') {
				$Months = 3;
			} elseif ($PlanDetails->type == '2') {
				$Months = 6;
			} elseif ($PlanDetails->type == '3') {
				$Months = 12;
			}

			$endTime = $startTime->copy()->addMonths($Months);

			$TruckCompanySubscription                    = new TruckCompanySubscription;
			$TruckCompanySubscription->truck_company_id  = $user->id;
			$TruckCompanySubscription->is_free           = $PlanDetails->is_free;
			$TruckCompanySubscription->plan_id           = $PlanDetails->id;
			$TruckCompanySubscription->price             = $PlanDetails->price;
			$TruckCompanySubscription->discount          = 0;
			$TruckCompanySubscription->total_price       = $PlanDetails->price;
			$TruckCompanySubscription->type              = $PlanDetails->type;
			$TruckCompanySubscription->column_type       = $PlanDetails->column_type;
			$TruckCompanySubscription->total_truck       = count($truckRegistrationData['number_of_trucks_details']);
			$TruckCompanySubscription->status            = "activate";
			$TruckCompanySubscription->start_time        = $startTime;
			$TruckCompanySubscription->end_time          = $endTime;

			$TruckCompanySubscription->save();
		}
		$logData=array(
			'record_id'=>$user->id,
			'module_name'=>'User',
			'action_name' => 'create',
			'action_description' => 'Create User Account',
			'record_url' => route('users.show',base64_encode($user->id)),
			'user_agent' => $request->header('User-Agent'),
			'browser_device' => '',
			'location' => '',
			'ip_address' => $request->ip()
		);


		session()->forget('truck_registration_data');
		session()->forget('userTypePhoneData');
 
		Session()->flash('success',trans("messages.your_account_has_been_added_successfully"));
		return Redirect()->route('thankyou');
	}
	
	public function thankyou(Request $request){

		return View("frontend.truck-company-registration.thankyou");

	}

	public function willSendScheduledMessages(){
        \Log::info('willSendScheduledMessages---');
		$this->planEndedWithinTwoOrCurrentDay();
		$this->sendNotification30DaysBeforeExpiry();
		$this->sendNotification1DayAfterExpiration();
		$this->sendShipmentFutureNotification();
		$this->when_digital_and_no_certificate_uploaded();
		$this->shipmentsReviewAfterScheduleEnd();

	}

    public function planEndedWithinTwoOrCurrentDay(){
		$currentDate = now();
		$afterTwoDays = $currentDate->copy()->addDays(2); 

		$TruckCompanySubscriptionTwoDays = TruckCompanySubscription::with('companyUser')->where('status', 'activate')
			->where('two_days_before_mail_send', 0)
			->whereDate('end_time', '>', $currentDate->toDateString()) // End time after current date
			->whereDate('end_time', '<=', $afterTwoDays->toDateString()) // End time within next two days
			->take(20)
			->get();

		$TruckCompanySubscriptionTwoDaysId = TruckCompanySubscription::where('status', 'activate')
		->where('two_days_before_mail_send', 0)
		->whereDate('end_time', '>', $currentDate->toDateString()) // End time after current date
		->whereDate('end_time', '<=', $afterTwoDays->toDateString()) // End time within next two days
		->take(20)
		->pluck('truck_company_id')
		->toArray();

		TruckCompanyRequestSubscription::whereIn('truck_company_id', $TruckCompanySubscriptionTwoDaysId)->delete();

		$planAddArray = array();

		$settingsEmail 	= 	Config::get("Site.from_email");

		$emailActions 	= 	EmailAction::where('action','=','company_subscription_plan_expire_date')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','company_subscription_plan_expire_date')->get(array('name','subject','action','body', 'mail_enable'))-> toArray();
		$cons 			= 	explode(',',$emailActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];

		if($TruckCompanySubscriptionTwoDays->count() > 0){

			foreach($TruckCompanySubscriptionTwoDays as $key => $value){
				$type       = $value->type;
				$columnType = $value->column_type;

				if ($type == '0') {
					$typeData = trans('messages.monthly');
				} elseif ($type == '1') {
					$typeData = trans('messages.quarterly');
				} elseif ($type == '2') {
					$typeData = trans('messages.Half Yearly');
				} else {
					$typeData = trans('messages.Yearly');
				}
		
				if ($columnType == '0') {
					$columntypeData = trans('messages.Up to 5 Trucks');
				}  else {
					$columntypeData = trans('messages.More then 5');
				}

				$characters = 'abcdefghijklmnopqrstuvwxyz';
				$validateString = '';

				for ($i = 0; $i < strlen($characters); $i++) {
					$validateString .= $characters[rand(0, strlen($characters) - 1)];
				}

                 $planAddArray[]  = array(
   
	                'truck_company_id' => $value->truck_company_id, 
					'plan_id' => $value->plan_id, 
					'price' => $value->price, 
					'discount' => $value->discount, 
					'total_price' => $value->total_price, 
					'type' => $value->type, 
					'column_type' => $value->column_type, 
					'total_truck' => 0, 
					'validate_string' => $validateString, 
 
                 );
				
				if($value->is_free == 0){
					$paymentUrl = route('plan-subscription', $validateString);
				}else{
					$paymentUrl = route('subscribe-plan', $validateString);
				}
				
				$truckCompany = $value->companyUser->toArray();

				$Information = array(
					'name'         => $truckCompany['name'],
					'email'        => $truckCompany['email'],
					'phone_number' => $truckCompany['phone_number'],
					'price'        => $value->price,
					'discount'     => $value->discount,
					'total_price'  => round($value->total_price, 2),
					'type'         => $typeData,
					'column_type'  => $columntypeData,
					'expireDate'   => $afterTwoDays,
					'paymentUrl'   => $paymentUrl,
				);

				$this->company_subscription_plan_expire_date($value->truck_company_id, $Information, $truckCompany);
				
				$full_name 		= 	$truckCompany['name'];
				$email 			=	$truckCompany['email'];
				$messageBody 	= 	str_replace($constants, $Information, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $email,
					"name" => $full_name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1){
				SendMail::dispatch($requestData)->onQueue('send_mail');
				}

			}

			if (!empty($planAddArray)) {
			    TruckCompanyRequestSubscription::insert($planAddArray);
			}

			TruckCompanySubscription::whereIn('truck_company_id', $TruckCompanySubscriptionTwoDaysId)->where('two_days_before_mail_send', 0)->update(['two_days_before_mail_send'=>1]);

		}

		$TruckCompanySubscriptionCurrent = TruckCompanySubscription::with('companyUser')->where('status', 'activate')
		->where('same_day_mail_send', 0)
		->whereDate('end_time', '=', $currentDate->toDateString())
		->take(20)
		->get();

		$TruckCompanySubscriptionCurrentId = TruckCompanySubscription::where('status', 'activate')
		->where('same_day_mail_send', 0)
		->whereDate('end_time', '=', $currentDate->toDateString())
		->take(20)
		->pluck('truck_company_id')
		->toArray();

		TruckCompanyRequestSubscription::whereIn('truck_company_id', $TruckCompanySubscriptionCurrentId)->delete();

		if($TruckCompanySubscriptionCurrent->count() > 0){

			foreach($TruckCompanySubscriptionCurrent as $value){

				$type       = $value->type;
				$columnType = $value->column_type;
				
				if ($type == '0') {
					$typeData = trans('messages.monthly');
				} elseif ($type == '1') {
					$typeData = trans('messages.quarterly');
				} elseif ($type == '2') {
					$typeData = trans('messages.Half Yearly');
				} else {
					$typeData = trans('messages.Yearly');
				}
		
				
				if ($columnType == '0') {
					$columntypeData = trans('messages.Up to 5 Trucks');
				}  else {
					$columntypeData = trans('messages.More then 5');
				}

				$characters = 'abcdefghijklmnopqrstuvwxyz';
				$validateString = '';

				for ($i = 0; $i < strlen($characters); $i++) {
					$validateString .= $characters[rand(0, strlen($characters) - 1)];
				}
				
				$planAddArray[]  = array(
   
	                'truck_company_id' => $value->truck_company_id, 
					'plan_id' => $value->plan_id, 
					'price' => $value->price, 
					'discount' => $value->discount, 
					'total_price' => $value->total_price, 
					'type' => $value->type, 
					'column_type' => $value->column_type, 
					'total_truck' => 0, 
					'validate_string' => $validateString, 
 
                 );
			

				if($value->is_free == 0){
					$paymentUrl = route('plan-subscription', $validateString);
				}else{
					$paymentUrl = route('subscribe-plan', $validateString);
				}
					
				$truckCompany = $value->companyUser->toArray();

				$Information = array(
					'name'         => $truckCompany['name'],
					'email'        => $truckCompany['email'],
					'phone_number' => $truckCompany['phone_number'],
					'price'        => $value->price,
					'discount'     => $value->discount,
					'total_price'  => round($value->total_price, 2),
					'type'         => $typeData,
					'column_type'  => $columntypeData,
					'expireDate'   => $currentDate,
					'paymentUrl'   => $paymentUrl,
				);

				$this->company_subscription_plan_expire_date($value->truck_company_id, $Information, $truckCompany);

				$messageBody 	= 	str_replace($constants, $Information, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompany['email'],
					"name" => $truckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1){
				SendMail::dispatch($requestData)->onQueue('send_mail');
				}

			}

			if (!empty($planAddArray)) {
			    TruckCompanyRequestSubscription::insert($planAddArray);
			}

			TruckCompanySubscription::whereIn('truck_company_id', $TruckCompanySubscriptionCurrentId)->where('same_day_mail_send', 0)->update(['same_day_mail_send'=>1]);
		}

		// Deactivate the last one day plan

		$currentDate = now();
		$lastDay = $currentDate->copy()->subDays(1)->format("Y-m-d");	
		$expireYesterdayTruckCompanySubscription = TruckCompanySubscription::whereDate('end_time', $lastDay)->where('status', 'activate')->get();
		if($expireYesterdayTruckCompanySubscription->count()){
			TruckCompanySubscription::whereDate('end_time', $lastDay)->update(['status' => 'deactivate']);		
		}
	}
	public function shipmentsReviewAfterScheduleEnd(){
       $pastOneDay = now()->subHours(24)->format('Y-m-d H:i:s');
       $add10Minutes = now()->subHours(24)->addMinutes(10)->format('Y-m-d H:i:s');
       $usersLists = ShipmentDriverSchedule::leftjoin('shipments', 'shipment_driver_schedules.shipment_id', 'shipments.id')
			->leftJoin('shipment_offers' ,'shipments.id', 'shipment_offers.shipment_id')
			->leftJoin('users' ,'users.id', 'shipments.customer_id')
            ->where('shipments.send_notification_shipment_review_after_schedule_end', 0)
            ->where('users.user_role_id', 2)
            ->where('users.customer_type', 'business')
            ->where('shipment_driver_schedules.shipment_status', 'end')
            ->where('shipment_offers.status', 'approved_from_company')
			->whereBetween('shipment_driver_schedules.shipment_actual_end_time', [$pastOneDay, $add10Minutes])
            ->select(
				'shipments.*',
				'shipment_driver_schedules.shipment_id',
				'shipment_driver_schedules.truck_company_id',
				'shipment_driver_schedules.shipment_actual_end_time',
				'shipment_driver_schedules.shipment_status',
				'shipment_driver_schedules.truck_id as offers_truck_id',
				'users.customer_type',
				'users.name',
				'users.email',
				'users.phone_number',
				'users.language',
				'shipment_offers.price',
				'shipment_offers.extra_time_price',
				'shipment_offers.description as offers_description',
				'shipment_offers.status as offers_status',
				'shipment_offers.truck_id as offers_truck_id',

			)
            ->get(); 

		$userCompanyInformation = array();

		foreach($usersLists as $list){

			if($list->customer_type == "business" ){
				$userCompanyInformation = UserCompanyInformation::where("user_id",$list->customer_id)->first()->toArray();
			}else{
				$userCompanyInformation = array(
					"user_id"						=>  $list->customer_id,
					"contact_person_name"           =>  $list->name,
					"contact_person_email"          =>  $list->email,
					"contact_person_phone_number"   =>  $list->phone_number
				);
			}
			
			if($list->request_type){
				$shipmentType = $list->request_type;
			}else{
				$shipmentType = $list->shipment_type;
			}
			$shipmentObj = Shipment::find($list->id);
			$this->shipmentReviewAfterScheduleEnd($list, $userCompanyInformation, $shipmentType);
			$shipmentObj->send_notification_shipment_review_after_schedule_end = 1;
            $shipmentObj->save();
		}

    }

	public function sendNotification30DaysBeforeExpiry(){
		$currentNewDate = now();
		$currentDate = now();
		$end30Days = clone $currentDate;
		$end30Days   = $currentDate->addDays(30);

		$expiryInsuranceTruckDocuments = Truck::with('truckCompanyDetails', 'truckTypeDetails')->whereDate('truck_insurance_expiration_date', '<=', $end30Days)
			->whereDate('truck_insurance_expiration_date', '>=', $currentNewDate)
			->where('send_insurance_notification_before_30_days', 0)
			->orderBy('trucks.id', 'desc')
			->take(20)
			->get();
		$expiryLicenceTruckDocuments = Truck::with('truckCompanyDetails', 'truckTypeDetails')->whereDate('truck_licence_expiration_date', '<=', $end30Days)
			->whereDate('truck_licence_expiration_date', '>=', $currentNewDate)
			->where('send_licence_notification_before_30_days', 0)
			->orderBy('trucks.id', 'desc')
			->take(20)
			->get();
			

		$expiryInsuranceCount = $expiryInsuranceTruckDocuments->count();
		$expiryLicenceCount = $expiryLicenceTruckDocuments->count();
		// Insurance Expiry
		if($expiryInsuranceCount > 0){
			$emailActions 	= 	EmailAction::where('action','=','truck_insurance_notification_before_30_days')->get()->toArray();
			$emailTemplates = 	EmailTemplate::where('action','=','truck_insurance_notification_before_30_days')->get(array('name','subject','action','body', 'mail_enable'))-> toArray();
			$cons 			= 	explode(',',$emailActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			$subject 		= 	$emailTemplates[0]['subject'];

			foreach($expiryInsuranceTruckDocuments as $value){
				$Information = array(
					'truckCompanyName'     => $value->truckCompanyDetails ?->name, 
					'truckSystemNumber'    => $value->truck_system_number,
					'companyRefueling'     => $value->companyRefueling ?->code,
					'truckType'            => $value->truckTypeDetails ?->name,
					'companyTidulak'       => $value->companyTidulakDetails ?->code,
					'expiryInsuranceDate'  => $value->truck_insurance_expiration_date,
				);
				
				$TruckCompany = User::where("id", $value->truck_company_id)->first()->toArray();
				$this->sendTruckNotificationInsuranceExpiryBefore30Days($Information, $value->truck_company_id, $TruckCompany);

				Truck::where('id', $value->id)->where('send_insurance_notification_before_30_days', 0)->update(['send_insurance_notification_before_30_days'=>1]);
				$messageBody 	= 	str_replace($constants, $Information, $emailTemplates[0]['body']);
				$requestData = [
					"email" => $TruckCompany['email'],
					"name" => $TruckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1){
				SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}

		}

		// Licence Expiry
		if($expiryLicenceCount > 0){
			$emailActions 	= 	EmailAction::where('action','=','truck_licence_notification_before_30_days')->get()->toArray();
			$emailTemplates = 	EmailTemplate::where('action','=','truck_licence_notification_before_30_days')->get(array('name','subject','action','body', 'mail_enable'))-> toArray();
			$cons 			= 	explode(',',$emailActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			$subject 		= 	$emailTemplates[0]['subject'];

			foreach($expiryLicenceTruckDocuments as $value){
				$Information = array(
					'truckCompanyName'     => $value->truckCompanyDetails ?->name, 
					'truckSystemNumber'    => $value->truck_system_number,
					'companyRefueling'     => $value->companyRefueling ?->code,
					'truckType'            => $value->truckTypeDetails ?->name,
					'companyTidulak'       => $value->companyTidulakDetails ?->code,
					'expiryLicenceDate'  => $value->truck_licence_expiration_date,
				);

				$TruckCompany = User::where("id", $value->truck_company_id)->first()->toArray();
				$this->sendTruckNotificationLicenceExpiryBefore30Days($Information, $value->truck_company_id, $TruckCompany);

				Truck::where('id', $value->id)->where('send_licence_notification_before_30_days', 0)->update(['send_licence_notification_before_30_days'=>1]);
				$messageBody 	= 	str_replace($constants, $Information, $emailTemplates[0]['body']);
				$requestData = [
					"email" => $TruckCompany['email'],
					"name" => $TruckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1){
				SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}

		}
	}

	public function sendNotification1DayAfterExpiration(){
		$newDate = now();
		$currentDate = clone $newDate;
		$end1Day  = $newDate->subDays(1);


		$expiryInsuranceTruckList = Truck::with('truckCompanyDetails', 'truckTypeDetails')->whereDate('truck_insurance_expiration_date', '=', $end1Day)
			->where('send_insurance_notification_after_expiry_of_one_day', 0)
			->orderBy('trucks.id', 'desc')
			->take(20)
			->get();
		$expiryLicenceTruckList = Truck::with('truckCompanyDetails', 'truckTypeDetails')->whereDate('truck_licence_expiration_date', '=', $end1Day)
			->where('send_licence_notification_after_expiry_of_one_day', 0)
			->orderBy('trucks.id', 'desc')
			->take(20)
			->get();



		$expiryInsuranceCount = $expiryInsuranceTruckList->count();
		$expiryLicenceCount = $expiryLicenceTruckList->count();
		
		// Insurance Expiry
		if($expiryInsuranceCount > 0){
			$emailActions 	= 	EmailAction::where('action','=','truck_insurance_notification_after_expired')->get()->toArray();
			$emailTemplates = 	EmailTemplate::where('action','=','truck_insurance_notification_after_expired')->get(array('name','subject','action','body', 'mail_enable'))-> toArray();
			$cons 			= 	explode(',',$emailActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			$subject 		= 	$emailTemplates[0]['subject'];

			foreach($expiryInsuranceTruckList as $value){
				$Information = array(
					'truckCompanyName'     => $value->truckCompanyDetails ?->name, 
					'truckSystemNumber'    => $value->truck_system_number,
					'companyRefueling'     => $value->companyRefueling ?->code,
					'truckType'            => $value->truckTypeDetails ?->name,
					'companyTidulak'       => $value->companyTidulakDetails ?->code,
					'expiryInsuranceDate'  => $value->truck_insurance_expiration_date,
				);
				
				$TruckCompany = User::where("id", $value->truck_company_id)->first()->toArray();

				$this->sendTruckNotificationInsuranceAfterExpired($Information, $value->truck_company_id, $TruckCompany);

				Truck::where('id', $value->id)->where('send_insurance_notification_after_expiry_of_one_day', 0)->update(['send_insurance_notification_after_expiry_of_one_day'=>1]);

				$messageBody 	= 	str_replace($constants, $Information, $emailTemplates[0]['body']);
				$requestData = [
					"email" => $TruckCompany['email'],
					"name" => $TruckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1){
				SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}

		}

		// Licence Expiry
		if($expiryLicenceCount > 0){
			$emailActions 	= 	EmailAction::where('action','=','truck_licence_notification_after_expired')->get()->toArray();
			$emailTemplates = 	EmailTemplate::where('action','=','truck_licence_notification_after_expired')->get(array('name','subject','action','body', 'mail_enable'))-> toArray();
			$cons 			= 	explode(',',$emailActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			$subject 		= 	$emailTemplates[0]['subject'];

			foreach($expiryLicenceTruckList as $value){
				$Information = array(
					'truckCompanyName'     => $value->truckCompanyDetails ?->name, 
					'truckSystemNumber'    => $value->truck_system_number,
					'companyRefueling'     => $value->companyRefueling ?->code,
					'truckType'            => $value->truckTypeDetails ?->name,
					'companyTidulak'       => $value->companyTidulakDetails ?->code,
					'expiryLicenceDate'  => $value->truck_licence_expiration_date,
				);

				$TruckCompany = User::where("id", $value->truck_company_id)->first()->toArray();
				$this->sendTruckNotificationLicenceAfterExpired($Information, $value->truck_company_id, $TruckCompany);

				Truck::where('id', $value->id)->where('send_licence_notification_after_expiry_of_one_day', 0)->update(['send_licence_notification_after_expiry_of_one_day'=>1]);

				$messageBody 	= 	str_replace($constants, $Information, $emailTemplates[0]['body']);
				$requestData = [
					"email" => $TruckCompany['email'],
					"name" => $TruckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1){
				SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}

		}
	}

	public function sendShipmentFutureNotification(){
		$curDate = now();
		$currentDate = $curDate->copy();
		$one_day     = $curDate->addDay(1);


		$oneDayBeforeShipment = Shipment::whereDate('request_date', $one_day)->where('status', 'shipment')->where('send_notification_one_day_before_shipment_starts', 0)->orderBy('id', 'desc')->take(20)->get();
		$oneDayBeforeShipmentCount = $oneDayBeforeShipment->count();

		if($oneDayBeforeShipmentCount > 0){
			foreach($oneDayBeforeShipment as $objShipment){
				$objShipmentDriverSchedule = ShipmentDriverSchedule::where('shipment_id', $objShipment->id)->first();
				$objShipmentOffer =   DB::table("shipment_offers")->where("shipment_id",$objShipment->id)->where("status",'approved_from_company')->first();
				$this->send_notification_one_day_before_shipment_starts($objShipment, $objShipmentOffer, $objShipmentDriverSchedule);
				Shipment::where("id",$objShipment->id)->update(['send_notification_one_day_before_shipment_starts' => 1]);
			}
		}
		$startOneHour = $currentDate->copy()->addHour(1)->format('Y-m-d H:i:s');
        $endOneHour = $currentDate->copy()->addHour(1)->addMinutes(10)->format('Y-m-d H:i:s');
		$oneHourBeforeShipment = Shipment::whereBetween('request_date', [$startOneHour, $endOneHour])->where('status', 'shipment')->where('send_notification_one_hour_before_shipment_starts', 0)->orderBy('id', 'desc')->take(20)->get();
		$oneHourBeforeShipmentCount = $oneHourBeforeShipment->count();
		if($oneHourBeforeShipmentCount > 0){
			foreach($oneHourBeforeShipment as $objShipment){
				$objShipmentDriverSchedule = ShipmentDriverSchedule::where('shipment_id', $objShipment->id)->first();
				$objShipmentOffer =   DB::table("shipment_offers")->where("shipment_id",$objShipment->id)->where("status",'approved_from_company')->first();
				$this->send_notification_one_hour_before_shipment_starts($objShipment, $objShipmentOffer, $objShipmentDriverSchedule);
				Shipment::where("id",$objShipment->id)->update(['send_notification_one_hour_before_shipment_starts' => 1]);
			}
		}


	}


	public function when_digital_and_no_certificate_uploaded(){

		$pastOneDay = now()->subHours(24)->format('Y-m-d H:i:s');
        $add10Minutes = now()->subHours(24)->addMinutes(10)->format('Y-m-d H:i:s');
		
		$shipmentLists = ShipmentDriverSchedule::with('customer', 'companyInformation')
			->join('shipments', 'shipments.id', 'shipment_driver_schedules.shipment_id')
			->whereBetween('shipment_driver_schedules.start_time', [$pastOneDay, $add10Minutes])
			->join('shipment_stops', 'shipment_stops.shipment_id', 'shipment_driver_schedules.shipment_id')
			->where('shipments.when_digital_and_no_certificate_uploaded', 0)
			->whereRaw("(select count(*) as cntStops from shipment_stops where shipment_stops.request_certificate_type = 'digital' and shipment_stops.request_certificate IS NULL and shipment_stops.shipment_id =  shipments.id ) > 0 ")
			->get();

		$shipmentListsCount = $shipmentLists->count();
		$emailActions 	= 	EmailAction::where('action','=','when_digital_and_no_certificate_uploaded')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','when_digital_and_no_certificate_uploaded')->get(array('name','subject','action','body', 'mail_enable'))-> toArray();
		$cons 			= 	explode(',',$emailActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];
		
        if($shipmentListsCount > 0){
			foreach($shipmentLists as $shipment){
				
					$Information = array(
						'customer_name'               => $shipment->customer ?->name,
						'customer_email'              => $shipment->customer ?->email,
						'customer_phone_number'       => $shipment->customer ?->phone_number,
						'truck_company_name'          => $shipment->companyInformation ?->company_name,
						'contact_person_name'         => $shipment->companyInformation ?->contact_person_name,
						'contact_person_email'        => $shipment->companyInformation ?->contact_person_email,
						'contact_person_phone_number' => $shipment->companyInformation ?->contact_person_phone_number,
						'url'                         => route($shipment->customer ?->customer_type.'-shipment-details', $shipment->request_number . '?type=upload_certificate'),
						'shipment_id'                 => $shipment->request_number,
					);	
					Shipment::where('id', $shipment->shipment_id)->update(['when_digital_and_no_certificate_uploaded' => 1]);

					$this->send_notification_when_digital_and_no_certificate_uploaded($Information, $shipment);

				}
	    }
		
	}



}
