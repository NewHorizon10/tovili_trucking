<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Config;
use App\Models\Admin;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use URL;
use Session,App;

class LoginController extends Controller
{
    public function __construct(Request $request)
    {   
        parent::__construct();
        $this->request = $request;
    }

    public function login(Request $request)
    {
        
        $language_id = $this->current_language_id();
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }
        if ($request->isMethod('POST')) {
            $rules = array(
                'email'   => 'required|email:rfc,dns',
                'password' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $userdata = array(
                    'email'       => $request->email,
                    'password'    => $request->password,
                    'is_deleted'  => 0,
                );
                
                if (auth()->guard('admin')->attempt($userdata)) {
                    if (auth()->guard('admin')->user()->is_active == 0) {
                        auth()->guard('admin')->logout();
                        Session()->flash('error', ucfirst(trans("messages.Your account has been deactivated , please contact to admin for more details")));
                        return back()->withInput();
                    } 

                    $lang = App::getLocale();
                    $lanid        =    DB::table("languages")->where('lang_code', '=', $lang)->first()->id;
                    session()->put('sel_lang',$lanid);
                    $admin_modules	=	$this->buildTree();
                    Session()->put('acls',$admin_modules);
					
                    $logData=array(
                        'user_id' => Auth::guard('admin')->id(),
                        'record_id'=>Auth::guard('admin')->id(),
                        'module_name'=>'Login',
                        'action_name' => 'loginSuccess',
                        'action_description' => 'Login Success',
                        'record_url' => '',
                        'user_agent' => $request->header('User-Agent'),
                        'browser_device' => '',
                        'location' => '',
                        'ip_address' => $request->ip()
                    );
                    Session::put('admin_applocale', App::getLocale());

                    $this->genrateAdminLog($logData);

                    Session()->flash('flash_notice', ucfirst(trans("messages.you_have_successfully_logged_in")));
                    return Redirect()->route('dashboard');
                } else {
                    $checkAdmin = Admin::where('email',$request->email)->first();
                    if($checkAdmin){
                        $logData=array(
                            'user_id' => $checkAdmin->id,
                            'record_id'=>$checkAdmin->id,
                            'module_name'=>'Login',
                            'action_name' => 'loginFailed',
                            'action_description' => 'Login Failed',
                            'record_url' => '',
                            'user_agent' => $request->header('User-Agent'),
                            'browser_device' => '',
                            'location' => '',
                            'ip_address' => $request->ip()
                        );
                        
                        $this->genrateAdminLog($logData);
                    }
                    Session()->flash('error', ucfirst(trans("messages.admin_common_Email_or_Password_is_incorrect")));
                    return back()->withInput();
                }
            }
        }
        return view('admin.login.index');
    }

    public function logout(Request $request){

        $authId = Auth::guard('admin')->id();
        auth()->guard('admin')->logout();
        
        $logData=array(
            'user_id' => $authId,
            'record_id'=>$authId,
            'module_name'=>'Login',
            'action_name' => 'logout',
            'action_description' => 'logout',
            'record_url' => '',
            'user_agent' => $request->header('User-Agent'),
            'browser_device' => '',
            'location' => '',
            'ip_address' => $request->ip()
        );
                        
        $this->genrateAdminLog($logData);

        Session()->flash('flash_notice', ucfirst(trans("messages.You_are_logged_out")));
        return Redirect()->route('adminpnlx');
    }

    public function forgetPassword(Request $request){
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }
        if ($request->isMethod('POST')) {
      

            $rules = array(
                'email'   => 'required|email:rfc,dns',
            );
            
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $email              =    $request->email;
                $userDetail         =    Admin::where('email', $email)->first();
             
                if (!empty($userDetail)) {
                    if ($userDetail->is_active == 1) {
                        $forgot_password_validate_string    =     md5($userDetail->email . time() . time());
                        Admin::where('email', $email)->update(array('forgot_password_validate_string' => $forgot_password_validate_string));
                        $settingsEmail          =  Config('Site.email');
                        $email                  =  $userDetail->email;
                        $full_name              =  $userDetail->full_name;
                        URL::to('adminpnlx/reset_password/'.$forgot_password_validate_string);
                        $route_url              =  URL::to('adminpnlx/reset_password/'.$forgot_password_validate_string);
                        $emailActions           =  EmailAction::where('action', '=', 'forgot_password')->get()->toArray();
                        $language_id            =  1;
                        $emailTemplates         =  EmailTemplate::where('action', '=', 'forgot_password')->get()->toArray();
                        $cons = explode(',', $emailActions[0]['options']);
                        $constants = array();
                        foreach ($cons as $key => $val) {
                            $constants[] = '{' . $val . '}';
                        }
                        $messages = array(
                            'email.required'                 => ucfirst(trans('messages.the_email_field_is_required')),
                        );

                        $subject             =  $emailTemplates[0]['subject'];
                        $rep_Array             = array($email, $route_url);
                        $messageBody        =  str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
                        $this->sendMail($email, $full_name, $subject, $messageBody, $settingsEmail);

                     
                        Session()->flash('flash_notice', ucfirst(trans("messages.An_email_has_been_sent_to_your_inbox_To_reset_your_password_please_follow_the_steps_mentioned_in_the_email")));

                        $logData=array(
                            'user_id' => $userDetail->id,
                            'record_id'=>$userDetail->id,
                            'module_name'=>'Login',
                            'action_name' => 'forgotPassword',
                            'action_description' => 'forgot password',
                            'record_url' => '',
                            'user_agent' => $request->header('User-Agent'),
                            'browser_device' => '',
                            'location' => '',
                            'ip_address' => $request->ip()
                        );
                                        
                        $this->genrateAdminLog($logData);

                        return Redirect()->route('adminpnlx');
                    } else {
                        return Redirect()->route('forget_password')->with('error', ucfirst(trans("messages.Account Locked, Please contact to admin")));
                    }
                } else {
                    return Redirect()->route('adminpnlx')->with('error', ucfirst(trans("messages.admin_Your_email_is_not_registered_with_Us")));
                }
            }
        }
        return view('admin.login.forget_password');
    }

    public function resetPassword($validate_string = null, Request $request) {
        if ($validate_string != "" && $validate_string != null) {
            $userDetail    =    Admin::where('is_active', '1')->where('forgot_password_validate_string', $validate_string)->first();
            if (!empty($userDetail)) {
                return View('admin.login.reset_password', compact('validate_string'));
            } else {
                return Redirect()->route('adminpnlx')
                    ->with('error', ucfirst(trans("messages.admin_Sorry_you_are_using_wrong_link")));
            }
        } else {
            return Redirect()->route('adminpnlx')->with('error', ucfirst(trans("messages.admin_Sorry_you_are_using_wrong_link")));
        }
    }

    public function save_password($validate_string = null, Request $request){
        $thisData                =    $request->all();
        $newPassword        =    $request->input('new_password');
        $validate_string    =    $request->input('validate_string');
        $messages = array(
            'new_password.required'                 => trans("messages.This field is required"),
            'new_password_confirmation.required'     => trans("messages.This field is required"),
            'new_password.confirmed'                 => trans('messages.The confirm password must be the same as the password'),
            'new_password.min'                         => trans('messages.admin_The_new_password_must_be_at_least_8_characters'),
            'new_password_confirmation.min'         => trans('messages.admin_The_confirm_password_must_be_at_least_8_characters'),
            "new_password.custom_password"            => trans("messages.admin_The_Password_must_be_atleast_8_characters_with_combination_of_atleast_have_one_alpha_one_numeral_and_one_special_character"),
        );
        Validator::extend('custom_password', function ($attribute, $value, $parameters) {
            if (preg_match('#[0-9]#', $value) && preg_match('#[a-zA-Z]#', $value) && preg_match('#[\W]#', $value)) {
                return true;
            } else {
                return false;
            }
        });
        $validator = Validator::make(
            $request->all(),
            array(
                'new_password'                => 'required|min:8|custom_password',
                'new_password_confirmation' => 'required|same:new_password',
            ),
            $messages
        );
        if ($validator->fails()) {
            return Redirect()->to('adminpnlx/reset_password/' . $validate_string)
                ->withErrors($validator)->withInput();
        } else {
            $userInfo = Admin::where('forgot_password_validate_string', $validate_string)->first();
            Admin::where('forgot_password_validate_string', $validate_string)
                ->update(array(
                    'password'                            =>    Hash::make($newPassword),
                    'forgot_password_validate_string'    =>    ''
                ));
            $settingsEmail         = Config('Site.email');

           
            $action                = "reset_password";
            $emailActions        =    EmailAction::where('action', '=', 'reset_password')->get()->toArray();
            $emailTemplates        =    EmailTemplate::where('action', '=', 'reset_password')->get(array('name', 'subject', 'action', 'body'))->toArray();
            $cons                 =     explode(',', $emailActions[0]['options']);
            $constants             =     array();
            foreach ($cons as $key => $val) {
                $constants[] = '{' . $val . '}';
            }
            $subject             =  $emailTemplates[0]['subject'];
            $rep_Array             = array($userInfo->name);
            $messageBody        =  str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
            $this->sendMail($userInfo->email, $userInfo->name, $subject, $messageBody, $settingsEmail);

            $logData=array(
                'user_id' => $userInfo->id,
                'record_id'=>$userInfo->id,
                'module_name'=>'Login',
                'action_name' => 'resetPassword',
                'action_description' => 'reset password',
                'record_url' => '',
                'user_agent' => $request->header('User-Agent'),
                'browser_device' => '',
                'location' => '',
                'ip_address' => $request->ip()
            );
                                
            $this->genrateAdminLog($logData);

            Session()->flash('flash_notice', ucfirst(trans('messages.admin_Thank_you_for_resetting_your_password_Please_login_to_access_your_account')));
            return Redirect()->route('adminpnlx');
        }
    } 
}
