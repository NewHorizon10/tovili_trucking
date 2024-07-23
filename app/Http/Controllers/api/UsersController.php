<?php
namespace App\Http\Controllers\api;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Helper,Config;
use Stripe;
use Cache, Cookie, Input, Mail, mongoDate, Response, Session, URL;
use Illuminate\Validation\Rules\Password;

class UsersController extends Controller
{ 

	public function __construct(Request $request) {
		parent::__construct();
        $this->request              =   $request;
    }

	public function index(Request $request){
	
		return View("frontend.index");
		
	}

	public function userLogin(Request $request){
		return View("frontend.login");
	}

	public function otp(Request $request){
		return View("frontend.otp");
	}

	public function userForgotPassword(Request $request){
		return View("frontend.forgot-password");
	}

	public function newPassword(Request $request){
		return View("frontend.new-password");
	}

	public function signUp(Request $request){
		return View("frontend.sign-up");
	}

	

	public function login(Request $request){

		
		$formData	=	$request->all();
		$response	=	array();
		if(!empty($formData)){

			$request->replace($this->arrayStripTags($request->all()));
			$validated = $request->validate(
				array(
					'phone_number'      		=> 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
					'password'      		 	=> 'required|min:4|max:4',
				),
				array(
					"phone_number.required"		=> trans("messages.This field is required"),
					'phone_number.regex'        => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
					"phone_number.digits"		=> trans("messages.Phone number should be 10 digits"),
					"password.required" 		=> trans("messages.This field is required"),
					"password.min"	 			=> trans("messages.Password should be 4 digits"),
					"password.max"	 			=> trans("messages.Password should be 4 digits"),
				)
			);
			
			$userDetail		 = User::where('phone_number', $request->phone_number)->where('is_deleted', 0)->first();
			if(!empty($userDetail)){
				$AuthAttemptUser = (!empty($userDetail)) ? Hash::check($request->input('password'), $userDetail->getAuthPassword()) : array();
				if(!empty($AuthAttemptUser)){
					if($userDetail->is_active == 0){
						Session()->flash('error', trans("messages.Your account has been deactivated , please contact to admin for more details"));
						return Redirect()->back()->withInput();
					}else {
						
						$verify_user	   = array(
							'phone_number'	=> $request->phone_number,
							'password'		=> $request->password,
						);
						if (Auth::attempt($verify_user)) {
								$userDetail		 				= 	User::where('email', $request->email)->where('is_deleted', 0)->first();
								$user          					= 	Auth::user();
								$token        					=	$user->createToken('tovilli Personal Access Client')->accessToken;
								$response["status"]				=	"success";
								$response["msg"]				=	trans("Login successfully");
								$response["passwordReset"]		=	1;
								$response["data"]				=	$userDetail;
								$response["token"]				=	$token;
								return json_encode($response);
						} else {
								$response["status"]			=	"error";
								$response["msg"]			=	trans("Something went wrong");
								$response["data"]			=	(object)array();
								return json_encode($response);
						}
					}
				}else {
					Session()->flash('error', trans("messages.Phone number or password is incorrect"));
					return Redirect()->back()->withInput();
				}
			}else {
				Session()->flash('error', trans("messages.Phone number or password is incorrect"));
				return Redirect()->back()->withInput();
			}
			
			Session()->flash('flash_notice', trans("messages.otp_has_been_sent_successfully_on_your_phone_number"));
			return redirect()->route("otp");
			
					
		}else {
			Session()->flash('error', trans("messages.Invalid Request"));
			return Redirect()->back()->withInput();
		}
	}

    public function resetPassword(Request $request){
        
		$formData	=	$request->all();
		$response	=	array();
		if(!empty($formData)){
			$validated = $request->validate(
				array(
					'phone_number'      			 	=> 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
				),
				array(
					"phone_number.required"				=> trans("messages.This field is required"),
					'phone_number.regex'        		=> trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
					"phone_number.digits"				=> trans("messages.Phone number should be 10 digits"),
				)
			);
			$phone_number		=	$request->input('phone_number');
			$userDetail	=	User::where('phone_number',$phone_number)->where("is_deleted",0)->first();
			if(!empty($userDetail)){
				if($userDetail->is_active == 1 ){
					$forgot_password_validate_string	= 	md5($userDetail->phone_number.time().time());
					$verification_code					=	'0000';//$this->getVerificationCodes();
					$phone_number						=	$request->input('phone_number');
					User::where('phone_number',$phone_number)->update(array('forgot_password_validate_string'=>$forgot_password_validate_string));
					UserVerificationCode::where('phone_number',$phone_number)->where("type","forget_password")->delete();
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
				}else{
					Session()->flash('error', trans("messages.Your account has been temporarily disabled"));
					return Redirect()->back()->withInput();
				}
			}else{
				Session()->flash('error', trans("messages.Phone_number_is_not_registered_with_us"));
				return Redirect()->back()->withInput();
			}
		}else {
			Session()->flash('error', trans("messages.Invalid Request"));
			return Redirect()->back()->withInput();
		}
		return json_encode($response);
   
	}
	
