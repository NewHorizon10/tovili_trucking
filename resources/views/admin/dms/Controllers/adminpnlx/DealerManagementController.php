<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Users;
use App\Model\DropDown;
use App\Model\Acl;
use App\Model\UserPermission;
use App\Model\UserPermissionAction;
use App\Model\AclAdminAction;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* AdminDashBoard Controller
*
* Add your methods in the class below
*
* This file will render views\admin\dashboard
*/
	class DealerManagementController extends BaseController {
	/**
	* Function for display adminpnlx dashboard
	*
	* @param null
	*
	* @return view page. 
	*/
	public function dealerList(){	
		
		$DB			=	Users::query();
		$state	 	=	DB::table('states')
						->where("status",1)
						->where("country_id",COUNTRY_ID)
						->pluck("name","id")->toArray();
		$searchVariable				=	array(); 
		$inputGet					=	Input::get();
		if((Input::get())){
			$searchData				=	Input::get();
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
				if(!empty($fieldValue) && $fieldName != 'state_name' && $fieldName != 'phone_number'){
					$DB->where("users.$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
				if(!empty($fieldValue) && $fieldName == 'phone_number'){
					$DB->where(function ($query) use($fieldValue) {
							$query->where('users.phone_number', 'like', '%' . $fieldValue . '%')
							->orWhere('users.telephone', 'like', '%' . $fieldValue . '%');
					  });
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
				if($fieldName == 'state_name' && !empty($fieldValue)){
					$DB->where("s.id",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy 					= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'users.id';
		$order  					= 	(Input::get('order')) ? Input::get('order')   : 'users.DESC';
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if(!empty($assignedDealer)){
				$DB->whereIn('users.id', $assignedDealer);
			}
		}
		$result 					= 	$DB->where("users.user_role_id",DEALER_ROLE_ID)
										->where('users.is_deleted',0)
										->leftjoin("states as s","s.id",'=','users.state_id')
										->leftjoin("cities","cities.id",'=','users.city')
										->select('users.*','s.name as state_name','cities.name as city_name')
										->orderBy($sortBy,$order)
										->paginate(Config::get("Reading.records_per_page"));
										
		$complete_string			=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("dealers_search_data",$inputGet);
		
		//echo '<pre>'; print_r($result); die;
		return View::make('admin.dealerManagement.index',compact('result','searchVariable','sortBy','order','query_string','state'));
	}
	//end function dealerList()


	/**
	* Function for dealer Add
	*
	* @param null
	*
	* @return view page. 
	*/


	public function dealerAdd(){
		
		$state=	DB::table('states')
				->where("status",1)
				->where("country_id",COUNTRY_ID)
				->pluck("name","id")->toArray();
	
		$sales_person=DB::table('users')
					->where('user_role_id',STAFF_USER_ROLE_ID)
					->where('department','sales')
					->where('designation','Sales Person')
					->pluck('full_name','id')
					->toArray();
		$distributors = DropDown::where('dropdown_type','distributors')
						->where("is_active",1)
						->orderBy('name', 'ASC')
						->pluck('name','id');
		$type = 0;				
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
		//echo '<pre>'; print_r($aclModules); die;			
			
		return  View::make('admin.dealerManagement.add',compact('state','sales_person','distributors','aclModules'));
	}
	// end function dealerAdd()


	/**
	* Function for dealer Save
	*
	* @param null
	*
	* @return view page. 
	*/

	public function dealerSave(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		//print_r($thisData);die;
		if(!empty($thisData)){
			Validator::extend("custom_password", function($attribute, $value, $parameters) {
				if (preg_match("#[0-9]#", $value) && preg_match("#[a-zA-Z]#", $value)) {
					return true;
				} else {
					return false;
				}
			});
			$validator	 =	 Validator::make(
								$thisData,
								array(
									'name' 					=>	 'required',
									'mobile_no' 			=>	 'required|integer|digits:10',
									'phone_number' 			=>	'integer|digits:10',
									'pincode' 				=>	 'required|numeric',
									'email' 				=>	 'unique:users,email|required',
									'address' 				=>	 'required',
									'contact_person' 				=>	 'required',
									'city' 					=>	 'required',
									//'credit_limit' 			=>	 'required|numeric',
									'state' 				=>	 'required',
									'gstin_number' 			=>	 'required',
									// 'cin_number' 			=>	 'required',
									// 'dealer_logo' 			=>	 'required',
									//'sales_person' 		=>	 'required',
									//'distributor' 			=>	 'required',
									'password'				=> 	 'required|min:8|custom_password',
									'confirm_password'		=> 	 'required|same:password',
									
								),
								array
								(
									"password.custom_password"			=>	trans("Password must have be a combination of numeric and alphabets."),
									"password.required"					=>	trans("The password field is required."),
									"password.min"						=>	trans("Password must have minimum of 8 characters."),
									"confirm_password.required"			=>	trans("The confirm password field is required."),
									"confirm_password.same"				=>	trans("Password and confirm password must match."),
									"email.required"					=>	trans("The email address field is required."),
									"email.unique"						=>	trans("This email address is already exist."),
									"gstin_number.required"				=>	trans("GSTIN number is required."),
									// "cin_number.required"				=>	trans("CIN number is required."),
									// "dealer_logo.required"				=>	trans("Dealer logo field is required."),
									"mobile_no.required"				=>	trans("The mobile number field is required."),
									"mobile_no.integer"					=>	trans("The mobile number must have a numeric value."),
									"mobile_no.numeric"					=>	trans("The mobile number must be a number."),
									"mobile_no.digits"					=>	trans("The mobile number must have 10 digits."),
									
								)	
								
							);
			if ($validator->fails()) {	
				return Redirect::to('adminpnlx/dealer-management/add-dealer')->withErrors($validator)->withInput();
			}else{
				DB::beginTransaction();
				$dealer 					= 	new Users;
				$dealer->user_role_id		=	DEALER_ROLE_ID;
				$dealer->full_name   		= 	Input::get('name');
				$dealer->phone_number   	= 	Input::get('mobile_no');
				$dealer->address   			= 	Input::get('address');
				$dealer->telephone   		= 	Input::get('phone_number');
				$dealer->pincode   			= 	Input::get('pincode');
				$dealer->contact_person   	= 	Input::get('contact_person');
				$dealer->city   			= 	Input::get('city');
				$dealer->credit_limit   	= 	Input::get('credit_limit');
				$dealer->state_id   		= 	Input::get('state');
				$dealer->sales_person_id 	= 	Input::get('sales_person');
				$dealer->email 				= 	Input::get('email');
				$dealer->password 			= 	Hash::make(Input::get("password"));
				$dealer->distributor_id 	= 	Input::get('distributor');
				$dealer->gstin_number 		= 	Input::get('gstin_number');
				$dealer->cin_number 		= 	Input::get('cin_number');
				$dealer->is_active			=  	1;
				$dealer->created_at			=  	date("Y-m-d H:i:s");
				$dealer->updated_at			=  	date("Y-m-d H:i:s");
				$dealer->save();
				DB::commit();
				$id  = $dealer->id;
				if(!empty($id)){
					$dealer_code		=	'#DC000'.$id;
					Users::where('id',$id)->update(array('dealer_code'=>$dealer_code));
					// dealer logo
					$file               = 	Input::file('dealer_logo');
					if($file){
						$extension 	        = 	$file->getClientOriginalExtension();
						$fileName			=	time().'-dealer-logo.'.$extension;
						$file->move(DEALER_LOGO_ROOT_PATH, $fileName);
						Users::where('id',$id)->update(array('dealer_logo'=>$fileName));
					}
					
					if(!empty($thisData['data'])){
						UserPermission::where('user_id',$id)->delete();
						UserPermissionAction::where('user_id',$id)->delete();
						foreach($thisData['data'] as $data){ 
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
				}
				DB::commit();
				
				Session::flash('flash_notice', trans("Dealer has been added successfully")); 
				return Redirect::to('adminpnlx/dealer-management');
			}
		}
	}
	// end function dealerSave()
		

	/**
	* Function for dealer Edit
	*
	* @param userId
	*
	* @return view page. 
	*/
		public function dealerEdit($userId = 0){
			$userDetails			=	Users::find($userId); 
			if(empty($userDetails)) {
				return Redirect::back();
			}
			$state = DB::table('states')
					->where("status",1)
					->where("country_id",COUNTRY_ID)
					->pluck("name","id")->toArray();

			$sales_person = DB::table('users')
							->where('user_role_id',STAFF_USER_ROLE_ID)
							->where('department','sales')
							->where('designation','Sales Person')
							->pluck('full_name','id')
							->toArray();
			$distributors = DropDown::where('dropdown_type','distributors')
							->where("is_active",1)
							->orderBy('name', 'ASC')
							->pluck('name','id');
			$cityList	=	DB::table('cities')
							->where('state_id',$userDetails->state_id)
							->distinct('name')
							->pluck('name','id')
							->toArray();
			$type = 0;				
			$aclModules		=	Acl::select('title','id',DB::Raw("(select is_active from user_permissions where user_id = $userId AND admin_module_id = admin_modules.id LIMIT 1) as active"))->where('type',$type)->where('is_active',1)->where('parent_id',0)->get(); 
					
			if(!empty($aclModules)){
				foreach($aclModules as &$aclModule){
					$aclModule['sub_module']	=	Acl::where('is_active',1)->where('type',$type)->where('parent_id',$aclModule->id)->select('title','id')->get();
					$module_ids			=	array();
					if(!empty($aclModule['sub_module'])){
						foreach($aclModule['sub_module'] as &$module){
							$module_id		=		$module->id;
							$module_ids[$module->id]		=		$module->id;
							$module['module']	=	AclAdminAction::where('admin_module_id',$module->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $userId AND admin_sub_module_id = $module_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get();
							 
							 
						}
					}
					$newArray	=	array(); 
					//$module_id				=	$module->id;
					$aclModule['extModule']	=	Acl::where('is_active',1)->where('type',$type)->whereIn('parent_id',$module_ids)->select('title','id')->get();
			 
					if(!empty($aclModule['extModule'])){ 
						foreach($aclModule['extModule'] as &$record){
							$action_id			=	$record->id;
							$record['module']	=	AclAdminAction::where('admin_module_id',$record->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $userId AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get(); 
						}
					}
					
					if(($aclModule['sub_module']->isEmpty()) && ($aclModule['extModule']->isEmpty())){
						$action_id			=	$aclModule->id;
						$aclModule['parent_module_action']	=	AclAdminAction::where('admin_module_id',$aclModule->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $userId AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get();  
					} 
				}
			}		
			return View::make("admin.dealerManagement.edit", compact("userDetails",'cityList',"state","sales_person","distributors","aclModules"));
			
		}
		 // end function dealerEdit()


	/**
	* Function for dealer Update
	*
	* @param userId
	*
	* @return view page. 
	*/

	public function dealerUpdate($userId){	
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData						=	Input::all(); 
		//echo "<pre>";print_r($thisData);die;
		
		if(!empty($thisData)){
			Validator::extend("custom_password", function($attribute, $value, $parameters) {
				if (preg_match("#[0-9]#", $value) && preg_match("#[a-zA-Z]#", $value)) {
					return true;
				} else {
					return false;
				}
			});
				$validator 					= 	Validator::make(
					Input::all(),
					array(
						'name' 					=>	 'required',
						'contact_person' 		=>	 'required',
						'mobile_no' 			=>	 'required|integer|digits:10',
						'phone_number' 			=>	 'integer|digits:10',
						'pincode' 				=>	 'required|numeric|integer',
						'address' 				=>	 'required',
						'city' 					=>	 'required',
						//'credit_limit' 		=>	 'required|numeric',
						'state' 				=>	 'required',
						'gstin_number' 			=>	 'required',
						// 'cin_number' 			=>	 'required',
						//'dealer_logo' 		=>	 'required',
						//'distributor' 		=>	 'required',
						//'phone_number' 		=>	 'required|numeric|integer',
						'password'				=>	 'min:8|custom_password',
						'confirm_password'		=>	 'same:password', 
					),
					array
					(
						"password.custom_password"			=>	trans("Password must have be a combination of numeric and alphabets."),
						"password.min"						=>	trans("Password must have minimum of 8 characters."),
						"confirm_password.same"				=>	trans("Password and confirm password must match."),
						"gstin_number.required"				=>	trans("The GSTIN number is required."),
						// "cin_number.required"				=>	trans("The CIN number is required."),
						"mobile_no.required"				=>	trans("The mobile number field is required."),
						"mobile_no.integer"					=>	trans("The mobile number must have a numeric value."),
						"mobile_no.numeric"					=>	trans("The mobile number must be a number."),
						"mobile_no.digits"					=>	trans("The mobile number must have 10 digits."),
					)	
				);
				//print_r($thisData);die;
				if ($validator->fails()){
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
					DB::beginTransaction();
					
				$dealer1 					= 	Users::find($userId);
				$dealer1->user_role_id		=	DEALER_ROLE_ID;
				$dealer1->full_name   		= 	Input::get('name');
				$dealer1->phone_number   	= 	Input::get('mobile_no');
				$dealer1->address   		= 	Input::get('address');
				$dealer1->telephone   		= 	Input::get('phone_number');
				$dealer1->pincode   		= 	Input::get('pincode');
				$dealer1->contact_person   	= 	Input::get('contact_person');
				$dealer1->city   			= 	Input::get('city');
				$dealer1->credit_limit   	= 	Input::get('credit_limit');
				$dealer1->state_id   		= 	Input::get('state');
				$dealer1->sales_person_id 	= 	Input::get('sales_person');
				if(Input::get("password") != ''){
					$dealer1->password 			= 	Hash::make(Input::get("password"));
				}
				$dealer1->distributor_id 	= 	Input::get('distributor');
				$dealer1->gstin_number 		= 	Input::get('gstin_number');
				$dealer1->cin_number 		= 	Input::get('cin_number');
				$dealer1->is_active			=  	1;
				$dealer1->is_verified		=  	1;
				$dealer1->created_at		=  	date("Y-m-d H:i:s");
				$dealer1->updated_at		=  	date("Y-m-d H:i:s");
				if(Input::hasFile('dealer_logo')){
					$file               = 	Input::file('dealer_logo');
					if($file){
						$extension 	        	= 	$file->getClientOriginalExtension();
						$fileName				=	time().'-dealer-logo.'.$extension;
						$file->move(DEALER_LOGO_ROOT_PATH, $fileName);
						$dealer1->dealer_logo	=	$fileName;
					}
				}
				$dealer1->save();
				if(!empty($thisData['data'])){
					UserPermission::where('user_id',$userId)->delete();
					UserPermissionAction::where('user_id',$userId)->delete();
					foreach($thisData['data'] as $data){ 
						$obj 					= 	array(); 
						$obj['user_id']			=  !empty($userId)?$userId:0;  
						$obj['admin_module_id']	=  !empty($data['department_id'])?$data['department_id']:0; 
						$obj['is_active']		=  !empty($data['value'])?$data['value']:0; 
						$userpermissiondata 	=   UserPermission::create($obj);
						$userpermissionID		=	$userpermissiondata->id;
					
						if(isset($data['module']) && !empty($data['module'])){
							foreach($data['module'] as $subModule){ 
								$objData 							= array(); 
								$objData['user_id']					=  !empty($userId)?$userId:0;  
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
				DB::commit();
				Session::flash('flash_notice', trans("Dealer has been updated successfully")); 
				return Redirect::to('adminpnlx/dealer-management');
			}
		}
	}
	// end function dealerUpdate()


	/**
	* Function for export Dealers To Excel    
	*
	* @param null
	*
	* @return view page. 
	*/
	
	public function exportDealersToExcel(){
		$searchData					=	Session::get('dealers_search_data');
		$DB							=	Users::query();
		$searchVariable			=	array(); 
		if($searchData){
			$searchData				=	Input::get();
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
				if(!empty($fieldValue) && $fieldName != 'state_name' && $fieldName != 'phone_number'){
					$DB->where("users.$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
				if(!empty($fieldValue) && $fieldName == 'phone_number'){
					$DB->where(function ($query) use($fieldValue) {
							$query->where('users.phone_number', 'like', '%' . $fieldValue . '%')
							->orWhere('users.telephone', 'like', '%' . $fieldValue . '%');
					  });
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
				if($fieldName == 'state_name' && !empty($fieldValue)){
					$DB->where("s.id",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy 					= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'users.id';
		$order  					= 	(Input::get('order')) ? Input::get('order')   : 'users.DESC';
		$result 					= 	$DB->where("users.user_role_id",DEALER_ROLE_ID)
										->where('users.is_deleted',0)
										->leftjoin("users as u","u.id",'=','users.sales_person_id')
										->leftjoin("states as s","s.id",'=','users.state_id')
										->select('users.*','u.full_name as sales_person_name','s.name as state_name')
										->orderBy($sortBy,$order)
										->get()->toArray();							
									
												
		$thead = array();
		$thead[]		= array("Code","Name","Address","PostCode","City","State","Contact Person","Email","Mobile No","Phone No","Credit Limit","Sales Person");
		if(!empty($result)) {
			foreach($result as $record) {
				$dealer_code					=	!empty($record['dealer_code'])?$record['dealer_code']:'';
				$full_name						=	!empty($record['full_name'])?$record['full_name']:'';
				$address						=	!empty($record['address'])?$record['address']:'';
				$pincode						=	!empty($record['pincode'])?$record['pincode']:'';
				$city							=	!empty($record['city'])?$record['city']:'';
				$state_name						=	!empty($record['state_name'])?$record['state_name']:'';
				$contact_person					=	!empty($record['contact_person'])?$record['contact_person']:'';
				$email							=	!empty($record['email'])?$record['email']:'';
				$phone_number					=	!empty($record['phone_number'])?$record['phone_number']:'';
				$telephone						=	!empty($record['telephone'])?$record['telephone']:'';
				$credit_limit					=	!empty($record['credit_limit'])?$record['credit_limit']:'';
				$sales_person_name				=	!empty($record['sales_person_name'])?$record['sales_person_name']:'';
				$thead[]		= array($dealer_code,$full_name,$address,$pincode,$city,$state_name,$contact_person,$email,$phone_number,$telephone,$credit_limit,$sales_person_name);
			}
		}								
		//echo '<pre>'; print_r($thead); die;					
		return  View::make('admin.dealerManagement.export_excel', compact('thead'));
		
	}
	//end exportDealersToExcel()

	/**
	* Function for delete  dealer  
	*
	* @param user_id
	*
	* @return view page. 
	*/
	public function deleteDealer($userId = 0){
		$userDetails	=	Users::find($userId); 
		if(empty($userDetails)) {
			return Redirect::back();
		}
		if($userId){	
			$email 						=	'delete_'.$userId .'_'.$userDetails->email;
			$userModel					=	Users::where('id',$userId)->update(array('is_deleted'=>1,'email'=>$email,'deleted_at'=>date("Y-m-d H:i:s")));
			Session::flash('flash_notice',trans("Dealer deleted successfully")); 
		}
		return Redirect::back();
	} 
	// end deleteDealer()


	/**
	* Function for update DealerStatus    
	*
	* @param user_id,status
	*
	* @return view page. 
	*/
	public function updateDealerStatus($id = 0, $Status = 0){
		if($Status == 0){
			$statusMessage	=	trans("Dealer deactivated successfully");
			$staffDetails		=	Users::find($id); 
		}else{
			$statusMessage	=	trans("Dealer activated successfully");
		}
		$this->_update_all_status("users",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} 
	// end updateDealerStatus()

	// function uses for get the city list on state change
    public function GetCitiesList(){
		$state_id   =   Input::get('state_id');
		$city_id   =   Input::get('city_id');
        $response   =   array();
        if($state_id != ''){
            $cityList   =   DB::table('cities')->where('state_id', $state_id)->pluck('name', 'id')->toArray();
            if($cityList){
                $dropdown   =   '<option value="">'.trans("Select Your City").'</option>';
                if(isset($cityList ) && !empty($cityList)){
                    foreach($cityList  as $key=>$value){
                        $dropdown .= '<option value="'.$key.'">'.$value.'</option>';
                    }
                }
                $response['success'] =   1;
				$response['data']    =   $dropdown;
				$response['city_id']    =   $city_id;
            }else{
                $response['success'] =   0;
				$response['data']    =   '';
				$response['city_id']    =   '';
            }
        }else{
            $response['success'] =   0;
			$response['data']    =   '';
			$response['city_id']    =   '';
        }
        return Response::json($response);
    }
	
	
	  
}//end DealerManagementController

