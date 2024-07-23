<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\Designation;
use App\Model\Department;
use App\Model\DealerLocation;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb; 
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* DepartmentController Controller
*
* Add your methods in the class below
*
* This file will render views\DepartmentController\dashboard
*/
	class DepartmentController extends BaseController {
		
		public $model	=	'Department';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function listDepartment(){
		$DB 					= 	Department::query();
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
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB
										->where('is_deleted',0)
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));
			//echo'<pre>'; print_r($result); echo'</pre>'; die;
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			return  View::make('admin.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string'));
		}
		
	

	/**
	* Function for add Department page
	*
	* @param null
	*
	* @return view page. 
	*/

	public function addDepartment(){
		return View::make('admin.'.$this->model.'.add');
	}
	
	/**
	* Function for save Department
	*
	* @param null
	*
	* @return view page. 
	*/
	public function saveDepartment(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					
					'department_name' 		=>	 'required',
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				
				$Department 					= 	new Department;
				$Department->title   			= 	Input::get('department_name');
				$Department->created_at			= 	date('Y-m-d H:i:s');
				$Department->save();

				$Department_id					=	$Department->id;

				if(!empty($formData['designation'])){
					foreach ($formData['designation'] as $designation){
						if($designation != ''){
							$Designation             			= new Designation;
							$Designation->department_id 		= $Department_id;
							$Designation->title   				= $designation;
							$Designation->created_at			= 	date('Y-m-d H:i:s');
							$Designation->save(); 
						}
					}
				}
				
				
				Session::flash("success",trans("Dealer department added successfully."));
				return Redirect::to('adminpnlx/departments-management');
				//return Redirect::back();
			}
		}
	}
	
	public function editDepartment($id = ""){
		$departmentDetails		=	DB::table('departments')
									->where('id',$id)
									->first();

		if(empty($departmentDetails)) {
			return Redirect::back();
		}	

		$designationsDetails	=	DB::table('designations')
									->where('department_id',$id)
									->get();
									
		if(empty($designationsDetails)) {
			return Redirect::back();
		}	
		return View::make('admin.'.$this->model.'.edit', compact("departmentDetails",'designationsDetails'));
		
	} // end departmentDetails()
	
	public function updateDepartment($id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					
					'department_name' 		=>	 'required',
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				
				$Department 					= Department::findorFail($id);
				$Department->title   			= Input::get('department_name');
				$Department->updated_at			= 	date('Y-m-d H:i:s');
				$Department->save();

				$Department_id					=	$Department->id;
				Designation::where('Department_id', '=', $Department_id)->delete();

				if(!empty($formData['designation'])){
					foreach ($formData['designation'] as $designation){
						if($designation != ''){
							$Designation             				=  new Designation;
							$Designation->department_id 			= $Department_id;
							$Designation->title   					= $designation;
							$Designation->updated_at				= 	date('Y-m-d H:i:s');
							$Designation->save(); 
						}
					}
				}
				
				Session::flash('flash_notice', trans("Dealer department has been updated successfully.")); 
				return Redirect::to('adminpnlx/departments-management');
			}
		}
	}


	
	public function deleteDepartment($id = ''){
		
		$DepartmentDetails			=	Department::find($id); 
		if(empty($DepartmentDetails)) {
			return Redirect::back();
		}
		if($id){	
			$userModel					=	Department::where('id',$id)->delete();
			$userModel					=	Designation::where('department_id',$DepartmentDetails->id)->delete();
			Session::flash('flash_notice',trans("Department has been deleted successfully.")); 
		}
		return Redirect::back();
	}

	public function updateDepartmentStatus($Id = '', $Status = ''){
		if($Id != '' && $Status != ''){
			if($Status == 0	){
				$statusMessage	=	trans("Department deactivated successfully.");
			}else{
				$statusMessage	=	trans("Department activated successfully.");
			}
			$this->_update_all_status('departments',$Id,$Status);
		}else{
			$statusMessage	=	trans("Something went wrong. Please try again.");
		}
		Session::flash('flash_notice',  $statusMessage); 
		return Redirect::to('adminpnlx/departments-management');
	}	
	
		
	public function removeDesignation(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		$Designation_id  = $formData['Designation_id'];
		$DesignationDetails			=	Designation::find($Designation_id); 
		
		if(empty($DesignationDetails)) {
			return Redirect::back();
		}
		if($Designation_id != ''){	
			$userModel					=	Designation::where('id',$Designation_id)->delete();
		}
		return Redirect::back();
	}




} //end dealerNetworkController()
