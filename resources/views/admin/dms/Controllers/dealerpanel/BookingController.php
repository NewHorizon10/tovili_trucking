<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\Booking;
use App\Model\Enquiry;
use App\Model\UserMeta;
use App\Model\DropDown;
use App\Model\AdvanceBookingFollowUp;
use App\Model\BookingOtherCharge;
use App\Model\ModelServices;
use App\Model\TaxManager;
use App\Model\RetailServices;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* BookingController Controller
*
* Add your methods in the class below
*
* This file will render views\bookingController\dashboard
*/
	class BookingController extends BaseController {
		
		public $model	=	'Booking';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function listBooking(){
		$trigger_booking_id		=	(!empty($_GET["booking_id"])) ? $_GET["booking_id"] : "";
		
		$DB 					= 	Booking::query();
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
			if(isset($searchData['booking_id'])){
				unset($searchData['booking_id']);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == 'booking_date_start' || $fieldName == 'booking_date_end'  ){
						if($fieldName == 'booking_date_start'){  
							$DB->where('booking.booking_date','>=',$fieldValue);
						}
						if($fieldName == 'booking_date_end'){  
							$DB->where('booking.booking_date','<=',$fieldValue);
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
									->select("booking.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = booking.location_name) as location_name"),
									DB::raw("(SELECT full_name FROM users WHERE id = booking.sales_consultant) as sales_consultant"))
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
		/*echo'<pre>'; print_r($result); echo'</pre>'; die;*/
								
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		unset($complete_string["booking_id"]);
		$query_string			=	http_build_query($complete_string);

		$inputall	=	Input::all();
		if(!empty(Input::all())){
			unset($inputall["booking_id"]);
		}
		$result->appends($inputall)->render();
		Session::put("advance_booking_search_data",$inputGet);

		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
							->where('dealer_id',$dealer_id)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();

		$vehiclecolor =  $this->getDropDownListBySlug('vehiclecolor');
		$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		$invoicePath = '';
		if(!empty($trigger_booking_id)){
			$invoiceNameDetails			=	DB::table('booking')
									->where("id",$trigger_booking_id)
									->where("dealer_id",$dealer_id)
									->select("invoice_name")
									->first();
									
			$invoicePath = INVOICE_URL.$invoiceNameDetails->invoice_name;
		}
		
		return  View::make('dealerpanel.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string','sales_consultant','vehiclecolor','vehiclemodel','trigger_booking_id','invoicePath'));
	}
		
	

	/**
	* Function for add booking page
	*
	* @param null
	*
	* @return view page. 
	*/

	public function addBooking(){

	
		$dealer_id				=	$this->get_dealer_id();
		//add data form enquiries on add page 
		$vehicleDetails = array();
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
			$advance_booking_id='';
			$vehiclecolor 		= 	DB::table('inventories')
											->where('model_id',$enquiry_data->vehicle_modal)
											->leftjoin('dropdown_managers', 'inventories.color_id', '=', 'dropdown_managers.id')
											->leftjoin('dealer_inventory', 'inventories.id', '=', 'dealer_inventory.vehicle_id')
											->where('dealer_inventory.is_sold',0)
											->where('dealer_inventory.dealer_id',$dealer_id)
											->orderBy('dropdown_managers.name')
											->distinct()
											->pluck('dropdown_managers.name', 'dropdown_managers.id');
		}elseif(isset($_GET['advance_booking_id']) && $_GET['advance_booking_id'] != ''){
			$advance_booking_id = $_GET['advance_booking_id'];
			$enquiry_data 		= DB::table('advance_booking')->where('id', $advance_booking_id)->first();
		    // dd($enquiry_data);
			if($enquiry_data == ''){
				return Redirect::back();
			}
			$cityList		=	DB::table('cities')
									->where('state_id',$enquiry_data->state)
									->distinct('name')
									->pluck('name','id')
									->toArray();
			$enquiry_id = '';
			
			$dealerVehicales 	= 	DB::table('dealer_inventory')->where('dealer_id',$dealer_id)->where('is_sold',0)->pluck('vehicle_id','vehicle_id');
			$vehicleDetails 	= 	DB::table('inventories')->where('model_id',$enquiry_data->vehicle_modal)->where('color_id',$enquiry_data->vehicle_color)->whereIn('id',$dealerVehicales)->pluck('vin_number','id')->toArray();
			$vehiclecolor 		= 	DB::table('inventories')
											->where('model_id',$enquiry_data->vehicle_modal)
											->leftjoin('dropdown_managers', 'inventories.color_id', '=', 'dropdown_managers.id')
											->leftjoin('dealer_inventory', 'inventories.id', '=', 'dealer_inventory.vehicle_id')
											->where('dealer_inventory.is_sold',0)
											->where('dealer_inventory.dealer_id',$dealer_id)
											->orderBy('dropdown_managers.name')
											->distinct()
											->pluck('dropdown_managers.name', 'dropdown_managers.id');
			
			
		}else{
			$advance_booking_id='';
			$enquiry_id = '';
			$enquiry_data ='';
			$cityList	=	array();
			$vehiclecolor=array();
		}
		//echo'<pre>'; print_r($enquiry_data); echo'</pre>'; die;
		//add data form enquiries on add page

		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',101)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray();

		$dealerLocationName 	=  DB::table('dealer_location')
								->where('dealer_id',$dealer_id)
								->orderBy('is_active',1)
								->orderBy('location_name','ASC')
								->pluck('location_name','id')
								->toArray();

		//$vehiclecolor =  $this->getDropDownListBySlug('vehiclecolor');
		$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		$paymentmode =  $this->getDropDownListBySlug('paymentmode');
		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
							->where("dealer_id",$dealer_id)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();

		return View::make('dealerpanel.'.$this->model.'.add', compact('vehiclecolor' ,'cityList','vehiclemodel','sales_consultant','stateList','paymentmode','dealerLocationName','enquiry_data','enquiry_id','advance_booking_id','vehicleDetails'));
	}
	
