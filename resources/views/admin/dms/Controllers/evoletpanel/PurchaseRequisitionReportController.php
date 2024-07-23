<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Users;
use App\Model\DropDown;
use App\Model\PurchaseRequisition;
use App\Model\Acl;
use App\Model\UserPermission;
use App\Model\UserPermissionAction;
use App\Model\AclAdminAction;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* PurchaseRequisitionReport Controller
*
* Add your methods in the class below
*
* This file will render views\admin\dashboard
*/
	class PurchaseRequisitionReportController extends BaseController {
	/**
	* Function for display adminpnlx dashboard
	*
	* @param null
	*
	* @return view page. 
    */
    public function purchaseRequisitionReportList(){
		echo "hello"; die;
		$DB			=	PurchaseRequisition::query();
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
				
				if(!empty($fieldName) && !empty($fieldValue)){
					$DB->where($fieldName,'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy 					= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'id';
		$order  					= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->designation != PURCHASE_HOD ){
			$DB->where("created_by",Auth::user()->id);
		}
		$result 					= 	$DB
										->select('purchase_requisitions.*',
										DB::Raw("(SELECT unique_id FROM items WHERE items.id=purchase_requisitions.item_code) as item_code"),
										DB::Raw("(SELECT full_name FROM users WHERE users.id=purchase_requisitions.created_by) as created_by_name"),
										DB::Raw("(SELECT title FROM departments WHERE departments.id=purchase_requisitions.department_name) as department_name"))
										->orderBy($sortBy,$order)
										->paginate(Config::get("Reading.records_per_page"));
										
		$complete_string			=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		
		// echo '<pre>'; print_r($result); die;
		return View::make('admin.PurchaseRequisitionReport.index',compact('result','searchVariable','sortBy','order','query_string'));
	    }
    }
    ?>