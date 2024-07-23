<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Users;
use App\Model\DropDown;
use App\Model\PurchaseOrder;
use App\Model\Acl;
use App\Model\UserPermission;
use App\Model\UserPermissionAction;
use App\Model\AclAdminAction;
use App\Model\PurchaseOrderItem;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* PurchaseOrderController Controller
*
* Add your methods in the class below
*
* This file will render views\admin\dashboard
*/
	class PurchaseOrderController extends BaseController {
	/**
	* Function for display adminpnlx dashboard
	*
	* @param null
	*
	* @return view page. 
	*/
	public function purchaseOrderList(){	
		$is_allowed = $this->check_section_permission(array('section'=>'purchase_order'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$DB			=	PurchaseOrder::query();
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
		$DB->where('is_deleted',0);
		$sortBy 					= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'id';
		$order  					= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->designation != PURCHASE_HOD && Auth::user()->designation != ACCOUNTS_HOD ){
			$DB->where("created_by",Auth::user()->id);
		
		}elseif(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->designation == ACCOUNTS_HOD){
			$DB->where("is_approved",'!=',0);
		}
		$result 					= 	$DB
										->select('purchase_order.*',DB::Raw("(SELECT unique_id FROM items WHERE items.id=purchase_order.item_code) as item_code"),DB::Raw("(SELECT full_name FROM users WHERE users.id=purchase_order.created_by) as created_by_name"),DB::Raw("(SELECT purchase_requisition_number FROM purchase_requisitions WHERE purchase_requisitions.id=purchase_order.purchase_requisition_number) as purchase_requisition_number"))
										->orderBy($sortBy,$order)
										->paginate(Config::get("Reading.records_per_page"));
										
		$complete_string			=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		
		//echo '<pre>'; print_r($result); die;
		return View::make('admin.PurchaseOrder.index',compact('result','searchVariable','sortBy','order','query_string'));
	}
	//end function purchaseOrderList()

	/**
	* Function for purchaseOrder Add
	*
	* @param null
	*
	* @return view page. 
	*/
	public function purchaseOrderAdd($purchaserequisitionid = ""){
		// echo $purchaserequisitionid; die;
		$is_allowed = $this->check_section_permission(array('section'=>'purchase_order'));
		if(empty($is_allowed)){
			return Redirect::back();
		} 
		$items 					=   DB::table('items')->where('is_active',1)->where('is_approved',1)->pluck('unique_id','id');	
		$purchase_requisitions =   DB::table('purchase_requisitions')->where('is_approved',1)->where('is_active',1)->pluck('purchase_requisition_number','id');	
		return  View::make('admin.PurchaseOrder.add',compact('items','purchase_requisitions','purchaserequisitionid'));
	}
	// end function purchaseOrderAdd()


	/**
	* Function for purchaseOrder Save
	*
	* @param null
	*
	* @return view page. 
	*/

	public function purchaseOrderSave(){
		$is_allowed = $this->check_section_permission(array('section'=>'purchase_order'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		// echo "<pre>"; print_r($thisData); die;
		if(!empty($thisData)){
			
			$validator	 =	 Validator::make(
								$thisData,
								array(
									// 'item_code' 					=>	 'required',
									// 'cost' 							=>	 'required',
									// 'quantity' 						=>	 'required|numeric',
									// 'min_quantity' 					=>	 'required|numeric',
									// 'vendor' 						=>	 'required',
									// 'delivery_date' 				=>	 'required',
									// 'payment_desc' 					=>	 'required',
								),
								array
								(
									// "item_code.required"			=>	trans("The Item Code field is required."),
									// "cost.required"					=>	trans("The Cost field is required."),
									// "quantity.required"				=>	trans("The Quantity field is required."),									
									// "min_quantity.required"			=>	trans("The Minimum Quantity field is required."),
									// "vendor.required"				=>	trans("The Vendor field is required."),
									// "delivery_date.required"		=>	trans("The Delivery date field is required."),
									// "payment_desc.required"			=>	trans("The Description field is required."),
								)	
								
							);
			if ($validator->fails()) {	
				return Redirect::to('adminpnlx/purchase-order/add-purchase-order')->withErrors($validator)->withInput();
			}else{
				DB::beginTransaction();
				$purchaseReq 								= 	new PurchaseOrder;
				$purchaseReq->purchase_requisition_number	=	Input::get('purchase_requisition_number');
				$purchaseReq->vendor   						= 	Input::get('vendor');
				$purchaseReq->payment_desc   				= 	Input::get('payment_desc');
				$purchaseReq->delivery_date   				= 	Input::get('delivery_date');
				$purchaseReq->is_active   					= 	1;
				$purchaseReq->created_by    				= 	Auth::user()->id;
				$purchaseReq->save();
				DB::commit();
				$id  = $purchaseReq->id;
				if(!empty($id)){
					$unique_id		=	'#PO'.date('m').''.date('y').'000'.$id;
					PurchaseOrder::where('id',$id)->update(array('order_number'=>$unique_id));
				}
				if(!empty($thisData['item_data'])){
					foreach($thisData['item_data'] as $key => $value) {
						if(!empty($value)){
							$purchaseReqItem 							= 	new PurchaseOrderItem;	
							$purchaseReqItem->purchase_order_id			=	$id;
							$purchaseReqItem->item_code					=	$value["item_code"];
							$purchaseReqItem->quantity					= 	$value["quantity"];	
							$purchaseReqItem->quotation_number			= 	$value["quotation_number"];
							$purchaseReqItem->min_quantity				= 	$value["min_quantity"];
							$purchaseReqItem->cost						= 	$value["cost"];
							$purchaseReqItem->save();	
						}
					}
				}
				DB::commit();
				
				Session::flash('flash_notice', trans("Purchase Order has been added successfully")); 
				return Redirect::to('adminpnlx/purchase-order');
			}
		}
	}
	/**
	* Function for View  purchaseOrder 
	*
	* @param user_id
	*
	* @return view page. 
	*/
	public function purchaseOrderView($id = 0)
	{
		$is_allowed = $this->check_section_permission(array('section'=>'purchase_order'));
			if(empty($is_allowed)){
				return Redirect::back();
			}
		$DB			=	PurchaseOrder::query();
		$Details    =   $DB->select('purchase_order.*',
						DB::Raw("(SELECT full_name FROM users WHERE users.id=purchase_order.created_by) as created_by_name"),
						DB::Raw("(SELECT purchase_requisition_number FROM purchase_requisitions WHERE purchase_requisitions.id=purchase_order.purchase_requisition_number) as purchase_requisition_number"),
						DB::Raw("(SELECT company_name FROM vendors WHERE vendors.id=purchase_order.vendor) as vendor_name"))
						->where('purchase_order.id',$id)
						->first();
		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		$DB			=	PurchaseOrderItem::query();
		$Data = $DB->select('purchase_order_items.*',
				DB::Raw("(SELECT unique_id FROM items WHERE items.id=purchase_order_items.item_code) as item_code"))
				->where('purchase_order_id',$id)->get();
		$purchaseOrderItem = json_decode($Data, true);
		// echo'<pre>'; print_r($purchaseOrderItem); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}else{	
			return View::make('admin.PurchaseOrder.view', compact("Details","purchaseOrderItem"));
		}
    }
	
	
	// end function purchaseOrderSave()
		

	/**
	* Function for purchaseOrder Edit
	*
	* @param userId
	*
	* @return view page. 
	*/
		public function purchaseOrderEdit($id = 0){
			$is_allowed = $this->check_section_permission(array('section'=>'purchase_order'));
			if(empty($is_allowed)){
				return Redirect::back();
			}
			
			$details = DB::table('purchase_order')->where('id',$id)->first();
			
			if(empty($details)) {
				return Redirect::back();
			}
			$items 					=   DB::table('items')->where('is_active',1)->where('is_approved',1)->pluck('unique_id','id');	
			$itemDetails = DB::table("items")
						->where('items.id',$details->item_code)
						->first();

			// $vendors 			=   DB::table('vendors')->whereIn("id",explode(',',$itemDetails->vendors))->where('is_active',1)->pluck('company_name','id');
			$Data = DB::table('purchase_order_items')->where('purchase_order_id',$id)->get();
			$purchaseOrderItem = json_decode($Data, true);

			
			$vendors = array();
			$purchase_requisitions =   DB::table('purchase_requisitions')->where('is_active',1)->pluck('purchase_requisition_number','id');
			return View::make("admin.PurchaseOrder.edit", compact("details","items","vendors","purchase_requisitions","purchaseOrderItem"));
		}
		 // end function purchaseOrderEdit()


	/**
	* Function for purchaseOrder Update
	*
	* @param userId
	*
	* @return view page. 
	*/

	public function purchaseOrderUpdate($id){	
		$is_allowed = $this->check_section_permission(array('section'=>'purchase_order'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData						=	Input::all(); 
		// echo "<pre>";print_r($thisData);die;
		
		if(!empty($thisData)){
			
				$validator	 =	 Validator::make(
								$thisData,
								array(
									// 'item_code' 					=>	 'required',
									// 'cost' 							=>	 'required',
									// 'quantity' 						=>	 'required|numeric',
									// 'vendor' 						=>	 'required',
									// 'min_quantity' 					=>	 'required|numeric',
									// 'delivery_date' 				=>	 'required',
									// 'payment_desc' 					=>	 'required',
								),
								array
								(
									// "item_code.required"			=>	trans("The Item Code field is required."),
									// "cost.required"					=>	trans("The Cost field is required."),
									// "quantity.required"				=>	trans("The Quantity field is required."),
									// "vendor.required"				=>	trans("The Vendor field is required."),
									// "min_quantity.required"			=>	trans("The Minimum Quantity field is required."),
									// "delivery_date.required"		=>	trans("The Delivery date field is required."),
									// "payment_desc.required"			=>	trans("The Description field is required."),
								)	
								
							);
				//print_r($thisData);die;
				if ($validator->fails()){
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
					DB::beginTransaction();
					
				$purchaseReq 								= 	PurchaseOrder::find($id);
				$purchaseReq->purchase_requisition_number	=	Input::get('purchase_requisition_number');
				$purchaseReq->vendor   						= 	Input::get('vendor');
				$purchaseReq->payment_desc   				= 	Input::get('payment_desc');
				$purchaseReq->delivery_date   				= 	Input::get('delivery_date');
				$purchaseReq->save();
				DB::commit();
				DB::table('purchase_order_items')->where('purchase_order_id',$id)->delete();
				if(!empty($thisData['item_data'])){
					foreach($thisData['item_data'] as $key => $value) {
						if(!empty($value)){
							$purchaseReqItem 							= 	new PurchaseOrderItem;	
							$purchaseReqItem->purchase_order_id			=	$id;
							$purchaseReqItem->item_code					=	$value["item_code"];
							$purchaseReqItem->quantity					= 	$value["quantity"];	
							$purchaseReqItem->quotation_number			= 	$value["quotation_number"];
							$purchaseReqItem->min_quantity				= 	$value["min_quantity"];
							$purchaseReqItem->cost						= 	$value["cost"];
							$purchaseReqItem->save();	
						}
					}
				}
				// die;
				Session::flash('flash_notice', trans("Purchase Order has been updated successfully")); 
				return Redirect::to('adminpnlx/purchase-order');
			}
		}
	}
	// end function purchaseOrderUpdate()


	/**
	* Function for delete  purchaseOrder 
	*
	* @param user_id
	*
	* @return view page. 
	*/
	public function deletePurchaseOrder($id = 0){
		$is_allowed = $this->check_section_permission(array('section'=>'purchase_order'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		$Details	=	PurchaseOrder::find($id); 
		if(empty($Details)) {
			return Redirect::back();
		}
		if($id){	
			PurchaseOrder::where('id',$prId)->delete();
			Session::flash('flash_notice',trans("Purchase Order deleted successfully")); 
		}
		return Redirect::back();
	} 
	// end deletePurchaseOrder()

	/**
	* Function for delete  dealer  
	*
	* @param user_id
	*
	* @return view page. 
	*/
	public function deleteItem(){
		$id  = Input::get('id'); 
		PurchaseOrderItem::where('id', '=', $id)->delete();
	 }
	 // end deleteDealer()
	

	/**
	* Function for update purchaseOrderStatus    
	*
	* @param user_id,status
	*
	* @return view page. 
	*/
	public function updatePurchaseOrderStatus($id = 0, $Status = 0){
		$is_allowed = $this->check_section_permission(array('section'=>'purchase_order'));
		if(empty($is_allowed)){
			return Redirect::back();
		}
		
		if($Status == 1 || $Status == 3){
			$statusMessage	=	trans("Purchase Order approved successfully");
		}else{
			$statusMessage	=	trans("Purchase Order rejected successfully");
		}
		if($Status == 1){
			DB::table("purchase_order")->where('id',$id)->update(array('is_approved'=>$Status,'approved_by_purchase'=>Auth::user()->id));
		}
		if($Status == 3){
			DB::table("purchase_order")->where('id',$id)->update(array('is_approved'=>$Status,'approved_by_accounts'=>Auth::user()->id));
		}
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} 
	// end updatePurchaseOrderStatus()

	public function generateOrderPdf($orderid){
		
		$result			=	PurchaseOrder::where('id',$orderid)->first();
		
		$vendorDetail = DB::table("vendors")
								->select("vendors.*",DB::raw("(SELECT name FROM cities WHERE cities.id = vendors.city) as city_name"),DB::raw("(SELECT name FROM states WHERE states.id = vendors.state) as state_name"))	
								->where('id',$result->vendor)->first();
		$itemDetails = 	DB::table("items")->where('id',$result->item_code)->first();					
		// create file name and path
		$fileName			=	time().'-order-'.$orderid.'.pdf';
		$newCategory     	= 	strtoupper(date('M'). date('Y'))."/";
		$pdiPdfPath			=	PURCHASE_PDF_ROOT_PATH.$newCategory;
		if(!File::exists($pdiPdfPath)) {
			File::makeDirectory($pdiPdfPath, $mode = 0777,true);
		}
		$fullFileName		=	$newCategory.$fileName;
	//	echo '<pre>'; print_r($itemDetails); die;
		// file name in booking table
		if($pdiPdfPath != ''){
			PurchaseOrder::where('id',$orderid)->update(array('order_pdf'=> "$fullFileName"));
		}
		$DB			=	PurchaseOrderItem::query();
		$purchaseOrderItem = $DB->leftjoin('items', 'purchase_order_items.item_code', '=', 'items.id')
					->select('purchase_order_items.*','items.item_name as item_name','items.unique_id as unique_id','items.short_description as short_description')
				->where('purchase_order_id',$orderid)->get();
		//$purchaseOrderItem = json_decode($Data, true);
		return View::make('admin.PurchaseOrder.order_pdf', compact('result','pdiPdfPath','fileName','vendorDetail','itemDetails','purchaseOrderItem'));
	}
	
	public function addMoreItem(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData						=	Input::all(); 
		$count = $thisData['id'];
		$items 		 =   DB::table('items')->where('is_active',1)->where('is_approved',1)->pluck('unique_id','id');
		return View::make('admin.PurchaseOrder.add_more_item', compact('count','items'));
	}
	
	public function getPurchaseReq()
	{
		$id	= Input::get('id');
		$Details 	 = DB::table('purchase_requisitions_items')->select('purchase_requisitions_items.*',
				   	   DB::Raw("(SELECT purchase_requisition_number FROM purchase_requisitions WHERE purchase_requisitions.id=purchase_requisitions_items.purchase_requisitions_id) as purchase_requisition_number"))
				       ->where('purchase_requisitions_id',$id)->get();
		$itemDetails = json_decode($Details, true);
		$items 		 =   DB::table('items')->where('is_active',1)->where('is_approved',1)->pluck('unique_id','id');	
		// echo "<pre>"; print_r($items); die;
		return View::make("admin.PurchaseOrder.itemDetails", compact("itemDetails","items"));
		
		
	}
	  
}//end PurchaseOrderManagementController

