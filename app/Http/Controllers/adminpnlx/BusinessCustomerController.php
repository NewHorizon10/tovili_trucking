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
use App\Models\Shipment;
use App\Models\User;
use App\Models\UserCompanyInformation;  
use Carbon\Carbon;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use Redirect,Session;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Exports\ReportExport;

class BusinessCustomerController extends Controller
{
    public $model      =   'business-customers';
    public $sectionNameSingular      =   'business-customers';
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
        $DB->leftjoin('user_company_informations', 'users.id' , 'user_company_informations.user_id')
        ->select(
            'users.*',
            'user_company_informations.company_name',
            'user_company_informations.company_type',
            'user_company_informations.company_location',
            DB::Raw("(select shipments.created_at from shipments where shipments.customer_id = users.id ORDER BY shipments.id DESC LIMIT 1) as last_active_date")
        );

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
            $form_latitude = $request->current_lat;
            $form_longitude = $request->current_lng;
            if($request->city_name && $form_latitude != "" && $form_longitude != ""){
                $DB->where(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( users.current_lat ) ) * cos( radians(  users.current_lng  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( users.current_lat ) ) ))"),"<=",15);
                $DB->orderBy(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( users.current_lat ) ) * cos( radians(  users.current_lng  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( users.current_lat ) ) ))"),"ASC");
            }



			foreach ($searchData as $fieldName => $fieldValue) {
				if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("users.name", 'like', '%' . $fieldValue . '%');
                    }                    
                    if ($fieldName == "phone_number") {
                        $DB->where("users.phone_number", 'like', '%' . $fieldValue . '%');
                    }                  
                    if ($fieldName == "company_hp_number") {
                        $DB->where("company_hp_number", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "company_id") {
                        $DB->where("user_company_informations.company_id", 'like', '%' . $fieldValue . '%');
                    } 
                    
                    if ($fieldName == "company_name") {
                        $DB->where("user_company_informations.company_name", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "company_type") {
                        $DB->where("user_company_informations.company_type", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "company_location") {
                        $DB->where("user_company_informations.company_location", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "is_active") {
                        if ($fieldValue == 'inactive_over_30_days') {
                            $data = $DB->whereRaw('COALESCE(((select shipments.created_at from shipments where shipments.customer_id = users.id ORDER BY shipments.id DESC LIMIT 1)), users.updated_at) < ?', [Carbon::now()->subDays(30)]);
                        } else {
                           
                           $data =  $DB->whereRaw('COALESCE((SELECT MAX(shipments.created_at) FROM shipments WHERE shipments.customer_id = users.id), users.updated_at) > ?', [Carbon::now()->subDays(30)]);
                            $DB->where("users.is_active", $fieldValue );
                        }
                        
                    }
                    

                    if ($fieldName == "system_id") {
                        $DB->where("users.system_id", 'like', '%' . $fieldValue . '%');
                    }
				}
				$searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
			}
		}
        $DB->where("users.is_deleted", 0);
        $DB->where("users.user_role_id", 2);
        $DB->where("users.customer_type", 'business');
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'users.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");

        $DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_business_customer'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);



		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
        $resultcount = $results->count();
        $companyType    = Lookup::where('lookup_type',"company-type")->with(['lookupDiscription' => function($query) {
	        $query->where(['language_id' => getAppLocaleId()]);
        }])->get();
        return  View("admin.$this->model.index", compact('resultcount', 'results', 'companyType', 'searchVariable', 'sortBy', 'order', 'query_string'));
	}

	 
    public function create(Request $request)
    {      
        $companyType    = Lookup::where('lookup_type',"company-type")->with(['lookupDiscription' => function($query) {
	        $query->where(['language_id' => getAppLocaleId()]);
        }])->get();
        return  View("admin.$this->model.add",compact('companyType'));
    }

    public function Save(Request $request){

       if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $validator                    =   Validator::make(
                $request->all(), 
                array(
                    'name'                          => "required",
                    'email'                         => "nullable|email:rfc,dns",
                    'password'                      => 'required|string|min:4',
                    'confirm_password'              => 'required|same:password',
                    'phone_number'                  => 'required|unique:users,phone_number|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'company_name'                  => 'required',
                    'company_number'                => 'required|regex:'.Config('constants.COMPANY_HP_NUMBER_STRING'),
                    'contact_person_name'           => 'required',
                    'contact_person_phone_number'   => 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'contact_person_email'          => 'required|email:rfc,dns',
                    'company_location'              => 'required',
                    'company_type'                  => 'required',
                    'profile_image'                 => 'required',
                    'contact_person_picture'        => 'required|nullable|mimes:jpg,jpeg,png',
                    'company_logo'                  => 'required|nullable|mimes:jpg,jpeg,png'
                ), 
                array(
                    "password.required"                         => trans("messages.This field is required"),
                    "password.between"                          => trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "password.regex"                            => trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),

                    "name.required"                             => trans("messages.This field is required"),
                    "password.min"                           => trans("messages.password_should_be_minimum_4_characters"),
                    "confirm_password.required"                 => trans("messages.This field is required"),
                    "confirm_password.same"                     => trans("messages.The confirm password must be the same as the password"),
                    "phone_number.required"                     => trans("messages.This field is required"),
                    "phone_number.unique"                       => trans("messages.Mobile number already in use"),
                    "phone_number.regex"                        => trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
                    "company_name.required"                     => trans("messages.This field is required"),
                    "company_number.required"                   => trans("messages.This field is required"),
                    "contact_person_phone_number.required"      => trans("messages.This field is required"),
                    "contact_person_phone_number.regex"         => trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
                    "contact_person_name.required"              => trans("messages.This field is required"),
                    "contact_person_email.required"             => trans("messages.This field is required"),
                    "contact_person_email.email"                => trans("messages.The email must be a valid email address"),
                    "contact_person_email.regex"                => trans("messages.The email must be a valid email address"),
                    "company_location.required"                 => trans("messages.This field is required"),
                    "company_type.required"                     => trans("messages.This field is required"),
                    "email.email"                               => trans("messages.The email must be a valid email address"),
                    "contact_person_picture.required"           => trans("messages.File must be jpg, jpeg, png only"),
                    "contact_person_picture.mimes"              => trans("messages.File must be jpg, jpeg, png only"),
                    "company_logo.required"                     => trans("messages.This field is required"),
                    "company_logo.mimes"                        => trans("messages.File must be jpg, jpeg, png only"),
                    "profile_image.required"                    => trans("messages.This field is required"),
                )
            );
          
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();

            }else{
                $user                               =   new User;
                $user->user_role_id                 =   Config('constants.ROLE_ID.CUSTOMER_ROLE_ID');
                $user->name                         =   $request->input('name');
                $user->email                        =   isset($request->email) ? $request->email : '';
                $user->phone_number                 =   $request->phone_number;
                $user->customer_type                 =   'business';
                $user->password                     =   Hash::make($request->password);
                $user->current_lat                  =   $request->current_lat;
                $user->current_lng                  =   $request->current_lng;
                $user->system_id = 0;

                if ($request->hasFile('profile_image')) {
                    $file = rand() . '.' . $request->profile_image->getClientOriginalExtension();
                    $request->file('profile_image')->move(Config('constants.CUSTOMER_IMAGE_ROOT_PATH'), $file);
                    $user->image = $file;
                }
                $SavedResponse = $user->save();
                $user->system_id  =   1000+$user->id;
                
                $user->save();

                if (!$SavedResponse) {
                    Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                    return Redirect()->back()->withInput();
                } else {
                    $companyObj = new UserCompanyInformation;
                    $companyObj->company_name                       = $request->company_name;
                    $companyObj->company_hp_number                  = $request->company_number;
                    $companyObj->contact_person_name                = $request->contact_person_name;
                    $companyObj->contact_person_email               = $request->contact_person_email;
                    $companyObj->company_location                   = $request->company_location;
                    $companyObj->company_type                       = $request->company_type;
                    $companyObj->user_id                            = $user->id;
                    $companyObj->contact_person_phone_number        = $request->contact_person_phone_number;
                    if ($request->hasFile('contact_person_picture')) {
                        $file = rand() . '.' . $request->contact_person_picture->getClientOriginalExtension();
                        $request->file('contact_person_picture')->move(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH'), $file);
                        $companyObj->contact_person_picture =  $file;
                    }
                    if ($request->hasFile('company_logo')) {
                        $file = rand() . '.' . $request->company_logo->getClientOriginalExtension();
                        $request->file('company_logo')->move(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH'), $file);
                        $companyObj->company_logo = $file;;
                    }

                    $companyObj->save();
                   
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

                    Session()->flash('success', ucfirst(trans("messages.admin_Business_Customer_has_been_added_successfully")));
                    return Redirect()->route($this->model . ".index");
                }
            }
        } 
    }

    public function edit(Request $request,  $enuserid = null){   
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id        = base64_decode($enuserid);
            $userDetails    = User::where('id',$user_id)->with('userCompanyInformation')->first();
            $companyType    = Lookup::where('lookup_type',"company-type")->with(['lookupDiscription' => function($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }])->get();
            return  View("admin.$this->model.edit", compact('userDetails','companyType'));
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
                    'name'                              => "required",
                    'email'                             => "nullable|email:rfc,dns",
                    'phone_number'                      => 'required|unique:users,phone_number,'.$user_id.'|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'company_name'                      => 'required',
                    'company_number'                    => 'required|regex:'.Config('constants.COMPANY_HP_NUMBER_STRING'),
                    'contact_person_phone_number'       => 'required|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'contact_person_name'               => 'required',
                    'contact_person_email'              => 'required|email:rfc,dns',
                    'company_location'                  => 'required',
                    'company_type'                      => 'required',
                    'contact_person_picture'            => 'nullable|mimes:jpg,jpeg,png',
                    'company_logo'                      => 'nullable|mimes:jpg,jpeg,png',
                ), 
                array(
                    "name.required"                     => trans("messages.This field is required"),
                    "phone_number.required"             => trans("messages.This field is required"),
                    "phone_number.unique"               => trans("messages.Mobile number already in use"),
                    "phone_number.regex"                    => trans("messages.phone_number_should_be_9_digits_and_should_be_start_with_0"),
                   
                    "company_name.required"             => trans("messages.This field is required"),
                    "company_number.required"    => trans("messages.This field is required"),
                  
                    "contact_person_phone_number.required"    => trans("messages.This field is required"),
                    "contact_person_phone_number.regex"       => trans("messages.phone_number_should_be_9_digits_and_should_be_start_with_0"),
                   
                    "contact_person_name.required"      => trans("messages.This field is required"),
                    "contact_person_email.required"     => trans("messages.This field is required"),
                    "contact_person_email.email"        => trans("messages.The email must be a valid email address"),
                   
                    "company_location.required"         => trans("messages.This field is required"),
                    "company_type.required"             => trans("messages.This field is required"),

                    "email.email"                       => trans("messages.The email must be a valid email address"),
                    "contact_person_picture.mimes"      => trans("messages.File must be jpg, jpeg, png only"),
                    "company_logo.mimes"                => trans("messages.File must be jpg, jpeg, png only"),
                )
            );
          
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }else{
           
                $user                               =   User::where("id",$user_id)->first();
                $user->name                         =   $request->input('name');
                $user->email                        =   isset($request->email) ? $request->email : '';
                $user->phone_number                 =   $request->phone_number;
                $user->current_lat                  =   $request->current_lat;
                $user->current_lng                  =   $request->current_lng;
               

                if ($request->hasFile('profile_image')) {
                    $file = rand() . '.' . $request->profile_image->getClientOriginalExtension();
                    $request->file('profile_image')->move(Config('constants.CUSTOMER_IMAGE_ROOT_PATH'), $file);
                    $user->image = $file;
                }

                $user->save();
                
                $companyObj = UserCompanyInformation::where('user_id',$user_id)->first();
                $companyObj->company_name                       = $request->company_name;
                $companyObj->company_hp_number                  = $request->company_number;
                $companyObj->contact_person_name                = $request->contact_person_name;
                $companyObj->contact_person_email               = $request->contact_person_email;
                $companyObj->company_location                   = $request->company_location;
                $companyObj->company_type                       = $request->company_type;
                $companyObj->contact_person_phone_number        = $request->contact_person_phone_number;
                if ($request->hasFile('contact_person_picture')) {
                    $file = rand() . '.' . $request->contact_person_picture->getClientOriginalExtension();
                    $request->file('contact_person_picture')->move(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH'), $file);
                    $companyObj->contact_person_picture =  $file;
                }
                if ($request->hasFile('company_logo')) {
                    $file = rand() . '.' . $request->company_logo->getClientOriginalExtension();
                    $request->file('company_logo')->move(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH'), $file);
                    $companyObj->company_logo = $file;;
                }

                $companyObj->save();

                Session()->flash('success', ucfirst(trans("messages.admin_Business_Customer_has_been_updated_successfully")));

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

            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Business_Customer_has_been_removed_successfully")));
        }
        return back();
    }

    public function changeStatus(Request $request, $modelId = 0, $status = 0)
    {//
        if ($status == 1) {
            $statusMessage   =   ucfirst(trans("messages.admin_Business_Customer_has_been_activated_successfully"));
        } else {
            $statusMessage   =   ucfirst(trans("messages.admin_Business_Customer_has_been_deactivated_successfully"));
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
                    'new_password'          => 'required|string|min:4',
                    'confirm_password'  => 'required|same:new_password',
                ),
                array(
                    "new_password.required"     => trans("messages.This field is required"),
                    "new_password.min" 		=> trans("messages.password_should_be_minimum_4_characters"),
                   
                    "new_password.between"      => trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "new_password.regex"        => trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),
                    "confirm_password.required"  => trans("messages.This field is required"),
                   
                    "confirm_password.same"      => trans("messages.The confirm password must be the same as the password"),
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
                return Redirect()->route($this->model . '.index');
              }
            }
        }
        $userDetails = array();
        $userDetails   =  User::find($user_id);
        $data = compact('userDetails');
        return view("admin.$this->model.change_password", $data);
    }

    public function view($enuserid = null){
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
      
        $userDetails    =   User::where('id',$user_id)->with('userCompanyInformation')->first();
        $userDetails->userCompanyInformation->company_type = Lookup::where('id',$userDetails->userCompanyInformation->company_type)->with('lookupDiscription')->first()->lookupDiscription->code;

        $ShipmentLists  =  Shipment::where("customer_id",$user_id)
		->whereNotNull('invoice_file')
        ->with(
            [
                'ShipmentOffers' => function($query) {
				},
				'ShipmentStop' => function($query) {
				},
				'TruckTypeDescriptions' => function($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'RequestTimeDescription' => function($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'companyInformation'

            ]
        )
        ->orderBy('id','desc')
        ->get();

       return  View("admin.$this->model.view", compact('userDetails', 'ShipmentLists'));

    }

    public function sendCredentials(Request $request, $id){
       
        if(empty($id)){
            return redirect()->back();
        }
        $password = rand(1000, 9999);;
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
    // end sendCredentials()




    public function export(Request $request)
	{


        $list[0] = array(
			trans('messages.admin_sys_id'),
            trans('messages.Company Name'),
            trans('messages.admin_phone_number'),
            trans('messages.company_number') . '(H.P.)',
            trans('messages.last_activity_date'),
            trans('messages.admin_number_of_requests'),
            trans('messages.admin_Created_On'),
            trans('messages.admin_common_Status'),
		);

		$customers_export = Session::get('export_data_business_customer');
		

		foreach ($customers_export as $key => $excel_export) {

            $list[] = array(
                $excel_export->system_id,
                $excel_export->company_name,
                $excel_export->phone_number,
                $excel_export->userCompanyInformation ?->company_hp_number,
                $excel_export->last_active_date,
                $excel_export->company_type,
                date(config("Reading.date_format"), strtotime($excel_export->created_at)),
                ($excel_export->is_active==1 ? 'Activated' : 'Deactivated' ),

            );

		}
		

        $collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'Business Customer.xlsx');

	}



}
