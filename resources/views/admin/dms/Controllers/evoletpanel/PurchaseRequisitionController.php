<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Users;
use App\Model\DropDown;
use App\Model\PurchaseRequisition;
use App\Model\PurchaseRequisitionItem;
use App\Model\Acl;
use App\Model\UserPermission;
use App\Model\UserPermissionAction;
use App\Model\AclAdminAction;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* PurchaseRequisitionController Controller
*
* Add your methods in the class below
*
* This file will render views\admin\dashboard
*/
	class PurchaseRequisitionController extends BaseController {
	/**
	* Function for display adminpnlx dashboard
	*
	* @param null
	*
	* @return view page. 
	*/
	public function purchaseRequisitionList(){	
		
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
										->orderBy($sortBy,$order)
										->paginate(Config::get("Reading.records_per_page"));
										
		$complete_string			=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		
		//echo '<pre>'; print_r($result); die;
		return View::make('admin.PurchaseRequisition.index',compact('result','searchVariable','sortBy','order','query_string'));
	}
	//end function itemList()


	/**
	* Function for dealer Add
	*
	* @param null
	*
	* @return view page. 
	*/


	public function purchaseRequisitionAdd(){
		
		$items =   DB::table('items')->where('is_active',1)->where('is_approved',1)->pluck('unique_id','id');	
		$departments =   DB::table('departments')->where('is_active',1)->where('is_deleted',0)->pluck('title','id');	
		 $requirmentType	=   DB::table('dropdown_managers')->where('dropdown_type','requirmentType')->where('is_active',1)->pluck('name','id');
		return  View::make('admin.PurchaseRequisition.add',compact('items','departments','requirmentType'));
	}
	// end function dealerAdd()

	public function purchaseRequisitionAddMore(){
		$counter = Input::get('counter');
		$items =   DB::table('items')->where('is_active',1)->where('is_approved',1)->pluck('unique_id','id');	
		$departments =   DB::table('departments')->where('is_active',1)->where('is_deleted',0)->pluck('title','id');
		$requirmentType	=   DB::table('dropdown_managers')->where('dropdown_type','requirmentType')->where('is_active',1)->pluck('name','id');		
		return  View::make('admin.PurchaseRequisition.addMore',compact('items','departments','counter','requirmentType'));
	}
	// end function dealerAdd()


	/**
	* Function for dealer Save
	*
	* @param null
	*
	* @return view page. 
	*/

	public function purchaseRequisitionSave(){
		
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		// echo "<pre>"; print_r($thisData);die;
		if(!empty($thisData)){
			
			$validator	 =	 Validator::make(
								$thisData,
								array(
									//'item_code' 							=>	 'required',
									//'quantity' 								=>	 'required|numeric',
									//'requirement_type' 						=>	 'required',
									// 'specific_vendor' 						=>	 'required',
									// 'department_name' 						=>	 'required',
									// 'required_by_date' 						=>	 'required',
									//'purpose' 								=>	 'required',
									//'description' 							=>	 'required',
									
								),
								array
								(
									//"item_code.required"			=>	trans("The Item field is required."),
									//"quantity.required"				=>	trans("The Quantity field is required."),
									//"requirement_type.required"		=>	trans("The Requirement type field is required."),
									// "specific_vendor.required"		=>	trans("The Specific Vendor field is required."),
									// "department_name.required"		=>	trans("The Department Name field is required."),
									// "required_by_date.required"		=>	trans("The Required by date field is required."),
									//"purpose.required"				=>	trans("The Purpose of use field is required."),
									//"description.required"			=>	trans("The Description field is required."),
									
								)	
								
							);
			if ($validator->fails()) {	
				return Redirect::to('adminpnlx/purchase-requisition/add-purchase-requisition')->withErrors($validator)->withInput();
			}else{
				DB::beginTransaction();
				$purchaseReq 								= 	new PurchaseRequisition;
				$purchaseReq->purchase_requisition_number	=	'';
				$purchaseReq->created_by					=	Auth::user()->id;
				$purchaseReq->is_active   					= 	1;
				$purchaseReq->is_approved   				= 	0;
				$purchaseReq->created_at		    		=   date("Y-m-d H:i:s");
				$purchaseReq->save();
				DB::commit();
				$id  = $purchaseReq->id;
				if(!empty($id)){
					$unique_id		=	'#PR'.date('m').''.date('y').'000'.$id;
					PurchaseRequisition::where('id',$id)->update(array('purchase_requisition_number'=>$unique_id));
				}
				if(!empty($thisData['item'])){
					foreach($thisData['item'] as $key => $value) {
						if(!empty($value)){
							$purchaseReqItem 							= 	new PurchaseRequisitionItem;	
							$purchaseReqItem->purchase_requisitions_id	=	$id;
							$purchaseReqItem->item_code					=	$value["item_code"];
							$purchaseReqItem->quantity					= 	$value["quantity"];	
							$purchaseReqItem->requirement_type			= 	$value["requirement_type"];
							$purchaseReqItem->required_by_date			= 	$value["required_by_date"];
							$purchaseReqItem->purpose					= 	$value["purpose"];
							$purchaseReqItem->description				= 	$value["description"];	
							$purchaseReqItem->save();	
						}
					}
				}
				DB::commit();
				
				Session::flash('flash_notice', trans("Purchase Requisition has been added successfully")); 
				return Redirect::to('adminpnlx/purchase-requisition');
			}
		}
	}
	// end function purchaseRequisitionSave()
	 /**
	* Function for update purchaseRequisitionView    
	*
	* @param user_id,status
	*
	* @return view page. 
	*/
	public function purchaseRequisitionView($id = 0)
	{
		$is_allowed_view = $this->check_entry_allow_view(array('section'=>'purchase_reqisition','id'=>$id));
		if(empty($is_allowed_view)){
			return Redirect::back();
		}
		$Details = DB::table('purchase_requisitions_items')->select('purchase_requisitions_items.*',
				   DB::Raw("(SELECT purchase_requisition_number FROM purchase_requisitions WHERE purchase_requisitions.id=purchase_requisitions_items.purchase_requisitions_id) as purchase_requisition_number"),	
				   DB::Raw("(SELECT unique_id FROM items WHERE items.id=purchase_requisitions_items.item_code) as item_code"))
				   ->where('purchase_requisitions_id',$id)->get();
		
		$Data = json_decode($Details, true);

		if(empty($Details)) {
			return Redirect::back();
		}else{	
			return View::make('admin.PurchaseRequisition.view', compact("Data"));
		}
	}	

	/**
	* Function for dealer Edit
	*
	* @param userId
	*
	* @return view page. 
	*/
		public function purchaseRequisitionEdit($prId = 0){
			$is_allowed_view = $this->check_entry_allow_view(array('section'=>'purchase_reqisition','id'=>$prId));
			if(empty($is_allowed_view)){
				return Redirect::back();
			}
			$prDetails			=	PurchaseRequisition::find($prId); 
			if(empty($prDetails)) {
				return Redirect::back();
			}
			$items =   DB::table('items')->where('is_active',1)->where('is_approved',1)->pluck('unique_id','id');	
			$departments =   DB::table('departments')->where('is_active',1)->where('is_deleted',0)->pluck('title','id');	
			$itemDetails = DB::table("items")
						->leftjoin('dropdown_managers as color_table', 'items.color', '=', 'color_table.id')
						->leftjoin('dropdown_managers as size_table', 'items.size', '=', 'size_table.id')
						->where('items.id',$prDetails->item_code)
						->select('items.*', 'color_table.name as color_name','size_table.name as size_name')
						->first();
			$data	=	DB::table('purchase_requisitions_items')->where('purchase_requisitions_id',$prId)->get();
			$requirmentType	=   DB::table('dropdown_managers')->where('dropdown_type','requirmentType')->where('is_active',1)->pluck('name','id');	
			// echo "<pre>"; print_r($data); die;
			// $vendors 			=   DB::table('vendors')->whereIn("id",explode(',',$itemDetails->vendors))->where('is_active',1)->pluck('company_name','id');
			return View::make("admin.PurchaseRequisition.edit", compact("prDetails","items","departments","data","itemDetails","requirmentType"));
			
		}
		 // end function itemEdit()


	/**
	* Function for dealer Update
	*
	* @param userId
	*
	* @return view page. 
	*/

	public function purchaseRequisitionUpdate($prId){
		
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData						=	Input::all(); 
		// echo "<pre>";print_r($thisData);die;
		
		if(!empty($thisData)){
			
				$validator	 =	 Validator::make(
								$thisData,
								array(
									// 'item_code' 							=>	 'required',
									// 'quantity' 								=>	 'required|numeric',
									// 'requirement_type' 						=>	 'required',
									// 'specific_vendor' 						=>	 'required',
									// 'department_name' 						=>	 'required',
									// 'required_by_date' 						=>	 'required',
									// 'purpose' 								=>	 'required',
									// 'description' 							=>	 'required',
									
								),
								array
								(
									// "item_code.required"			=>	trans("The Item field is required."),
									// "quantity.required"				=>	trans("The Quantity field is required."),
									// "requirement_type.required"		=>	trans("The Requirement type field is required."),
									// "specific_vendor.required"		=>	trans("The Specific Vendor field is required."),
									// "department_name.required"		=>	trans("The Department Name field is required."),
									// "required_by_date.required"		=>	trans("The Required by date field is required."),
									// "purpose.required"				=>	trans("The Purpose of use field is required."),
									// "description.required"			=>	trans("The Description field is required."),
									
								)	
								
							);
				//print_r($thisData);die;
				if ($validator->fails()){
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
					DB::beginTransaction();
					
				$purchaseReq 						= 	PurchaseRequisition::find($prId);
				$purchaseReq->updated_at		    =   date("Y-m-d H:i:s");
				$purchaseReq->save();
				DB::commit();
				DB::table('purchase_requisitions_items')->where('purchase_requisitions_id',$prId)->delete();
				if(!empty($thisData['item'])){
					foreach($thisData['item'] as $key => $value) {
						if(!empty($value)){
							$purchaseReqItem 							= 	new PurchaseRequisitionItem;	
							$purchaseReqItem->purchase_requisitions_id	=	$prId;
							$purchaseReqItem->item_code					=	$value["item_code"];
							$purchaseReqItem->quantity					= 	$value["quantity"];	
							$purchaseReqItem->requirement_type			= 	$value["requirement_type"];
							$purchaseReqItem->required_by_date			= 	$value["required_by_date"];
							$purchaseReqItem->purpose					= 	$value["purpose"];
							$purchaseReqItem->description				= 	$value["description"];	
							$purchaseReqItem->save();	
						}
					}
				}
				// die;
				DB::commit();
				Session::flash('flash_notice', trans("Purchase Requisition has been updated successfully")); 
				return Redirect::to('adminpnlx/purchase-requisition');
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
	public function deletePurchaseRequisition($prId = 0){
		
		$prDetails	=	PurchaseRequisition::find($prId); 
		if(empty($prDetails)) {
			return Redirect::back();
		}
		if($prId){	
			$prModel					=	PurchaseRequisition::where('id',$prId)->delete();
			Session::flash('flash_notice',trans("Purchase Requisition deleted successfully")); 
		}
		return Redirect::back();
	} 
	// end deleteDealer()

	/**
	* Function for delete  dealer  
	*
	* @param user_id
	*
	* @return view page. 
	*/
	public function purchaseRequisitionDelete(){
		$id  = Input::get('id'); 
		PurchaseRequisitionItem::where('id', '=', $id)->delete();
	 }
	 // end deleteDealer()
	
	 /**
	* Function for update DealerStatus    
	*
	* @param user_id,status
	*
	* @return view page. 
	*/
	public function updatePurchaseRequisitionStatus($id = 0, $Status = 0){
		
		if($Status == 1){
			$statusMessage	=	trans("Purchase Requisition approved successfully");
		}else{
			$statusMessage	=	trans("Purchase Requisition rejected successfully");
		}
		DB::table("purchase_requisitions")->where('id',$id)->update(array('is_approved'=>$Status,'approved_by'=>Auth::user()->id));
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	}

	public function getItemDetails(){
		$id = Input::get('id');
		$itemDetails = DB::table("items")
						->leftjoin('dropdown_managers as color_table', 'items.color', '=', 'color_table.id')
						->leftjoin('dropdown_managers as size_table', 'items.size', '=', 'size_table.id')
						->where('items.id',$id)->where('is_approved',1)
						->select('items.*', 'color_table.name as color_name','size_table.name as size_name')
						->first();
		
		return View::make("admin.PurchaseRequisition.item-details", compact("itemDetails"));
	} 
	
	public function getVendor(){
		$html = '<option value="">Select Vendor</option>';
		$id = Input::get('id');
		$itemVendors = DB::table("items")
						->where('items.id',$id)->where('is_approved',1)
						->select('vendors')
						->first();
		if(!empty($itemVendors)){
			$vendors 			=   DB::table('vendors')->whereIn("id",explode(',',$itemVendors->vendors))->where('is_active',1)->pluck('company_name','id');
			
			//echo '<pre>'; print_r($vendors); die;
			foreach($vendors as $key=>$value){
				$html .= '<option value="'.$key.'">'.$value.'</option>';
			}
			
		}
		echo $html;					
	} 
	// end updateDealerStatus()

	
	
	  
}//end DealerManagementController

