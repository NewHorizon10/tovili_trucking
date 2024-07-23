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
use App\Models\OurServices;
use App\Models\AboutUs;
use App\Models\Achievment;
use App\Models\Client;
use App\Models\Team;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\TruckCompanySubscription;
use App\Models\TruckCompanyRequestSubscription;
use App\Models\Notification;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Helper, Config;
use Stripe;
use Illuminate\Support\Facades\Redirect;
use Cache, Cookie, Input, Mail, mongoDate, Response, Session, URL;
use Illuminate\Validation\Rules\Password;

class CustomerController extends Controller
{

	public function __construct(Request $request)
	{
		parent::__construct();
		$this->request              =   $request;
	}

	public function index(Request $request)
	{
		$language_id	= getAppLocaleId();
		$slider_data 	= HomepageSlider::leftjoin('homepageslidersdescriptions', 'homepageslidersdescriptions.parent_id', '=', 'homepagesliders.id')
			->where('language_id', getAppLocaleId())
			->select('homepageslidersdescriptions.*', 'homepagesliders.image')
			->get();
		return View("frontend.index", compact('slider_data'));
	}

	public function about(Request $request)
	{
		$language_id	= getAppLocaleId();

		$about = AboutUs::leftjoin('about_us_descriptions', 'about_us_descriptions.parent_id', '=', 'about_us.id')
			->where('language_id', $language_id)
			->select('about_us_descriptions.*', 'about_us.image', 'about_us.*', 'about_us.goal_image')
			->first();

		$achievments = Achievment::leftjoin('achievment_details', 'achievment_details.parent_id', '=', 'achievments.id')
			->where('language_id', $language_id)
			->where('achievments.is_deleted', 0)
			->orderBy('achievments.id', 'desc')
			->select('achievment_details.*', 'achievments.image')
			->get();

		$teams = Team::leftjoin('team_descriptions', 'team_descriptions.parent_id', '=', 'teams.id')
			->where('language_id', $language_id)
			->where('teams.is_deleted', 0)
			->orderBy('teams.id', 'desc')
			->select('team_descriptions.*', 'teams.image')->get();

		$clients = Client::where('is_deleted', 0)->orderBy('id', 'desc')->get();

		return View("frontend.about", compact('about', 'achievments', 'teams', 'clients'));
	}


	public function plan(Request $request)
	{
		$plans = Plan::where('is_active', 1)->where('is_deleted', 0)->get();
		foreach ($plans as $key => $plan) {
			$plan->feature			=    PlanFeature::join('plan_feature_descriptions', 'plan_features.id', 'plan_feature_descriptions.parent_id')
				->select('plan_feature_descriptions.name as mlname', 'plan_features.*')
				->where('plan_feature_descriptions.language_id', getAppLocaleId())
				->where('plan_features.plan_id', $plan->id)
				->get();
		}
		return View("frontend.truck-company-registration.plans", compact('plans'));
	}

	public function subscribePlan(Request $request,	$validate_string, $enplanId = null)
	{
        $details = TruckCompanyRequestSubscription::where('validate_string', $validate_string)->first();
		if($enplanId){
			$plansId = base64_decode($enplanId);
			$planDetais = Plan::where(['id'=>$plansId,'is_active'=> 1, 'is_deleted'=> 0,'is_free'=>0])->first();
			if($planDetais == null){
				return redirect()->route('link-is-expired');
			}
			$details->is_free 		= 0;
			$details->plan_id = $planDetais->id;
			$details->price 		= $planDetais->price;
			$details->discount 		= 0;
			$details->total_price 	= $planDetais->price;
			$details->type 			= $planDetais->type;
			$details->column_type 	= $planDetais->column_type;
			$details->save();
			return redirect()->route('plan-subscription',[$validate_string]);
		}else if($details == null){
			return redirect()->route('link-is-expired');
		}
		

		$plans = Plan::where(['is_active'=> 1, 'is_deleted'=> 0,'is_free'=>0])->get();
		foreach ($plans as $key => $plan) {
			$plan->feature			=    PlanFeature::join('plan_feature_descriptions', 'plan_features.id', 'plan_feature_descriptions.parent_id')
				->select('plan_feature_descriptions.name as mlname', 'plan_features.*')
				->where('plan_feature_descriptions.language_id', getAppLocaleId())
				->where('plan_features.plan_id', $plan->id)
				->get();
		}
		return View("frontend.truck-company-registration.subscribePlan", compact('plans','validate_string'));
	}