/**
	* Function for save booking
	*
	* @param null
	*
	* @return view page. 
		*/
	public function saveBooking(){
        //dd(Input::all());
		Input::replace($this->arrayStripTags(Input::all()));
		$dealer_id				=	$this->get_dealer_id();
		$formData						=	Input::all();
		// $formData['vehicle_color']='1';
		// $formData['vin_number']='POI PONY 2512563256';
		if(!empty($formData)){
			$validator 					=	Validator::make(
				$formData,
				array(
					'booking_date' 					=>	 'required',
					'customer_name' 				=>	 'required',
					'email' 						=>	 'email',
					'gender' 						=>	 'required',
					'address_1' 					=>	 'required',
					'city' 							=>	 'required',
					'state' 						=>	 'required',
					'zip' 							=>	 'required',
					'mobile_number' 				=>	 'required|integer|digits:10',
					'vin_number' 					=>	 'required',
					'price' 						=>	 'required|numeric',
					'payment_mode' 					=>	 'required',
					'location_name' 				=>	 'required',
					'sales_consultant' 				=>	 'required',
					'vehicle_modal'					=>		'required',
					'vehicle_color'					=>		'required',
				),
				array(
					'email.email'						=> 	'The email address is invalid.',
					'vehicle_modal.required' 			=>	 'The vehicle model field is required.',
					'vehicle_color.required' 			=>	 'The vehicle color field is required.',
					'dob.required' 						=>	 'The date of birth is required.',
					'zip.required' 						=>	 'The zipcode field is required.',
					'status.required' 					=>	 'The retail status field is required.',
					"mobile_number.integer"				=>	trans("Phone number must have a numeric value."),
					"mobile_number.digits"				=>	trans("Phone number must have 10 digits."),
					"vin_number.required"				=>	trans("Please select vehicle."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				
				$taxPercent = Config::get("Tax.gst");
				$totalAmount = 0;
				$totalPrice  = 0;
				$taxAmount   = 0;
				
				$price 			= 	!empty(Input::get('price'))?Input::get('price'):'0';
				if(!empty($price)){
					$totalPrice 	= 	$price;
					$texPercent  	= 	$taxPercent;
					$taxAmount 		= 	(($totalPrice*$texPercent)/100);
					$totalAmount    =  	($taxAmount+$totalPrice);
				}
				$booking 						= 	new Booking;
				$booking->dealer_id				=	$dealer_id;
				$booking->booking_date			=	!empty(Input::get('booking_date')) ? date('Y-m-d',strtotime(Input::get('booking_date'))) : '0000-00-00';
				$booking->vehicle_modal			=	Input::get('vehicle_modal');
				$booking->vehicle_color			=	Input::get('vehicle_color');
				$booking->vehicle_id			=	Input::get('vin_number');
				$booking->customer_name			=	Input::get('customer_name');
				$booking->email					=	Input::get('email');
				$booking->gender				=	Input::get('gender');	
				$booking->dob					=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
				$booking->address_1				=	Input::get('address_1');
				$booking->address_2				=	Input::get('address_2');
				$booking->city					=	Input::get('city');
				$booking->state					=	Input::get('state');
				$booking->zip					=	Input::get('zip');
				$booking->gst_in				=	Input::get('gst_in');
				$booking->mobile_number			=	Input::get('mobile_number');
				$booking->price					=	Input::get('price');
				$booking->quantity				=	Input::get('quantity');
				$booking->tax_amount			=	$taxAmount;
				$booking->tax_percent			=	$taxPercent;
				$booking->total_amount			=	$totalAmount;
				$booking->location_name			=	Input::get('location_name');
				$booking->place_supply			=	Input::get('place_supply');
				$booking->remarks				=	Input::get('remarks');
				$booking->sales_consultant		=	Input::get('sales_consultant');
				$booking->status				=	Input::get('status');
				$booking->payment_mode			=	Input::get('payment_mode');
				$enquiry_id						=	Input::get('enquiry_id');
				$booking->save();
				$id  = $booking->id;
				if(!empty($id)){
					// chage advance booking status
					if(Input::get('advance_booking_id') != ''){
						// close enquiry
						$enquiry_id		=	DB::table('advance_booking')->where('id',Input::get('advance_booking_id'))->value('enquiry_id');
						Enquiry::where('id',$enquiry_id)->update(array('status'=>ENQUIRY_CLOSE_STATUS));
						DB::table('advance_booking')->where('id',Input::get('advance_booking_id'))->update(array('status'=>ADVANCE_BOOKING_DISPATCHED_STATUS,'is_deleted'=>'1'));
					}
					// chage booking status
					if(Input::get('enquiry_id') != ''){
						Enquiry::where('id',Input::get('enquiry_id'))->update(array('status'=>ENQUIRY_CLOSE_STATUS));
					}
					$vehicleDetails = DB::table('inventories')
									->leftjoin('dropdown_managers', 'inventories.model_id', '=', 'dropdown_managers.id')
									->select('inventories.*','dropdown_managers.number_of_km')
									->where('inventories.id',Input::get('vin_number'))
									->first();
					$customer 						= 	new User;
					$customer->user_role_id			=	CUSTOMER_ROLE_ID;
					$customer->dealer_id			=	$dealer_id;
					$customer->unique_id			=	'';
					$customer->custom_imei_number 	=	$vehicleDetails->imei_number;
					$customer->full_name			=	Input::get('customer_name');
					$customer->gender				=	Input::get('gender');
					$customer->email				=	Input::get('email');
					$customer->phone_number			=	Input::get('mobile_number');
					$customer->dob					=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
					$customer->booking_date			=	!empty(Input::get('booking_date')) ? date('Y-m-d',strtotime(Input::get('booking_date'))) : '0000-00-00';
					//$customer->status				=	Input::get('status');
					$customer->dealer_location_name	=	Input::get('location_name');
					$customer->city					=	Input::get('city');
					if(!empty($vehicleDetails->imei_number)){
						$customer->is_tracking_enabled					=	1;
					}
					$customer->save();	
					$user_id = $customer->id;
					$unique_id	=	'#1000'.$user_id;
					if(!empty($user_id)){
						$dealer_id				=	$this->get_dealer_id();
						$total_bookings			=	Booking::where('dealer_id', $dealer_id)->count();
						$booking_number		=	'#BOOK-000'.$total_bookings;
						Booking::where('id',$id)->update(array('booking_number'=>$booking_number));
						User::where('id',$user_id)->update(array('unique_id'=>$unique_id));
						$customerMeta 									= 	new UserMeta;
						$customerMeta->custom_imei_number  				=	$vehicleDetails->imei_number;
						$customerMeta->user_id  						=	$user_id;
						$customerMeta->vehicle_number  					=	$vehicleDetails->vin_number;
						$customerMeta->engine_number  					=	$vehicleDetails->motor_number;
						$customerMeta->chasis_number   					=	$vehicleDetails->chassis_number;
						$customerMeta->battery_voltage   			=	!empty($vehicleDetails->battery_voltage) ? $vehicleDetails->battery_voltage : '0.00';
						$customerMeta->run_kilomiter_after_full_charge  =	!empty($vehicleDetails->number_of_km) ? $vehicleDetails->number_of_km : '0';
						$customerMeta->remaining_battery_percentage   	=	0;
						$customerMeta->remaining_kilomiter    			=	0;
						$customerMeta->current_location_latitude   		=	'';
						$customerMeta->current_location_longitude   	=	'';
						$customerMeta->next_service_due_date   			=	'';
						$customerMeta->save();
					}
					$vId = Input::get('vehicle_modal');
                    $modelDetails = ModelServices::where('dropdown_manager_id',$vId)->get()->toArray();
					if(!empty($modelDetails)){
						$counter = 1;
						foreach ($modelDetails as $key => $modelDetail) {
							$retailService             = new RetailServices;
							$retailService->booking_id = $id;
							$retailService->dealer_id  = $dealer_id;
							$retailService->service_no = $modelDetail['service_no'];
							$retailService->service_days = $modelDetail['service_days'];
							$retailService->service_days_string = $modelDetail['service_days_string'];
							$retailService->service_km   = $modelDetail['service_km'];
							$retailService->service_km_string   = $modelDetail['service_km_string'];
							$retailService->service_type = $modelDetail['service_type'];
							$retailService->service_amount = $modelDetail['service_amount'];
							$retailService->last_followup_date = "0000-00-00";
							if($counter==1){
								$date     	 = strtotime("+".$modelDetail['service_days']." day");
								$service_date = date('Y-m-d', $date);
								$retailService->service_date     =   $service_date;
								$retailService->fifteen_day_before_reminder_date =  date("Y-m-d",strtotime($service_date."-15 day"));
								$retailService->seven_day_before_reminder_date   = date("Y-m-d",strtotime($service_date."-7 day"));
								$retailService->third_day_before_reminder_date   = date("Y-m-d",strtotime($service_date."-3 day"));
								$retailService->one_day_before_reminder_date   = date("Y-m-d",strtotime($service_date."-1 day"));
							}
							$retailService->save();
							$counter++;
						}
					 
					}

					DB::table('dealer_inventory')->where('vehicle_id',$vehicleDetails->id)->update(array('is_sold'=>1,'customer_id'=>$user_id));					
				}	
				Session::flash("success",trans("Retail created successfully."));
				return Redirect::to('/dealerpanel/booking-management');
			}
		}
	}
	
	public function editBooking($id = ""){
		
		$bookingDetails			=	DB::table('booking')->where('id',$id)->where("dealer_id",$dealer_id)->first(); 
		if(empty($bookingDetails)) {
			return Redirect::back();
		}	
		$dealer_id				=	$this->get_dealer_id();
		$stateList		=	DB::table('states')
								->where('status',1)
								->where('country_id',101)
								->orderBy('name','ASC')
								->pluck('name','id')
								->toArray();

		$dealerLocationName =  DB::table('dealer_location')
								->where('dealer_id',$dealer_id)
								->where('is_active',1)
								->orderBy('location_name','ASC')
								->pluck('location_name','id')
								->toArray();
		$cityList			=	DB::table('cities')
								->where('state_id',$bookingDetails->state)
								->distinct('name')
								->pluck('name','id')
								->toArray();

		$vehiclecolor =  $this->getDropDownListBySlug('vehiclecolor');
		$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		$paymentmode =  $this->getDropDownListBySlug('paymentmode');	

		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
								->where('dealer_id',$dealer_id)
								->where("is_active",1)
								->where("is_deleted",0)
								->orderBy('full_name', 'ASC')
								->pluck('full_name','id')
								->toArray();


		return View::make('dealerpanel.'.$this->model.'.edit', compact("bookingDetails",'cityList','vehiclecolor' ,'vehiclemodel','sales_consultant','stateList','paymentmode','dealerLocationName'));
	
		
	} // end editUser()

	public function updateBooking($id){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'booking_date' 					=>	 'required',
					'customer_name' 				=>	 'required',
					'email' 						=>	 'email',
					'gender' 						=>	 'required',
					'address_1' 					=>	 'required',
					'city' 							=>	 'required',
					'state' 						=>	 'required',
					'zip' 							=>	 'required',
					'mobile_number' 				=>	 'required|integer|digits:10',
					'vehicle_modal' 				=>	 'required',
					'vehicle_color' 				=>	 'required',
					'price' 						=>	 'required|numeric',
					'quantity' 						=>	 'required|numeric',
					'payment_mode' 					=>	 'required',
					'location_name' 				=>	 'required',
					'remarks' 						=>	 'required',
					'sales_consultant' 				=>	 'required',
					'status' 						=>	 'required'
				),
				array(
					'email.email'						=> 	'The email address is invalid.',
					'vehicle_modal.required' 			=>	 'The model enquired field is required.',
					'vehicle_color.required' 			=>	 'The vehicle color field is required.',
					'dob.required' 						=>	 'The date of birth is required.',
					'zip.required' 						=>	 'The zipcode field is required.',
					'status.required' 					=>	 'The retail status field is required.',
					"mobile_number.integer"					=>	trans("Phone number must have a numeric value."),
					"mobile_number.digits"					=>	trans("Phone number must have 10 digits."),
				)
			
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$totalAmount = 0;
				$totalPrice  = 0;
				$taxAmount   = 0;
				
				$price 			= 	!empty(Input::get('price'))?Input::get('price'):'0';
				$quantity 		= 	!empty(Input::get('quantity'))?Input::get('quantity'):'1';
				if(!empty($price) && !empty($quantity)){
					$totalPrice 	= 	$price*$quantity;
					$texPercent  	= 	TAX_PERCENT;
					$taxAmount 		= 	(($totalPrice*$texPercent)/100);
					$totalAmount    =  ($taxAmount+$totalPrice);
				}
				$dealer_id				=	$this->get_dealer_id();
				$booking							= 	Booking::find($id);
				$booking->booking_date				=	!empty(Input::get('booking_date')) ? date('Y-m-d',strtotime(Input::get('booking_date'))) : '0000-00-00';
				$booking->vehicle_modal				=	Input::get('vehicle_modal');
				$booking->vehicle_color				=	Input::get('vehicle_color');
				$booking->customer_name				=	Input::get('customer_name');
				$booking->email						=	Input::get('email');
				$booking->gender					=	Input::get('gender');	
				$booking->dob						=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
				$booking->address_1					=	Input::get('address_1');
				$booking->address_2					=	Input::get('address_2');
				$booking->city						=	Input::get('city');
				$booking->state						=	Input::get('state');
				$booking->zip						=	Input::get('zip');
				$booking->gst_in					=	Input::get('gst_in');
				$booking->mobile_number				=	Input::get('mobile_number');
				$booking->price						=	Input::get('price');
				$booking->quantity					=	Input::get('quantity');
				$booking->tax_amount				=	$taxAmount;
				$booking->tax_percent				=	TAX_PERCENT;
				$booking->total_amount				=	$totalAmount;
				$booking->location_name				=	Input::get('location_name');
				$booking->place_supply				=	Input::get('place_supply');
				
				$booking->payment_mode				=	Input::get('payment_mode');
				$booking->status					=	Input::get('status');
				$booking->remarks					=	Input::get('remarks');
				$booking->sales_consultant			=	Input::get('sales_consultant');

				$booking->save();

				Session::flash('flash_notice', trans("Retail has been updated successfully.")); 
				return Redirect::to('/dealerpanel/booking-management');
			}
		}
	}

	//function for delete booking
	public function deleteBooking($id = ''){
		$dealer_id				=	$this->get_dealer_id();
		$bookingDetails			=	DB::table('booking')->where('id',$id)->where("dealer_id",$dealer_id)->first(); 
		if(empty($bookingDetails)) {
			return Redirect::back();
		}
		if($bookingDetails){	
			$Model					=	Booking::where('id',$id)->update(array('is_deleted'=>1));
			DB::table('dealer_inventory')->where('vehicle_id',$bookingDetails->vehicle_id)->update(array('is_sold'=>0,'customer_id'=>""));
			Session::flash('flash_notice',trans("Retail has been deleted successfully.")); 
		}
		return Redirect::back();
	}//end function for delete booking 


	//function for view booking
	public function viewBooking($booking_id=""){
		$dealer_id				=	$this->get_dealer_id();
		$bookingDetails			=	DB::table('booking')
									->where("id",$booking_id)
									->where("dealer_id",$dealer_id)
									->select("booking.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = booking.location_name) as location_name"),
									DB::raw("(SELECT name FROM states WHERE states.id = booking.state) as state"),
									DB::raw("(SELECT name FROM cities WHERE id = booking.city) as city"),
									DB::raw("(SELECT full_name FROM users WHERE users.id = booking.sales_consultant) as sales_consultant"))
									->first();
		if(empty($bookingDetails)){
			return Redirect::back();
		}
		
		$totalFinalAmount = 0;
		$totalFinalTax = 0;
		$totalTaxableAmount = 0;
		
		if($bookingDetails->total_amount != ''){
			$amount	=	$bookingDetails->total_amount;
			$tax	=	$bookingDetails->tax_amount;
			
			$price	=	$bookingDetails->price;
			if(!empty($tax)){
				$totalTaxableAmount += $price;
			}
			$totalFinalAmount 	+= $amount;
			$totalFinalTax 		+= $tax;

		}
		
		
		
		
		if($bookingDetails->insurance != ''){
			$amount	=	$bookingDetails->insurance;
			$tax	=	$bookingDetails->tax_insurance;
			if($tax !=''){
				$tax_amount= ($amount*$tax)/100;
				$total_amount	=	$amount+$tax_amount;
				
				$totalTaxableAmount += $amount;
			}else{
				$tax_amount		= 0;
				$total_amount	=	$amount;
			}
			$bookingDetails->insurance_tax_amount	=	$tax_amount;
			$bookingDetails->insurance_total_amount	=	$total_amount;
			$totalFinalAmount 	+= $total_amount;
			$totalFinalTax 		+= $tax_amount;

		}
		
		
		if($bookingDetails->helmet != ''){
			$amount	=	$bookingDetails->helmet;
			$tax	=	$bookingDetails->tax_helmet;
			if($tax !=''){
				$tax_amount= ($amount*$tax)/100;
				$total_amount	=	$amount+$tax_amount;
				
				$totalTaxableAmount += $amount;
			}else{
				$tax_amount		= 0;
				$total_amount	=	$amount;
			}
			$bookingDetails->helmet_tax_amount		=	$tax_amount;
			$bookingDetails->helmet_total_amount	=	$total_amount;
			
			$totalFinalAmount 	+= $total_amount;
			$totalFinalTax 		+= $tax_amount;
		}
		if($bookingDetails->registration_certificate != ''){
			$amount	=	$bookingDetails->registration_certificate;
			$tax	=	$bookingDetails->tax_registration_certificate;
			if($tax !=''){
				$tax_amount= ($amount*$tax)/100;
				$total_amount	=	$amount+$tax_amount;
				
				$totalTaxableAmount += $amount;
			}else{
				$tax_amount		= 0;
				$total_amount	=	$amount;
			}
			$bookingDetails->registration_certificate_tax_amount		=	$tax_amount;
			$bookingDetails->registration_certificate_total_amount	=	$total_amount;
			
			$totalFinalAmount 	+= $total_amount;
			$totalFinalTax 		+= $tax_amount;
		}
		if($bookingDetails->number_plate != ''){
			$amount	=	$bookingDetails->number_plate;
			$tax	=	$bookingDetails->tax_number_plate;
			if($tax !=''){
				$tax_amount= ($amount*$tax)/100;
				$total_amount	=	$amount+$tax_amount;
				
				$totalTaxableAmount += $amount;
			}else{
				$tax_amount		= 0;
				$total_amount	=	$amount;
			}
			
			$bookingDetails->number_plate_tax_amount		=	$tax_amount;
			$bookingDetails->number_plate_total_amount	=	$total_amount;
			
			$totalFinalAmount 	+= $total_amount;
			$totalFinalTax 		+= $tax_amount;
		}
		
		
									
		$vehicaleDetails = DB::table('inventories')->where('id',$bookingDetails->vehicle_id)->select('inventories.*',DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))->first();	
			
		$batteryDetails 	= 	DB::table('battery_details')->where('vehicle_id',$vehicaleDetails->id)->get()->toArray();	
		$otherCharges		=	BookingOtherCharge::where('booking_id',$bookingDetails->id)->get();
		
		//echo '<pre>'; print_r($otherCharges); die;	
							
		return View::make('dealerpanel.'.$this->model.'.view', compact("bookingDetails","vehicaleDetails","batteryDetails",'otherCharges'));
		
	}//end function for view booking

	
	public function exportAdvanceBookingToExcel(){
		$searchData			=	Session::get('advance_booking_search_data');
		$DB 					= 	Booking::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
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
					if($fieldName == 'booking_date_start' || $fieldName == 'booking_date_end'  ){
						if($fieldName == 'booking_date_start'){  
							$DB->where('booking.booking_date','>=',$fieldValue);
						}
						if($fieldName == 'booking_date_end'){  
							$DB->where('booking.booking_date','<=',$fieldValue);
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
									->select("booking.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = booking.payment_mode) as payment_mode"),
									DB::raw("(SELECT name FROM states WHERE states.id = booking.state) as state"),
									DB::raw("(SELECT name FROM cities WHERE id = booking.city) as city"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = booking.location_name) as location_name"),
									DB::raw("(SELECT full_name FROM users WHERE id = booking.sales_consultant) as sales_consultant"))
									->orderBy($sortBy, $order)
									->get()->toArray();						
									
												
		$thead = array();
		
		$thead[]		= array("Invoice-No.","Retail Date/Time","Customer Name","Gender","Date of Birth","Email Address","Mobile Number","Address 1","Address 2","City","State","Zipcode","Sales Consultant","Payment Mode","Location Name","Place of Supply","GST IN Number","Vehicle Modal","Vehicle Color","VIN Number","Motor Number","Chassis Number","IMEI Number","Batteries","Price(On Road)","Tax (On Road)","Total Price (On Road)","Amount(Invoice)","Tax(Invoice)","Total Amount With Tax (Invoice)");
		
		//echo '<pre>'; print_r($result); die;
		
		if(!empty($result)) {
			foreach($result as $record) {
				
				
				$vehicaleDetails = DB::table('inventories')->where('id',$record['vehicle_id'])->select('inventories.*',DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))->first();	
			
				$batteryDetails 	= 	DB::table('battery_details')->where('vehicle_id',$vehicaleDetails->id)->get()->toArray();	
				$otherCharges		=	BookingOtherCharge::where('booking_id',$record['id'])->get();
				
				$booking_number					=	!empty($record['booking_number'])?$record['booking_number']:'';
				$booking_date					=	!empty($record['booking_date'])?date(Config::get("Reading.date_format") , strtotime($record['booking_date'])):'';
				
				$customer_name					=	!empty($record['customer_name'])?$record['customer_name']:'';
				$genderID							=   !empty($record['gender']) ?$record['gender']:'';
				if($genderID == MALE){
					$gender = 'Male';
				}else{
					$gender = 'Female';
				}
				
				$dob							=	!empty($record['dob'])?date(Config::get("Reading.date_format") , strtotime($record['dob'])):'';
				$email							=	!empty($record['email'])?$record['email']:'';
				$mobile_number					=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$address_1						=	!empty($record['address_1'])?$record['address_1']:'';
				$address_2						=	!empty($record['address_2'])?$record['address_2']:'';
				$city							=	!empty($record['address_2'])?$record['city']:'';
				$state							=	!empty($record['address_2'])?$record['state']:'';
				$zip							=	!empty($record['zip'])?$record['zip']:'';
				$sales_consultant				=	!empty($record['sales_consultant'])?$record['sales_consultant']:'';
				$payment_mode					=	!empty($record['payment_mode'])?$record['payment_mode']:'';
				$location_name					=	!empty($record['location_name'])?$record['location_name']:'';
				$place_supply					=	!empty($record['place_supply'])?$record['place_supply']:'';
				$gst_in							=	!empty($record['gst_in'])?$record['gst_in']:'';
				$price							=	!empty($record['price'])?$record['price']:'';
				$tax_amount						=	!empty($record['tax_amount'])?$record['tax_amount']:'';
				$total_amount					=	!empty($record['total_amount'])?$record['total_amount']:'';
				
				
				$vehicle_modal 			= '';
				$vehicle_color 			= '';
				$vin_number 			= '';
				$motor_number 			= '';
				$chassis_number 		= '';
				$imei_number			= '';
				if(!empty($vehicaleDetails)){
					$vehicle_modal 			= $vehicaleDetails->vehicle_modal;
					$vehicle_color 			= $vehicaleDetails->vehicle_color;
					$vin_number 			= $vehicaleDetails->vin_number;
					$motor_number 			= $vehicaleDetails->motor_number;
					$chassis_number 		= $vehicaleDetails->chassis_number;
					$imei_number			= $vehicaleDetails->imei_number;
				}
				$batteryNumber = '';
				if(!empty($batteryDetails)){
					$btArray = array();
					foreach($batteryDetails as $btDetail){
						$btArray[] = $btDetail->battery_number;
					}
					$batteryNumber = implode(',',$btArray);
				}
				
				
				$totalAmountInvoice   				=   0;
				$totalTaxAmount    					=   0;
				$totalAmountWithTax   				=   0;
				
				$totalAmountInvoice   				+=   $record['price'];
				$totalTaxAmount    					+=   $record['tax_amount'];
				$totalAmountWithTax   				+=   $record['total_amount'];
				if($record['insurance'] != ''){
					$amount	=	$record['insurance'];
					$tax	=	$record['tax_insurance'];
					if($tax !=''){
						$tax_amount= ($amount*$tax)/100;
						$total_amount	=	$amount+$tax_amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$totalAmountInvoice		+=	$amount;
					$totalTaxAmount			+=	$tax_amount;
					$totalAmountWithTax		+=	$total_amount;

				}
				if($record['helmet'] != ''){
					$amount	=	$record['helmet'];
					$tax	=	$record['tax_helmet'];
					if($tax !=''){
						$tax_amount= ($amount*$tax)/100;
						$total_amount	=	$amount+$tax_amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$totalAmountInvoice		+=	$amount;
					$totalTaxAmount			+=	$tax_amount;
					$totalAmountWithTax		+=	$total_amount;
				}
				
				if($record['registration_certificate'] != ''){
					$amount	=	$record['registration_certificate'];
					$tax	=	$record['tax_registration_certificate'];
					if($tax !=''){
						$tax_amount= ($amount*$tax)/100;
						$total_amount	=	$amount+$tax_amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$totalAmountInvoice		+=	$amount;
					$totalTaxAmount			+=	$tax_amount;
					$totalAmountWithTax		+=	$total_amount;
				}
				if($record['number_plate'] != ''){
					$amount	=	$record['number_plate'];
					$tax	=	$record['tax_number_plate'];
					if($tax !=''){
						$tax_amount= ($amount*$tax)/100;
						$total_amount	=	$amount+$tax_amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$totalAmountInvoice		+=	$amount;
					$totalTaxAmount			+=	$tax_amount;
					$totalAmountWithTax		+=	$total_amount;
				}
				if(!empty($otherCharges)){
					foreach($otherCharges as $key=>$value){
						$totalAmountInvoice 			+= $value->amount;
						$totalTaxAmount 		+= $value->tax_amount;
						$totalAmountWithTax 	+= $value->total_amount;
					}
				}
				$thead[]		= array($booking_number,$booking_date,$gender,$dob,$email,$mobile_number,$address_1,$address_2,$city,$state,$zip,$sales_consultant,$payment_mode,$location_name,$location_name,$place_supply,$gst_in,$vehicle_modal,$vehicle_color,$vin_number,$motor_number,$chassis_number,$imei_number,$batteryNumber,$price,$tax_amount,$total_amount,$totalAmountInvoice,$totalTaxAmount,$totalAmountWithTax);
			}
		}				
		return  View::make('dealerpanel.'.$this->model.'.export_excel', compact('thead'));
	}


	/**
	* Function for get details of advance booking by mobile number
	*
	* @param null
	*
	* @return view page. 
	*/
	public function getAdvanceBooking(){
		$mobile_number	=	Input::get('number');
		$response		=	array();
		if($mobile_number != ''){
			$get_all_advance_booking 		=	DB::table('advance_booking')
														->where('mobile_number', $mobile_number)
														->where('advance_booking.is_deleted', 0)
														->leftjoin('dropdown_managers as model', 'advance_booking.vehicle_modal', '=', 'model.id')
														->leftjoin('dropdown_managers as color', 'advance_booking.vehicle_color', '=', 'color.id')
														->select('advance_booking.id','advance_booking.booking_number', 'advance_booking.booking_date', 'advance_booking.vehicle_modal', 'advance_booking.vehicle_color','advance_booking.advance_booking_amount', 'model.name as model_name', 'color.name as color_name')
														->get()->toArray();
			if(!empty($get_all_advance_booking)){
				$response['success']	=	1;
				$response['data']		=	$get_all_advance_booking;
			}else{
				$response['data']		=	array();
			}
		}else{
			$response		=	array(
				'success'=>0,
				'data'	=>array()
			);
		}
			
			return View::make('dealerpanel.'.$this->model.'.show_advance_booking', compact('response'));
		//return $response;die;
	}

	/**
	* Function for get details of advance booking by mobile number
	*
	* @param null
	*
	* @return view page. 
	*/
	public function findAdvanceBookingDetails(){
		$id			=	Input::get('id');
		$response	=	array();
		if($id != ''){
			$booking_details 		=	DB::table('advance_booking')
												->where('id', $id)
												->where('is_deleted', 0)
												->first();
			if(!empty($booking_details)){
				$response['success']	=	1;
				$response['data']		=	$booking_details;
			}else{
				$response['success']	=	0;
				$response['data']		=	array();
			}
		}else{
			$response['success']	=	0;
			$response['data']		=	array();	
		}
		return $response;
	}
	
	/**
	* Function for get vehicles from dealer inventory
	*
	* @param null 
	*
	* @return options page. 
	*/
	public function getVehicleFromInventry(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$vehicle_modal		=	Input::get('vehicle_modal');
			$vehicle_color	=	Input::get('vehicle_color');
			$response		=	array();
			//$dealerVehicales = DB::table('dealer_inventory')->where('dealer_id',$dealer_id)->where('is_sold',0)->pluck('vehicle_id','vehicle_id');
			
			$dealer_id				=	$this->get_dealer_id();
			$vehicleDetails = DB::table('inventories')
								->where('model_id',$vehicle_modal)
								->where('color_id',$vehicle_color)
								->leftjoin('dealer_inventory','inventories.id','=','dealer_inventory.vehicle_id')
								->where('dealer_inventory.is_sold',0)
								->where('dealer_inventory.dealer_id',$dealer_id)
								->pluck('vin_number','inventories.id')->toArray();
			
			$optionHtml = '';
			if(!empty($vehicleDetails)){
				$optionHtml = '<option value="">Select Vehicle</option>';
				foreach($vehicleDetails as $v_value=>$v_vin){
					$optionHtml .= '<option value="'.$v_value.'">'.$v_vin.'</option>';
				}
			}
			if($optionHtml){
				$response['success']	=	1;
				$response['option']	=	$optionHtml;
			}else{
				$response['success']	=	0;
				$response['option']	=	'';
			}
			return Response::json($response);die;
		}
	}
	/**
	* Function for get vehicle details
	*
	* @param null
	*
	* @return index page. 
	*/
	public function getVehicleDetail(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$vehicle_id		=	Input::get('vehicle_id');
			$vehicleDetails = DB::table('inventories')->where('id',$vehicle_id)->select('inventories.motor_number','inventories.hsn_sac','inventories.chassis_number','inventories.imei_number')->first();
			$batteryDetails = DB::table('battery_details')->where('vehicle_id',$vehicle_id)->get()->toArray();
			
			//echo '<pre>'; print_r($batteryDetails); die;
			if(!empty($vehicleDetails) && !empty($batteryDetails)){
				return View::make('dealerpanel.'.$this->model.'.vehicleDetails', compact('vehicleDetails' ,'batteryDetails'));
			}
		}
	}

	/**
	* Function for get vehicle details
	*
	* @param null
	*
	* @return index page. 
	*/
	public function getVehicleColor(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData				=	Input::all();
		if(!empty($thisData)){
			$vehicle_modal		=	$thisData['vehicle_modal'];
			$response			=	array();	
			$dealer_id				=	$this->get_dealer_id();		
			//$dealerVehicales = DB::table('dealer_inventory')->where('dealer_id',$dealer_id)->where('is_sold',0)->pluck('vehicle_id','vehicle_id');
			$vehicleColors 		= 	DB::table('inventories')
											->where('model_id',$vehicle_modal)
											->leftjoin('dropdown_managers', 'inventories.color_id', '=', 'dropdown_managers.id')
											->leftjoin('dealer_inventory', 'inventories.id', '=', 'dealer_inventory.vehicle_id')
											->where('dealer_inventory.is_sold',0)
											->where('dealer_inventory.dealer_id',$dealer_id)
											->select('dropdown_managers.id', 'dropdown_managers.name')
											->orderBy('dropdown_managers.name')
											->distinct()
											->get()->toArray();
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
	* Function for get vehicle details
	*
	* @param null
	*
	* @return index page. 
	*/
	public function generateInvoice($booking_id){
		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',101)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray();
		$bookingDetails	=	Booking::where('id',$booking_id)->select('gst_in','price','booking_number','customer_name', 'address_1', 'address_2', 'state', 'city', 'zip')->first();
		$cityList		=	DB::table('cities')
									->where('state_id',$bookingDetails->state)
									->distinct('name')
									->pluck('name','id')
									->toArray();
		$tax_array		=	TaxManager::where('is_active',1)->where('is_deleted',0)->orderBy('tax_value','asc')->pluck('tax_name', 'tax_value');
		return View::make('dealerpanel.'.$this->model.'.generate_invoice', compact('tax_array','booking_id','bookingDetails','stateList','cityList'));
	}

	/**
	* Function for get vehicle details
	*
	* @param null
	*
	* @return index page. 
	*/
	public function saveInvoice(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData				=	Input::all();
		//dd($thisData);
		$validator 				=	Validator::make(
			Input::all(),
			array(
				'customer_name' 				=>	 'required',
				'address_1' 					=>	 'required',
				'city' 							=>	 'required',
				'state' 						=>	 'required',
				'zip' 							=>	 'required',
			),
			array(
			)
		);
		if ($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{ 
			$dealer_id				=	$this->get_dealer_id();
			// store customer data in a array
			$InputArr['customer_name']	=	$thisData['customer_name'];
			$InputArr['address_1']		=	$thisData['address_1'];
			$InputArr['address_2']		=	$thisData['address_2'];
			$InputArr['zip']			=	$thisData['zip'];
			$InputArr['state']			=	DB::table('states')->where('id', $thisData['state'])->value('name');
			$InputArr['city']			=	DB::table('cities')->where('id', $thisData['city'])->value('name');
			$InputArr['gst_in']			=	$thisData['gst_in'];

			$findBooking				=	Booking::find($thisData['booking_id']);
			
			if(empty($findBooking)){
				return Redirect::back();
			}
			// save tax avail on vehicle amount
		
			/* if(!empty($thisData['tax_ex_showroom_price']) && !empty($thisData['ex_showroom_price'])){
				$taxPercent		=	$thisData['tax_ex_showroom_price'];
				$taxPercentsgst	=	$thisData['tax_ex_showroom_price_sgst'];
				$totalPrice 	= 	$thisData['ex_showroom_price'];
				//$taxAmount 		= 	(($totalPrice*$taxPercent)/100);
				$taxAmount 		= 	(($totalPrice*($taxPercent+$taxPercentsgst))/100);
				$totalAmount    =  	($taxAmount+$totalPrice);
				$findBooking->tax_amount			=	$taxAmount;
				$findBooking->tax_percent			=	$taxPercent;
				$findBooking->tax_percent_sgst		=	$taxPercentsgst;
				$findBooking->total_amount			=	$totalAmount;
			} */
			
		     	$taxPercent		=	$thisData['tax_ex_showroom_price'];
				$taxPercentsgst	=	$thisData['tax_ex_showroom_price_sgst'];
				$taxPercentcgst	=	$thisData['tax_ex_showroom_price_cgst'];
				$totalPrice 	= 	$thisData['ex_showroom_price'];
				dd($request->all());
				//$taxAmount 		= 	(($totalPrice*$taxPercent)/100);
				$taxAmount 		= 	(($totalPrice*($taxPercent+$taxPercentsgst+$taxPercentcgst))/100);
				$totalAmount    =  	($taxAmount+$totalPrice);
				$findBooking->tax_amount			=	$taxAmount;
				$findBooking->tax_percent			=	$taxPercent;
				$findBooking->tax_percent_sgst		=	$taxPercentsgst;
				$findBooking->tax_percent_cgst		=	$taxPercentcgst;
				$findBooking->total_amount			=	$totalAmount;
				
			// save charges data in database
			$findBooking->number_plate					=	$thisData['number_plate'];
			$findBooking->insurance						=	$thisData['insurance'];
			// $findBooking->other_charges					=	$thisData['other_charges'];
			$findBooking->registration_certificate		=	$thisData['registration_certificate'];
			$findBooking->helmet						=	$thisData['helmet'];
		//	if(!empty($thisData['number_plate'])){
				$findBooking->tax_number_plate				=	$thisData['tax_number_plate'];
				$findBooking->tax_number_plate_sgst			=	$thisData['tax_number_plate_sgst'];
				$findBooking->tax_number_plate_cgst			=	$thisData['tax_number_plate_cgst'];
		//	}
		//	if(!empty($thisData['insurance'])){
				$findBooking->tax_insurance					=	$thisData['tax_insurance'];
				$findBooking->tax_insurance_sgst			=	$thisData['tax_insurance_sgst'];
				$findBooking->tax_insurance_cgst			=	$thisData['tax_insurance_cgst'];
		//	}
		//	if(!empty($thisData['registration_certificate'])){
				$findBooking->tax_registration_certificate   	=	$thisData['tax_registration_certificate'];
				$findBooking->tax_registration_certificate_sgst	=	$thisData['tax_registration_certificate_sgst'];
				$findBooking->tax_registration_certificate_cgst	=	$thisData['tax_registration_certificate_cgst'];
		//	}
		//	if(!empty($thisData['helmet'])){
				$findBooking->tax_helmet					    =	$thisData['tax_helmet'];
				$findBooking->tax_helmet_sgst					=	$thisData['tax_helmet_sgst'];
				$findBooking->tax_helmet_cgst					=	$thisData['tax_helmet_cgst'];
		//	}
			
			$findBooking->save();
			// save_other_charges
			if(!empty($thisData['numberCount'])){
				BookingOtherCharge::where('booking_id',$thisData['booking_id'])->delete();
				foreach($thisData['numberCount'] as &$i){
					if($thisData['name_other_charges_'.$i] != '' && $thisData['other_charges_'.$i] != ''){
						$taxPercent	=	$thisData['tax_other_charges_'.$i];	
						$taxPercentsgst	=  $thisData['tax_other_charges_'.$i.'_sgst']; 
						$taxPercentcgst	=  $thisData['tax_other_charges_'.$i.'_cgst']; 	
						$amount		=	$thisData['other_charges_'.$i];
						if($taxPercent != '' || $taxPercentsgst != '' ||  $taxPercentcgst != '' ){
							$TaxAmount	=	($amount*($taxPercent+$taxPercentsgst+$taxPercentcgst))/100;
						}else{
							$TaxAmount	=	0;
						}

						$TotalAmount	=	$amount+$TaxAmount;
						if(!empty($thisData['other_charges_'.$i])){
							$obj				=	new BookingOtherCharge;
							$obj->booking_id 	=	$thisData['booking_id'];
							$obj->name_type 	=	$thisData['name_other_charges_'.$i];
							$obj->amount 		=	$thisData['other_charges_'.$i];
							$obj->tax_percent 	=	$thisData['tax_other_charges_'.$i];
							$obj->tax_percent_sgst 	= $thisData['tax_other_charges_'.$i.'_sgst'];
							$obj->tax_percent_cgst 	= $thisData['tax_other_charges_'.$i.'_cgst'];
							$obj->tax_amount 	=	$TaxAmount;
							$obj->total_amount 	=	$TotalAmount;
							$obj->save();
						}
						
					}
				}
			}
			if($findBooking->id){
				$bookingDetails			=	DB::table('booking')
														->where('booking.id',$thisData['booking_id'])
														->where("booking.dealer_id",$dealer_id)
														->leftjoin('dropdown_managers as vehiclemodal', 'vehiclemodal.id','=','booking.vehicle_modal')
														->leftjoin('dropdown_managers as vehiclecolor', 'vehiclecolor.id','=','booking.vehicle_color')
														->select('booking.*', 'vehiclemodal.name as vehicle_modal_name','vehiclecolor.name as vehicle_color_name',DB::raw("(SELECT name FROM states WHERE states.id = booking.state) as state"),
														DB::raw("(SELECT name FROM cities WHERE id = booking.city) as city"))
														->first(); 
				if(empty($bookingDetails)) {
					return Redirect::back();
				}
				$totalFinalAmount = 0;
				$totalFinalTax = 0;
				$totalTaxableAmount = 0;
				
				if($bookingDetails->total_amount != ''){
					$amount	=	$bookingDetails->total_amount;
					$tax	=	$bookingDetails->tax_amount;
					
					$price	=	$bookingDetails->price;
					if(!empty($tax)){
						$totalTaxableAmount += $price;
					}
					$totalFinalAmount 	+= $amount;
					$totalFinalTax 		+= $tax;

				}
				if($bookingDetails->insurance != ''){
					$amount   	=	$bookingDetails->insurance;
					$tax	    =	$bookingDetails->tax_insurance;
					$tax_ins	=	$bookingDetails->tax_insurance_sgst;
					$tax_inscg	=	$bookingDetails->tax_insurance_cgst;
					if($tax !='' || $tax_ins !=''   ||  $tax_inscg !=''){
						$tax_amount= ($amount*($tax+$tax_ins+$tax_inscg))/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$bookingDetails->insurance_tax_amount	=	$tax_amount;
					$bookingDetails->insurance_total_amount	=	$total_amount;
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;

				}
				if($bookingDetails->helmet != ''){
					$amount	=	$bookingDetails->helmet;
					$tax	=	$bookingDetails->tax_helmet;
					$taxhemlet	=	$bookingDetails->tax_helmet_sgst;
					$taxhemletcg	=	$bookingDetails->tax_helmet_cgst;

					if($tax !='' || $taxhemlet !='' || $taxhemletcg !='' ){
						$tax_amount= ($amount*($tax+$taxhemlet+$taxhemletcg))/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$bookingDetails->helmet_tax_amount		=	$tax_amount;
					$bookingDetails->helmet_total_amount	=	$total_amount;
					
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
				}
				if($bookingDetails->registration_certificate != ''){
					$amount	=	$bookingDetails->registration_certificate;
					$tax	=	$bookingDetails->tax_registration_certificate;
					$taxrto	=	$bookingDetails->tax_registration_certificate_sgst;
					$taxrtocg	=	$bookingDetails->tax_registration_certificate_cgst;
					if($tax !=''  || $taxrto !='' ||  $taxrtocg !=''){
						$tax_amount= ($amount*($tax+$taxrto+$taxrtocg))/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$bookingDetails->registration_certificate_tax_amount		=	$tax_amount;
					$bookingDetails->registration_certificate_total_amount	=	$total_amount;
					
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
				}
				if($bookingDetails->number_plate != ''){
					$amount	=	$bookingDetails->number_plate;
					$tax	=	$bookingDetails->tax_number_plate;
					$taxnumber	=	$bookingDetails->tax_number_plate_sgst;
					$taxnumbercg	=	$bookingDetails->tax_number_plate_cgst;
					if($tax !='' ||  $taxnumber !=''    || $taxnumbercg !=''   ){
						$tax_amount= ($amount*($tax+$taxnumber+$taxnumbercg))/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					
					$bookingDetails->number_plate_tax_amount		=	$tax_amount;
					$bookingDetails->number_plate_total_amount	=	$total_amount;
					
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
				}
				// echo "<pre>";print_r($bookingDetails);die;
				$vehicleDetails = DB::table('inventories')->where('id',$bookingDetails->vehicle_id)->select('inventories.*',DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))->first();
				$otherCharges	=	BookingOtherCharge::where('booking_id',$thisData['booking_id'])->get();
				
				if(!empty($otherCharges)){
					foreach($otherCharges as $key=>$value){
						$tax_amount 	= $value->tax_amount;	
						$total_amount 	= $value->total_amount;	
						$amount 		= $value->amount;
						
						
/* $taxPercent	=	$thisData['tax_other_charges_'.$i];	
							$taxPercentsgst	=  $thisData['tax_other_charges_'.$i.'_sgst']; 
							$taxPercentcgst	=  $thisData['tax_other_charges_'.$i.'_cgst']; 	
							$amount		=	$thisData['other_charges_'.$i];
							if($taxPercent != '' ||  $taxPercentsgst != ''  ||   $taxPercentcgst != ''   ){
								$TaxAmount	=	($amount*($taxPercent+$taxPercentsgst+$taxPercentcgst))/100;
							}else{
								$TaxAmount	=	0;
							}
							$TotalAmount	=	$amount+$TaxAmount; */


						
						if(!empty($tax_amount)){
							$totalTaxableAmount += $amount;
						}
						
						$totalFinalAmount 	+= $total_amount;
						$totalFinalTax 		+= $tax_amount;
					}
				}
				$bookingDetails->final_tax_amount		=	$totalFinalTax;
				$bookingDetails->final_amount			=	$totalFinalAmount;
				$bookingDetails->taxable_amount			=	$totalTaxableAmount;

				
				$dealerDetails = DB::table('users')
										->where('users.id',$bookingDetails->dealer_id)
										->leftjoin('states', 'users.state_id', '=', 'states.id')
										->select('users.*', 'states.name as state_name')
										->first();
				// get total amount in words
				$amount_in_words	=	$this->convert_number_into_words($bookingDetails->final_amount);
				// get tax amount in words
				$tax_amount			=	$this->convert_number_into_words($bookingDetails->final_tax_amount);
				// create file name and path
				$fileName			=	time().'-invoice-'.$bookingDetails->id.'.pdf';
				$newCategory     	= 	strtoupper(date('M'). date('Y'))."/";
				$invoicePath		=	INVOICE_ROOT_PATH.$newCategory;
				if(!File::exists($invoicePath)) {
					File::makeDirectory($invoicePath, $mode = 0777,true);
				}
				$fullFileName		=	$newCategory.$fileName;
				// file name in booking table
				if($invoicePath != ''){
					Booking::where('id',Input::get('booking_id'))->update(array('invoice_name'=>"$fullFileName"));
				}
				$actual_total_price	=	$bookingDetails->price+$findBooking->number_plate+$findBooking->insurance+$findBooking->other_charges+$findBooking->registration_certificate+$findBooking->helmet;
			}
		}
		
		return View::make('dealerpanel.'.$this->model.'.view_invoice_pdf', compact('bookingDetails','dealerDetails', 'amount_in_words', 'tax_amount','vehicleDetails','invoicePath','fileName','InputArr','otherCharges'));
	}

	

	public function previewInvoice(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData				=	Input::all();
     //  dd($thisData);
		$validator 				=	Validator::make(
			Input::all(),
			array(
				'customer_name' 				=>	 'required',
				'address_1' 					=>	 'required',
				'city' 							=>	 'required',
				'state' 						=>	 'required',
				'zip' 							=>	 'required',
			),
			array(
			)
		);
		if ($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{ 
			$dealer_id				=	$this->get_dealer_id();
			// store customer data in a array
			$InputArr['customer_name']	=	$thisData['customer_name'];
			$InputArr['address_1']		=	$thisData['address_1'];
			$InputArr['address_2']		=	$thisData['address_2'];
			$InputArr['zip']			=	$thisData['zip'];
			$InputArr['state']			=	DB::table('states')->where('id', $thisData['state'])->value('name');
			$InputArr['city']			=	DB::table('cities')->where('id', $thisData['city'])->value('name');
			$InputArr['gst_in']			=	$thisData['gst_in'];

			$findBooking				=	Booking::find($thisData['booking_id']);
			
			if(empty($findBooking)){
				return Redirect::back();
			}
			
		
			if($findBooking->id){
				$bookingDetails			=	DB::table('booking')
														->where('booking.id',$thisData['booking_id'])
														->where("booking.dealer_id",$dealer_id)
														->leftjoin('dropdown_managers as vehiclemodal', 'vehiclemodal.id','=','booking.vehicle_modal')
														->leftjoin('dropdown_managers as vehiclecolor', 'vehiclecolor.id','=','booking.vehicle_color')
														->select('booking.*', 'vehiclemodal.name as vehicle_modal_name','vehiclecolor.name as vehicle_color_name',DB::raw("(SELECT name FROM states WHERE states.id = booking.state) as state"),
														DB::raw("(SELECT name FROM cities WHERE id = booking.city) as city"))
														->first(); 
				if(empty($bookingDetails)) {
					return Redirect::back();
				}


				
				$bookingDetails->tax_number_plate				=	$thisData['tax_number_plate'];
				$bookingDetails->tax_number_plate_sgst			=	$thisData['tax_number_plate_sgst'];
				$bookingDetails->tax_number_plate_cgst			=	$thisData['tax_number_plate_cgst'];

				$bookingDetails->tax_insurance			    =	$thisData['tax_insurance'];
				$bookingDetails->tax_insurance_sgst			=	$thisData['tax_insurance_sgst'];
				$bookingDetails->tax_insurance_cgst			=	$thisData['tax_insurance_cgst'];
		
				$bookingDetails->tax_registration_certificate   	=	$thisData['tax_registration_certificate'];
				$bookingDetails->tax_registration_certificate_sgst	=	$thisData['tax_registration_certificate_sgst'];
				$bookingDetails->tax_registration_certificate_cgst	=	$thisData['tax_registration_certificate_cgst'];
	
				$bookingDetails->tax_helmet					    =	$thisData['tax_helmet'];
				$bookingDetails->tax_helmet_sgst				=	$thisData['tax_helmet_sgst'];
				$bookingDetails->tax_helmet_cgst				=	$thisData['tax_helmet_cgst'];
				
				
				$totalFinalAmount = 0;
				$totalFinalTax = 0;
				$totalTaxableAmount = 0;


				$taxPercent		=	$thisData['tax_ex_showroom_price'];
				$taxPercentsgst	=	$thisData['tax_ex_showroom_price_sgst'];
				$taxPercentcgst	=	$thisData['tax_ex_showroom_price_cgst'];
				$totalPrice 	= 	$thisData['ex_showroom_price'];
				$taxAmount 		= 	(($totalPrice*($taxPercent+$taxPercentsgst+$taxPercentcgst))/100);
				$totalAmount    =  	($taxAmount+$totalPrice);
				

				$bookingDetails->total_amount		    =	$totalAmount;
				$bookingDetails->tax_amount			    =	$taxAmount;
				$bookingDetails->tax_percent			=	$taxPercent;
				$bookingDetails->tax_percent_sgst		=	$taxPercentsgst;
				$bookingDetails->tax_percent_cgst		=	$taxPercentcgst;
				
				if($bookingDetails->total_amount != ''){
					$amount	=	$bookingDetails->total_amount;
					$tax	=	$bookingDetails->tax_amount;
                	$price	=	$bookingDetails->price;
					if(!empty($tax)){
						$totalTaxableAmount += $price;
					}
					$totalFinalAmount 	+= $amount;
					$totalFinalTax 		+= $tax;

				}

				$bookingDetails->insurance = $thisData['insurance'];
				if($thisData['insurance'] != ''){
					$amount   	=	$thisData['insurance'];
					$tax	    =	$thisData['tax_insurance'];
					$tax_ins	=	$thisData['tax_insurance_sgst'];
					$tax_inscg	=	$thisData['tax_insurance_cgst'];
					if($tax !='' || $tax_ins !='' || $tax_inscg !='' ){
						$tax_amount= ($amount*($tax+$tax_ins+$tax_inscg))/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					
					$bookingDetails->insurance_tax_amount	=	$tax_amount;
					$bookingDetails->insurance_total_amount	=	$total_amount;
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
               	}

				$bookingDetails->helmet = $thisData['helmet'];
				if($thisData['helmet'] != ''){
					$amount	    =	$thisData['helmet'];
					$tax	    =	$thisData['tax_helmet'];
					$taxhemlet	=	$thisData['tax_helmet_sgst'];
					$taxhemletcge	=	$thisData['tax_helmet_cgst'];
                  
					if($tax !='' || $taxhemlet!='' || $taxhemletcge!='' ){
						$tax_amount= ($amount*($tax+$taxhemlet+$taxhemletcge))/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					
					$bookingDetails->helmet_tax_amount		=	$tax_amount;
					$bookingDetails->helmet_total_amount	=	$total_amount;
					
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
				}

				$bookingDetails->registration_certificate = $thisData['registration_certificate'];
				if($thisData['registration_certificate'] != ''){
					$amount	=	$thisData['registration_certificate'];
					$tax	=	$thisData['tax_registration_certificate'];
					$taxrto	=	$thisData['tax_registration_certificate_sgst'];
					$taxrtocg	=	$thisData['tax_registration_certificate_cgst'];
					if($tax !=''  || $taxrto !=''  ||  $taxrtocg !=''){
						$tax_amount= ($amount*($tax+$taxrto+$taxrtocg))/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}

					
					$bookingDetails->registration_certificate_tax_amount		=	$tax_amount;
					$bookingDetails->registration_certificate_total_amount   	=	$total_amount;
                	$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
				}

				$bookingDetails->number_plate = $thisData['number_plate'];
				if($thisData['number_plate'] != ''){
					$amount	=	$thisData['number_plate'];
					$tax	=	$thisData['tax_number_plate'];
					$taxnumber	=	$thisData['tax_number_plate_sgst'];
					$taxnumbercg	=	$thisData['tax_number_plate_cgst'];
					if($tax !='' || $taxnumber !='' ||  $taxnumbercg !=''){
						$tax_amount= ($amount*($tax+$taxnumber+$taxnumbercg))/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					
					$bookingDetails->number_plate_tax_amount	=	$tax_amount;
					$bookingDetails->number_plate_total_amount	=	$total_amount;
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
				}
				// echo "<pre>";print_r($bookingDetails);die;
				$vehicleDetails = DB::table('inventories')->where('id',$bookingDetails->vehicle_id)->select('inventories.*',DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))->first();
				
				$otherCharges = array();
				 if(!empty($thisData['numberCount'])){
					$f = 0;
					foreach($thisData['numberCount'] as &$i){
						if($thisData['name_other_charges_'.$i] != '' && $thisData['other_charges_'.$i] != ''){
							$taxPercent	=	$thisData['tax_other_charges_'.$i];	
							$taxPercentsgst	=  $thisData['tax_other_charges_'.$i.'_sgst']; 	
							$taxPercentcgst	=  $thisData['tax_other_charges_'.$i.'_cgst']; 	
							$amount		=	$thisData['other_charges_'.$i];
							if($taxPercent != '' ||  $taxPercentsgst != ''  ||   $taxPercentcgst != ''   ){
								$TaxAmount	=	($amount*($taxPercent+$taxPercentsgst+$taxPercentcgst))/100;
							}else{
								$TaxAmount	=	0;
							}
							$TotalAmount	=	$amount+$TaxAmount;
							if(!empty($thisData['other_charges_'.$i])){
							
								$otherCharges[$f]['name_type'] 			=	$thisData['name_other_charges_'.$i];
								$otherCharges[$f]['amount'] 		    =	$thisData['other_charges_'.$i];
								$otherCharges[$f]['tax_percent'] 	    =	$thisData['tax_other_charges_'.$i];
								$otherCharges[$f]['tax_percent_sgst'] 	=   $thisData['tax_other_charges_'.$i.'_sgst'];
								$otherCharges[$f]['tax_percent_cgst'] 	=   $thisData['tax_other_charges_'.$i.'_cgst'];
								$otherCharges[$f]['tax_amount'] 	    =	$TaxAmount; 
								$otherCharges[$f]['total_amount'] 	    =	$TotalAmount;
								$f++;
							}
						}
					}
				 } 

				 

				if(!empty($thisData['numberCount'])){
				
					foreach($thisData['numberCount'] as &$i){
						if($thisData['name_other_charges_'.$i] != '' && $thisData['other_charges_'.$i] != ''){
							$taxPercent	=	$thisData['tax_other_charges_'.$i];	
							$taxPercentsgst	=  $thisData['tax_other_charges_'.$i.'_sgst']; 
							$taxPercentcgst	=  $thisData['tax_other_charges_'.$i.'_cgst']; 	
							$amount		=	$thisData['other_charges_'.$i];
							if($taxPercent != '' ||  $taxPercentsgst != ''  ||   $taxPercentcgst != ''   ){
								$TaxAmount	=	($amount*($taxPercent+$taxPercentsgst+$taxPercentcgst))/100;
							}else{
								$TaxAmount	=	0;
							}
							$TotalAmount	=	$amount+$TaxAmount;

							$total_amount 	=	$TotalAmount;
							$tax_amount   	=	$TaxAmount;
							$amount 		=	$thisData['other_charges_'.$i];

							if(!empty($tax_amount)){
								$totalTaxableAmount += $amount;
							}
							
							$totalFinalAmount 	+= $total_amount;
							$totalFinalTax 		+= $tax_amount;

						}}}

				/* if(!empty($otherCharges)){
					foreach($otherCharges as $key=>$value){
						$tax_amount 	= $value->tax_amount;	
						$total_amount 	= $value->total_amount;	
						$amount 		= $value->amount;	
						
						if(!empty($tax_amount)){
							$totalTaxableAmount += $amount;
						}
						
						$totalFinalAmount 	+= $total_amount;
						$totalFinalTax 		+= $tax_amount;
					}
				} */
			
				$bookingDetails->final_tax_amount		=	$totalFinalTax;
				$bookingDetails->final_amount			=	$totalFinalAmount;
				$bookingDetails->taxable_amount			=	$totalTaxableAmount;

				
				$dealerDetails = DB::table('users')
										->where('users.id',$bookingDetails->dealer_id)
										->leftjoin('states', 'users.state_id', '=', 'states.id')
										->select('users.*', 'states.name as state_name')
										->first();
				// get total amount in words
				$amount_in_words	=	$this->convert_number_into_words($bookingDetails->final_amount);
				// get tax amount in words
				$tax_amount			=	$this->convert_number_into_words($bookingDetails->final_tax_amount);
				// create file name and path
				 $fileName			=	time().'-invoice-'.$bookingDetails->id.'.pdf';
				 $newCategory     	= 	strtoupper(date('M'). date('Y'))."/";
				$invoicePath		=	INVOICE_ROOT_PATH.$newCategory;
				if(!File::exists($invoicePath)) {
					File::makeDirectory($invoicePath, $mode = 0777,true);
				}
			/*	$newCategory     	= 	strtoupper(date('M'). date('Y'))."/";
				$invoicePath		=	INVOICE_ROOT_PATH.$newCategory;
				if(!File::exists($invoicePath)) {
					File::makeDirectory($invoicePath, $mode = 0777,true);
				}
				$fullFileName		=	$newCategory.$fileName; */
				// file name in booking table
				/* if($invoicePath != ''){
					Booking::where('id',Input::get('booking_id'))->update(array('invoice_name'=>"$fullFileName"));
				} */
				$actual_total_price	=	$bookingDetails->price+$findBooking->number_plate+$findBooking->insurance+$findBooking->other_charges+$findBooking->registration_certificate+$findBooking->helmet;
			}
		}

		//dd($bookingDetails);
	
		return View::make('dealerpanel.'.$this->model.'.preview_invoice_pdf', compact('bookingDetails','dealerDetails','fileName','invoicePath', 'amount_in_words', 'tax_amount','vehicleDetails','InputArr','otherCharges'));
	}

	
	/**
	* Function 
	*
	* @param null
	*
	* @return   
	*/
	public function addMoreOtherCharges(){
		Input::replace($this->arrayStripTags(Input::all()));
		$tax_array		=	TaxManager::where('is_active',1)->where('is_deleted',0)->orderBy('tax_value','asc')->pluck('tax_name', 'tax_value');
		$count		=	Input::get('count')+1;
		
		return View::make('dealerpanel.'.$this->model.'.other_charges_add_more', compact('count','tax_array'));
		
	}
	
	
	/**
	* Function for create invoice
	*
	* @param null
	*
	* @return view page. 
	*/
	public function createInvoice($id=""){
		$dealer_id				=	$this->get_dealer_id();
		$bookingDetails			=	DB::table('booking')
												->where('booking.id',$id)
												->where("booking.dealer_id",$dealer_id)
												->leftjoin('dropdown_managers as vehiclemodal', 'vehiclemodal.id','=','booking.vehicle_modal')
												->leftjoin('dropdown_managers as vehiclecolor', 'vehiclecolor.id','=','booking.vehicle_color')
												->select('booking.*', 'vehiclemodal.name as vehicle_modal_name','vehiclecolor.name as vehicle_color_name',DB::raw("(SELECT name FROM states WHERE states.id = booking.state) as state"),
												DB::raw("(SELECT name FROM cities WHERE id = booking.city) as city"))
												->first(); 
		if(empty($bookingDetails)) {
			return Redirect::back();
		}
		
		$totalFinalAmount = 0;
				$totalFinalTax = 0;
				$totalTaxableAmount = 0;
				
				if($bookingDetails->total_amount != ''){
					$amount	=	$bookingDetails->total_amount;
					$tax	=	$bookingDetails->tax_amount;
					
					$price	=	$bookingDetails->price;
					if(!empty($tax)){
						$totalTaxableAmount += $price;
					}
					$totalFinalAmount 	+= $amount;
					$totalFinalTax 		+= $tax;

				}
				if($bookingDetails->insurance != ''){
					$amount	=	$bookingDetails->insurance;
					$tax	=	$bookingDetails->tax_insurance;
					if($tax !=''){
						$tax_amount= ($amount*$tax)/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$bookingDetails->insurance_tax_amount	=	$tax_amount;
					$bookingDetails->insurance_total_amount	=	$total_amount;
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;

				}
				if($bookingDetails->helmet != ''){
					$amount	=	$bookingDetails->helmet;
					$tax	=	$bookingDetails->tax_helmet;
					if($tax !=''){
						$tax_amount= ($amount*$tax)/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$bookingDetails->helmet_tax_amount		=	$tax_amount;
					$bookingDetails->helmet_total_amount	=	$total_amount;
					
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
				}
				if($bookingDetails->registration_certificate != ''){
					$amount	=	$bookingDetails->registration_certificate;
					$tax	=	$bookingDetails->tax_registration_certificate;
					if($tax !=''){
						$tax_amount= ($amount*$tax)/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					$bookingDetails->registration_certificate_tax_amount		=	$tax_amount;
					$bookingDetails->registration_certificate_total_amount	=	$total_amount;
					
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
				}
				if($bookingDetails->number_plate != ''){
					$amount	=	$bookingDetails->number_plate;
					$tax	=	$bookingDetails->tax_number_plate;
					if($tax !=''){
						$tax_amount= ($amount*$tax)/100;
						$total_amount	=	$amount+$tax_amount;
						
						$totalTaxableAmount += $amount;
					}else{
						$tax_amount		= 0;
						$total_amount	=	$amount;
					}
					
					$bookingDetails->number_plate_tax_amount		=	$tax_amount;
					$bookingDetails->number_plate_total_amount	=	$total_amount;
					
					$totalFinalAmount 	+= $total_amount;
					$totalFinalTax 		+= $tax_amount;
				}
				// echo "<pre>";print_r($bookingDetails);die;
				$vehicleDetails = DB::table('inventories')->where('id',$bookingDetails->vehicle_id)->select('inventories.*',DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))->first();
				$otherCharges	=	BookingOtherCharge::where('booking_id',$id)->get();
				if(!empty($otherCharges)){
					foreach($otherCharges as $key=>$value){
						$tax_amount 	= $value->tax_amount;	
						$total_amount 	= $value->total_amount;	
						$amount 		= $value->amount;	
						
						if(!empty($tax_amount)){
							$totalTaxableAmount += $amount;
						}
						
						$totalFinalAmount 	+= $total_amount;
						$totalFinalTax 		+= $tax_amount;
					}
				}
				$bookingDetails->final_tax_amount		=	$totalFinalTax;
				$bookingDetails->final_amount			=	$totalFinalAmount;
				$bookingDetails->taxable_amount			=	$totalTaxableAmount;
		
		
		$vehicleDetails = DB::table('inventories')->where('id',$bookingDetails->vehicle_id)->select('inventories.*',DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))->first();
		
		$dealerDetails = DB::table('users')
								->where('users.id',$bookingDetails->dealer_id)
								->leftjoin('states', 'users.state_id', '=', 'states.id')
								->select('users.*', 'states.name as state_name')
								->first();
		// get total amount in words
		$amount_in_words	=	$this->convert_number_into_words($bookingDetails->total_amount);
		// get tax amount in words
		$tax_amount			=	$this->convert_number_into_words($bookingDetails->tax_amount);
		return View::make('dealerpanel.'.$this->model.'.view_invoice_pdf_demo', compact('bookingDetails','dealerDetails', 'amount_in_words', 'tax_amount','vehicleDetails'));
	}// end of function
	

	/**
	* Function for create invoice
	*
	* @param null
	*
	* @return view page. 
	*/
/* 	public function deleteBookingInvoice($id=""){
     
		$vehicaleDetails = DB::table('booking')->where('id',$id)->update(array('invoice_name' => ''));
	   	Session::flash("success",trans("Booking invoice has been deleted successfully"));
				return Redirect::to('/dealerpanel/booking-management');
		
	} end of function */

} //end BookingController()
