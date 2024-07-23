<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\CommodityCategory;

use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* CommodityCategoryController Controller
*
* Add your methods in the class below
*
* This file will render views\CommodityCategoryController\dashboard
*/
	class CommodityCategoryController extends BaseController {
		
		public $model	=	'CommodityCategory';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function list(){
		$is_allowed = $this->check_section_permission(array('section'=>'commodity_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$DB 					= 	CommodityCategory::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of name and description */ 
			if ((Input::get())) {
				$searchData			=	Input::get();
				//echo'<pre>'; print_r($searchData); echo'</pre>'; die;
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
					$DB->where("commodity_category.".$fieldName,'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
            }
			$DB->where('is_deleted',0);
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';

            $result 				= 	$DB
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
			
			// echo'<pre>'; print_r($result); echo'</pre>'; die;
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("_search_data",$inputGet);

			return  View::make('admin.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string'));
	}
		
	

	/**
	* Function for add  page
	*
	* @param null
	*
	* @return view page. 
	*/


	public function add(){
		$is_allowed = $this->check_section_permission(array('section'=>'commodity_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		return View::make('admin.'.$this->model.'.add');
	}
	
	/**
	* Function for save 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function save(){
		$is_allowed = $this->check_section_permission(array('section'=>'commodity_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
                    'commodity_name' 				=>	 'required',
					'commodity_name' 				=>	 'required',
					'commodity_description' 		=>	 'required',
				),
				array(
                    "commodity_code.required"		=>	trans("The commodity code field is required."),
					"commodity_name.required"		=>	trans("The commodity name field is required."),
					"commodity_description.required"	=>	trans("The commodity description field is required."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
                $commodityCategory 					        = 	new CommodityCategory;
                $commodityCategory->commodity_code	        =	Input::get('commodity_code');
				$commodityCategory->commodity_name	        =	Input::get('commodity_name');
				$commodityCategory->commodity_desc	        =	Input::get('commodity_description');
				$commodityCategory->created_at		        =   date("Y-m-d H:i:s");
				$commodityCategory->save();
				// echo $data; die;
				Session::flash("success",trans("Commodity Category added successfully."));
				return Redirect::to('adminpnlx/commodity-category');
			}
		}
	}
	
	public function edit($id = ""){
		$is_allowed = $this->check_section_permission(array('section'=>'commodity_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$Details	    =	DB::table('commodity_category')
								->where('commodity_category.id',$id)
								->first();

		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}	
		
		return View::make('admin.'.$this->model.'.edit', compact("Details"));
	
		
	} // end editUser()
	
	public function update($id=""){
		$is_allowed = $this->check_section_permission(array('section'=>'commodity_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
                    'commodity_name' 				=>	 'required',
					'commodity_name' 				=>	 'required',
					'commodity_description' 		=>	 'required',
				),
				array(
                    "commodity_code.required"		=>	trans("The commodity code field is required."),
					"commodity_name.required"		=>	trans("The commodity name field is required."),
					"commodity_description.required"	=>	trans("The commodity description field is required."),
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				
				$commodityCategory						    = 	CommodityCategory::find($id);
				$commodityCategory->commodity_code	        =	Input::get('commodity_code');
				$commodityCategory->commodity_name	        =	Input::get('commodity_name');
				$commodityCategory->commodity_desc	=	Input::get('commodity_description');
				$commodityCategory->updated_at		        =   date("Y-m-d H:i:s");
				$commodityCategory->save();
				Session::flash('flash_notice', trans("Commodity Category has been updated successfully.")); 
				return Redirect::to('adminpnlx/commodity-category');
			}
		}
	}


	public function view($id=""){
		$is_allowed = $this->check_section_permission(array('section'=>'commodity_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$Details	    =	DB::table('commodity_category')
								->where('commodity_category.id',$id)
								->first();

		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}else{	
			return View::make('admin.'.$this->model.'.view', compact("Details"));
		}
	}

	
	public function delete($id = ''){
		$is_allowed = $this->check_section_permission(array('section'=>'commodity_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$Details			=	CommodityCategory::find($id); 
		if(empty($Details)) {
			return Redirect::back();
		}
		if($id){	
			$userModel					=	CommodityCategory::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans("Commodity Category  has been deleted successfully.")); 
		}
		return Redirect::back();
	}

	public function updateStatus($id = 0, $Status = 0){
		$is_allowed = $this->check_section_permission(array('section'=>'commodity_category'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		if($Status == 0){
			$statusMessage	=	trans("Commodity Category has been deactivated.");
			$staffDetails		=	CommodityCategory::find($id); 
		}else{
			$statusMessage	=	trans("Commodity Category has been activated.");
		}
		$this->_update_all_status("commodity_category",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} // end updateCommodityCategorystatus()
	
	
} //end CommodityCategoryController()
