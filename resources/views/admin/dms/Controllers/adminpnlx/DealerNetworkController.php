<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\DealerNetwork;
use App\Model\Enquiry;
use App\Model\DealerLocation;

use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* DealerNetworkController Controller
*
* Add your methods in the class below
*
* This file will render views\DealerNetworkController\dashboard
*/
	class DealerNetworkController extends BaseController {
		
		public $model	=	'DealerNetwork';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function listNetwork(){
		$DB 					= 	DealerNetwork::query();
		$LocationCode 			= 	DealerLocation::query();
		$LocationName 			= 	DealerLocation::query();
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
					$DB->where("dealer_network.".$fieldName,'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
				$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
				if(!empty($assignedDealer)){
					$DB->whereIn('dealer_network.dealer_id', $assignedDealer);
				}
			}
			$result 				= 	$DB
									->where('dealer_network.is_deleted',0)
									->leftjoin('dealer_location', 'dealer_network.location_name', '=', 'dealer_location.id')
									->select('dealer_network.*', 'dealer_location.location_name as dealer_location_name','dealer_location.location_code as dealer_location_code',
									DB::raw("(SELECT name FROM states WHERE id = dealer_network.state) as state"),
									DB::raw("(SELECT full_name FROM users WHERE id = dealer_network.dealer_id) as dealer_name"),
									DB::raw("(SELECT name FROM cities WHERE id = dealer_network.city) as city"))
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
			
			//echo'<pre>'; print_r($result); echo'</pre>'; die;
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("network_search_data",$inputGet);
			//dealer_location
			$dealerLocationCode  	=	$this->get_dealer_location_code_list();
			$dealerLocationName  	=	$this->get_dealer_location_name_list();
			
			$dealerName = $this->get_dealer_list();
			

			return  View::make('admin.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string','dealerLocationCode','dealerLocationName','dealerName'));
		}
		
	

	/**
	* Function for add Network page
	*
	* @param null
	*
	* @return view page. 
	*/


	public function addNetwork(){
		$stateList		=	DB::table('states')
									->where('status',1)
									->where('country_id',101)
									->orderBy('name','ASC')
									->pluck('name','id')->toArray();

		// dealer list
		$dealerName = $this->get_dealer_list();
		//dealer_location
		$dealerLocationCode  	=	$this->get_dealer_location_code_list();
		$dealerLocationName  	=	$this->get_dealer_location_name_list();

		

		return View::make('admin.'.$this->model.'.add',compact('stateList','dealerName','dealerLocationName','dealerLocationCode'));
	}
	
	/**
	* Function for save Network
	*
	* @param null
	*
	* @return view page. 
	*/
	public function saveNetwork(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					
					'dealer_name' 			=>	 'required',
					'branch_name' 			=>	 'required',
					'location_name' 		=>	 'required',
					'state' 				=>	 'required',
					'city' 					=>	 'required',
					'contact_person_name' 	=>	'required',
					'mobile_number' 		=>	 'required|numeric|integer|digits:10',
					'email' 				=>	 'required|email',
					'registration_date' 	=>	 'required',
					'address_1'				=>	 'required',
					'zipcode'				=>	'required',
					
				),
				array(
					"contact_person_name.required"			=>	trans("The contact name field is required."),
					"email.required"						=>	trans("The email address field is required."),
					'email.email'							=> 	'The email address is invalid.',
					"mobile_number.numeric"					=>	trans("Phone number must have a numeric value."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				
				$network 						= 	new DealerNetwork;
				$network->dealer_id				=	Input::get('dealer_name');
				$network->branch_name			=	Input::get('branch_name');
				$network->location_name			=	Input::get('location_name');
				$network->location_code			=	DB::table('dealer_location')->where('location_code',Input::get('location_code'))->value('id');
				$network->state					=	Input::get('state');
				$network->city					=	Input::get('city');
				$network->contact_person_name	=	Input::get('contact_person_name');
				$network->mobile_number			=	Input::get('mobile_number');
				$network->email					=	Input::get('email');
				$network->registration_date		= 	!empty(Input::get('registration_date')) ? date('Y-m-d',strtotime(Input::get('registration_date'))) : '0000-00-00';
				$network->created_at			=  date("Y-m-d H:i:s");
				$network->address_1				= 	!empty(Input::get('address_1')) ? Input::get('address_1') : '';
				$network->address_2				= 	!empty(Input::get('address_2')) ? Input::get('address_2') : '';
				$network->zipcode				= 	!empty(Input::get('zipcode')) ? Input::get('zipcode') : '';
				$network->save();
				Session::flash("success",trans("Dealer network added successfully."));
				return Redirect::to('adminpnlx/dealer-network-management');
				//return Redirect::back();
			}
		}
	}
	
	public function editNetwork($id = ""){
		$networkDetails	    =	DB::table('dealer_network')
								->where('dealer_network.id',$id)
								->leftjoin('dealer_location', 'dealer_network.location_code', '=', 'dealer_location.id')
								->select('dealer_network.*', 'dealer_location.location_code as l_code')
								->first();

		//echo'<pre>'; print_r($networkDetails); echo'</pre>'; die;
		if(empty($networkDetails)) {
			return Redirect::back();
		}	
		$cityList		=	DB::table('cities')
							->where('state_id',$networkDetails->state)
							->distinct('name')
							->pluck('name','id')
							->toArray();
		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',101)
							->orderBy('name','ASC')
							->pluck('name','id')->toArray();
		// dealer name
		$dealerName = $this->get_dealer_list();
		//dealer_location
		$dealerLocationCode  	=	$this->get_dealer_location_code_list();
		$dealerLocationName  	=	$this->get_dealer_location_name_list();
		return View::make('admin.'.$this->model.'.edit', compact("networkDetails",'dealerName','cityList','stateList','dealerLocationName'));
	
		
	} // end editUser()
	
	public function updateNetwork($id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'dealer_name' 			=>	 	'required',
					'branch_name' 			=>	 	'required',
					'location_name' 		=>		'required',
					'state' 				=>	 	'required',
					'city' 					=>	 	'required',
					'contact_person_name' 	=>		'required',
					'mobile_number' 		=>	  	'required|integer|digits:10',
					'email' 				=>	 	'required|email',
					'registration_date' 	=>	 	'required',
					'address_1'				=>	 	'required',
					'zipcode'				=>		'required',
					
				),
				array(
					"contact_person_name.required"			=>	trans("The contact name field is required."),
					"email.required"						=>	trans("The email address field is required."),
					'email.email'							=> 	'The email address is invalid.',
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				
				$network						= 	DealerNetwork::find($id);
				$network->dealer_id				=	Input::get('dealer_name');
				$network->branch_name			=	Input::get('branch_name');
				$network->location_name			=	Input::get('location_name');
				$network->location_code			=	DB::table('dealer_location')->where('location_code',Input::get('location_code'))->value('id');
				$network->state					=	Input::get('state');
				$network->city					=	Input::get('city');
				$network->contact_person_name	=	Input::get('contact_person_name');
				$network->mobile_number			=	Input::get('mobile_number');
				$network->email					=	Input::get('email');
				$network->registration_date		= 	!empty(Input::get('registration_date')) ? date('Y-m-d',strtotime(Input::get('registration_date'))) : '0000-00-00';
				$network->updated_at			=  date("Y-m-d H:i:s");
				$network->address_1				= 	!empty(Input::get('address_1')) ? Input::get('address_1') : '';
				$network->address_2				= 	!empty(Input::get('address_2')) ? Input::get('address_2') : '';
				$network->zipcode				= 	!empty(Input::get('zipcode')) ? Input::get('zipcode') : '';
				

				$network->save();
				
				Session::flash('flash_notice', trans("Dealer network has been updated successfully.")); 
				return Redirect::to('adminpnlx/dealer-network-management');
			}
		}
	}


	public function viewNetwork($id=""){
		$networkDetails	    =	DB::table('dealer_network')
								->where('dealer_network.id',$id)
								->leftjoin('dealer_location', 'dealer_network.location_name', '=', 'dealer_location.id')
										->select('dealer_network.*', 'dealer_location.location_name as location_name','dealer_location.location_code as location_code',
										DB::raw("(SELECT name FROM states WHERE id = dealer_network.state) as state"),
										DB::raw("(SELECT full_name FROM users WHERE id = dealer_network.dealer_id) as dealer_name"),
										DB::raw("(SELECT name FROM cities WHERE id = dealer_network.city) as city"))
								->first();
		
		if(empty($networkDetails)) {
			return Redirect::back();
		}else{
			
			return View::make('admin.'.$this->model.'.view', compact("networkDetails"));
		}
	}

	
	public function deleteNetwork($id = ''){
		
		$networkDetails			=	DealerNetwork::find($id); 
		if(empty($networkDetails)) {
			return Redirect::back();
		}
		if($id){	
			$email 						=	'delete_'.$id .'_'.$networkDetails->email;
			$userModel					=	DealerNetwork::where('id',$id)->update(array('is_deleted'=>1,'email'=>$email));
			Session::flash('flash_notice',trans("Dealer network has been deleted successfully.")); 
		}
		return Redirect::back();
	}

	public function updateNetworkStatus($id = 0, $Status = 0){
		if($Status == 0){
			$statusMessage	=	trans("Dealer network has been deactivated.");
			$staffDetails		=	DealerNetwork::find($id); 
		}else{
			$statusMessage	=	trans("Dealer Network has been activated.");
		}
		$this->_update_all_status("dealer_network",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} // end updateDealerNetworkstatus()
	
	
	public function exportDealerNetworkToExcel(){
		$searchData			=	Session::get('network_search_data');
		$DB 				= 	DealerNetwork::query();	
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
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer		=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if(!empty($assignedDealer)){
				$result 			= 	$DB->whereIn('dealer_network.dealer_id', $assignedDealer);
			}
		}
		$result 				= 	$DB
									->where('dealer_network.is_deleted',0)
									->leftjoin('dealer_location', 'dealer_network.location_name', '=', 'dealer_location.id')
									->select('dealer_network.*', 'dealer_location.location_name as dealer_location_name','dealer_location.location_code as dealer_location_code',
									DB::raw("(SELECT name FROM states WHERE id = dealer_network.state) as state"))
									->orderBy($sortBy, $order)
									->get()->toArray();

	
								
			
		$thead = array();
		$genderArr = Config::get("gender_type_array");
		$thead[]		= array("Branch","Location-Name","Location-Code","Address","Zipcode","State","City","Registration Date","Contact-Person-Name","Mobile Number","Email-ID","Status");
		if(!empty($result)) {
			foreach($result as $record) {
				if($record['is_active']	==1){
					$status = "Activated";
				}else{
					$status = "Deactivated";
				}
				
				$branch_name				=	!empty($record['branch_name'])?$record['branch_name']:'';
				$dealer_location_name		=	!empty($record['dealer_location_name'])?$record['dealer_location_name']:'';
				$dealer_location_code		=	!empty($record['dealer_location_code'])?$record['dealer_location_code']:'';
				$address					=	!empty($record['address_1'])?$record['address_1'].' '.$record['address_2']:'';
				$zipcode					=	!empty($record['zipcode'])?$record['zipcode']:'';
				$state						=	!empty($record['state'])?$record['state']:'';
				$city						=	!empty($record['city'])?$record['city']:'';
				$registration_date			=	!empty($record['registration_date'])?date(Config::get("Reading.date_format") , strtotime($record['registration_date'])):'';
				$contact_person_name						=	!empty($record['contact_person_name'])?$record['contact_person_name']:'';
				$mobile_number						=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$email						=	!empty($record['email'])?$record['email']:'';
				$status						=	!empty($status)?$status:'';
				
				$thead[]		= array($branch_name,$dealer_location_name,$dealer_location_code,$address,$zipcode,$state,$city,$registration_date,$contact_person_name,$mobile_number,$email,$status);
			}
		}													
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
				'errors' 	=>	 trans("Location code added successfully.")
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
	
} //end dealerNetworkController()
