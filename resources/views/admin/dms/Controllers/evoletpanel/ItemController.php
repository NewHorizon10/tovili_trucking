<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Users;
use App\Model\DropDown;
use App\Model\Item;
use App\Model\QualityParams;
use App\Model\Acl;
use App\Model\UserPermission;
use App\Model\UserPermissionAction;
use App\Model\AclAdminAction;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* AdminDashBoard Controller
*
* Add your methods in the class below
*
* This file will render views\admin\dashboard
*/
	class ItemController extends BaseController {
	/**
	* Function for display adminpnlx dashboard
	*
	* @param null
	*
	* @return view page. 
	*/
	public function itemList(){	
		$is_allowed = $this->check_section_permission(array('section'=>'item'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$DB			=	Item::query();
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
				if(!empty($fieldValue) ){
					$DB->where("items.$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
				
			}
		}
		
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->designation != PURCHASE_HOD ){
			$DB->where("created_by",Auth::user()->id);
		}
		$sortBy 					= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'id';
		$order  					= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		
		$result 					= 	$DB
										->select('items.*',DB::Raw("(SELECT category_name FROM item_category WHERE item_category.id=items.category) as category_name"),DB::Raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id=items.purchase) as purchase"),DB::Raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id=items.item_type) as item_type"))
										->orderBy($sortBy,$order)
										->paginate(Config::get("Reading.records_per_page"));
										
		$complete_string			=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("dealers_search_data",$inputGet);

		return View::make('admin.items.index',compact('result','searchVariable','sortBy','order','query_string'));
	}
	//end function itemList()
	public function stockList(){	
		/*$is_allowed = $this->check_section_permission(array('section'=>'item'));
		if(empty($is_allowed)){
			return Redirect::back();
		}*/
		$DB			=	Item::query();
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
				if(!empty($fieldValue) ){
					$DB->where("items.$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
				
			}
		}
		
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->designation != PURCHASE_HOD ){
			$DB->where("created_by",Auth::user()->id);
		}
		$sortBy 					= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'id';
		$order  					= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		
		$result 					= 	$DB
										->where("is_approved",1)
										->select('items.*',DB::Raw("(SELECT category_name FROM item_category WHERE item_category.id=items.category) as category_name"),DB::Raw("(SELECT commodity_name FROM commodity_category WHERE commodity_category.id=items.commodity_category) as commodity_category_name"),DB::Raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id=items.purchase) as purchase"),DB::Raw("(SELECT name FROM dropdown_managers WHERE dropdown_managers.id=items.item_type) as item_type"))
										->orderBy($sortBy,$order)
										->paginate(Config::get("Reading.records_per_page"));
										
		$complete_string			=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("dealers_search_data",$inputGet);

		return View::make('admin.items.stock',compact('result','searchVariable','sortBy','order','query_string'));
	}

	/**
	* Function for dealer Add
	*
	* @param null
	*
	* @return view page. 
	*/


	public function itemAdd(){
		$is_allowed = $this->check_section_permission(array('section'=>'item'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		
		 $size 	=   DB::table('dropdown_managers')->where('dropdown_type','size')->where('is_active',1)->pluck('name','id');		
		 $color	=   DB::table('dropdown_managers')->where('dropdown_type','color')->where('is_active',1)->pluck('name','id');	
		  $itemmodel	=   DB::table('dropdown_managers')->where('dropdown_type','itemmodel')->where('is_active',1)->pluck('name','id');	
		  $goodtype	=   DB::table('dropdown_managers')->where('dropdown_type','goodtype')->where('is_active',1)->pluck('name','id');
		  $materialtype	=   DB::table('dropdown_managers')->where('dropdown_type','materialtype')->where('is_active',1)->pluck('name','id');
		  $purchaseUnit	=   DB::table('dropdown_managers')->where('dropdown_type','purchaseUnit')->where('is_active',1)->pluck('name','id');
		  $requirmentType	=   DB::table('dropdown_managers')->where('dropdown_type','requirmentType')->where('is_active',1)->pluck('name','id');
		 $item_category 		=   DB::table('item_category')->where('is_deleted',0)->pluck('category_name','id');	
		 $commodity_category 	=   DB::table('commodity_category')->where('is_active',1)->pluck('commodity_name','id');
		 $vendors 				=   DB::table('vendors')->where('is_active',1)->pluck('company_name','id');	
		// print_r($purchaseUnit); die;
		 return  View::make('admin.items.add',compact('size','color','vendors','item_category','commodity_category','itemmodel','goodtype','materialtype','purchaseUnit','requirmentType'));
	}
	// end function dealerAdd()

	public function addMore(){
		$counter = Input::get('counter');
		return  View::make('admin.items.addMore',compact('counter'));
	}
	// end function dealerAdd()

	/**
	* Function for dealer Save
	*
	* @param null
	*
	* @return view page. 
	*/

	public function itemSave(){
		$is_allowed = $this->check_section_permission(array('section'=>'item'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		 
		if(!empty($thisData)){
			
			$validator	 =	 Validator::make(
								$thisData,
								array(
									'unique_id' 						=>	 'required',
									'item_name' 						=>	 'required',
									'category' 					=>	 'required',
									'commodity_category' 		=>	 'required',
									'model'						=>	 'required',
									'material_type'				=>	 'required',
									'hsn_code'					=>	 'required',
									'self_life_days'			=>	 'required|numeric',
									'item_type'					=>	 'required',
									'warranty_months'			=>	 'required|numeric',
									'purchase'					=>	 'required',
									'distribution'				=>	 'required',
									'conversion_factor'			=>	 'required',
									//'safe_stock'				=>	 'required',
									//'min_quantity'				=>	 'required|numeric',
									//'min_stock'					=>	 'required|numeric',
									//'max_quantity'				=>	 'required|numeric',
									//'item_reorder'					=>	 'required',
									'requirement_type'			=>	 'required',
									//'max_stock'					=>	 'required|numeric',
									'avg_moving_cost'			=>	 'required',
									'question'                  =>   'required',
									//'vendors'					=>	 'required',
									//'short_description'			=>	 'required',
									//'description'				=>	 'required',
									
								),
								array
								(
									"unique_id.required"			=>	trans("The Item code field is required."),
									"item_name.required"			=>	trans("The Name field is required."),
									"category.required"				=>	trans("The Category field is required."),
									"commodity_category.required"	=>	trans("The Commodity Category field is required."),
									// "model.required"				=>	trans("The model field is required."),
									// "material_type.required"		=>	trans("The Material Type field is required."),
									"hsn_code.required"				=>	trans("The HSN Code field is required."),
									"self_life_days.required"		=>	trans("The self Life field is required."),
									// "item_type.required"			=>	trans("The Item Type field is required."),
									"warranty_months.required"		=>	trans("The Warranty Months field is required."),
									// "purchase.required"				=>	trans("The Purchase field is required."),
									// "distribution.required"			=>	trans("The Distribution field is required."),
									"conversion_factor.required"	=>	trans("The Conversion Factor field is required."),
									//"safe_stock.required"			=>	trans("The Safe Stock field is required."),
									//"min_quantity.required"			=>	trans("The Minimum Quantity field is required."),
									//"min_stock.required"			=>	trans("The Minimum Stock field is required."),
									//"max_quantity.required"			=>	trans("The Maximum Quantity field is required."),
									//"item_reorder.required"				=>	trans("The Reorder field is required."),
									//"max_stock.required"			=>	trans("The Maximum Stock field is required."),
									// "requirement_type.required"		=>	trans("The Requirement type field is required."),
									"avg_moving_cost.required"		=>	trans("The Moving cost field is required."),
									//"short_description.required"	=>	trans("The Short Description field is required."),
									//"description.required"			=>	trans("The Description field is required."),
									"vendors.required"				=>	trans("The Vendors field is required."),
									"question.required"				=> trans("The Question field is required."),
									
								)	
								
							);
			if ($validator->fails()) {	
				return Redirect::to('adminpnlx/items/add-item')->withErrors($validator)->withInput();
			}else{
				DB::beginTransaction();
				$item 						= 	new Item;
				$item->unique_id				=	Input::get('unique_id');;
				$item->item_name   				= 	Input::get('item_name');
				$item->created_by   		= 	Auth::user()->id;
				$item->category   			= 	Input::get('category');
				$item->commodity_category   = 	Input::get('commodity_category');
				$item->modal   				= 	Input::get('model');
				$item->material_type   		= 	Input::get('material_type');
				$item->hsn_code   			= 	Input::get('hsn_code');
				$item->self_life_days   	= 	Input::get('self_life_days');
				$item->item_type   			= 	Input::get('item_type');
				$item->warranty_months   	= 	Input::get('warranty_months');
				$item->purchase   			= 	Input::get('purchase');
				$item->distribution   		= 	Input::get('distribution');
				$item->conversion_factor   	= 	Input::get('conversion_factor');
				$item->safe_stock   		= 	(Input::get('safe_stock')) ? Input::get('safe_stock') : 0;;
				$item->min_quantity   		= 	(Input::get('min_quantity')) ? Input::get('min_quantity') : 0;
				$item->min_stock   			= 	(Input::get('min_stock')) ? Input::get('min_stock') : 0;
				$item->max_quantity   		= 	(Input::get('max_quantity')) ? Input::get('max_quantity') :0;
				$item->item_reorder   		= 	(Input::get('item_reorder')) ? Input::get('item_reorder') : 0;
				$item->max_stock   			= 	(Input::get('max_stock')) ? Input::get('max_stock') : 0;
				$item->requirement_type   	= 	Input::get('requirement_type');
				$item->avg_moving_cost   	= 	Input::get('avg_moving_cost');
				$item->size   				= 	Input::get('size');
				$item->color   				= 	Input::get('color');
				$item->height   			= 	Input::get('height');
				$item->width   				= 	Input::get('width');
				$item->weight   			= 	Input::get('weight');
				$item->volume   			= 	Input::get('volume');
				$item->short_description   	= 	Input::get('short_description');
				$item->description   		= 	Input::get('description');
				$item->vendors   			= 	(!empty(Input::get('vendors'))) ? implode(',',Input::get('vendors')) : "";
				//$item->quality_params   	= 	implode(',',Input::get('quality_params'));
				$item->quality_params   	= 	'';
				$item->is_active   			= 	1;
				$item->is_approved   		= 	0;
				$item->save();
				DB::commit();
				$id  = $item->id;
				/* if(!empty($id)){
					$item_category 		=   DB::table('item_category')->where('id',Input::get('category'))->select('category_code')->first();	
					$unique_id		=	'#I/'.$item_category->category_code.'/000'.$id;
					Item::where('id',$id)->update(array('unique_id'=>$unique_id));
				} */

				if(!empty($thisData['question'])){
					foreach ($thisData['question'] as $question){
						if($question != ''){
							$ques             			= new QualityParams;
							$ques->item_id 				= $id;
							$ques->questions   			= $question;
							$ques->created_at			= date('Y-m-d H:i:s');
							$ques->save(); 
						}
					}
				}

				DB::commit();
				
				Session::flash('flash_notice', trans("Item has been added successfully")); 
				return Redirect::to('adminpnlx/items');
			}
		}
	}
	// end function dealerSave()
	/**
	* Function for dealer Edit
	*
	* @param userId
	*
	* @return view page. 
	*/
	public function itemView($itemId = 0){
		$is_allowed = $this->check_section_permission(array('section'=>'item'));
		if(empty($is_allowed)){
			return Redirect::back();
		} 
		
		$is_allowed_view = $this->check_entry_allow_view(array('section'=>'item','id'=>$itemId));
		if(empty($is_allowed_view)){
			return Redirect::back();
		}
		
		$itemDetails	    =	DB::table('items')
								->where("items.id",$itemId)
								->leftjoin('dropdown_managers as color_table', 'items.color', '=', 'color_table.id')
								->leftjoin('dropdown_managers as size_table', 'items.size', '=', 'size_table.id')
								->leftjoin('item_category', 'items.category', '=', 'item_category.id')
								->leftjoin('commodity_category', 'items.commodity_category', '=', 'commodity_category.id')
								->select('items.*', 'color_table.name as color_name','size_table.name as size_name','item_category.category_name as category_name','commodity_category.commodity_name as commodity_name'
								)
								->first();
		$vendros = explode(",",$itemDetails->vendors);					
		$vendors = DB::table("vendors")->whereIn('id',$vendros)->select("company_name")->get();
		$questions = DB::table("quality_params_questions")->where('item_id',$itemId)->get();
		if(empty($itemDetails)) {
			return Redirect::back();
		}
		// echo '<pre>'; print_r($questions); die;
		return View::make("admin.items.view", compact("itemDetails","vendors","questions"));
		
	}
	 // end function itemEdit()	

	/**
	* Function for dealer Edit
	*
	* @param userId
	*
	* @return view page. 
	*/
		public function itemEdit($itemId = 0){
			$is_allowed = $this->check_section_permission(array('section'=>'item'));
			if(empty($is_allowed)){
				return Redirect::back();
			} 
			
			
			$is_allowed_view = $this->check_entry_allow_view(array('section'=>'item','id'=>$itemId));
			if(empty($is_allowed_view)){
				return Redirect::back();
			}
			
			$itemDetails			=	Item::find($itemId); 
			if(empty($itemDetails)) {
				return Redirect::back();
			}
			$size 				=   DB::table('dropdown_managers')->where('dropdown_type','size')->where('is_active',1)->pluck('name','id');		
			$color 				=   DB::table('dropdown_managers')->where('dropdown_type','color')->where('is_active',1)->pluck('name','id');	

			$itemmodel	=   DB::table('dropdown_managers')->where('dropdown_type','itemmodel')->where('is_active',1)->pluck('name','id');	
			$goodtype	=   DB::table('dropdown_managers')->where('dropdown_type','goodtype')->where('is_active',1)->pluck('name','id');
			$materialtype	=   DB::table('dropdown_managers')->where('dropdown_type','materialtype')->where('is_active',1)->pluck('name','id');
			$purchaseUnit	=   DB::table('dropdown_managers')->where('dropdown_type','purchaseUnit')->where('is_active',1)->pluck('name','id');
			$requirmentType	=   DB::table('dropdown_managers')->where('dropdown_type','requirmentType')->where('is_active',1)->pluck('name','id');

			
			$item_category 		=   DB::table('item_category')->where('is_deleted',0)->pluck('category_name','id');	
		 	$commodity_category =   DB::table('commodity_category')->where('is_active',1)->pluck('commodity_name','id');
			$vendors 			=   DB::table('vendors')->where('is_active',1)->pluck('company_name','id');	
			$questions 			=   DB::table("quality_params_questions")->where('item_id',$itemId)->get();
			// echo "hello"; die;
			return View::make("admin.items.edit", compact("itemDetails",'size',"color","questions","vendors","item_category","commodity_category",'itemmodel','goodtype','materialtype','purchaseUnit','requirmentType'));
			
		}
		 // end function itemEdit()


	/**
	* Function for dealer Update
	*
	* @param userId
	*
	* @return view page. 
	*/

	public function itemUpdate($itemId){	
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData						=	Input::all(); 
		//echo "<pre>";print_r($thisData);die;
		//echo '<pre>'; print_r($thisData);die;
		if(!empty($thisData)){
			
				$validator	 =	 Validator::make(
								$thisData,
								array(
									'unique_id' 						=>	 'required',
									'item_name' 						=>	 'required',
									'category' 					=>	 'required',
									'commodity_category' 		=>	 'required',
									'model'						=>	 'required',
									'material_type'				=>	 'required',
									'hsn_code'					=>	 'required',
									'self_life_days'			=>	 'required',
									'item_type'					=>	 'required',
									'warranty_months'			=>	 'required',
									'purchase'					=>	 'required',
									'distribution'				=>	 'required',
									'conversion_factor'			=>	 'required',
									//'safe_stock'				=>	 'required',
									//'min_quantity'				=>	 'required',
									//'min_stock'					=>	 'required',
									//'max_quantity'				=>	 'required',
									//'item_reorder'					=>	 'required',
									'requirement_type'			=>	 'required',
									//'max_stock'					=>	 'required',
									'avg_moving_cost'			=>	 'required',
									//'vendors'					=>	 'required',
									//'short_description'			=>	 'required',
									//'description'				=>	 'required',
									
								),
								array
								(
									"unique_id.required"			=>	trans("The Item code field is required."),
									"item_name.required"					=>	trans("The Name field is required."),
									"category.required"				=>	trans("The Category field is required."),
									"commodity_category.required"	=>	trans("The Commodity Category field is required."),
									// "model.required"				=>	trans("The model field is required."),
									// "material_type.required"		=>	trans("The Material Type field is required."),
									"hsn_code.required"				=>	trans("The HSN Code field is required."),
									"self_life_days.required"		=>	trans("The self Life field is required."),
									// "item_type.required"			=>	trans("The Item Type field is required."),
									"warranty_months.required"		=>	trans("The Warranty Months field is required."),
									// "purchase.required"				=>	trans("The Purchase field is required."),
									// "distribution.required"			=>	trans("The Distribution field is required."),
									"conversion_factor.required"	=>	trans("The Conversion Factor field is required."),
									//"safe_stock.required"			=>	trans("The Safe Stock field is required."),
									//"min_quantity.required"			=>	trans("The Minimum Quantity field is required."),
									//"min_stock.required"			=>	trans("The Minimum Stock field is required."),
									//"max_quantity.required"			=>	trans("The Maximum Quantity field is required."),
									//"item_reorder.required"				=>	trans("The Reorder field is required."),
									//"max_stock.required"			=>	trans("The Maximum Stock field is required."),
									// "requirement_type.required"		=>	trans("The Requirement type field is required."),
									"avg_moving_cost.required"		=>	trans("The Moving cost field is required."),
									//"short_description.required"	=>	trans("The Short Description field is required."),
									//"description.required"			=>	trans("The Description field is required."),
									"vendors.required"				=>	trans("The Vendors field is required."),
									
									
								)
								
							);
				//print_r($thisData);die;
				if ($validator->fails()){
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
					DB::beginTransaction();
					
				$item 						= 	Item::find($itemId);
				$item->unique_id   				= 	Input::get('unique_id');
				$item->item_name   				= 	Input::get('item_name');
				$item->category   			= 	Input::get('category');
				$item->commodity_category   = 	Input::get('commodity_category');
				$item->modal   				= 	Input::get('model');
				$item->material_type   		= 	Input::get('material_type');
				$item->hsn_code   			= 	Input::get('hsn_code');
				$item->self_life_days   	= 	Input::get('self_life_days');
				$item->item_type   			= 	Input::get('item_type');
				$item->warranty_months   	= 	Input::get('warranty_months');
				$item->purchase   			= 	Input::get('purchase');
				$item->distribution   		= 	Input::get('distribution');
				$item->conversion_factor   	= 	Input::get('conversion_factor');
				$item->safe_stock   		= 	(Input::get('safe_stock')) ? Input::get('safe_stock') : 0;;
				$item->min_quantity   		= 	(Input::get('min_quantity')) ? Input::get('min_quantity') : 0;
				$item->min_stock   			= 	(Input::get('min_stock')) ? Input::get('min_stock') : 0;
				$item->max_quantity   		= 	(Input::get('max_quantity')) ? Input::get('max_quantity') :0;
				$item->item_reorder   		= 	(Input::get('item_reorder')) ? Input::get('item_reorder') : 0;
				$item->max_stock   			= 	(Input::get('max_stock')) ? Input::get('max_stock') : 0;
				$item->requirement_type   	= 	Input::get('requirement_type');
				$item->avg_moving_cost   	= 	Input::get('avg_moving_cost');
				$item->size   				= 	Input::get('size');
				$item->color   				= 	Input::get('color');
				$item->height   			= 	Input::get('height');
				$item->width   				= 	Input::get('width');
				$item->weight   			= 	Input::get('weight');
				$item->volume   			= 	Input::get('volume');
				$item->short_description   	= 	Input::get('short_description');
				$item->description   		= 	Input::get('description');
				$item->vendors   			= 	(!empty(Input::get('vendors'))) ? implode(',',Input::get('vendors')) : "";
				//$item->quality_params   	= 	implode(',',Input::get('quality_params'));
				$item->quality_params   	= 	'';
				$item->save();
				DB::commit();
				$id  = $item->id;
				DB::table('quality_params_questions')->where('item_id',$id)->delete();
				if(!empty($thisData['question'])){
					foreach ($thisData['question'] as $question){
						if($question != ''){
							$ques             			= new QualityParams;
							$ques->item_id 				= $id;
							$ques->questions   			= $question;
							$ques->updated_at			= date('Y-m-d H:i:s');
							$ques->save(); 
						}
					}
				}

				DB::commit();
				Session::flash('flash_notice', trans("Item has been updated successfully")); 
				return Redirect::to('adminpnlx/items');
			}
		}
	}
	// end function dealerUpdate()


	/**
	* Function for delete  dealer  
	*
	* @param user_id
	*
	* @return view page. 
	*/
	public function ItemDelete(){
		$id  = Input::get('id'); 
	    QualityParams::where('id',$id)->delete();
	} 
	// public function deleteItem($itemId = 0){
	// 	$userDetails	=	Item::find($itemId); 
	// 	if(empty($userDetails)) {
	// 		return Redirect::back();
	// 	}
	// 	if($itemId){	
	// 		$email 						=	'delete_'.$itemId .'_'.$userDetails->email;
	// 		$userModel					=	Item::where('id',$itemId)->delete();
	// 		Session::flash('flash_notice',trans("Item deleted successfully")); 
	// 	}
	// 	return Redirect::back();
	// } 
	// end deleteDealer()


	/**
	* Function for update DealerStatus    
	*
	* @param user_id,status
	*
	* @return view page. 
	*/
	public function updateItemStatus($id = 0, $Status = 0){
		if($Status == 1){
			$statusMessage	=	trans("Item Approved successfully");
		}else{
			$statusMessage	=	trans("Item Rejected successfully");
		}
		DB::table("items")->where("id",$id)->update(array("is_approved"=>$Status,"approved_by"=>Auth::user()->id));
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} 
	// end updateDealerStatus()

	
	
	  
}//end DealerManagementController

