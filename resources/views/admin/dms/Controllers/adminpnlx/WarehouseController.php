<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\Warehouse;

use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* WarehouseController Controller
*
* Add your methods in the class below
*
* This file will render views\WarehouseController\dashboard
*/
	class WarehouseController extends BaseController {
		
		public $model	=	'Warehouse';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function list(){
		$DB 					= 	Warehouse::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of name and warehouse_id */ 
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
					$DB->where("warehouses.".$fieldName,'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			$DB->where('is_deleted',0);
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';

			$result 				= 	$DB
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
			
			// echo'<pre>'; print_r($result); echo'</pre>'; die;
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("_search_data",$inputGet);

			return  View::make('admin.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string'));
		}
		
	

	/**
	* Function for add  page
	*
	* @param null
	*
	* @return view page. 
	*/


	public function add(){
		
		return View::make('admin.'.$this->model.'.add');
	}
	
	/**
	* Function for save 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function save(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 					=>	 'required',
					'address' 				=>	 'required',
					'description' 			=>	 'required',
				),
				array(
					"name.required"							=>	trans("The name field is required."),
					"address.required"						=> 	trans("The address field is required."),
					"description.required"					=>	trans("The description field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				$day = date("my");
				$rand = mt_rand(1000,9999);
				$value = "#W".$day."".$rand;
				$warehouse 						= 	new Warehouse;
				$warehouse->warehouse_id		= 	$value;
				$warehouse->name				=	Input::get('name');
				$warehouse->address				=	Input::get('address');
				$warehouse->description			=	Input::get('description');
				$warehouse->is_active			= 	1;
				$warehouse->created_at			=   date("Y-m-d H:i:s");
				$warehouse->save();
				$id = $warehouse->id;
				$values = $value."".$id;
				// echo $value; die;
				$data = Warehouse::find($id);
				$data->warehouse_id = $values;
				$data->save();
				// echo $data; die;
				Session::flash("success",trans("Warehouse added successfully."));
				return Redirect::to('adminpnlx/warehouse');
			}
		}
	}
	
	public function edit($id = ""){
		$Details	    =	DB::table('warehouses')
								->where('warehouses.id',$id)
								->first();

		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}	
		
		return View::make('admin.'.$this->model.'.edit', compact("Details"));
		
	} // end editUser()
	
	public function update($id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 					=>	 'required',
					'address' 				=>	 'required',
					'description' 			=>	 'required',
					
				),
				array(
					"name.required"							=>	trans("The name field is required."),
					"address.required"						=> 	trans("The address field is required."),
					"description.required"					=>	trans("The description field is required."),
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				
				$warehouse						= 	Warehouse::find($id);
				$warehouse->name				=	Input::get('name');
				$warehouse->address				=	Input::get('address');
				$warehouse->description			=	Input::get('description');
				$warehouse->is_active			= 	1;
				$warehouse->updated_at			=  date("Y-m-d H:i:s");
				$warehouse->save();
				Session::flash('flash_notice', trans("Warehouse has been updated successfully.")); 
				return Redirect::to('adminpnlx/warehouse');
			}
		}
	}


	public function view($id=""){
		$Details	    =	DB::table('warehouses')
								->where('warehouses.id',$id)
								->first();

		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}else{	
			return View::make('admin.'.$this->model.'.view', compact("Details"));
		}
	}

	
	public function delete($id = ''){
		
		$Details			=	Warehouse::find($id); 
		if(empty($Details)) {
			return Redirect::back();
		}
		if($id){	
			$userModel					=	Warehouse::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans("Warehouse has been deleted successfully.")); 
		}
		return Redirect::back();
	}

	public function updateStatus($id = 0, $Status = 0){
		if($Status == 0){
			$statusMessage	=	trans("Warehouse has been deactivated.");
			$staffDetails		=	Warehouse::find($id); 
		}else{
			$statusMessage	=	trans("Warehouse has been activated.");
		}
		$this->_update_all_status("warehouses",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} // end updateWarehousestatus()
	
	
	// public function exportWarehouseToExcel(){
	// 	$searchData			=	Session::get('_search_data');
	// 	$DB 				= 	Warehouse::query();	
	// 	$searchVariable			=	array(); 						
	// 	if ($searchData) {
	// 		unset($searchData['display']);
	// 		unset($searchData['_token']);
	// 		if(isset($searchData['order'])){
	// 			unset($searchData['order']);
	// 		}
	// 		if(isset($searchData['sortBy'])){
	// 			unset($searchData['sortBy']);
	// 		}
	// 		if(isset($searchData['page'])){
	// 			unset($searchData['page']);
	// 		}
	// 		foreach($searchData as $fieldName => $fieldValue){
	// 			if($fieldValue != ""){
	// 				if($fieldName == 'booking_date_start' || $fieldName == 'booking_date_end'){
	// 					if($fieldName == 'booking_date_start'){  
	// 						$DB->where('users.booking_date','>=',$fieldValue);
	// 					}
	// 					if($fieldName == 'booking_date_end'){  
	// 						$DB->where('users.booking_date','<=',$fieldValue);
	// 					}
	// 				}else{
	// 					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
	// 				}
	// 			}
	// 			$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
	// 		}
	// 	}
	// 	$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
	// 	$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
	// 	if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
	// 		$assignedDealer		=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
	// 		if(!empty($assignedDealer)){
	// 			$result 			= 	$DB->whereIn('dealer_.dealer_id', $assignedDealer);
	// 		}
	// 	}
	// 	$result 				= 	$DB
	// 								->where('dealer_.is_deleted',0)
	// 								->leftjoin('dealer_location', 'dealer_.location_name', '=', 'dealer_location.id')
	// 								->select('dealer_.*', 'dealer_location.location_name as dealer_location_name','dealer_location.location_code as dealer_location_code',
	// 								DB::raw("(SELECT name FROM states WHERE id = dealer_.state) as state"))
	// 								->orderBy($sortBy, $order)
	// 								->get()->toArray();

	
								
			
	// 	$thead = array();
	// 	$genderArr = Config::get("gender_type_array");
	// 	$thead[]		= array("Branch","Location-Name","Location-Code","Address","Zipcode","State","City","Registration Date","Contact-Person-Name","Mobile Number","Email-ID","Status");
	// 	if(!empty($result)) {
	// 		foreach($result as $record) {
	// 			if($record['is_active']	==1){
	// 				$status = "Activated";
	// 			}else{
	// 				$status = "Deactivated";
	// 			}
				
	// 			$branch_name				=	!empty($record['branch_name'])?$record['branch_name']:'';
	// 			$dealer_location_name		=	!empty($record['dealer_location_name'])?$record['dealer_location_name']:'';
	// 			$dealer_location_code		=	!empty($record['dealer_location_code'])?$record['dealer_location_code']:'';
	// 			$address					=	!empty($record['address_1'])?$record['address_1'].' '.$record['address_2']:'';
	// 			$zipcode					=	!empty($record['zipcode'])?$record['zipcode']:'';
	// 			$state						=	!empty($record['state'])?$record['state']:'';
	// 			$city						=	!empty($record['city'])?$record['city']:'';
	// 			$registration_date			=	!empty($record['registration_date'])?date(Config::get("Reading.date_format") , strtotime($record['registration_date'])):'';
	// 			$contact_person_name						=	!empty($record['contact_person_name'])?$record['contact_person_name']:'';
	// 			$mobile_number						=	!empty($record['mobile_number'])?$record['mobile_number']:'';
	// 			$email						=	!empty($record['email'])?$record['email']:'';
	// 			$status						=	!empty($status)?$status:'';
				
	// 			$thead[]		= array($branch_name,$dealer_location_name,$dealer_location_code,$address,$zipcode,$state,$city,$registration_date,$contact_person_name,$mobile_number,$email,$status);
	// 		}
	// 	}													
	// 	return  View::make('dealerpanel.'.$this->model.'.export_excel', compact('thead'));
		
	// }
	
} //end WarehouseController()
