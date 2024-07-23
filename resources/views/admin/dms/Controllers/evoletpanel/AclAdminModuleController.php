<?php 
namespace App\Http\Controllers\admin;

use App\Http\Controllers\BaseController;
use App\Model\Acl; 
use App\Model\AdminModuleCategory; 
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator,Route,App;
 
 
class AclAdminModuleController extends BaseController {
 
	public function index(){  
		
		/*get all controller name */
		$controllers = []; 
		$counter	=	0;
		foreach (Route::getRoutes()->getRoutes() as $route)
		{
			$action = $route->getAction();
			if (array_key_exists('controller', $action))
			{
				$array = explode('App\Http\Controllers\admin', $action['controller']);
				$value = isset($array[1])?$array[1]:0; 
				
				if(!empty($value)){
					$array = explode('@', $value); 
					$value = isset($array[0])?trim($array[0],'"\"'):0; 
					$value1 = isset($array[1])?$array[1]:0;  					
				}
				
				if(!empty($value)){
					$is_saved		=	AdminModuleCategory::where('controller_name',$value)->where('function_name',$value1)->select('id','type')->first();	 
					$controllers[$value][$counter]['name']	= $value1;  
					$controllers[$value][$counter]['id']	= isset($is_saved->id)?$is_saved->id:'';  
					$controllers[$value][$counter]['type']	= isset($is_saved->type)?$is_saved->type:'';    
				}
				$counter++;
			}
		} 
		return View::make("admin.AdminModuleCategory.index",compact('controllers'));
	}
  
	public function saveAdminModuleCategory(){
		$data	=	Input::all();
		if(!empty($data)){
			$saveData	=	array();
			$saveData['id']					=	$data['id'];
			$saveData['controller_name']	=	$data['controller_name'];
			$saveData['function_name']		=	$data['function_name'];
			$saveData['type']				=	$data['type'];
			$saveData['action_type']		=	$data['action_type'];
			 
			if($saveData['action_type'] == "true"){
				if(isset($saveData['id']) && (!empty($saveData['id']))){ 
					$obj		=	AdminModuleCategory::findorFail($saveData['id']);
					$obj->fill($data)->save();
				}else{
					AdminModuleCategory::create($saveData);
				}
			}else{
				AdminModuleCategory::where('controller_name',$saveData['controller_name'])->where('function_name',$saveData['function_name'])->delete();
			}		
			return "1";die;
		}
	}
} 
