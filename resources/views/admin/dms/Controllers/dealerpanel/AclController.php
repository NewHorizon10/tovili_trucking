<?php 
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\Acl; 
use App\Model\AclAdminAction; 
use App\Model\User; 
use App\Model\UserPermission; 
use App\Model\UserPermissionAction; 
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator,Artisan;
class AclController extends BaseController {
 
	public function index(){  
		$DB 					= 	Acl::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
		if(Input::get()) {
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
				if(!empty($fieldValue) && ($fieldName == "parent_id")){
					$DB->where("admin_modules.$fieldName",$fieldValue); 
				}else if(!empty($fieldValue) && ($fieldName != "parent_id")){
					$DB->where("admin_modules.$fieldName",'LIKE','%'.$fieldValue.'%'); 
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'admin_modules.updated_at';
	    $order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		
		$result 				= 	$DB->leftjoin('admin_modules as parent_admin','parent_admin.id','=','admin_modules.parent_id') 
									->select('admin_modules.*','parent_admin.title as parent_title')
									->where('admin_modules.type',0)
									->orderBy($sortBy,$order)
									->paginate(Config::get("Reading.records_per_page"));
									
		$parent_list 			= 	DB::table('admin_modules')->pluck('title','id')->toArray();
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		return View::make('dealerpanel.Acl.index', compact('result' ,'searchVariable','sortBy','order','query_string','parent_list'));
	} 
 
	public function add(){
		$parent_list = DB::table('admin_modules')->where('type',0)->pluck('title','id')->toArray();
		return View::make('dealerpanel.Acl.add',compact('parent_list'));
	} 
	 
	public function save(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'title'				=> 'required',
					'path'				=> 'required',
					'module_order'		=> 'required',  
				) 
			); 
			if ($validator->fails()){
				 return Redirect::back()->withErrors($validator)->withInput();
			}else{  
				$obj 					=  array(); 
				$obj['parent_id']		=  Input::get('parent_id'); 
				$obj['title']			=  Input::get('title'); 
				$obj['path']			=  Input::get('path'); 
				$obj['module_order']	=  Input::get('module_order'); 
				$obj['icon']			=  Input::get('icon');  
				Acl::create($obj); 
				
				$admin_modules	=	$this->buildTree(0);
				Session::put('admin_modules',$admin_modules);
				Session::flash('success',trans("Module added successfully"));
				return Redirect::route('dealer.Acl.index');
			}
		}
	} 
	 
	public function edit($Id = 0){
		$result	=	Acl::find($Id); 
		if(empty($result)){
			return Redirect::back();
		}
		$result			=	Acl::with('get_admin_module_action')->where('id',$Id)->first();
		$parent_list	= 	DB::table('admin_modules')->where('parent_id','!=',$Id)->where('type',0)->pluck('title','id')->toArray();
		return View::make('dealerpanel.Acl.edit',compact('parent_list','Id','result'));
	}  
	 
	public function update($userId = 0){	
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all(); 
		$validator 					=	Validator::make(
			Input::all(),
			array(
				'title'				=> 'required',
				'path'				=> 'required',
				'module_order'		=> 'required',  
			) 
		); 
		if ($validator->fails()){	
			return Redirect::route('Acl.edit',$userId)
				->withErrors($validator)->withInput();
		}else{
			$data 					=  array(); 
			$data['parent_id']		=  Input::get('parent_id'); 
			$data['title']			=  Input::get('title'); 
			$data['path']			=  Input::get('path'); 
			$data['module_order']	=  Input::get('module_order'); 
			$data['icon']			=  Input::get('icon');
			
			$admin_modules	=	$this->buildTree(0);
			Session::put('admin_modules',$admin_modules);
			AclAdminAction::where('admin_module_id',$userId)->delete();
			if(isset($thisData['data']) && !empty($thisData['data'])){
				foreach($thisData['data'] as $record){
					if(!empty($record['name']) && !empty($record['type'])){
						$obj 					= array(); 
						$obj['admin_module_id']	=  $userId; 
						$obj['name']			=  $record['name']; 
						$obj['type']			=  $record['type'];  
						AclAdminAction::create($obj);
					}
				}
			}
			
			
			if(isset($userId)){ 
				$obj				=	Acl::findorFail($userId);
				$obj->fill($data)->save();
			}  
			return Redirect::route('dealer.Acl.index')->with('success',trans("Module updated successfully"));
		}
	} 	
	 
	public function status($userId = 0, $userStatus = 0){
		if($userStatus == 0){
			$statusMessage	=	trans("Module deactivated successfully");
			Acl::where('id',$userId)->update(array('is_active'=>1));
		}else{
			$statusMessage	=	trans("Module activated successfully");
			Acl::where('id',$userId)->update(array('is_active'=>0));
		}
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::route('dealer.Acl.index');
	}  
	
	public function deleteAcl($userId = 0){
		$userDetails	=	Acl::find($userId); 
		if(empty($userDetails)) {
			return Redirect::route('dealer.Acl.index');
		}		
		 Acl::where('parent_id',$userId)->delete();
		 Acl::where('id',$userId)->delete();
		 Session::flash('flash_notice',trans("Module removed successfully"));
		return Redirect::route('dealer.Acl.index');
	} 
	 
	public function addMoreRow(){
		$counter	=	Input::get('counter'); 
		return View::make('dealerpanel.Acl.add_more',compact('counter'));
	}
 
	public function userPermission(){
		$seachData	=	array();
		$userList	=	User::where('is_active',1)
						->whereIn('user_role_id',array(DEALER_ROLE_ID,SUPER_ADMIN_ROLE_ID))
						->where('is_deleted',0)
						->where('users.is_deleted',0)
						->pluck("full_name","id")->toArray(); 
		 
		$aclModules	=	array();
		if(!empty(Input::all())){
			$seachData	=	Input::all('');
			if(isset($seachData['id']) && !empty($seachData['id'])){ 
				$user_id		=	(!empty($seachData['id']))?$seachData['id']:'';
				$aclModules		=	Acl::where('is_active',1)->where('parent_id',0)->where('admin_modules.type',1)->select('title','id',DB::Raw("(select is_active from user_permissions where user_id = $user_id AND admin_module_id = admin_modules.id LIMIT 1) as active"))->get(); 
				if(!empty($aclModules)){
					foreach($aclModules as &$aclModule){
						$aclModule['sub_module']	=	Acl::where('is_active',1)->where('admin_modules.type',1)->where('parent_id',$aclModule->id)->select('title','id')->get();
						$module_ids			=	array();
						if(!empty($aclModule['sub_module'])){
							foreach($aclModule['sub_module'] as &$module){
								$module_id		=		$module->id;
								$module_ids[$module->id]		=		$module->id;
								$module['module']	=	AclAdminAction::where('admin_module_id',$module->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $user_id AND admin_sub_module_id = $module_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get();
								 
								 
							}
						}
						 
						$newArray	=	array(); 
						//$module_id				=	$module->id;
						$aclModule['extModule']	=	Acl::where('is_active',1)->whereIn('parent_id',$module_ids)->select('title','id')->get();
				 
						if(!empty($aclModule['extModule'])){ 
							foreach($aclModule['extModule'] as &$record){
								$action_id			=	$record->id;
								$record['module']	=	AclAdminAction::where('admin_module_id',$record->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $user_id AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get(); 
							}
						}
						
						if(($aclModule['sub_module']->isEmpty()) && ($aclModule['extModule']->isEmpty())){
							$action_id			=	$aclModule->id;
							$aclModule['parent_module_action']	=	AclAdminAction::where('admin_module_id',$aclModule->id)->select('name','type','id',DB::Raw("(select is_active from user_permission_actions where user_id = $user_id AND admin_sub_module_id = $action_id AND admin_module_action_id = admin_module_actions.id LIMIT 1) as active"))->orderBy('type','ASC')->get();  
						} 
					}
				}  
			}else{
				return Redirect::Back();
			}  
		}
		return View::make('dealerpanel.Acl.user_permission',compact('userList','aclModules','seachData'));
	} 
	
	public function saveUserPermission(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData	=	Input::all();
		if(!empty($formData['data'])){
			UserPermission::where('user_id',$formData['user_id'])->delete();
			UserPermissionAction::where('user_id',$formData['user_id'])->delete();
			foreach($formData['data'] as $data){ 
				$obj 					= 	array(); 
				$obj['user_id']			=  !empty($formData['user_id'])?$formData['user_id']:0;  
				$obj['admin_module_id']	=  !empty($data['department_id'])?$data['department_id']:0; 
				$obj['is_active']		=  !empty($data['value'])?$data['value']:0; 
				$userpermissiondata 	=   UserPermission::create($obj);
				$userpermissionID		=	$userpermissiondata->id;
			
				if(isset($data['module']) && !empty($data['module'])){
					foreach($data['module'] as $subModule){ 
						$objData 							= array(); 
						$objData['user_id']					=  !empty($formData['user_id'])?$formData['user_id']:0;  
						$objData['user_permission_id']		=  $userpermissionID; 
						$objData['admin_module_id']			=  !empty($data['department_id'])?$data['department_id']:0; 
						$objData['admin_sub_module_id'] 	=  !empty($subModule['department_module_id'])?$subModule['department_module_id']:0; 
						$objData['admin_module_action_id']	=  !empty($subModule['id'])?$subModule['id']:0; 
						$objData['is_active']				=  !empty($subModule['value'])?$subModule['value']:0; 
						UserPermissionAction::create($objData);
					}
				}
			} 
			//User::where('id',$formData['user_id'])->update(['is_login'=>1]);
			Session::flash('success',trans("Permissions saved successfully"));
			return "1";die;
		}
	} 
	
	public function clearCache(){
		Artisan::call('cache:clear');
		echo "Cache is cleared";die;
	}
} 
