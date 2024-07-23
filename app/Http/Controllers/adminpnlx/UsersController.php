<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Config;
use App\Models\Lookup;  
use App\Models\User;
use Carbon\Carbon;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use Redirect,Session;

class UsersController extends Controller
{
    public $model      =   'users';
    public $sectionNameSingular      =   'customers';
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
        $this->request = $request;
    }

    public function index(Request $request)
	{
		$DB					=	User::query();
		$searchVariable		=	array();
		$inputGet			=	$request->all();
		if ($request->all()) {
			$searchData			=	$request->all();
			unset($searchData['display']);
			unset($searchData['_token']);

			if (isset($searchData['order'])) {
				unset($searchData['order']);
			}
			if (isset($searchData['sortBy'])) {
				unset($searchData['sortBy']);
			}
			if (isset($searchData['page'])) {
				unset($searchData['page']);
			}
			if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
			    $dateS = date("Y-m-d",strtotime($searchData['date_from']));
                $dateE =  date("Y-m-d",strtotime($searchData['date_to']));
				$DB->whereBetween('users.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
			} elseif (!empty($searchData['date_from'])) {
				$dateS = $searchData['date_from'];
				$DB->where('users.created_at', '>=', [$dateS . " 00:00:00"]);
			} elseif (!empty($searchData['date_to'])) {
				$dateE = $searchData['date_to'];
				$DB->where('users.created_at', '<=', [$dateE . " 00:00:00"]);
			}
			foreach ($searchData as $fieldName => $fieldValue) {
				if ($fieldValue != "") {

                    if ($fieldName == "name") {
                        $DB->where("users.name", 'like', '%' . $fieldValue . '%');
                    }
                    
                    if ($fieldName == "email") {
                        $DB->where("users.email", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("users.is_active", 'like', '%' . $fieldValue . '%');
                    }
				}
				$searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
			}
		}

        $DB->where("users.is_deleted", 0);
        $DB->Usertype(3);
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'users.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
        $resultcount = $results->count();
        return  View("admin.$this->model.index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string'));
	}

	
    public function create(Request $request)
    {       
        return  View("admin.$this->model.add");
    }

    public function Save(Request $request){
       if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $validator                    =   Validator::make(
                $request->all(), 
                array(
                    'name'                    => "required",
                    'email'                   => "required|email:rfc,dns",
                    'password'                => 'required|min:8',
                    'confirm_password'        => 'required|same:password',
                    'profile_image'           => 'nullable|mimes:jpg,jpeg,png'
                ), 
                array(
                    "name.required"              => trans("messages.This field is required"),
                    "email.required"             => trans("messages.This field is required"),
                    "email.email"                => trans("messages.The email must be a valid email address"),
                    "email.unique"               => trans("messages.The email must be unique"),
                    "password.required"          => trans("messages.This field is required"),
                    "password.min"               => trans("messages.admin_The_Password_must_be_atleast_8_characters_with_combination_of_atleast_have_one_alpha_one_numeral_and_one_special_character"),
                    "confirm_password.required"  => trans("messages.This field is required"),
                    "confirm_password.same"      => trans("messages.The confirm password must be the same as the password"),
                )
            );
            $password = $request->input('password');
            if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password) && preg_match('#[\W]#', $password)) {
            $correctPassword = Hash::make($password);
            } else {
            $errors = $validator->messages();
            $errors->add('password', ucfirst(trans("messages.admin_The_Password_must_be_atleast_8_characters_with_combination_of_atleast_have_one_alpha_one_numeral_and_one_special_character")));
            return Redirect::back()->withErrors($errors)->withInput();
            }
            if ($validator->fails()) {
    			return Redirect::back()->withErrors($validator)->withInput();
    		}else{
                $user                               =   new User;
                $user->user_role_id                 =   Config('constants.ROLE_ID.CUSTOMER_ROLE_ID');
                $user->name                         =   $request->input('name');
                $user->email                        =   $request->email;
                $user->password                     =   Hash::make($request->password);

                if ($request->hasFile('profile_image')) {
                    $file = rand() . '.' . $request->profile_image->getClientOriginalExtension();
                    $request->file('profile_image')->move(Config('constants.CUSTOMER_IMAGE_ROOT_PATH'), $file);
                    $user->image = $file;
                }

                $SavedResponse = $user->save();
                if (!$SavedResponse) {
                    Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                    return Redirect()->back()->withInput();
                } else {

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
                    $this->genrateAdminLog($logData);
                    Session()->flash('success', ucfirst(trans("messages.admin_Customer_has_been_added_successfully")));
                    return Redirect()->route($this->model . ".index");
                }
            }
        } 
    }

    public function edit(Request $request,  $enuserid = null)
    {   
      
       
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id        = base64_decode($enuserid);
            $userDetails    = User::find($user_id);


           
            return  View("admin.$this->model.edit", compact('userDetails'));
        } else {
            return redirect()->route($this->model . ".index");
        }
    }
 
    public function update(Request $request,  $enuserid = null){



         if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $user_id = '';
            $image = "";
            if (!empty($enuserid)) {
                $user_id = base64_decode($enuserid);
            } else {
                return redirect()->route($this->model . ".index");
            }
            $validator                    =   Validator::make(
                $request->all(), 
               array(
                    'name'              => "required",
                    'email'             => 'required|email:rfc,dns',
                    'profile_image'     => 'nullable|mimes:jpg,jpeg,png'
                    
                ),
                array(
                    "name.required"        => trans("messages.This field is required"),
                    "email.required"       => trans("messages.This field is required"),
                    "email.email"          => trans("messages.The email must be a valid email address"),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }else{
           
                $user                               =   User::where("id",$user_id)->first();
                $user->name                         =   $request->input('name');
                $user->email                        =   $request->email;
               

                if ($request->hasFile('profile_image')) {
                    $file = rand() . '.' . $request->profile_image->getClientOriginalExtension();
                    $request->file('profile_image')->move(Config('constants.CUSTOMER_IMAGE_ROOT_PATH'), $file);
                    $user->image = $file;
                }

                $SavedResponse = $user->save();

                if (!$SavedResponse) {
                    Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                    return Redirect()->back()->withInput();
                }
                Session()->flash('success', ucfirst(trans("messages.admin_Customer_has_been_updated_successfully")));

                    $logData=array(
                        'record_id'=>$user->id,
                        'module_name'=>'User',
                        'action_name' => 'edit',
                        'action_description' => 'Edit User Account',
                        'record_url' => route('users.show',base64_encode($user->id)),
                        'user_agent' => $request->header('User-Agent'),
                        'browser_device' => '',
                        'location' => '',
                        'ip_address' => $request->ip()
                    );

                    $this->genrateAdminLog($logData);

                return Redirect()->route($this->model . ".index");
            }
        }
    }

    public function destroy(Request $request, $enuserid)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails   =   User::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            $email              =   'delete_' . $user_id . '_' .!empty($userDetails->email);
            $phone_number       =   'delete_' . $user_id . '_' .!empty($userDetails->phone_number);

            User::where('id', $user_id)->update(array(
                'is_deleted'    => 1, 
                'email'         => $email, 
            ));

                    $logData=array(
                        'record_id'=>$user_id,
                        'module_name'=>'User',
                        'action_name' => 'delete',
                        'action_description' => 'Delete User Account',
                        'record_url' => '',
                        'user_agent' => $request->header('User-Agent'),
                        'browser_device' => '',
                        'location' => '',
                        'ip_address' => $request->ip()
                    );

                    $this->genrateAdminLog($logData);

            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Customer_has_been_removed_successfully")));
        }
        return back();
    }

    public function changeStatus(Request $request, $modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage   =   ucfirst(trans("messages.admin_Customer_has_been_activated_successfully"));
        } else {
            $statusMessage   =   ucfirst(trans("messages.admin_Customer_has_been_deactivated_successfully"));
        }
        $user = User::find($modelId);
        if ($user) {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
                $actionType = 'activeUser';
            } else {
                $NewStatus = 0;
                $actionType = 'deactiveUser';
            }
            $user->is_active = $NewStatus;
            $ResponseStatus = $user->save();

            $logData=array(
                'record_id'=>$user->id,
                'module_name'=>'User',
                'action_name' => $actionType,
                'action_description' => ucfirst($actionType).' Account',
                'record_url' => route('users.show',base64_encode($user->id)),
                'user_agent' => $request->header('User-Agent'),
                'browser_device' => '',
                'location' => '',
                'ip_address' => $request->ip()
            );

                    $this->genrateAdminLog($logData);
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }


    public function changedPassword(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        if ($request->isMethod('POST')) {
            if (!empty($user_id)) {
                $validator                  =   Validator::make(
                $request->all(),
                    array(
                    'new_password'      => 'required|min:8',
                    'confirm_password'  => 'required|same:new_password',
                ),
                array(
                    "new_password.required"      => trans("messages.This field is required"),
                    "new_password.min"           => trans("messages.admin_The_Password_must_be_atleast_8_characters_with_combination_of_atleast_have_one_alpha_one_numeral_and_one_special_character"),
                    "confirm_password.required"  => trans("messages.This field is required"),
                    "confirm_password.same"      => trans("messages.The confirm password must be the same as the password"),
                )
            );
            $password = $request->input('new_password');
            if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password) && preg_match('#[\W]#', $password)) {
            $correctPassword = Hash::make($password);
            } else {
            $errors = $validator->messages();
            $errors->add('new_password', ucfirst(trans("messages.admin_The_Password_must_be_atleast_8_characters_with_combination_of_atleast_have_one_alpha_one_numeral_and_one_special_character")));
            return Redirect::back()->withErrors($errors)->withInput();
            }if ($validator->fails()) {
                
            return Redirect::back()->withErrors($validator)->withInput();
            } else {

                $userDetails   =  User::find($user_id);
                $userDetails->password     =  Hash::make($request->new_password);
                $SavedResponse =  $userDetails->save();
                if (!$SavedResponse) {
                    Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                    return Redirect()->back();
                }

                $logData=array(
                    'record_id'=>$userDetails->id,
                    'module_name'=>'User',
                    'action_name' => 'changePassword',
                    'action_description' => 'User password changed',
                    'record_url' => route('users.show',base64_encode($userDetails->id)),
                    'user_agent' => $request->header('User-Agent'),
                    'browser_device' => '',
                    'location' => '',
                    'ip_address' => $request->ip()
                );

                $this->genrateAdminLog($logData);
                
                Session()->flash('success', ucfirst(trans("messages.Password has been changed successfully")));
                return Redirect()->route($this->model . '.index');
              }
            }
        }
        $userDetails = array();
        $userDetails   =  User::find($user_id);
        $data = compact('userDetails');
        return view("admin.$this->model.change_password", $data);
    }

   public function view($enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        $userDetails    =    User::where('users.id', $user_id)->first();
       return  View("admin.$this->model.view", compact('userDetails'));

    }

    public function sendCredentials(Request $request, $id){
        
        if(empty($id)){
            return redirect()->back();
        }

        $user           = 	User::find($id);

        if(!empty($user) && $user->email == ''){
            Session()->flash('error', ucfirst(trans("messages.email_id_of_this_user_is_not_updated")));
            return Redirect()->back();
        }
       
       
        $password       =   generatePassword();
       
        $settingsEmail 	= 	Config::get("Site.from_email");
        $full_name 		= 	$user->name;
        $email 			=	$user->email;
        $user->password =   Hash::make($password);
        $user->temp_pass =   1;

        $user->save();
        $emailActions 	= 	EmailAction::where('action','=','send_login_credentials')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','send_login_credentials')->get(array('name','subject','action','body'))-> toArray();
        $cons 			= 	explode(',',$emailActions[0]['options']);
        $constants 		= 	array();
        foreach($cons as $key => $val){
            $constants[] = '{'.$val.'}';
        }
        $subject 		= 	$emailTemplates[0]['subject'];
        $route_url      =  	Config('constants.WEBSITE_ADMIN_URL').'/login';	
        $rep_Array 		= 	array($full_name,$email,$password,$user->phone_number);
        $messageBody 	= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
        $this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail);
        Session()->flash('flash_notice', ucfirst(trans("messages.admin_Login_credentials_send_successfully")));

        $logData=array(
            'record_id'=>$user->id,
            'module_name'=>'User',
            'action_name' => 'sendCredentials',
            'action_description' => 'Send new credentials',
            'record_url' => route('users.show',base64_encode($user->id)),
            'user_agent' => $request->header('User-Agent'),
            'browser_device' => '',
            'location' => '',
            'ip_address' => $request->ip()
        );

        $this->genrateAdminLog($logData);

        return redirect()->back();
    }
    
}
