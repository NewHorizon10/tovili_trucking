<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\ItemCategory;

use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* ItemCategoryController Controller
*
* Add your methods in the class below
*
* This file will render views\ItemCategoryController\dashboard
*/
	class ItemCategoryController extends BaseController {
		
		public $model	=	'ItemCategory';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function list(){
		$is_allowed = $this->check_section_permission(array('section'=>'item_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$DB 					= 	ItemCategory::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of name and description */ 
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
					$DB->where("item_category.".$fieldName,'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
            }
			$DB->where('parent_id',0);
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
		$is_allowed = $this->check_section_permission(array('section'=>'item_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
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
		$is_allowed = $this->check_section_permission(array('section'=>'item_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'category_code' 				=>	 'required',
					'category_name' 				=>	 'required',
					'category_description' 			=>	 'required',
				),
				array(
					"category_code.required"		=>	trans("The category code field is required."),
					"category_name.required"		=>	trans("The category name field is required."),
					"category_description.required"	=>	trans("The category description field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				$itemCategory 					    = 	new ItemCategory;
				$itemCategory->category_code	    =	Input::get('category_code');
				$itemCategory->category_name	    =	Input::get('category_name');
				$itemCategory->category_description	=	Input::get('category_description');
				$itemCategory->created_at		=   date("Y-m-d H:i:s");
				$itemCategory->save();
				// echo $data; die;
				Session::flash("success",trans("Item Category added successfully."));
				return Redirect::to('adminpnlx/itemCategory');
			}
		}
	}
	
	public function edit($id = ""){
		$is_allowed = $this->check_section_permission(array('section'=>'item_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		// echo $id; die;
		$Details	    =	DB::table('item_category')
								->where('item_category.id',$id)
								->first();

		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}	
		
		return View::make('admin.'.$this->model.'.edit', compact("Details"));
	
		
	} // end editUser()
	
	public function update($id=""){
		$is_allowed = $this->check_section_permission(array('section'=>'item_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'category_code' 				=>	 'required',
					'category_name' 				=>	 'required',
					'category_description' 			=>	 'required',
				),
				array(
					"category_code.required"		=>	trans("The category code field is required."),
					"category_name.required"		=>	trans("The category name field is required."),
					"category_description.required"	=>	trans("The category description field is required."),
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				
				$itemCategory						    = 	ItemCategory::find($id);
				$itemCategory->category_code	    =	Input::get('category_code');
				$itemCategory->category_name		    =	Input::get('category_name');
				$itemCategory->category_description	    =	Input::get('category_description');
				$itemCategory->created_at			    =   date("Y-m-d H:i:s");
				$itemCategory->save();
				Session::flash('flash_notice', trans("Item Category has been updated successfully.")); 
				return Redirect::to('adminpnlx/itemCategory');
			}
		}
	}


	public function view($id=""){
		$is_allowed = $this->check_section_permission(array('section'=>'item_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$Details	    =	DB::table('item_category')
								->where('item_category.id',$id)
								->first();

		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}else{	
			return View::make('admin.'.$this->model.'.view', compact("Details"));
		}
	}

	
	public function delete($id = ''){
		$is_allowed = $this->check_section_permission(array('section'=>'item_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$Details			=	ItemCategory::find($id); 
		if(empty($Details)) {
			return Redirect::back();
		}
		if($id){	
			$userModel					=	ItemCategory::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans("Item Category  has been deleted successfully.")); 
		}
		return Redirect::back();
	}

	// public function updateStatus($id = 0, $Status = 0){
	// 	if($Status == 0){
	// 		$statusMessage	=	trans("Warehouse has been deactivated.");
	// 		$staffDetails		=	Warehouse::find($id); 
	// 	}else{
	// 		$statusMessage	=	trans("Warehouse has been activated.");
	// 	}
	// 	$this->_update_all_status("warehouses",$id,$Status);	
	// 	Session::flash("flash_notice", $statusMessage); 
	// 	return Redirect::back();
	// } // end updateWarehousestatus()
	
	
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
