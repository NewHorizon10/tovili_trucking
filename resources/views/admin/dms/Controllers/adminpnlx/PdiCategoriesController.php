<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\PdiCategories;

use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;

/**
* DealerLocationController Controller
*
* Add your methods in the class below
*
* This file will render views\DealerLocationController\dashboard
*/
class PdiCategoriesController extends BaseController {
		

		
	public function listcatagory(){	
		$DB				=	PdiCategories::query();
		$searchVariable	=	array(); 
		$inputGet		=	Input::get();
		if ($inputGet) {
			$searchData	=	Input::get();
			unset($searchData['display']);
			unset($searchData['_token']);
            unset($searchData['sortBy']);
				unset($searchData['order']);
			if(isset($searchData['page'])){
				unset($searchData['page']);
				unset($searchData['sortBy']);
				unset($searchData['order']);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if(!empty($fieldValue)){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy = (Input::get('sortBy')) ? Input::get('sortBy') : 'id';
	    $order  = (Input::get('order')) ? Input::get('order')   : 'ASC';
		$result = $DB->where('parent_id',0)->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
		
		return  View::make('admin.pdi_catagories.index',compact('result','searchVariable','sortBy','order'));
	} // end listSetting()
	

	/**
	* Function for add Location page
	*
	* @param null
	*
	* @return view page. 
	*/

	public function addCatagory(){ 
		 
		return View::make('admin.pdi_catagories.add');
	}
	
	/**
	* Function for save Location
	*
	* @param null
	*
	* @return view page. 
	*/
	public function saveCatagory(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 		=>	 'required',
					
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$model 						= 	new PdiCategories;
				
				$model->name			=	Input::get('name');
				$model->description			=  	Input::get('description');
				$model->created_at			=  	date("Y-m-d H:i:s");
				$model->save();
				Session::flash("success",trans("PDI category  added successfully."));
				return Redirect::to('/adminpnlx/pdi-category');
				//return Redirect::back();
			}
		}
	}
	
	public function editCatagory($id = ""){
		$categoryDetails	=	DB::table('pdi_categories')
								->where('id',$id)
								->first();
		if(empty($categoryDetails)) {
			return Redirect::back();
		}	
		return View::make('admin.pdi_catagories.edit', compact("categoryDetails"));
	
		
	} // end editUser()
	
	public function updateCatagory($id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 		=>	 'required',
					
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$model						= 	PdiCategories::find($id);
				$model->name			=	Input::get('name');
				$model->description			=  	Input::get('description');
				$model->created_at			=  	date("Y-m-d H:i:s");
				$model->save();
				Session::flash('flash_notice', trans("PDI category has been updated successfully.")); 
				return Redirect::to('/adminpnlx/pdi-category');
			}
		}
	}


	
	

	public function updateCatagoryStatus($id = 0, $Status = 0){
		if($Status == 0){
			$statusMessage	=	trans("PDI category deactivated successfully.");
			$locationDetails		=	PdiCategories::find($id); 
		}else{
			$statusMessage	=	trans("PDI category activated successfully.");
		}
		$this->_update_all_status("pdi_categories",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} // end updateDealerNetworkstatus()
	
	
	public function listsubcatagory($id){	
		$DB				=	PdiCategories::query();
		$searchVariable	=	array(); 
		$inputGet		=	Input::get();
		$parentid        = $id;
		
		$categoryDetails	=	DB::table('pdi_categories')
								->where('id',$id)
								->first();
		if(empty($categoryDetails)) {
			return Redirect::route('category.index');
		}
		
		$DB->where('parent_id',$parentid);
		if ($inputGet) {
			$searchData	=	Input::get();
			unset($searchData['display']);
			unset($searchData['_token']);
            unset($searchData['sortBy']);
				unset($searchData['order']);
			if(isset($searchData['page'])){
				unset($searchData['page']);
				unset($searchData['sortBy']);
				unset($searchData['order']);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if(!empty($fieldValue)){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy = (Input::get('sortBy')) ? Input::get('sortBy') : 'id';
	    $order  = (Input::get('order')) ? Input::get('order')   : 'DESC';
		$result = $DB->where('parent_id',$parentid)->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
		
		return  View::make('admin.pdi_subcatgories.index',compact('categoryDetails','parentcatagory','parentid','result','searchVariable','sortBy','order'));
		
	
	} // end listSetting()

	public function addsubCatagory($id){ 
		$categoryDetails	=	DB::table('pdi_categories')
								->where('id',$id)
								->first();
		if(empty($categoryDetails)) {
			return Redirect::route('category.index');
		}
		 $parentid=$id;
		return View::make('admin.pdi_subcatgories.add',compact('parentid'));
	}
	
	/**
	* Function for save Location
	*
	* @param null
	*
	* @return view page. 
	*/
	public function savesubCatagory($parentid){
		
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 		=>	 'required',
					
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$model 						= 	new PdiCategories;
				
				$model->name			=	Input::get('name');
				$model->parent_id			=	$parentid;
				$model->description			=  	Input::get('description');
				$model->created_at			=  	date("Y-m-d H:i:s");
				$model->save();
				Session::flash("success",trans("PDI subcategory  added successfully."));
				return Redirect::to('/adminpnlx/pdi-category/sub-category/'.$parentid.'');
				//return Redirect::back();
			}
		}
	}
	public function editsubCatagory($id = ""){
		$categoryDetails	=	DB::table('pdi_categories')
								->where('id',$id)
								->first();
		if(empty($categoryDetails)) {
			return Redirect::back();
		}	
		return View::make('admin.pdi_subcatgories.edit', compact("id","categoryDetails"));
	
		
	} // end editUser()
	
	public function updatesubCatagory($id="",$parentid){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 		=>	 'required',
					
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$model						= 	PdiCategories::find($id);
				$model->name			=	Input::get('name');
				$model->description			=  	Input::get('description');
				$model->created_at			=  	date("Y-m-d H:i:s");
				$model->save();
				Session::flash('flash_notice', trans("PDI subcategory has been updated successfully.")); 
				return Redirect::to('/adminpnlx/pdi-category/sub-category/'.$parentid.'');
			}
		}
	}


	
	

	public function updatesubCatagoryStatus($id = 0, $Status = 0){
		if($Status == 0){
			$statusMessage	=	trans("PDI subcategory deactivated successfully.");
			$locationDetails		=	PdiCategories::find($id); 
		}else{
			$statusMessage	=	trans("PDI subcategory activated successfully.");
		}
		$this->_update_all_status("pdi_categories",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} // end updateDealerNetworkstatus()
	
} //end dealerNetworkController()
