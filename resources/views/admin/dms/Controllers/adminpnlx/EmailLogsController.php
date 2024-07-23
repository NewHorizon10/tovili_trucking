<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\EmailLog;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* Base Controller
*
* Add your methods in the class below
*
* This is the base controller called everytime on every request
*/
class  EmailLogsController extends BaseController {
/*
* Function for display email detail from database   
*
* @param null
*
* @return view page. 
*/	
	public function listEmail(){
		$DB				=	EmailLog::query();
		$searchVariable	=	array(); 
		if (Input::get()) {
			$searchData	=	Input::get();
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
				if(!empty($fieldValue)){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy = (Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
	    $order  = (Input::get('order')) ? Input::get('order')   : 'DESC';
		$result	= $DB->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
		
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		return View::make('admin.emaillogs.index',compact('result','searchVariable','sortBy','order','query_string'));
	}//end listEmail()
/*
* Function for dispaly email details on popup   
*
* @param $id as mail id 
*
* @return view page. 
*/
	public function EmailDetail($id){
		if(Request::ajax()){   
			$result	= EmailLog::where('id',$id)->get();
			return View::make('admin.emaillogs.popup',compact('result'));
		}  
	}// end EmailDetail()
}// end EmailLogsController