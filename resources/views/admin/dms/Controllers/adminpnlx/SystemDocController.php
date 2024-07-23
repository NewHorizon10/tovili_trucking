<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\SystemDoc;
use Illuminate\Support\Facades\Storage;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* SystemDocController Controller
*
* Add your methods in the class below
*
* This file will render views from views/admin/systemdoc
*/
class SystemDocController extends BaseController {
		
	public $model	=	'SystemDoc';
	
	public function __construct() {
		View::share('modelName',$this->model);
	}
/**
* Function for display all Document 
*
* @param null
*
* @return view page. 
*/
	public function listDoc(){	
		$DB							=	SystemDoc::query();
		$DB1						=	SystemDoc::query();
		$searchVariable				=	array(); 
		$inputGet					=	Input::get();
		if ((Input::get())) {
			///print_r($inputGet);die;
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
			
			if(isset($searchData['per_page'])){
				unset($searchData['per_page']);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if(!empty($fieldValue)){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy 					= (Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
	    $order  					= (Input::get('order')) ? Input::get('order')   : 'DESC';
		$PerPageRecord				=	(Input::get('per_page')) ? Input::get('per_page'): Config::get("Reading.records_per_page");
		$result 					= $DB->orderBy($sortBy, $order)->paginate($PerPageRecord);
		$count						= $DB1->count();
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		
		return  View::make('admin.'.$this->model.'.index',compact('result','searchVariable','sortBy','order','query_string','count'));
	}// end listBlock()
/**
* Function for display page  for add new Document
*
* @param null
*
* @return view page. 
*/
	public function addDoc(){
		return  View::make('admin.'.$this->model.'.add');
	} //end addBlock()
/**
* Function for save document
*
* @param null
*
* @return redirect page. 
*/
	function saveDoc(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData				=	Input::all();
		$validator = Validator::make(
			$thisData,
			array(
				'title' 		=> 'required',
				'file' 			=> 'required|mimes:'.IMAGE_EXTENSION,
			),
			array(
				'file.required'=>'Image field is required',
				'file.mimes'=>'Image valid type is: '.IMAGE_EXTENSION
			)
		);
		if ($validator->fails()){	
			return Redirect::back()
			->withErrors($validator)->withInput();
		}else{
			if(Input::hasFile('file')){
				$extension 		=	Input::file('file')->getClientOriginalExtension();
				$file_name		=	time().'file.'.$extension; 
				/* Input::file('file')->move(SYSTEM_IMAGE_DIRECTROY_PATH, $file_name); */
				$filePath	=	SYSTEM_IMAGE_DIRECTROY_PATH.$file_name;
				$file = Input::file('file');
				Storage::disk('s3')->put($filePath, file_get_contents($file));
				Storage::disk('s3')->setVisibility($filePath, 'public');
			}else{
				$file_name		= 	'';
			} 
			$doc 				= 	new SystemDoc;
			$doc->title    		= 	Input::get('title');
			$doc->slug   		= 	$this->getSlug($doc->title,'title','SystemDoc');
			$doc->name    		= 	$file_name;
			$doc->save();
			Session::flash('flash_notice', trans("System Image added successfully")); 
			return Redirect::route($this->model.'.index');
		}
	}//end saveBlock()
/**
* Function for display page  for edit Document page
*
* @param $Id ad id of Document page
*
* @return view page. 
*/	
	public function editDoc($Id){
		$docs				=	SystemDoc::find($Id);
		if(empty($docs)) {
			return Redirect::route($this->model.'.index');
		}
		return  View::make('admin.'.$this->model.'.edit',array('doc' => $docs));
	}// end editBlock()
/**
* Function for update Document 
*
* @param $Id ad id of Document 
*
* @return redirect page. 
*/
	function updateDoc($Id){
		Input::replace($this->arrayStripTags(Input::all()));
		$this_data				=	Input::all();
		$doc 					= 	SystemDoc:: find($Id);
		$validator = Validator::make(
			$this_data,
			array(
				'title' 		=> 'required',
				'file' 			=> 'mimes:'.IMAGE_EXTENSION,
			),
			array(
				'file.mimes'=>'Image valid type is: '.IMAGE_EXTENSION
			)
		);
		if ($validator->fails()){	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{	
			if(Input::hasFile('file')){
				$extension 		=	Input::file('file')->getClientOriginalExtension();
				$file_name		=	time().'file.'.$extension; 
				/* Input::file('file')->move(SYSTEM_IMAGE_DIRECTROY_PATH, $file_name); */
				//@unlink(SYSTEM_IMAGE_DIRECTROY_PATH.$doc->name);
				Storage::disk('s3')->delete(SYSTEM_IMAGE_DIRECTROY_PATH. $doc->name);
				$filePath	=	SYSTEM_IMAGE_DIRECTROY_PATH.$file_name;
				$file = Input::file('file');
				Storage::disk('s3')->put($filePath, file_get_contents($file));
				Storage::disk('s3')->setVisibility($filePath, 'public');
				$doc->name    	= 	$file_name;
			}
			$doc->title    		= 	Input::get('title');
			$doc->save();
			Session::flash('flash_notice',  trans("System Image updated successfully")); 
			return Redirect::route($this->model.'.index');
		}
	}// end updateDoc()
/**
* Function for update Doc  status
*
* @param $Id as id of Document 
* @param $Status as status of Document 
*
* @return redirect page. 
*/	
	public function updateDocStatus($Id = 0, $Status = 0){
		if($Status == 0	){
			$statusMessage	=	trans("System Image deactivated successfully");
		}else{
			$statusMessage	=	trans("System Image activated successfully");
		}
		$this->_update_all_status('system_documents',$Id,$Status);
		/* $model					=	SystemDoc::find($Id);
		$model->is_active		=	$Status;
		$model->save(); */
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::route($this->model.'.index');
	}// end updateDocStatus()
/**
* Function for delete document 
*
* @param $Id as id of document 
*
* @return redirect page. 
*/	
	public function deleteDoc($Id = 0){
		if($Id){
			$doc				=	SystemDoc::find($Id) ;
			//delete from folder
			/* $doc->delete();	 */
			$this->_delete_table_entry('system_documents',$Id,'id');
			@unlink(SYSTEM_DOCUMENTS_UPLOAD_DIRECTROY_PATH.$doc->name);
		}
		Session::flash('flash_notice',trans("System Image removed successfully"));  
		return Redirect::route($this->model.'.index');
	}// end deleteDoc()
/**
* Function for delete multiple doc
*
* @param null
*
* @return view page. 
*/
	public function performMultipleAction(){
		if(Request::ajax()){
			$actionType 		=	((Input::get('type'))) ? Input::get('type') : '';
			if(!empty($actionType) && !empty(Input::get('ids'))){
				if($actionType	==	'delete'){
					SystemDoc::whereIn('id', Input::get('ids'))->delete();
					Session::flash('flash_notice',trans("messages.management.doc_all_delete_msg")); 
				}
			}
		}
	}//end performMultipleAction()
	
	public function deleteImage(){
		$id					=	Input::get('id');
		$imageData			=	SystemDoc::find($id);
		 
		if(!empty($imageData)){
			@unlink(SYSTEM_DOCUMENTS_UPLOAD_DIRECTROY_PATH.$imageData->name);
			SystemDoc::where('id',$id)->update(['name'=>null]);
			$response					=	array(
				'success' 				=> 	true,
			);
			return Response::json($response);
			die;
		}else{
			$response					=	array(
				'success' 				=> 	false,
			);
			return Response::json($response);
			die;
		}
	}
}// end BlockController	
