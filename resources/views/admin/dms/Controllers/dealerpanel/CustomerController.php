<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\Enquiry;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* CustomerController Controller
*
* Add your methods in the class below
*
* This file will render views\CustomerController\dashboard
*/
	class CustomerController extends BaseController {
		
		public $model	=	'Customer';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function listCustomer(){
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
										->where('users.is_deleted',0)
										->where('users.dealer_id',$dealer_id)
										->where('users.user_role_id',CUSTOMER_ROLE_ID)
										->leftJoin('dealer_inventory','dealer_inventory.customer_id','=','users.id')
										->leftJoin('inventories','inventories.id','=','dealer_inventory.vehicle_id')
										->select('users.*', 'inventories.imei_number',DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"))
										->orderBy('users.'.$sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("customer_search_data",$inputGet);
             
			return  View::make('dealerpanel.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string'));
		}
		
	

	/**
	* Function for add Customer page
	*
	* @param null
	*
	* @return view page. 
	*/

	public function addCustomer(){
		$dealer_id				=	$this->get_dealer_id();
		$dealerLocationName  	=	DB::table('dealer_location')
									->where('dealer_id',$dealer_id)
									->where('is_active',1)
									->where('is_deleted',0)
									->orderBy('location_name','ASC')
									->pluck('location_name','id')
									->toArray(); 

		$dealerLocationCode  	=	DB::table('dealer_location')
									->where('dealer_id',$dealer_id)
									->where('is_active',1)
									->where('is_deleted',0)
									->orderBy('location_code','ASC')
									->pluck('location_code','id')
									->toArray(); 

		return View::make('dealerpanel.'.$this->model.'.add', compact('dealerLocationCode' ,'dealerLocationName'));
	}
	
	/**
	* Function for save Customer
	*
	* @param null
	*
	* @return view page. 
	*/
	public function saveCustomer(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'customer_name' 	=>	 'required',
					'city' 				=>	 'required',
					'location_name' 	=>	 'required',
					'email' 			=>	 'email|unique:users',
					'mobile_number' 	=>	 'required|integer|digits:10',
					'gender' 			=>	 'required',
					
				),
				array(
					'email.email'							=> 	'The email address is invalid.',
					'email.unique'							=> 	'This email address has already been taken.',
					"dob.required"							=>	trans("The date of birth field is required."),
					"mobile_number.integer"					=>	trans("Mobile number must have a numeric value."),
					"mobile_number.digits"					=>	trans("Mobile number must have 10 digits."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				$dealer_id				=	$this->get_dealer_id();
				$customer 						= 	new User;
				$customer->dealer_id			=	$dealer_id;
				$customer->user_role_id			=	CUSTOMER_ROLE_ID;
				$customer->full_name			=	Input::get('customer_name');
				$customer->gender				=	Input::get('gender');
				$customer->email				=	Input::get('email');
				$customer->phone_number			=	Input::get('mobile_number');
				$customer->dob					=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
				$customer->booking_date			=	!empty(Input::get('booking_date')) ? date('Y-m-d',strtotime(Input::get('booking_date'))) : '0000-00-00';
				//$customer->status				=	Input::get('status');
				$customer->dealer_location_name	=	Input::get('location_name');
				$customer->city					=	Input::get('city');
				$customer->dealer_location_code	=	Input::get('location_code');
				$customer->created_at			=  date("Y-m-d H:i:s");
				$customer->save();				
				
				//return Redirect::back();
			}
			Session::flash("success",trans("Customer added successfully."));
				return Redirect::to('/dealerpanel/customer-management');
		}
	}
	
	public function editCustomer($id = ""){
		$dealer_id				=	$this->get_dealer_id();
		$customerDetails	   =	DB::table('users')
								->where('users.id',$id)
								->where('users.user_role_id',CUSTOMER_ROLE_ID)
								->where("users.dealer_id",$dealer_id)
								->leftjoin('dealer_location', 'users.dealer_location_name', '=', 'dealer_location.id')
								->select('users.*', 'dealer_location.location_code as l_code')
								->first();

		///echo'<pre>'; print_r($customerDetails); echo'</pre>'; die;				
		$dealerLocationName  	=	DB::table('dealer_location')
									->where('dealer_id',$dealer_id)
									->where('is_active',1)
									->where('is_deleted',0)
									->orderBy('location_name','ASC')
									->pluck('location_name','id')
									->toArray();
								
		if(empty($customerDetails)) {
			return Redirect::back();
		}		
		return View::make('dealerpanel.'.$this->model.'.edit', compact("customerDetails",'dealerLocationName'));
	
		
	} // end editUser()
	
	public function updateCustomer($id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		// echo '<pre>'; print_r($thisData); die;
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'customer_name' 	=>	 'required',
					'city' 				=>	 'required',
					'location_name' 	=>	 'required',
					'mobile_number' 	=>	 'required|integer|digits:10',
					'gender' 			=>	 'required',
					
				),
				array(
					
					"dob.required"							=>	trans("The date of birth field is required."),
					"mobile_number.integer"					=>	trans("Phone number must have a numeric value."),
					"mobile_number.digits"					=>	trans("Phone number must have 10 digits."),
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				
				$customer						= 	User::find($id);
				$customer->full_name			=	Input::get('customer_name');
				$customer->gender				=	Input::get('gender');
				//$customer->email				=	Input::get('email');
				$customer->phone_number			=	Input::get('mobile_number');
				$customer->dob					=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '';
				$customer->booking_date			=	!empty(Input::get('booking_date')) ? date('Y-m-d',strtotime(Input::get('booking_date'))) : '';
				//$customer->status				=	Input::get('status');
				$customer->dealer_location_name	=	Input::get('location_name');
				$customer->city					=	Input::get('city');
				$customer->dealer_location_code	=	Input::get('location_code');
				$customer->updated_at			=   date("Y-m-d H:i:s");
				$customer->save();
				
				Session::flash('flash_notice', trans("Customer has been updated successfully.")); 
				return Redirect::to('/dealerpanel/customer-management');
			}
		}
	}


	public function viewCustomer($id=""){
		$dealer_id				=	$this->get_dealer_id();
		$customerDetails	    =	DB::table('users')
									->where('users.id',$id)
									->where('users.user_role_id',CUSTOMER_ROLE_ID)
									->where("users.dealer_id",$dealer_id)
									->leftJoin('dealer_inventory','dealer_inventory.customer_id','=','users.id')
									->select('users.*', 'dealer_inventory.vehicle_id',DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"))
									->first();
	
		if(empty($customerDetails)) {
			return Redirect::back();
		}else{
			$vehicaleDetails = DB::table('inventories')->where('id',$customerDetails->vehicle_id)
							->select('inventories.*',
							DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))->first();	
			if(!empty($vehicaleDetails)){			
				$batteryDetails = DB::table('battery_details')->where('vehicle_id',$vehicaleDetails->id)->get()->toArray();
			}
			return View::make('dealerpanel.'.$this->model.'.view', compact("customerDetails",'vehicaleDetails','batteryDetails'));
		}
	}

	
	public function deleteCustomer($id = ''){
		
		$customerDetails			=	User::find($id); 
		if(empty($customerDetails)) {
			return Redirect::back();
		}
		if($id){	
			$email 						=	'delete_'.$id .'_'.$customerDetails->email;
			$userModel					=	User::where('id',$id)->update(array('is_deleted'=>1,'email'=>$email,'deleted_at'=>date("Y-m-d H:i:s")));
			Session::flash('flash_notice',trans("Customer has been deleted successfully.")); 
		}
		return Redirect::back();
	}
	
	public function exportCustomerToExcel(){
		$searchData			=	Session::get('customer_search_data');
		$DB 				= 	User::query();	
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
									->where('users.is_deleted',0)
									->where('users.dealer_id',$dealer_id)
									->where('users.user_role_id',CUSTOMER_ROLE_ID)
									->leftJoin('dealer_inventory','dealer_inventory.customer_id','=','users.id')
									// ->leftJoin('inventories','inventories.id','=','dealer_inventory.vehicle_id')
									->leftjoin('dealer_location', 'users.dealer_location_name', '=', 'dealer_location.id')
									->select('users.*', 'dealer_inventory.vehicle_id',DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"))
									->orderBy('users.'.$sortBy, $order)
									->get()->toArray();											
		$thead = array();
		$genderArr = Config::get("gender_type_array");
		$thead[]		= array("Name","Gender","DOB","Email-ID","Location-Name","Location-code","City","Mobile Number","Vehicle Modal","Vehicle Color","VIN Number","Motor Number","Chassis Number","IMEI Number","Battery Number");
		if(!empty($result)) {
			foreach($result as $record) {
				$vehicaleDetails = DB::table('inventories')->where('id',$record['vehicle_id'])
							->select('inventories.*',
							DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.model_id) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id = inventories.color_id) as vehicle_color"))->first();
						
				$batteryDetails			= '';
				$vehicle_modal 			= '';
				$vehicle_color 			= '';
				$vin_number 			= '';
				$motor_number 			= '';
				$chassis_number 		= '';
				$imei_number			= '';
				if(!empty($vehicaleDetails)){
					$batteryDetails = DB::table('battery_details')->where('vehicle_id',$vehicaleDetails->id)->get()->toArray();

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
				$full_name					=	!empty($record['full_name'])?$record['full_name']:'';
				$gender						=	!empty($record['gender'])?$genderArr[$record['gender']]:'';
				$dob						=	!empty($record['dob'])?date(Config::get("Reading.date_format") , strtotime($record['dob'])):'';
				$email						=	!empty($record['email'])?$record['email']:'';
				$dealer_location_name		=	!empty($record['dealer_location_name'])?$record['dealer_location_name']:'';
				$dealer_location_code		=	!empty($record['dealer_location_code'])?$record['dealer_location_code']:'';
				$city						=	!empty($record['city'])?$record['city']:'';
				$booking_date				=	!empty($record['booking_date'])?date(Config::get("Reading.date_format") , strtotime($record['booking_date'])):'';
				$phone_number				=	!empty($record['phone_number'])?$record['phone_number']:'';
				$status						=	!empty($record['status'])?$record['status']:'';
				
				$thead[]		= array($full_name,$gender,$dob,$email,$dealer_location_name,$dealer_location_code,$city,$phone_number,$vehicle_modal,$vehicle_color,$vin_number,$motor_number,$chassis_number,$imei_number,$batteryNumber);
			}
		}								
		//echo '<pre>'; print_r($thead); die;					
		return  View::make('dealerpanel.'.$this->model.'.export_excel', compact('thead'));
		
	}
	public function GetDealerCode(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData		=	 Input::all();	
		if($formData != ''){
			$location_id			=	Input::get("location_name"); 
			$location_code 			=	DB::table('dealer_location')
										->where('id',$location_id)
										->orderBy('is_active',1)
										->value('location_code');
				
			$response	=	array(
				'success' 	=>	'1',
				'location_code' 	=>	$location_code,
				'errors' 	=>	 trans("Location code added successfully"),
				); 			
			return Response::json($response); 
			die;
		}else{
			$response	=	array(
				'success' 	=>	'2',
				'errors' 	=>	 trans("There is an error.")
			); 
			return Response::json($response); 
			die;
		} 
	}
	
} //end CustomerController()