	public function planSubscription(Request $request, $subId){
		if($subId){
			$plansubscriptionDetail = TruckCompanyRequestSubscription::where('validate_string', $subId)->first();
			if($plansubscriptionDetail == null){
				return redirect()->route('link-is-expired');
			}
			$planId = $plansubscriptionDetail->plan_id ?? 0 ;
			$planDetails = Plan::where('id', $planId)->first();
			

			$planFeatures = PlanFeature::join('plan_feature_descriptions', 'plan_features.id', 'plan_feature_descriptions.parent_id')
			->where('plan_feature_descriptions.language_id', getAppLocaleId())
			->where('plan_features.plan_id', $planId)
			->get();
		}
	

		$planDetailsCount = $planDetails ?->count() ?? 0;
		
		if($planDetailsCount == 0){
			return redirect()->route('link-is-expired');
		}
		return view('frontend.truck-company-registration.plan-subscription', compact('planDetails', 'planFeatures', 'planDetailsCount', 'plansubscriptionDetail'));
	
	}

	public function service(Request $request)
	{

		$services = DB::table('our_services')
			->where('our_services.is_active', 1)
			->where('our_services.is_deleted', 0)
			->leftjoin('our_services_descriptions', 'our_services_descriptions.parent_id', 'our_services.id')
			->where('our_services_descriptions.language_id', getAppLocaleId())
			->select('our_services_descriptions.*', 'our_services.image')
			->get();
		return View("frontend.service", compact('services'));
	}

	public function contact(Request $request)
	{
		return View("frontend.contacts");
	}