	public function verifyOtp(Request $request,$validate_string){
		
		$userInfo = User::where('forgot_password_validate_string',$validate_string)->first();
		$response	=	(object)array();
		if(empty($userInfo)){
			Session()->flash('error', trans("messages.Invalid validate string"));
			return Redirect()->back()->withInput();
		}
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$thisData = $request->all();
		$validated = $request->validate(
			array(
				'otp'      			 	=> 'required|digits:4',
			),
			array(
				"otp.required"			=> trans("messages.This field is required"),
				"otp.digits"				=> trans("messages.OTP should be 4 digits"),
			)
		);	
		$UserVerificationCode = UserVerificationCode::where('phone_number',$userInfo->phone_number)
		->where("verification_code",$request->otp)
		->where("type","forget_password")->first();
		if(!$UserVerificationCode){
			Session()->flash('error', trans("messages.Invalid_otp"));
			return Redirect()->back()->withInput();
		}					
		$data = array(
			"forgot_password_validate_string"=>$userInfo->forgot_password_validate_string
		);
		Session()->flash('data', json_encode($data));
		Session()->flash('flash_notice', trans("messages.OTP has been successfully verified"));
		return redirect()->route("otp");
		
	}
	
	public function createNewPassword(Request $request,$validate_string){
		$userInfo = User::where('forgot_password_validate_string',$validate_string)->first();
		if(empty($userInfo)){
			Session()->flash('error', trans("messages.Invalid validate string"));
			return Redirect()->back()->withInput();
		}
		
		$this->request->replace($this->arrayStripTags($request->all()));
		$thisData = $request->all();
		$validated = $request->validate(
			array(
				'password'                  => 'required|digits:4',
				'confirm_password'          => 'required|same:password',
			),
			array(
				"password.required"			=> trans("messages.This field is required"),
				"password.digits"			=> trans("messages.Password should be 4 digits"),
				"confirm_password.required"	=> trans("messages.This field is required"),
				"confirm_password.same"		=> trans("messages.The confirm password must be the same as the password"),
			)
		);	
		$UserVerificationCode = UserVerificationCode::where('phone_number',$userInfo->phone_number)
		->where("type","forget_password")->delete();

		$user                                   =   User::find($userInfo->id) ;
		$user->password                         =   Hash::make($request->password);			
		$user->forgot_password_validate_string	=   '';			
		$user->save();
		Session()->flash('flash_notice', trans("messages.Password has been changed successfully"));
		return redirect()->route("otp");
	}

	public function manageProfile(Request $request){
		if (Auth::guard('api')->user()) {
			$user = Auth::guard('api')->user();
		}
		$companyType = $user_company_informations = false; 
		
		if($user->user_role_id == 3 ){
			$user_company_informations = UserCompanyInformation::where('user_id',$user->id)->first();
			$companyType    = Lookup::where('lookup_type',"company-type")->with('lookupDiscription')->get();
		}else if( $user->user_role_id == 4){
			$companyType = $user_company_informations = false;
		}
		return View("deleted", compact('user_company_informations', 'user', 'companyType'));
		
	}

