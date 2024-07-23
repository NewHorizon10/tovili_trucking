<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\DropDown;
use App\Model\Language;
use App\Model\DropDownDescription;
use App\Model\ModelServices;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* DropDownController Controller
*
* Add your methods in the class below
*
* This file will render views from views/dropdown
*/
	class DropDownController extends BaseController {
/**
* Function for display all DropDown    
*
* @param $type as category of dropdown 
*
* @return view page. 
*/
	public function listDropDown($type='') {
		/* $service[]	=	array(
			"service_days"	=>	90,
			"service_km"	=>	2000,
			"service_type"	=>	"0",
			"service_amount"	=>	0,
		);
		$service[]	=	array(
			"service_days"	=>	185,
			"service_km"	=>	4000,
			"service_type"	=>	"0",
			"service_amount"	=>	0,
		);
		$service[]	=	array(
			"service_days"	=>	230,
			"service_km"	=>	6000,
			"service_type"	=>	"1",
			"service_amount"	=>	100,
		);
		$service[]	=	array(
			"service_days"	=>	275,
			"service_km"	=>	8000,
			"service_type"	=>	"1",
			"service_amount"	=>	200,
		);
		$service[]	=	array(
			"service_days"	=>	320,
			"service_km"	=>	10000,
			"service_type"	=>	"0",
			"service_amount"	=>	200,
		);
		$service[]	=	array(
			"service_days"	=>	365,
			"service_km"	=>	12000,
			"service_type"	=>	"1",
			"service_amount"	=>	200,
		);
		
		
		
		$model_services	=	DropDown::where('dropdown_type',$type)->get();
		if(!empty($model_services)){
			foreach($model_services as $model_service){
				$counter	=	1;
				foreach ($service as $stepsResult) { 
					if($counter == 1){
						$service_days			=	$stepsResult['service_days']-5;
						$service_km				=	$stepsResult['service_km']-100;
						$service_days_string	=	$service_days."-".$stepsResult['service_days'];
						$service_km_string		=	$service_km."-".$stepsResult['service_km'];;
					}else{
						$service_days			=	$stepsResult['service_days']-10;
						$service_km				=	$stepsResult['service_km']-100;
						$service_days_string	=	$service_days."-".$stepsResult['service_days'];
						$service_km_string		=	$service_km."-".$stepsResult['service_km'];;
					}
				
					$modelService                =  new ModelServices;
					$modelService->dropdown_manager_id    =  $model_service->id;
					$modelService->service_no    =  $counter;
					$modelService->service_days  = 	$stepsResult['service_days'];
					$modelService->service_days_string  = 	$service_days_string;
					$modelService->service_amount = ($stepsResult['service_amount']) ?
												   $stepsResult['service_amount'] : 0;
					$modelService->service_km 	 = 	$stepsResult['service_km'];
					$modelService->service_km_string 	 = 	$service_km_string;
					$modelService->service_type  = 	$stepsResult['service_type'];
					$modelService->save();
					$counter++;
				}
			}
		}
		die("ho gya"); */
		
		
		
		
		
		if(empty($type)) {
			return Redirect::to('adminpnlx/dashboard');
		}
		$DB				=	DropDown::query()->where('dropdown_type',$type);
		$searchVariable	=	array(); 
		$inputGet		=	Input::get();
		//print_r($inputGet);die;
		if (Input::get()) {
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
				if(!empty($fieldValue) || $fieldValue==0){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		
		$sortBy = (Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
	    $order  = (Input::get('order')) ? Input::get('order')   : 'DESC';
		$result = $DB->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page")); 
		
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		
		return  View::make('admin.dropdown.index',compact('result','searchVariable','sortBy','order','type','query_string'));
	}// end listDropDown()
/**
* Function for display page  for add new DropDown  
*
* @param $type as category of dropdown 
*
* @return view page. 
*/
	public function addDropDown($type=''){
		$languages			=	DB::select("CALL GetAcitveLanguages(1)");
		$default_language	=	Config::get('default_language');
		$language_code 		=   $default_language['language_code'];
		
		return  View::make('admin.dropdown.add',compact('languages' ,'language_code','type'));
	} //end addDropDown()
 
/**
* Function for Add More Services DropDown page
*
* @param null
*
* @return redirect page. 
*/ 
  public function addMoreServices(){
		$counter 			=	input::get('counter');	
		$id 					=	input::get('id');	 
		return  View::make('admin.dropdown.add_more_services',compact('counter','id'));
	}

	

	/**
* Function for save added DropDown page
*
* @param null
*
* @return redirect page. 
*/ 
	function saveDropDown($type=''){
		
		Input::replace($this->arrayStripTags(Input::all()));
		$this_data										=	Input::all();
		//$dropdown 										= 	DropDown:: find($Id);
		$default_language								=	Config::get('default_language');
		$language_code 									=   $default_language['language_code'];
		$dafaultLanguageArray							=	$this_data['data'][$language_code];
		if($type == "vehiclemodel"){
			$validator = Validator::make(
				array(
					'name' 			=>  $dafaultLanguageArray['name'],
					'dropdown_type'	=>	$type,
					'number_of_km'=> $dafaultLanguageArray['number_of_km'],
					
				),	
				array(
					'name' 			=> 'required',
					"number_of_km" 	=> "required|numeric",
					
				)
			);
		}else{
			$validator = Validator::make(
				array(
					'name' 			=>  $dafaultLanguageArray['name'],
					'dropdown_type'	=>	$type,
					
				),	
				array(
					'name' 			=> 'required',
					
					
				)
			);
		}
		if ($validator->fails()){	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			$dropdown = new DropDown;
			$dropdown->slug    							= 	$this->getSlugWithoutModel($type ,'slug', 'dropdown_managers');
			$dropdown->name    							= 	$dafaultLanguageArray['name'];
			$dropdown->dropdown_type    				= 	$type;
			if(isset($dafaultLanguageArray['number_of_km'])){
				$dropdown->number_of_km    					= 	$dafaultLanguageArray['number_of_km'];
			}else{
				$dropdown->number_of_km    					= 	0;
			}
			$dropdown->save(); 
			$dropdownId									=	$dropdown->id;
			foreach ($this_data['data'] as $language_id => $value) {
				$modelDropDownDescription				=  new DropDownDescription();
				$modelDropDownDescription->language_id	=	$language_id;
				$modelDropDownDescription->parent_id	=	$dropdownId;
				$modelDropDownDescription->name			=	$value['name'];		
				$modelDropDownDescription->save();
			}
			if(!empty($this_data['service'])){
				$counter	=	1;
				foreach ($this_data['service'] as $stepsResult) { 
					if($counter == 1){
						$service_days			=	$stepsResult['service_days']-5;
						$service_km				=	$stepsResult['service_km']-100;
						$service_days_string	=	$service_days."-".$stepsResult['service_days'];
						$service_km_string		=	$service_km."-".$stepsResult['service_km'];;
					}else{
						$service_days			=	$stepsResult['service_days']-10;
						$service_km				=	$stepsResult['service_km']-100;
						$service_days_string	=	$service_days."-".$stepsResult['service_days'];
						$service_km_string		=	$service_km."-".$stepsResult['service_km'];;
					}
				
					$modelService                =  new ModelServices;
					$modelService->dropdown_manager_id    =  $dropdownId;
					$modelService->service_no    =  $counter;
					$modelService->service_days  = 	$stepsResult['service_days'];
					$modelService->service_days_string  = 	$service_days_string;
					$modelService->service_amount = ($stepsResult['service_amount']) ?
												   $stepsResult['service_amount'] : 0;
					$modelService->service_km 	 = 	$stepsResult['service_km'];
					$modelService->service_km_string 	 = 	$service_km_string;
					$modelService->service_type  = 	$stepsResult['service_type'];
					$modelService->save();
					$counter++;
				}
			}
			Session::flash('flash_notice', trans(ucfirst($type).' added successfully')); 
			return Redirect::to('adminpnlx/dropdown-manager/'.$type);
		}
	}//end saveDropDown()
/**
* Function for display page  for edit DropDown page
*
* @param $Id ad id of DropDown 
* @param $type as category of dropdown 
*
* @return view page. 
*/	
	public function editDropDown($Id,$type){
		$dropdown				=	DropDown::find($Id);
		if(empty($dropdown)) {
			return Redirect::to('adminpnlx/dropdown-manager/'.$type);
		}
		$dropdownDescription	=	DropDownDescription::where('parent_id', '=',  $Id)->get();
		$services  = ModelServices::where('dropdown_manager_id',$Id)->get()->toArray();
		$multiLanguage		 	=	array();
		
		if(!empty($dropdownDescription)){
			foreach($dropdownDescription as $description) {
				$multiLanguage[$description->language_id]['name']			=	$description->name;	
				$multiLanguage[$description->language_id]['number_of_km']	=	$dropdown->number_of_km;				
			}
		}
		$languages				=	DB::select("CALL GetAcitveLanguages(1)");
		$default_language		=	Config::get('default_language');
		$language_code 			=   $default_language['language_code'];
        
		return  View::make('admin.dropdown.edit',array('languages' => $languages,'language_code' => $language_code,'dropdown' => $dropdown,'multiLanguage' => $multiLanguage,'type'=>$type,'services'=>$services));
	}// end editDropDown()
/**
* Function for view DropDown 
*
* @param $Id ad id of DropDown 
* @param $type as category of dropdown 
*
* @return redirect page. 
*/

	public function view($Id,$type=''){
		$dropdown				=	DropDown::find($Id);
		if(empty($dropdown)) {
			return Redirect::to('adminpnlx/dropdown-manager/'.$type);
		}
		$dropdownDescription	=	DropDownDescription::where('parent_id', '=',  $Id)->get();
		$services  = ModelServices::where('dropdown_manager_id',$Id)->get()->toArray();
		$multiLanguage		 	=	array();
		
		if(!empty($dropdownDescription)){
			foreach($dropdownDescription as $description) {
				$multiLanguage[$description->language_id]['name']			=	$description->name;	
				$multiLanguage[$description->language_id]['number_of_km']	=	$dropdown->number_of_km;				
			}
		}

		$languages				=	DB::select("CALL GetAcitveLanguages(1)");
		$default_language		=	Config::get('default_language');
		$language_code 			=   $default_language['language_code'];
		return  View::make('admin.dropdown.show',array('languages' => $languages,'language_code' => $language_code,'dropdown' => $dropdown,'multiLanguage' => $multiLanguage,'type'=>$type,'services'=>$services));
	}
/**
* Function for update DropDown 
*
* @param $Id ad id of DropDown 
* @param $type as category of dropdown 
*
* @return redirect page. 
*/
	function updateDropDown($Id,$type=''){
		Input::replace($this->arrayStripTags(Input::all()));
		$this_data										=	Input::all();
		$dropdown 										= 	DropDown:: find($Id);
		$default_language								=	Config::get('default_language');
		$language_code 									=   $default_language['language_code'];
		$dafaultLanguageArray							=	$this_data['data'][$language_code];
		if($type == "vehiclemodel"){
			$validator = Validator::make(
				array(
					'name' 			=>  $dafaultLanguageArray['name'],
					'dropdown_type'	=>	$type,
					'number_of_km'=> $dafaultLanguageArray['number_of_km'],
					
				),	
				array(
					'name' 			=> 'required',
					"number_of_km" 	=> "required|numeric",
					
				)
			);
		}else{
			$validator = Validator::make(
				array(
					'name' 			=>  $dafaultLanguageArray['name'],
					'dropdown_type'	=>	$type,
					
				),	
				array(
					'name' 			=> 'required',
					
					
				)
			);
		}
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/dropdown-manager/edit-dropdown/'.$Id.'/'.$type)
				->withErrors($validator)->withInput();
		}else{
			$dropdown->name								= 	$dafaultLanguageArray['name'];
			if(isset($dafaultLanguageArray['number_of_km'])){
				$dropdown->number_of_km    					= 	$dafaultLanguageArray['number_of_km'];
			}
			$dropdown->save();
			$dropdownId		=	$dropdown->id;
			$dropdownId		=	$Id;
			DropDownDescription::where('parent_id', '=', $Id)->delete();

			foreach ($this_data['data'] as $language_id => $value) {
				$modelDropDownDescription				=  new DropDownDescription();
				$modelDropDownDescription->language_id	=	$language_id;
				$modelDropDownDescription->name			=	$value['name'];	
				$modelDropDownDescription->parent_id	=	$dropdownId;
				$modelDropDownDescription->save();					
			}
			if(!empty($this_data['service'])){
				ModelServices::where('dropdown_manager_id',$Id)->delete();
				$counter	=	1;
				foreach ($this_data['service'] as $stepsResult) { 
					if($counter == 1){
						$service_days			=	$stepsResult['service_days']-5;
						$service_km				=	$stepsResult['service_km']-100;
						$service_days_string	=	$service_days."-".$stepsResult['service_days'];
						$service_km_string		=	$service_km."-".$stepsResult['service_km'];;
					}else{
						$service_days			=	$stepsResult['service_days']-10;
						$service_km				=	$stepsResult['service_km']-100;
						$service_days_string	=	$service_days."-".$stepsResult['service_days'];
						$service_km_string		=	$service_km."-".$stepsResult['service_km'];;
					}
				
					$modelService                =  new ModelServices;
					$modelService->dropdown_manager_id    =  $dropdownId;
					$modelService->service_no    =  $counter;
					$modelService->service_days  = 	$stepsResult['service_days'];
					$modelService->service_days_string  = 	$service_days_string;
					$modelService->service_amount = ($stepsResult['service_amount']) ?
												   $stepsResult['service_amount'] : 0;
					$modelService->service_km 	 = 	$stepsResult['service_km'];
					$modelService->service_km_string 	 = 	$service_km_string;
					$modelService->service_type  = 	$stepsResult['service_type'];
					$modelService->save();
					$counter++;
				}
			}
				
			Session::flash('flash_notice',trans(ucfirst($type)." updated successfully")); 
			return Redirect::intended('adminpnlx/dropdown-manager/'.$type);
		}
	}// end updateDropDown()
/**
* Function for update DropDown  status
*
* @param $Id as id of DropDown 
* @param $Status as status of DropDown 
* @param $type as category of dropdown 
*
* @return redirect page. 
*/	
	public function updateDropDownStatus($Id = 0, $Status = 0,$type=''){
		if($Status == 0	){
			$statusMessage	=	trans(ucfirst($type)." deactivated successfully");
		}else{
			$statusMessage	=	trans(ucfirst($type)." activated successfully");
		}
		$this->_update_all_status('dropdown_managers',$Id,$Status);
		
		/* if($Status == 1){
			$message				=	trans("messages.master.master_activate_message");
		}else{
			$message				=	trans("messages.master.master_deactivate_message");
		}
		$model						=	DropDown::find($Id);
		$model->is_active			=	$Status;
		$model->save(); */
		Session::flash('flash_notice',$statusMessage); 
		return Redirect::to('adminpnlx/dropdown-manager/'.$type);
	}// end updateDropDownStatus()
/**
* Function for delete DropDown 
*
* @param $Id as id of DropDown 
* @param $type as category of dropdown 
*
* @return redirect page. 
*/	
	public function deleteDropDown($Id = 0,$type=''){
		$dropdown					=	DropDown::find($Id) ;
		//$dropdown->description()->delete();
		/* if($type=='faq'){
			$dropdown->faq()->delete();
		} */
		//$dropdown->delete();
		if(!empty($dropdown)){
			$this->_delete_table_entry('dropdown_managers',$Id,'id');
			$this->_delete_table_entry('dropdown_manager_descriptions',$Id,'parent_id');
			Session::flash('flash_notice', trans(ucfirst($type)." removed successfully"));  
		}else{
			Session::flash('error', trans("Invalid url"));  
		}
		return Redirect::to('adminpnlx/dropdown-manager/'.$type);
	}// end deleteDropDown()
/**
* Function for multiple delete
*
* @param $type as type of dropdown
*
* @return redirect page. 
*/
 	public function performMultipleAction($type = 0){
		if(Request::ajax()){
			$actionType 			= ((Input::get('type'))) ? Input::get('type') : '';
			if(!empty($actionType) && !empty(Input::get('ids'))){
				if($actionType	==	'delete'){
					$dropdown		=	DropDown::whereIn('id', Input::get('ids'));
					$dropdown->description()->delete();
					/* if($type=='faq'){
						$dropdown->faq()->delete();
					} */
					$dropdown->delete();
				}
				Session::flash('flash_notice', trans("messages.user_management.action_performed_message")); 
			}
		}
	}//end performMultipleAction()




public function deleteService(){
	$id  = Input::get('id'); 
	DB::table('model_services')->where('id', '=', $id)->delete();
}
	// end deleteService()

}// end DropDownController