	public function forgotPassword(Request $request)
	{
		if ($request->isMethod('post')) {
			$validator = Validator::make(
				$request->all(),
				array(
					'phone_number'  			=> 'required|not_in:0000000000,2222222222,1111111111,3333333333,4444444444,5555555555,6666666666,7777777777,8888888888,9999999999|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
				),
				array(
					'phone_number.regex'        => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
					'phone_number.required'  	=> trans('messages.This field is required'),
				)
			);
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			} else {
				$phone_number		=	$request->input('phone_number');
				$userDetail	=	User::where('phone_number', $phone_number)
					->where("is_deleted", 0)
					->where("user_role_id", 2)
					->first();
				if (!empty($userDetail)) {
					$forgot_password_validate_string		= 	md5($userDetail->phone_number . time() . time());
					$verification_code						=	'9999'; //$this->getVerificationCodes();
					$phone_number      						=	$request->input('phone_number');
					User::where('phone_number', $phone_number)->update(
						array('forgot_password_validate_string' => $forgot_password_validate_string)
					);
					UserVerificationCode::where('phone_number', $userDetail->phone_number)->where("type", "forget_password")->delete();
					$obj 								= 	new UserVerificationCode;
					$obj->phone_number  				= 	$userDetail->phone_number;
					$obj->type   						= 	'forget_password';
					$obj->verification_code				= 	$verification_code;
					$obj->save();

					return redirect::route('forgot-password-verify-otp', $forgot_password_validate_string)
						->withSuccess(trans('messages.otp_has_been_sent_successfully_on_your_phone_number'));
				}
				return redirect::route('forgot-password')
					->withError(trans('messages.sorry_you_have_entered_invalid_credentials'));
			}
		}
		return View("frontend.login-signup.forgot-password");
	}
	public function forgotPasswordVerifyOtp(Request $request, $forgot_password_validate_string)
	{
		$validUser = User::where('forgot_password_validate_string', $forgot_password_validate_string)->first();
		if (empty($validUser)) {
			return redirect::route('forgot-password')
				->withErrors('Otp has been url expired');
		}
		if ($request->isMethod('post')) {
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
					'fullOtp.digits' => trans('messages.this_otp_field_is_required'),
				]
			);
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			} else {
				$otpData = $request->otp1 . $request->otp2 . $request->otp3 . $request->otp4;
				$UserVerificationCode = UserVerificationCode::where('phone_number', $validUser->phone_number)
					->where("verification_code", $otpData)
					->where("type", "forget_password")
					->first();

				if (!$UserVerificationCode) {
					Session()->flash('error', trans("messages.Invalid_otp"));
					return Redirect()->back()->withInput();
				}
				Session()->flash('success', trans("messages.otp_has_been_successfully_verified"));
				return redirect()->route("create-new-password", $forgot_password_validate_string);
			}
		}
		return View("frontend.login-signup.forgot-password-verify-otp", compact('validUser'));
	}

	public function createNewPasswordn(Request $request, $forgot_password_validate_string)
	{
		$validUser = User::where('forgot_password_validate_string', $forgot_password_validate_string)->first();
		if (empty($validUser)) {
			return redirect::route('forgot-password')
				->withErrors('Otp has been url expired');
		}
		if ($request->isMethod('POST')) {
			$validated = $request->validate(
				[
					'new_password'     => ['required', 'string', 'between:4,8', 'regex:' . Config('constants.PASSWORD_VALIDATION_STRING')],
					'confirm_password' => 'required|same:new_password',
				],
				[
					"new_password.required"		=> trans("messages.This field is required"),
					"new_password.between"		=> trans("messages.password_should_be_in_between_4_to_8_characters"),
					"new_password.regex"		=> trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),
					"confirm_password.required"	=> trans("messages.This field is required"),
					"confirm_password.same"		=> trans("messages.The confirm password must be the same as the password"),
				]
			);
			$validUser->password     =  Hash::make($request->new_password);
			$validUser->temp_pass    = 0;
			if (!$validUser->save()) {
				Session()->flash('error', trans("messages.something_went_wrong"));
				return Redirect()->back();
			}
			Session()->flash('success', trans("messages.Password has been changed successfully"));
			return redirect()->route("login");
		}
		return View("frontend.login-signup.new-password", compact('validUser'));
	}
	public function userLogin(Request $request)
	{
		if ($request->isMethod('post')) {
			$validator = Validator::make(
				$request->all(),
				array(
					'phone_number'	=> 'required|not_in:0000000000,2222222222,1111111111,3333333333,4444444444,5555555555,6666666666,7777777777,8888888888,9999999999|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
					'password'		=> 'required',
				),
				array(
					'phone_number.regex'           			=> trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
					'phone_number.required' 				=> trans('messages.the_phone_number_field_is_required'),
					'phone_number.exists'   				=> trans('messages.the_selected_phone_number_is_invalid'),
					'phone_number.not_in'   				=> trans('messages.the_selected_phone_number_is_invalid'),
					'password.required'     				=> trans('messages.the_password_field_is_required'),
				)
			);
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			} else {
				$phone_number = $request->input('phone_number');
				$password = $request->input('password');
				$remember_me = $request->input('remember_me');


				if (Auth::attempt(['phone_number' => $phone_number, 'password' => $password, 'user_role_id'  => '2'])) {

					if (Auth::user()->temp_pass == 1) {
						$userDetail = User::find(Auth::user()->id);
						Auth::logout();
						
						$validate_string = md5($phone_number . time() . time());
						$userDetail->forgot_password_validate_string = $validate_string;
						$userDetail->save();
	
						Session::flash('success', trans('messages.this_is_an_temp_password_please_new_password'));
						return redirect()->route('create-new-password', $validate_string);
					}

					if (Auth::user()->is_active == 0) {
						Auth::logout();
						Session::flash('error', trans('messages.your_account_is_deactivated_please_contact_to_the_admin'));
						return redirect::route('login');
					}
					if (Auth::user()->is_deleted == 1) {
						Auth::logout();
						Session::flash('error', trans('messages.your_account_is_deactivated_please_contact_to_the_admin'));
						return redirect::route('login');
					}

					$this->last_activity_date_time(Auth::user()->id);

					//save language into user profile
					$user = Auth::user();
					$user->language = getAppLocaleId();
					$user->save();

					self::setRememberMeCookie($remember_me, $phone_number, $password);

					return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
						->withSuccess(trans('messages.you_have_successfully_logged_in'));
				}
				return redirect::route('login')
					->withError(trans('messages.sorry_you_have_entered_invalid_credentials'));
			}
		}
		return view('frontend.login-signup.login');
	}
	public function chooceCustomer(Request $request)
	{
		return View("frontend.login-signup.chooce-customer");
	}
	public function seletedCustomer(Request $request)
	{
		$formData = $request->all();
		$validator = Validator::make(
			$request->all(),
			array(
				'userType'               => 'required',
			),
			array(
				'userType.required'      => trans('messages.the_user_type_field_must_be_required'),
			)
		);
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		} else {
			$type = $request->input('userType');
			Session::put('userTypeData', $type);
			return to_route("verify-mobile");
		}
	}


	public function verifyMobile(Request $request)
	{
		if (!Session::get('userTypeData')) {
			return redirect::route('sign-up');
		}
		if ($request->isMethod('post')) {
			$validator = Validator::make(
				array(
					'phone_number' 							=>  $request->phone_number
				),
				array(
					'phone_number'     						=> 'required|unique:users|not_in:0000000000,2222222222,1111111111,3333333333,4444444444,5555555555,6666666666,7777777777,8888888888,9999999999|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
				),
				array(
					'phone_number.required'                	=> trans('messages.the_phone_number_field_is_required'),
					'phone_number.regex'           			=> trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
					'phone_number.not_in'                	=> trans('messages.the_selected_phone_number_is_invalid'),
					'phone_number.unique'                	=> trans('messages.Phone number already in use'),
				)
			);
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			} else {
				$phone_number	=	$request->input('phone_number');
				Session::put('userTypePhoneData', $phone_number);
				$userDetail  	=	User::where('phone_number', $phone_number)->where("is_deleted", 0)->first();

				$verification_code					=	'9999'; //$this->getVerificationCodes();
				UserVerificationCode::where('phone_number', $phone_number)->where("type", "account_verification")->delete();
				$obj 								= 	new UserVerificationCode;
				$obj->phone_number  				= 	$phone_number;
				$obj->type   						= 	'account_verification';
				$obj->verification_code				= 	$verification_code;
				$obj->save();

				return redirect::route('verify-otp')
					->withSuccess(trans("messages.otp_has_been_sent_successfully_on_your_phone_number"));
			}
		} else {
			return view('frontend.login-signup.verify-mobile');
		}
	}

	public function verifyOtp(Request $request)
	{
		if (!Session::get('userTypeData')) {
			return redirect::route('sign-up');
		}
		if (!$request->session()->get('userTypePhoneData')) {
			Session()->flash('error', trans("messages.Otp has been url expired"));
			return redirect::route('customersLogin');
		}
		return View("frontend.login-signup.verify-otp");
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
			return redirect()->route("create-password");
		}
	}

	public function createPassword(Request $request)
	{
		if (!Session::get('userTypeData')) {
			return redirect::route('sign-up');
		}
		if ($request->isMethod('POST')) {
			$request->validate(
				[
					'new_password'     => 'required|string|min:4',
					'confirm_password' => 'required|same:new_password',
				],
				[
					"new_password.required"		=> trans("messages.new_password_field_is_required"),
					"new_password.between"          => trans("messages.password_should_be_in_between_4_to_8_characters"),
					"new_password.min"          => trans("messages.password_should_be_minimum_4_characters"),
					"confirm_password.required"	=> trans("messages.confirm_password_field_is_required"),
					"confirm_password.same"		=> trans("messages.The confirm password must be the same as the password"),
				]
			);
			if (Session::get('userTypeData')) {
				if (Session::get('userTypeData') == 'private') {
					$userDetails 				=	User::where(['phone_number' => Session::get('userTypePhoneData')])->first();
					if (!$userDetails) {
						$userDetails  			= 	new User;
					}
					$userDetails->user_role_id 	=  	2;
					$userDetails->customer_type	=  	'private';
					$userDetails->name			=  	'';
					$userDetails->email			=  	'';
					$userDetails->language 		=  	getAppLocaleId();
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
						return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
							->withSuccess(trans('messages.your_account_has_been_successfully_created'));
					} else {
						return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
							->withError(trans('messages.sorry_you_have_entered_invalid_credentials'));
					}
				} else {
					Session::put('userTypeNewPasswordData', $request->new_password);
					return redirect::route('sign-up-business-costomer');
				}
			} else {
				return redirect::route('sign-up')->withSuccess(trans('messages.your_session_has_been_expired'));
			}
		}
		return View("frontend.login-signup.create-password");
	}

	public function signUpBusinessCostomer(Request $request)
	{
		if (!Session::get('userTypeData')) {
			return redirect::route('sign-up');
		}
		if ($request->isMethod('POST')) {
			$request->validate(
				[
					'company_type' 				 	=> 'required',
					'company_name' 				 	=> 'required',
					'company_logo' 				 	=> 'required|nullable|image|mimes:jpeg,png,jpg',
					'company_hp_number' 	 	=> 'required',
					'company_location' 			 	=> 'required',
					'contact_person_picture'	 	=> 'required|nullable|image|mimes:jpeg,png,jpg',
					'contact_person_name' 		 	=> 'required',
					'contact_person_email'		 	=> 'required|email',
					'contact_person_phone_number'	=> 'required|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
				],
				[
					"company_name.required"                 => trans("messages.This field is required"),
					"company_hp_number.required"        => trans("messages.This field is required"),
					"contact_person_phone_number.required"  => trans("messages.This field is required"),
					'contact_person_phone_number.regex'     => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
					"contact_person_name.required"          => trans("messages.This field is required"),
					"contact_person_email.required"         => trans("messages.This field is required"),

					"contact_person_email.email"            => trans("messages.The email must be a valid email address"),
					"contact_person_email.regex"            => trans("messages.The email must be a valid email address"),
					"company_location.required"             => trans("messages.This field is required"),
					"company_type.required"                 => trans("messages.This field is required"),
					"contact_person_picture.required"       => trans("messages.This field is required"),
					"contact_person_picture.mimes"          => trans("messages.File must be jpg, jpeg, png only"),
					"company_logo.required"                 => trans("messages.This field is required"),
					"company_logo.mimes"                    => trans("messages.File must be jpg, jpeg, png only"),
				]
			);
			if (Session::get('userTypeData') == 'business') {
				$userDetails 				=	User::where(['phone_number' => Session::get('userTypePhoneData')])->first();
				if (!$userDetails) {
					$userDetails  			= 	new User;
				}
				$userDetails->user_role_id 	=  	2;
				$userDetails->customer_type	=  	'business';
				$userDetails->name			=  	'';
				$userDetails->email			=  	'';
				$userDetails->phone_number 	=  	Session::get('userTypePhoneData');
				$userDetails->password     	=  	Hash::make(Session::get('userTypeNewPasswordData'));
				$userDetails->language 		=  	getAppLocaleId();
				$userDetails->current_lat   =   $request->current_lat??'';
				$userDetails->current_lng   =   $request->current_lng ?? '';
				$userDetails->save();
				$userDetails->system_id  =   1000 + $userDetails->id;

				$userDetails->save();

				$userCompanyInformations 	=	UserCompanyInformation::where('user_id', $userDetails->id)->first();
				if (!$userCompanyInformations) {
					$userCompanyInformations  							= 	new UserCompanyInformation;
					$userCompanyInformations->user_id 					= $userDetails->id;
				}
				$userCompanyInformations->company_name 				 	= $request->company_name;
				$userCompanyInformations->company_hp_number 	 		= $request->company_hp_number;
				$userCompanyInformations->contact_person_name 		 	= $request->contact_person_name;
				$userCompanyInformations->contact_person_email 		 	= $request->contact_person_email;
				$userCompanyInformations->contact_person_phone_number	= $request->contact_person_phone_number;
				$userCompanyInformations->company_location 			 	= $request->company_location;
				$userCompanyInformations->company_type 				 	= $request->company_type;
				$userCompanyInformations->company_market 			 	= '';
				$userCompanyInformations->company_description 		 	= '';
				if ($request->hasFile('contact_person_picture')) {
					$file = rand() . '.' . $request->contact_person_picture->getClientOriginalExtension();
					$request->file('contact_person_picture')->move(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH'), $file);
					$userCompanyInformations->contact_person_picture =  $file;
				}
				if ($request->hasFile('company_logo')) {
					$file = rand() . '.' . $request->company_logo->getClientOriginalExtension();
					$request->file('company_logo')->move(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH'), $file);
					$userCompanyInformations->company_logo = $file;
				}
				if (!$userCompanyInformations->save()) {
					Session()->flash('error', trans("messages.something_went_wrong"));
					return Redirect()->back();
				}
				$authAttemp = array(
					'phone_number' => $userDetails->phone_number,
					'password' => Session::get('userTypeNewPasswordData')
				);
				$request->session()->forget(['userTypePhoneData', 'userTypeNewPasswordData', 'userTypeData']);
				if (Auth::attempt($authAttemp)) {
					return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
						->withSuccess(trans('messages.your_account_has_been_successfully_created'));
				} else {
					return redirect::route(Auth::user()->customer_type . '.customer-dashboard')
						->withError(trans('messages.sorry_you_have_entered_invalid_credentials'));
				}
			} else {
				return redirect::route('sign-up-business-costomer');
			}
		}
		$companyType   = Lookup::where('lookup_type', "company-type")->where('is_active',1)->with(['lookupDiscriptionList' => function ($query) {
			$query->where(['language_id' => getAppLocaleId()]);
		}])->get();
		
		return View("frontend.login-signup.sign-up-business-costomer", compact('companyType'));
	}
	public function logout(Request $request)
	{
		$this->last_activity_date_time(Auth::user()->id);
		Auth::logout();
		Session()->flash('success', ucfirst(trans('messages.You_are_logged_out')));
		return Redirect()->route('index');
	}

	public function customersProfile(Request $request)
	{
		$user_id = Auth::user()->id;
		$user 	 = User::where('id', $user_id)->first();

		$companyType = $user_company_informations = false;

		if ($user->customer_type == "business") {
			$user_company_informations = UserCompanyInformation::where('user_id', $user->id)->first();
			$companyType    = Lookup::where('lookup_type', "company-type")->with('lookupDiscription')->get();

			return View("frontend.customers.business_profile", compact('user_company_informations', 'user', 'companyType'));
		} else {
			return View("frontend.customers.private_profile", compact('user'));
		}
	}



	public function ProfileUpdate(Request $request)
	{
		$user = Auth::user();

		$validator                    =   Validator::make(
			$request->all(),
			array(
				'name'                  => "required",
				'email'                 => "nullable|email:rfc,dns",
				'phone_number'          => 'required|not_in:0000000000,2222222222,1111111111,3333333333,4444444444,5555555555,6666666666,7777777777,8888888888,9999999999|unique:users,phone_number,' . $user->id . '|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
				'location'      		=> 'required',
			),
			array(
				"name.required"                     => trans("messages.This field is required"),
				"email.email"            			=> trans("messages.The email must be a valid email address"),
				"phone_number.required"             => trans("messages.This field is required"),
				"phone_number.unique"           	=> trans("messages.The phone number must be unique"),
				"phone_number.regex"           		=> trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
				"location.required"                 => trans("messages.This field is required"),
			)
		);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		} else {
			$user->name                         =  $request->input('name');
			$user->email                        =  isset($request->email) ? $request->email : '';
			$user->phone_number                 =  $request->phone_number;
			$user->location                     =  $request->location;
			$user->current_lat                  =  $request->current_lat ?? '';
			$user->current_lng                  =  $request->current_lng ?? '';

			if ($request->hasFile('image')) {

				$extension 				=	$request->file('image')->getClientOriginalExtension();
				$original_image_name 	=	$request->file('image')->getClientOriginalName();
				$fileName				=	time() . '-image.' . $extension;

				$folderName     		= 	strtoupper(date('M') . date('Y')) . "/";
				$folderPath				=	Config('constants.CUSTOMER_IMAGE_ROOT_PATH') . $folderName;
				if (!File::exists($folderPath)) {
					File::makeDirectory($folderPath, $mode = 0777, true);
				}
				if ($request->file('image')->move($folderPath, $fileName)) {
					$user->image					=	$folderName . $fileName;
				}
			}

			$user->save();
			$this->last_activity_date_time(Auth::user()->id);

			Session()->flash('success', "Your profile has been updated successfully");
			return Redirect::back();
		}
	}


	public function resetPassword(Request $request)
	{

		$formData	=	$request->all();
		$response	=	array();
		if (!empty($formData)) {
			$validated = $request->validate(
				array(
					'phone_number'      			 	=> 'required|digits:10|not_in:0000000000,2222222222,1111111111,3333333333,4444444444,5555555555,6666666666,7777777777,8888888888,9999999999|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
				),
				array(
					"phone_number.required"			=> trans("messages.This field is required"),
					"phone_number.digits"				=> trans("messages."),
					'phone_number.regex'           			=> trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
				)
			);
			$phone_number		=	$request->input('phone_number');
			$userDetail	=	User::where('phone_number', $phone_number)->where("is_deleted", 0)->first();
			if (!empty($userDetail)) {
				if ($userDetail->is_active == 1) {
					$forgot_password_validate_string	= 	md5($userDetail->phone_number . time() . time());
					$verification_code					=	'0000'; //$this->getVerificationCodes();
					$phone_number						=	$request->input('phone_number');
					User::where('phone_number', $phone_number)->update(array('forgot_password_validate_string' => $forgot_password_validate_string));
					UserVerificationCode::where('phone_number', $phone_number)->where("type", "forget_password")->delete();
					$obj 								= 	new UserVerificationCode;
					$obj->phone_number  				= 	$phone_number;
					$obj->type   						= 	'forget_password';
					$obj->verification_code				= 	$verification_code;
					$obj->save();
					
					$data = array(
						'forgot_password_validate_string' =>	$forgot_password_validate_string,
					);

					Session()->flash('data', json_encode($data));
					Session()->flash('flash_notice', trans("messages.otp_has_been_sent_successfully_on_your_phone_number"));
					return redirect()->route("otp");
				} else {
					Session()->flash('error', trans("messages.Your account has been temporarily disabled"));
					return Redirect()->back()->withInput();
				}
			} else {
				Session()->flash('error', trans("messages.Phone_number_is_not_registered_with_us"));
				return Redirect()->back()->withInput();
			}
		} else {
			Session()->flash('error', trans("messages.Invalid Request"));
			return Redirect()->back()->withInput();
		}
		return json_encode($response);
	}

	public function createNewPassword(Request $request, $validate_string)
	{
		$userInfo = User::where('forgot_password_validate_string', $validate_string)->first();
		if (empty($userInfo)) {
			Session()->flash('error', trans("messages.Invalid validate string"));
			return Redirect()->back()->withInput();
		}

		$this->request->replace($this->arrayStripTags($request->all()));
		$thisData = $request->all();
		$validated = $request->validate(
			array(
				'password'                  => ['required', 'string', 'between:4,8', 'regex:' . Config('constants.PASSWORD_VALIDATION_STRING')],
				'confirm_password'          => 'required|same:password',
			),
			array(
				"password.required"			=> trans("messages.This field is required"),
				"password.between"			=> trans("messages.password_should_be_in_between_4_to_8_characters"),
				"password.regex"			=> trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),
				"confirm_password.required"	=> trans("messages.This field is required"),
				"confirm_password.same"		=> trans("messages.The confirm password must be the same as the password"),
			)
		);
		$UserVerificationCode = UserVerificationCode::where('phone_number', $userInfo->phone_number)
			->where("type", "forget_password")->delete();

		$user                                   =   User::find($userInfo->id);
		$user->password                         =   Hash::make($request->password);
		$user->forgot_password_validate_string	=   '';
		$user->save();
		Session()->flash('flash_notice', trans("messages.Password has been changed successfully"));
		return redirect()->route("otp");
	}


	public function updateCompanyDetails(Request $request)
	{
		$formData	=	$request->all();
		if (!empty($formData)) {

			$request->replace($this->arrayStripTags($request->all()));
			$validated = $request->validate(
				array(
					'company_name'                  => 'required',
					'company_mobile_number'         => 'required|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
					'contact_person_name'           => 'required',
					'contact_person_phone_number'   => 'required|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
					'contact_person_email'          => 'required|email',
					'company_location'              => 'required',
					'company_type'                  => 'required',
					'contact_person_picture'        => 'nullable|mimes:jpg,jpeg,png',
					'company_logo'                  => 'nullable|mimes:jpg,jpeg,png'
				),
				array(
					"company_name.required"                 => trans("messages.The field is required"),
					"company_mobile_number.required"        => trans("messages.The field is required"),
					'company_mobile_number.regex'           => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
					"contact_person_phone_number.required"  => trans("messages.The field is required"),
					'contact_person_phone_number.regex'     => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
					"contact_person_name.required"          => trans("messages.The field is required"),
					"contact_person_email.required"         => trans("messages.The field is required"),
					"contact_person_email.email"            => trans("messages.The email must be a valid email address"),
					"contact_person_email.regex"            => trans("messages.The email must be a valid email address"),
					"company_location.required"             => trans("messages.The field is required"),
					"company_type.required"                 => trans("messages.The field is required"),
					"contact_person_picture.required"       => trans("messages.The field is required"),
					"contact_person_picture.mimes"          => trans("messages.File must be jpg, jpeg, png only"),
					"company_logo.required"                 => trans("messages.The field is required"),
					"company_logo.mimes"                    => trans("messages.File must be jpg, jpeg, png only"),
				)
			);

			if ($request->wantsJson()) {
				if (Auth::guard('api')->user()) {
					$user = Auth::guard('api')->user();
				}
			} else {
				if (Auth::user()) {
					$user = Auth::user();
				}
			}

			$companyObj = array(
				'company_name'                       => $formData['company_name'],
				'company_mobile_number'              => $formData['company_mobile_number'],
				'contact_person_name'                => $formData['contact_person_name'],
				'contact_person_email'               => $formData['contact_person_email'],
				'company_location'                   => $formData['company_location'],
				'company_type'                       => $formData['company_type'],
				'contact_person_phone_number'        => $formData['contact_person_phone_number'],
			);
			if ($request->hasFile('contact_person_picture')) {
				$file = rand() . '.' . $request->contact_person_picture->getClientOriginalExtension();
				$request->file('contact_person_picture')->move(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH'), $file);
				$companyObj['contact_person_picture'] =  $file;
			}
			if ($request->hasFile('company_logo')) {
				$file = rand() . '.' . $request->company_logo->getClientOriginalExtension();
				$request->file('company_logo')->move(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH'), $file);
				$companyObj['company_logo'] = $file;
			}

			UserCompanyInformation::where('user_id', $user->id)->update($companyObj);

			Session()->flash('flash_notice', trans("messages.Company_details_have_been_updated_successfully"));
			return Redirect()->back();
		} else {
			Session()->flash('error', trans("messages.Invalid Request"));
			return Redirect()->back()->withInput();
		}
	}

	public function updatePersonDetails(Request $request)
	{
		$formData	=	$request->all();
		if (!empty($formData)) {

			$request->replace($this->arrayStripTags($request->all()));

			if ($request->wantsJson()) {
				if (Auth::guard('api')->user()) {
					$user = Auth::guard('api')->user();
				}
			} else {
				if (Auth::user()) {
					$user = Auth::user();
				}
			}

			$validated = $request->validate(
				array(
					'name'                  		=> "required",
					'email'                 		=> "required|nullable|email",
					'phone_number'          		=> "required|not_in:0000000000,2222222222,1111111111,3333333333,4444444444,5555555555,6666666666,7777777777,8888888888,9999999999|unique:users,phone_number," . $user->id . '|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
					'location'						=> "required"
				),
				array(
					"name.required"                 => trans("messages.The field is required"),
					"phone_number.required"         => trans("messages.The field is required"),
					"phone_number.regex"           	=> trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
					"email.required"                => trans("messages.The field is required"),
					"email.email"                   => trans("messages.The email must be a valid email address"),
					"email.unique"                  => trans("messages.The email must be unique"),
					"phone_number.unique"           => trans("messages.The phone number must be unique"),
					"location.required"             => trans("messages.The field is required"),
				)
			);

			$user                               	=   User::find($user->id);
			$user->name                         	=   $request->input('name');
			$user->email                        	=   isset($request->email) ? $request->email : '';
			$user->phone_number                 	=   $request->phone_number;
			$user->location                 		=   $request->location;
			$user->save();

			Session()->flash('flash_notice', trans("messages.Person details have been updated successfully"));
			return Redirect()->back();
		} else {
			Session()->flash('error', trans("messages.Invalid Request"));
			return Redirect()->back()->withInput();
		}
	}

	public function verifyPhoneNumber(Request $request)
	{
		$formData	=	$request->all();
		$response	=	array();
		if (!empty($formData)) {

			$request->replace($this->arrayStripTags($request->all()));
			$validated = $request->validate(
				[
					'mobile' 			=> 'required|unique:users,phone_number|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
				],
				[
					'mobile.required' 	=> trans("messages.This field is required"),
					"mobile.unique"      => trans("messages.Mobile number already in use"),
					"mobile.regex"      => trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),

				]
			);

			$mobile					=	$request->input('mobile');
			UserVerificationCode::where('phone_number', $mobile)->where("type", "account_verification")->delete();
			$verification_code		=	'0000'; //$this->getVerificationCodes();
			$obj 					= 	new UserVerificationCode;
			$obj->phone_number  	= 	$mobile;
			$obj->type   			= 	'account_verification';
			$obj->verification_code	= 	$verification_code;
			$obj->save();
			
			Session()->flash('flash_notice', trans("messages.otp_has_been_sent_successfully_on_your_phone_number"));
			return redirect()->route("otp");
		} else {
			Session()->flash('error', trans("messages.Invalid Request"));
			return Redirect()->back()->withInput();
		}


	}

	/**
	 * Function use for forget password
	 *
	 * @param null
	 *
	 * @return response
	 */

	public function viewchangePassword()
	{

		$user = request()->wantsJson() == true ? Auth::guard('api')->user() : Auth::user();

		return view('frontend.customers.change-password', compact('user'));
	}

	public function changePassword(Request $request)
	{
		$formData = $request->all();
		$response = [];

		if (!empty($formData)) {
			$this->request->replace($this->arrayStripTags($request->all()));

			$validator = Validator::make($formData, [
				'old_password' => 'required',
				'password' => 'required|string|min:4',
				'confirm_password' => 'required|same:password',
			], [
				"old_password.required" => trans("messages.The old password field is required"),
				"password.required" => trans("messages.This field is required"),
				"password.between" => trans("messages.password_should_be_in_between_4_to_8_characters"),
				"password.min" => trans("messages.password_should_be_minimum_4_characters"),
				"confirm_password.required" => trans("messages.This field is required"),
				"confirm_password.same" => trans("messages.The confirm password must be the same as the password"),
			]);

			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}

			$user = Auth::user();

			$old_password = $request->input('old_password');
			$password = $request->input('password');
			$confirm_password = $request->input('confirm_password');

			if (Hash::check($old_password, $user->getAuthPassword())) {
				$user->password = Hash::make($password);

				if ($user->save()) {
					$response["status"] = "success";
					$response["msg"] = trans("Password has been changed successfully");
					$response["data"] = (object)[];

					return redirect()->route('customers-profile')->with('success', trans("Password has been changed successfully"));
				} else {
					$response["status"] = "error";
					$response["msg"] = trans("Invalid Request");
					$response["data"] = (object)[];
					Session()->flash('error', trans("Invalid Request"));
					return redirect()->back();
				}
			} else {
				$response["status"] = "error";
				$response["msg"] = trans("Your old password is incorrect");
				$response["data"] = (object)[];

				Session()->flash('error', trans("Your old password is incorrect"));

				return redirect()->back();
			}
		} else {
			$response["status"] = "error";
			$response["msg"] = trans("Invalid Request");
			$response["data"] = (object)[];

			return redirect()->back()->with('error', trans("Invalid Request"))->withInput();
		}
	}
	public function getVerificationCodes()
	{
		$code	=	rand(100000, 999999);
		return $code;
	}

	public function galleryImagesUploads(Request $request)
	{

		if (!empty($request->images)) {
			foreach ($request->images as $keys => $value) {
				if ($request->hasFile('images')) {
					$extension           = $value->getClientOriginalExtension();
					$fileName            = rand() . time() . '-images.' . $extension;
					$folderName          = strtoupper(date('M') . date('Y')) . "/";
					$folderPath          = Config('constants.' . $request->path) . $folderName;
					// dd('constants.' . $request->path);
					$types               = $value->getMimeType();
					$imageArray          = explode("/", $types);
					if ($imageArray[0] == "image" || $imageArray[0] == "video" || $imageArray[0] == "application") {
						$value->move($folderPath, $fileName);
					}
					$images[]        = ['image' => $folderName . $fileName, 'type' => $imageArray[0]];
				}
			}
			return $images;
		}
	}


	public function softDeleteFiles(Request $request)
	{
		if ($request->isMethod('POST')) {
			
			$file_path = Config('constants.' . $request->path) . $request->image;
			if (file_exists($file_path)) {
				unlink($file_path);
				return 'success';
			}
		}
	}

	public function setRememberMeCookie($remember_me, $login_number, $login_password)
    {

        if ($remember_me == "1") {
            setcookie("tovilli_login__number", $login_number, time() + 3600);
            setcookie("tovilli_login__password", $login_password, time() + 3600);
            setcookie("tovilli_login_remember_me", $remember_me, time() + 3600);
        } else {
            setcookie("tovilli_login__number", "", time() - 3600);
            setcookie("tovilli_login__password", "", time() - 3600);
            setcookie("tovilli_login_remember_me", "", time() - 3600);
        }
    }


	public function clearAllNotification(Request $request){

		$notifications = Notification::where('user_id', Auth::user()->id)->delete();
		session()->flash('success', trans('messages.notifications_has_been_deleted_successfully'));
		return redirect()->back();

	}


}