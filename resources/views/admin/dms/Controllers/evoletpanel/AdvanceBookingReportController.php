<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\AdvanceBooking;
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
	class AdvanceBookingReportController extends BaseController {
		
		public $model	=	'enquiry';

	public function __construct() {
		View::share('modelName',$this->model);
	}
	/* Advance Booking Report start*/	
	public function advanceBookingReport(){
		//return Input::get('status');die;
		$DB 					= 	AdvanceBooking::query();
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
			// follow_up_start_date
			if(Input::get("follow_up_start_date") != '' && Input::get("follow_up_end_date") == ''){
				$DB->where("advance_booking.next_follow_up_date","=",$searchData['follow_up_start_date']);
				
			}elseif(Input::get("follow_up_start_date") != '' && Input::get("follow_up_end_date") != ''){
				$DB->whereBetween("advance_booking.next_follow_up_date",array($searchData['follow_up_start_date'],$searchData['follow_up_end_date']));
				
			}elseif(Input::get("follow_up_start_date") == '' && Input::get("follow_up_end_date") != ''){
				$DB->where("advance_booking.next_follow_up_date","=",$searchData['follow_up_end_date']);
			}
			// booking_date_start
			if(Input::get("booking_date_start") != '' && Input::get("booking_date_end") == ''){
				$DB->where("advance_booking.booking_date","=",$searchData['booking_date_start']);
				
			}elseif(Input::get("booking_date_start") != '' && Input::get("booking_date_end") != ''){
				$DB->whereBetween("advance_booking.booking_date",array($searchData['booking_date_start'],$searchData['booking_date_end']));
				
			}elseif(Input::get("booking_date_start") =='' && Input::get("booking_date_end") != ''){
				$DB->where("advance_booking.booking_date","=",$searchData['booking_date_end']);
				
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != "" && ($fieldName != 'follow_up_end_date' && $fieldName != 'follow_up_start_date' && $fieldName != 'booking_date_start' && $fieldName != 'booking_date_end')){
					if($fieldName == 'state_new'){
						$DB->where("advance_booking.state",'=',$fieldValue);
					
					}elseif($fieldName == 'dealer_id'){
						$DB->where("advance_booking.dealer_id",$fieldValue);
					}else{
						$DB->where("advance_booking.$fieldName",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if(!empty($assignedDealer)){
				$DB->whereIn('advance_booking.dealer_id', $assignedDealer);
			}
		}
		$result 				= 	$DB->where("advance_booking.is_deleted",0)
									->leftjoin('users', 'advance_booking.dealer_id', '=', 'users.id')
									->select("advance_booking.*","users.dealer_code","users.full_name",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = advance_booking.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.status) as status_name"),
									
									DB::raw("(SELECT name FROM states WHERE id = advance_booking.state) as state"),
									DB::raw("(SELECT name FROM cities WHERE id = advance_booking.city) as city"),
									DB::raw("(SELECT full_name FROM users WHERE id = advance_booking.sales_consultant) as sales_consultant"))
									->orderBy('advance_booking.'.$sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
		// echo'<pre>'; print_r($result); echo'</pre>'; die;
								
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("advance_booking_report_search_data",$inputGet);
		
		$stateList = DB::table('states')->where('status',1)->pluck('name','id');
		$status_type =  $this->getDropDownListBySlug('advancebookingstatus');
		$dealersList 			= 	$this->get_dealer_list();
		return  View::make('admin.AdvanceBookingReport.advanceBookingReport', compact('result', 'dealersList','searchVariable','sortBy','order','query_string','stateList','status_type'));
	}
	
	
	public function exportAdvanceBookingReportToExcel(){
		$searchData			=	Session::get('advance_booking_report_search_data');
		// echo "<pre>";print_r($searchData);;die;
		$DB 					= 	AdvanceBooking::query();
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
					if($fieldName == 'follow_up_end_date' || $fieldName == 'follow_up_start_date'){
						if($searchData["follow_up_start_date"] != '' && $searchData["follow_up_end_date"] == ''){
							$DB->where("advance_booking.next_follow_up_date","=",$searchData['follow_up_start_date']);
							
						}elseif($searchData["follow_up_start_date"] != '' && $searchData["follow_up_end_date"] != ''){
							$DB->whereBetween("advance_booking.next_follow_up_date",array($searchData['follow_up_start_date'],$searchData['follow_up_end_date']));
							
						}elseif($searchData["follow_up_start_date"] == '' && $searchData["follow_up_end_date"] != ''){
							$DB->where("advance_booking.next_follow_up_date","=",$searchData['follow_up_end_date']);
						}
					}elseif($fieldName == 'booking_date_start' || $fieldName == 'booking_date_end'){
						if($searchData["booking_date_start"] != '' && $searchData["booking_date_end"] == ''){
							$DB->where("advance_booking.booking_date","=",$searchData['booking_date_start']);
							
						}elseif($searchData["booking_date_start"] != '' && $searchData["booking_date_end"] != ''){
							$DB->whereBetween("advance_booking.booking_date",array($searchData['booking_date_start'],$searchData['booking_date_end']));
							
						}elseif($searchData["booking_date_start"] =='' && $searchData["booking_date_end"] != ''){
							$DB->where("advance_booking.booking_date","=",$searchData['booking_date_end']);
							
						}
					}elseif($fieldName == 'state_new'){
						$DB->where("advance_booking.state",'=',$fieldValue);
					
					}elseif($fieldName == 'dealer_id'){
						$DB->where("advance_booking.dealer_id",$fieldValue);
					}else{
						$DB->where("advance_booking.$fieldName",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';		
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if(!empty($assignedDealer)){
				$DB->whereIn('advance_booking.dealer_id', $assignedDealer);
			}
		}				
		$result 				= 	$DB->where("advance_booking.is_deleted",0)
									->leftjoin('users', 'advance_booking.dealer_id', '=', 'users.id')
									->select("advance_booking.*","users.dealer_code","users.full_name",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = advance_booking.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.status) as status_name"),
									DB::raw("(SELECT name FROM states WHERE id = advance_booking.state) as state"),
									DB::raw("(SELECT name FROM cities WHERE id = advance_booking.city) as city"),
									DB::raw("(SELECT full_name FROM users WHERE id = advance_booking.sales_consultant) as sales_consultant"))
									->orderBy('advance_booking.'.$sortBy, $order)
									->get()->toArray();						
									
												
		$thead = array();
		$thead[]		= array("Dealer Name","Dealer Code","State","City","Booking No.","Booking Date","Status","Customer Name","Mobile Number","Email Id","dob","Planned Date of Visit","Advance Amount Received (Yes / No)","Advance Amount");
		if(!empty($result)) {
			foreach($result as $record) {
				$state					=	!empty($record['state'])?$record['state']:'';
				$city							=	!empty($record['city'])?$record['city']:'';
				$dealer_code					=	$record['dealer_code'];
				$full_name						=	$record['full_name'];
				
				$booking_number					=	!empty($record['booking_number'])?$record['booking_number']:'';
				$booking_date					=	!empty($record['booking_date'])? date(Config::get("Reading.date_format") , strtotime($record['booking_date'])):'';
				$paymentRecived = 'Yes';
				if (!empty($record['status'])){
					if($record['status']	== ADVANCE_BOOKING_CANCEL_STATUS){
						$status_name 			= "Cancelled";
						$paymentRecived = 'No';
					}else{
						$status_name 			= $record['status_name'];
						$paymentRecived = 'Yes';
					}
				}else{
					$status_name 				= '';
				}
				$customer_name					=	!empty($record['customer_name'])?$record['customer_name']:'';
				$mobile_number					=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$email							=	!empty($record['email'])?$record['email']:'';
				$dob							=	!empty($record['dob'])? date(Config::get("Reading.date_format") , strtotime($record['dob'])):'';
				$planned_visit_date				=	!empty($record['planned_visit_date'])? date(Config::get("Reading.date_format") , strtotime($record['planned_visit_date'])):'';
				$advance_booking_amount			=	!empty($record['advance_booking_amount'])?$record['advance_booking_amount']:'';
				
				$thead[]						= 	array($full_name,$dealer_code,$state,$city,$booking_number,$booking_date,$status_name,$customer_name,$mobile_number,$email,$dob,$planned_visit_date,$paymentRecived,$advance_booking_amount);
			}
		}										
		return  View::make('admin.AdvanceBookingReport.advance_booking_report_export_excel', compact('thead'));
		
	}
	
	/* Customer Enquiry Report end*/	

	/* Advance Booking Report start*/	
	public function viewAdvanceBookingReport(){
		$DB 					= 	AdvanceBooking::query();
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
			if(!empty($searchData['booking_date_start']) && empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
				$DB->whereDate('advance_booking.booking_date',$date_from);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
			}elseif(empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_end']));
				$DB->whereDate('advance_booking.booking_date',$date_from);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
			}elseif(!empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
				$date_to	=	date('Y-m-d',strtotime($searchData['booking_date_end']));
				$DB->whereBetween('advance_booking.booking_date',[$date_from,$date_to]);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start'],'booking_date_end' => $searchData['booking_date_end']));
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != "" && ($fieldName != 'booking_date_start' && $fieldName != 'booking_date_end')){
					if($fieldName == 'dealer_id'){
						$DB->where("advance_booking.dealer_id",$fieldValue);
					}else{
						$DB->where("advance_booking.$fieldName",'like','%'.$fieldValue.'%');
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if(!empty($assignedDealer)){
				$DB->whereIn('advance_booking.dealer_id', $assignedDealer);
			}
		}
		$result 				= 	$DB->where("advance_booking.is_deleted",0)
									->leftjoin('users', 'advance_booking.dealer_id', '=', 'users.id')
									->select("advance_booking.booking_number", 	"advance_booking.advance_booking_amount","advance_booking.mobile_number","advance_booking.booking_date",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = advance_booking.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.status) as status_name"),"users.full_name")
									->orderBy('advance_booking.'.$sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
		// echo'<pre>'; print_r($result); echo'</pre>'; die;
								
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("view_advance_booking_report_search_data",$inputGet);
		
		$status_type 	=  $this->getDropDownListBySlug('advancebookingstatus');
		$vehiclemodel 	=  $this->getDropDownListBySlug('vehiclemodel');
		$dealersList 			= 	$this->get_dealer_list();
		return  View::make('admin.AdvanceBookingReport.view_advance_booking_report', compact('result' ,'searchVariable','sortBy','order','query_string','vehiclemodel','status_type',"dealersList"));
	}

	public function exportViewAdvanceBookingReportToExcel(){
		$searchData			=	Session::get('view_advance_booking_report_search_data');
		$DB 				= 	AdvanceBooking::query();
		$searchVariable		=	array(); 
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
			if(!empty($searchData['booking_date_start']) && empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
				$DB->whereDate('advance_booking.booking_date',$date_from);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
			}elseif(empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_end']));
				$DB->whereDate('advance_booking.booking_date',$date_from);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
			}elseif(!empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
				$date_to	=	date('Y-m-d',strtotime($searchData['booking_date_end']));
				$DB->whereBetween('advance_booking.booking_date',[$date_from,$date_to]);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start'],'booking_date_end' => $searchData['booking_date_end']));
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != "" && ($fieldName != 'booking_date_start' && $fieldName != 'booking_date_end')){
					if($fieldName == 'dealer_id'){
						$DB->where("advance_booking.dealer_id",$fieldValue);
					}else{
						$DB->where("advance_booking.$fieldName",'like','%'.$fieldValue.'%');
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if(!empty($assignedDealer)){
				$DB->whereIn('advance_booking.dealer_id', $assignedDealer);
			}
		}
		$result 				= 	$DB->where("advance_booking.is_deleted",0)
									->leftjoin('users', 'advance_booking.dealer_id', '=', 'users.id')
									->select("advance_booking.booking_number", 	"advance_booking.advance_booking_amount","advance_booking.mobile_number","advance_booking.booking_date",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = advance_booking.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.status) as status_name"),"users.full_name")
									->orderBy('advance_booking.'.$sortBy, $order)
									->get()->toArray();	
									 //echo "<pre>";print_r($result);die;
						
		$thead = array();
		$thead[]		= array("Dealer Name","Advance Booking No.","Model","Colour","Payment Mode","Advance","Mobile Number","Location","Advance Booking Date","Booking Status");
		if(!empty($result)) {
			foreach($result as $record) {
				$booking_number					=	!empty($record['booking_number'])?$record['booking_number']:'';
				$dealer_name					=	!empty($record['full_name'])?$record['full_name']:'';
				$vehicle_modal					=	!empty($record['vehicle_modal'])?$record['vehicle_modal']:'';
				$vehicle_color					=	!empty($record['vehicle_color'])?$record['vehicle_color']:'';
				$payment_mode					=	!empty($record['payment_mode'])?$record['payment_mode']:'';
				$advance_booking_amount			=	!empty($record['advance_booking_amount'])?$record['advance_booking_amount']:'';		
				$mobile_number					=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$location_name					=	!empty($record['location_name'])?$record['location_name']:'';
				$booking_date					=	!empty($record['booking_date'])? date(Config::get("Reading.date_format") , strtotime($record['booking_date'])):'';
				$status_name					=	!empty($record['status_name'])?$record['status_name']:'';
				$thead[]						= 	array($dealer_name,$booking_number,$vehicle_modal,$vehicle_color,$payment_mode,$advance_booking_amount,$mobile_number,$location_name,$booking_date,$status_name);
			}
		}								
		// echo '<pre>'; print_r($thead); die;					
		return  View::make('admin.AdvanceBookingReport.view_advance_booking_export_excel', compact('thead'));
		
	}
	
	
	
	
	
} //end AdvanceBookingReportController()
