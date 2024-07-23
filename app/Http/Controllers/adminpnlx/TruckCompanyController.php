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
use App\Models\LookupDiscription;
use App\Models\TruckCompanySubscription;
use App\Models\User;
use App\Models\UserCompanyInformation;
use App\Models\ShipmentOffer;
use Carbon\Carbon;
use App\Models\Shipment;
use App\Models\EmailAction;
use App\Models\NotificationAction;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateDescription;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\EmailTemplate;
use App\Models\Truck;
use App\Models\TruckType;
use App\Models\PlanFeature;
use App\Models\TruckTypeQuestion;
use App\Models\TruckTypeDescription;
use App\Models\TruckCompanyRequestSubscription;
use App\Models\EmailTemplateDescription;
use App\Models\UserDriverDetail;
use App\Models\ShipmentDriverSchedule;
use Redirect, Session;
use Illuminate\Support\Facades\URL;

use App\Jobs\SendMail;

use App\Jobs\InsertNotification;
use App\Jobs\SendPushNotification;
use App\Jobs\SendGreenApiMessage;
use Illuminate\Support\Facades\Log;


use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Exports\ReportExport;

use App\Exports\UserExport;
use App\Imports\UserImport;

class TruckCompanyController extends Controller
{
    public $model = 'truck-company';
    public $sectionNameSingular = 'truck-company';
    public function __construct(Request $request)
    {
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
        $this->request = $request;
    }

