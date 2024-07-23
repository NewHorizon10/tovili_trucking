<?php
namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\BaseController;
use App\Model\Cms;
use App\Model\CmsDescription;
use App\Model\Language;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Redirect,Request,Response,Session,URL,View,Validator;

/**
* Cms Controller
* Add your methods in the class below
* This file will render views from views/Cms
*/
	class CmsPagesController extends BaseController {
/**
* Function for display all cms page
*
* @param null
*
* @return view page. 
*/
	public function listCms(){	
		$DB							=	Cms::query();
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
				if(!empty($fieldValue)){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy 					= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
	    $order  					= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 					= 	$DB->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
		$complete_string			=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		
		return  View::make('admin.Cms.index',compact('result','searchVariable','sortBy','order','query_string'));
	}
/**
* Function for display page  for add new cms page 
*
* @param null
*
* @return view page. 
*/
	public function addCms(){
		$languages					=	DB::select("CALL GetAcitveLanguages(1)");
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		return  View::make('admin.Cms.add',compact('languages' ,'language_code'));
	}
/**
* Function for save added cms page
*
* @param null
*
* @return redirect page. 
*/
	function saveCms(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$thisData['data'][$language_code];
		$validator = Validator::make(
			array(
				'name' 				=> Input::get('name'),
				'title' 			=> $dafaultLanguageArray['title'],
				'body' 				=> $dafaultLanguageArray['body'],
				'meta_title' 		=> $dafaultLanguageArray['meta_title'],
				'meta_description'  => $dafaultLanguageArray['meta_description'],
				'meta_keywords' 	=> $dafaultLanguageArray['meta_keywords']
			),
			array(
				'name' 				=> 'required',
				'title' 			=> 'required',
				'body' 				=> 'required',
				'meta_title' 		=> 'required',
				'meta_description' 	=> 'required',
				'meta_keywords' 	=> 'required'
			),
			array(
				'name.required' 			=> 'The page name field is required.',
				'title.required' 			=> 'The page title field is required.',
				'body.required' 			=> 'The description field is required.',
				'meta_keywords.required' 	=> 'The meta keyword field is required.'
			)
		);
		
		if ($validator->fails()) {	
			return Redirect::to('adminpnlx/cms-manager/add-cms')
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$cms 					= new Cms;
			$cms->name    			= Input::get('name');
			$cms->title   			= $dafaultLanguageArray['title'];
			$cms->body   			= $dafaultLanguageArray['body'];
			$cms->meta_title   		= $dafaultLanguageArray['meta_title'];
			$cms->meta_description  = $dafaultLanguageArray['meta_description'];
			$cms->meta_keywords   	= $dafaultLanguageArray['meta_keywords'];
			$cms->slug   			= $this->getSlug($dafaultLanguageArray['title'],'title','Cms');
			$cmspags				=	$cms->save();
			if(!$cmspags) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::back()->withInput();
			}
			$cms_page_id			=	$cms->id;
			foreach ($thisData['data'] as $language_id => $cms) {
				if (is_array($cms))
					foreach ($cms as $key => $value) {
						$CmsDescription		=	CmsDescription::insert(
												array(
													'language_id'				=>	$language_id,
													'foreign_key'				=>	$cms_page_id,
													'source_col_name'			=>	$key,
													'source_col_description'	=>	$value,
													'created_at'				=>	date("Y-m-d H:i:s"),
													'updated_at'				=>  date("Y-m-d H:i:s"),
												)
											);
						if(!$CmsDescription) {
							DB::rollback();
							Session::flash('error', trans("Something went wrong.")); 
							return Redirect::back()->withInput();
						}
					}
			}
			DB::commit();
			Session::flash('flash_notice', trans("Cms page added successfully")); 
			return Redirect::to('adminpnlx/cms-manager');
		}
	}
/**
* Function for display page  for edit cms page
*
* @param $Id ad id of cms page
*
* @return view page. 
*/	
	public function editCms($Id){
		$Cms				=	Cms::find($Id);
		if(empty($Cms)) {
			return Redirect::to('adminpnlx/cms-manager');
		}
		$CmsDescription		=	DB::select("CALL GetCmsDescription($Id)");
		//echo "<pre>";print_r($CmsDescription);die;
		$multiLanguage		=	array();
		if(!empty($CmsDescription)){
			foreach($CmsDescription as $description) {
				$multiLanguage[$description->language_id][$description -> source_col_name]	=	$description->source_col_description;						
			}
		}
		//echo "<pre>";print_r($multiLanguage);die;
		$languages			=	DB::select("CALL GetAcitveLanguages(1)");
		$default_language	=	Config::get('default_language');
		$language_code 		=   $default_language['language_code'];
		return  View::make('admin.Cms.edit',array('languages' => $languages,'language_code' => $language_code,'adminCmspage' => $Cms,'multiLanguage' => $multiLanguage));
	}
/**
* Function for update cms page
*
* @param $Id ad id of cms page
*
* @return redirect page. 
*/
	function updateCms($Id){
		Input::replace($this->arrayStripTags(Input::all()));
		$this_data				=	Input::all();
		$default_language		=	Config::get('default_language');
		$language_code 			=   $default_language['language_code'];
		$dafaultLanguageArray	=	$this_data['data'][$language_code];
		
		$validator = Validator::make(
			array(
				'name' 				=> Input::get('name'),
				'title' 			=> $dafaultLanguageArray['title'],
				'body' 				=> $dafaultLanguageArray['body'],
				'meta_title' 		=> $dafaultLanguageArray['meta_title'],
				'meta_description'  => $dafaultLanguageArray['meta_description'],
				'meta_keywords' 	=> $dafaultLanguageArray['meta_keywords']
			),
			array(
				'name' 				=> 'required',
				'title' 			=> 'required',
				'body' 				=> 'required',
				'meta_title' 		=> 'required',
				'meta_description' 	=> 'required',
				'meta_keywords' 	=> 'required'
			),
			array(
				'name.required' 			=> 'The page name field is required.',
				'title.required' 			=> 'The page title field is required.',
				'body.required' 			=> 'The description field is required.',
				'meta_keywords.required' 	=> 'The meta keyword field is required.'
			)
		);
		
		if($validator->fails()){	
			return Redirect::to('adminpnlx/cms-manager/edit-cms/'.$Id)
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$Cms_response		=	Cms::where('id', $Id)->update(array(
				'name'   	 		=>  Input::get('name'),
				'title' 			=>  $dafaultLanguageArray['title'],
				'body' 				=>  $dafaultLanguageArray['body'],
				'meta_title' 		=>  $dafaultLanguageArray['meta_title'],
				'meta_description' 	=>  $dafaultLanguageArray['meta_description'],
				'meta_keywords' 	=>  $dafaultLanguageArray['meta_keywords'],
				'updated_at' 		=> DB::raw('NOW()')
			));
			
			if(!$Cms_response) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::back()->withInput();
			}
			
			$cms_page_id		=	$Id;
			$Cms_response		=	DB::statement("CALL DeleteCmsDescription($Id)");
			if(!$Cms_response) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::back()->withInput();
			}
			
			foreach ($this_data['data'] as $language_id => $cms) {
				if (is_array($cms))
					foreach ($cms as $key => $value) {
						$CmsDescription		=	 CmsDescription::insert(
							array(
								'language_id'				=>	$language_id,
								'foreign_key'				=>	$cms_page_id,
								'source_col_name'			=>	$key,
								'source_col_description'	=>	$value,
								'created_at'				=>	date("Y-m-d H:i:s"),
								'updated_at'				=>  date("Y-m-d H:i:s"),
							)
						);
						if(!$CmsDescription) {
							DB::rollback();
							Session::flash('error', trans("Something went wrong.")); 
							return Redirect::back()->withInput();
						}
					}
			}
			DB::commit();
			Session::flash('flash_notice', trans("Cms page updated successfully")); 
			return Redirect::to('adminpnlx/cms-manager');
		}
	}
/**
* Function for update cms page status
*
* @param $Id as id of cms page
* @param $Status as status of cms page
*
* @return redirect page. 
*/	
	public function updateCmsStatus($Id = 0, $Status = 0){
		if($Status == 0	){
			$statusMessage	=	trans("Cms page deactivated successfully");
		}else{
			$statusMessage	=	trans("Cms page activated successfully");
		}
		$this->_update_all_status('cms_pages',$Id,$Status);
		Session::flash('flash_notice',  $statusMessage); 
		return Redirect::to('adminpnlx/cms-manager');
	}
}
