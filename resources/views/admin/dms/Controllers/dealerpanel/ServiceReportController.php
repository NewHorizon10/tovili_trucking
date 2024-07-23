<?php 
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\User;
use App\Model\Booking;
use App\Model\ServiceReminderFollowUps;
use App\Model\RetailServices;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;

class ServiceReportController extends BaseController {
		
		public $model	=	'ServiceReport';

	public function __construct() {
		View::share('modelName',$this->model);
	}

	/*
	 * Function of showing service report listing 
	 */
	public function index(){
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
												->leftJoin('retail_services','retail_services.booking_id','=','booking.id')
												->where('booking.dealer_id',$dealer_id)
												->select('retail_services.*','retail_services.id AS retailId','booking.id as bookingId','booking.booking_number','booking.booking_date','booking.created_at','booking.updated_at','states.name as state_name','cities.name as city_name','inventories.motor_number','inventories.chassis_number','inventories.imei_number',
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
			Session::put("service_report_search_data",$inputGet);
			// dd($result);
			$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		
		return  View::make('dealerpanel.ServiceReport.index', compact('result' ,'searchVariable','sortBy','order','query_string','stateList','vehiclemodel'));
	}//End Index()
   
     /*
	 * Function for export services 
	 */
	public function exportServiceReportToExcel($id){
		$genderArr 			= 	Config::get("gender_type_array");
		$serviceType        =   Config::get("service_type_array");
		$searchData			=	Session::get('service_report_search_data');
        if($id!==''){
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
												->leftJoin('retail_services','retail_services.booking_id','=','booking.id')
												->where('booking.dealer_id',$dealer_id)
												->where('retail_services.booking_id',$id)
												->select('retail_services.*','retail_services.id AS retailId','booking.id as bookingId','booking.booking_number','booking.booking_date','booking.created_at','booking.updated_at','states.name as state_name','cities.name as city_name','inventories.motor_number','inventories.chassis_number','inventories.imei_number',
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_modal) as vehicle_modal"),
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_color) as vehicle_color"),
												DB::raw("(SELECT full_name FROM users WHERE id = booking.sales_consultant) as sales_consultant"),
												DB::raw("(SELECT full_name FROM users WHERE id = ".$dealer_id.") as dealer_name"))
												->get()->toArray();
												//dd($result);
         $thead = array();
		 $thead[]		= array("State","Dealer","City","Chassis No.","Motor No.","IMEI No.","Model","Colour","Invoice No.",	"Invoice Date",	"Retail Date",	"Updated By",	"Updated Date","Service In Days","Service In KM"," Service Type","1st Service","1st Service Avail Date ");
		 if(!empty($result)) {
			foreach($result as $key=>$record) {
                 if($key==0){ 
				
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
				$start_date = !empty($record['start_date'])? date(Config::get("Reading.date_format") , strtotime($record['start_date'])):'';

				$serviceNum  =  !empty($record['service_no'])?$record['service_no']:'';
				$service_no  =  $this->getSerialNumber($serviceNum);
                $service_days =!empty($record['service_days']) ? $record['service_days']:0;
				$service_km   = !empty($record['service_km']) ? $record['service_km']:0;
				
				$service_date = !empty($record['service_date'])? date(Config::get("Reading.date_format") , strtotime($record['service_date'])):'';
				if(array_key_exists($record['service_type'],$serviceType)){
					$service_type   =  $serviceType[$record['service_type']];
					if($service_type=="Paid"){
				       $service_type = $service_type.' '.$record['service_amount'].CURRENCY_SYMBOL;
				     }
				}else{
					    $service_type=   '';
				}

		          $thead[] = array($state_name,$dealer_name,$city_name,$chassis_number,$motor_number,$imei_number,$vehicle_modal,$vehicle_color,$booking_number,$booking_date,$retail_date,$updated_by,$updated_date,$service_days,$service_km,$service_type,$start_date,$service_date);

				
				}//key=0
				else{
					$lenght[] =   $this->getSerialNumber($record['service_no']);
					$serviceNo =  $this->getSerialNumber($record['service_no']);	
					array_push($thead[0],$serviceNo);
				    array_push($thead[0],$serviceNo." Avail Date");

				    $startDate = !empty($record['start_date'])? date(Config::get("Reading.date_format") , strtotime($record['start_date'])):'';
				    array_push($thead[1],$startDate);

				    $service_date = !empty($record['service_date'])? date(Config::get("Reading.date_format") , strtotime($record['service_date'])):'';
				    array_push($thead[1],$service_date);

				   
				}
               }//End Foreach
             }//empty result

               $count = count($lenght)*2;
             // echo '<pre>'; print_r($thead); die;
             return  View::make('dealerpanel.ServiceReport.serviceReport', compact('thead','count'));
          }
       } // End Export function

	
	public function exportServiceReportAllDataExcel(){
        $genderArr 			= 	Config::get("gender_type_array");
		$serviceType        =   Config::get("service_type_array");
		$searchData			=	Session::get('service_report_search_data'); 

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
												->leftJoin('retail_services','retail_services.booking_id','=','booking.id')
												->where('booking.dealer_id',$dealer_id)
												->select('retail_services.*','retail_services.id AS retailId','booking.id as bookingId','booking.booking_number','booking.booking_date','booking.created_at','booking.updated_at','states.name as state_name','cities.name as city_name','inventories.motor_number','inventories.chassis_number','inventories.imei_number',
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_modal) as vehicle_modal"),
												DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_color) as vehicle_color"),
												DB::raw("(SELECT full_name FROM users WHERE id = booking.sales_consultant) as sales_consultant"),
												DB::raw("(SELECT full_name FROM users WHERE id = ".$dealer_id.") as dealer_name"))
												->orderBy($sortBy, $order)
												->get()
												->toArray();
		$thead = array();
		 $thead[]		= array("State","Dealer","City","Chassis No.","Motor No.","IMEI No.","Model","Colour","Invoice No.",	"Invoice Date",	"Retail Date",	"Updated By",	"Updated Date","Service In Days","Service In KM"," Service Type","1st Service");
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
				$start_date = !empty($record['start_date'])? date(Config::get("Reading.date_format") , strtotime($record['start_date'])):'';

				$serviceNum  =  !empty($record['service_no'])?$record['service_no']:0;
				$service_no  =  $this->getSerialNumber($serviceNum);
                $service_days =!empty($record['service_days']) ? $record['service_days']:0;
				$service_km   = !empty($record['service_km']) ? $record['service_km']:0;

				if(array_key_exists($record['service_type'],$serviceType)){
					$service_type   =  $serviceType[$record['service_type']];
					if($service_type=="Paid"){
				       $service_type = $service_type.' '.$record['service_amount'].CURRENCY_SYMBOL;
				     }
				}else{
					    $service_type=   '';
				}
				
				$thead[] = array($state_name,$dealer_name,$city_name,$chassis_number,$motor_number,$imei_number,$vehicle_modal,$vehicle_color,$booking_number,$booking_date,$retail_date,$updated_by,$updated_date,$service_days,$service_km,$service_type,$start_date);
			}
		}
		//echo '<pre>'; print_r($thead); die;	
	  return  View::make('dealerpanel.ServiceReport.allServiceReport', compact('thead'));
		
	}//End Export Report 
	

  }//End Class
?>