    public function index(Request $request)
    {
       
        $DB = User::query()->with('userCompanyInformation');
        
        $DB->leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
        ->leftjoin('truck_company_subscription_plans', 'truck_company_subscription_plans.truck_company_id', 'users.id')
        ->leftjoin('plans', 'plans.id', 'truck_company_subscription_plans.plan_id')
            ->select(
                'users.*',
                'user_company_informations.company_name',
                'user_company_informations.company_location',
                'user_company_informations.latitude',
                'user_company_informations.longitude',
                'user_company_informations.company_tidaluk',
                'user_company_informations.company_refueling',
                'plans.plan_name',
                'truck_company_subscription_plans.type',
                'truck_company_subscription_plans.total_price',
                'truck_company_subscription_plans.end_time',
                'truck_company_subscription_plans.status',
                DB::Raw("(select COUNT(*) from trucks where trucks.truck_company_id = users.id AND is_deleted = 0 LIMIT 1) as total_trucks"),
                DB::Raw("(select COUNT(*) from shipment_offers where shipment_offers.truck_company_id = users.id AND is_deleted = 0 and `status` IN ('waiting','selected') LIMIT 1) as total_offers"),
                DB::Raw("(select COUNT(*) from shipment_offers where shipment_offers.truck_company_id = users.id AND is_deleted = 0 and `status` = 'approved_from_company' LIMIT 1) as total_shipments"),
                DB::Raw("(select shipment_offers.created_at from shipment_offers where shipment_offers.truck_company_id = users.id ORDER BY shipment_offers.id DESC LIMIT 1) as last_active_date")
            );
          
        $searchVariable = array();
        $inputGet = $request->all();
        if ($request->all()) {
            $searchData = $request->all();
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
                $dateS = date("Y-m-d", strtotime($searchData['date_from']));
                $dateE = date("Y-m-d", strtotime($searchData['date_to']));
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
            if($form_latitude != "" && $form_longitude != ""){
                $DB->where(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( user_company_informations.latitude ) ) * cos( radians(  user_company_informations.longitude  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( user_company_informations.latitude ) ) ))"),"<=",15);
                $DB->orderBy(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( user_company_informations.latitude ) ) * cos( radians(  user_company_informations.longitude  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( user_company_informations.latitude ) ) ))"),"ASC");
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("users.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "phone_number") {
                   
                        $DB->whereHas('userCompanyInformation', function($query) use($fieldValue){
                            $query->where('company_hp_number', 'like', '%' . $fieldValue . '%');
                        });
                        $DB->OrWhere("users.phone_number", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "company_id") {
                        $DB->where("user_company_informations.company_id", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "company_name") {
                        $DB->where("user_company_informations.company_name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "company_hp_number") {
                        $DB->where("user_company_informations.company_hp_number", 'like', '%' . $fieldValue . '%');
                    }
                    if($fieldName == "plan_name"){
                        $DB->where("plans.plan_name", 'like', '%' . $fieldValue . '%');
                    }


                    if ($fieldName == "system_id") {
                        $DB->where("users.system_id", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "company_tidaluk") {
                        $DB->where("user_company_informations.company_tidaluk", $fieldValue);
                    }

                    if ($fieldName == "company_refueling") {
                        $DB->where("user_company_informations.company_refueling", 'like', '%' . $fieldValue . '%');
                    }

                    if($fieldName == 'plan_status'){
                        if ($fieldValue == 'no_purchased') {
                            $DB->whereDoesntHave('planDetails');
                        } else if($fieldValue == 'near_to_expiry_plan'){
							$DB->whereNotNull('truck_company_subscription_plans.end_time')
                               ->where('truck_company_subscription_plans.status', 'activate')
                               ->where("truck_company_subscription_plans.end_time", '<=', now()->addDays(4)->endOfDay()->format('Y-m-d H:i:s'));
                        } else {
                            $DB->whereHas('planDetails', function($query) use($fieldValue){
                                $query->where('status', $fieldValue);
                            });
                        }
                    }
                    
                    if ($fieldName == "status") {
                        if ($fieldValue == 'inactive_over_30_days') {
                            $DB->whereRaw('COALESCE(((select shipment_offers.created_at from shipment_offers where shipment_offers.truck_company_id = users.id ORDER BY shipment_offers.id DESC LIMIT 1)), users.updated_at) < ?', [Carbon::now()->subDays(30)]);
                            $DB->where("users.is_approved", 1);
                            $DB->where("users.is_active", 1);
                        } 
                        else if ($fieldValue == "active") {
                            $DB->where("users.is_approved", 1);
                            $DB->where("users.is_active", 1);

                        } else if ($fieldValue == "deactivate") {
                            $DB->where("users.is_active", 0);
                            $DB->where("users.is_approved", 1);

                        } else if ($fieldValue == "rejected") {
                            $DB->where("users.is_approved", 2);

                        } else if ($fieldValue == "waiting_for_approval") {
                            $DB->where("users.is_approved", '=', 0); 
                        }
                    }
                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where("users.is_deleted", 0);
        $DB->where("users.user_role_id", 3);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'users.created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
      
        $DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_company_track'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);


        $complete_string = $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string = http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount      = $results->count();
        $companyType      = Lookup::where('lookup_type', "company-type")->with('lookupDiscription')->get();
        $refuelingMethods = Lookup::where('lookup_type', "fueling-methods")->with('lookupDiscriptionList')->get();

        $langCode = $this->current_language_id();
        $tidalukCompaniesids = Lookup::query()->where('lookup_type', 'tidaluk-company-type')->pluck('id')->toArray();
        $tidalukCompanies    = LookupDiscription::with('LookupParentId')->whereIn('parent_id', $tidalukCompaniesids)->where('language_id', $langCode)->get();
        $fuelingMethodsids = Lookup::query()->where('lookup_type', 'fueling-methods')->pluck('id')->toArray();
        $fuelingMethods    = LookupDiscription::with('LookupParentId')->whereIn('parent_id', $fuelingMethodsids)->where('language_id', $langCode)->get();

        return View("admin.$this->model.index", compact('resultcount', 'companyType', 'results', 'searchVariable', 'sortBy', 'order', 'query_string', 'refuelingMethods', 'tidalukCompanies', 'fuelingMethods'));
    }


    public function create(Request $request)
    {
        $companyType = Lookup::where('lookup_type', "company-type")->with('lookupDiscription')->get();
        $tidalukCompanyType = Lookup::where('lookup_type', "tidaluk-company-type")->with([
            'lookupDiscription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
        ])->get();
        $fuelingType = Lookup::where('lookup_type', "fueling-methods")->with([
            'lookupDiscription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
        ])->get();
        return View("admin.$this->model.add", compact('companyType', 'tidalukCompanyType', 'fuelingType'));
    }

    public function Save(Request $request)
    {

        if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $validator = Validator::make(
                $request->all(),
                array(
                    'name' => "required",
                    'email' => "nullable|email:rfc,dns",
                    'password' => 'required|string|min:4',
                    'confirm_password' => 'required|same:password',
                    'phone_number' => 'required|unique:users,phone_number|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
                    'company_name' => 'required',
                    'company_number' => 'required|regex:' . Config('constants.COMPANY_HP_NUMBER_STRING'),
                    'company_refulling' => 'required',
                    'company_tidaluk' => 'required',
                    'company_location' => 'required',
                    'contact_person_picture' => 'nullable|mimes:jpg,jpeg,png',
                    'company_logo' => 'nullable|mimes:jpg,jpeg,png'
                ),
                array(
                    "name.required" => trans("messages.This field is required"),
                    "password.required" => trans("messages.This field is required"),
                    "company_refulling.required" => trans("messages.This field is required"),
                    "company_tidaluk.required" => trans("messages.This field is required"),
                    "password.between" => trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "password.min" => trans("messages.password_should_be_minimum_4_characters"),
                    "confirm_password.required" => trans("messages.This field is required"),
                    "confirm_password.same" => trans("messages.The confirm password must be the same as the password"),
                    "phone_number.required" => trans("messages.This field is required"),
                    "phone_number.unique" => trans("messages.Mobile number already in use"),
                    "phone_number.regex" => trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
                    "company_name.required" => trans("messages.This field is required"),
                    "company_number.required" => trans("messages.This field is required"),
                    "contact_person_phone_number.required" => trans("messages.This field is required"),
                    "contact_person_name.required" => trans("messages.This field is required"),
                    "contact_person_email.required" => trans("messages.This field is required"),
                    "contact_person_email.email" => trans("messages.The email must be a valid email address"),
                    "contact_person_email.regex" => trans("messages.The email must be a valid email address"),
                    "company_location.required" => trans("messages.This field is required"),
                    "company_type.required" => trans("messages.This field is required"),
                    "email.email" => trans("messages.The email must be a valid email address"),
                    "contact_person_picture.required" => trans("messages.This field is required"),
                    "contact_person_picture.mimes" => trans("messages.File must be jpg, jpeg, png only"),
                    "company_logo.required" => trans("messages.This field is required"),
                    "company_logo.mimes" => trans("messages.File must be jpg, jpeg, png only"),
                )
            );

            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();

            } else {
                $user = new User;
                $user->user_role_id = Config('constants.ROLE_ID.TRUCK_COMPANY_ID');
                $user->name = $request->input('name');
                $user->email = isset($request->email) ? $request->email : '';
                $user->phone_number = $request->phone_number;
                $user->customer_type = 'business';
                $user->password = Hash::make($request->password);

                if ($request->hasFile('profile_image')) {
                    $file = rand() . '.' . $request->profile_image->getClientOriginalExtension();
                    $request->file('profile_image')->move(Config('constants.CUSTOMER_IMAGE_ROOT_PATH'), $file);
                    $user->image = $file;
                }

                $SavedResponse = $user->save();
                $user->system_id = 0;
                $user->save();

                $system_id = 1000 + $user->id;
                User::where("id", $user->id)->update(array("system_id" => $system_id));

                if (!$SavedResponse) {
                    Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                    return Redirect()->back()->withInput();
                } else {
                    $companyObj = new UserCompanyInformation;
                    $companyObj->company_name = $request->company_name;
                    $companyObj->company_hp_number = $request->company_number;
                    $companyObj->contact_person_name = $request->input('name');
                    $companyObj->contact_person_email = isset($request->email) ? $request->email : '';
                    $companyObj->company_location = $request->company_location;
                    $companyObj->latitude = $request->current_lat;
                    $companyObj->longitude = $request->current_lng;
                    $companyObj->company_type = $request->company_type ?? 0;
                    $companyObj->user_id = $user->id;
                    $companyObj->contact_person_phone_number = $request->phone_number;
                    $companyObj->company_tidaluk = $request->company_tidaluk;
                    $companyObj->company_refueling = $request->company_refulling;
                    $companyObj->company_trms = $request->company_terms;
                    $companyObj->company_description = $request->company_description;
                    $companyObj->contact_person_picture = '';
                    $companyObj->company_logo = '';

                    if ($request->hasFile('contact_person_picture')) {
                        $file = rand() . '.' . $request->contact_person_picture->getClientOriginalExtension();
                        $request->file('contact_person_picture')->move(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH'), $file);
                        $companyObj->contact_person_picture = $file;
                    }
                    if ($request->hasFile('company_logo')) {
                        $file = rand() . '.' . $request->company_logo->getClientOriginalExtension();
                        $request->file('company_logo')->move(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH'), $file);
                        $companyObj->company_logo = $file;
                        ;
                    }
                    $companyObj->save();

                    $PlanDetails                    = Plan::where("id", 1)->where('is_deleted', 0)->where('is_active', 1)->first();
                    if($PlanDetails){
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
                        $TruckCompanySubscription->total_truck       = 0;
                        $TruckCompanySubscription->status            = "activate";
                        $TruckCompanySubscription->start_time        = $startTime;
                        $TruckCompanySubscription->end_time          = $endTime;
            
                        $TruckCompanySubscription->save();
                    }

                    if($request->as_driver != null){
                        $driverDetails                      =   new UserDriverDetail;
                        $driverDetails->user_id             =   $user->id;
                        $driverDetails->driver_picture      =   '';
                        $driverDetails->licence_picture     =   '';
                        $driverDetails->save();
                        $user->truck_company_id             = $user->id;
                        $user->save();
                    }

                    $logData = array(
                        'record_id' => $user->id,
                        'module_name' => 'User',
                        'action_name' => 'create',
                        'action_description' => 'Create User Account',
                        'record_url' => route('users.show', base64_encode($user->id)),
                        'user_agent' => $request->header('User-Agent'),
                        'browser_device' => '',
                        'location' => '',
                        'ip_address' => $request->ip()
                    );
                    $this->genrateAdminLog($logData);


                    Session()->flash('success', ucfirst(trans("messages.admin_Truck_Company_has_been_added_successfully")));

                    if($request->from_page == 'tidaluk_company'){
                        return Redirect()->route($this->model . ".tidaluk-company");
                    }else if($request->from_page == 'fueling_company'){
                        return Redirect()->route($this->model . ".fueling-methods");
                    }
                    else{
                        return Redirect()->route($this->model . ".index");
                    }

                }
            }
        }
    }

    public function edit(Request $request, $enuserid = null)
    {


        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = User::where('id', $user_id)->with('userCompanyInformation')->first();
            $companyType = Lookup::where('lookup_type', "company-type")->with('lookupDiscription')->get();
            $tidalukCompanyType = Lookup::where('lookup_type', "tidaluk-company-type")->with([
                'lookupDiscription' => function ($query) {
                    $query->where(['language_id' => getAppLocaleId()]);
                }
            ])->get();
            $fuelingType = Lookup::where('lookup_type', "fueling-methods")->with([
                'lookupDiscription' => function ($query) {
                    $query->where(['language_id' => getAppLocaleId()]);
                }
            ])->get();

            $ShipmentInvoiceLists                   =   Shipment::
            join('shipment_offers','shipments.id','shipment_offers.shipment_id')
            ->where('shipment_offers.truck_company_id', $user_id)
            ->where('shipment_offers.status', "approved_from_company")
            ->whereNotNull('shipments.invoice_file')
            ->orderBy('shipments.id','desc')
            ->get();

            $userTruckCompanySubscription = TruckCompanySubscription::where('truck_company_id', $user_id)->first();
      
            $total_offer = ShipmentOffer::where('truck_company_id', $user_id)
                ->whereIn('status', ['waiting', 'selected'])
                ->count();
            $total_shipment = ShipmentOffer::where('truck_company_id', $user_id)
                ->whereIn('status', ['approved_from_company'])
                ->count();


            if ($userTruckCompanySubscription) {
                $planDetails = Plan::where('plans.id', $userTruckCompanySubscription->plan_id)->first();
            } else {
                $planDetails = false;
            }

            
        $DB = Truck::query();
        $DB->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
            ->leftjoin('user_company_informations', 'trucks.truck_company_id', "=", 'user_company_informations.user_id')
            ->leftjoin('users', 'trucks.driver_id', "=", 'users.id');

         $DB->select(
        'users.name',
        'user_company_informations.company_name',
        'trucks.id',
        'trucks.truck_company_id',
        'trucks.driver_id',
        'trucks.truck_system_number',
        'trucks.size_of_the_crane',
        'trucks.type_of_truck',
        'trucks.basketman',
        'trucks.truck_licence_number',
        'trucks.truck_licence_expiration_date',
        'trucks.truck_insurance_picture',
        'trucks.truck_insurance_expiration_date',
        'trucks.is_active',
        'trucks.is_deleted',
        'trucks.created_at',
        'trucks.updated_at',
        DB::Raw("(select company_refueling from user_company_informations where trucks.truck_company_id = user_company_informations.user_id LIMIT 1) as company_refueling"),
        DB::Raw("(select name from truck_type_descriptions where language_id = " . getAppLocaleId() . " and parent_id = trucks.type_of_truck) as type_of_truck")
        );
        $DB->where('truck_type_descriptions.language_id', getAppLocaleId());
                $DB->where('trucks.truck_company_id', $user_id);
            $DB->where('trucks.is_deleted', 0);
        $truckDetalsList = $DB->get();


        $DB = User::query();
         $DB->leftjoin('user_driver_details', 'users.id' , 'user_driver_details.user_id')
         ->leftjoin('user_company_informations', 'users.truck_company_id' , 'user_company_informations.user_id')
        ->select('users.*', 'user_company_informations.company_name','user_driver_details.licence_number','user_driver_details.licence_exp_date');

        $DB->where("users.is_deleted", 0);
        $DB->whereIn("users.user_role_id", [3, 4]);
        $TCuserid = base64_decode($enuserid); 

        if($TCuserid){
            $DB->where("users.truck_company_id", $TCuserid);
        }

        $driverDetailsList = $DB->get();


        return View("admin.$this->model.edit", compact('userDetails', 'companyType', 'tidalukCompanyType', 'fuelingType', 'ShipmentInvoiceLists', 'userTruckCompanySubscription', 'truckDetalsList', 'driverDetailsList', 'TCuserid', 'planDetails'));
        } else {
            return redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request, $enuserid = null)
    {
        
        if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $user_id = '';
            $image = "";

            if (!empty($enuserid)) {
                $user_id = base64_decode($enuserid);
            } else {
                return redirect()->route($this->model . ".index");
            }
            $validator = Validator::make(
                $request->all(),
                array(
                    'name' => "required",
                    'email' => "nullable|email:rfc,dns",
                    'phone_number' => 'required|unique:users,phone_number,' . $user_id . '|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
                    'company_name' => 'required',
                    'company_number' => 'required|regex:' . Config('constants.COMPANY_HP_NUMBER_STRING'),
                    'company_location' => 'required',
                    'company_logo' => 'nullable|mimes:jpg,jpeg,png',
                    'company_refulling' => 'required',
                    'company_tidaluk' => 'required',
                ),
                array(
                    "name.required" => trans("messages.This field is required"),
                    "phone_number.required" => trans("messages.This field is required"),
                    "phone_number.unique" => trans("messages.Mobile number already in use"),
                    "phone_number.regex" => trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
                    "company_name.required" => trans("messages.This field is required"),
                    "company_number.required" => trans("messages.This field is required"),
                    "contact_person_phone_number.required" => trans("messages.This field is required"),
                    "company_location.required" => trans("messages.This field is required"),
                    "email.email" => trans("messages.The email must be a valid email address"),
                    "contact_person_picture.mimes" => trans("messages.File must be jpg, jpeg, png only"),
                    "company_logo.mimes" => trans("messages.File must be jpg, jpeg, png only"),
                    "company_refulling.required" => trans("messages.This field is required"),
                    "company_tidaluk.required" => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return Redirect::route($this->model . ".edit", $enuserid)->withErrors($validator)->withInput();
            } else {

                $user = User::where("id", $user_id)->first();
                $user->name = $request->input('name');
                $user->email = isset($request->email) ? $request->email : '';
                $user->phone_number = $request->phone_number;


                if ($request->hasFile('profile_image')) {
                    $file = rand() . '.' . $request->profile_image->getClientOriginalExtension();
                    $request->file('profile_image')->move(Config('constants.CUSTOMER_IMAGE_ROOT_PATH'), $file);
                    $user->image = $file;
                }

                $user->save();

                $companyObj = UserCompanyInformation::where('user_id', $user_id)->first();
                $companyObj->company_name = $request->company_name;
                $companyObj->company_hp_number = $request->company_number;
                $companyObj->contact_person_name = $request->input('name');
                $companyObj->contact_person_email = isset($request->email) ? $request->email : '';
                $companyObj->company_location = $request->company_location;
                $companyObj->latitude = $request->current_lat;
                $companyObj->longitude = $request->current_lng;
                $companyObj->company_type = $request->company_type ?? 0;
                $companyObj->contact_person_phone_number = $request->phone_number;

                $companyObj->company_tidaluk = $request->company_tidaluk;
                $companyObj->company_refueling = $request->company_refulling;
                $companyObj->company_trms = $request->company_terms;
                $companyObj->company_description = $request->company_description;

                if ($request->hasFile('contact_person_picture')) {
                    $file = rand() . '.' . $request->contact_person_picture->getClientOriginalExtension();
                    $request->file('contact_person_picture')->move(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH'), $file);
                    $companyObj->contact_person_picture = $file;
                }
                if ($request->hasFile('company_logo')) {
                    $file = rand() . '.' . $request->company_logo->getClientOriginalExtension();
                    $request->file('company_logo')->move(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH'), $file);
                    $companyObj->company_logo = $file;
                    ;
                }


                $companyObj->save();

                $logData = array(
                    'record_id' => $user->id,
                    'module_name' => 'User',
                    'action_name' => 'edit',
                    'action_description' => 'Edit User Account',
                    'record_url' => route('users.show', base64_encode($user->id)),
                    'user_agent' => $request->header('User-Agent'),
                    'browser_device' => '',
                    'location' => '',
                    'ip_address' => $request->ip()
                );


                $this->genrateAdminLog($logData);
                Session()->flash('success', ucfirst(trans("messages.admin_Truck_Company_has_been_updated_successfully")));

                if($request->as_driver == null){
                    $totalActiveShipment                     =   ShipmentDriverSchedule::where('driver_id',$user->id)
                        ->whereIn('shipment_status',['not_start','not_start'])
                        ->count();
                    if($totalActiveShipment == 0){
                        $user->truck_company_id             =   null;
                        $user->save();
                        $driverDetails                      =   UserDriverDetail::where('user_id',$user->id)->delete();
                    }else{
                        Session()->flash('error', ucfirst(trans("messages.Truck driver cannot be deactivated Please complete the scheduled shipment first")));
                    }   
                }else{
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
                }


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
        $userDetails = User::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            DB::table('oauth_access_tokens')->where("user_id", $user_id)->delete();
            $email = 'delete_' . $user_id . '_' . !empty($userDetails->email);
            $phone_number = 'delete_' . $user_id . '_' . !empty($userDetails->phone_number);
            User::where('id', $user_id)->update(
                array(
                    'is_deleted' => 1,
                    'email' => $email,
                    'phone_number' => null,
                )
            );

            $logData = array(
                'record_id' => $user_id,
                'module_name' => 'User',
                'action_name' => 'delete',
                'action_description' => 'Delete User Account',
                'record_url' => '',
                'user_agent' => $request->header('User-Agent'),
                'browser_device' => '',
                'location' => '',
                'ip_address' => $request->ip()
            );

            $this->genrateAdminLog($logData);

            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Truck_Company_has_been_removed_successfully")));
        }
        return back();
    }

    public function changeStatus(Request $request, $modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("messages.admin_Truck_Company_has_been_activated_successfully");
        } else {
            $statusMessage = trans("messages.admin_Truck_Company_has_been_deactivated_successfully");
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

            $logData = array(
                'record_id' => $user->id,
                'module_name' => 'User',
                'action_name' => $actionType,
                'action_description' => ucfirst($actionType) . ' Account',
                'record_url' => route('users.show', base64_encode($user->id)),
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

    public function approveStatus(Request $request, $modelId = 0, $status = 0)
    {
        $emailActionsString = '';
        if ($status == 1) {
            $statusMessage = trans("messages.admin_truck_company_has_been_approve_successfully");
            $emailActionsString = "approve_user";
        } else if ($status == 2) {
            $statusMessage = trans("messages.admin_truck_company_has_been_rejected_successfully");
            $emailActionsString = "rejected_user";
        }
        $user = User::find($modelId);
        if ($user) {
            if ($status == 1) {
                $actionType = 'approveUser';
            } else if ($status == 2) {
                $actionType = 'rejecttUser';
            }
            $user->is_approved = $status;
            $user->save();


            $settingsEmail = Config::get("Site.from_email");
            $emailActions = EmailAction::where('action', '=', $emailActionsString)->get()->toArray();
            $emailTemplates = EmailTemplate::where('action', '=', $emailActionsString)->get(array('name', 'subject', 'action', 'body', 'mail_enable'))->toArray();
            $cons = explode(',', $emailActions[0]['options']);
            $constants = array();
            foreach ($cons as $key => $val) {
                $constants[] = '{' . $val . '}';
            }
            $subject = $emailTemplates[0]['subject'];
            $rep_Array = array($user->name, $user->email);
            $messageBody = str_replace($constants, $rep_Array, $emailTemplates[0]['body']);


            $requestData = [
                "email" => $user->email,
                "name" => $user->name,
                "subject" => $subject,
                "messageBody" => $messageBody,
            ];

            if($emailTemplates[0]['mail_enable'] == 1){
            SendMail::dispatch($requestData)->onQueue('send_mail');
            }

            $logData = array(
                'record_id' => $user->id,
                'module_name' => 'User',
                'action_name' => $actionType,
                'action_description' => ucfirst($actionType) . ' Account',
                'record_url' => route('users.show', base64_encode($user->id)),
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
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'new_password' => 'required|string|min:4',
                        'confirm_password' => 'required|same:new_password',
                    ),
                    array(
                        "new_password.required" => trans("messages.This field is required"),
                        "new_password.between" => trans("messages.password_should_be_in_between_4_to_8_characters"),
                        "new_password.min" => trans("messages.password_should_be_minimum_4_characters"),
                        "confirm_password.required" => trans("messages.This field is required"),
                        "confirm_password.same" => trans("messages.The confirm password must be the same as the password"),
                    )
                );

                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {

                    $userDetails = User::find($user_id);
                    $userDetails->password = Hash::make($request->new_password);
                    $SavedResponse = $userDetails->save();
                    if (!$SavedResponse) {
                        Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                        return Redirect()->back();
                    }

                    $logData = array(
                        'record_id' => $userDetails->id,
                        'module_name' => 'User',
                        'action_name' => 'changePassword',
                        'action_description' => 'User password changed',
                        'record_url' => route('users.show', base64_encode($userDetails->id)),
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
        $userDetails = User::find($user_id);
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
        $userDetails = User::where('id', $user_id)->with('userCompanyInformation.getCompanyRefuelingDescription')->first();

        $userTruckCompanySubscription = TruckCompanySubscription::where('truck_company_id', $user_id)->first();
      
        $total_offer = ShipmentOffer::where('truck_company_id', $user_id)
            ->whereIn('status', ['waiting', 'selected'])
            ->count();
        $total_shipment = ShipmentOffer::where('truck_company_id', $user_id)
            ->whereIn('status', ['approved_from_company'])
            ->count();


        if ($userTruckCompanySubscription) {
            $planDetails = Plan::where('plans.id', $userTruckCompanySubscription->plan_id)->first();
        } else {
            $planDetails = false;
        }

		$ShipmentInvoiceLists					=	Shipment::
        join('shipment_offers','shipments.id','shipment_offers.shipment_id')
        ->where('shipment_offers.truck_company_id', $user_id)
        ->where('shipment_offers.status', "approved_from_company")
        ->whereNotNull('shipments.invoice_file')
        ->orderBy('shipments.id','desc')
        ->get();


     $userTruckCompanySubscription = TruckCompanySubscription::where('truck_company_id', $user_id)->first();
      
            $total_offer = ShipmentOffer::where('truck_company_id', $user_id)
                ->whereIn('status', ['waiting', 'selected'])
                ->count();
            $total_shipment = ShipmentOffer::where('truck_company_id', $user_id)
                ->whereIn('status', ['approved_from_company'])
                ->count();


            if ($userTruckCompanySubscription) {
                $planDetails = Plan::where('plans.id', $userTruckCompanySubscription->plan_id)->first();
            } else {
                $planDetails = false;
            }


            
        $DB = Truck::query();
        $DB->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
            ->leftjoin('user_company_informations', 'trucks.truck_company_id', "=", 'user_company_informations.user_id')
            ->leftjoin('users', 'trucks.driver_id', "=", 'users.id');

         $DB->select(
        'users.name',
        'user_company_informations.company_name',
        'trucks.id',
        'trucks.truck_company_id',
        'trucks.driver_id',
        'trucks.truck_system_number',
        'trucks.size_of_the_crane',
        'trucks.type_of_truck',
        'trucks.basketman',
        'trucks.truck_licence_number',
        'trucks.truck_licence_expiration_date',
        'trucks.truck_insurance_picture',
        'trucks.truck_insurance_expiration_date',
        'trucks.is_active',
        'trucks.is_deleted',
        'trucks.created_at',
        'trucks.updated_at',
        DB::Raw("(select company_refueling from user_company_informations where trucks.truck_company_id = user_company_informations.user_id LIMIT 1) as company_refueling"),
        DB::Raw("(select name from truck_type_descriptions where language_id = " . getAppLocaleId() . " and parent_id = trucks.type_of_truck) as type_of_truck")
        );
        $DB->where('truck_type_descriptions.language_id', getAppLocaleId());
                $DB->where('trucks.truck_company_id', $user_id);
            $DB->where('trucks.is_deleted', 0);
        $truckDetalsList = $DB->get();


        $DB = User::query();
         $DB->leftjoin('user_driver_details', 'users.id' , 'user_driver_details.user_id')
         ->leftjoin('user_company_informations', 'users.truck_company_id' , 'user_company_informations.user_id')
        ->select('users.*', 'user_company_informations.company_name','user_driver_details.licence_number','user_driver_details.licence_exp_date');

        $DB->where("users.is_deleted", 0);
        $DB->whereIn("users.user_role_id", [3,4]);
        $TCuserid = base64_decode($enuserid); 
        
        if($TCuserid){
            $DB->where("users.truck_company_id", $TCuserid);

        }

        $driverDetailsList = $DB->get();

        
        
        return View("admin.$this->model.view", compact('userDetails', 'userTruckCompanySubscription', 'planDetails', 'total_offer', 'total_shipment', 'ShipmentInvoiceLists', 'truckDetalsList', 'driverDetailsList', 'TCuserid'));

    }

    public function sendCredentials(Request $request, $id)
    {
        if (empty($id)) {
            return redirect()->back();
        }
        $password = rand(1000, 9999);
        ;
        $user = User::find($id);
        $settingsEmail = Config::get("Site.from_email");
        $full_name = $user->name;
        $email = $user->email;
        $user->password = Hash::make($password);
        $user->save();
        $emailActions = EmailAction::where('action', '=', 'send_login_credentials')->get()->toArray();
        $emailTemplates = EmailTemplate::where('action', '=', 'send_login_credentials')->get(array('name', 'subject', 'action', 'body', 'mail_enable'))->toArray();
        $cons = explode(',', $emailActions[0]['options']);
        $constants = array();
        foreach ($cons as $key => $val) {
            $constants[] = '{' . $val . '}';
        }
        $subject = $emailTemplates[0]['subject'];
        $route_url = Config('constants.WEBSITE_ADMIN_URL') . '/login';
        $rep_Array = array($full_name, $email, $password, $user->phone_number);
        $messageBody = str_replace($constants, $rep_Array, $emailTemplates[0]['body']);


        $requestData = [
            "email" => $user->email,
            "name" => $user->name,
            "subject" => $subject,
            "messageBody" => $messageBody,
        ];

        if($emailTemplates[0]['mail_enable'] == 1){
        SendMail::dispatch($requestData)->onQueue('send_mail');
        }

        Session()->flash('flash_notice', ucfirst(trans("messages.admin_Login_credentials_send_successfully")));

        $logData = array(
            'record_id' => $user->id,
            'module_name' => 'User',
            'action_name' => 'sendCredentials',
            'action_description' => 'Send new credentials',
            'record_url' => route('users.show', base64_encode($user->id)),
            'user_agent' => $request->header('User-Agent'),
            'browser_device' => '',
            'location' => '',
            'ip_address' => $request->ip()
        );

        $this->genrateAdminLog($logData);

        return redirect()->back();
    }

    public function import(Request $request)
    {
        return View("admin.$this->model.import");
    }



    public function export(Request $request, $id)
    {
        if ($id == 'company-type') {
            $data = Lookup::where('lookup_type', "company-type")->pluck('code', 'id')->toArray();

        } else if ($id == 'sample') {
            $data = [];
        }
        return Excel::download(new UserExport($data), $id . '.xlsx');

    }


    public function importList(Request $request)
    {
        $formData = $request->all();
        $validator                    =   Validator::make(
            $request->all(),
            array(
                'file' => 'required|mimes:xls,xlsx,csv', 
            ),
            array(
                'file.required' => 'The file must be required',
                'file.mimes' => 'The file must be a valid Excel (XLS, XLSX) or CSV file',
            )
        );
       
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'validator')->withInput();
        }
        $errors = [];
        $validated_data = [];
        $data = Excel::toArray(new UserImport, $request->file->store('temp'));
        Validator::extend('chack_company_id', function ($attribute, $value, $parameters, $validator) {

            $getinfo = DB::table("user_company_informations")->where("company_id", $value)->first();

            if (empty($getinfo)) {
                $validator->addReplacer(
                    'chack_company_id',
                    function ($message, $attribute, $rule, $parameters) use ($value) {
                        if (empty($value)) {
                            return str_replace('validation.chack_company_id', " The comapany field is required.", $message);
                        } else {
                            return str_replace('validation.chack_company_id', "incorrect comapany", $message);
                        }
                    }
                );

                return false;
            } else {
                return true;
            }
        });

        Validator::extend('valid_company_type', function ($attribute, $value, $parameters, $validator) {
            $getinfo = Lookup::where('lookup_type', 'company-type')
                ->where('is_active', 1)
                ->leftJoin('lookup_discriptions', 'lookup_discriptions.parent_id', 'lookups.id')
                ->where('lookup_discriptions.language_id', $this->current_language_id())
                ->where('lookup_discriptions.code', $value)
                ->first();
        
            if (empty($getinfo)) {
                return false;
            }
        
            return true;
        });
        
        Validator::replacer('valid_company_type', function ($message, $attribute, $rule, $parameters, $validator) {
            return str_replace('validation.valid_company_type', "This company type field is invalid.", $message);
        });
        

        foreach ($data as $info) {
            foreach ($info as $key => $value) {
                $validator = Validator::make($value, [
                    'name' => 'required',
                    'email' => 'required|email:rfc,dns|unique:users',
                    'phone_number' => 'required',
                    'company_name' => 'required',
                    'contact_person_name' => 'required',
                    'contact_person_email' => 'required',
                    'company_location' => 'required',
                    'company_type' => 'required|valid_company_type',
                    'contact_person_phone_number' => 'required',
                ]);

                $validated_data[] = $value;
                if ($validator->fails()) {
                    $errors[$key] = $validator->messages();
                }
            }
        }

        return view('admin.' . $this->model . '.import-data', [
            'errors' => $errors,
            'import_data' => $validated_data,

        ]);

    }



    public function importListdata(Request $request)
    {
        $request->validate([
            'keys' => 'required',
            'values' => 'required'
        ]);
        $data = [];
        $childs = [];
        $company_type = Lookup::select('code')->where('lookup_type', 'company-type')->get();
        $CompanyType = array();
        if (!empty($company_type)) {
            foreach ($company_type as $key => $value) {
                $CompanyType[$value->code] = $key + 1;
            }
        }

        if (count($request->keys) == count($request->values)) {
            foreach ($request->keys as $key => $value) {
                foreach ($value as $k => $v) {
                    if (in_array($v, ['name', 'email', 'phone_number', 'company_name', 'company_mobile_number', 'contact_person_name', 'contact_person_email', 'company_location', 'company_type', 'contact_person_phone_number', 'company_id'])) {

                        if ($v == 'name') {
                            $data[$key][$v] = $request->values[$key][$k];
                        }

                        if ($v == 'email') {
                            $data[$key][$v] = $request->values[$key][$k];
                        }

                        if ($v == 'phone_number') {
                            $data[$key][$v] = $request->values[$key][$k];
                        }

                        if ($v == 'company_name') {
                            $data[$key][$v] = $request->values[$key][$k];
                        }


                        if ($v == 'company_mobile_number') {
                            $data[$key][$v] = $request->values[$key][$k];
                        }

                        if ($v == 'contact_person_email') {
                            $data[$key][$v] = $request->values[$key][$k];
                        }

                        if ($v == 'company_location') {
                            $data[$key][$v] = $request->values[$key][$k];
                        }
                        if ($v == 'contact_person_phone_number') {
                            $data[$key][$v] = $request->values[$key][$k];
                        }
                        if ($v == 'company_type') {
                            $data[$key][$v] = $request->values[$key][$k];
                        }

                    }
                }
            }
        }

        foreach ($data as $key => $info) {
            $User = new User;
            $User->name = $info['name'];
            $User->email = $info['email'];
            $User->phone_number = isset($info['phone_number']) ? $info['phone_number'] : '';
            $User->user_role_id = 3;
            $User->customer_type = 'business';
            $User->save();

            $userInformation = new UserCompanyInformation;

            $userInformation->user_id = $User->id;
            $userInformation->company_name = isset($info['company_name']) ? $info['company_name'] : '';
            $userInformation->company_mobile_number = isset($info['company_mobile_number']) ? $info['company_mobile_number'] : '';
            $userInformation->contact_person_name = isset($info['contact_person_name']) ? $info['contact_person_name'] : '';
            $userInformation->contact_person_email = isset($info['contact_person_email']) ? $info['contact_person_email'] : '';
            $userInformation->company_location = isset($info['company_location']) ? $info['company_location'] : '';
            $userInformation->contact_person_phone_number = isset($info['contact_person_phone_number']) ? $info['contact_person_phone_number'] : '';
            $userInformation->company_type = isset($info['company_type']) ? $CompanyType[$info['company_type']] : 0;
            $userInformation->contact_person_picture = '';
            $userInformation->company_logo = '';
            $userInformation->save();

        }
        Session::flash('flash_notice', ucfirst(trans('messages.admin_Data_imported_successfully')));
        return Redirect::route($this->model . '.index');

    }

    public function truckDetails(Request $request, $entruckid)
    {   
                    $truckId = base64_decode($entruckid);
                $DB = Truck::query();
        $DB->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
            ->leftjoin('user_company_informations', 'trucks.truck_company_id', "=", 'user_company_informations.user_id')
            ->leftjoin('users', 'trucks.driver_id', "=", 'users.id');

        $searchVariable = array();
        $inputGet = $request->all();
        if ($request->all()) {
            $searchData = $request->all();
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
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "truck_type") {
                        $DB->where("trucks.type_of_truck", $fieldValue);
                    }
                    if ($fieldName == "truck_system_number") {
                        $DB->where("trucks.truck_system_number", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "company_tidaluk") {
                        $DB->where("company_tidaluk", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "company_refueling") {
                        $DB->where("company_refueling", 'like', '%' . $fieldValue . '%');
                    }

                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->select(
            'users.name',
            'user_company_informations.company_name',
            'trucks.id',
            'trucks.truck_company_id',
            'trucks.driver_id',
            'trucks.truck_system_number',
            'trucks.size_of_the_crane',
            'trucks.type_of_truck',
            'trucks.basketman',
            'trucks.truck_licence_number',
            'trucks.truck_licence_expiration_date',
            'trucks.truck_insurance_picture',
            'trucks.truck_insurance_expiration_date',
            'trucks.is_active',
            'trucks.is_deleted',
            'trucks.created_at',
            'trucks.updated_at',
            DB::Raw("(select company_refueling from user_company_informations where trucks.truck_company_id = user_company_informations.user_id LIMIT 1) as company_refueling"),
            DB::Raw("(select name from truck_type_descriptions where language_id = " . getAppLocaleId() . " and parent_id = trucks.type_of_truck) as type_of_truck")
        );
        $DB->where('truck_type_descriptions.language_id', getAppLocaleId());
                    $DB->where('trucks.truck_company_id', $truckId);
                $DB->where('trucks.is_deleted', 0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'trucks.created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string = $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string = http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
        $truckType = TruckType::where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();
        return View("admin.$this->model.index_truck", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string', 'entruckid', 'truckType'));
    }

    public function truckDetailsCreate($entruckid = null)
    {
        
        $TCuserid = base64_decode($entruckid);
        $truck_id = base64_decode($entruckid);
        $language_id    =   getAppLocaleId();
        $truckType = TruckType::where(
            [
                'map_truck_type_id'=> 0,
                'is_active'=> 1,
                'is_deleted'=> 0
            ]
        )
        ->select(
            DB::raw("(select name from truck_type_descriptions where parent_id=truck_types.id and language_id=$language_id) as name"),
            "id"
        )
        ->with([
            'TruckTypeQuestionsList'=>function ($query) {
                $query->where('is_active', 1)->where('is_deleted', 0);
            },
            'TruckTypeQuestionsList.TruckTypeQuestionDiscription'=>function ($query) {
                $query->where('language_id', getAppLocaleId());
            }
        ])
        ->get();
        $free_driver = User::where("users.truck_company_id",$truck_id)
        ->select("users.id","users.name", "users.user_role_id")
        ->leftjoin('trucks','trucks.driver_id','users.id')
        ->whereNull("trucks.id")
        ->where("users.user_role_id",4)
        ->where("users.is_active",1)
        ->where("users.is_deleted",0)
        ->get();

        $truckCompanies = User::where(['users.is_active' => 1, 'users.is_deleted' => 0, 'users.user_role_id' => 3])
                        ->leftjoin('user_company_informations', 'user_company_informations.user_id', 'users.id')
                        ->select(
                            'user_company_informations.user_id as company_id',
                            'user_company_informations.company_name as company_name',
                        )
                        ->get();
       
        
        return View("admin.$this->model.add_truck", compact('entruckid', 'truckType','free_driver', 'TCuserid', 'truckCompanies'));

    }

    public function truckDetailsSave(Request $request, $entruckid = null)
    {
        $from_page      = $request->from_page ?? '';
        $tc_id          = base64_encode($request->tc_id) ?? '';
        $user           = null;
        if($entruckid != null ){
            $truckId    = base64_decode($entruckid);
            $user       = User::find($truckId);
        }else{
            if($request->truck_company != null ){
                $user   = User::find($request->truck_company);
            }
        }
        $thisData = $request->all();
        $truckRegistrationData = UserCompanyInformation::where("user_id",$user->id)->first();

        $image = $request->file('image');
        
        if ($image != null) {
            $imagename = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(Config('constants.TRUCK_IMAGE_ROOT_PATH'), $imagename);
        }
        
        $file1 = $request->file('truck_insurance_picture');
        $filename1 = null;
        if ($file1) {
            $filename1 = rand() . '.' . $file1->getClientOriginalExtension();
            $file1->move(Config('constants.TRUCK_INSURANCE_IMAGE_ROOT_PATH'), $filename1);
        }
    
        $file2 = $request->file('truck_licence_number');
        $filename2 = null;
        if ($file2) {
            $filename2 = rand() . '.' . $file2->getClientOriginalExtension();
            $file2->move(Config('constants.TRUCK_LICENCE_NUMBER_IMAGE_ROOT_PATH'), $filename2);
        }



        $getActivePlan = TruckCompanySubscription::where('truck_company_id', $user->id)->where('status', 'activate')->first();
        
        $getTotalTrucksCount = Truck::where('truck_company_id', $user->id)->count();
        

        if($getActivePlan == null && $getTotalTrucksCount == 5){
            Session()->flash('error', ucfirst(trans('messages.company_does_not_have_active_plan_to_add_more_then_five_trucks')));
            return Redirect()->back()->withInput();
        }

        $column_type = $getActivePlan->column_type ?? 0;

        $getTruckCounts = Truck::where('truck_company_id', $user->id)->count();
        
        if($column_type == 0 && $getTruckCounts == 5){

            Session()->flash('error', ucfirst(trans('messages.truck_company_can_not_add_more_then_five_trucks_as_per_its_plan_type')));
            return Redirect()->back()->withInput();
            
        }else{

            DB::table('trucks')->insert(
                array(
                    'truck_company_id' 					=> $user->id,
                    'truck_system_number' 				=> $request->truck_system_number,
                    'basketman' 						=> NULL,
                    'type_of_truck' 					=> $request->truck_type,
                    'driver_id' 						=> $request->driver_id ?? 0,
                    'truck_licence_expiration_date' 	=> ($request->truck_licence_expiration_date ? Carbon::createFromFormat('d/m/y', ($request->truck_licence_expiration_date))->format('Y-m-d') : null),
                    'truck_insurance_expiration_date' 	=> ($request->truck_insurance_expiration_date ? Carbon::createFromFormat('d/m/y', ($request->truck_insurance_expiration_date))->format('Y-m-d') : null),
                    'questionnaire'						=> json_encode($request->ans[1][$request->truck_type] ?? []) ,
                    'company_refueling' 				=> $truckRegistrationData ? $truckRegistrationData->company_refueling : '',
                    'company_tidaluk' 					=> $truckRegistrationData ? $truckRegistrationData->company_tidaluk : '',
                    'truck_licence_number' 				=> $filename2,
                    'truck_insurance_picture' 			=> $filename1,
                    'image' 				            => (isset($imagename) ? $imagename : ''),
                    'is_active' 						=> 1,
                    'is_deleted' 						=> 0,
                )
            );
        
        }
        
        Session()->flash('success', ucfirst(trans("messages.truck_has_been_added_successfully")));

       

        if($from_page == "tc_edit"){
            return redirect()->route("truck-company.edit", array(
                 $tc_id,
                 'from_page' => 'tc_edit',
                 'tabs' => 'truck_detail'
             ));
         }else if($entruckid != null){
             return Redirect()->route($this->model . ".index_truck", array($entruckid));
         }else{
            return Redirect()->route("truck-company.all-truck-list");
        }
        }

    public function edit_truck(Request $request, $entruckid = null)
    {

        $user_id = '';
        if (!empty($entruckid)) {
            $truck_id = base64_decode($entruckid);
            $truckDetails = Truck::where('id', $truck_id)->first();
            $language_id    =   getAppLocaleId();
            $truckType = TruckType::where(
                [
                    'map_truck_type_id'=> 0,
                    'is_active'=> 1,
                    'is_deleted'=> 0
                ]
            )
            ->select(
                DB::raw("(select name from truck_type_descriptions where parent_id=truck_types.id and language_id=$language_id) as name"),
                "id"
            )
            ->with([
                'TruckTypeQuestionsList'=>function ($query) {
                    $query->where('is_active', 1)->where('is_deleted', 0);
                },
                'TruckTypeQuestionsList.TruckTypeQuestionDiscription'=>function ($query) {
                    $query->where('language_id', getAppLocaleId());
                }
            ])
            ->get();

            $free_driver = User::where("users.truck_company_id",$truckDetails->truck_company_id)
        ->select("users.id","users.name", "users.user_role_id")
        ->leftjoin('trucks','trucks.driver_id','users.id')
        ->whereRaw("trucks.driver_id IS NULL or trucks.driver_id = ".$truckDetails->driver_id)
        ->where("users.user_role_id",4)
        ->where("users.is_active",1)
        ->where("users.is_deleted",0)
        ->get();
        
        
        return View("admin.$this->model.edit_truck", compact('truckDetails', 'truckType', 'entruckid','free_driver'));
        } else {
            return redirect()->route($this->model . ".index");
        }
    }

    public function update_truck(Request $request, $entruckid)
    {
        $truckId = base64_decode($entruckid);
        $thisData = $request->all();
        $obj = Truck::find($truckId);
        $obj->type_of_truck = $request->truck_type;
        $obj->truck_system_number                   = $request->truck_system_number;
        $obj->questionnaire                         = json_encode($request->ans[1][$request->truck_type] ?? []);
        $obj->truck_licence_expiration_date         = $request->truck_licence_expiration_date ? Carbon::createFromFormat('d/m/y', ($request->truck_licence_expiration_date))->format('Y-m-d') : null;
        $obj->truck_insurance_expiration_date       = $request->truck_insurance_expiration_date ? Carbon::createFromFormat('d/m/y', ($request->truck_insurance_expiration_date))->format('Y-m-d') : null;

        $image = $request->file('image');
        $file1 = $request->file('truck_insurance_picture');
        $file2 = $request->file('truck_licence_number');

        if ($image) {
            $imagename = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(Config('constants.TRUCK_IMAGE_ROOT_PATH'), $imagename);
            $obj->image = $imagename;
        }

        if ($file1) {
            $filename1 = rand() . '.' . $file1->getClientOriginalExtension();
            $file1->move(Config('constants.TRUCK_INSURANCE_IMAGE_ROOT_PATH'), $filename1);
            $obj->truck_insurance_picture = $filename1;
        }

        if ($file2) {
            $filename2 = rand() . rand() . '.' . $file2->getClientOriginalExtension();
            $file2->move(Config('constants.TRUCK_LICENCE_NUMBER_IMAGE_ROOT_PATH'), $filename2);
            $obj->truck_licence_number = $filename2;
        }
        $obj->send_insurance_notification_before_30_days = 0;
        $obj->send_licence_notification_before_30_days   = 0;
        $obj->save();

        $from_page = $request->from_page ?? '';
        Session()->flash('success', ucfirst(trans("messages.admin_Truck_has_been_updated_successfully")));
        if($from_page == "tc_edit"){
           return redirect()->route($this->model . ".edit", array(
                base64_encode($obj->truck_company_id),
                'from_page' => 'tc_edit',
                'tabs' => 'truck_detail'
            ));
        }else if($from_page == "tc_view"){
           return redirect()->route($this->model . ".show", array(
                base64_encode($obj->truck_company_id),
                'from_page' => 'tc_view',
                'tabs' => 'truck_detail'
            ));
        }else{
              return Redirect()->route("truck-company.all-truck-list");
        }

    }

    public function changeStatus_truck(Request $request, $modelId = 0, $status = 0)
    {
        
        $user = Truck::find($modelId);
        if ($user) {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $user->is_active = $NewStatus;
            if ($NewStatus == 1) {
                $statusMessage = ucfirst(trans("messages.admin_Truck_has_been_activated_successfully"));
            } else {
                $statusMessage = ucfirst(trans("messages.admin_Truck_has_been_deactivated_successfully"));
            }
            $ResponseStatus = $user->save();

        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

    public function destroy_truck(Request $request, $enuserid)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = User::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            Truck::where('id', $user_id)->update(
                array(
                    'driver_id'  => 0,
                    'is_deleted' => 1,
                )
            );

            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Truck_has_been_removed_successfully")));
        }
        return back();
    }

    public function view_truck($enuserid = null)
    {
        $i = 1;
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }

        $truckDetails = Truck::with([
            'typeOfTruck' => function ($query) {
            },
            'typeOfTruck.TruckTypeQuestionsList',
            'truckDriver'
        ])
            ->where('id', $user_id)->first();



        $userTruckCompanySubscription = UserCompanyInformation::where('user_id', $truckDetails->truck_company_id)->first();
        $truckDetails->company_tidaluk = LookupDiscription::where(['parent_id' => $userTruckCompanySubscription->company_tidaluk, 'language_id' => getAppLocaleId()])->first()->code ?? "";

        $truckDetails->company_refueling = LookupDiscription::where(['parent_id' => $userTruckCompanySubscription->company_refueling, 'language_id' => getAppLocaleId()])->first()->code ?? "";

        return View("admin.$this->model.view_truck", compact('truckDetails', 'i'));
    }

    public function allTruckList(Request $request)
    {   
        $DB = Truck::query();
        $DB->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
            ->leftjoin('user_company_informations', 'trucks.truck_company_id', "=", 'user_company_informations.user_id')
            ->leftjoin('users', 'trucks.driver_id', "=", 'users.id');
        $searchVariable = array();
        $inputGet = $request->all();
        if ($request->all()) {
            $searchData = $request->all();
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
				$DB->whereBetween('trucks.truck_insurance_expiration_date', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
			} elseif (!empty($searchData['date_from'])) {
				$dateS = $searchData['date_from'];
				$DB->where('trucks.truck_insurance_expiration_date', '>=', [$dateS . " 00:00:00"]);
			} elseif (!empty($searchData['date_to'])) {
				$dateE = $searchData['date_to'];
				$DB->where('trucks.truck_insurance_expiration_date', '<=', [$dateE . " 00:00:00"]);
			}

            $form_latitude = $request->current_lat;
            $form_longitude = $request->current_lng;
            if($request->city_name && $form_latitude != "" && $form_longitude != ""){
                $DB->where(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( users.current_lat ) ) * cos( radians(  users.current_lng  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( users.current_lat ) ) ))"),"<=",15);
                $DB->orderBy(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( users.current_lat ) ) * cos( radians(  users.current_lng  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( users.current_lat ) ) ))"),"ASC");
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "truck_type") {
                        $DB->where("trucks.type_of_truck", $fieldValue);
                    }
                    if ($fieldName == "truck_system_number") {
                        $DB->where("trucks.truck_system_number", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "company_tidaluk") {
                        $DB->where("user_company_informations.company_tidaluk", $fieldValue);
                    }

                    if ($fieldName == "company_refueling") {
                        $DB->where("user_company_informations.company_refueling", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("trucks.is_active", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->select(
            'users.name',
            'users.current_lat',
            'users.current_lng',
            'user_company_informations.company_name',
            'trucks.id',
            'trucks.truck_company_id',
            'trucks.driver_id',
            'trucks.truck_system_number',
            'trucks.size_of_the_crane',
            'trucks.type_of_truck',
            'trucks.basketman',
            'trucks.truck_licence_number',
            'trucks.truck_licence_expiration_date',
            'trucks.truck_insurance_picture',
            'trucks.truck_insurance_expiration_date',
            'trucks.is_active',
            'trucks.is_deleted',
            'user_company_informations.company_tidaluk',
            'users.name as truck_drivers',
            'trucks.created_at',
            'trucks.updated_at',
            DB::Raw("(select company_refueling from user_company_informations where trucks.truck_company_id = user_company_informations.user_id LIMIT 1) as company_refueling"),
            DB::Raw("(select name from truck_type_descriptions where language_id = " . getAppLocaleId() . " and parent_id = trucks.type_of_truck) as type_of_truck")
        );
        $DB->where('truck_type_descriptions.language_id', getAppLocaleId());
                $DB->where('trucks.is_deleted', 0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'trucks.created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string = $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string = http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
       
        $langCode = $this->current_language_id();

        $truckType = TruckType::join('truck_type_descriptions', 'truck_types.id', '=', 'truck_type_descriptions.parent_id')
       ->where('truck_type_descriptions.language_id', $langCode)
       ->pluck('truck_types.name', 'truck_types.id');
       

        $tidalukCompaniesids = Lookup::query()->where('lookup_type', 'tidaluk-company-type')->pluck('id')->toArray();
        $tidalukCompanies    = LookupDiscription::with('LookupParentId')->whereIn('parent_id', $tidalukCompaniesids)->where('language_id', $langCode)->get();
        $fuelingMethodsids = Lookup::query()->where('lookup_type', 'fueling-methods')->pluck('id')->toArray();
        $fuelingMethods    = LookupDiscription::with('LookupParentId')->whereIn('parent_id', $fuelingMethodsids)->where('language_id', $langCode)->get();


        return View("admin.$this->model.all_truck_list", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string', 'truckType', 'tidalukCompanies', 'fuelingMethods'));
    }



    public function tcexport(Request $request)
	{

        
        $list[0] = array(
			trans('messages.admin_sys_id'),
            trans('messages.Company Name'),
            trans('messages.company_number'),
            trans('messages.offers'),
            trans('messages.shipment'),
            trans('messages.admin_plan_name'),
            trans('messages.admin_common_plan_price'),
            trans('messages.admin_plan_expiry_date'),
            trans('messages.admin_plan_status'),
            trans('messages.admin_common_Last_Activity_Date'),
            trans('messages.admin_Created_On'),
            trans('messages.admin_common_Status'),
		);

		$customers_export = Session::get('export_data_company_track');
		
		foreach ($customers_export as $key => $excel_export) {
            

            $typeData = '';

            if ($excel_export ?->type == '0') {
            $typeData = trans('messages.monthly');
            } elseif ($excel_export ?->type == '1') {
                $typeData = trans('messages.quarterly');
            } elseif ($excel_export ?->type == '2') {
                $typeData = trans('messages.Half Yearly');
            } elseif ($excel_export ?->type == '3') {
                $typeData = trans('messages.Yearly');
            }
       
            $endTime = $excel_export ? $excel_export->end_time : '';

            $list[] = array(
                $excel_export->system_id,
                $excel_export->company_name,
                $excel_export->phone_number,
                $excel_export->total_offers,
                $excel_export->total_shipments,
                $typeData,
                $excel_export ?->total_price,
                $endTime,
                $excel_export ?->status,
                $excel_export->last_active_date,
                date(config("Reading.date_format"), strtotime($excel_export->created_at)),
                ($excel_export->is_active==1 ? trans('messages.admin_activate_status') : trans('messages.admin_deactivate_status')),
            );
        }

        $collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'Truck Company.xlsx');
			

	}


    public function subscriptionPlan(Request $request, $enuserid)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = User::find($user_id);
        $plans = array();
        $plans[0] = Plan::where('is_active', 1)->where('is_deleted', 0)->where('column_type', 0)->get();
        $plans[1] = Plan::where('is_active', 1)->where('is_deleted', 0)->where('column_type', 1)->get();
		foreach ($plans as &$values) {
            foreach ($values as &$plan) {
                if($plan->type =='0')
                    $plan->name = trans('messages.monthly');
                elseif($plan->type =='1') 
                    $plan->name = trans('messages.quarterly');
                elseif($plan->type =='2')
                    $plan->name = trans('messages.Half Yearly');
                else
                    $plan->name = trans('messages.Yearly');
            }
		}

        $subscribePlan = TruckCompanySubscription::with('planDetail')->where('truck_company_id', $user_id)->orderBy('id', 'desc')->first();
        if(empty($subscribePlan)){
            $subscribePlan = TruckCompanyRequestSubscription::with('planDetail')->where('truck_company_id', $user_id)->orderBy('id', 'desc')->first();
        }

        $planDetails = Plan::where('is_active', 1)->where('is_deleted', 0)->get();



        return View("admin.$this->model.subscription_plan",compact('userDetails','plans', 'subscribePlan', 'planDetails'));
    }

    public function subscribePlanSave(Request $request, $id){
       
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $randomString = '';

        for ($i = 0; $i < strlen($characters); $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        $getId = '';
        if($id){
            $getId = base64_decode($id);
        }
        if($request->discountCheck == 'on'){
            $validated = $request->validate([
                'column_type'      => 'required',
                'plan_name'        => 'required',
                'price'            => 'required',
                'discount_price'   => 'required|numeric|max:100', 
            ],
            [
                'column_type.required'             => trans("messages.This field is required"),
                'plan_name.required'           => trans("messages.This field is required"),
                'price.required'                   => trans("messages.This field is required"),
                'discount_price.required'          => trans("messages.This field is required"),
                'discount_price.max'               => trans("messages.discount_must_not_be_greater"),
            ]);
        }else{
            $validated = $request->validate([
                'column_type'      => 'required',
                'plan_name'        => 'required',
                'price'            => 'required',
            ],[
                'column_type.required'      => trans("messages.This field is required"),
                'plan_name.required'        => trans("messages.This field is required"),
                'price.required'            => trans("messages.This field is required"),
            ]);
        }
        TruckCompanyRequestSubscription::where('truck_company_id', $getId)->delete();
        $planDetail = Plan::find($request->plan_name);

        $obj                     = new TruckCompanyRequestSubscription;
        $obj->truck_company_id   = $getId;
        $obj->plan_id            = $request->plan_name;
        $obj->price              = ($request->is_free ? '0' : $request->price);
        $obj->column_type        = $request->column_type;
        $obj->type               = $planDetail->type; 
        $obj->is_free            = $request->is_free;
       
        if($request->discountCheck == 'on'){
            $obj->discount           = $request->discount_price;
            $discountAmount          = $request->price * ($request->discount_price / 100);
            $obj->total_price        = $request->price - $discountAmount;
        }else{
            $obj->total_price        = ($request->is_free ? '0' : ($request->price ?? '0'));
        }
        $obj->validate_string    = $randomString;
        $obj->save();
        Session()->flash('success', ucfirst(trans("messages.subscription_plan_has_been_added_successfully")));
        

        $typeData = '';
        $columntypeData = '';
        if ($obj->type == '0') {
            $typeData = trans('messages.monthly');
        } elseif ($obj->type == '1') {
            $typeData = trans('messages.quarterly');
        } elseif ($obj->type == '2') {
            $typeData = trans('messages.Half Yearly');
        } elseif ($obj->type == '3') {
            $typeData = trans('messages.Yearly');
        }

    
        if ($obj->column_type == '0') {
            $columntypeData = trans('messages.Up to 5 Trucks');
        }  else if ($obj->column_type == '1'){
            $columntypeData = trans('messages.More then 5');
        }

        $paymentUrl = route('plan-subscription', $obj->validate_string);

        $truckCompany = User::where("id",$getId)->first()->toArray();

        $Information = array(
            'name'         => $truckCompany['name'],
            'email'        => $truckCompany['email'],
            'phone_number' => $truckCompany['phone_number'],
            'price'        => $request->price,
            'discount'     => $request->discount_price,
            'total_price'  => round($obj->total_price, 2),
            'type'         => $typeData,
            'column_type'  => $columntypeData,
            'paymentUrl'   => $paymentUrl,
        );
        $this->truckCompanySendSubscriptionNotification($getId, $Information, $truckCompany);
        return redirect()->back();
    }

    public function getPlanDuration($paymentmethod, $type){
        
       $planDetails = Plan::where([
            'is_free' => $paymentmethod,
            'column_type' => $type,
            'is_active' => 1,
            'is_deleted' => 0,
        ])->get();
      
      $option_string = '<option value="">'. trans('messages.Select') ." " . trans("messages.admin_plan_name").'</option>';

      foreach($planDetails as $plan_name){

            $typeData = '';

            if ($plan_name->type == '0') {
                $typeData = trans('messages.monthly');
            } elseif ($plan_name->type == '1') {
                $typeData = trans('messages.quarterly');
            } elseif ($plan_name->type == '2') {
                $typeData = trans('messages.Half Yearly');
            } elseif ($plan_name->type == '3') {
                $typeData = trans('messages.Yearly');
            }

        $option_string .= '<option value="'. $plan_name->id .'" data-plan-price="'.$plan_name->price.'">'. $plan_name->plan_name .' </option>';


      }

      return response()->json([
 
             'options'  => $option_string,
       
        ]);

    }

    public function extendPlanExpiry(Request $request){
     
        TruckCompanyRequestSubscription::where(['truck_company_id'=> $request->truck_company_id])->delete();
        $obj = TruckCompanySubscription::where('truck_company_id', $request->truck_company_id)->orderBy('id', 'desc')->first();
        $obj->two_days_before_mail_send     = 0;
        $obj->same_day_mail_send            = 0;
        $obj->status                        = "activate";
        $obj->end_time                      = date("Y-m-d", strtotime($request->expiry_date));
        $obj->save();

        $typeData = '';
        $columntypeData = '';
        if ($obj->type == '0') {
            $typeData = trans('messages.monthly');
        } elseif ($obj->type == '1') {
            $typeData = trans('messages.quarterly');
        } elseif ($obj->type == '2') {
            $typeData = trans('messages.Half Yearly');
        } elseif ($obj->type == '3') {
            $typeData = trans('messages.Yearly');
        }

        $truckCompany = User::where("id",$request->truck_company_id)->first()->toArray();
     

        $Information = array(
            'name'         => $truckCompany['name'],
            'email'        => $truckCompany['email'],
            'phone_number' => $truckCompany['phone_number'],
            'price'        => $obj->price,
            'discount'     => $obj->discount_price,
            'total_price'  => round($obj->total_price, 2),
            'type'         => $typeData,
            'expiry_date'  => date(Config('Reading.date_format'), strtotime($request->expiry_date)),
        );
        
        $this->sendTruckCompanySubscriptionPlanExpiryDateExtend($truckCompany['id'], $Information, $truckCompany);

       session()->flash('flash_notice', trans('messages.plan_expiry_date_extend_message'));
       $lastUrl = url()->previous();
       return redirect()->to($lastUrl . '?tabs=plan_details');
    }

    public function truckCompanyFuelingMethods(Request $request)
    {
       
        $DB = User::query()->with(['userCompanyInformation','userCompanyInformation.getCompanyRefuelingDescription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
        ]);
        $DB->leftjoin('user_company_informations', 'user_company_informations.user_id', 'users.id')
        ->select(
            'users.*',
            'user_company_informations.company_name',
            'user_company_informations.company_refueling',
        );
        $allResultCount = $DB->count();
          
        $searchVariable = array();
        $inputGet = $request->all();
        if ($request->all()) {
            $searchData = $request->all();
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
                $dateS = date("Y-m-d", strtotime($searchData['date_from']));
                $dateE = date("Y-m-d", strtotime($searchData['date_to']));
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
                    if ($fieldName == "phone_number") {
                        $DB->whereHas('userCompanyInformation', function($query) use($fieldValue){
                            $query->where('users.phone_number', 'like', '%' . $fieldValue . '%');
                        });
                    }
                    if ($fieldName == "refueling_method") {
                        $DB->whereHas('userCompanyInformation', function($query) use($fieldValue){
                            if($fieldValue == "not_selected"){
                                $query->whereNull('company_refueling' );                                
                            }else{
                                $query->where('company_refueling', $fieldValue );
                            }
                        });
                    }

                    if ($fieldName == "company_id") {
                        $DB->where("user_company_informations.company_id", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "company_name") {
                        $DB->where("user_company_informations.company_name", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "system_id") {
                        $DB->where("users.system_id", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }
             $allResultCount = $DB->count();
        }

        

        $DB->where("users.is_deleted", 0);
        $DB->where("users.user_role_id", 3);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'users.created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
      
        $DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_company_track_fueling_methods'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);


        $complete_string = $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string = http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
        $refuelingMethodList = Lookup::where('lookup_type', "fueling-methods")
        ->with([
            'lookupDiscription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
        ])->get();
        return View("admin.$this->model.fueling-methods", compact('resultcount', 'refuelingMethodList', 'results', 'searchVariable', 'sortBy', 'order', 'query_string', 'allResultCount'));
    }

    public function truckCompanyFuelingMethodNotification(Request $request){

        if($request->isMethod("post")){

            $DB = User::query()->with(['userCompanyInformation','userCompanyInformation.getCompanyRefuelingDescription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
            ]);
            $DB->leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
                ->select(
                'users.*',
                'user_company_informations.company_name',
            );

                $searchVariable = json_decode(html_entity_decode($request->searchVariable),true);
                if (count($searchVariable)>0) {
                    $searchData = $searchVariable;

                if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                    $dateS = date("Y-m-d", strtotime($searchData['date_from']));
                    $dateE = date("Y-m-d", strtotime($searchData['date_to']));
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
                        if ($fieldName == "phone_number") {
                            $DB->whereHas('userCompanyInformation', function($query) use($fieldValue){
                                $query->where('users.phone_number', 'like', '%' . $fieldValue . '%');
                            });
                        }
                        if ($fieldName == "refueling_method") {
                            $DB->whereHas('userCompanyInformation', function($query) use($fieldValue){
                                if($fieldValue == "not_selected"){
                                    $query->whereNull('company_refueling' );                                
                                }else{
                                    $query->where('company_refueling', $fieldValue );
                                }
                            });
                        }

                        if ($fieldName == "company_id") {
                            $DB->where("user_company_informations.company_id", 'like', '%' . $fieldValue . '%');
                        }
                        if ($fieldName == "company_name") {
                            $DB->where("user_company_informations.company_name", 'like', '%' . $fieldValue . '%');
                        }

                        if ($fieldName == "system_id") {
                            $DB->where("users.system_id", 'like', '%' . $fieldValue . '%');
                        }
                    }
                    $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
                }
     
            }

            //////////////////////////////////////////filter stop

            if($request->checkType == "allIdsSelected"){
               Session::put(['refueling_methods_ids'=>$DB->pluck('id')->toArray()]);
               if(is_array(Session::get('refueling_methods_ids'))) {
                    $count = count(Session::get('refueling_methods_ids'));
                    $Selected = 'allIdsSelected';
                }
            }else if($request->checkType == "allIdsNotSelected"){
                Session::forget('refueling_methods_ids');
                $count = 0;
                $Selected = 'allIdsNotSelected';
            }if ($request->checkType == 'id' && $request->idSelected == 'IdSelected') {
                $selectedIds = session('refueling_methods_ids', []);
                $selectedIds[] = $request->id; 
                Session::put('refueling_methods_ids', $selectedIds);
                $count = count(Session::get('refueling_methods_ids'));
                $Selected = '';
            } else if ($request->checkType == 'id' && $request->idSelected == 'IdNotSelected') {
                $selectedIds = session('refueling_methods_ids', []);
                $selectedIds = array_diff($selectedIds, [$request->id]);
                Session::put('refueling_methods_ids', $selectedIds);
                $count = count(Session::get('refueling_methods_ids'));
                $Selected = '';
            }
            
            return response()->json(['status' => true, 'allCount' => $count, 'selected' => $Selected]);


        }else{

            $notificationTemplateActions =  NotificationAction::whereIn('action', ['truck_company_fueling_method_notification'])->get();

            $notificationTemplateIds = NotificationTemplate::whereIn('action', ['truck_company_fueling_method_notification'])->pluck('id')->toArray();

            $NotificationTemplateDescription = NotificationTemplateDescription::with('NotificationAction')->whereIn('parent_id', $notificationTemplateIds)->where('language_id', $this->current_language_id())->get();

            $TemplateDescription = NotificationTemplateDescription::whereIn('parent_id', $notificationTemplateIds)->get();

            $options =  NotificationAction::whereIn('action', ['truck_company_fueling_method_notification'])->value('options');
            $optionsvalue = explode(',', $options);

            // Email Notification...
            $emailTemplateActions =  EmailAction::whereIn('action', ['truck_company_fueling_method_notification'])->get();


            $emailTemplatesIds       =  EmailTemplate::whereIn('action', ['truck_company_fueling_method_notification'])->pluck('id')->toArray();

            $Email_Template_Description = EmailTemplateDescription::with('EmailAction')->whereIn('parent_id', $emailTemplatesIds)->where('language_id', $this->current_language_id())->get();

            $emailTemplateDescription = EmailTemplateDescription::whereIn('parent_id', $emailTemplatesIds)->get();

            $emailoptions =  EmailAction::whereIn('action', ['truck_company_fueling_method_notification'])->value('options');
            $emailoptionsvalue = explode(',', $options);


            return view('admin.'.$this->model.'.truck_company_fueling_method_notification', compact('notificationTemplateActions', 'notificationTemplateIds', 'NotificationTemplateDescription', 'TemplateDescription', 'optionsvalue', 'emailTemplateActions', 'emailTemplatesIds', 'emailTemplateDescription', 'Email_Template_Description', 'emailoptions', 'emailoptionsvalue'));

        }

    }

    public function sendCompanyFuelingMethodNotification(Request $request){

     foreach($request->input("notificationAction") as $notificationAction){
        
            $fuelingMethodIds = Session::get('refueling_methods_ids') ?? 0;

            $DB = User::query();
            $DB->leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
               ->leftjoin('lookup_discriptions', 'lookup_discriptions.parent_id', 'user_company_informations.company_refueling')
               ->where('lookup_discriptions.language_id', getAppLocaleId())
                ->select(
                'users.*',
                'user_company_informations.company_name as truck_company_name',
                'users.phone_number',
                'user_company_informations.contact_person_name',
                'user_company_informations.contact_person_email',
                'user_company_informations.contact_person_phone_number',
                'lookup_discriptions.code as refueling_method',
                'users.language',
            )->whereIn('users.id', $fuelingMethodIds);


            $refuelingMethodsList = $DB->get();

            $notificationActions    =   NotificationAction::where('action','=', $notificationAction)
            ->get()
            ->toArray();

            $cons           =   explode(',',$notificationActions[0]['options']);
            $constants      =   array();
            foreach($cons as $key => $val){
                $constants[] = '{'.$val.'}';
            }
            
            $map_id = 0;

            $notification = $request->input($notificationAction);

            // Email actions
			$emailActions 	= 	EmailAction::where('action','=',$notificationAction)->get()->toArray();
			$emailCons 			= 	explode(',',$emailActions[0]['options']);
			$emailConstants 		= 	array();
			foreach($emailCons as $key => $val){
				$emailConstants[] = '{'.$val.'}';
			}

            foreach($refuelingMethodsList as $refuelingMethod){

                $TruckCompany = User::where("id", $refuelingMethod->id)->first();

                $rep_Array         = array(
                 $refuelingMethod->truck_company_name,
                 $refuelingMethod->phone_number,
                 $refuelingMethod->contact_person_name,
                 $refuelingMethod->contact_person_email,
                 $refuelingMethod->contact_person_phone_number,
                 $refuelingMethod->refueling_method,
                );

                // Email send...
			    foreach($notification['email'] as $key => $description){

					if($description['language_id'] == $refuelingMethod->language){
					
						if($request->email_notification == 1 && $refuelingMethod->contact_person_email != ""){
							$messageBody 	= 	str_replace($emailConstants, $rep_Array, $description['description']);
							$requestData = [
								"email" => $refuelingMethod->contact_person_email,
								"name" => $refuelingMethod->contact_person_name,
								"subject" => $description["subject"],
								"messageBody" => $messageBody,
							];
							SendMail::dispatch($requestData)->onQueue('send_mail');  
						}
							
					}

				}

                foreach($notification['notification'] as $key => $description){
                    if($request->system_notification == 1){
                        $notificationObj = new Notification();
                        $notificationObj->user_id               = $refuelingMethod->id;
                        $notificationObj->language_id           = $description['language_id'];
                        $notificationObj->title                 = $description["subject"];
                        $notificationObj->description           = str_replace($constants, $rep_Array, $description['description']);
                        $notificationObj->is_read               = 0;
                        $notificationObj->shipment_id           = 0;
                        $notificationObj->notification_type     = $notificationAction;
                        $notificationObj->is_notification_sent  = 0;
                        $notificationObj->map_id                = $map_id;
                        $notificationObj->save();
                        if($map_id == 0){
                            $notificationObj->map_id            = $notificationObj->id;
                            $notificationObj->save();
                            $map_id = $notificationObj->id;
                        }
                    }
                    if($TruckCompany['language'] == $description['language_id'] ){
                        $selectedNotification   = $description;
                        $message               = str_replace($constants, $rep_Array, $description['description']);
                        $notification_type      = $notificationAction;
                        $title                 = $description['subject'];
                        $service_request_id    = 0;
                        
                        // System notification 
                        if($request->system_notification == 1){
                         $service_number        = $notificationObj->id;
                        } 
                        
                        // Whatsapp notification 
                        if($request->whatsapp_notification == 1){
                         //send whatsapp message
                         SendGreenApiMessage::dispatch($message,$TruckCompany)->onQueue('send_green_api_message');
                        }
                    }

                  
        
                }

                // System notification 
                if($request->system_notification == 1){
                    $data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

                    $user_device_tokens = DB::table("user_device_tokens")->where("user_id",$TruckCompany['id'])->orderBy("id","DESC")->first();
                    if($user_device_tokens){
                        $server_key             =   Config::get("Site.truck_company_android_sever_api_key");
                        $adwance_options = array(
                            'type'              => 'shipment',
                            'map_id'            => $map_id,
                            'shipments_status'  => 0,
                            'request_number'    => 0,
                        );
                        SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
                    }
                }
            }       


        }

        Session::forget('refueling_methods_ids');
        Session()->flash('flash_notice', trans('messages.truck_fueling_method_notification_message'));
        return redirect()->route($this->model.'.fueling-methods');
     

    }

    public function fuelingMethodsExport(Request $request)
	{

        
        $list[0] = array(
			trans('messages.admin_sys_id'),
            trans('messages.Company Name'),
            trans('messages.admin_common_Email'),
            trans('messages.company_number'),
            trans('messages.refueling_method'),
		);

		$customers_export = Session::get('export_data_company_track_fueling_methods');
		
		foreach ($customers_export as $key => $excel_export) {
            $list[] = array(
                $excel_export->system_id,
                $excel_export->userCompanyInformation ?->company_name,
                $excel_export->email,
                $excel_export->phone_number,
                $excel_export->userCompanyInformation ?->getCompanyRefuelingDescription ?->code,
            );
        }

        $collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'Truck Company Fueling Methods.xlsx');
			

	}
    public function truckCompanyTidalukCompany(Request $request)
    {
       
        $DB = User::query()->with(['userCompanyInformation','userCompanyInformation.getCompanyTidalukCompanyDescription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
        ]);
        $DB->leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
            ->select(
                'users.*',
                'user_company_informations.company_name',
            );
        $allResultCount = $DB->count();
          
        $searchVariable = array();
        $inputGet = $request->all();
        if ($request->all()) {
            $searchData = $request->all();
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
                $dateS = date("Y-m-d", strtotime($searchData['date_from']));
                $dateE = date("Y-m-d", strtotime($searchData['date_to']));
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
                    if ($fieldName == "phone_number") {
                        $DB->whereHas('userCompanyInformation', function($query) use($fieldValue){
                            $query->where('users.phone_number', 'like', '%' . $fieldValue . '%');
                        });
                    }
                    if ($fieldName == "company_tidaluk") {
                        $DB->whereHas('userCompanyInformation', function($query) use($fieldValue){
                            if($fieldValue == "not_selected"){
                                $query->whereNull('company_tidaluk' );                                
                            }else{
                                $query->where('company_tidaluk', $fieldValue );
                            }
                        });
                    }

                    if ($fieldName == "company_id") {
                        $DB->where("user_company_informations.company_id", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "company_name") {
                        $DB->where("user_company_informations.company_name", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "system_id") {
                        $DB->where("users.system_id", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }

            $allResultCount = $DB->count();
        }

        $DB->where("users.is_deleted", 0);
        $DB->where("users.user_role_id", 3);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'users.created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
      
        $DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_company_track_tidaluk'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);


        $complete_string = $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string = http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
        $tidalukCompanyList = Lookup::where('lookup_type', "tidaluk-company-type")
        ->with([
            'lookupDiscription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
        ])->get();
        return View("admin.$this->model.tidaluk-company", compact('resultcount', 'tidalukCompanyList', 'results', 'searchVariable', 'sortBy', 'order', 'query_string', 'allResultCount'));
    }

    public function tidalukCompanyExport(Request $request)
	{

        
        $list[0] = array(
			trans('messages.admin_sys_id'),
            trans('messages.Company Name'),
            trans('messages.admin_common_Email'),
            trans('messages.company_number'),
            trans('messages.admin_Tidaluk_Company'),
		);

		$customers_export = Session::get('export_data_company_track_tidaluk');
		
		foreach ($customers_export as $key => $excel_export) {
            $list[] = array(
                $excel_export->system_id,
                $excel_export->userCompanyInformation ?->company_name,
                $excel_export->email,
                $excel_export->phone_number,
                $excel_export->userCompanyInformation ?->getCompanyTidalukCompanyDescription ?->code,
            );
        }

        $collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'Truck Company Tidaluk Company.xlsx');
			

	}

    public function truckCompanyTidalukMethodNotification(Request $request){
        
        if($request->isMethod("post")){

            $DB = User::query()->with(['userCompanyInformation','userCompanyInformation.getCompanyRefuelingDescription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
            ]);
            $DB->leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
                ->select(
                'users.*',
                'user_company_informations.company_name',
            );

                $searchVariable = json_decode(html_entity_decode($request->searchVariable),true);
                if (count($searchVariable)>0) {
                    $searchData = $searchVariable;

                if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                    $dateS = date("Y-m-d", strtotime($searchData['date_from']));
                    $dateE = date("Y-m-d", strtotime($searchData['date_to']));
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
                        if ($fieldName == "phone_number") {
                            $DB->whereHas('userCompanyInformation', function($query) use($fieldValue){
                                $query->where('users.phone_number', 'like', '%' . $fieldValue . '%');
                            });
                        }
                        if ($fieldName == "refueling_method") {
                            $DB->whereHas('userCompanyInformation', function($query) use($fieldValue){
                                if($fieldValue == "not_selected"){
                                    $query->whereNull('company_refueling' );                                
                                }else{
                                    $query->where('company_refueling', $fieldValue );
                                }
                            });
                        }

                        if ($fieldName == "company_id") {
                            $DB->where("user_company_informations.company_id", 'like', '%' . $fieldValue . '%');
                        }
                        if ($fieldName == "company_name") {
                            $DB->where("user_company_informations.company_name", 'like', '%' . $fieldValue . '%');
                        }

                        if ($fieldName == "system_id") {
                            $DB->where("users.system_id", 'like', '%' . $fieldValue . '%');
                        }
                    }
                    $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
                }
     
            }

            //////////////////////////////////////////filter stop

            if($request->checkType == "allIdsSelected"){
               Session::put(['tidaluk_company_ids'=>$DB->pluck('id')->toArray()]);
               if(is_array(Session::get('tidaluk_company_ids'))) {
                    $count = count(Session::get('tidaluk_company_ids'));
                    $Selected = 'allIdsSelected';
                }
            }else if($request->checkType == "allIdsNotSelected"){
                Session::forget('tidaluk_company_ids');
                $count = 0;
                $Selected = 'allIdsNotSelected';
            }if ($request->checkType == 'id' && $request->idSelected == 'IdSelected') {
                $selectedIds = session('tidaluk_company_ids', []);
                $selectedIds[] = $request->id; 
                Session::put('tidaluk_company_ids', $selectedIds);
                $count = count(Session::get('tidaluk_company_ids'));
                $Selected = '';
            } else if ($request->checkType == 'id' && $request->idSelected == 'IdNotSelected') {
                $selectedIds = session('tidaluk_company_ids', []);
                $selectedIds = array_diff($selectedIds, [$request->id]);
                Session::put('tidaluk_company_ids', $selectedIds);
                $count = count(Session::get('tidaluk_company_ids'));
                $Selected = '';
            }
            
            return response()->json(['status' => true, 'allCount' => $count, 'selected' => $Selected]);


        }else{

            $notificationTemplateActions =  NotificationAction::whereIn('action', ['truck_company_tidaluk_company_notification'])->get();

            $notificationTemplateIds = NotificationTemplate::whereIn('action', ['truck_company_tidaluk_company_notification'])->pluck('id')->toArray();

            $NotificationTemplateDescription = NotificationTemplateDescription::with('NotificationAction')->whereIn('parent_id', $notificationTemplateIds)->where('language_id', $this->current_language_id())->get();

            $TemplateDescription = NotificationTemplateDescription::whereIn('parent_id', $notificationTemplateIds)->get();

            $options =  NotificationAction::whereIn('action', ['truck_company_tidaluk_company_notification'])->value('options');
            $optionsvalue = explode(',', $options);

            // Email Notification...
            $emailTemplateActions =  EmailAction::whereIn('action', ['truck_company_tidaluk_company_notification'])->get();


            $emailTemplatesIds       =  EmailTemplate::whereIn('action', ['truck_company_tidaluk_company_notification'])->pluck('id')->toArray();

            $Email_Template_Description = EmailTemplateDescription::with('EmailAction')->whereIn('parent_id', $emailTemplatesIds)->where('language_id', $this->current_language_id())->get();

            $emailTemplateDescription = EmailTemplateDescription::whereIn('parent_id', $emailTemplatesIds)->get();

            $emailoptions =  EmailAction::whereIn('action', ['truck_company_tidaluk_company_notification'])->value('options');
            $emailoptionsvalue = explode(',', $options);


            return view('admin.'.$this->model.'.truck_company_tidaluk_company_notification', compact('notificationTemplateActions', 'notificationTemplateIds', 'NotificationTemplateDescription', 'TemplateDescription', 'optionsvalue', 'emailTemplateActions', 'emailTemplatesIds', 'emailTemplateDescription', 'Email_Template_Description', 'emailoptions', 'emailoptionsvalue'));

        }

    }

    public function sendCompanyTidalukCompanyNotification(Request $request){

     foreach($request->input("notificationAction") as $notificationAction){
        
            $tidalukCompanyIds = Session::get('tidaluk_company_ids') ?? 0;

            $DB = User::query();
            $DB->leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
               ->leftjoin('lookup_discriptions', 'lookup_discriptions.parent_id', 'user_company_informations.company_tidaluk')
               ->where('lookup_discriptions.language_id', getAppLocaleId())
                ->select(
                'users.*',
                'user_company_informations.company_name as truck_company_name',
                'users.phone_number',
                'user_company_informations.contact_person_name',
                'user_company_informations.contact_person_email',
                'user_company_informations.contact_person_phone_number',
                'lookup_discriptions.code as company_tidaluk',
                'users.language',
            )->whereIn('users.id', $tidalukCompanyIds);


            $tidalukCompanyList = $DB->get();

            $notificationActions    =   NotificationAction::where('action','=', $notificationAction)
            ->get()
            ->toArray();

            $cons           =   explode(',',$notificationActions[0]['options']);
            $constants      =   array();
            foreach($cons as $key => $val){
                $constants[] = '{'.$val.'}';
            }
            
            $map_id = 0;

            $notification = $request->input($notificationAction);

            // Email actions
			$emailActions 	= 	EmailAction::where('action','=',$notificationAction)->get()->toArray();
			$emailCons 			= 	explode(',',$emailActions[0]['options']);
			$emailConstants 		= 	array();
			foreach($emailCons as $key => $val){
				$emailConstants[] = '{'.$val.'}';
			}

            foreach($tidalukCompanyList as $tidalukCompanyList){

                $TruckCompany = User::where("id", $tidalukCompanyList->id)->first();

                $rep_Array         = array(
                 $tidalukCompanyList->truck_company_name,
                 $tidalukCompanyList->phone_number,
                 $tidalukCompanyList->contact_person_name,
                 $tidalukCompanyList->contact_person_email,
                 $tidalukCompanyList->contact_person_phone_number,
                 $tidalukCompanyList->company_tidaluk,
                );

                // Email send...
			    foreach($notification['email'] as $key => $description){

					if($description['language_id'] == $tidalukCompanyList->language){
					
						if($request->email_notification == 1 && $tidalukCompanyList->contact_person_email != ""){
							$messageBody 	= 	str_replace($emailConstants, $rep_Array, $description['description']);
							$requestData = [
								"email" => $tidalukCompanyList->contact_person_email,
								"name" => $tidalukCompanyList->contact_person_name,
								"subject" => $description["subject"],
								"messageBody" => $messageBody,
							];
							SendMail::dispatch($requestData)->onQueue('send_mail');  
						}
							
					}

				}

                foreach($notification['notification'] as $key => $description){
                    if($request->system_notification == 1){
                        $notificationObj = new Notification();
                        $notificationObj->user_id               = $tidalukCompanyList->id;
                        $notificationObj->language_id           = $description['language_id'];
                        $notificationObj->title                 = $description["subject"];
                        $notificationObj->description           = str_replace($constants, $rep_Array, $description['description']);
                        $notificationObj->is_read               = 0;
                        $notificationObj->shipment_id           = 0;
                        $notificationObj->notification_type     = $notificationAction;
                        $notificationObj->is_notification_sent  = 0;
                        $notificationObj->map_id                = $map_id;

                        $notificationObj->save();
                        if($map_id == 0){
                            $notificationObj->map_id            = $notificationObj->id;
                            $notificationObj->save();
                            $map_id = $notificationObj->id;
                        }
                    }

                    if($TruckCompany['language'] == $description['language_id'] ){
                        $selectedNotification   = $description;
                        $message               = str_replace($constants, $rep_Array, $description['description']);
                        $notification_type      = $notificationAction;
                        $title                 = $description['subject'];
                        $service_request_id    = 0;
                        

                        if($request->system_notification == 1){
                            $service_number        = $notificationObj->id;
                        }
                        
                        if($request->whatsapp_notification == 1){
                        //send whatsapp message
                            SendGreenApiMessage::dispatch($message,$TruckCompany)->onQueue('send_green_api_message');
                        }
                    }


                   
        
                }

                if($request->system_notification == 1){
                    $data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);
                    $user_device_tokens = DB::table("user_device_tokens")->where("user_id",$TruckCompany['id'])->orderBy("id","DESC")->first();
                    if($user_device_tokens){
                        $server_key             =   Config::get("Site.truck_company_android_sever_api_key");
                        $adwance_options = array(
                            'type'              => 'shipment',
                            'map_id'            => $map_id,
                            'shipments_status'  => 0,
                            'request_number'    => 0,
                        );
                        SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
                    }
                }
               


            }       


        }

        Session::forget('tidaluk_company_ids');
        Session()->flash('flash_notice', trans('messages.truck_tidaluk_company_notification_message'));
        return redirect()->route($this->model.'.tidaluk-company');
     

    }

    public function fetch_truck_drivers(Request $request){
        if($request->company_id != ''){
            $driverLists    = User::where('users.truck_company_id', $request->company_id)
                            ->leftjoin('trucks','trucks.driver_id','users.id')
                            ->select('users.*')
                            ->whereNull("trucks.id")
                            ->where('users.user_role_id', 4)->where(['users.is_active' => 1, 'users.is_deleted' => 0])->orderBy('users.id', 'desc')->get();
        }else{
            $driverLists = '';
        }
        return response()->json(['driverLists' => $driverLists]);

    }
   
}