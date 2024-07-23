<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\Enquiry;
use App\Model\DropDown;
use App\Model\EnquiryFollowUp;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* EnquiryController Controller
*
* Add your methods in the class below
*
* This file will render views\EnquiryController\dashboard
*/
	class EnquiryReportController extends BaseController {
		
		public $model	=	'enquiry';

	public function __construct() {
		View::share('modelName',$this->model);
	}
	/* Customer Enquiry Report start*/	
	public function customerEnquiryReport(){
		$DB 					= 	Enquiry::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
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
					if($fieldName == 'enquiry_start_date' || $fieldName == 'enquiry_end_date'  ){
						if($fieldName == 'enquiry_start_date'){  
							$DB->where('enquiry_date','>=',$fieldValue);
						}
						if($fieldName == 'enquiry_end_date'){  
							$DB->where('enquiry_date','<=',$fieldValue);
						}
					}else{
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB
									->where('enquiries.is_deleted',0)
									->where('enquiries.dealer_id',$dealer_id)
									->select("enquiries.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_type) as enquiry_mode"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.status) as enquirystatus"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_color) as vehicle_color_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.source_of_info) as source_of_info_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_occupation) as customer_occupation_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_level) as enquiry_level_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_category) as customer_category_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.current_vehicle_brand) as current_vehicle_brand_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.finencer_option) as finencer_option_name"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = enquiries.location_name) as location_name"),
									DB::raw("(SELECT full_name FROM users WHERE id = enquiries.sales_consultant) as sales_consultant"),
									DB::raw("(SELECT name FROM cities WHERE id = enquiries.city) as city"),
									DB::raw("(SELECT name FROM states WHERE id = enquiries.state) as state"))
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("customer_enquiry_report_search_data",$inputGet);
		$enquirymode 		=  $this->getDropDownListBySlug('enquirymode');
		$status_type 		=  $this->getDropDownListBySlug('enquirystatus');
		$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		$sourceOfInformation =  $this->getDropDownListBySlug('sourceOfInformation');
		$customerOccupation =  $this->getDropDownListBySlug('customerOccupation');
		$finencer_option =  $this->getDropDownListBySlug('finenceroptions');
		$customer_category =  $this->getDropDownListBySlug('customer-category');
		$enquirylevel 	=  $this->getDropDownListBySlug('enquirylevel');

		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
							->where("dealer_id",$dealer_id)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();

		return  View::make('dealerpanel.EnquiryReport.customerEnquiryReport', compact('result', 'enquirylevel','customer_category','sourceOfInformation','customerOccupation','finencer_option','vehiclemodel','enquirymode','status_type','sales_consultant','searchVariable','sortBy','order','query_string'));
	}
	
	
	public function exportCustomerEnquiryReportToExcel(){
		$genderArr 			= 	Config::get("gender_type_array");
		$searchData			=	Session::get('customer_enquiry_report_search_data');
		$DB 					= 	Enquiry::query();
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
					if($fieldName == 'enquiry_start_date' || $fieldName == 'enquiry_end_date'  ){
						if($fieldName == 'enquiry_start_date'){  
							$DB->where('enquiries.enquiry_date','>=',$fieldValue);
						}
						if($fieldName == 'enquiry_end_date'){  
							$DB->where('enquiries.enquiry_date','<=',$fieldValue);
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
									->where('enquiries.is_deleted',0)
									->where('enquiries.dealer_id',$dealer_id)
									->select("enquiries.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_type) as enquiry_mode"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.status) as enquirystatus"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_color) as vehicle_color_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.source_of_info) as source_of_info_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_occupation) as customer_occupation_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_level) as enquiry_level_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_category) as customer_category_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.current_vehicle_brand) as current_vehicle_brand_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.finencer_option) as finencer_option_name"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = enquiries.location_name) as location_name"),
									DB::raw("(SELECT full_name FROM users WHERE id = enquiries.sales_consultant) as sales_consultant"),
									DB::raw("(SELECT name FROM cities WHERE id = enquiries.city) as city"),
									DB::raw("(SELECT name FROM states WHERE id = enquiries.state) as state"))
									->orderBy($sortBy, $order)
									->get()->toArray();	
									// echo "<pre>";print_r($result);die;						
									
												
		$thead = array();
		$thead[]		= array("S. No.","Enquiry No.","Mode of Enquiry","Enquiry Date","Source of Information","Occupation","Test Ride","Booking Model","Booking Model Colour","Enquiry Status","Enquiry Level","Sales Consultant","Customer Name","Date of Birth","Owner Age","Gender","Mobile Number","Contact No.","Email Id","Address1","Address2","Pin","State","City","Delivery Date","Delivery Address1","Delivery Address2","Customer Category","Financier","Follow-up Count","Follow-up Date","Comparison Company","Comparison Model","Current Vehicle","Advance","Payment Mode");
		if(!empty($result)) {
			 $i = 1;
			foreach($result as $record) {
				$serialNumber					=	$i;
				$enquiry_number					=	!empty($record['enquiry_number'])?$record['enquiry_number']:'';
				$enquiry_mode					=	!empty($record['enquiry_mode'])?$record['enquiry_mode']:'';
				$enquiry_date					=	!empty($record['enquiry_date'])? date(Config::get("Reading.date_format") , strtotime($record['enquiry_date'])):'';
				$source_of_info_name			=	!empty($record['source_of_info_name'])?$record['source_of_info_name']:'';
				$customer_occupation_name		=	!empty($record['customer_occupation_name'])?$record['customer_occupation_name']:'';
				$test_drive_date				=	!empty($record['test_drive_date'])? date(Config::get("Reading.date_format") , strtotime($record['test_drive_date'])):'';
				$vehicle_modal					=	!empty($record['vehicle_modal'])?$record['vehicle_modal']:'';
				$vehicle_color_name				=	!empty($record['vehicle_color_name'])?$record['vehicle_color_name']:'';
				if($record['status']	== ENQUIRY_CLOSE_STATUS){
					$enquirystatus 				= "Closed";
				}else{ 
					$enquirystatus 				= $record['enquirystatus']; 
				}
				$enquiry_level_name				=	!empty($record['enquiry_level_name'])?$record['enquiry_level_name']:'';
				$sales_consultant				=	!empty($record['sales_consultant'])?$record['sales_consultant']:'';
				$customer_name					=	!empty($record['customer_name'])?$record['customer_name']:'';
				$dob							=	!empty($record['dob'])? date(Config::get("Reading.date_format") , strtotime($record['dob'])):'';
				$age							=	!empty($record['age'])?$record['age']:'';
				$gender							=	!empty($record['gender'])? $genderArr[$record['gender']]:'';
				
				$mobile_number					=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$contact_number					=	!empty($record['contact_number'])?$record['contact_number']:'';
				$email							=	!empty($record['email'])?$record['email']:'';
				$address_1						=	!empty($record['address_1'])?$record['address_1']:'';
				$address_2						=	!empty($record['address_2'])?$record['address_2']:'';
				$zip							=	!empty($record['zip'])?$record['zip']:'';
				$state							=	!empty($record['state'])?$record['state']:'';
				$city							=	!empty($record['city'])?$record['city']:'';
				$delivery_date					=	!empty($record['delivery_date'])? date(Config::get("Reading.date_format") , strtotime($record['delivery_date'])):'';
				
				$delivery_address_1				=	!empty($record['delivery_address_1'])?$record['delivery_address_1']:'';
				$delivery_address_2				=	!empty($record['delivery_address_2'])?$record['delivery_address_2']:'';
				$customer_category_name			=	!empty($record['customer_category_name'])?$record['customer_category_name']:'';
				$financer						=	!empty($record['financer'])?$record['financer']:'';				
				$follow_up_count				=	0;
				$next_follow_up_date			=	!empty($record['next_follow_up_date'])? date(Config::get("Reading.date_format") , strtotime($record['next_follow_up_date'])):'';
				$current_vehicle_brand_name		=	!empty($record['current_vehicle_brand_name'])?$record['current_vehicle_brand_name']:'';
				$current_vehicle_model			=	!empty($record['current_vehicle_model'])?$record['current_vehicle_model']:'';
				$current_vehicle				=	!empty($record['current_vehicle'])?$record['current_vehicle']:'';
				$advance						=	0;
				$finencer_option_name			=	!empty($record['finencer_option_name'])?$record['finencer_option_name']:'';
				
				$thead[]						= 	array($serialNumber,$enquiry_number,$enquiry_mode,$enquiry_date,$source_of_info_name,$customer_occupation_name,$test_drive_date,$vehicle_modal,$vehicle_color_name,$enquirystatus,$enquiry_level_name,$sales_consultant,$customer_name,$dob,$age,$gender,$mobile_number,$contact_number,$email,$address_1,$address_2,$zip,$state,$city,$delivery_date,$delivery_address_1,$delivery_address_2,$customer_category_name,$financer,$follow_up_count,$next_follow_up_date,$current_vehicle_brand_name,$current_vehicle_model,$current_vehicle,$advance,$finencer_option_name);
				
				$i++;
			}
		}								
		// echo '<pre>'; print_r($thead); die;					
		return  View::make('dealerpanel.EnquiryReport.customer_enquiry_export_excel', compact('thead'));
		
	}
	
	/* Customer Enquiry Report end*/	
	
	
	/* Customer Enquiry Follow up  Report start*/	
	public function customerEnquiryFollowUpReport(){
		$EN 					= 	DB::table('enquiries')
                                            ->where('enquiries.status', '!=',ENQUIRY_CLOSE_STATUS)
                                            ->leftjoin('dropdown_managers as modal', 'modal.id', '=', 'enquiries.vehicle_modal')
                                            ->leftjoin('dropdown_managers as color', 'color.id', '=', 'enquiries.vehicle_color');
		$searchVariable			=	array(); 
        $searchData			    =	Input::all();
        $today                  =   date('Y-m-d');
        
        $inputGet				=	Input::get();
        
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
					if($fieldName == 'follow_up_date' || $fieldName == 'follow_end_date'  ){
						if(!empty(Input::get("follow_up_date")) && empty(Input::get("follow_end_date"))){
							$EN->where("enquiries.next_follow_up_date","=",$searchData['follow_up_date']);
							
						}elseif(!empty(Input::get("follow_up_date")) && !empty(Input::get("follow_end_date"))){
							$EN->whereBetween("enquiries.next_follow_up_date",array($searchData['follow_up_date'],$searchData['follow_end_date']));
							
						}elseif(empty(Input::get("follow_up_date")) && !empty(Input::get("follow_end_date"))){
							$EN->where("enquiries.next_follow_up_date","=",$searchData['follow_up_date']);
							
						}
					}else{
						$EN->where("$fieldName",'like','%'.$fieldValue.'%');
					}
					
					
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$dealer_id				=	$this->get_dealer_id();
        $sortBy  =  (Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order   =  (Input::get('order')) ? Input::get('order')   : 'DESC';
		// query for both Enquiry and Advance Booking
		$result    =   $EN->where('enquiries.is_deleted', 0)
							->where('enquiries.dealer_id',$dealer_id)
							->select('enquiries.id','enquiries.next_follow_up_date as contact_date', 'enquiries.customer_name', 'enquiries.mobile_number', 'enquiries.enquiry_number as unique_number', DB::raw("NULL as follow_up_remark"), 'modal.name as vehicle_modal', 'color.name as vehicle_color', DB::raw("('enquiries') as table_name"), 'enquiries.created_at',DB::raw("(SELECT full_name FROM users WHERE id = enquiries.sales_consultant) as sales_consultant"))->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
							
							
		if($result){
			foreach($result as &$res){
				
				$followUpDetails = DB::table('enquiry_follow_up')->select('detail','next_follow_up_date')->where('enquiry_id',$res->id)->orderBy('created_at','DESC')->first();
				$res->follow_up_remark = $followUpDetails->detail;
				$res->contact_date = $followUpDetails->next_follow_up_date;
				
				
			}
		}					
							
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render(); 
        $sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
                                        ->where('dealer_id',$dealer_id)
                                        ->where("is_active",1)
                                        ->where("is_deleted",0)
                                        ->orderBy('full_name', 'ASC')
                                        ->pluck('full_name','id')
                                        ->toArray();
                                        
        $outletList 	=  $this->getDropDownListBySlug('outlet');                                
         Session::put("enquiry_follow_up_report_search_data",$inputGet);                               
        return View::make('dealerpanel.EnquiryReport.enquiryFollowUpReport', compact("result","sortBy","order","total_filtered_data", "query_string", "searchVariable","sales_consultant","outletList"));
	}
	
	public function exportEnquiryFolloeUpReportToExcel(){
		$searchData			=	Session::get('enquiry_follow_up_report_search_data');
		$EN 					= 	DB::table('enquiries')
                                            ->where('enquiries.status', '!=',ENQUIRY_CLOSE_STATUS)
                                            ->leftjoin('dropdown_managers as modal', 'modal.id', '=', 'enquiries.vehicle_modal')
                                            ->leftjoin('dropdown_managers as color', 'color.id', '=', 'enquiries.vehicle_color');
		$searchVariable			=	array(); 
        $today                  =   date('Y-m-d');
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
					if($fieldName == 'follow_up_date' || $fieldName == 'follow_end_date'  ){
						if(!empty(Input::get("follow_up_date")) && empty(Input::get("follow_end_date"))){
							$EN->where("enquiries.next_follow_up_date","=",$searchData['follow_up_date']);
							
						}elseif(!empty(Input::get("follow_up_date")) && !empty(Input::get("follow_end_date"))){
							$EN->whereBetween("enquiries.next_follow_up_date",array($searchData['follow_up_date'],$searchData['follow_end_date']));
							
						}elseif(empty(Input::get("follow_up_date")) && !empty(Input::get("follow_end_date"))){
							$EN->where("enquiries.next_follow_up_date","=",$searchData['follow_up_date']);
							
						}
					}else{
						$EN->where("$fieldName",'like','%'.$fieldValue.'%');
					}
					
					
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$dealer_id				=	$this->get_dealer_id();
        $sortBy  =  (Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order   =  (Input::get('order')) ? Input::get('order')   : 'DESC';
		// query for both Enquiry and Advance Booking
		$result    =   $EN->where('enquiries.is_deleted', 0)
							->where('enquiries.dealer_id',$dealer_id)
							->select('enquiries.id','enquiries.next_follow_up_date as contact_date', 'enquiries.customer_name', 'enquiries.mobile_number', 'enquiries.enquiry_number as unique_number', DB::raw("NULL as follow_up_remark"), 'modal.name as vehicle_modal', 'color.name as vehicle_color', DB::raw("('enquiries') as table_name"), 'enquiries.created_at',DB::raw("(SELECT full_name FROM users WHERE id = enquiries.sales_consultant) as sales_consultant"))->orderBy($sortBy, $order)->get()->toArray();	;
							
							
		if($result){
			foreach($result as &$res){
				
				$followUpDetails = DB::table('enquiry_follow_up')->select('detail','next_follow_up_date')->where('enquiry_id',$res->id)->orderBy('created_at','DESC')->first();
				$res->follow_up_remark = $followUpDetails->detail;
				$res->contact_date = $followUpDetails->next_follow_up_date;
				
				
			}
		}													
		//echo "<pre>";print_r($result);die;							
												
		$thead = array();
		$thead[]		= array("Enquiry No.","Customer Name","Vehicle Type","Follow-up Date","Sales Consultant","Sales Consultant");
		if(!empty($result)) {
			foreach($result as $record) {
				$unique_number					=	!empty($record->unique_number)?$record->unique_number:'';
				$customer_name					=	!empty($record->customer_name)?$record->customer_name:'';
				$vehicle_modal					=	!empty($record->vehicle_modal)?$record->vehicle_modal:'';
				$contact_date					=	!empty($record->contact_date)? date(Config::get("Reading.date_format") , strtotime($record->contact_date)):'';
				$follow_up_remark				=	!empty($record->follow_up_remark)?$record->follow_up_remark:'';
				$sales_consultant				=	!empty($record->sales_consultant)?$record->sales_consultant:'';
				
				
				$thead[]						= 	array($unique_number,$customer_name,$vehicle_modal,$contact_date,$follow_up_remark,$sales_consultant);
			}
		}								
		// echo '<pre>'; print_r($thead); die;					
		return  View::make('dealerpanel.EnquiryReport.enquiry_follow_up_export_excel', compact('thead'));
		
	}


	/* Search Chassis Report*/
	public function uploadCustomerEnquiryReport(){
		$DB 					= 	Enquiry::query();
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
				$DB->whereBetween('enquiries.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
				$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
			}elseif(empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
				$date_from	=	date("Y-m-d",strtotime($inputGet['retail_end_date']));
				$DB->whereBetween('enquiries.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
				$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
			}elseif(!empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
				$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
				$date_to	=	date('Y-m-d',strtotime($inputGet['retail_end_date']));
				$DB->whereBetween('enquiries.created_at',[$date_from." 00:00:00",$date_to." 23:59:59"]);
				$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date'],'retail_end_date' => $inputGet['retail_end_date']));
			}
			unset($inputGet['retail_start_date']);
			unset($inputGet['retail_end_date']);
			// foreach($inputGet as $fieldName => $fieldValue){
			// 	if($fieldValue != ""){
			// 		if($fieldName == 'unique_id' && $fieldValue != ''){
			// 			$DB->where("users.$fieldName",'like','%'.$fieldValue.'%');							
			// 		}elseif(($fieldName == 'chassis_number' || $fieldName == 'motor_number') && $fieldValue != ''){
			// 			$DB->where("inventories.$fieldName",'like','%'.$fieldValue.'%');
			// 		}else{
			// 			$DB->where("enquiries.$fieldName",'like','%'.$fieldValue.'%');
			// 		}
			// 	}
			// 	$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			// }

		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'enquiries.created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB->where('enquiries.is_deleted',0)
										->where('enquiries.dealer_id',$dealer_id)
										->select("enquiries.customer_name","enquiries.dob","enquiries.mobile_number","enquiries.enquiry_date","enquiries.email","enquiries.customer_occupation","enquiries.address_1","enquiries.address_2","enquiries.test_drive_date","enquiries.state","enquiries.zip","enquiries.enquiry_number","enquiries.current_vehicle","enquiries.outlet","enquiries.delivery_date","enquiries.delivery_address_1","enquiries.delivery_address_2", "enquiries.remarks", "enquiries.gender","enquiries.contact_number",
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_type) as enquiry_mode"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.status) as enquirystatus"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_modal) as vehicle_modal"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_color) as vehicle_color_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.source_of_info) as source_of_info_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_occupation) as customer_occupation_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_level) as enquiry_level_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_category) as customer_category_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.current_vehicle_brand) as current_vehicle_brand_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.finencer_option) as finencer_option"),
										DB::raw("(SELECT location_name FROM dealer_location WHERE id = enquiries.location_name) as location_name"),
										DB::raw("(SELECT full_name FROM users WHERE id = enquiries.sales_consultant) as sales_consultant"),
										DB::raw("(SELECT name FROM cities WHERE id = enquiries.city) as city"),
										DB::raw("(SELECT name FROM states WHERE id = enquiries.state) as state"),
										DB::raw("(SELECT next_follow_up_date FROM enquiry_follow_up WHERE enquiry_id = enquiries.id ORDER BY updated_at DESC LIMIT 1) as next_follow_up_date"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.outlet) as outlet"))
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));
			
			// echo "<pre>";print_r($result);die;
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("upload_customer_enquiry",Input::get());	
			$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');

		return  View::make('dealerpanel.EnquiryReport.upload_customer_enquiry', compact('result' ,'searchVariable','sortBy','order','query_string','vehiclemodel'));
	}

	public function exportuploadCustomerEnquiryReportToExcel(){
		$inputGet				=	Session::get('upload_customer_enquiry');
		$DB 					= 	Enquiry::query();
		$searchVariable			=	array(); 
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
				$DB->whereBetween('enquiries.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
				$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
			}elseif(empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
				$date_from	=	date("Y-m-d",strtotime($inputGet['retail_end_date']));
				$DB->whereBetween('enquiries.created_at',[$date_from." 00:00:00", $date_from." 23:59:59"]);
				$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date']));
			}elseif(!empty($inputGet['retail_start_date']) && !empty($inputGet['retail_end_date'])){
				$date_from	=	date("Y-m-d",strtotime($inputGet['retail_start_date']));
				$date_to	=	date('Y-m-d',strtotime($inputGet['retail_end_date']));
				$DB->whereBetween('enquiries.created_at',[$date_from." 00:00:00",$date_to." 23:59:59"]);
				$searchVariable	=	array_merge($searchVariable,array('retail_start_date' => $inputGet['retail_start_date'],'retail_end_date' => $inputGet['retail_end_date']));
			}
			unset($inputGet['retail_start_date']);
			unset($inputGet['retail_end_date']);
			// foreach($inputGet as $fieldName => $fieldValue){
			// 	if($fieldValue != ""){
			// 		if($fieldName == 'unique_id' && $fieldValue != ''){
			// 			$DB->where("users.$fieldName",'like','%'.$fieldValue.'%');							
			// 		}elseif(($fieldName == 'chassis_number' || $fieldName == 'motor_number') && $fieldValue != ''){
			// 			$DB->where("inventories.$fieldName",'like','%'.$fieldValue.'%');
			// 		}else{
			// 			$DB->where("enquiries.$fieldName",'like','%'.$fieldValue.'%');
			// 		}
			// 	}
			// 	$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			// }

		}
		$dealer_id				=	$this->get_dealer_id();
		$genderArr 				= 	Config::get("gender_type_array");
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'enquiries.created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB->where('enquiries.is_deleted',0)
										->where('enquiries.dealer_id',$dealer_id)
										->select("enquiries.customer_name","enquiries.dob","enquiries.mobile_number","enquiries.enquiry_date","enquiries.email","enquiries.customer_occupation","enquiries.address_1","enquiries.address_2","enquiries.test_drive_date","enquiries.state","enquiries.zip","enquiries.enquiry_number","enquiries.current_vehicle","enquiries.outlet","enquiries.delivery_date","enquiries.delivery_address_1","enquiries.delivery_address_2", "enquiries.remarks", "enquiries.gender","enquiries.contact_number",
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_type) as enquiry_mode"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.status) as enquirystatus"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_modal) as vehicle_modal"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_color) as vehicle_color_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.source_of_info) as source_of_info_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_occupation) as customer_occupation_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_level) as enquiry_level_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_category) as customer_category_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.current_vehicle_brand) as current_vehicle_brand_name"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.finencer_option) as finencer_option"),
										DB::raw("(SELECT location_name FROM dealer_location WHERE id = enquiries.location_name) as location_name"),
										DB::raw("(SELECT full_name FROM users WHERE id = enquiries.sales_consultant) as sales_consultant"),
										DB::raw("(SELECT name FROM cities WHERE id = enquiries.city) as city"),
										DB::raw("(SELECT name FROM states WHERE id = enquiries.state) as state"),
										DB::raw("(SELECT next_follow_up_date FROM enquiry_follow_up WHERE enquiry_id = enquiries.id ORDER BY updated_at DESC LIMIT 1) as next_follow_up_date"),
										DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.outlet) as outlet"))
										->orderBy($sortBy, $order)
										->get()
										->toArray();

		$thead = array();
		$thead[]		= array("Customer Name","Date of Birth","Gender","Mobile Number","Location Code","Enquiry Status","Enquiry Date","Source of Information","Sales Consultant","Customer Category","Follow-up Date","Booking Model","Colour","Contact Number",	"Email Id",	"Occupation",	"Address1",	"Address2","Pin","State","City","Payment Mode","Test Ride","Current Vehicle","Outlet","Enquiry Level","Delivery Date","Delivery Address1","Delivery Address2","Remarks");
		if(!empty($result)) {
			foreach($result as $record) {
				
				$customer_name							=	!empty($record['customer_name'])?$record['customer_name']:'';
				$dob									=	!empty($record['dob'])?date(Config::get("Reading.date_format") ,strtotime($record['dob'])):'';
				$gender									=	!empty($record['gender'])?$genderArr[$record['gender']]:'';
				$mobile_number							=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$location_name							=	!empty($record['location_name'])?$record['location_name']:'';
				$enquirystatus							=	!empty($record['enquirystatus'])?$record['enquirystatus']:'';
				$enquiry_date							=	!empty($record['enquiry_date'])? date(Config::get("Reading.date_format") , strtotime($record['enquiry_date'])):'';
				$source_of_info_name					=	!empty($record['source_of_info_name'])?$record['source_of_info_name']:'';
				$sales_consultant						=	!empty($record['sales_consultant'])?$record['sales_consultant']:'';
				$customer_category_name					=	!empty($record['customer_category_name'])?$record['customer_category_name']:'';
				$next_follow_up_date					=	!empty($record['next_follow_up_date'])? date(Config::get("Reading.date_format") , strtotime($record['next_follow_up_date'])):'';
				$vehicle_modal							=	!empty($record['vehicle_modal'])?$record['vehicle_modal']:'';
				$vehicle_color_name						=	!empty($record['vehicle_color_name'])?$record['vehicle_color_name']:'';
				$address_1								=	!empty($record['address_1'])?$record['address_1']:'';
				$address_2								=	!empty($record['address_2'])?$record['address_2']:'';
				$contact_number							=	!empty($record['contact_number'])?$record['contact_number']:'';
				$email									=	!empty($record['email'])?$record['email']:'';
				$zip									=	!empty($record['zip'])?$record['zip']:'';
				$state									=	!empty($record['state'])?$record['state']:'';
				$city									=	!empty($record['city'])?$record['city']:'';
				$test_drive_date						=	!empty($record['test_drive_date'])?date(Config::get("Reading.date_format"),strtotime($record['test_drive_date'])):'';
				$payment_mode							=	!empty($record['finencer_option'])?$record['finencer_option']:'';
				$current_vehicle_brand_name				=	!empty($record['current_vehicle_brand_name'])?$record['current_vehicle_brand_name']:'';
				$outlet									=	!empty($record['outlet'])?$record['outlet']:'';
				$enquiry_level_name						=	!empty($record['enquiry_level_name'])?$record['enquiry_level_name']:'';
				$delivery_date							=	!empty($record['delivery_date'])? date(Config::get("Reading.date_format") , strtotime($record['delivery_date'])):'';
				$delivery_address_1						=	!empty($record['delivery_address_1'])?$record['delivery_address_1']:'';
				$delivery_address_2						=	!empty($record['delivery_address_2'])?$record['delivery_address_2']:'';
				$remarks								=	!empty($record['remarks'])?$record['remarks']:'';
				$customer_occupation_name				=	!empty($record['customer_occupation_name'])?$record['customer_occupation_name']:'';
				$thead[]						= 	array($customer_name,$dob,$gender,$mobile_number,$location_name,$enquirystatus,$enquiry_date,$source_of_info_name,$sales_consultant,$customer_category_name,$next_follow_up_date,$vehicle_modal,$vehicle_color_name,$contact_number, $email, $customer_occupation_name,$address_1,$address_2, $zip, $state,$city,$payment_mode,$test_drive_date,$current_vehicle_brand_name,$outlet,$enquiry_level_name,$delivery_date,$delivery_address_1,$delivery_address_2,$remarks,);
			}
		}											
		return  View::make('dealerpanel.EnquiryReport.upload_customer_enquiry_excel', compact('thead'));
	}
	
	
	
	
	
	
} //end EnquiryReportController()
