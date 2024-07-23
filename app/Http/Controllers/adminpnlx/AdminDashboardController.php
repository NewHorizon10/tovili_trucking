<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

use App\Models\Admin;
use App\Models\User;
use App\Models\Shipment;
use App\Models\UserCompanyInformation;


class AdminDashboardController extends Controller
{
   public $model = 'dashboard';
   public function __construct(Request $request)
   {  
      parent::__construct();
      View()->share('model', $this->model);
      $this->request = $request;
   }

   public function showdashboard()
   {  $total = array();
      $total['private_customer']	=	DB::table("users")
      ->where('user_role_id',2)
      ->where('customer_type','private')
      ->where('is_deleted', 0)
      ->count();

      

      $total['business_customer'] = DB::table("users")
      ->where('user_role_id',2)
      ->where('customer_type','business')
      ->where('is_deleted', 0)
      ->count();


      $total['truck_company'] = User::leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
      ->leftjoin('truck_company_subscription_plans', 'truck_company_subscription_plans.truck_company_id', 'users.id')
      ->leftjoin('plans', 'plans.id', 'truck_company_subscription_plans.plan_id')
     
      ->where("users.is_deleted", 0)
      ->where("users.user_role_id", 3)
      ->count();
      
      $total['driver'] = User::leftjoin('users as user_company', 'users.truck_company_id' , 'user_company.id')
         ->leftjoin('user_driver_details', 'users.id' , 'user_driver_details.user_id')
         ->leftjoin('user_company_informations', 'user_company.id' , 'user_company_informations.user_id')
         ->where("users.is_deleted", 0)
         ->where("users.user_role_id", 4)
      ->count();

      $total['total_shipment_requests'] = DB::table("shipments")
        ->leftjoin('users', 'shipments.customer_id' , 'users.id')
        ->whereIn('shipments.status',['new','offers','offer_chosen'])
        ->where("users.is_deleted", 0)
        ->where("users.user_role_id", 2)
        ->count();

      $total['total_shipment_offers'] = DB::table("shipment_offers")
      ->join('trucks', 'trucks.id' , 'shipment_offers.truck_id')
      ->join('shipments', 'shipments.id' , 'shipment_offers.shipment_id')
      ->join('users', 'shipments.customer_id' , 'users.id')
      ->where("users.is_deleted", 0)
      ->count();

      $language_id = getAppLocaleId();
      $total['total_active_shipment'] = Shipment::leftjoin('users', 'shipments.customer_id' , 'users.id')
      ->leftjoin('shipment_driver_schedules','shipments.id','shipment_driver_schedules.shipment_id')
      ->select(
          'shipments.*',
          'users.name as user_name',
          'shipment_driver_schedules.shipment_status',
          DB::raw("
              IF(shipments.request_type = 0,
                  (SELECT name FROM truck_type_descriptions 
                      WHERE truck_type_descriptions.parent_id = shipments.shipment_type 
                          AND truck_type_descriptions.language_id = $language_id),
                  (SELECT name FROM truck_type_descriptions 
                  WHERE truck_type_descriptions.parent_id = shipments.request_type 
                      AND truck_type_descriptions.language_id = $language_id)) 
                  as request_types
          ")
      )->where('status', 'shipment')
      ->where('shipment_status', 'start')     
      ->where("users.is_deleted", 0)
      ->where("users.user_role_id", 2)
      ->whereIn('shipments.status', ['shipment', 'end', 'cancelled'])
      ->count();

      $total['total_new_shipment'] = DB::table("shipments")
        ->leftjoin('users', 'shipments.customer_id' , 'users.id')
        ->whereIn('shipments.status',['new'])
        ->where("users.is_deleted", 0)
        ->where("users.user_role_id", 2)
        ->count();


      $total['total_expired_truck_insurance'] = DB::table("trucks")
         ->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
         ->where('trucks.is_deleted', 0)
         ->where('truck_type_descriptions.language_id', getAppLocaleId())
         ->where("trucks.truck_insurance_expiration_date", '<=', now()->toDateString())
         ->count();
      
      $total['total_near_to_expired_truck_insurance'] = DB::table("trucks")
         ->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
         ->where('trucks.is_deleted', 0)
         ->where('truck_type_descriptions.language_id', getAppLocaleId())
         ->whereBetween("trucks.truck_insurance_expiration_date", [now()->toDateString(),now()->addDays(4)->toDateString()])
         ->count();

      $total['total_expired_truck_license'] = DB::table("trucks")
         ->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
         ->where('trucks.is_deleted', 0)
         ->where('truck_type_descriptions.language_id', getAppLocaleId())
         ->where("trucks.truck_licence_expiration_date", '<=', now()->toDateString())
         ->count();
      
      $total['total_near_to_expired_truck_license'] = DB::table("trucks")
         ->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
         ->where('trucks.is_deleted', 0)
         ->where('truck_type_descriptions.language_id', getAppLocaleId())
         ->whereBetween("trucks.truck_licence_expiration_date", [now()->toDateString(),now()->addDays(4)->toDateString()])
         ->count();

      $total['activated_plan_details'] = User::leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
      ->leftjoin('truck_company_subscription_plans', 'truck_company_subscription_plans.truck_company_id', 'users.id')
      ->leftjoin('plans', 'plans.id', 'truck_company_subscription_plans.plan_id')
      ->whereHas('planDetails', function($query) {
         $query->where('status', "activate");
      })
      ->where("users.is_deleted", 0)
      ->where("users.user_role_id", 3)
      ->count();

      $total['expired_company_plan'] = User::leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
      ->leftjoin('truck_company_subscription_plans', 'truck_company_subscription_plans.truck_company_id', 'users.id')
      ->leftjoin('plans', 'plans.id', 'truck_company_subscription_plans.plan_id')
      ->whereHas('planDetails', function($query) {
         $query->where('status', "deactivate");
      })
      ->where("users.is_deleted", 0)
      ->where("users.user_role_id", 3)
      ->count();

      $truckCompanyIds = DB::table('truck_company_subscription_plans')
      ->pluck('truck_company_id')
      ->toArray();

      $total['without_plan_company'] =  User::leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
      ->leftjoin('truck_company_subscription_plans', 'truck_company_subscription_plans.truck_company_id', 'users.id')
      ->leftjoin('plans', 'plans.id', 'truck_company_subscription_plans.plan_id')
      ->whereDoesntHave('planDetails')
      ->where("users.is_deleted", 0)
      ->where("users.user_role_id", 3)
      ->count();

      $total['near_to_expire_plan'] = DB::table("truck_company_subscription_plans")
      ->leftjoin('users', 'users.id', 'truck_company_subscription_plans.truck_company_id')
      ->whereNotNull('truck_company_subscription_plans.end_time')
      ->where('truck_company_subscription_plans.status', 'activate')
      ->where("truck_company_subscription_plans.end_time", '<=', now()->addDays(4)->endOfDay()->format('Y-m-d H:i:s'))
      ->where('users.is_deleted', 0)
      ->count();



      $total['total_inactive_private_customers_count']	=	User::whereRaw('COALESCE(((select shipments.created_at from shipments where shipments.customer_id = users.id ORDER BY shipments.id DESC LIMIT 1)), users.updated_at) < ?', [Carbon::now()->subDays(30)])
         ->where("users.is_deleted", 0)
         ->where("users.user_role_id", 2)
         ->where("users.customer_type", 'private')
         ->count();


      $total['total_inactive_business_customers_count'] = User::leftjoin('user_company_informations', 'users.id' , 'user_company_informations.user_id')
         ->whereRaw('COALESCE(((select shipments.created_at from shipments where shipments.customer_id = users.id ORDER BY shipments.id DESC LIMIT 1)), users.updated_at) < ?', [Carbon::now()->subDays(30)])
         ->where("users.is_deleted", 0)
         ->where("users.user_role_id", 2)
         ->where("users.customer_type", 'business')
         ->count();
         
      $total['total_inactive_customer_count'] = $total['total_inactive_business_customers_count'] + $total['total_inactive_private_customers_count'];

  
      $total['total_inactive_truck_company'] = User::leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
      ->leftjoin('truck_company_subscription_plans', 'truck_company_subscription_plans.truck_company_id', 'users.id')
      ->leftjoin('plans', 'plans.id', 'truck_company_subscription_plans.plan_id')
      ->whereRaw('COALESCE(((select shipment_offers.created_at from shipment_offers where shipment_offers.truck_company_id = users.id ORDER BY shipment_offers.id DESC LIMIT 1)), users.updated_at) < ?', [Carbon::now()->subDays(30)])
      ->where("users.is_approved", '!=', 0)
      ->where("users.is_deleted", 0)
      ->where("users.user_role_id", 3)
      ->count();

         
      $shipment['total_shipment_requests'] = DB::table('shipments')
      ->whereIn('status', ['new', 'offers', 'offer_chosen'])
      ->whereBetween('request_date', [Carbon::now()->subYear(), Carbon::now()])
      ->select(
          DB::raw("DATE_FORMAT(request_date,'%Y-%m') as daymonth"),
          DB::raw('COUNT(*) as userCntByMonth')
      )
      ->groupBy('daymonth')
      ->pluck('userCntByMonth','daymonth')
      ->toArray();


      $shipment['total_shipment_offers'] = DB::table('shipments')
      ->where('status', 'offers')
      ->whereBetween('request_date', [Carbon::now()->subYear(), Carbon::now()])
      ->select(
          DB::raw("DATE_FORMAT(request_date,'%Y-%m') as daymonth"),
          DB::raw('COUNT(*) as userCntByMonth')
      )
      ->groupBy('daymonth')
      ->pluck('userCntByMonth','daymonth')
      ->toArray();
  

      $shipment['total_active_shipment'] = DB::table('shipments')
      ->join('shipment_driver_schedules','shipments.id','shipment_driver_schedules.shipment_id')
      ->where('shipment_driver_schedules.shipment_status','start')
      ->whereBetween('shipment_driver_schedules.created_at', [Carbon::now()->subYear(), Carbon::now()])
      ->select(
          DB::raw(" DATE_FORMAT(shipment_driver_schedules.created_at,'%Y-%m') as daymounth"),
          DB::raw('COUNT(*) as userCntByMounth')
      )
      ->groupBy('daymounth')
      ->pluck('userCntByMounth','daymounth')
      ->toArray();



      $shipment['total_new_shipment'] = DB::table('shipments')
      ->where('status', 'new')
      ->whereBetween('created_at',[Carbon::now()->subYear(), Carbon::now()])
      ->select(
          DB::raw(" DATE_FORMAT(created_at,'%Y-%m') as daymounth"),
          DB::raw('COUNT(*) as userCntByMounth')
      )
      ->groupBy('daymounth')
      ->pluck('userCntByMounth','daymounth')
      ->toArray();


      //for graph of total 
         $month										=	date('m');
         $year										=	date('Y');
         for ($i = 0; $i < 12; $i++) {
            $months[] 								=	date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
            
         }
         $months										=	array_reverse($months);

         $num										=	0;
      
         $customer								=	array();
         foreach($months as $key => $month){
            $month_start_date						=	date('Y-m-01 00:00:00', strtotime($month));
            $month_end_date					   =	date('Y-m-t 23:59:59', strtotime($month));
            $strmonth                        =  strtotime($month_start_date)*1000;
            $customer[$num]['month']			=	$strmonth;
            $customer[$num]['private']			=	DB::table('users')
                                                ->where('customer_type','private')
                                                ->where('user_role_id',2)
                                                ->where("is_deleted",0)
                                                ->where('created_at','>=',$month_start_date)
                                                ->where('created_at','<=',$month_end_date)
                                                ->count();
            $customer[$num]['business']	   =	DB::table('users')->where('customer_type','business')
                                                ->where('user_role_id',2)
                                                ->where("is_deleted",0)
                                                ->where('created_at','>=',$month_start_date)
                                                ->where('created_at','<=',$month_end_date)
                                                ->count();
            $customer[$num]['truck_company']	=	DB::table('users')
                                                ->where('user_role_id',3)
                                                ->where("is_deleted",0)
                                                ->where('created_at','>=',$month_start_date)
                                                ->where('created_at','<=',$month_end_date)
                                                ->count();
            $customer[$num]['driver']	 =	      DB::table('users')
                                                ->where('user_role_id',4)
                                                ->where("is_deleted",0)
                                                ->where('created_at','>=',$month_start_date)
                                                ->where('created_at','<=',$month_end_date)
                                                ->count();
            // ;
            // $cnt = count($shipment['total_shipment_requests']);
            $shipment['total_shipment_requests_cnt'][$num]['month'] = date('M y', strtotime($month_start_date));
            $shipment['total_shipment_requests_cnt'][$num]['shipment_requests'] = $shipment['total_shipment_requests'][$month] ?? 0 ;
            $shipment['total_shipment_requests_cnt'][$num]['shipment_offers'] = $shipment['total_shipment_offers'][$month] ?? 0 ;
            $shipment['total_shipment_requests_cnt'][$num]['active_shipment'] = $shipment['total_active_shipment'][$month] ?? 0 ;

            $shipment['total_shipment_requests_cnt'][$num]['new_shipment'] = $shipment['total_new_shipment'][$month] ?? 0 ;



            $shipment['total_shipment_offers_cnt'][$num]['month'] = date('M y', strtotime($month_start_date));
            $shipment['total_shipment_offers_cnt'][$num]['shipment_requests'] = $shipment['total_shipment_requests'][$month] ?? 0 ;
            $shipment['total_shipment_offers_cnt'][$num]['shipment_offers'] = $shipment['total_shipment_offers'][$month] ?? 0 ;
            $shipment['total_shipment_offers_cnt'][$num]['active_shipment'] = $shipment['total_active_shipment'][$month] ?? 0 ;
            $shipment['total_shipment_offers_cnt'][$num]['new_shipment'] = $shipment['total_new_shipment'][$month] ?? 0 ;

            $shipment['total_active_shipment_cnt'][$num]['month'] = date('M y', strtotime($month_start_date));
            $shipment['total_active_shipment_cnt'][$num]['shipment_requests'] = $shipment['total_shipment_requests'][$month] ?? 0;
              $shipment['total_active_shipment_cnt'][$num]['shipment_offers'] = $shipment['total_shipment_offers'][$month] ?? 0 ;
            $shipment['total_active_shipment_cnt'][$num]['active_shipment'] = $shipment['total_active_shipment'][$month] ?? 0 ;
            $shipment['total_active_shipment_cnt'][$num]['new_shipment'] = $shipment['total_new_shipment'][$month] ?? 0 ;

            $shipment['total_new_shipment_cnt'][$num]['month'] = date('M y', strtotime($month_start_date));
            $shipment['total_new_shipment_cnt'][$num]['shipment_requests'] = $shipment['total_shipment_requests'][$month] ?? 0;
              $shipment['total_new_shipment_cnt'][$num]['shipment_offers'] = $shipment['total_shipment_offers'][$month] ?? 0 ;
            $shipment['total_new_shipment_cnt'][$num]['active_shipment'] = $shipment['total_active_shipment'][$month] ?? 0 ;
            $shipment['total_new_shipment_cnt'][$num]['new_shipment'] = $shipment['total_new_shipment'][$month] ?? 0 ;


            $num ++;
         }
         unset($shipment['total_shipment_requests']);
         unset($shipment['total_active_shipment']);
         unset($shipment['total_shipment_offers']);
         unset($shipment['total_new_shipment']);

     
     
  
         $total_fueling_methods = User::query()->with(['userCompanyInformation','userCompanyInformation.getCompanyRefuelingDescription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
         ])
         ->leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
         ->where("users.is_deleted", 0)
         ->where("users.user_role_id", 3);
         $total['total_fueling_mothods'] = $total_fueling_methods->count();

         // Fueling Method Data
         $total['fuelingMethodsdata'] = User::
         leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
         ->where('users.is_deleted', 0)
         ->where('users.user_role_id', 3)
         ->select(
              'user_company_informations.company_refueling as fueling_method_id',
              
              DB::raw("(select code from lookup_discriptions where lookup_discriptions.parent_id=user_company_informations.company_refueling and lookup_discriptions.language_id= '".getAppLocaleId()."' limit 1) as fueling_method_name"),
              DB::raw('count(user_company_informations.user_id) as fueling_company_count')
         )
          
         ->groupBy('user_company_informations.company_refueling')
         ->get();


         $total_tidaluk_company = User::query()->with(['userCompanyInformation','userCompanyInformation.getCompanyTidalukCompanyDescription' => function ($query) {
                $query->where(['language_id' => getAppLocaleId()]);
            }
         ])->leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
         ->where("users.is_deleted", 0)
         ->where("users.user_role_id", 3);

         $total['total_tidaluk_company'] = $total_tidaluk_company->count();

         //  Tidaluk Company Data
         $total['tidalukCompanydata'] = User::
         leftjoin('user_company_informations', 'users.id', 'user_company_informations.user_id')
         ->select(
            'user_company_informations.company_tidaluk as tidaluk_company_id',
            DB::raw("(select code from lookup_discriptions where lookup_discriptions.parent_id=user_company_informations.company_tidaluk and lookup_discriptions.language_id= '".getAppLocaleId()."' limit 1) as tidaluk_company_name"),
            DB::raw('count(user_company_informations.user_id) as tidaluk_company_count')
         )
         ->where('users.is_deleted', 0)
         ->where('users.user_role_id', 3)
         ->groupBy('user_company_informations.company_tidaluk')
         ->get();


      return  View('admin.dashboard.dashboard',compact('total','customer','shipment'));
   }

   public function getChatCount(){
      $total = array();
      $total['total']	=	DB::table("chats")
         ->where('receiver_id',1)
         ->where('channel_id',1)
         ->where('is_read',0)
         ->count();
         if($total['total']<=0){
            $total['total'] = "";
         }

         return response()->json(['success' => true, 'data' => $total]);
      
   }



   public function myaccount(Request $request)
   {
      if ($request->isMethod('POST')) {
         $validated = $request->validate([
            'name' => 'required',
            'email'  => 'required|email:rfc,dns|regex:/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/',
         ]);
         $user             =    Admin::find(Auth::guard('admin')->user()->id);
         $user->name       =    $request->name;
         $user->email       =    $request->email;
         if ($user->save()) {
            return Redirect()->route('dashboard')->with('success', trans("messages.admin_Information_updated_successfully"));
         }
      }
      $userInfo   =   Auth::guard("admin")->user();
      return  View("admin.$this->model.myaccount", compact('userInfo'));
   }

   public function changedPassword(Request $request)
   {
      if ($request->isMethod('POST')) {
         $validated = $request->validate([
            'old_password' => 'required',
            'new_password' => ['required', 'string', 'between:4,8', 'regex:'.Config('constants.PASSWORD_VALIDATION_STRING')],
            'confirm_password' => 'required|same:new_password',
         ],
         [

            "old_password.required"          => trans("messages.The old password field is required"),
            "new_password.required"          => trans("messages.admin_The_new_password_field_is_required"),
            "new_password.string"            => trans("messages.admin_The_new_password_should_be_string"),
            "new_password.between"               => trans("messages.password_should_be_in_between_4_to_8_characters"),
            "new_password.regex"               => trans("messages.password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character"),
            "confirm_password.required"      => trans("messages.confirm_password_field_is_required"),
            "confirm_password.same"          => trans("messages.The confirm password must be the same as the password"),

        ]);
         $user = Admin::find(Auth::guard('admin')->user()->id);
         $oldpassword = $request->old_password;
         if (Hash::check($oldpassword, $user->getAuthPassword())) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return Redirect()->route('dashboard')
               ->with('success', trans("messages.Password has been changed successfully"));
         } else {
            return Redirect()->route('dashboard')
               ->with('error', trans("messages.Your old password is incorrect"));
         }
      }
      return  View("admin.$this->model.changedPassword");
   }
}
