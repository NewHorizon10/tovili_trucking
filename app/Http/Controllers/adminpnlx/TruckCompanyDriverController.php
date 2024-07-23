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
use App\Models\UserDriverDetail;
use App\Models\UserCompanyInformation;  
use Carbon\Carbon;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use Redirect,Session;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Exports\ReportExport;

class TruckCompanyDriverController extends Controller
{
    public $model      =   'truck-company-driver';
    public $sectionNameSingular      =   'truck-company-driver';
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
        $this->request = $request;
    }

    public function index(Request $request)
	{
        $TCuserid        = $request->truck_id;
		$DB					=	User::query();
        $DB->leftjoin('users as user_company', 'users.truck_company_id' , 'user_company.id')
         ->leftjoin('user_driver_details', 'users.id' , 'user_driver_details.user_id')
         ->leftjoin('user_company_informations', 'user_company.id' , 'user_company_informations.user_id')
        ->select('users.*', 'user_company_informations.company_name','user_driver_details.licence_number','user_driver_details.licence_exp_date');

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

            elseif (!empty($searchData['licence_number'])) {
				$lincenceNumber = $searchData['licence_number'];
				$DB->where('user_driver_details.licence_number', $lincenceNumber);
			}

            elseif (!empty($searchData['system_id'])) {
				$system_id = $searchData['system_id'];
				$DB->where('users.system_id', $system_id);
			}

            elseif (!empty($searchData['licence_exp_date'])) {
				$dateE = $searchData['licence_exp_date'];
				$DB->where('user_driver_details.licence_exp_date', date('Y-m-d',strtotime($dateE)));
			}
			foreach ($searchData as $fieldName => $fieldValue) {
				if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("users.name", 'like', '%' . $fieldValue . '%');
                    }                    
                    if ($fieldName == "email") {
                        $DB->where("users.email", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "phone_number") {
                        $DB->where("users.phone_number", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "system_id") {
                        $DB->where("users.system_id", 'like', '%' . $fieldValue . '%');
                    } 

                    if ($fieldName == "is_active") {
                        $DB->where("users.is_active", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "is_active") {
                        $DB->where("users.is_active", 'like', '%' . $fieldValue . '%');
                    }
                    if (isset($searchData["company_city"]) && isset($searchData["company_latitude"]) && isset($searchData["company_longitude"])) {
                        $distance = 50;
                        $latitude = $searchData["company_latitude"] ?? 0;
                        $longitude = $searchData["company_longitude"] ?? 0;
                        
                        $DB->havingRaw("(6371 * acos(cos(radians(?)) * cos(radians(users.current_lat)) * cos(radians(users.current_lng) - radians(?)) + sin(radians(?)) * sin(radians(users.current_lat)))) < ?", [$latitude, $longitude, $latitude, $distance]);
                    }

				}
				$searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
			}
		}
        $DB->where("users.is_deleted", 0);
        $DB->where("users.user_role_id", 4);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'users.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");

        $DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_company_track_drivers'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);

		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
        $resultcount = $results->count();
        return  View("admin.$this->model.index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string','TCuserid'));
	}

    public function create(Request $request)
    {      
        
        $TCuserid        = $request->truck_id;
        $companies    = User::where('user_role_id',3)
        ->where('is_active',1)
        ->where('is_deleted',0)
        ->with('userCompanyInformation')
        ->get();
        return  View("admin.$this->model.add",compact('companies','TCuserid'));
    }

    public function Save(Request $request){
        $TCuserid        = $request->truck_id;

       if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $validator                    =   Validator::make(
                $request->all(), 
                array(
                    'name'                          => "required",
                    'email'                         => "required|email:rfc,dns",
                    'phone_number'                  => 'required|unique:users,phone_number|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'driver_picture'                => 'required',
                ), 
                array(
                    "name.required"                         => trans("messages.This field is required"),
                    "password.required"                     => trans("messages.This field is required"),
                    "password.between"                      => trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "password.regex"                        => trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),
                    "confirm_password.required"             => trans("messages.This field is required"),
                    "confirm_password.same"                 => trans("messages.The confirm password must be the same as the password"),
                    "phone_number.required"                 => trans("messages.This field is required"),
                    "phone_number.unique"                   => trans("messages.Mobile number already in use"),
                    "phone_number.regex"                    => trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
                    "truck_company_id.required"             => trans("messages.This field is required"),
                    "email.required"                        => trans("messages.This field is required"),
                    "email.email"                           => trans("messages.The email must be a valid email address"),
                    "driver_picture.required"               => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();

            }else{

                try {

                    DB::beginTransaction();

                        $user                               =   new User;
                        $user->user_role_id                 =   Config('constants.ROLE_ID.TRUCK_COMPANY_DRIVER_ID');
                        $user->name                         =   $request->input('name');
                        $user->truck_company_id             =   $request->input('truck_company_id');
                        $user->email                        =   $request->email;
                        $user->phone_number                 =   $request->phone_number;
                        $user->customer_type                =   'business';
                        $user->save();
                        $user->system_id =0;
                        $user->save();

                        $system_id  =   1000+$user->id;
                        User::where("id",$user->id)->update(array("system_id"=>$system_id));

                        $driverDetails                      =   new UserDriverDetail;
                        $driverDetails->user_id             =   $user->id;
                        $driverDetails->licence_number      =   $request->input('licence_number') ?? '';
                        $driverDetails->licence_exp_date    =   date('Y-m-d', strtotime($request->input('licence_exp_date'))) ?? '';

                        if ($request->hasFile('driver_picture')) {
                            $file = rand() . '.' . $request->driver_picture->getClientOriginalExtension();
                            $request->file('driver_picture')->move(Config('constants.DRIVER_PICTURE_ROOT_PATH'), $file);
                            $driverDetails->driver_picture =  $file;
                        }
                        $driverDetails->save();

                        DB::commit();

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
                        Session()->flash('success', ucfirst(trans("messages.admin_Driver_has_been_added_successfully")));

                        $from_page = $request->from_page ?? '';
                         if($from_page == "tc_edit"){
                           return redirect()->route("truck-company.edit", array(
                                base64_encode($TCuserid),
                                'from_page' => 'tc_edit',
                                'tabs' => 'driver_detail'
                            ));
                        }
                        else if($from_page == "tc_view"){
                           return redirect()->route("truck-company.show", array(
                                base64_encode($TCuserid),
                                'from_page' => 'tc_view',
                                'tabs' => 'driver_detail'
                            ));
                        }
                        else{
                         return Redirect()->route($this->model . ".index",($TCuserid? ["truck_id=".$TCuserid] :null));
                        }

                } catch (\Throwable $th) {

                    DB::rollback(); 
                    Session()->flash('error',$th->getMessage());
                    return Redirect::back();
                }
            }
        } 
    }

    public function edit(Request $request,  $enuserid = null){   
            
        $TCuserid        = $request->truck_id;

        $user_id = '';
        
        if (!empty($enuserid)) {
            $user_id        = base64_decode($enuserid);
            $userDetails    = User::where('id',$user_id)->with('userCompanyInformation')->with('userDriverDetail')->first();
            $companyType    = Lookup::where('lookup_type',"company-type")->with('lookupDiscription')->get();
            $companies      = User::where('user_role_id',3)
                ->where('is_active',1)
                ->where('is_deleted',0)
                ->with('userCompanyInformation')
                ->get();
            return  View("admin.$this->model.edit", compact('userDetails','companies','TCuserid'));
        } else {
            return redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request,  $enuserid = null){

        $TCuserid        = $request->truck_id;
        
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
                    'name'                  => "required",
                    'email'                 => "required|email:rfc,dns",
                    'phone_number'          => 'required|unique:users,phone_number,'.$user_id.'|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'truck_company_id'      => 'required',
                ), 
                array(
                    "name.required"                     => trans("messages.This field is required"),
                    "phone_number.required"             => trans("messages.This field is required"),
                    "phone_number.unique"               => trans("messages.Mobile number already in use"),
                    "phone_number.regex"                    => trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
                    "email.required"                    => trans("messages.This field is required"),
                    "truck_company_id.required"         => trans("messages.This field is required"),
                    "email.email"                       => trans("messages.The email must be a valid email address"),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }else{
                $user                               =   User::where("id",$user_id)->first();
                $user->name                         =   $request->input('name');
                $user->email                        =   $request->email;
                $user->phone_number                 =   $request->phone_number;
                $user->truck_company_id             =   $request->truck_company_id;
                $user->save();

                $driverDetails                      =   UserDriverDetail::where('user_id',$user_id)->first();
                $driverDetails->user_id             =   $user->id;
                $driverDetails->licence_number      =   $request->input('licence_number') ?? '';
                $driverDetails->licence_exp_date    =   date('Y-m-d', strtotime($request->input('licence_exp_date'))) ?? '';


                if ($request->hasFile('driver_picture')) {
                    $file = rand() . '.' . $request->driver_picture->getClientOriginalExtension();
                    $request->file('driver_picture')->move(Config('constants.DRIVER_PICTURE_ROOT_PATH'), $file);
                    $driverDetails->driver_picture =  $file;
                }

                $driverDetails->save();

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

                Session()->flash('success', ucfirst(trans("messages.admin_Driver_has_been_updated_successfully")));

                $from_page = $request->from_page ?? '';
                
                if($from_page == 'tc_edit'){
                    return redirect()->route("truck-company.edit", array(
                        base64_encode($request->truck_id),
                        'from_page' => 'tc_edit',
                        'tabs' => 'driver_detail'
                    ));
                }
                else if($from_page == 'tc_view'){
                    return redirect()->route("truck-company.show", array(
                        base64_encode($request->truck_id),
                        'from_page' => 'tc_edit',
                        'tabs' => 'driver_detail'
                    ));
                }
                else{
                      return Redirect()->route($this->model . ".index",($TCuserid? ["truck_id=$TCuserid"] :[]));
                }

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
            DB::table('oauth_access_tokens')->where("user_id", $user_id)->delete();
            $email              =   'delete_' . $user_id . '_' .!empty($userDetails->email);
            $phone_number       =   'delete_' . $user_id . '_' .!empty($userDetails->phone_number);

            User::where('id', $user_id)->update(array(
                'is_deleted'    => 1, 
                'email'         => $email, 
                'phone_number'  => null,
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

            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Driver_has_been_removed_successfully")));
        }
 
        $from_page = $request->from_page ?? '';
        $tc_id     = $request->tc_id ?? '';
        
        if($from_page == "tc_edit"){
           return redirect()->route("truck-company.edit", array(
                $tc_id,
                'from_page' => 'tc_edit',
                'tabs' => 'driver_detail'
            ));
        }
        else if($from_page == "tc_view"){
           return redirect()->route("truck-company.show", array(
                $tc_id,
                'from_page' => 'tc_view',
                'tabs' => 'driver_detail'
                    ));
        }
        else{
              return back();
        }

    }

    public function changeStatus(Request $request, $modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage   =   ucfirst(trans("messages.admin_Driver_has_been_activated_successfully"));
        } else {
            $statusMessage   =   ucfirst(trans("messages.admin_Driver_has_been_deactivated_successfully"));
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

        $from_page = $request->from_page ?? '';
        $tc_id     = $request->tc_id ?? '';
        
        if($from_page == "tc_edit"){
            return redirect()->route("truck-company.edit", array(
                $tc_id,
                'from_page' => 'tc_edit',
                'tabs' => 'driver_detail'
            ));
        }
        else if($from_page == "tc_view"){
            return redirect()->route("truck-company.show", array(
                $tc_id,
                'from_page' => 'tc_view',
                'tabs' => 'driver_detail'
            ));
        }else{
            return back();
        }

    }


    public function changedPassword(Request $request, $enuserid = null, $enTCuserid = false){
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
                    'new_password'      => ['required', 'string', 'between:4,8', 'regex:'.Config('constants.PASSWORD_VALIDATION_STRING')],
                    'confirm_password'  => 'required|same:new_password',
                ),
                array(
                    "new_password.required"             => trans("messages.This field is required"),
                    "new_password.between"              => trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "new_password.regex"                => trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),
                    "confirm_password.required"         => trans("messages.This field is required"),
                    "confirm_password.same"             => trans("messages.The confirm password must be the same as the password"),
                )
            );
            
            if ($validator->fails()) {
                
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
                return Redirect()->route($this->model . '.index',($enTCuserid? [$enTCuserid] :[]));
              }
            }
        }
        $userDetails = array();
        $userDetails   =  User::find($user_id);
        $data = compact('userDetails','enTCuserid');
        return view("admin.$this->model.change_password", $data);
    }

    public function view(Request $request, $enuserid = null){
        $user_id = '';
        $TCuserid        = $request->truck_id;

        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        $userDetails    =   User::where('id',$user_id)->with('TruckCompanyInformation','userDriverDetail')->first();
       return  View("admin.$this->model.view", compact('userDetails','TCuserid'));

    }

    public function sendCredentials(Request $request, $id){
        if(empty($id)){
            return redirect()->back();
        }
        $password = rand(1000, 9999);
        $user  = 	User::find($id);
        $settingsEmail 	= 	Config::get("Site.from_email");
        $full_name 		= 	$user->name;
        $email 			=	$user->email;
        $user->password = Hash::make($password);
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



    public function export(Request $request)
	{


        $list[0] = array(
            trans('messages.Company Name'),
            trans('messages.name'),
            trans('messages.admin_phone_number'),
            trans('messages.admin_app_status'),
            trans('messages.admin_Created_On'),
            trans('messages.admin_common_Status'),
		);


		$customers_export = Session::get('export_data_company_track_drivers');

		foreach ($customers_export as $key => $excel_export) {


            $list[] = array(
                $excel_export->company_name,
                $excel_export->name,
                $excel_export->phone_number,
                date(config("Reading.date_format"), strtotime($excel_export->licence_exp_date)),
                date(config("Reading.date_format"), strtotime($excel_export->created_at)),
                ($excel_export->is_active==1 ? 'Activated' : 'Deactivated' ),

            );
		}
	
        $collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'Truck Company Drivers.xlsx');

         
	}
    
}
