<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\Enquiry;
use App\Model\DropDown;
use App\Model\Acl;
use App\Model\UserPermission;
use App\Model\UserPermissionAction;
use App\Model\AclAdminAction;
use App\Model\AssignDealerStaff;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* StaffController Controller
*
* Add your methods in the class below
*
* This file will render views\StaffController\dashboard
*/
	class StaffController extends BaseController {
		
		public $model	=	'Staff';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function listStaff(){
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
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB
									->where('users.is_deleted',0)
									->where('users.user_role_id',ADMIN_STAFF_ROLE_ID)
									->leftjoin('dealer_location', 'users.dealer_location_name', '=', 'dealer_location.id')
									->select('users.*', 'dealer_location.location_name as dealer_location_name',
									DB::raw("(SELECT title FROM departments WHERE id = users.department) as department_name"),
									DB::raw("(SELECT title FROM designations WHERE id = users.designation) as designation_name"),
									DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"))
									->orderBy('users.'.$sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
		//echo'<pre>'; print_r($result); echo'</pre>'; die;
								
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("staff_search_data",$inputGet);

		$designations  			=	DB::table('designations')
									->orderBy('is_active',1)
									->orderBy('title','ASC')
									->pluck('title','id')
									->toArray();								


		return  View::make('admin.'.$this->model.'.index', compact('result','searchVariable','sortBy','order','query_string','designations'));
	}
		
	

	/**
	* Function for add Staff page
	*
	* @param null
	*
	* @return view page. 
	*/

	public function addStaff(){
		
		$stateList			=	DB::table('states')
								->where('status',1)
								->where('country_id',101)
								->orderBy('name','ASC')
								->pluck('name','id')
								->toArray();

		$departments		=	DB::table('departments')
								->where('is_active',1)
								->orderBy('title','ASC')
								->pluck('title','id')
								->toArray();


		
		// dealer name
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			$dealersList  			=	DB::table('users')
												->whereIn('id', $assignedDealer)
												->where('user_role_id',DEALER_ROLE_ID)
												->where('is_active',1)
												->where('is_deleted',0)
												->orderBy('full_name','ASC')
												->pluck('full_name','id')->toArray(); 
		}else{
			$dealersList  			=	DB::table('users')
											->where('user_role_id',DEALER_ROLE_ID)
											->where('is_active',1)
											->where('is_deleted',0)
											->orderBy('full_name','ASC')
											->pluck('full_name','id')->toArray(); 
		}

		$qualification =  $this->getDropDownListBySlug('qualification');
		$training_attended 	=  $this->getDropDownListBySlug('training-attended');
		
		$type = 1;				
		$aclModules		=	Acl::select('title','id')->where('type',$type)->where('is_active',1)->where('parent_id',0)->get(); 
					
		if(!empty($aclModules)){
			foreach($aclModules as &$aclModule){
				$aclModule['sub_module']	=	Acl::where('is_active',1)->where('type',$type)->where('parent_id',$aclModule->id)->select('title','id')->get();
				$module_ids			=	array();
				if(!empty($aclModule['sub_module'])){
					foreach($aclModule['sub_module'] as &$module){
						$module_id		=		$module->id;
						$module_ids[$module->id]		=		$module->id;
						$module['module']	=	AclAdminAction::where('admin_module_id',$module->id)->select('name','type','id')->orderBy('type','ASC')->get();
						 
						 
					}
				}
				$newArray	=	array(); 
				//$module_id				=	$module->id;
				$aclModule['extModule']	=	Acl::where('is_active',1)->where('type',$type)->whereIn('parent_id',$module_ids)->select('title','id')->get();
		 
				if(!empty($aclModule['extModule'])){ 
					foreach($aclModule['extModule'] as &$record){
						$action_id			=	$record->id;
						$record['module']	=	AclAdminAction::where('admin_module_id',$record->id)->select('name','type','id')->orderBy('type','ASC')->get(); 
					}
				}
				
				if(($aclModule['sub_module']->isEmpty()) && ($aclModule['extModule']->isEmpty())){
					$action_id			=	$aclModule->id;
					$aclModule['parent_module_action']	=	AclAdminAction::where('admin_module_id',$aclModule->id)->select('name','type','id')->orderBy('type','ASC')->get();  
				} 
			}
		}	
		


		return View::make('admin.'.$this->model.'.add', compact( 'departments','training_attended','dealersList','qualification','stateList','aclModules'));
	}
	
	/**
	* Function for save Staff
	*
	* @param null
	*
	* @return view page. 
	*/
	public function saveStaff(){ 
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		// echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			Validator::extend("custom_password", function($attribute, $value, $parameters) {
				if (preg_match("#[0-9]#", $value) && preg_match("#[a-zA-Z]#", $value)) {
					return true;
				} else {
					return false;
				}
			});
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 						=>	 'required',
					'city' 						=>	 'required',
					'email' 					=>	 'required|email|unique:users',
					'dob' 						=>	 'required',
					'mobile_number' 			=>	 'required|numeric|digits:10',
					'gender' 					=>	 'required',
					'department' 				=>	 'required',
					'address_1' 				=>	 'required',
					'zip' 			  			=>	 'required|numeric|integer',
					'state' 					=>	 'required',
					'remarks' 					=>	 'required',
					'designation' 				=>	 'required',
					'father_name' 				=>	 'required',
					"password"					=>	 "required|min:8|custom_password",
					'joining_date' 				=>	 'required',
					'qualification' 			=>	 'required',
					'documents' 				=>	 'mimes:'.CUSTOM_DOC_EXTENSION,
					'work_experience'			=>	  'numeric',
					'training_status'			=>	  'required',
					"training_institute" 		=>	 "required_if:training_status,==,".TRAINED,
					
				),
				array(
					'zip.required'							=> 	"The zipcode field is required.",
					'father_name.required'					=> 	"The father's name field is required.",
					'name.required'							=> 	'The staff name field is required.',
					'joining_date.required'					=> 	'The date of joining field is required.',
					'qualification.required'				=> 	'The educational qualification field is required.',
					'email.email'							=> 	'The email address is invalid.',
					'email.unique'							=> 	'This email address has already been taken.',
					'email.required'						=> 	'The email address field is required.',
					"documents.mimes"						=>	trans("Invalid document format."),
					"password.custom_password"				=>	trans("Password must be a combination of numeric and alphabets."),
					"password.required"						=>	trans("The password field is required."),
					"password.min"							=>	trans("Password must have minimum of 8 characters."),
					"dob.required"							=>	trans("The date of birth field is required."),
					"mobile_number.integer"					=>	trans("Phone number must have a numeric value."),
					"mobile_number.digits"					=>	trans("Phone number must have 10 digits."),
					"training_institute.required_if"		=>	trans("The training attended field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				
				$staff 								= 	new User;
				$staff->dealer_id					=	'';
				$staff->user_role_id				=	ADMIN_STAFF_ROLE_ID;
				$staff->full_name					=	Input::get('name');
				$staff->father_name					=	Input::get('father_name');
				$staff->gender						=	Input::get('gender');
				$staff->email						=	Input::get('email');
				$staff->password					=	Hash::make(Input::get("password"));
				$staff->phone_number				=	Input::get('mobile_number');
				$staff->dob							=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
				$staff->joining_date				=	!empty(Input::get('joining_date')) ? date('Y-m-d',strtotime(Input::get('joining_date'))) : '0000-00-00';
				$staff->address						=	Input::get('address_2');
				$staff->address_1					=	Input::get('address_1');
				$staff->dealer_location_name		=	'';
				$staff->city						=	Input::get('city');
				$staff->state_id					=	Input::get('state');
				$staff->qualification				=	Input::get('qualification');
				$staff->work_experience				=	Input::get('work_experience');
				$staff->training_status				=	Input::get('training_status');
				$staff->training_institute			=	(Input::get("training_status")== TRAINED)?Input::get("training_institute"):'';
				$staff->designation		 			=	Input::get('designation');
				$staff->department		 			=	Input::get('department');
				$staff->pincode						=	Input::get('zip');
				$staff->remarks						=	Input::get('remarks');
				$staff->referred_by					=	Input::get('referred_by');
				$staff->created_at					=  date("Y-m-d H:i:s");
				if(input::hasFile("documents")){
					$extension 			=	 Input::file("documents")->getClientOriginalExtension();
					$fileName			=	time()."-user-documents.".$extension;
					if(Input::file("documents")->move(STAFF_DOCUMENTS_ROOT_PATH, $fileName)){
						$staff->documents		=	$fileName;
					}
				}
				$staff->save();
				$staff_id_val	=	$staff->id;
				if($staff_id_val != ''){
					$staff_number		=	'#SF000'.$staff_id_val;
					User::where('id',$staff_id_val)->update(array('unique_id'=>$staff_number));
					
					if(!empty($formData['data'])){
						UserPermission::where('user_id',$staff_id_val)->delete();
						UserPermissionAction::where('user_id',$staff_id_val)->delete();
						foreach($formData['data'] as $data){ 
							$obj 					= 	array(); 
							$obj['user_id']			=  !empty($staff_id_val)?$staff_id_val:0;  
							$obj['admin_module_id']	=  !empty($data['department_id'])?$data['department_id']:0; 
							$obj['is_active']		=  !empty($data['value'])?$data['value']:0; 
							$userpermissiondata 	=   UserPermission::create($obj);
							$userpermissionID		=	$userpermissiondata->id;
						
							if(isset($data['module']) && !empty($data['module'])){
								foreach($data['module'] as $subModule){ 
									$objData 							= array(); 
									$objData['user_id']					=  !empty($staff_id_val)?$staff_id_val:0;  
									$objData['user_permission_id']		=  $userpermissionID; 
									$objData['admin_module_id']			=  !empty($data['department_id'])?$data['department_id']:0; 
									$objData['admin_sub_module_id'] 	=  !empty($subModule['department_module_id'])?$subModule['department_module_id']:0; 
									$objData['admin_module_action_id']	=  !empty($subModule['id'])?$subModule['id']:0; 
									$objData['is_active']				=  !empty($subModule['value'])?$subModule['value']:0; 
									UserPermissionAction::create($objData);
								}
							}
						} 
					}
					if(isset($formData['dealer_ids'])){
						if(is_array($formData['dealer_ids']) && !empty($formData['dealer_ids'])){
							foreach($formData['dealer_ids'] as $assign_id){
								if(!empty($assign_id)){
									$dealerStaff			=	new AssignDealerStaff;
									$dealerStaff->dealer_id	=	$assign_id;
									$dealerStaff->staff_id	=	$staff_id_val;
									$dealerStaff->save();
								}
							}
						}
					}
				}
				Session::flash("success",trans("Staff added successfully."));
				return Redirect::to('/adminpnlx/staff-management');
				//return Redirect::back();
			}
		}
	}
	
	public function editStaff($id = ""){
		$staffDetails	    =	User::where('id',$id)
								->where('user_role_id',ADMIN_STAFF_ROLE_ID)
								->first();
		if(empty($staffDetails)) {
			return Redirect::back();
		}	
		$cityList		=	DB::table('cities')
							->where('state_id',$staffDetails->state_id)
							->distinct('name')
							->pluck('name','id')
							->toArray();
		
		
		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',101)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray();
	
		$departments		=	DB::table('departments')
								->where('is_active',1)
								->orderBy('title','ASC')
								->pluck('title','id')
								->toArray();

		$designations		=	DB::table('designations')
								->where('department_id',$staffDetails->department)
								->where('is_active',1)
								->orderBy('title','ASC')
								->pluck('title','id')
								->toArray();
		$qualification =  $this->getDropDownListBySlug('qualification');
		// dealer name
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			$dealersList  			=	DB::table('users')
												->whereIn('id', $assignedDealer)
												->where('user_role_id',DEALER_ROLE_ID)
												->where('is_active',1)
												->where('is_deleted',0)
												->orderBy('full_name','ASC')
												->pluck('full_name','id')->toArray(); 
		}else{
			$dealersList  			=	DB::table('users')
											->where('user_role_id',DEALER_ROLE_ID)
											->where('is_active',1)
											->where('is_deleted',0)
											->orderBy('full_name','ASC')
											->pluck('full_name','id')->toArray(); 
		}	
		$type = 1;				
		$aclModules		=	Acl::select('title','id',DB::Raw("(select is_active from user_permissions where user_id = $id AND admin_module_id = admin_modules.id LIMIT 1) as active"))->where('type',$type)->where('is_active',1)->where('parent_id',0)->get(); 
		$assignedDrealers	=	AssignDealerStaff::where('staff_id', $id)->pluck('dealer_id')->toArray();
		if(!empty($assignedDrealers)){
			$assignedDrealersStr	=	implode(',', $assignedDrealers);
		}else{
			$assignedDrealersStr	=	'';
		}
		// echo $assignedDrealersStr;die;
		if(!empty($aclModules)){
			foreach($aclModules as &$aclModule){
				$aclModule['sub_module']	=	Acl::where('is_active',1)->where('type',$type)->where('parent_id',$aclModule->id)->select('title','id')->get();
				$module_ids			=	array();
				if(!empty($aclModule['sub_module'])){
					foreach($aclModule['sub_module'] as &$module){
						$module_id		=		$module->id;
						$module_ids[$module->id]		=		$module->id;
						$module['module']	=	AclAdminAction::where('admin_module_id',$module->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $id AND admin_sub_module_id = $module_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get();
						 
						 
					}
				}
				$newArray	=	array(); 
				//$module_id				=	$module->id;
				$aclModule['extModule']	=	Acl::where('is_active',1)->where('type',$type)->whereIn('parent_id',$module_ids)->select('title','id')->get();
		 
				if(!empty($aclModule['extModule'])){ 
					foreach($aclModule['extModule'] as &$record){
						$action_id			=	$record->id;
						$record['module']	=	AclAdminAction::where('admin_module_id',$record->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $id AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get(); 
					}
				}
				
				if(($aclModule['sub_module']->isEmpty()) && ($aclModule['extModule']->isEmpty())){
					$action_id			=	$aclModule->id;
					$aclModule['parent_module_action']	=	AclAdminAction::where('admin_module_id',$aclModule->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $id AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get();  
				} 
			}
		}
			
		return View::make('admin.'.$this->model.'.edit', compact("staffDetails",'dealersList','assignedDrealers','cityList','stateList','qualification','departments','designations','aclModules'));
	
		
	} // end editUser()
	
	public function updateStaff($id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData					=	Input::all();
		// echo "<pre>";print_r($formData);die;
		if(!empty($formData)){
			Validator::extend("custom_password", function($attribute, $value, $parameters) {
				if (preg_match("#[0-9]#", $value) && preg_match("#[a-zA-Z]#", $value)) {
					return true;
				} else {
					return false;
				}
			});
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 						=>	 'required',
					'city' 						=>	 'required',
					'dob' 						=>	 'required',
					'mobile_number' 			=>	 'required|numeric|digits:10',
					'gender' 					=>	 'required',
					'department' 				=>	 'required',
					'address_1' 				=>	 'required',
					'zip' 			  			=>	 'required|numeric|integer',
					'state' 					=>	 'required',
					'remarks' 					=>	 'required',
					'designation' 				=>	 'required',
					'father_name' 				=>	 'required',
					'joining_date' 				=>	 'required',
					'qualification' 			=>	 'required',
					'documents' 				=>	 'mimes:'.CUSTOM_DOC_EXTENSION,
					'training_status'			=>	 'required',
					"training_institute" 		=>	 "required_if:training_status,==,".TRAINED,
					
				),
				array(
					'zip.required'							=> 	"The zipcode field is required.",
					'father_name.required'					=> 	"The father's name field is required.",
					'name.required'							=> 	'The staff name field is required.',
					'documents.required'					=> 	'Please upload document.',
					'joining_date.required'					=> 	'The date of joining field is required.',
					'qualification.required'				=> 	'The educational qualification field is required.',
					"documents.mimes"						=>	trans("Invalid document format."),
					"mobile_number.numeric"					=>	trans("Phone number must have a numeric value."),
					"password.custom_password"				=>	trans("Password must have a combination of numeric and alphabets."),
					"password.required"						=>	trans("The password field is required."),
					"password.min"							=>	trans("Password must have minimum of 8 characters."),
					"dob.required"							=>	trans("The date of birth field is required."),
					"mobile_number.integer"					=>	trans("Phone number must have a numeric value."),
					"mobile_number.digits"					=>	trans("Phone number must have 10 digits."),
					"training_institute.required_if"		=>	trans("The training attended field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{
				
				$staff								= 	User::find($id);
				$staff->full_name					=	Input::get('name');
				$staff->father_name					=	Input::get('father_name');
				$staff->gender						=	Input::get('gender');
				$staff->phone_number				=	Input::get('mobile_number');
				$staff->dob							=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
				$staff->joining_date				=	!empty(Input::get('joining_date')) ? date('Y-m-d',strtotime(Input::get('joining_date'))) : '0000-00-00';
				$staff->address						=	Input::get('address_2');
				$staff->address_1					=	Input::get('address_1');
				$staff->dealer_location_name		=	'';
				$staff->city						=	Input::get('city');
				$staff->state_id					=	Input::get('state');
				$staff->qualification				=	Input::get('qualification');
				$staff->work_experience				=	Input::get('work_experience');
				$staff->training_status				=	Input::get('training_status');
				$staff->training_institute			=	 (Input::get("training_status")== TRAINED)?Input::get("training_institute"):'';
				$staff->designation		 			=	Input::get('designation');
				$staff->department		 			=	Input::get('department');
				$staff->pincode						=	Input::get('zip');
				$staff->remarks						=	Input::get('remarks');
				$staff->referred_by					=	Input::get('referred_by');
				$staff->updated_at					=  date("Y-m-d H:i:s");
				if(input::hasFile("documents")){
					$extension 			=	 Input::file("documents")->getClientOriginalExtension();
					$fileName			=	time()."-user-documents.".$extension;
					if(Input::file("documents")->move(STAFF_DOCUMENTS_ROOT_PATH, $fileName)){
						$staff->documents		=	$fileName;
					}
				}
				
				$staff->save();
				$staff_id_val = $staff->id;
				// assign to dealer
				if(isset($formData['dealer_ids'])){
					if(is_array($formData['dealer_ids']) && !empty($formData['dealer_ids'])){
						AssignDealerStaff::where('staff_id', $id)->delete();
						foreach($formData['dealer_ids'] as $assign_id){
							if(!empty($assign_id)){
								$dealerStaff			=	new AssignDealerStaff;
								$dealerStaff->dealer_id	=	$assign_id;
								$dealerStaff->staff_id	=	$staff_id_val;
								$dealerStaff->save();
							}
						}
					}
				}else{
					AssignDealerStaff::where('staff_id', $id)->delete();
				}


				if(!empty($formData['data'])){
					UserPermission::where('user_id',$id)->delete();
					UserPermissionAction::where('user_id',$id)->delete();
					foreach($formData['data'] as $data){ 
						$obj 					= 	array(); 
						$obj['user_id']			=  !empty($id)?$id:0;  
						$obj['admin_module_id']	=  !empty($data['department_id'])?$data['department_id']:0; 
						$obj['is_active']		=  !empty($data['value'])?$data['value']:0; 
						$userpermissiondata 	=   UserPermission::create($obj);
						$userpermissionID		=	$userpermissiondata->id;
					
						if(isset($data['module']) && !empty($data['module'])){
							foreach($data['module'] as $subModule){ 
								$objData 							= array(); 
								$objData['user_id']					=  !empty($id)?$id:0;  
								$objData['user_permission_id']		=  $userpermissionID; 
								$objData['admin_module_id']			=  !empty($data['department_id'])?$data['department_id']:0; 
								$objData['admin_sub_module_id'] 	=  !empty($subModule['department_module_id'])?$subModule['department_module_id']:0; 
								$objData['admin_module_action_id']	=  !empty($subModule['id'])?$subModule['id']:0; 
								$objData['is_active']				=  !empty($subModule['value'])?$subModule['value']:0; 
								UserPermissionAction::create($objData);
							}
						}
					} 
				}
				
				Session::flash('flash_notice', trans("Staff has been updated successfully.")); 
				return Redirect::to('/adminpnlx/staff-management');
			}
		}
	}


	
	public function viewStaff($id=""){
		
		$staffDetails	    =	DB::table('users')
								->where("users.id",$id)
								->where('users.user_role_id',ADMIN_STAFF_ROLE_ID)
								->leftjoin('dealer_location', 'users.dealer_location_name', '=', 'dealer_location.id')
								->select('users.*', 'dealer_location.location_name as dealer_location_name',
								DB::raw("(SELECT name FROM states WHERE id = users.state_id) as state"),
								DB::raw("(SELECT title FROM departments WHERE id = users.department) as department"),
								DB::raw("(SELECT name FROM dropdown_managers WHERE id = users.qualification) as qualification"),
								DB::raw("(SELECT title FROM designations WHERE id = users.designation) as designation"),
								DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"),
								DB::raw("(SELECT name FROM dropdown_managers WHERE id = users.training_institute) as training_institute_name"))
								->first();
		$assignedDrealers	=	AssignDealerStaff::where('staff_id', $id)
														->leftjoin('users', 'users.id', '=','assign_dealer_staff.dealer_id')
														->pluck('users.full_name')->toArray();

		if(empty($staffDetails)) {
			return Redirect::back();
		}else{
			
			return View::make('admin.'.$this->model.'.view', compact('staffDetails','assignedDrealers'));
		}
	}

	
	public function deleteStaff($id = ''){
		
		$staffDetails			=	User::find($id); 
		if(empty($staffDetails)) {
			return Redirect::back();
		}
		if($id){	
			$email 						=	'delete_'.$id .'_'.$staffDetails->email;
			$userModel					=	User::where('id',$id)->update(array('is_deleted'=>1,'email'=>$email,'deleted_at'=>date("Y-m-d H:i:s")));
			Session::flash('flash_notice',trans("Staff has been deleted successfully.")); 
		}
		return Redirect::back();
	}

	public function updateStaffStatus($id = 0, $Status = 0){
		if($Status == 0){
			$statusMessage	=	trans("Staff deactivated successfully.");
			$staffDetails		=	User::find($id); 
		}else{
			$statusMessage	=	trans("Staff activated successfully.");
		}
		$this->_update_all_status("users",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} // end updateStaffStatus()
	
	public function exportStaffToExcel(){
		$searchData				=	Session::get('staff_search_data');
		$DB 					= 	User::query();
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
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB
										->where('users.is_deleted',0)
										->where('users.user_role_id',ADMIN_STAFF_ROLE_ID)
										->leftjoin('dealer_location', 'users.dealer_location_name', '=', 'dealer_location.id')
										->select('users.*', 'dealer_location.location_name as dealer_location_name',
										DB::raw("(SELECT title FROM departments WHERE id = users.department) as department_name"),
										DB::raw("(SELECT title FROM designations WHERE id = users.designation) as designation_name"),DB::raw("(SELECT name FROM cities WHERE id = users.city) as city"))
										->get()->toArray();		
			
									
		$genderArr = Config::get("gender_type_array");														
		$thead = array();
		$thead[]		= array("Unique Staff Number","Name","Father's Name","Department name","Designation","Educational Qualification","Gender","DOB","Email-ID","Dealer Name","Mobile Number","City","Joining Date","Work Experience","Training Status","Training Attended","Status");
		if(!empty($result)) {
			foreach($result as $record) {
				if($record['is_active']	==1){
					$status = "Activated";
				}else{
					$status = "Deactivated";
				}
				$unique_id							=	!empty($record['unique_id'])?$record['unique_id']:'';
				$full_name							=	!empty($record['full_name'])?$record['full_name']:'';
				$father_name							=	!empty($record['father_name'])?$record['father_name']:'';
				$qualification							=	!empty($record['qualification'])?$record['qualification']:'';
				
				
				$department_name					=	!empty($record['department_name'])?$record['department_name']:'';
				$designation_name					=	!empty($record['designation_name'])?$record['designation_name']:'';
				$gender								=	!empty($record['gender'])?$genderArr[$record['gender']]:'';
				$dob								=	!empty($record['dob'])?date(Config::get("Reading.date_format") , strtotime($record['dob'])):'';
				$email								=	!empty($record['email'])? $record['email']:'';
				$id									=	!empty($record['unique_id'])?$record['unique_id']:'';
				$dealer_location_name				=	!empty($record['dealer_location_name'])?$record['dealer_location_name']:'';
				$phone_number						=	!empty($record['phone_number'])?$record['phone_number']:'';
				$city								=	!empty($record['city'])?$record['city']:'';
				
				
				$joining_date						=	!empty($record['joining_date'])?date(Config::get("Reading.date_format") , strtotime($record['joining_date'])):'';
				
				$work_experience								=	!empty($record['work_experience'])?$record['work_experience'].' -Years':'';
				
				
				$Training_status		  						= Config::get("Training_type_array");
				if(array_key_exists($record['training_status'],$Training_status)){
					$traingStatus = $Training_status[$record['training_status']];
				}else{
					$traingStatus = '';
				}
				
				if($record['training_status'] ==  TRAINED){
					$trainingAttended = $record['training_institute'];
				}else{
					$trainingAttended = '';
				}
				
				
				
				$status								=	!empty($status)?$status:'';
				
				
				
				$thead[]		= array($unique_id,$full_name,$father_name,$department_name,$designation_name,$qualification,$gender,$dob,$email,$dealer_location_name,$phone_number,$city,$joining_date,$work_experience,$traingStatus,$trainingAttended,$status);
			}
		}								
		//echo '<pre>'; print_r($thead); die;					
		return  View::make('admin.'.$this->model.'.export_excel', compact('thead'));
		
	}

	public function GetDesignation(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData		=	 Input::all();
		if($formData != ''){
			$department_id		=	Input::get("department"); 
			$designations 			=	DB::table('designations')
											->where('department_id',$department_id)
											->orderBy('is_active',1)
											->orderBy('title','ASC')
											->pluck('title','id');

			$html = '<option value="">'.trans("Select Designations").'</option>';
			if(isset($designations ) && !empty($designations )){
				foreach($designations  as $key=>$value){
					$html .= '<option value="'.$key.'">'.$value.'</option>';
				}
			}
			$response	=	array(
				'success' 			=>	'1',
				'options' 			=>	$html,
				'designation_id' 	=>	$formData['designation_id'],
				'errors' 			=>	 trans("Designations added successfully.")
				); 
			
		}else{
			$response	=	array(
				'success' 			=>	'2',
				'designation_id' 	=>	'',
				'errors' 			=>	 trans("Something went wrong. Please try again.")
			); 
		} 
		return Response::json($response); 
		die;
	}
	public function updateStatusWithReson(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData		=	 Input::all();
		if($formData != ''){
			$status			=	Input::get("status"); 
			$staff_id		=	Input::get("staff_id"); 
			$reason			=	Input::get("reason"); 
			if($status == 0){
				$statusMessage	=	trans("Staff deactivated successfully.");
			}else{
				$statusMessage	=	trans("Staff activated successfully.");
			}
			
			$staffData									= 	User::find($staff_id);
			$staffData->is_active						=	$status;
			$staffData->status_reason					=	$reason;
			$staffData->save();
			
			Session::flash('flash_notice', $statusMessage); 
			$response	=	array(
				'success' 			=>	'1',
				'message' 			=>	$statusMessage,
				); 
		}else{
			$response	=	array(
				'success' 			=>	'2',
				'message' 			=>	'2',
			); 
		} 
		return Response::json($response); 
		die;
	}
	
	public function changePassword($id=""){
		$staffDetails	    =	DB::table('users')->where("users.id",$id)->where('users.user_role_id',ADMIN_STAFF_ROLE_ID)->select('users.*')->first();
		if(empty($staffDetails)) {
			return Redirect::back();
		}else{
			
			return View::make('admin.'.$this->model.'.changePassword', compact('staffDetails'));
		}
	}
	
	public function savePassword($id){ 
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			Validator::extend("custom_password", function($attribute, $value, $parameters) {
				if (preg_match("#[0-9]#", $value) && preg_match("#[a-zA-Z]#", $value)) {
					return true;
				} else {
					return false;
				}
			});
			$validator 					=	Validator::make(
				Input::all(),
				array(
					"password"					=>	 "required|min:8|custom_password",
					'confirm_password'  		=> 'required|min:8|same:password', 
					
				),
				array(
					"password.custom_password"				=>	trans("Password must be a combination of numeric and alphabets."),
					"password.required"						=>	trans("The password field is required."),
					"password.min"							=>	trans("Password must have minimum of 8 characters."),
					"confirm_password.same"					=>	trans("Your passwords do not match."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				$staff								= 	User::find($id);
				$staff->password					=	Hash::make(Input::get("password"));
				$staff->save();
				Session::flash("success",trans("Password changed successfully."));
				return Redirect::to('/adminpnlx/staff-management');
				//return Redirect::back();
			}
		}
	}
	
} //end StaffController()
