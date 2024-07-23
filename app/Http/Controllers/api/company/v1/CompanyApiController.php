<?php

namespace App\Http\Controllers\api\company\v1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\UserCompanyInformation;
use App\Models\Lookup;
use App\Models\UserDriverDetail;
use App\Models\UserDeviceToken;
use App\Models\Shipment;
use App\Models\UserVerificationCode;
use App\Models\Plan;
use App\Models\Chat;


use App\Models\ShipmentOffer;
use App\Models\Language;
use App\Models\ShipmentDriverSchedule;
use App\Models\Truck;
use App\Models\Notification;
use App\Models\TruckCompanySubscription;
use App\Models\TruckCompanyRequestSubscription;

use Cache, Cookie, Input, Mail, mongoDate, Response, Session, URL, App, Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class CompanyApiController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function login(Request $request)
    {
                $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $this->request->replace($this->arrayStripTags($request->all()));
            $validator = Validator::make(
                $request->all(),
                array(
                    'phone_number'          => 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'password'              => ['required'],
                ),
                array(
                    "phone_number.required" => trans("messages.This field is required"),
                    "phone_number.digits"   => trans("messages.Phone number should be 10 digits"),
                    'phone_number.regex'        => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "password.required"     => trans("messages.This field is required"),
					"Password.between"      => trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "Password.regex"        => trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),
                    )
            );
            if ($validator->fails()) {
               return $this->change_error_msg_layout($validator->errors()->getMessages());
            } else {
                $userDetail         = User::where('phone_number', $request->phone_number)
                    ->where('is_deleted', 0)
                    ->where('user_role_id', 3)
                    ->first();
                if (!empty($userDetail)) {
                    $AuthAttemptUser = (!empty($userDetail)) ? Hash::check($request->input('password'), $userDetail->getAuthPassword()) : array();
                    if (!empty($AuthAttemptUser)) {
                        if ($userDetail->is_active == 0) {
                            $response["status"]        =    "error";
                            $response["msg"]        =    trans("messages.Account Locked, Please contact to admin");
                            $response["data"]        =    (object)array();
                            return response()->json($response);
                        }

                        $verify_user       = array(
                            'phone_number'    => $request->phone_number,
                            'password'        => $request->password,
                            'user_role_id'    => 3,
                        );

                        //logout from other divice
                        $userForLogout =  User::find($userDetail->id);
                        UserDeviceToken::where("user_id",$userForLogout->id)->delete();
                        $userForLogout->tokens()->delete();
                        if (Auth::attempt($verify_user)) {
                             $this->last_activity_date_time(Auth::user()->id);
                            $userDetail                         =     User::where('phone_number', $request->phone_number)->where('is_deleted', 0)->first();
                    
                            $companyTypeData = UserCompanyInformation::where(['user_id'=>$userDetail->id])->first();
                            $companyTypeData->contact_person_picture = Config('constants.CONTACT_PERSON_PROFILE_IMAGE_PATH').$companyTypeData->contact_person_picture;
                            $userDetail->user_image = $companyTypeData->contact_person_picture;
                            
                            //save language into user profile
                            $user = Auth::user();
                            $user->language = getAppLocaleId();
                            $user->save();
                            if(!empty($request->input('device_id')) && !empty($request->input('device_type'))){

                                $UserDeviceToken = UserDeviceToken::where("user_id",$user->id)->first();
                                if(!empty($UserDeviceToken)){
                                    $userDetail = $UserDeviceToken;
                                    $userDetail->user_id = $user->id;
                                    $userDetail->device_type = $request->input('device_type');
                                    $userDetail->device_id = $request->input('device_id');
                                    $userDetail->device_token = (!empty($request->input('device_id'))) ? $request->input('device_id') : "";
                                    $userDetail->save();
                                }else{
                                    $userDetail = new UserDeviceToken();
                                    $userDetail->user_id = $user->id;
                                    $userDetail->device_type = $request->input('device_type');
                                    $userDetail->device_id = $request->input('device_id');
                                    $userDetail->device_token = (!empty($request->input('device_id'))) ? $request->input('device_id') : "";
                                    $userDetail->save();
                                }
                            }

                            $user                       =    Auth::user();
                            $companyTypeData            =    UserCompanyInformation::where(['user_id'=>$user->id])->first();
                            $user->user_image           =    $companyTypeData->contact_person_picture;


                            $token                      =    $user->createToken('tovilli Personal Access Client')->accessToken;
                            if($user->is_approved == 0){
                                $messages = trans("messages.login_successfully_and_please_wait_for_administrator_approval");
                            }else{
                                $messages = trans("messages.login_successfully");
                            }
                            $response["status"]         =    "success";
                            $response["msg"]            =    $messages;
                            $response["passwordReset"]  =    1;
                            $response["data"]           =    $user;
                            $response["token"]          =    $token;
                            return response()->json($response);
                        } else {
                            $response["status"]            =    "error";
                            $response["msg"]            =    trans("messages.invalid_requests");
                            $response["data"]            =    (object)array();
                            return response()->json($response);
                        }
                    } else {
                        $response["status"]            =    "error";
                        $response["msg"]            =    trans("messages.The_phone_number_or_password_is_incorrect");
                        $response["data"]            =    (object)array();
                        return response()->json($response);
                    }
                } else {
                    $response["status"]        =    "error";
                    $response["msg"]        =     trans("messages.The_phone_number_or_password_is_incorrect");
                    $response["data"]        =    (object)array();
                }
            }
        } else {
            $response["status"]        =    "error";
            $response["msg"]        =    trans("messages.Invalid Request");
            $response["data"]        =    (object)array();
        }
        return response()->json($response);
    }
    public function resetPassword(Request $request)
    {

        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {

            $validator = Validator::make(
                $request->all(),
                array(
                    'phone_number'                       => 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                ),
                array(
                    "phone_number.required"             => trans("messages.This field is required"),
                    'phone_number.regex'                => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "phone_number.digits"               => trans("messages.Phone number should be 10 digits"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }

            $phone_number        =    $request->input('phone_number');
            $userDetail    =    User::where('phone_number', $phone_number)->where('user_role_id',3)->where("is_deleted", 0)->first();
            if (!empty($userDetail)) {
                if ($userDetail->is_active == 1) {
                    $forgot_password_validate_string    =     md5($userDetail->phone_number . time() . time());
                    $verification_code                    =    '9999'; //$this->getVerificationCodes();
                    $phone_number                        =    $request->input('phone_number');
                    User::where('phone_number', $phone_number)->update(array('forgot_password_validate_string' => $forgot_password_validate_string));
                    UserVerificationCode::where('phone_number', $phone_number)->where("type", "forget_password")->delete();

                 
                    $obj                                 =     new UserVerificationCode;
                    $obj->phone_number                  =     $phone_number;
                    $obj->type                           =     'forget_password';
                    $obj->verification_code                =     $verification_code;
                    $obj->save();

                    $data = array(
                        'forgot_password_validate_string' =>    $forgot_password_validate_string,
                    );
                    $response["status"]                =    "success";
                    $response["msg"]            =   trans("messages.otp_has_been_sent_successfully_on_your_phone_number");
                    $response["data"]                =    $data;
                    return response()->json($response);
                } else {
                    $response["status"]            =    "error";
                    $response["msg"]            =   trans("messages.Your account has been temporarily disabled");
                    $response["data"]            =    (object)array();
                    return response()->json($response);
                }
            } else {
                $response["status"]            =    "error";
                $response["msg"]            =    trans("messages.Phone_number_is_not_registered_with_us");
                $response["data"]            =    (object)array();
                return response()->json($response);
            }
        } else {

            $response["status"]            =    "error";
            $response["msg"]            =    trans("messages.Invalid Request");
            $response["data"]            =    (object)array();
            return response()->json($response);
        }
        return response()->json($response);
    }

    public function verifyPhoneNumber(Request $request)
    {
        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $request->replace($this->arrayStripTags($request->all()));
            $validator = Validator::make(
                $request->all(),
                [
                    'mobile'             => 'required|unique:users,phone_number|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                ],
                [
                    'mobile.required'     => trans("messages.This field is required"),
                    "mobile.unique"      => trans("messages.Mobile number already in use"),
                    'mobile.regex'        => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "mobile.digits"      => trans("messages.Phone number should be 10 digits"),
                ]
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }

            $mobile                    =    $request->input('mobile');
            UserVerificationCode::where('phone_number', $mobile)->where("type", "account_verification")->delete();
            $verification_code        =    '9999'; //$this->getVerificationCodes();
            $obj                     =     new UserVerificationCode;
            $obj->phone_number      =     $mobile;
            $obj->type               =     'account_verification';
            $obj->verification_code    =     $verification_code;
            $obj->save();

            $response["status"]                =    "success";
            $response["msg"]            =   trans("messages.otp_has_been_sent_successfully_on_your_phone_number");
            $response["data"]                =    (object)array();
            return response()->json($response);
        } else {
            $response["status"]            =    "error";
            $response["msg"]            =    trans("messages.Invalid Request");
            $response["data"]            =    (object)array();
            return response()->json($response);
        }
        return response()->json($response);
    }

    public function verifyOtp(Request $request)
    {

        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $request->replace($this->arrayStripTags($request->all()));
            $validator = Validator::make(
                $request->all(),
                [
                    'validate_string'             => 'required',
                    'otp'                       => 'required|digits:4',
                ],
                [
                    'validate_string.required'     => trans("messages.This field is required"),
                    "otp.required"            => trans("messages.This field is required"),
                    "otp.digits"                => trans("messages.OTP should be 4 digits"),
                ]
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }

            $userInfo = User::where('forgot_password_validate_string', $request->validate_string)->first();
      
           
            if (empty($userInfo)) {
                $response["status"]            =    "error";
                $response["msg"]            =    trans("messages.Invalid Request");
                $response["data"]            =    (object)array();
                return response()->json($response);
            }

           

            $UserVerificationCode = UserVerificationCode::where('phone_number', $userInfo->phone_number)
                ->where("verification_code",$request->otp)
                ->where("type", "forget_password")->first();

            if (!$UserVerificationCode) {
                $response["status"]            =    "error";
                $response["msg"]            =    trans("messages.Invalid_otp");
                $response["data"]            =    (object)array();
                return response()->json($response);
            }

          
            $data = array(
                "forgot_password_validate_string" => $userInfo->forgot_password_validate_string
            );
            $response["status"]            =    "success";
            $response["msg"]            =    trans("messages.otp_has_been_successfully_verified");
            $response["data"]            =   $data;
            
            return response()->json($response);
        }
    }

    public function createNewPassword(Request $request)
    {
        $validate_string = $request->forgot_password_validate_string;

        $userInfo = User::where('forgot_password_validate_string',$validate_string)->first();

       
        if (empty($userInfo)) {
            $response["status"]            =    "error";
            $response["msg"]            =    trans("messages.Invalid validate string");
            $response["data"]            =    (object)array();
            return response()->json($response);
        }

        $this->request->replace($this->arrayStripTags($request->all()));

        $validator = Validator::make(
            $request->all(),
            array(
                'password'                  => 'required|string|min:4',
                'confirm_password'          => 'required|same:password',
            ),
            array(
                "password.between"          			=> trans("messages.password_should_be_in_between_4_to_8_characters"),
                "password.min"          				=> trans("messages.password_should_be_minimum_4_characters"),
                "password.required"                     => trans("messages.This field is required"),
                "password.digits"                       => trans("messages.Password should be 4 digits"),
                "confirm_password.required"             => trans("messages.This field is required"),
                "confirm_password.same"                 => trans("messages.The confirm password must be the same as the password"),
            )
        );
        if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }

        UserVerificationCode::where('phone_number', $userInfo->phone_number)
            ->where("type", "forget_password")->delete();
        $this->last_activity_date_time($userInfo->id);

        $user                                   =   User::find($userInfo->id);
        $user->password                         =   Hash::make($request->password);
        $user->forgot_password_validate_string    =   '';
        $user->save();

        $response["status"]                =    "success";
        $response["msg"]                =    trans("messages.Password created successfully");
        $response["data"]                =   (object)array();
        
        return response()->json($response);
    }

    public function updateCompanyDetails(Request $request)
    { 
        $formData    =    $request->all();
        if (!empty($formData)) {

            $request->replace($this->arrayStripTags($request->all()));

            $validator = Validator::make(
                $request->all(),
                array(
                    'company_name'                  => 'required',
                    'company_location'              => 'required',
                    'lat'                           => 'required',
                    'lng'                           => 'required',
                    'tidaluk'                       => "required",
                    'refueling'                     => "required",
                    'company_logo'                  => 'nullable|mimes:jpg,jpeg,png'
                ),
                array(
                    "company_name.required"                 => trans("messages.This field is required"),
                    "company_mobile_number.required"        => trans("messages.This field is required"),
                    "company_mobile_number.digits"          => trans("messages.Phone number should be 10 digits"),
                    'company_mobile_number.regex'           => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "company_location.required"             => trans("messages.This field is required"),
                    "lat.required"                          => trans("messages.This field is required"),
                    "lng.required"                          => trans("messages.This field is required"),
                    "tidaluk.required"                      => trans("messages.This field is required"),
                    "refueling.required"                    => trans("messages.This field is required"),
                    "company_logo.required"                 => trans("messages.This field is required"),
                    "company_logo.mimes"                    => trans("messages.File must be jpg, jpeg, png only"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }
            if (Auth::guard('api')->user()) {
                $user = Auth::guard('api')->user();
            }

            $companyObj = array(
                'company_name'                      => $formData['company_name'],
                'company_mobile_number'             => $formData['company_mobile_number'] ?? '',
                'company_hp_number'                 => $formData['company_hp_number'] ?? '',
                'company_location'                  => $formData['company_location'],
                'latitude'                          => $formData['lat'],
                'longitude'                         => $formData['lng'],
                'company_type'                      => $formData['company_type'] ?? 0,
                'company_description'               => $formData['description'] ?? '',
                'company_trms'                      => $formData['terms_condition'] ?? '',
                'company_tidaluk'                   => $request->tidaluk,
                'company_refueling'                 => $request->refueling,
            );


            UserCompanyInformation::where('user_id', $user->id)->update($companyObj);
            if ($request->hasFile('company_logo')) {
                $file = rand() . '.' . $request->company_logo->getClientOriginalExtension();
                $request->file('company_logo')->move(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH'), $file);
                $companyObj['company_logo'] = $file;
            }

            UserCompanyInformation::where('user_id', $user->id)->update($companyObj);

            $response["status"]                =    "success";
            $response["msg"]                =    trans("messages.Company_details_have_been_updated_successfully");
            $response["data"]                =   (object)array();
            return response()->json($response);
        } else {
            $response["status"]                =    "error";
            $response["msg"]                =    trans("messages.Invalid Request");
            $response["data"]                =   (object)array();
            return response()->json($response);
        }
    }

    public function updatePersonDetails(Request $request)
    {
        $formData    =    $request->all();
        if (!empty($formData)) {

            $request->replace($this->arrayStripTags($request->all()));
            if (Auth::guard('api')->user()) {
                $user = Auth::guard('api')->user();
            }

            $validator = Validator::make(
                $request->all(),
                array(
                    'name'                          => "required",
                    'email'                         => "nullable",
                    'phone_number'                  => "required|digits:10|unique:users,phone_number," . $user->id . '|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'contact_person_picture'        => 'nullable|mimes:jpg,jpeg,png'
                ),
                array(
                    "name.required"                 => trans("messages.This field is required"),
                    "phone_number.required"         => trans("messages.This field is required"),
                    "phone_number.digits"           => trans("messages.Phone number should be 10 digits"),
                    'phone_number.regex'            => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "email.required"                => trans("messages.This field is required"),
                    "email.email"                   => trans("messages.The email must be a valid email address"),
                    "email.regex"                   => trans("messages.The email must be a valid email address"),
                    "email.unique"                  => trans("messages.The email must be unique"),
                    "tidaluk.required"              => trans("messages.This field is required"),
                    "location.required"             => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }

            $user                                   =   User::find($user->id);
            $user->name                             =   $request->input('name');
            $user->email                            =   isset($request->email) ? $request->email : '';
            $user->phone_number                     =   $request->phone_number;
            $user->last_activity_date_time          =   date('Y-m-d G:i:s');
            
            if ($request->hasFile('contact_person_picture')) {
                $file = rand() . '.' . $request->file('contact_person_picture')->getClientOriginalExtension();
                $request->file('contact_person_picture')->move(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH'), $file);
                UserCompanyInformation::where('user_id', $user->id)->update(['contact_person_picture'=>$file,'contact_person_phone_number'=>$request->phone_number]);
            }

            $user->save();

            $response["status"]                =    "success";
            $response["msg"]                =    trans("messages.Person_details_has_been_updated_successfully");
            $response["data"]                =   (object)array();
            return response()->json($response);
        } else {

            $response["status"]                =    "error";
            $response["msg"]                =    trans("messages.Invalid Request");
            $response["data"]                =   (object)array();
            return response()->json($response);
        }
    }

    public function changePassword(Request $request)
    {
        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $this->request->replace($this->arrayStripTags($request->all()));

            $validator = Validator::make(
                $request->all(),
                [
                    'old_password'                  => 'required',
                    'password'                      => 'required|string|min:4',
                    'confirm_password'              => 'required|same:password',
                ],
                [
                    "old_password.required"         => trans("messages.The old password field is required"),
                    "password.required"             => trans("messages.This field is required"),
                    "password.digits"               => trans("messages.Password should be 4 digits"),
                    "password.between"              => trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "password.min"          	    => trans("messages.password_should_be_minimum_4_characters"),
                    "confirm_password.required"     => trans("messages.This field is required"),
                    "confirm_password.same"         => trans("messages.The confirm password must be the same as the password"),
                ]
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }

            $password                     =     $request->input('password');

            if (Auth::guard('api')->user()) {
                $users = Auth::guard('api')->user();
            }

            if (!empty($users)) {
                $user                     = User::find($users->id);
                $old_password             = $request->input('old_password');
                $password                 = $request->input('password');

                if (Hash::check($old_password, $user->getAuthPassword())) {
                    $user->password = Hash::make($password);
                    if ($user->save()) {
                        $response["status"]        =    "success";
                        $response["msg"]        =    trans("messages.Password has been changed successfully");
                        $response["data"]        =    (object)array();
                        $this->last_activity_date_time($user->id);
                        return response()->json($response);
                    } else {
                        $response["status"]        =    "error";
                        $response["msg"]        =    trans("messages.Invalid Request");
                        $response["data"]        =    (object)array();
                        return response()->json($response);
                    }
                } else {
                    $response["status"]        =    "error";
                    $response["msg"]        =    trans("messages.Your old password is incorrect");
                    $response["data"]        =    (object)array();
                    return response()->json($response);
                }
            } else {
                $response["status"]        =    "error";
                $response["msg"]        =    trans("messages.Invalid Request");
                $response["data"]        =    (object)array();
                return response()->json($response);
            }
        } else {
            $response["status"]        =    "error";
            $response["msg"]        =    trans("messages.Invalid Request");
            $response["data"]        =    (object)array();
            return response()->json($response);
        }
        return response()->json($response);
    }

    public function logout(Request $request)
    {
        UserDeviceToken::where("user_id",Auth::guard('api')->user()->id)->delete();
        $this->last_activity_date_time(Auth::guard('api')->user()->id);
        Auth::guard('api')->user()->tokens()->delete();
        $response["status"]        =    "success";
        $response["msg"]        =    trans("messages.You are logout successfully");
        $response["data"]        =    (object)array();
        return json_encode($response);
    }

    public function companyType(Request $request)
    {

        $formData    =    $request->all();
        $response    =    array();
		$companyType   = Lookup::where('lookup_type', "company-type")->with(['lookupDiscription' => function($query) {
	        $query->where(['language_id' => getAppLocaleId()]);
        }])->get(); 
        $companyTypeData = array();
        foreach ($companyType as $key => $value) {
            $cnt = count($companyTypeData);
            $companyTypeData[] = ['value' => $value->id, 'name' => $value->lookupDiscription->code];
        };
        $response["status"] =    "success";
        $response["msg"]    =     "";
        $response["data"]   =    $companyTypeData;
        return json_encode($response);
    }

    public function truckCompanyDetails(Request $request)
    {
        if (Auth::guard('api')->user()) {
            $companyUser = Auth::guard('api')->user();
        }

        $formData    =    $request->all();
        $response    =    array();
		$companyType   = Lookup::where('lookup_type', "company-type")->with(['lookupDiscription' => function($query) {
	        $query->where(['language_id' => getAppLocaleId()]);
        }])->get(); 

        $tidalukcompanyType   = Lookup::where('lookup_type', 'tidaluk-company-type')->with(['lookupDiscription' => function($query) {
            $query->where(['language_id' => getAppLocaleId()]);
        }])->get(); 

        $fuelingMethodsType   = Lookup::where('lookup_type', 'fueling-methods')->with(['lookupDiscription' => function($query) {
            $query->where(['language_id' => getAppLocaleId()]);
        }])->get(); 
        $companyTypeData['user'] = $companyUser;

        // $companyTypeData = UserCompanyInformation::where(['user_id'=>$userDetail->id])->first();



        $companyTypeData['userCompanyInformation'] = UserCompanyInformation::where(['user_id'=>$companyUser->id])->first();
        $companyTypeData['customerAsDriverInformation'] = UserDriverDetail::where(['user_id'=>$companyUser->id])->first();

        $companyTypeData['user']->user_image = $companyTypeData['userCompanyInformation']->contact_person_picture;

        $company_tidaluk= Lookup::where('id',$companyTypeData['userCompanyInformation']->company_tidaluk)->with(['lookupDiscription' => function($query) {
            $query->where(['language_id' => getAppLocaleId()]);
        }])->first(); 
        $companyTypeData['userCompanyInformation']->company_tidaluk = $company_tidaluk ? ['value' => $company_tidaluk->id, 'name' => $company_tidaluk->lookupDiscription->code] : null;
        
        //set company_refueling as object pair of key value 
        $company_refueling = Lookup::where('id',$companyTypeData['userCompanyInformation']->company_refueling)->with(['lookupDiscription' => function($query) {
            $query->where(['language_id' => getAppLocaleId()]);
        }])->first(); 
        $companyTypeData['userCompanyInformation']->company_refueling = $company_refueling ? ['value' => $company_refueling->id, 'name' => $company_refueling->lookupDiscription->code] : null;

        $companyTypeData['companyType'] = array();
        foreach ($companyType as $key => $value) {
            $cnt = count($companyTypeData['companyType']);
            $companyTypeData['companyType'][] = ['value' => $value->id, 'name' => $value->lookupDiscription->code];
            if($companyTypeData['userCompanyInformation']->company_type == $value->id ){
                $companyTypeData['userCompanyInformation']->companyType = ['value' => $value->id, 'name' => $value->lookupDiscription->code];
            }
        };
        $companyTypeData['tidalukcompanyType'] = array();
        foreach ($tidalukcompanyType as $key => $value) {
            $cnt = count($companyTypeData['tidalukcompanyType']);
            $companyTypeData['tidalukcompanyType'][] = ['value' => $value->id, 'name' => $value->lookupDiscription->code];
            if($companyTypeData['userCompanyInformation']->company_type == $value->id ){
                $companyTypeData['userCompanyInformation']->companyType = ['value' => $value->id, 'name' => $value->lookupDiscription->code];
            }
        };

        $companyTypeData['fuelingMethodsType'] = array();
        foreach ($fuelingMethodsType as $key => $value) {
            $cnt = count($companyTypeData['fuelingMethodsType']);
            $companyTypeData['fuelingMethodsType'][] = ['value' => $value->id, 'name' => $value->lookupDiscription->code];
            if($companyTypeData['userCompanyInformation']->company_type == $value->id ){
                $companyTypeData['userCompanyInformation']->companyType = ['value' => $value->id, 'name' => $value->lookupDiscription->code];
            }
        };

        $response["status"] =    "success";
        $response["msg"]    =     "";
        $response["data"]   =    $companyTypeData;
        return json_encode($response);
    }

    public function dashboard(Request $request) {
        $results = array();

        $results["total_shipment"]                  =   ShipmentOffer::where('truck_company_id',Auth::guard('api')->user()->id)
        ->where('status','approved_from_company')
        ->get()->count() ;
        
        $results["total_offered_shipment_request"]  =   ShipmentOffer::where('truck_company_id',Auth::guard('api')->user()->id)
        ->where('status','waiting')
        ->get()->count() ;

        $truckTypeIds = Truck::where('truck_company_id',Auth::guard('api')->user()->id)
        ->groupBy('type_of_truck')
        ->pluck('type_of_truck')
        ->toArray();



        $shipment_offer_request_rejected =   DB::table("shipment_offer_request_rejected")->where("customer_id",Auth::guard('api')->user()->id)->pluck("shipment_id")->toArray();
        $shipment_offer =   DB::table("shipment_offers")->where("truck_company_id",Auth::guard('api')->user()->id)->pluck("shipment_id")->toArray();
        $shipment_offer_request_rejected = array_merge($shipment_offer,$shipment_offer_request_rejected);

        if(Auth::guard('api')->user()->is_approved == 1){
            $results["new"]      =   Shipment::select("*")
            ->whereIn('shipment_type',$truckTypeIds)
            ->whereIn('status',['new','offers'])
            ->whereNotIn('shipments.id',$shipment_offer_request_rejected)
            ->whereRaw('
                (DATE_ADD(shipments.request_date, INTERVAL shipments.request_date_flexibility DAY)) > "'.now().'" 
            ')
            ->get()->count() ;
        }else{
            $results["new"]      =   0;
        }

        
        $results["total_new_notifications"] = Notification::where('user_id',Auth::guard('api')->user()->id)->where("language_id",getAppLocaleId())->where("is_read", 0)->count();

        $results['admin_chat_count']      = Chat::where('sender_id', 1)->where('is_read', 0)->count();
        $results['user_chat_count']       = Chat::where('sender_id', '!=', 1)->where('sender_id', '!=', Auth::guard('api')->user()->id)->where('is_read', 0)->count();
        
        ////////////
        $totalActiveShipment                     =   ShipmentDriverSchedule::where('truck_company_id',Auth::guard('api')->user()->id)
        ->Where('shipment_status','start')
        ->get();

        $totalUpcomingShipment                     =   Shipment::where('shipments.status','shipment')
        ->join('shipment_offers','shipments.id','shipment_offers.shipment_id')
        ->leftjoin('users', 'users.id' , 'shipments.customer_id')
        ->leftjoin('shipment_driver_schedules','shipments.id','shipment_driver_schedules.shipment_id')
        ->where('shipment_offers.truck_company_id',Auth::guard('api')->user()->id)
        ->whereRaw('(shipment_driver_schedules.shipment_status IS NULL or shipment_driver_schedules.shipment_status = "not_start")')
        ->whereDate('shipments.request_date', '>=', now())
        ->where('shipment_offers.status','approved_from_company')
        ->whereIn('shipments.status',['shipment'])
        ->get();

        $totalPastShipment                     =   Shipment::join('shipment_offers','shipments.id','shipment_offers.shipment_id')
        ->orWhere('shipments.status','end')
        ->where('shipment_offers.truck_company_id',Auth::guard('api')->user()->id)
        ->get();

        $totalOfferShipment                     =   ShipmentOffer::join('shipments','shipments.id','shipment_offers.shipment_id')
        ->whereRaw('
            (DATE_ADD(shipments.request_date, INTERVAL shipments.request_date_flexibility DAY)) >= "'.now().'" 
        ')
        ->where('shipment_offers.truck_company_id',Auth::guard('api')->user()->id)
        ->where('shipment_offers.is_deleted',0)
        ->whereRaw('(shipment_offers.status ="waiting" or shipment_offers.status = "selected")')
        ->get();

        $totalScheduleShipment =  $totalUpcomingShipment;

        $results["active"]                = $totalActiveShipment->count(); 
        $results["upcoming"]              = $totalUpcomingShipment->count();
        $results["past"]                  = $totalPastShipment->count();
        $results["schedule"]              = $totalScheduleShipment->count(); 
        $results["offer"]                 = $totalOfferShipment->count();

        ////////////
            

        ////////////
        $results['notifications']					=	Notification::leftjoin('shipments', 'shipments.id' , 'notifications.shipment_id')
        ->where('user_id',Auth::guard('api')->user()->id)
        ->where("language_id",getAppLocaleId())
        ->select(
            'notifications.*',
            'shipments.status as shipments_status',
            'shipments.request_number as request_number'
        )->orderByDesc("notifications.id")->get()->take(5);
        foreach ($results['notifications'] as &$notifications) {
            $notifications->description = strip_tags(html_entity_decode($notifications->description));
            
            if($notifications->shipments_status == "new" || $notifications->shipments_status == "offers" || $notifications->shipments_status == "offer_chosen"){
                $notifications->shipments_status = "request";
            }else if($notifications->shipments_status == "shipment" || $notifications->shipments_status == "shipment"){
                $notifications->shipments_status = "shipment";
            }

        }

        
        $user                       =    Auth::guard('api')->user();

        $driverDetails                      =   UserDriverDetail::where("user_id",$user->id)->first();
        $results['drivers_statics'] = null;
        if($driverDetails){
            $results['drivers_statics'] = array();
            $totalActiveShipment                     =   ShipmentDriverSchedule::where('driver_id',$user->id)
            ->Where('shipment_status','start')
            ->get();

            $totalUpcomingShipment                     =   ShipmentDriverSchedule::where('shipment_driver_schedules.shipment_status','not_start')
            ->where('driver_id',$user->id)
            ->get();

            $totalPastShipment                     =   ShipmentDriverSchedule::where('shipment_driver_schedules.shipment_status','end')
            ->where('driver_id',$user->id)
            ->get();

            $results['drivers_statics']["active"]                = $totalActiveShipment->count(); 
            $results['drivers_statics']["upcoming"]              = $totalUpcomingShipment->count();
            $results['drivers_statics']["past"]                  = $totalPastShipment->count();
        }



        $response                                   =   array();
        $response["data"]                           =   $results;

        if($user->is_approved == 0){
            $response["status"]                         =   "error";
            $response["msg"]                            =   trans("messages.please_wait_for_administrator_approval");
        }else{
            $response["status"]                         =   "success";
            $response["msg"]                            =   "";
        }
       
        return response()->json($response);
	}

    public function notifications(Request $request) {

        Notification::where('user_id',Auth::guard('api')->user()->id)->update(["is_read"=>1]);

        $DB					=	Notification::query();
        $DB->leftjoin('shipments', 'shipments.id' , 'notifications.shipment_id');
        $DB->where('user_id',Auth::guard('api')->user()->id);
        $DB->where("language_id",getAppLocaleId());
        $DB->select(
            'notifications.*',
            'shipments.status as shipments_status',
            'shipments.request_number as request_number'
        );

        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'notifications.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        if(!$results->isEmpty()){
            foreach($results as &$item){
                $item->created_date = date(config("Reading.date_format"),strtotime($item->created_at));
                $item->description = strip_tags(html_entity_decode($item->description));
                if($item->shipments_status == "new" || $item->shipments_status == "offers" || $item->shipments_status == "offer_chosen"){
                    $item->shipments_status = "request";
                }else if($item->shipments_status == "shipment" || $item->shipments_status == "shipment"){
                    $item->shipments_status = "shipment";
                }
            }
        }
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        return response()->json($response);
	}

    public function clearSelectedNotifications(Request $request){
        $obj = Notification::where('user_id',Auth::guard('api')->user()->id);
        if($request->notifications_map_id){
            $obj->where('map_id',$request->notifications_map_id);
        }
        $obj->delete();

        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   trans("messages.notifications_has_been_deleted_successfully");
        $response["data"]                   =   (object)array();
        return response()->json($response);
    }


    public function viewNotifications(Request $request)
    {
        $formData = $request->all();
        $response = array();
    
        if (!empty($formData)) {
            $request->replace($this->arrayStripTags($request->all()));
    
            $validator = Validator::make(
                $request->all(),
                [
                    'map_id' => 'required',
                ],
                [
                    "map_id.required" => trans("messages.the_map_id_field_is_required"),
                ]
            );
    
            if ($validator->fails()) {
                $response = $this->change_error_msg_layout($validator->errors()->getMessages());
                return response()->json($response);
            } else {
                $map_id = $request->map_id;
                if (!empty($map_id)) {
                    Notification::where('map_id', $map_id)->update([
                        "is_read" => 1,
                        "is_view" => 1
                    ]);
                }
    
                $response = [
                    'status' => 'success',
                    'msg' => '',
                    'data' => [],
                ];
                return response()->json($response);
            }
        }
    }

    public function activateDriver(Request $request)
    {
        $user                               =   User::find(Auth::guard('api')->user()->id);
        if($request->active == 1){
            $driverDetails                      =   UserDriverDetail::where("user_id",$user->id)->first();
            if(!$driverDetails){
                $user->truck_company_id             =   $user->id;
                $user->save();
                $driverDetails                      =   new UserDriverDetail();
                $driverDetails->user_id             =   $user->id;
                $driverDetails->driver_picture      =   '';
                $driverDetails->licence_picture     =   '';
                $driverDetails->save();
            }
            $response["msg"]                    =     trans("messages.Truck Driver has been activated successfully");
            $response["status"]                 =    "success";
        }else if($request->active == 0){
            $totalActiveShipment                     =   ShipmentDriverSchedule::where('driver_id',$user->id)
            ->whereIn('shipment_status',['not_start','not_start'])
            ->count();
            if($totalActiveShipment == 0){
                $user->truck_company_id             =   null;
                $user->save();
                $driverDetails                      =   UserDriverDetail::where('user_id',$user->id)->delete();
                $response["msg"]                    =     trans("messages.Truck Driver has been deactivated successfully");
                $response["status"]                 =    "success";
            }else{
                $response["msg"]                    =     trans("messages.Truck driver cannot be deactivated Please complete the scheduled shipment first");
                $response["status"]                 =    "error";
            }
        }
        $response["data"]                   =    (object)array();
        return response()->json($response);
    }

    public function changeLanguage(Request $request)
    {
        $user                               =   User::find(Auth::guard('api')->user()->id);
        $selectedLanguage                   =   Language::where("lang_code",$request->lang_code)->first();

        $user->language                     =   $selectedLanguage->id;
        $user->save();
        $response["status"]                 =    "success";
        $response["data"]                   =    (object)array();
        return response()->json($response);
    }

    public function sendProposal(Request $request)
    {
        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $request->replace($this->arrayStripTags($request->all()));
            $validator = Validator::make(
                $request->all(),
                [
                    'message'             => 'required',
                ],
                [
					'message.required'  => trans('messages.the_message_field_is_required'),
                ]
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }
            $user                     =     Auth::guard('api')->user();
            $obj                      =     new Contact();
            $obj->name                =     $user->name;
            $obj->email               =     $user->email;
            $obj->phone_number        =     $user->phone_number;
            $obj->comments            =     $request->message;
            $obj->save();

            $response["status"]       =    "success";
            $response["msg"]          =   trans("messages.send_proposal_successfully");
            $response["data"]         =    (object)array();
            return response()->json($response);
           
        } else {
            $response["status"]       =    "error";
            $response["msg"]          =    trans("messages.Invalid Request");
            $response["data"]         =    (object)array();
            return response()->json($response);
        }
    }

    public function mySubscription(){

        $companyId = Auth::guard('api')->user()->id;
        
        $companyPlanDetails = TruckCompanySubscription::where('truck_company_id', $companyId)->where('status', 'activate')->first();
        if($companyPlanDetails == null){
            $companyPlanDetails = TruckCompanyRequestSubscription::where('truck_company_id', $companyId)->first();
        }

        

        if($companyPlanDetails == null){
            $response["status"]       =    "error";
            $response["msg"]          =    trans('messages.you_have_no_active_plan_please_contact_to_admin');
            $response["data"]         =    (object)array();
            return response()->json($response);
        }else{

            $planDetails = Plan::find($companyPlanDetails->plan_id);

            $typeData = '';
            $columntypeData = '';
            if ($companyPlanDetails->type == '0') {
                $typeData = trans('messages.monthly');
            } elseif ($companyPlanDetails->type == '1') {
                $typeData = trans('messages.quarterly');
            } elseif ($companyPlanDetails->type == '2') {
                $typeData = trans('messages.Half Yearly');
            } elseif ($companyPlanDetails->type == '3') {
                $typeData = trans('messages.Yearly');
            }
        
            if ($companyPlanDetails->column_type == '0') {
                $columntypeData = trans('messages.Up to 5 Trucks');
            }  else if ($companyPlanDetails->column_type == '1'){
                $columntypeData = trans('messages.More then 5');
            }

            $statusString = '';
            if($companyPlanDetails->status == 'activate'){
                $statusString  = trans('messages.admin_activate_status');
            }
            elseif($companyPlanDetails->status == 'deactivate'){
                $statusString  = trans('messages.admin_activate_status');
            }
    
            $paymentUrl = ($companyPlanDetails->validate_string ? route('plan-subscription', $companyPlanDetails->validate_string) : '');

            $companyPlan = array(
                'plan_name'         => $planDetails->plan_name ?? '',
                'plan_duration'     => $typeData,
                'truck_type'        => $columntypeData,
                'price'             => $companyPlanDetails->price,
                'discount'          => $companyPlanDetails->discount_price,
                'total_price'       => round($companyPlanDetails->total_price, 2),
                'start_time'        => date(config("Reading.date_format"),strtotime($companyPlanDetails->start_time)),
                'end_time'          => date(config("Reading.date_format"),strtotime($companyPlanDetails->end_time)),
                'url'               => $paymentUrl == "" ? null : $paymentUrl,
                'status'            => ($companyPlanDetails->status ? $companyPlanDetails->status : 'no_active_plan'),
                'status_string'     => $statusString,
            );

           
            $response["status"]       =    "success";
            $response["msg"]          =    trans('messages.admin_plan_detail');
            $response["data"]         =    $companyPlan;
            return response()->json($response);

        }

    }
}
