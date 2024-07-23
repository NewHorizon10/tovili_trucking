<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\Enquiry;
use App\Model\AdvanceBooking;
use App\Model\DropDown;
use App\Model\AdvanceBookingFollowUp;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
use MPDF;
/**
* bookingController Controller
*
* Add your methods in the class below
*
* This file will render views\bookingController\dashboard
*/
	class AdvanceBookingController extends BaseController {
		
		public $model	=	'AdvanceBooking';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function listBooking(){
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
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == 'follow_up_end_date' || $fieldName == 'follow_up_start_date' || $fieldName == 'booking_date_start' || $fieldName == 'booking_date_end'  ){
						if($fieldName == 'follow_up_end_date'){  
							$DB->where('advance_booking.next_follow_up_date','<=',$fieldValue);
						}
						if($fieldName == 'follow_up_start_date'){  
							$DB->where('advance_booking.next_follow_up_date','>=',$fieldValue);
						}
						if($fieldName == 'booking_date_start'){  
							$DB->where('advance_booking.booking_date','>=',$fieldValue);
						}
						if($fieldName == 'booking_date_end'){  
							$DB->where('advance_booking.booking_date','<=',$fieldValue);
						}
					}else{
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
					
					
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		if(Input::get('status') != '' && Input::get('status') == ADVANCE_BOOKING_CANCEL_STATUS){
			$DB->where('advance_booking.status', ADVANCE_BOOKING_CANCEL_STATUS);
		}else{
			$DB->where('advance_booking.status', '!=',ADVANCE_BOOKING_CANCEL_STATUS);
		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB->where("is_deleted",0)
									->where('dealer_id',$dealer_id)
									->select("advance_booking.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = advance_booking.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.status) as status_name"),
									DB::raw("(SELECT full_name FROM users WHERE id = advance_booking.sales_consultant) as sales_consultant"))
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
		//echo'<pre>'; print_r($result); echo'</pre>'; die;
								
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("advance_booking_search_data",$inputGet);

		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
							->where("dealer_id",$dealer_id)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();

		

		$vehiclecolor =  $this->getDropDownListBySlug('vehiclecolor');
		$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		$status_type =  $this->getDropDownListBySlug('advancebookingstatus');
		$advancebookingcancelReason =  $this->getDropDownListBySlug('advancebookingcancelReason');
		
		return  View::make('dealerpanel.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string','sales_consultant','vehiclecolor','vehiclemodel','status_type','advancebookingcancelReason'));
	}
	//end function listBooking()
		
	

	/**
	* Function for add booking page
	*
	* @param null
	*
	* @return view page. 
	*/

	public function addBooking(){
		if( isset($_GET['enquiry_id']) && $_GET['enquiry_id'] != ''){
			$enquiry_id = $_GET['enquiry_id'];
			$enquiry_data = DB::table('enquiries')->where('id', $enquiry_id)->first();
			if($enquiry_data == ''){
				return Redirect::back();
			}
			$cityList		=	DB::table('cities')
									->where('state_id',$enquiry_data->state)
									->distinct('name')
									->pluck('name','id')
									->toArray();
		}else{
			$enquiry_id = '';
			$enquiry_data ='';
			$cityList	=	array();
		}
		//echo'<pre>'; print_r($enquiry_data); die;
		
		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',101)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray();

		$vehiclecolor =  $this->getDropDownListBySlug('vehiclecolor');
		$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		$sourceOfInformation =  $this->getDropDownListBySlug('sourceOfInformation');
		$paymentmode =  $this->getDropDownListBySlug('paymentmode');
		$status_type =  $this->getDropDownListBySlug('advancebookingstatus');		
		$dealer_id				=	$this->get_dealer_id();
		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
							->where("dealer_id",$dealer_id)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();
			
		$dealerLocationName 	=  DB::table('dealer_location')
										->where('dealer_id',$dealer_id)
										->where('is_active',1)
										->orderBy('location_name','ASC')
										->pluck('location_name','id')
										->toArray();
			
		return View::make('dealerpanel.'.$this->model.'.add', compact('vehiclecolor' ,'cityList','vehiclemodel','sales_consultant','stateList','paymentmode','dealerLocationName','enquiry_data','enquiry_id','status_type'));
	}
	// end function addBooking()
	