	public function updateCompanyDetails(Request $request){
		$formData	=	$request->all();	
		if(!empty($formData)){

			$request->replace($this->arrayStripTags($request->all()));
			$validated = $request->validate(
				array(
                    'company_name'                  => 'required',
                    'company_mobile_number'         => 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'contact_person_name'           => 'required',
                    'contact_person_phone_number'   => 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'contact_person_email'          => 'required|email',
                    'company_location'              => 'required',
                    'company_type'                  => 'required',
                    'contact_person_picture'        => 'nullable|mimes:jpg,jpeg,png',
                    'company_logo'                  => 'nullable|mimes:jpg,jpeg,png'
				),
				array(
                    "company_name.required"                 => trans("messages.The field is required"),
                    "company_mobile_number.required"        => trans("messages.The field is required"),
					'company_mobile_number.regex'        	=> trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
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
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
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

			UserCompanyInformation::where('user_id',$user->id)->update($companyObj);
			
			Session()->flash('flash_notice', trans("messages.Company_details_have_been_updated_successfully"));
			return Redirect()->back();

		}else {
			Session()->flash('error', trans("messages.Invalid Request"));
			return Redirect()->back()->withInput();
		}
		
	}

	public function updatePersonDetails(Request $request){
		$formData	=	$request->all();	
		if(!empty($formData)){

			$request->replace($this->arrayStripTags($request->all()));
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
			$validated = $request->validate(
				array(
                    'name'                  		=> "required",
                    'email'                 		=> "required|nullable|email",
                    'phone_number'          		=> "required|unique:users,phone_number,".$user->id."regex:".Config('constants.MOBILE_VALIDATION_STRING'),
					'location'						=> "required"
				),
				array(
                    "name.required"                 => trans("messages.The field is required"),
                    "phone_number.required"         => trans("messages.The field is required"),
					'phone_number.regex'        	=> trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
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

		}else {
			Session()->flash('error', trans("messages.Invalid Request"));
			return Redirect()->back()->withInput();
		}
		
	}

    public function verifyPhoneNumber(Request $request){
		$formData	=	$request->all();
		$response	=	array();
		if(!empty($formData)){

			$request->replace($this->arrayStripTags($request->all()));
			$validated = $request->validate([
					'mobile' 			=> 'required|unique:users,phone_number|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
				],
				[
					'mobile.required' 	=> trans("messages.This field is required"),
					'mobile.regex'      => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "mobile.unique"     => trans("messages.Mobile number already in use"),
                    "mobile.digits"     => trans("messages.Phone number should be 10 digits"),
				]
			);
			
			$mobile					=	$request->input('mobile');
			UserVerificationCode::where('phone_number',$mobile)->where("type","account_verification")->delete();
			$verification_code		=	'0000';//$this->getVerificationCodes();
			$obj 					= 	new UserVerificationCode;
			$obj->phone_number  	= 	$mobile;
			$obj->type   			= 	'account_verification';
			$obj->verification_code	= 	$verification_code;
			$obj->save();
			
			Session()->flash('flash_notice', trans("messages.otp_has_been_sent_successfully_on_your_phone_number"));
			return redirect()->route("otp");
			
					
		}else {
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
	
	public function changePassword(Request $request){
		$formData	=	$request->all();
		$response	=	array();
		if(!empty($formData)){
			$this->request->replace($this->arrayStripTags($request->all()));
			$validated = $request->validate([
				'old_password' 				=> 'required',
				'password'                  => 'required|digits:4',
				'confirm_password'          => 'required|same:password',
				],
				[
					"old_password.required" 	=> trans("messages.The old password field is required"),
					"password.required"			=> trans("messages.This field is required"),
					"password.digits"			=> trans("messages.Password should be 4 digits"),
					"confirm_password.required"	=> trans("messages.This field is required"),
					"confirm_password.same"		=> trans("messages.The confirm password must be the same as the password"),
				]
			);

			$password 					= 	$request->input('password');
			$correctPassword		=	Hash::make($password);
		
			$currentTime	=	date("Y-m-d H:i:s");
			$users			=	User::where("users.id",Auth::guard('api')->user()->id)
										->select("users.id")
										->first();
			if(!empty($users)){
				$user 					= User::find(Auth::guard('api')->user()->id);
				$old_password 			= $request->input('old_password'); 
				$password 				= $request->input('password');
				$confirm_password 		= $request->input('confirm_password');

				if(Hash::check($old_password, $user->getAuthPassword())){
					$user->password = Hash::make($password);
					if($user->save()) {
						$response["status"]		=	"success";
						$response["msg"]		=	trans("Password has been changed successfully");
						$response["data"]		=	(object)array();
					}else {
						$response["status"]		=	"error";
						$response["msg"]		=	trans("Invalid Request");
						$response["data"]		=	(object)array();
					}
				} else {
					$response["status"]		=	"error";
					$response["msg"]		=	trans("Your old password is incorrect");
					$response["data"]		=	(object)array();
				}
			}else{
				$response["status"]		=	"error";
				$response["msg"]		=	trans("Invalid Request");
				$response["data"]		=	(object)array();
			}
		}else {
			$response["status"]		=	"error";
			$response["msg"]		=	trans("Invalid Request");
			$response["data"]		=	(object)array();
		}
		return json_encode($response);
	}
	public function getVerificationCodes(){
		$code	=	rand(100000,999999);
		return $code;
	}
	public function logout(Request $request){
		Auth::guard('api')->user()->tokens()->delete();
		$response["status"]		=	"success";
		$response["msg"]		=	trans("You are logout successfully");
		$response["data"]		=	(object)array();
		return json_encode($response);
	}

}



