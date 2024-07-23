<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\DocumentManager;

use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* ItemCategoryController Controller
*
* Add your methods in the class below
*
* This file will render views\ItemCategoryController\dashboard
*/
	class DocumentManagerController extends BaseController {
		
		public $model	=	'DocumentManager';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function list($id = "",$slug=""){
		$DB 					= 	DocumentManager::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of name and description */ 
			if ((Input::get())) {
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
					$DB->where($fieldName,'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
            }
            $DB->where('is_deleted',0);
            $data = $DB->where('entry_id',$id)->where('section_name',$slug);
            
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';

            $result 				= 	$DB
                                        ->orderBy($sortBy, $order)
									    ->paginate(Config::get("Reading.records_per_page"));
			
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("_search_data",$inputGet);

			return  View::make('admin.'.$this->model.'.index', compact('result','id','slug','searchVariable','sortBy','order','query_string'));
		}
		
	
 
	/**
	* Function for add  page
	*
	* @param null
	*
	* @return view page. 
	*/


	public function add($id="",$slug=""){
		return View::make('admin.'.$this->model.'.add',compact('id','slug'));
	}
	
	/**
	* Function for save 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function save($id="",$slug=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'document_name' 			    =>	 'required',
					'document' 			            =>	 'required',
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
                $documentManager 					    = 	new DocumentManager;
                $documentManager->entry_id              =   $id;   
				$documentManager->user_id	        	=	Auth::user()->id;
				$documentManager->section_name	        =	$slug;
				$documentManager->document_name	        =	input::get("document_name");
                if(input::hasFile('document')){
					$extension 	=	 input::file('document')->getClientOriginalExtension();
					$fileName	=	time().'-document.'.$extension;
					
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath			=	DOCUMENT_MANAGER_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					$file               = 	Input::file('document');
					if($file->move($folderPath, $fileName)){
						$documentManager->document	=	$folderName.$fileName;
					}
				}
				$documentManager->save();
				Session::flash("success",trans("Document has been added successfully."));
				return Redirect::to("adminpnlx/document-manager/index/$id/$slug");
			}
		}
	}
	
	public function edit($id = "",$slug=""){
		$Details	    =	DB::table('document_manager')
                                ->where('document_manager.id',$id)
                                ->where('document_manager.user_id',Auth::user()->id)
                                ->where('document_manager.section_name',$slug)
								->first();
		if(empty($Details)) {
			return Redirect::back();
		}
		return View::make('admin.'.$this->model.'.edit', compact("Details","slug"));
	} // end editUser()
	
	public function update($id="",$slug=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'document_name' 			    =>	 'required',
					//'document' 			            =>	 'required',
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$documentManager 					    = 	DocumentManager::find($id);
                $documentManager->entry_id              =   $id;
				$documentManager->section_name	        =	$slug;
				$documentManager->document_name	        =	input::get("document_name");
                if(input::hasFile('document')){
					$extension 	=	 input::file('document')->getClientOriginalExtension();
					$fileName	=	time().'-document.'.$extension;
					
					$folderName     	= 	strtoupper(date('M'). date('Y'))."/";
					$folderPath			=	DOCUMENT_MANAGER_ROOT_PATH.$folderName;
					if(!File::exists($folderPath)) {
						File::makeDirectory($folderPath, $mode = 0777,true);
					}
					$file               = 	Input::file('document');
					if($file->move($folderPath, $fileName)){
						$documentManager->document	=	$folderName.$fileName;
					}
				}
				$documentManager->save();
				Session::flash('flash_notice', trans("Document manager has been updated successfully.")); 
                return Redirect::back();
			}
		}
	}

	
	public function delete($id = '',$slug = ""){
        $Details			=	DocumentManager::find($id);
        $Details->where('section_name',$slug); 
		if(empty($Details)) {
			return Redirect::back();
		}
		if($id){	
			$userModel					=	DocumentManager::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans("Document manager has been deleted successfully.")); 
		}
		return Redirect::back();
	}

	
} //end WarehouseController()