/**
	* Function for save booking
	*
	* @param null
	*
	* @return view page. 
*/
	public function saveBooking(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'booking_date' 					=>	 'required',
					'vehicle_modal' 				=>	 'required',
					'vehicle_color' 				=>	 'required',
					'customer_name' 				=>	 'required',
					'location_name' 				=>	 'required',
					'email' 						=>	 'email',
					'gender' 						=>	 'required',
					'address_1' 					=>	 'required',
					'city' 							=>	 'required',
					'state' 						=>	 'required',
					//'zip' 							=>	 'required',
					'mobile_number' 				=>	 'required|integer|digits:10',
					'advance_booking_amount' 		=>	 'required|numeric',
					'payment_mode' 					=>	 'required',
					//'status' 						=>	 'required',
					'remarks' 						=>	 'required',
					'next_follow_up_date' 			=>	 'required',
					'sales_consultant' 				=>	 'required',
				),
				array(
					'email.email'							=> 	'The email address is invalid.',
					'dob.required'							=>	 'The date of birth field is required.', 
					'zip.required' 							=>	 'The zipcode field is required.',
					'vehicle_modal.required' 				=>	 'The model enquired field is required.',
					'vehicle_color.required' 				=>	 'The vehicle color field is required.',
					"mobile_number.integer"					=>	trans("Phone number must have a numeric value."),
					"mobile_number.digits"					=>	trans("Phone number must have 10 digits."),
					//'status.required' 						=>	 'The booking status field is required.',
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				$dealer_id						=	$this->get_dealer_id();
				$booking 						= 	new AdvanceBooking;
				$booking->dealer_id				=	$dealer_id;
				$booking->enquiry_id			=	!empty(Input::get('enquiry_id')) ? Input::get('enquiry_id'): '';
				$booking->booking_date			=	!empty(Input::get('booking_date')) ? date('Y-m-d',strtotime(Input::get('booking_date'))) : '0000-00-00';
				$booking->vehicle_modal			=	Input::get('vehicle_modal');
				$booking->vehicle_color			=	Input::get('vehicle_color');
				$booking->customer_name			=	Input::get('customer_name');
				$booking->location_name			=	Input::get('location_name');
				$booking->email					=	Input::get('email');
				$booking->gender				=	Input::get('gender');	
				$booking->dob					=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
				$booking->address_1				=	Input::get('address_1');
				$booking->address_2				=	Input::get('address_2');
				$booking->city					=	Input::get('city');
				$booking->state					=	Input::get('state');
				$booking->zip					=	Input::get('zip');
				$booking->mobile_number			=	Input::get('mobile_number');
				$booking->advance_booking_amount=	Input::get('advance_booking_amount');
				$booking->payment_mode			=	Input::get('payment_mode');
				$booking->current_occupation	=	Input::get('current_occupation');
				$booking->status				=	ADVANCE_PAYMENT_RECEIVED_STATUS;
				$booking->remarks				=	Input::get('remarks');
				$booking->next_follow_up_date	=	!empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
				$booking->planned_visit_date	=	!empty(Input::get('planned_visit_date')) ? date('Y-m-d',strtotime(Input::get('planned_visit_date'))) : '0000-00-00';
				$booking->sales_consultant		=	Input::get('sales_consultant');
				$enquiry_id						=	Input::get('enquiry_id');
				$booking->save();

				if(!empty($enquiry_id)){
					Enquiry::where('id',$enquiry_id)->update(array('status'=> ENQUIRY_BOOKED_STATUS));
				}	
				$id  = $booking->id;
				if(!empty($id)){
					
					$booking_number		=	'#AB000'.$id;
					AdvanceBooking::where('id',$id)->update(array('booking_number'=>$booking_number));
					
					// check it
					$followUpObj 						= new AdvanceBookingFollowUp;
					$followUpObj->user_id 				= $dealer_id;
					$followUpObj->booking_id 			= $id;
					$followUpObj->next_follow_up_date   = !empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
					$followUpObj->detail 				= Input::get('remarks');
					$followUpObj->save();
					
				}
					
				Session::flash("success",trans("Advance Booking added successfully."));
				return Redirect::to('/dealerpanel/advance-booking-management');
			}
		}
	}
	// end function saveBooking()

	/**
	* Function for edit Booking
	*
	* @param id
	*
	* @return view page. 
	*/

	public function editBooking($id = ""){
		$dealer_id			=	$this->get_dealer_id();
		$bookingDetails	    =	DB::table('advance_booking')
								->where('id',$id)
								->where("dealer_id",$dealer_id)
								->first();
		if(empty($bookingDetails)) {
			return Redirect::back();
		}	
		
		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',101)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray();
		$cityList		=	DB::table('cities')
							->where('state_id',$bookingDetails->state)
							->distinct('name')
							->pluck('name','id')
							->toArray();

		// $vehiclecolor =  $this->getDropDownListBySlug('vehiclecolor');
		// $vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		$vehiclemodel	=	DB::table('dropdown_managers')->where('id',$bookingDetails->vehicle_modal)->value('name');
		$vehiclecolor	=	DB::table('dropdown_managers')->where('id',$bookingDetails->vehicle_color)->value('name');
		$sourceOfInformation =  $this->getDropDownListBySlug('sourceOfInformation');
		$paymentmode =  $this->getDropDownListBySlug('paymentmode');	
		$status_type =  $this->getDropDownListBySlug('advancebookingstatus');		

		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
								->where("dealer_id",$dealer_id)
								->where("is_active",1)
								->where("is_deleted",0)
								->orderBy('full_name', 'ASC')
								->pluck('full_name','id')
								->toArray();

		$dealerLocationName 	=  DB::table('dealer_location')
								->where('dealer_id',$dealer_id)
								->where('is_active',1)
								->orderBy('location_name','ASC')
								->pluck('location_name','id')
								->toArray();


		return View::make('dealerpanel.'.$this->model.'.edit', compact("bookingDetails",'cityList','vehiclecolor' ,'vehiclemodel','sales_consultant','stateList','paymentmode','dealerLocationName','status_type'));
	
		
	} // end function for editBooking()

	
	/**
	* Function for update Booking 
	*
	* @param id
	*
	* @return view page. 
	*/


	public function updateBooking($id){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'booking_date' 					=>	 'required',
					'vehicle_modal' 				=>	 'required',
					'vehicle_color' 				=>	 'required',
					'customer_name' 				=>	 'required',
					'location_name' 				=>	 'required',
					'email' 						=>	 'email',
					'gender' 						=>	 'required',
					'address_1' 					=>	 'required',
					'city' 							=>	 'required',
					'state' 						=>	 'required',
					'zip' 							=>	 'required',
					'mobile_number' 				=>	 'required|integer|digits:10',
					'advance_booking_amount' 		=>	 'required|numeric',
					'payment_mode' 					=>	 'required',
					//'status' 						=>	 'required',
					'remarks' 						=>	 'required',
					'next_follow_up_date' 			=>	 'required',
					'sales_consultant' 				=>	 'required',
				),
				array(
					'email.email'							=> 	'The email address is invalid.',
					'dob.required'							=>	 'The date of birth field is required.', 
					'zip.required' 							=>	 'The zipcode field is required.',
					'vehicle_modal.required' 				=>	 'The model enquired field is required.',
					'vehicle_color.required' 				=>	 'The vehicle color field is required.',
					"mobile_number.integer"					=>	trans("Phone number must have a numeric value."),
					"mobile_number.digits"					=>	trans("Phone number must have 10 digits."),
					//'status.required' 						=>	 'The booking status field is required.',
				)
			
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				
				$booking1						= 	AdvanceBooking::find($id);
				$booking1->booking_date			=	!empty(Input::get('booking_date')) ? date('Y-m-d',strtotime(Input::get('booking_date'))) : '0000-00-00';
				// $booking1->vehicle_modal			=	Input::get('vehicle_modal');
				// $booking1->vehicle_color			=	Input::get('vehicle_color');
				$booking1->customer_name			=	Input::get('customer_name');
				$booking1->location_name			=	Input::get('location_name');
				$booking1->email					=	Input::get('email');
				$booking1->gender				=	Input::get('gender');	
				$booking1->dob					=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
				$booking1->address_1				=	Input::get('address_1');
				$booking1->address_2				=	Input::get('address_2');
				$booking1->city					=	Input::get('city');
				$booking1->state					=	Input::get('state');
				$booking1->zip					=	Input::get('zip');
				$booking1->mobile_number			=	Input::get('mobile_number');
				$booking1->advance_booking_amount=	Input::get('advance_booking_amount');
				$booking1->payment_mode			=	Input::get('payment_mode');
				$booking1->current_occupation	=	Input::get('current_occupation');
				$booking1->status				=	ADVANCE_PAYMENT_RECEIVED_STATUS;
				$booking1->remarks				=	Input::get('remarks');
				$booking1->next_follow_up_date	=	!empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
				$booking1->planned_visit_date	=	!empty(Input::get('planned_visit_date')) ? date('Y-m-d',strtotime(Input::get('planned_visit_date'))) : '0000-00-00';
				$booking1->sales_consultant		=	Input::get('sales_consultant');

				$booking1->save();

				Session::flash('flash_notice', trans("Advance Booking has been updated successfully.")); 
				return Redirect::to('/dealerpanel/advance-booking-management');
			}
		}
	}
	// end function updateBooking()

	//function for delete booking
	public function deleteBooking($id = ''){
		$bookingDetails			=	AdvanceBooking::find($id); 
		if(empty($bookingDetails)) {
			return Redirect::back();
		}
		if($bookingDetails){	
			$Model					=	AdvanceBooking::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans("Advance Booking has been deleted successfully.")); 
		}
		return Redirect::back();
	}//end function for delete booking 


	//function for view booking
	public function viewBooking($booking_id=""){

		$dealer_id				=	$this->get_dealer_id();
		$bookingDetails			=	DB::table('advance_booking')
									->where("id",$booking_id)
									->where("dealer_id",$dealer_id)
									->select("advance_booking.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = advance_booking.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT name FROM states WHERE id = advance_booking.state) as state"),
									DB::raw("(SELECT full_name FROM users WHERE id = advance_booking.sales_consultant) as sales_consultant"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.status) as advance_booking_status"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.close_reason) as close_reason"),
									DB::raw("(SELECT name FROM cities WHERE id = advance_booking.city) as city"))
									->first();
									

		if(empty($bookingDetails)) {
			return Redirect::back();
		}else{	
			$advancebookingcancelReason =  $this->getDropDownListBySlug('advancebookingcancelReason');
			$status_type =  $this->getDropDownListBySlug('advancebookingstatus');			
			$followUpDetails = DB::table('advance_booking_follow_up')
								 ->leftJoin('users','users.id','=','advance_booking_follow_up.user_id')
								 ->select('advance_booking_follow_up.*','users.full_name as fullname')
								->where('advance_booking_follow_up.booking_id',$booking_id)->get();	
							
			return View::make('dealerpanel.'.$this->model.'.view', compact("bookingDetails","followUpDetails","status_type",'advancebookingcancelReason'));
		}
	}//end function for view booking

	
	
	public function addFollowUp(){
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'booking_id' 				=>	 'required',
					'next_follow_up_date' 		=>	 'required',
					'detail' 					=>	 'required',
					),
				array(
					"detail.required"					=>	trans("The remarks field is required."),
				)
			);
			if ($validator->fails()) {	
				$response				=	array(
					'success' 			=> 	2,
					'errors' 			=> 	$validator->errors()
				);
				return Response::json($response); 
				die;
			}else{
				$dealer_id				=	$this->get_dealer_id();
				$followUpObj 						= new AdvanceBookingFollowUp;
				$followUpObj->user_id 				= $dealer_id;
				$followUpObj->booking_id 			= Input::get('booking_id');
				$followUpObj->next_follow_up_date   = !empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
				$followUpObj->detail 				= Input::get('detail');
				$followUpObj->save();
				AdvanceBooking::where('id',Input::get('booking_id'))->update(array('next_follow_up_date'=>Input::get('next_follow_up_date')));
				
				$response				=	array(
					'success' 			=> 	1,
					'errors' 			=> 	'',
				);
				Session::flash('flash_notice',trans("Follow up added successfully.")); 
				return Response::json($response); 
				die;
			}
		}
	}
	
	public function exportAdvanceBookingToExcel(){
		$searchData			=	Session::get('advance_booking_search_data');
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
					if($fieldName == 'follow_up_end_date' || $fieldName == 'follow_up_start_date' || $fieldName == 'booking_date_start' || $fieldName == 'booking_date_end'  ){
						if($fieldName == 'follow_up_end_date'){  
							$DB->where('advance_booking.next_follow_up_date','<=',$fieldValue);
						}
						if($fieldName == 'follow_up_start_date'){  
							$DB->where('advance_booking.next_follow_up_date','>=',$fieldValue);
						}
						if($fieldName == 'booking_date_start'){  
							$DB->where('advance_booking.booking_date','>=',$fieldValue);
						}
						if($fieldName == 'booking_date_end'){  
							$DB->where('advance_booking.booking_date','<=',$fieldValue);
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
		$result 				= 	$DB->where("is_deleted",0)
									->where('dealer_id',$dealer_id)
									->select("advance_booking.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = advance_booking.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT full_name FROM users WHERE id = advance_booking.sales_consultant) as sales_consultant"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.status) as advance_booking_status"),
									DB::raw("(SELECT name FROM states WHERE id = advance_booking.state) as state"),
									DB::raw("(SELECT name FROM cities WHERE id = advance_booking.city) as city"))
									->orderBy($sortBy, $order)
									->get()->toArray();						
									
												
		$thead = array();
		
		$thead[]		= array("Booking-No.","Booking Date/Time","Model enquired","Vehicle Color","Customer Name","Gender","Date of Birth","Email Address","Customer Occupation","Address 1","Address 2","City","State","Zipcode","Mobile Number","Sales Consultant","Location Name","Status","Advance Booking Amount","Payment Mode","Remarks");
		if(!empty($result)) {
			foreach($result as $record) {
				$booking_number					=	!empty($record['booking_number'])?$record['booking_number']:'';
				$booking_date					=	!empty($record['booking_date'])?date(Config::get("Reading.date_format") , strtotime($record['booking_date'])):'';
				$vehicle_modal					=	!empty($record['vehicle_modal'])?$record['vehicle_modal']:'';
				$vehicle_color					=	!empty($record['vehicle_color'])?$record['vehicle_color']:'';
				$customer_name					=	!empty($record['customer_name'])?$record['customer_name']:'';
				$mobile_number					=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$email							=	!empty($record['email'])?$record['email']:'';
				$current_occupation				=	!empty($record['current_occupation'])?$record['current_occupation']:'';
				
				$address_1						=	!empty($record['address_1'])?$record['address_1']:'';
				$address_2						=	!empty($record['address_2'])?$record['address_2']:'';
				$city							=	!empty($record['address_2'])?$record['city']:'';
				$state							=	!empty($record['state'])?$record['state']:'';
				$zip							=	!empty($record['zip'])?$record['zip']:'';
				$sales_consultant				=	!empty($record['sales_consultant'])?$record['sales_consultant']:'';
				$advance_booking_status				=	!empty($record['advance_booking_status'])?$record['advance_booking_status']:'';
				$remarks				=	!empty($record['remarks'])?$record['remarks']:'';
				
				
				
				
				$payment_mode					=	!empty($record['payment_mode'])?$record['payment_mode']:'';
				$advance_booking_amount			=	!empty($record['advance_booking_amount'])?$record['advance_booking_amount']:'';
				$location_name							=	!empty($record['location_name'])?$record['location_name']:'';
				$sales_consultant				=	!empty($record['sales_consultant'])?$record['sales_consultant']:'';
				$next_follow_up_date			=	!empty($record['next_follow_up_date'])?date(Config::get("Reading.date_format") , strtotime($record['next_follow_up_date'])):'';
				$planned_visit_date				=	!empty($record['planned_visit_date'])?date(Config::get("Reading.date_format") , strtotime($record['planned_visit_date'])):'';
				
				$dob				=	!empty($record['dob'])?date(Config::get("Reading.date_format") , strtotime($record['dob'])):'';
				$status							=	!empty($record['status'])?$record['status']:'';
				
				$genderID							=   !empty($record['gender']) ?$record['gender']:'';
				if($genderID == MALE){
					$gender = 'Male';
				}else{
					$gender = 'Female';
				}	
				
				
				$thead[]		= array($booking_number,$booking_date,$vehicle_modal,$vehicle_color,$customer_name,$gender,$dob,$email,$current_occupation,$address_1,$address_2,$city,$state,$zip,$mobile_number,$sales_consultant,$location_name,$advance_booking_status,'₹'.$advance_booking_amount,$payment_mode,$remarks);
			}
		}								
		//echo '<pre>'; print_r($thead); die;					
		return  View::make('dealerpanel.'.$this->model.'.export_excel', compact('thead'));
	}

	/**
	* Function for Change enquiry status
	*
	* @param null
	*
	* @return index page. 
	*/
	public function changeAdvanceBookingStatus(){
		$formData						=	Input::all();
		$booking_id		=	Input::get('booking_id');
		$change_status	=	Input::get('change_status');
		$cancel_reason	=	!empty(Input::get('close_reason')) ? Input::get('close_reason') : '';
		$cancel_remarks	=	!empty(Input::get('remark')) ? Input::get('remark') : '';
		$response		=	array();
		if($booking_id != '' && $change_status != ''){
			$findBooking	=	AdvanceBooking::find($booking_id);
			if($findBooking){
				if($change_status == ADVANCE_BOOKING_CANCEL_STATUS){
					$enquiry_id		=	$findBooking->enquiry_id;
					if($enquiry_id != ''){
						Enquiry::where('id',$enquiry_id)->update(array('status'=>ENQUIRY_CLOSE_STATUS));
					}
				}
				// if($change_status == ADVANCE_BOOKING_DISPATCHED_STATUS){
				// 	$enquiry_id		=	$findBooking->enquiry_id;
				// 	if($enquiry_id != ''){
				// 		Enquiry::where('id',$enquiry_id)->update(array('status'=>ENQUIRY_CLOSE_STATUS));
				// 	}
				// }
				$findBooking->status			=	$change_status;
				$findBooking->close_reason		=	$cancel_reason;
				$findBooking->cancel_remarks	=	$cancel_remarks;
				$findBooking->is_cancelled		=	1;
				$findBooking->save();
				$response['success']	=	1;
			}
		}else{
			$response['success']	=	0;
		}
		Session::flash('flash_notice',trans("Advance booking status has been changed successfully.")); 
		return Response::json($response);die;
	}

	/**
	* Function for cancel inquiry reason
	*
	* @param null
	*
	* @return index page. 
	*/
	public function cancelBooking(){
		$thisData			=	Input::all();
		$booking_id    		=   Input::get('booking_id');
	
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'cancel_remarks' 				=>	 'required',
					'close_reason' 					=>	 'required',
					),
				array(
					"cancel_remarks.required"		=>	trans("The remarks field is required."),
					"cancel_reason.required"		=>	trans("Please select close reason."),
				)
			);
			if ($validator->fails()) {	
				$response				=	array(
					'success' 			=> 	2,
					'errors' 			=> 	$validator->errors()
				);
				return Response::json($response); 
				die;
			}else{
				// cancle enquiry
				$AdBooking		=	AdvanceBooking::find($booking_id);
				$cancelEnquiry	=	Enquiry::where('id',$AdBooking->enquiry_id)->update(array('status'=>ENQUIRY_CLOSE_STATUS));

				// cancle advance booking
				AdvanceBooking::where('id',$booking_id)->update(array('status'=>ADVANCE_BOOKING_CANCEL_STATUS,'is_cancelled'=>1,'cancel_remarks'=>Input::get('cancel_remarks'),'close_reason'=>Input::get('close_reason')));
				
				$response				=	array(
					'success' 			=> 	1,
					'errors' 			=> 	'',
				);
				Session::flash('flash_notice',trans("Advance Booking has been cancelled successfully.")); 
				return Response::json($response); 
				die;
			}
		}
	}


	/**
	* Function for cancel inquiry reason
	*
	* @param null
	*
	* @return index page. 
	*/
	public function getAvailableVehicleColor(){
			Input::replace($this->arrayStripTags(Input::all()));
			$thisData				=	Input::all();
			if(!empty($thisData)){
				$dealer_id				=	$this->get_dealer_id();
				$vehicle_modal		=	$thisData['vehicle_modal'];
				$response			=	array();			
				$vehicleColors 		= 	DB::table('dealer_inventory')
												->where('dealer_id',$dealer_id)
												->where('is_sold',0)
												->leftjoin('inventories', 'dealer_inventory.vehicle_id','=','inventories.id')
												->leftjoin('dropdown_managers', 'inventories.color_id', '=', 'dropdown_managers.id')
												->where('inventories.model_id',$vehicle_modal)
												->select('dropdown_managers.name', 'dropdown_managers.id')
												->orderBy('dropdown_managers.name')
												->distinct()
												->get()->toArray();
												// echo "<pre>";print_r($vehicleColors);die;
				if(empty($vehicleColors)){
					$vehicleColors 		= 	DB::table('inventories')
												->where('model_id',$vehicle_modal)
												->leftjoin('dropdown_managers', 'inventories.color_id', '=', 'dropdown_managers.id')
												->where('inventories.is_sent_to_dealer',0)
												->select('dropdown_managers.id', 'dropdown_managers.name')
												->orderBy('dropdown_managers.name')
												->distinct()
												->get()->toArray();
				}
				
				$optionHtml = '';
				if(!empty($vehicleColors)){
					$optionHtml = '<option value="">Select Vehicle Color</option>';
					foreach($vehicleColors as $v_value=>$v_vin){
						$optionHtml .= '<option value="'.$v_vin->id.'">'.$v_vin->name.'</option>';
					}
				}
				if($optionHtml){
					$response['success']	=	1;
					$response['option']	=	$optionHtml;
				}else{
					$vehicleModel = DB::table('dropdown_managers')->where('id',$vehicle_modal)->select('name')->first();
					$response['success']	=	0;
					$response['option']	=	'';
					$response['vehicle']	=	$vehicleModel->name;
				}
				return Response::json($response);die;
			}
	}

		/**
	* Function for generate advance booking invoice
	*
	* @param null
	*
	* @return index page. 
	*/
	public function generateAdvanceBookingInvoice($id){
		$dealer_id				=	$this->get_dealer_id();
		$bookingDetails	    =	DB::table('advance_booking')
								->where('advance_booking.id',$id)
								->where("dealer_id",$dealer_id)
								->leftjoin('dropdown_managers as model','advance_booking.vehicle_modal','=','model.id')
								->leftjoin('dropdown_managers as color','advance_booking.vehicle_color','=','color.id')
								->select('advance_booking.*', 'model.name as model_name','color.name as color_name')
								->first();
		if(empty($bookingDetails)) {
			return Redirect::back();
		}	
		$stateList		=	DB::table('states')
								->where('status',1)
								->where('country_id',101)
								->orderBy('name','ASC')
								->pluck('name','id')
								->toArray();
		$cityList		=	DB::table('cities')
								->where('state_id',$bookingDetails->state)
								->distinct('name')
								->pluck('name','id')
								->toArray();
		return  View::make('dealerpanel.'.$this->model.'.generate_advance_booking_invoice', compact('bookingDetails','stateList','cityList'));
	}

	/**
	* Function for generate advance booking invoice
	*
	* @param null
	*
	* @return index page. 
	*/
	public function saveAdvanceBookingInvoice(){
		Input::replace($this->arrayStripTags(Input::all()));
		$dealer_id				=	$this->get_dealer_id();
		$thisData				=	Input::all();
		$validator 				=	Validator::make(
			Input::all(),
			array(
				'customer_name' 				=>	 'required',
				'address_1' 					=>	 'required',
				'city' 							=>	 'required',
				'state' 						=>	 'required',
			),
			array(
			)
		);
		if ($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{ 
			$bookingDetails				=	AdvanceBooking::where('id',$thisData['booking_id'])
															->where("dealer_id",$dealer_id)
															->first();
			if(empty($bookingDetails)){
				return Redirect::back();
			}
			$InputArr['customer_name']	=	$thisData['customer_name'];
			$InputArr['address_1']		=	$thisData['address_1'];
			$InputArr['address_2']		=	$thisData['address_2'];
			$InputArr['zip']			=	$thisData['zip'];
			$InputArr['state']			=	DB::table('states')->where('id', $thisData['state'])->value('name');
			$InputArr['city']			=	DB::table('cities')->where('id', $thisData['city'])->value('name');
			$dealerDetails 				= DB::table('users')
												->where('users.id',$bookingDetails->dealer_id)
												->leftjoin('states', 'users.state_id', '=', 'states.id')
												->select('users.*', 'states.name as state_name')
												->first();
			return View::make('dealerpanel.'.$this->model.'.view_invoice_pdf', compact('bookingDetails','dealerDetails', 'amount_in_words', 'tax_amount','vehicleDetails','invoicePath','fileName','InputArr'));
		}
	}   


	public function downloadAdvanceBookingInvoice($booking_id=""){
		
		// $data	=	Input::all();
    	$dealer_id				=	$this->get_dealer_id();
		
		$bookingDetails			=	DB::table('advance_booking')
									->where("id",$booking_id)
									->where("dealer_id",$dealer_id)
									->select("advance_booking.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = advance_booking.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT name FROM states WHERE id = advance_booking.state) as state"),
									DB::raw("(SELECT full_name FROM users WHERE id = advance_booking.sales_consultant) as sales_consultant"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.status) as advance_booking_status"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = advance_booking.close_reason) as close_reason"),
									DB::raw("(SELECT name FROM cities WHERE id = advance_booking.city) as city"))
									->first(); 

			$dealerDetails = DB::table('users')
							->where('users.id',$bookingDetails->dealer_id)
							->leftjoin('states', 'users.state_id', '=', 'states.id')
							->select('users.*', 'states.name as state_name')
							->first();							
				return View::make('dealerpanel.AdvanceBooking.invoice' ,compact('bookingDetails','dealerDetails') );	
    
         }


} //end bookingController()
