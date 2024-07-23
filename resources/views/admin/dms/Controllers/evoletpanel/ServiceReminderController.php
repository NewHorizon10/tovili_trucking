<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\User;
use App\Model\Booking;
use App\Model\ServiceReminderFollowUps;
use App\Model\RetailServices;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* Sevice Reminder Controller
*
* Add your methods in the class below
*
* This file will render views\ServiceReminderController\dashboard
*/
	class ServiceReminderController extends BaseController {
			public $model	=	'ServiceReminder';

	   public function __construct() {
		   View::share('modelName',$this->model);
	   }
/**
* Sevice Reminder Controller
* 
* Index Funtion for listing
*/

       public function index() {
			$DB 					  = 	RetailServices::query();
			$searchVariable			=	array(); 
			$inputGet				=	Input::get();
			if ((Input::get())) {
				$searchData			=	Input::get();
				unset($searchData['display']);
				unset($searchData['_token']);
				if(isset($searchData['order'])){
					unset($searchData['order']);
				}
				if(isset($searchData['sortBy'])){
					unset($searchData['sortBy']);
				}
				if(isset($searchData['page'])){
					unset($searchData['page']);
				}
				foreach($searchData as $fieldName => $fieldValue){
					if($fieldValue != ""){
						if($fieldName == 'dealer_id'){
							$DB->where("booking.dealer_id",$fieldValue);
						}
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			if(!empty(input::get("full_name"))){
				$userinfo	=	input::get("full_name");
				$DB->where(function ($query) use($userinfo){
						$query->Orwhere("users.full_name","LIKE","%".$userinfo."%");
						$query->Orwhere("users.gender","LIKE","%".$userinfo."%");
						$query->Orwhere("users.email","LIKE","%".$userinfo."%");
						$query->Orwhere("users.phone_number","LIKE","%".$userinfo."%");
						$query->Orwhere("users.address_1","LIKE","%".$userinfo."%");
						$query->Orwhere("users.city","LIKE","%".$userinfo."%");
					});
			}
			
			if(!empty(input::get("chassis_number"))){
				$DB->where("inventories.chassis_number",input::get("chassis_number"));
			}
			if(!empty(input::get("motor_number"))){
				$DB->where("inventories.motor_number",input::get("motor_number"));
			}
			
			if(!empty(input::get("service_from"))){
				$DB->where("retail_services.service_date",">=",input::get("service_from"));
			}
			
			if(!empty(input::get("service_to"))){
				$DB->where("retail_services.service_date","<=",input::get("service_to"));
			}
			
			if(empty(input::get("service_from")) && empty(input::get("service_to"))){
				$DB->where(function ($query){
					$query->Orwhere(function ($query){
						$query->Orwhere("retail_services.service_date",date("Y-m-d"));
						$query->Orwhere("retail_services.fifteen_day_before_reminder_date",date("Y-m-d"));
						$query->Orwhere("retail_services.seven_day_before_reminder_date",date("Y-m-d"));
						$query->Orwhere("retail_services.third_day_before_reminder_date",date("Y-m-d"));
						$query->Orwhere("retail_services.one_day_before_reminder_date",date("Y-m-d"));
					});
					$query->Orwhere(function ($query){
						$query->where("retail_services.fifteen_day_before_reminder_date","<",date("Y-m-d"));
						$query->where('retail_services.is_close',0);
						$query->where("retail_services.last_followup_date","<",DB::raw("retail_services.fifteen_day_before_reminder_date"));
					});
					$query->Orwhere(function ($query){
						$query->where("retail_services.seven_day_before_reminder_date","<",date("Y-m-d"));
						$query->where('retail_services.is_close',0);
						$query->where("retail_services.last_followup_date","<",DB::raw("retail_services.seven_day_before_reminder_date"));
					});
					$query->Orwhere(function ($query){
						$query->where("retail_services.third_day_before_reminder_date","<",date("Y-m-d"));
						$query->where('retail_services.is_close',0);
						$query->where("retail_services.last_followup_date","<",DB::raw("retail_services.third_day_before_reminder_date"));
					});
					$query->Orwhere(function ($query){
						$query->where("retail_services.one_day_before_reminder_date","<",date("Y-m-d"));
						$query->where('retail_services.is_close',0);
						$query->where(function ($query){
							$query->Orwhere("retail_services.last_followup_date","<",DB::raw("retail_services.one_day_before_reminder_date"));
							$query->OrwhereNull("retail_services.last_followup_date");
						});
						
					});
				});
			}
			
			//$dealer_id				=	$this->get_dealer_id();
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'service_date';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
				$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
				if(!empty($assignedDealer)){
					$DB->whereIn('retail_services.dealer_id', $assignedDealer);
				}
			}
			$DB->leftJoin('booking','booking.id','=','retail_services.booking_id');
			$DB->leftJoin('inventories','inventories.id','=','booking.vehicle_id');
			$DB->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id');
			$DB->leftJoin('users','dealer_inventory.customer_id','=','users.id');
			$result 				= 	$DB
										//->where('users.dealer_id',$dealer_id)
										->where('retail_services.is_close',0)
										->where('retail_services.service_date','!=','')
										
										->select('retail_services.*','users.full_name','users.gender','users.email','users.dob','users.address_1','users.phone_number','inventories.model_id','inventories.color_id','inventories.vin_number','inventories.motor_number','inventories.chassis_number','inventories.imei_number','booking.created_at as invoice_date','booking.booking_number as invoice_id','booking.booking_date as booking_date', DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"),DB::raw("(SELECT full_name FROM users WHERE id = booking.dealer_id) as dealer_name"))
										->orderBy('retail_services.'.$sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));
							
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("service_reminder_report_search_data",$inputGet);
			
			$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
			$dealersList 		= 	$this->get_dealer_list();
       	    return  View::make('admin.'.$this->model.'.index',compact('result' ,'searchVariable','sortBy','order','query_string','stateList','vehiclemodel','dealersList'));
       }
	   
	   
	/**
	* ServiceReport Controller
	* 
	* Index Funtion for listing
	*/
		public function report_index() {
			$DB 					= 	Booking::query();
			$searchVariable			=	array(); 
			$inputGet				=	Input::get();
			if ((Input::get())) {
				$searchData			=	Input::get();
				unset($searchData['display']);
				unset($searchData['_token']);
				if(isset($searchData['order'])){
					unset($searchData['order']);
				}
				if(isset($searchData['sortBy'])){
					unset($searchData['sortBy']);
				}
				if(isset($searchData['page'])){
					unset($searchData['page']);
				}
				foreach($searchData as $fieldName => $fieldValue){
					if($fieldValue != ""){
						if($fieldName == 'dealer_id'){
							$DB->where("booking.dealer_id",$fieldValue);
						}
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			if(!empty(input::get("full_name"))){
				$userinfo	=	input::get("full_name");
				$DB->where(function ($query) use($userinfo){
						$query->Orwhere("users.full_name","LIKE","%".$userinfo."%");
						$query->Orwhere("users.gender","LIKE","%".$userinfo."%");
						$query->Orwhere("users.email","LIKE","%".$userinfo."%");
						$query->Orwhere("users.phone_number","LIKE","%".$userinfo."%");
						$query->Orwhere("users.address_1","LIKE","%".$userinfo."%");
						$query->Orwhere("users.city","LIKE","%".$userinfo."%");
					});
			}
			
			if(!empty(input::get("chassis_number"))){
				$DB->where("inventories.chassis_number",input::get("chassis_number"));
			}
			if(!empty(input::get("motor_number"))){
				$DB->where("inventories.motor_number",input::get("motor_number"));
			}
			
			if(!empty(input::get("service_from"))){
				$DB->where("retail_services.service_date",">=",input::get("service_from"));
			}
			
			if(!empty(input::get("service_to"))){
				$DB->where("retail_services.service_date","<=",input::get("service_to"));
			}
			
			//$dealer_id				=	$this->get_dealer_id();
			if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
				$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
				if(!empty($assignedDealer)){
					$DB->whereIn('retail_services.dealer_id', $assignedDealer);
				}
			}
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'retail_services.service_avail_date';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			
			$DB->leftJoin('retail_services','retail_services.booking_id','=','booking.id');
			$DB->leftJoin('inventories','inventories.id','=','booking.vehicle_id');
			$DB->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id');
			$DB->leftJoin('users','dealer_inventory.customer_id','=','users.id');
			$result 				= 	$DB
										//->where('users.dealer_id',$dealer_id)
										->where('retail_services.is_close',1)
										->where('retail_services.service_avail_date','!=','')
										
										->select('booking.id','users.full_name','users.gender','users.email','users.dob','users.address_1','users.phone_number','inventories.model_id','inventories.color_id','inventories.vin_number','inventories.motor_number','inventories.chassis_number','inventories.imei_number','booking.created_at as invoice_date','booking.booking_number as invoice_id','booking.booking_date as booking_date', DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"),DB::raw("(SELECT full_name FROM users WHERE id = booking.dealer_id) as dealer_name"))
										->groupBy("booking.id")
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));
										
			if(!$result->isEmpty()){
				foreach($result as &$record){
					$record->service_lists	=	DB::table("retail_services")->where("booking_id",$record->id)->get()->toArray();
				}
			}
							
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("service_reminder_report_search_data_report",$inputGet);
			
			$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
			
			$totalHighServices	=	DB::select(DB::raw("SELECT count(id) as recordcount  FROM `model_services` group by dropdown_manager_id  ORDER BY recordcount DESc limit 1"));
			$totalHighServices	=	(!empty($totalHighServices[0]->recordcount)) ? $totalHighServices[0]->recordcount : 6;
			$dealersList 		= 	$this->get_dealer_list();
			return  View::make('admin.'.$this->model.'.report_index',compact('result' ,'searchVariable','sortBy','order','query_string','stateList','vehiclemodel','totalHighServices','dealersList'));
	   }
	   
	   public function export_report_index() {
			$DB 					= 	Booking::query();
			$searchVariable			=	array(); 
			$genderArr 			= 	Config::get("gender_type_array");
			$serviceType        =   Config::get("service_type_array");
			$searchData			=	Session::get('service_reminder_report_search_data_report');

			if (($searchData)) {
				$searchData			=	$searchData;
				unset($searchData['display']);
				unset($searchData['_token']);
				if(isset($searchData['order'])){
					unset($searchData['order']);
				}
				if(isset($searchData['sortBy'])){
					unset($searchData['sortBy']);
				}
				if(isset($searchData['page'])){
					unset($searchData['page']);
				}
				foreach($searchData as $fieldName => $fieldValue){
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			if(!empty($searchData["full_name"])){
				$userinfo	=	$searchData["full_name"];
				$DB->where(function ($query) use($userinfo){
						$query->Orwhere("users.full_name","LIKE","%".$userinfo."%");
						$query->Orwhere("users.gender","LIKE","%".$userinfo."%");
						$query->Orwhere("users.email","LIKE","%".$userinfo."%");
						$query->Orwhere("users.phone_number","LIKE","%".$userinfo."%");
						$query->Orwhere("users.address_1","LIKE","%".$userinfo."%");
						$query->Orwhere("users.city","LIKE","%".$userinfo."%");
					});
			}
			
			if(!empty($searchData["chassis_number"])){
				$DB->where("inventories.chassis_number",$searchData["chassis_number"]);
			}
			if(!empty($searchData["motor_number"])){
				$DB->where("inventories.motor_number",$searchData["motor_number"]);
			}
			
			if(!empty($searchData["service_from"])){
				$DB->where("retail_services.service_avail_date",">=",$searchData["service_from"]);
			}
			
			if(!empty($searchData["service_to"])){
				$DB->where("retail_services.service_avail_date","<=",$searchData["service_to"]);
			}
			
			$dealer_id				=	$this->get_dealer_id();
			if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
				$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
				if(!empty($assignedDealer)){
					$DB->whereIn('retail_services.dealer_id', $assignedDealer);
				}
			}
			$sortBy 				= 	'retail_services.service_avail_date';
			$order  				= 	'DESC';
			
			$DB->leftJoin('retail_services','retail_services.booking_id','=','booking.id');
			$DB->leftJoin('inventories','inventories.id','=','booking.vehicle_id');
			$DB->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id');
			$DB->leftJoin('users','dealer_inventory.customer_id','=','users.id');
			$result 				= 	$DB
										//->where('users.dealer_id',$dealer_id)
										->where('retail_services.is_close',1)
										->where('retail_services.service_avail_date','!=','')
										
										->select('booking.id','users.full_name','users.gender','users.email','users.dob','users.address_1','users.phone_number','inventories.model_id','inventories.color_id','inventories.vin_number','inventories.motor_number','inventories.chassis_number','inventories.imei_number','booking.created_at as invoice_date','booking.booking_number as invoice_id','booking.booking_date as booking_date', DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))
										->groupBy("booking.id")
										->orderBy($sortBy, $order)
										->get()->toArray();
										
			if(!empty($result)){
				foreach($result as &$record){
					$record["service_lists"]	=	DB::table("retail_services")->where("booking_id",$record["id"])->get()->toArray();
				}
			}
						
			$totalHighServices	=	DB::select(DB::raw("SELECT count(id) as recordcount  FROM `model_services` group by dropdown_manager_id  ORDER BY recordcount DESc limit 1"));
			$totalHighServices	=	(!empty($totalHighServices[0]->recordcount)) ? $totalHighServices[0]->recordcount : 6;
			return  View::make('admin.'.$this->model.'.export_report_index',compact('result' ,'searchVariable','sortBy','order','query_string','stateList','vehiclemodel','totalHighServices'));
	   }
	   
     /* 
      *  Function for export list 
      *  of Service Reminder
      *
      *
	  */

       public function exportServiceReminderReportToExcel(){
		$genderArr 			= 	Config::get("gender_type_array");
		$serviceType        =   Config::get("service_type_array");
		$searchData			=	Session::get('service_reminder_report_search_data');

		$DB 					  = 	RetailServices::query();
		$searchVariable			=	array(); 
		if (($searchData)) {
			unset($searchData['display']);
			unset($searchData['_token']);
			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
		}
		if(!empty($searchData["full_name"])){
			$userinfo	=	$searchData["full_name"];
			$DB->where(function ($query) use($userinfo){
					$query->Orwhere("users.full_name","LIKE","%".$userinfo."%");
					$query->Orwhere("users.gender","LIKE","%".$userinfo."%");
					$query->Orwhere("users.email","LIKE","%".$userinfo."%");
					$query->Orwhere("users.phone_number","LIKE","%".$userinfo."%");
					$query->Orwhere("users.address_1","LIKE","%".$userinfo."%");
					$query->Orwhere("users.city","LIKE","%".$userinfo."%");
				});
		}
		
		if(!empty($searchData["chassis_number"])){
			$DB->where("inventories.chassis_number",$searchData["chassis_number"]);
		}
		if(!empty($searchData["motor_number"])){
			$DB->where("inventories.motor_number",$searchData["chassis_number"]);
		}
		
		if(!empty($searchData["service_from"])){
			$DB->where("retail_services.service_date",">=",$searchData["service_from"]);
		}
		
		if(!empty($searchData["service_to"])){
			$DB->where("retail_services.service_date","<=",$searchData["service_to"]);
		}
		
		if(empty($searchData["service_from"]) && empty($searchData["service_to"])){
			$DB->where(function ($query){
				$query->Orwhere(function ($query){
					$query->Orwhere("retail_services.service_date",date("Y-m-d"));
					$query->Orwhere("retail_services.fifteen_day_before_reminder_date",date("Y-m-d"));
					$query->Orwhere("retail_services.seven_day_before_reminder_date",date("Y-m-d"));
					$query->Orwhere("retail_services.third_day_before_reminder_date",date("Y-m-d"));
					$query->Orwhere("retail_services.one_day_before_reminder_date",date("Y-m-d"));
				});
				$query->Orwhere(function ($query){
					$query->where("retail_services.fifteen_day_before_reminder_date","<",date("Y-m-d"));
					$query->where('retail_services.is_close',0);
					$query->where("retail_services.last_followup_date","<",DB::raw("retail_services.fifteen_day_before_reminder_date"));
				});
				$query->Orwhere(function ($query){
					$query->where("retail_services.seven_day_before_reminder_date","<",date("Y-m-d"));
					$query->where('retail_services.is_close',0);
					$query->where("retail_services.last_followup_date","<",DB::raw("retail_services.seven_day_before_reminder_date"));
				});
				$query->Orwhere(function ($query){
					$query->where("retail_services.third_day_before_reminder_date","<",date("Y-m-d"));
					$query->where('retail_services.is_close',0);
					$query->where("retail_services.last_followup_date","<",DB::raw("retail_services.third_day_before_reminder_date"));
				});
				$query->Orwhere(function ($query){
					$query->where("retail_services.one_day_before_reminder_date","<",date("Y-m-d"));
					$query->where('retail_services.is_close',0);
					$query->where(function ($query){
						$query->Orwhere("retail_services.last_followup_date","<",DB::raw("retail_services.one_day_before_reminder_date"));
						$query->OrwhereNull("retail_services.last_followup_date");
					});
					
				});
			});
		}
		
		//$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	'service_date';
		$order  				= 	'DESC';
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if(!empty($assignedDealer)){
				$DB->whereIn('retail_services.dealer_id', $assignedDealer);
			}
		}
		$DB->leftJoin('booking','booking.id','=','retail_services.booking_id');
		$DB->leftJoin('inventories','inventories.id','=','booking.vehicle_id');
		$DB->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id');
		$DB->leftJoin('users','dealer_inventory.customer_id','=','users.id');
		$result 				= 	$DB
									//->where('users.dealer_id',$dealer_id)
									->where('retail_services.is_close',0)
									->where('retail_services.service_date','!=','')
									
									->select('retail_services.*','users.full_name','users.gender','users.email','users.dob','users.address_1','users.phone_number','inventories.model_id','inventories.color_id','inventories.vin_number','inventories.motor_number','inventories.chassis_number','inventories.imei_number','booking.created_at as invoice_date','booking.booking_number as invoice_id','booking.booking_date as booking_date', DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))
									->orderBy('retail_services.'.$sortBy, $order)
									->get()->toArray();
									
		return  View::make('admin.ServiceReminder.serviceReminderReport', compact('result' ,'searchVariable','sortBy','order','query_string','stateList','vehiclemodel'));
	 }//End exportServiceReminderReportToExcel

	

     /* 
      *  Function for follow up 
      *  
      *
      *
	  */
     public function followUpService($id){
     	$retailServices	 =	RetailServices::where('id',$id)->first(); 
		
		if(empty($retailServices)) {
			return Redirect::back();
		}else{
			
			$followUpDetails = DB::table('service_reminder_followups')
								 ->leftJoin('retail_services','retail_services.id','=','service_reminder_followups.retail_service_id')
								  ->leftJoin('users','users.id','=','service_reminder_followups.user_id')
								  ->select('service_reminder_followups.*','users.full_name as fullname')
								->where('service_reminder_followups.retail_service_id',$id)->get();
			
			return view('admin.ServiceReminder.followUp',compact("retailServices","followUpDetails"));
		}
     	

     }// End followUpService

     /* 
      *  Function for Add follow up 
      *  
      *
      *
	  */
  
    public function addFollowUpService(){
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'retail_service_id' 				=>	 'required',
					'detail' 					=>	 'required',
					),
				array(
					"detail.required"					=>	trans("The remarks field is required"),
				)
			);
			if ($validator->fails()) {	
				$response				=	array(
					'success' 			=> 	2,
					'errors' 			=> 	$validator->errors()
				);
				return Response::json($response); 
				die;;
			}else{
				$followUpObj 						= new ServiceReminderFollowUps;
				$followUpObj->user_id 				= Auth::user()->id;
				$followUpObj->retail_service_id     =Input::get('retail_service_id');
				$followUpObj->next_follow_up_date   = '0000-00-00';
				$followUpObj->detail 				= Input::get('detail');
				$followUpObj->save();
				
				RetailServices::where('id',Input::get('retail_service_id'))->update(array('last_followup_date'=>date("Y-m-d")));
				
				$response				=	array(
					'success' 			=> 	1,
					'errors' 			=> 	'',
				);
				Session::flash('flash_notice',trans("Follow up added successfully.")); 
				return Response::json($response); 
				die;
		 	}
		 }
		}// end function addFollowUp()

		/*
         * Function for close service
         *
         *
		 */
		public function closeService(){
		  $id = Input::get('id');
		  $service_by   = Input::get('service_by');
		  $service_date = Input::get('service_date');
		  $retail_service_id   = Input::get('id');
		  if($retail_service_id==''){
		  	return Redirect::back();
		  }else{
				$serviceDetails	=	DB::table("retail_services")->where("id",$retail_service_id)->first();
				if(!empty($serviceDetails)){
					RetailServices::where('id',$serviceDetails->id)->update(array('is_close'=>1,'close_by'=>Auth::user()->id,'close_on'=>date("Y-m-d"),"service_avail_date"=>$service_date,"service_by"=>$service_by));
			  
					$details =RetailServices::where("booking_id",$serviceDetails->booking_id)->where("service_no",">",$serviceDetails->service_no)->where("is_close",0)->orderBy("id","ASC")->first();
					if(!empty($details)){
						$days	=	$details->service_days;
						$service_date = date('Y-m-d', strtotime($service_date. " + $days day"));

						$service_date     =   $service_date;
						$fifteen_day_before_reminder_date =  date("Y-m-d",strtotime($service_date."-15 day"));
						$seven_day_before_reminder_date   = date("Y-m-d",strtotime($service_date."-7 day"));
						$third_day_before_reminder_date   = date("Y-m-d",strtotime($service_date."-3 day"));
						$one_day_before_reminder_date   = date("Y-m-d",strtotime($service_date."-1 day"));
						
						RetailServices::where('id',$details->id)->update(array('service_date'=>$service_date,'fifteen_day_before_reminder_date'=>$fifteen_day_before_reminder_date,'seven_day_before_reminder_date'=>$seven_day_before_reminder_date,'third_day_before_reminder_date'=>$third_day_before_reminder_date,'one_day_before_reminder_date'=>$one_day_before_reminder_date));
					}
					$response				=	array(
						'success' 			=> 	1,
						'errors' 			=> 	'',
					);
					Session::flash('flash_notice',trans("Service has successfully Closed.")); 
					return Response::json($response); 
					die;
				}else{
					$response				=	array(
						'success' 			=> 	1,
						'errors' 			=> 	'',
					);
					Session::flash('flash_notice',trans("Something went wrong.")); 
					return Response::json($response); 
					die;
				}
				
				
		  }

		}// end function closeService()
       

}//End class