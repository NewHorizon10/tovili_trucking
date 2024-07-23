<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\AdvanceBooking;
use App\Model\DropDown;
use App\Model\EnquiryFollowUp;
use App\Model\Booking;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* EnquiryController Controller
*
* Add your methods in the class below
*
* This file will render views\EnquiryController\dashboard
*/
	class RetailReportController extends BaseController {
		
		public $model	=	'enquiry';

	public function __construct() {
		View::share('modelName',$this->model);
	}
	/* Retail Report start*/	
	public function customerDetailReport(){
		$DB 					= 	User::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
			if ((Input::get())) {
				$searchData			=	Input::get();
				//echo'<pre>'; print_r($searchData); echo'</pre>'; die;
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
						if($fieldName == 'booking_date_start' || $fieldName == 'booking_date_end'){
							if($fieldName == 'booking_date_start'){  
								$DB->where('users.booking_date','>=',$fieldValue);
							}
							if($fieldName == 'booking_date_end'){  
								$DB->where('users.booking_date','<=',$fieldValue);
							}
						}else{
							$DB->where("$fieldName",'like','%'.$fieldValue.'%');
						}
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			$dealer_id				=	$this->get_dealer_id();
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB
										->leftJoin('dealer_inventory','dealer_inventory.customer_id','=','users.id')
										->leftJoin('inventories','inventories.id','=','dealer_inventory.vehicle_id')
										->leftJoin('booking','booking.vehicle_id','=','dealer_inventory.vehicle_id')
										->where('users.is_deleted',0)
										->where('users.dealer_id',$dealer_id)
										->where('users.user_role_id',CUSTOMER_ROLE_ID)
										->select('users.id','users.full_name','users.gender','users.email','users.dob','users.address_1','users.phone_number','inventories.model_id','inventories.color_id','inventories.vin_number','inventories.motor_number','inventories.chassis_number','inventories.imei_number','booking.created_at as invoice_date','booking.booking_number as invoice_id','booking.booking_date as booking_date', DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))
										->orderBy('users.'.$sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));
							
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("customer_detail_report_search_data",$inputGet);
			
			$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		
		return  View::make('dealerpanel.RetailReport.customerDetailReport', compact('result' ,'searchVariable','sortBy','order','query_string','stateList','vehiclemodel'));
	}
	
	
	public function exportCustomerDetailReportToExcel(){
		$genderArr 			= 	Config::get("gender_type_array");
		$searchData			=	Session::get('customer_detail_report_search_data');
		$DB 					= 	User::query();
		$searchVariable			=	array(); 
		if ($searchData) {
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
					if($fieldName == 'booking_date_start' || $fieldName == 'booking_date_end'){
						if($fieldName == 'booking_date_start'){  
							$DB->where('users.booking_date','>=',$fieldValue);
						}
						if($fieldName == 'booking_date_end'){  
							$DB->where('users.booking_date','<=',$fieldValue);
						}
					}else{
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';						
		$result 				= 	$DB
										->leftJoin('dealer_inventory','dealer_inventory.customer_id','=','users.id')
										->leftJoin('inventories','inventories.id','=','dealer_inventory.vehicle_id')
										->leftJoin('booking','booking.vehicle_id','=','dealer_inventory.vehicle_id')
										->where('users.is_deleted',0)
										->where('users.dealer_id',$dealer_id)
										->where('users.user_role_id',CUSTOMER_ROLE_ID)
										->select('users.id','users.full_name','users.gender','users.email','users.dob','users.address_1','users.phone_number','inventories.model_id','inventories.color_id','inventories.vin_number','inventories.motor_number','inventories.chassis_number','inventories.imei_number','booking.created_at as invoice_date','booking.booking_number as invoice_id','booking.booking_date as booking_date', DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))
										->orderBy('users.'.$sortBy, $order)
										->get()->toArray();									
		$thead = array();
		$thead[]		= array("Chassis No.","Motor No.","Model","Colour","Retail Date","Customer Name","Gender","Invoice No.","Invoice Date","Date of Birth","Address","City","Mobile No.","Email Address");
		if(!empty($result)) {
			foreach($result as $record) {
				
				$chassis_number							=	!empty($record['chassis_number'])?$record['chassis_number']:'';
				$motor_number							=	!empty($record['motor_number'])?$record['motor_number']:'';
				$vehicle_modal							=	!empty($record['vehicle_modal'])?$record['vehicle_modal']:'';
				$vehicle_color							=	!empty($record['vehicle_color'])?$record['vehicle_color']:'';
				
				$booking_date							=	!empty($record['booking_date'])? date(Config::get("Reading.date_format") , strtotime($record['booking_date'])):'';
				$full_name								=	!empty($record['full_name'])?$record['full_name']:'';
				if(array_key_exists($record['gender'],$genderArr)){
					$gender = $genderArr[$record['gender']];
				}else{
					$gender = '';
				}
				$invoice_id							=	!empty($record['invoice_id'])?$record['invoice_id']:'';
				$invoice_date						=	!empty($record['invoice_date'])? date(Config::get("Reading.date_format") , strtotime($record['invoice_date'])):'';
				$dob								=	!empty($record['dob'])? date(Config::get("Reading.date_format") , strtotime($record['dob'])):'';
				
				$address_1									=	!empty($record['address_1'])?$record['address_1']:'';
				$city									=	!empty($record['city'])?$record['city']:'';
				$phone_number									=	!empty($record['phone_number'])?$record['phone_number']:'';
				$email									=	!empty($record['email'])?$record['email']:'';
				
				
				
				
				$thead[]						= 	array($chassis_number,$motor_number,$vehicle_modal,$vehicle_color,$booking_date,$full_name,$gender,$invoice_id,$invoice_date,$dob,$address_1,$city,$phone_number,$email);
			}
		}								
		// echo '<pre>'; print_r($thead); die;					
		return  View::make('dealerpanel.RetailReport.customerDetailReport_export_excel', compact('thead'));
		
	}
	
	/* Customer Enquiry Report end*/	
	/* Retail Report starts*/
	public function retailReport(){
		$DB 					= 	Booking::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
			if ((Input::get())) {
				$inputGet			=	Input::get();
				unset($inputGet['display']);
				unset($inputGet['_token']);
				if(isset($inputGet['order'])){
					unset($inputGet['order']);
				}
				if(isset($inputGet['sortBy'])){
					unset($inputGet['sortBy']);
				}
				if(isset($inputGet['page'])){
					unset($inputGet['page']);
				}
				if(!empty($inputGet['retail_start_date']) && empty($inputGet['retail_end_date'])){
					$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
					$DB->whereBetween('booking.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
					$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
				}elseif(empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
					$date_from	=	date("Y-m-d",strtotime($inputGet['retail_end_date']));
					$DB->whereBetween('booking.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
					$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
				}elseif(!empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
					$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
					$date_to	=	date('Y-m-d',strtotime($inputGet['retail_end_date']));
					$DB->whereBetween('booking.created_at',[$date_from." 00:00:00",$date_to." 23:59:59"]);
					$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date'],'retail_end_date' => $inputGet['retail_end_date']));
				}
			}
			$dealer_id				=	$this->get_dealer_id();
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'booking.created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB->where("booking.is_deleted",0)
												->leftjoin('inventories', 'booking.vehicle_id','=','inventories.id')
												->leftjoin('states','booking.state', '=', 'states.id')
												->leftjoin('cities','booking.city', '=', 'cities.id')
												->where('dealer_id',$dealer_id)
												->select('booking.booking_number','booking.booking_date','booking.created_at','booking.updated_at','states.name as state_name','cities.name as city_name','inventories.motor_number','inventories.chassis_number','inventories.imei_number',
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_modal) as vehicle_modal"),
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_color) as vehicle_color"),
												DB::raw("(SELECT full_name FROM users WHERE id = booking.sales_consultant) as sales_consultant"),
												DB::raw("(SELECT full_name FROM users WHERE id = ".$dealer_id.") as dealer_name"))
												->orderBy($sortBy, $order)
												->paginate(Config::get("Reading.records_per_page"));
			// echo "<pre>";print_r($result);die;				
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("retail_report_search_data",Input::get());		
		return  View::make('dealerpanel.RetailReport.retailReport', compact('result' ,'searchVariable','sortBy','order','query_string'));
	}

	public function exportRetailReportToExcel(){
		$inputGet				=	Session::get('retail_report_search_data');
		$DB 					= 	Booking::query();
		$searchVariable			=	array(); 
			if ($inputGet) {
				unset($inputGet['display']);
				unset($inputGet['_token']);
				if(isset($inputGet['order'])){
					unset($inputGet['order']);
				}
				if(isset($inputGet['sortBy'])){
					unset($inputGet['sortBy']);
				}
				if(isset($inputGet['page'])){
					unset($inputGet['page']);
				}
				if(!empty($inputGet['retail_start_date']) && empty($inputGet['retail_end_date'])){
					$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
					$DB->whereBetween('booking.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
					$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
				}elseif(empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
					$date_from	=	date("Y-m-d",strtotime($inputGet['retail_end_date']));
					$DB->whereBetween('booking.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
					$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
				}elseif(!empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
					$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
					$date_to	=	date('Y-m-d',strtotime($inputGet['retail_end_date']));
					$DB->whereBetween('booking.created_at',[$date_from." 00:00:00",$date_to." 23:59:59"]);
					$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date'],'retail_end_date' => $inputGet['retail_end_date']));
				}
			}
			$dealer_id				=	$this->get_dealer_id();
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'booking.created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB->where("booking.is_deleted",0)
												->leftjoin('inventories', 'booking.vehicle_id','=','inventories.id')
												->leftjoin('states','booking.state', '=', 'states.id')
												->leftjoin('cities','booking.city', '=', 'cities.id')
												->where('dealer_id',$dealer_id)
												->select('booking.booking_number','booking.booking_date','booking.created_at','booking.updated_at','states.name as state_name','cities.name as city_name','inventories.motor_number','inventories.chassis_number','inventories.imei_number',
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_modal) as vehicle_modal"),
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_color) as vehicle_color"),
												DB::raw("(SELECT full_name FROM users WHERE id = booking.sales_consultant) as sales_consultant"),
												DB::raw("(SELECT full_name FROM users WHERE id = ".$dealer_id.") as dealer_name"))
												->orderBy($sortBy, $order)
												->get()
												->toArray();
		$thead = array();
		$thead[]		= array("State","Dealer","City","Chassis No.","Motor No.","IMEI No.","Model","Colour","Invoice No.",	"Invoice Date",	"Retail Date",	"Updated By",	"Updated Date");
		if(!empty($result)) {
			foreach($result as $record) {
				
				$state_name								=	!empty($record['state_name'])?$record['state_name']:'';
				$dealer_name							=	!empty($record['dealer_name'])?$record['dealer_name']:'';
				$city_name								=	!empty($record['city_name'])?$record['city_name']:'';
				$chassis_number							=	!empty($record['chassis_number'])?$record['chassis_number']:'';
				$motor_number							=	!empty($record['motor_number'])?$record['motor_number']:'';
				$imei_number							=	!empty($record['imei_number'])?$record['imei_number']:'';
				$vehicle_modal							=	!empty($record['vehicle_modal'])?$record['vehicle_modal']:'';
				$vehicle_color							=	!empty($record['vehicle_color'])?$record['vehicle_color']:'';
				$booking_number							=	!empty($record['booking_number'])?$record['booking_number']:'';
				$booking_date							=	!empty($record['booking_date'])? date(Config::get("Reading.date_format") , strtotime($record['booking_date'])):'';
				$retail_date							=	!empty($record['created_at'])? date(Config::get("Reading.date_format") , strtotime($record['created_at'])):'';
				$updated_by								=	!empty($record['sales_consultant'])?$record['sales_consultant']:'';
				$updated_date							=	!empty($record['updated_at'])? date(Config::get("Reading.date_format") , strtotime($record['updated_at'])):'';	
				$thead[]						= 	array($state_name,$dealer_name,$city_name,$chassis_number,$motor_number,$imei_number,$vehicle_modal,$vehicle_color,$booking_number,$booking_date,$retail_date,$updated_by,$updated_date);
			}
		}	
		// Session::forget('retail_report_search_data');											
		return  View::make('dealerpanel.RetailReport.retailReport_excel_export', compact('thead'));
	}

	/* Search Chassis Report*/
	public function searchChassisReport(){
		$DB 					= 	Booking::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
			if ((Input::get())) {
				$inputGet			=	Input::get();
				unset($inputGet['display']);
				unset($inputGet['_token']);
				if(isset($inputGet['order'])){
					unset($inputGet['order']);
				}
				if(isset($inputGet['sortBy'])){
					unset($inputGet['sortBy']);
				}
				if(isset($inputGet['page'])){
					unset($inputGet['page']);
				}
				if(!empty($inputGet['retail_start_date']) && empty($inputGet['retail_end_date'])){
					$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
					$DB->whereBetween('booking.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
					$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
				}elseif(empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
					$date_from	=	date("Y-m-d",strtotime($inputGet['retail_end_date']));
					$DB->whereBetween('booking.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
					$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
				}elseif(!empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
					$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
					$date_to	=	date('Y-m-d',strtotime($inputGet['retail_end_date']));
					$DB->whereBetween('booking.created_at',[$date_from." 00:00:00",$date_to." 23:59:59"]);
					$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date'],'retail_end_date' => $inputGet['retail_end_date']));
				}
				unset($inputGet['retail_start_date']);
				unset($inputGet['retail_end_date']);
				foreach($inputGet as $fieldName => $fieldValue){
					if($fieldValue != ""){
						if($fieldName == 'unique_id' && $fieldValue != ''){
							$DB->where("users.$fieldName",'like','%'.$fieldValue.'%');							
						}elseif(($fieldName == 'chassis_number' || $fieldName == 'motor_number') && $fieldValue != ''){
							$DB->where("inventories.$fieldName",'like','%'.$fieldValue.'%');
						}else{
							$DB->where("booking.$fieldName",'like','%'.$fieldValue.'%');
						}
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}

			}
			$dealer_id				=	$this->get_dealer_id();
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'booking.created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB->where("booking.is_deleted",0)
												->leftjoin('inventories', 'booking.vehicle_id','=','inventories.id')
												->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id')
												->leftjoin('users', 'users.id','=','dealer_inventory.customer_id')
												->leftjoin('states','booking.state', '=', 'states.id')
												->leftjoin('cities','booking.city', '=', 'cities.id')
												->where('booking.dealer_id',$dealer_id)
												->select('booking.booking_number','booking.booking_date','booking.created_at','booking.updated_at','states.name as state_name','cities.name as city_name','inventories.motor_number','inventories.chassis_number','inventories.imei_number','users.unique_id','booking.customer_name','booking.mobile_number','booking.vehicle_id',
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_modal) as vehicle_modal"),
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_color) as vehicle_color"),
												DB::raw("(SELECT full_name FROM users WHERE id = booking.sales_consultant) as sales_consultant"),
												DB::raw("(SELECT full_name FROM users WHERE id = ".$dealer_id.") as dealer_name"),DB::raw("(SELECT location_name FROM dealer_location WHERE id = booking.location_name) as location_name"))
												->orderBy($sortBy, $order)
												->paginate(Config::get("Reading.records_per_page"));
			foreach($result as &$booking){
				$booking->battery_details	=	DB::table('battery_details')
															->where('vehicle_id',$booking->vehicle_id)
															->pluck('battery_number')->toArray();
				$booking->battery_count		=	count($booking->battery_details);
			}	
			// echo "<pre>";print_r($result);die;
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("search_chassis_data",Input::get());	
			$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');

		return  View::make('dealerpanel.RetailReport.search_chassis_report', compact('result' ,'searchVariable','sortBy','order','query_string','vehiclemodel'));
	}

	public function exportsearchChassisReportToExcel(){
		$inputGet				=	Session::get('search_chassis_data');
		$DB 					= 	Booking::query();
		$searchVariable			=	array(); 
		if ($inputGet) {
			unset($inputGet['display']);
			unset($inputGet['_token']);
			if(isset($inputGet['order'])){
				unset($inputGet['order']);
			}
			if(isset($inputGet['sortBy'])){
				unset($inputGet['sortBy']);
			}
			if(isset($inputGet['page'])){
				unset($inputGet['page']);
			}
			if(!empty($inputGet['retail_start_date']) && empty($inputGet['retail_end_date'])){
				$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
				$DB->whereBetween('booking.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
				$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
			}elseif(empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
				$date_from	=	date("Y-m-d",strtotime($inputGet['retail_end_date']));
				$DB->whereBetween('booking.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
				$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
			}elseif(!empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
				$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
				$date_to	=	date('Y-m-d',strtotime($inputGet['retail_end_date']));
				$DB->whereBetween('booking.created_at',[$date_from." 00:00:00",$date_to." 23:59:59"]);
				$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date'],'retail_end_date' => $inputGet['retail_end_date']));
			}
			unset($inputGet['retail_start_date']);
			unset($inputGet['retail_end_date']);
			foreach($inputGet as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == 'unique_id' && $fieldValue != ''){
						$DB->where("users.$fieldName",'like','%'.$fieldValue.'%');							
					}elseif(($fieldName == 'chassis_number' || $fieldName == 'motor_number') && $fieldValue != ''){
						$DB->where("inventories.$fieldName",'like','%'.$fieldValue.'%');
					}else{
						$DB->where("booking.$fieldName",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}

		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'booking.created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB->where("booking.is_deleted",0)
											->leftjoin('inventories', 'booking.vehicle_id','=','inventories.id')
											->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id')
											->leftjoin('users', 'users.id','=','dealer_inventory.customer_id')
											->leftjoin('states','booking.state', '=', 'states.id')
											->leftjoin('cities','booking.city', '=', 'cities.id')
											->where('booking.dealer_id',$dealer_id)
											->select('booking.booking_number','booking.booking_date','booking.created_at','booking.updated_at','states.name as state_name','cities.name as city_name','inventories.motor_number','inventories.chassis_number','inventories.imei_number','users.unique_id','booking.customer_name','booking.mobile_number','booking.vehicle_id',
											DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_modal) as vehicle_modal"),
											DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_color) as vehicle_color"),
											DB::raw("(SELECT full_name FROM users WHERE id = booking.sales_consultant) as sales_consultant"),
											DB::raw("(SELECT full_name FROM users WHERE id = ".$dealer_id.") as dealer_name"),DB::raw("(SELECT location_name FROM dealer_location WHERE id = booking.location_name) as location_name"))
											->orderBy($sortBy, $order)
											->get()
											->toArray();
		foreach($result as &$booking){
			$booking['battery_details']	=	DB::table('battery_details')
														->where('vehicle_id',$booking['vehicle_id'])
														->pluck('battery_number')->toArray();
			$booking['battery_count']		=	count($booking['battery_details']);
		}
		$thead = array();
		$thead[]		= array("Chassis No.","Motor No.","IMEI No.","Battery No. 1","Battery No. 2","Battery No. 3","Battery No. 4","Battery No. 5","Battery No. 6","Model","Colour","Invoice No.",	"Invoice Date",	"Retail Date",	"Customer Unique ID",	"Customer Name","Location","Mobile No.");
		if(!empty($result)) {
			foreach($result as $record) {
				
				$chassis_number								=	!empty($record['chassis_number'])?$record['chassis_number']:'';
				$motor_number							=	!empty($record['motor_number'])?$record['motor_number']:'';
				$imei_number								=	!empty($record['imei_number'])?$record['imei_number']:'';
				 $j = 0;
				 $bat_arr	=	array();
				foreach($record['battery_details'] as $key=>$battery){
					$bat_arr[$j]	=	$battery;
					$j++;
				}
				for($i=6; $j<$i;$j++){
					$bat_arr[$j]	=	'';
				}
				// echo "<pre>";print_r($bat_arr);die;

				$vehicle_modal							=	!empty($record['vehicle_modal'])?$record['vehicle_modal']:'';
				$vehicle_color							=	!empty($record['vehicle_color'])?$record['vehicle_color']:'';
				$booking_number							=	!empty($record['booking_number'])?$record['booking_number']:'';
				$booking_date							=	!empty($record['booking_date'])? date(Config::get("Reading.date_format") , strtotime($record['booking_date'])):'';
				$retail_date							=	!empty($record['created_at'])? date(Config::get("Reading.date_format") , strtotime($record['created_at'])):'';
				$customer_id							=	!empty($record['unique_id'])?$record['unique_id']:'';
				$customer_name							=	!empty($record['customer_name'])?$record['customer_name']:'';
				$location_name							=	!empty($record['location_name'])?$record['location_name']:'';
				$mobile_number							=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$thead[]						= 	array($chassis_number,$motor_number,$imei_number,$bat_arr[0],$bat_arr[1],$bat_arr[2],$bat_arr[3],$bat_arr[4],$bat_arr[5],$vehicle_modal,$vehicle_color,$booking_number,$booking_date,$retail_date,$customer_id,$customer_name,$location_name,$mobile_number);
			}
		}	
				// echo "<pre>";print_r($thead);die;

		// Session::forget('retail_report_search_data');											
		return  View::make('dealerpanel.RetailReport.search_chassis_report_excel_export', compact('thead'));
	}
	
	
	
	
	
	
} //end AdvanceBookingReportController()
