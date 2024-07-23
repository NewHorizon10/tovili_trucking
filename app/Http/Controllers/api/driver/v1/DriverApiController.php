<?php

namespace App\Http\Controllers\api\driver\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserVerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Cache, Cookie, Input, Mail, mongoDate, Response, Session, URL;
use Illuminate\Validation\Rules\Password;
use App\Models\UserDeviceToken;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentDriverSchedule;

class DriverApiController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request              =   $request;
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
                    'phone_number'              => 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                ),
                array(
                    "phone_number.required"     => trans("messages.This field is required"),
                    'phone_number.regex'        => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "password.required"         => trans("messages.This field is required"),
					"Password.between"          => trans("messages.password_should_be_in_between_4_to_8_characters"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            } else {
                $userDetail         = User::where('phone_number', $request->phone_number)
                    ->where('is_deleted', 0)
                    ->where('user_role_id', 4)
                    ->first();
                   
                  
                if (!empty($userDetail)) {
                    $AuthAttemptUser = 'users';

                    if (!empty($AuthAttemptUser)) {
                        if ($userDetail->is_active == 0) {
                            $response["status"]        =    "error";
                            $response["msg"]        =    trans("messages.Account Locked, Please contact to admin");
                            $response["data"]        =    (object)array();
                            return response()->json($response);
                        }

                        $login_validate_string    =     md5($userDetail->phone_number . time() . time());
                        $verification_code                    =    '9999'; //$this->getVerificationCodes();
                        $phone_number                        =    $request->input('phone_number');

                        User::where('phone_number', $phone_number)->update(array('login_validate_string' => $login_validate_string));
                        UserVerificationCode::where('phone_number', $phone_number)->where("type", "forget_password")->delete();
                        $obj                                 =     new UserVerificationCode;
                        $obj->phone_number                  =     $phone_number;
                        $obj->type                           =     'forget_password';
                        $obj->verification_code                =     $verification_code;
                        $obj->save();

                        $data = array(
                            'login_validate_string' =>    $login_validate_string,
                        );
                        $response["status"]                =    "success";
                        $response["msg"]            =   trans("messages.otp_has_been_sent_successfully_on_your_phone_number");
                        $response["data"]                =    $data;
                        return response()->json($response);
                    } else {
                        $response["status"]                     =    "error";
                        $response["msg"]                        =    trans("messages.Phone number is incorrect");
                        $response["data"]                       =    (object)array();
                        return response()->json($response);
                    }
                } else {
                    $response["status"]                         =    "error";
                    $response["msg"]                            =     trans("messages.Phone number is incorrect");
                    $response["data"]                           =    (object)array();
                }
            }
        } else {
            $response["status"]        =    "error";
            $response["msg"]        =    trans("messages.Invalid Request");
            $response["data"]        =    (object)array();
        }
        return response()->json($response);
    }


    public function verifyLoginOtp(Request $request)
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

            $userInfo = User::where('login_validate_string', $request->validate_string)->with('userDriverDetail')->first();
      
           
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
            
            //logout from other divice
            $userForLogout =  User::find($userInfo->id);
            UserDeviceToken::where("user_id",$userForLogout->id)->delete();
            $userForLogout->tokens()->delete();

            $userInfo->language = getAppLocaleId();
            $userInfo->save();
            if(!empty($request->input('device_id')) && !empty($request->input('device_type'))){
                $UserDeviceToken = UserDeviceToken::where("user_id",$userInfo->id)->first();
                if(!empty($UserDeviceToken)){
                    $userDetail = $UserDeviceToken;
                    $userDetail->user_id = $userInfo->id;
                    $userDetail->device_type = $request->input('device_type');
                    $userDetail->device_id = $request->input('device_id');
                    $userDetail->device_token = (!empty($request->input('device_id'))) ? $request->input('device_id') : "";
                    $userDetail->save();
                }else{
                    $userDetail = new UserDeviceToken();
                    $userDetail->user_id = $userInfo->id;
                    $userDetail->device_type = $request->input('device_type');
                    $userDetail->device_id = $request->input('device_id');
                    $userDetail->device_token = (!empty($request->input('device_id'))) ? $request->input('device_id') : "";
                    $userDetail->save();
                }
            }  
            if($userInfo->userDriverDetail->driver_picture){
                $userInfo->driver_image = Config('constants.DRIVER_PICTURE_PATH').$userInfo->userDriverDetail->driver_picture;
            }else{
                $userInfo->driver_image = $userInfo->image;
            }
            $UserVerificationCode->delete();
            $token                              =    $userInfo->createToken('tovilli Personal Access Client')->accessToken;
            $response["status"]                 =    "success";
            $response["msg"]                    =    trans("messages.Login successfully");
            $response["passwordReset"]          =    1;
            $response["data"]                   =    $userInfo;
            $response["token"]                  =    $token;
            return response()->json($response);
        }
    }
    public function resetPassword(Request $request)
    {

        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {

            $validator = Validator::make(
                $request->all(),
                array(
                    'phone_number'                          => 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                ),
                array(
                    "phone_number.required"                 => trans("messages.This field is required"),
                    'phone_number.regex'                    => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "phone_number.digits"                   => trans("messages.Phone number should be 10 digits"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }

            $phone_number        =    $request->input('phone_number');
            $userDetail    =    User::where('phone_number', $phone_number)
                ->where('user_role_id', 4)
                ->where("is_deleted", 0)->first();
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
            $verification_code        =    $this->getVerificationCodes();
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
            $response["msg"]            =    trans("messages.OTP Verfied Successfully");
            $response["data"]            =   $data;
            
            return response()->json($response);
        }
    }

    public function createNewPassword(Request $request)
    {
        $validate_string = $request->validate_string;

        $userInfo = User::where('forgot_password_validate_string', $validate_string)->first();
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
                'password'                      => ['required', 'string', 'between:4,8', 'regex:'.Config('constants.PASSWORD_VALIDATION_STRING')],
                'confirm_password'              => 'required|same:password',
            ),
            array(
                "password.required"             => trans("messages.This field is required"),
                "password.between"              => trans("messages.password_should_be_in_between_4_to_8_characters"),
                "password.regex"          		=> trans("messages.".Config('constants.PASSWORD_VALIDATION_MESSAGE_STRING')),
                "password.digits"               => trans("messages.Password should be 4 digits"),
                "confirm_password.required"     => trans("messages.This field is required"),
                "confirm_password.same"         => trans("messages.The confirm password must be the same as the password"),
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

    public function changePassword(Request $request)
    {
        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $this->request->replace($this->arrayStripTags($request->all()));

            $validator = Validator::make(
                $request->all(),
                [
                    'old_password'                 => 'required',
                    'password'                  => ['required', 'string', 'between:4,8', 'regex:'.Config('constants.PASSWORD_VALIDATION_STRING')],
                    'confirm_password'          => 'required|same:password',
                ],
                [
                    "old_password.required"     => trans("messages.The old password field is required"),
                    "password.required"            => trans("messages.This field is required"),
                    "Password.between"          			=> trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "Password.regex"          				=> trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),
                    "password.digits"            => trans("messages.Password should be 4 digits"),
                    "confirm_password.required"    => trans("messages.This field is required"),
                    "confirm_password.same"        => trans("messages.The confirm password must be the same as the password"),
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

    public function updateProfile(Request $request)
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
                    'email'                         => "required|nullable|email",
                    'phone_number'                  => "required|unique:users,phone_number," . $user->id . '|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'location'                      => "required"
                ),
                array(
                    "name.required"                 => trans("messages.This field is required"),
                    "phone_number.required"         => trans("messages.This field is required"),
                    "phone_number.unique"           => trans("messages.The phone number must be unique"),
                    'phone_number.regex'            => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "email.required"                => trans("messages.This field is required"),
                    "email.email"                   => trans("messages.The email must be a valid email address"),
                    "email.unique"                  => trans("messages.The email must be unique"),
                    "location.required"             => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }
            $this->last_activity_date_time($user->id);
            $user                                   =   User::find($user->id);
            $user->name                             =   $request->input('name');
            $user->email                            =   isset($request->email) ? $request->email : '';
            $user->phone_number                     =   $request->phone_number;
            $user->location                         =   $request->location;
            $user->save();

            $response["status"]                =    "success";
            $response["msg"]                =    trans("messages.Profile has been updated successfully");
            $response["data"]                =   (object)array();

            return response()->json($response);
        } else {

            $response["status"]                =    "error";
            $response["msg"]                =    trans("messages.Invalid Request");
            $response["data"]                =   (object)array();
            return response()->json($response);
        }
    }
    
	public function logout(Request $request){
        UserDeviceToken::where("user_id",Auth::guard('api')->user()->id)->delete();
        $this->last_activity_date_time(Auth::guard('api')->user()->id);
		Auth::guard('api')->user()->tokens()->delete();
		$response["status"]		=	"success";
		$response["msg"]		=	trans("messages.You are logout successfully");
		$response["data"]		=	(object)array();
		return json_encode($response);
	}

    public function dashboard(Request $request) {
        $results = array();

        $results["total_new_notifications"] = Notification::where('user_id',Auth::guard('api')->user()->id)->where("language_id",getAppLocaleId())->where("is_read", 0)->count();
        
        $totalActiveShipment                     =   ShipmentDriverSchedule::where('driver_id',Auth::guard('api')->user()->id)
        ->Where('shipment_status','start')
        ->get();

        $totalUpcomingShipment                     =   ShipmentDriverSchedule::where('shipment_driver_schedules.shipment_status','not_start')
        ->where('driver_id',Auth::guard('api')->user()->id)
        ->get();

        $totalPastShipment                     =   ShipmentDriverSchedule::where('shipment_driver_schedules.shipment_status','end')
        ->where('driver_id',Auth::guard('api')->user()->id)
        ->get();

        $results["active"]                = $totalActiveShipment->count(); 
        $results["upcoming"]              = $totalUpcomingShipment->count();
        $results["past"]                  = $totalPastShipment->count();

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
        ////////////
        
        $response                                   =   array();
        $response["status"]                         =   "success";
        $response["msg"]                            =   "";
        $response["data"]                           =   $results;
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


    public function updateDriverLocation(Request $request)
    { 
        $formData    =    $request->all();
        if (!empty($formData)) {

            $request->replace($this->arrayStripTags($request->all()));

            $validator = Validator::make(
                $request->all(),
                array(
                    'current_location'              => 'required',
                    'current_lat'                   => 'required',
                    'current_lng'                   => 'required',
                ),
                array(
                    "current_location.required"     => trans("messages.This field is required"),
                    "current_lat.required"          => trans("messages.This field is required"),
                    "current_lng.required"          => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            }
            if (Auth::guard('api')->user()) {
                $user = Auth::guard('api')->user();
            }

            $companyObj = User::find($user->id);
            $companyObj->current_location                  = $request->current_location;
            $companyObj->current_lat                       = $request->current_lat;
            $companyObj->current_lng                       = $request->current_lng;
            $companyObj->save();

            $response["status"]                =    "success";
            $response["msg"]                =    trans("messages.driver_current_location_updated_successfully");
            $response["data"]                =   (object)array();
            return response()->json($response);
        } else {
            $response["status"]                =    "error";
            $response["msg"]                =    trans("messages.Invalid Request");
            $response["data"]                =   (object)array();
            return response()->json($response);
        }
    }

    public function viewNotifications(Request $request){
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

    public function changeLanguage(Request $request){
        $user                               =   User::find(Auth::guard('api')->user()->id);
        $selectedLanguage                   =   Language::where("lang_code",$request->lang_code)->first();

        $user->language                     =   $selectedLanguage->id;
        $user->save();
        $response["status"]                 =    "success";
        $response["data"]                   =    (object)array();
        return response()->json($response);
    }



}