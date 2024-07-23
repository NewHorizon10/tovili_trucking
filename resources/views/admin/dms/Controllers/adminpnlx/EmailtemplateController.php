<?php
namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\EmailTemplate;
use App\Model\EmailAction;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;

/**
* Emailtemplate Controller
*
* Add your methods in the class below
*
* This file will render views from views/emailtemplates
*/
 
	class EmailtemplateController extends BaseController {
/**
* Function for display list of all email templates
*
* @param null
*
* @return view page. 
*/
	public function listTemplate(){
		$DB				=	EmailTemplate::query();
		$searchVariable	=	array(); 
		$searchData		=	Input::get();
		if (Input::get()){
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
		
		$sortBy = (Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
	    $order  = (Input::get('order')) ? Input::get('order')   : 'DESC';
		
		$result	 	= 	$DB->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
		
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		//echo "<pre>";print_r(Input::get());die;
		return  View::make('admin.emailtemplates.index', compact('result','searchVariable','sortBy','order','query_string'));
	}// end listTemplate()
/**
* Function for display page for add email template
*
* @param null
*
* @return view page. 
*/
	public function addTemplate(){
		$Action_options	=	EmailAction::pluck('action','action');
		return  View::make('admin.emailtemplates.add',compact('Action_options'));
	}// end addTemplate()
/**
* Function for display save email template
*
* @param null
*
* @return redirect page. 
*/
	public function saveTemplate(){
		Input::replace($this->arrayStripTags(Input::all()));
		$validator = Validator::make(
			Input::all(),
			array(
				'name' 			=> 'required',
				'subject' 		=> 'required',
				'action' 		=> 'required',
				'constants' 	=> 'required',
				'body' 			=> 'required'
			)
		);
		if ($validator->fails())
		{	
			return Redirect::to('adminpnlx/email-manager/add-template')
				->withErrors($validator)->withInput();
		}else{			
			EmailTemplate::insert(
				array(
					'name'		 	=> Input::get('name'),
					'subject' 		=> Input::get('subject'),
					'action' 		=> Input::get('action'),
					'body'			=> Input::get('body'),
					'created_at' 	=> DB::raw('NOW()'),
					'updated_at' 	=> DB::raw('NOW()')
				)
			);
			
			Session::flash('flash_notice', trans("Email template added successfully")); 
			return Redirect::intended('adminpnlx/email-manager');
		}
	}//  end saveTemplate()
/**
* Function for display page for edit email template page
*
* @param $Id as id of email template
*
* @return view page. 
*/
	public function editTemplate($Id){
		$Action_options	=	EmailAction::pluck('action','action')->toArray();
		$emailTemplate	=	EmailTemplate::find($Id);
		if(empty($emailTemplate)) {
			return Redirect::to('adminpnlx/email-manager');
		}
		return  View::make('admin.emailtemplates.edit',compact('Action_options','emailTemplate'));
	} // end editTemplate()
/**
* Function for update email template
*
* @param $Id as id of email template
*
* @return redirect page. 
*/
	public function updateTemplate($Id){
		Input::replace($this->arrayStripTags(Input::all()));
		$validator = Validator::make(
			Input::all(),
			array(
				'name' 			=> 'required',
				'subject' 		=> 'required',
				'body' 			=> 'required'
			)
		);
		if ($validator->fails())
		{	
			return Redirect::to('adminpnlx/email-manager/edit-template/'.$Id)
				->withErrors($validator)->withInput();
		}else{
			EmailTemplate::where('id', $Id)
				->update(
					array(
						'name'		 	=> Input::get('name'),
						'subject' 		=> Input::get('subject'),
						'body'			=> Input::get('body'),
						'updated_at' 	=> DB::raw('NOW()')
					)
				);
			Session::flash('flash_notice', trans("Email template updated successfully")); 
			return Redirect::intended('adminpnlx/email-manager');
		}
	} // end updateTemplate()
	/**
	* Function for get all  defined constant  for email template
	*
	* @param null
	*
	* @return all  constant defined for template. 
	*/
	public function getConstant(){
		if(Request::ajax() && Input::get()){
			$constantName 	= 	Input::get('constant');
			$options		= 	EmailAction::where('action', '=', $constantName)->pluck('options','action'); 
			$a 				= 	explode(',',$options[$constantName]);
			echo json_encode($a);
		}
		exit;
	}



	
}