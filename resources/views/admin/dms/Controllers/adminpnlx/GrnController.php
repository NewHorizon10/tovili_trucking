<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Users;
use App\Model\DropDown;
use App\Model\GoodReceivedNote;
use App\Model\GoodReceivedNoteItem;
use App\Model\Acl;
use App\Model\UserPermission;
use App\Model\UserPermissionAction;
use App\Model\AclAdminAction;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* GrnController Controller
*
* Add your methods in the class below
*
* This file will render views\admin\dashboard
*/
	class GrnController extends BaseController {
	/**
	* Function for display adminpnlx dashboard
	*
	* @param null
	*
	* @return view page. 
	*/
	public function grnList(){	
		
		$DB			=	GoodReceivedNote::query();
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
		// $DB->where('is_deleted',0);
		$sortBy 					= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'id';
		$order  					= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		
		$result 					= 	$DB
										->select('good_received_note.*',DB::Raw("(SELECT order_number FROM purchase_order WHERE purchase_order.id=good_received_note.purchase_order_no) as order_number"),DB::Raw("(SELECT company_name FROM vendors WHERE vendors.id=good_received_note.vendor_name) as vendor_name"),DB::Raw("(SELECT name FROM warehouses WHERE warehouses.id=good_received_note.warehouse) as warehouse_name"),DB::Raw("(SELECT full_name FROM users WHERE users.id=good_received_note.received_by) as received_by_name"))
										->orderBy($sortBy,$order)
										->paginate(Config::get("Reading.records_per_page"));
										
		$complete_string			=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		
		//echo '<pre>'; print_r($result); die;
		return View::make('admin.Grn.index',compact('result','searchVariable','sortBy','order','query_string'));
	}
	//end function GNRList()

	/**
	* Function for GNR Add
	*
	* @param null
	*
	* @return view page. 
	*/
	public function grnAdd(){
       // $date           =   date('d/m/Y');
		$vendors 		=   DB::table('vendors')->where('is_active',1)->pluck('company_name','id');
		$warehouses 		=   DB::table('warehouses')->where('is_active',1)->pluck('name','id');
		$purchase_orders =   DB::table('purchase_order')->where('is_active',1)->pluck('order_number','id');	
		return  View::make('admin.Grn.add',compact('vendors','purchase_orders','warehouses'));
	}
	// end function GNRAdd()


	/**
	* Function for purchaseOrder Save
	*
	* @param null
	*
	* @return view page. 
	*/

	public function grnSave(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		// echo "<pre>"; print_r($thisData); die;
		// $purchase_order_no			=	(!empty(Input::get('purchase_order_no'))) ? Input::get('purchase_order_no') : 0;
		if(!empty($thisData)){
			// Validator::extend('check_qty_validation', function($attribute, $value, $parameters) {
			// 	$purchase_order_no			=	(!empty($parameters[0])) ? $parameters[0] : 0;
				
			// 	$purchase_order_detials	=	DB::table("purchase_order")->where("id",$purchase_order_no)->value("quantity");
			// 	$material_received_note	=	DB::table("material_received_note")->where("purchase_order_no",$purchase_order_no)
			// 								->value(DB::raw("SUM(quantity_pass)"));
				
			// 	if(($material_received_note+$value) > $purchase_order_detials){
			// 		return false;
			// 	}else{
			// 		return true;
			// 	}
			// });
			$validator	 =	 Validator::make(
								$thisData,
								array(
									// 'purchase_order_no' 					=>	 'required',
									// 'vendor_name' 							=>	 'required',
									// 'challan_no' 							=>	 'required',
									// 'warehouse' 							=>	 'required',
									// 'quantity' 								=>	 "required|numeric|check_qty_validation:$purchase_order_no",
								),
								array
								(
                                    // "purchase_order_no.required"	=>	trans("The Purchase Order Number field is required."),
									// "vendor_name.required"			=>	trans("The Vendor Name field is required."),
									// "challan_no.required"			=>	trans("The Challen No. field is required."),
									// "warehouse.required"			=>	trans("The Warehouse field is required."),
									// "quantity.required"				=>	trans("The quantity field is required."),
									// "quantity.check_qty_validation"				=>	trans("The quantity must be equal PO or less then."),
									)	
								
							);
			if ($validator->fails()) {	
				return Redirect::to('adminpnlx/goods-receive-note/add-grn')->withErrors($validator)->withInput();
			}else{
				DB::beginTransaction();
				$gnr 								= 	new GoodReceivedNote;
                $gnr->grn_no   					    = 	'';
                $gnr->delivery_date   				= 	date("Y-m-d");
				$gnr->purchase_order_no   			= 	Input::get('purchase_order_no');
				$gnr->challen_no   					= 	Input::get('challan_no');
				$gnr->received_by   				= 	Auth::user()->id;
				$gnr->save();
				DB::commit();
				$id  = $gnr->id;
				$purchase_order_no = $gnr->purchase_order_no;
				if(!empty($id)){
					$unique_id		=	'#GRN'.date('m').''.date('y').'000'.$id;
					GoodReceivedNote::where('id',$id)->update(array('grn_no'=>$unique_id));
				}
				$orderDetails = DB::table("purchase_order_items")
						->select('purchase_order_items.*',
						DB::Raw("(SELECT item_name FROM items WHERE items.id=purchase_order_items.item_code) as item_name"),
						DB::Raw("(SELECT id FROM items WHERE items.id=purchase_order_items.item_code) as item_code"),
						DB::Raw("(SELECT vendors FROM items WHERE items.id=purchase_order_items.item_code) as vendors"))				
						->where('purchase_order_items.purchase_order_id',$purchase_order_no)->get();
				$itemDetails = json_decode($orderDetails, true);		
						// echo "<pre>"; print_r($itemDetails); die;
				$goods_item = [];	
						//echo '<pre>'; print_r($thisData['item_data']); die;
						if(!empty($thisData['item_data'])){
							foreach($thisData['item_data'] as $key => $value) {
								if(!empty($value)){
									$orderDetails = DB::table("purchase_order_items")
													->select('purchase_order_items.*',
													DB::Raw("(SELECT item_name FROM items WHERE items.id=purchase_order_items.item_code) as item_name"),
													DB::Raw("(SELECT vendors FROM items WHERE items.id=purchase_order_items.item_code) as vendors"))				
													->where('purchase_order_items.purchase_order_id',$purchase_order_no)->where('purchase_order_items.item_code',$key)->first();
									$grn 							= 	new GoodReceivedNoteItem;	
									$grn->grn_id					=	$id;
									$grn->item_name					=	$orderDetails->item_name;
									$grn->item_code					=	$key;
									$grn->quantity					= 	$orderDetails->quantity;	
									$grn->vendors					= 	$orderDetails->vendors;
									$grn->warehouse					= 	'';
									$grn->received_quantity 		=   $value['received_quantity'];
									$grn->save();
								}
							}
						}
						// print_r($goods_item); die;
						DB::commit();
						// if(!empty($thisData['received_quantity'])){
						// 	foreach($thisData['received_quantity'] as $key => $value) {
						// 		if(!empty($value)){
						// 			$grn->received_quantity =   $value;
						// 			$grn->save();
						// 		}
						// 	}
						// }
						DB::commit();


				Session::flash('flash_notice', trans("GRN has been added successfully")); 
				return Redirect::to('adminpnlx/goods-receive-note');
			}
		}
	}
	// end function purchaseOrderSave()
		

	

    /**
	* Function for view 
	*
	* @param null
	*
	* @return view page. 
	*/

	public function view($id=""){
		$Details	    =	DB::table('good_received_note')
								->where('good_received_note.id',$id)
								->first();
		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}else{	
			return View::make('admin.Grn.view', compact("Details"));
		}
	}
	
	/**
	* Function for view 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function getOrderDetail(){
		Input::replace($this->arrayStripTags(Input::all()));
		$id = Input::get('id');
		$orderDetails = DB::table("purchase_order_items")
						->select('purchase_order_items.*',
						DB::Raw("(SELECT item_name FROM items WHERE items.id=purchase_order_items.item_code) as item_name"),
						DB::Raw("(SELECT unique_id FROM items WHERE items.id=purchase_order_items.item_code) as unique_id"),
						DB::Raw("(SELECT vendors FROM items WHERE items.id=purchase_order_items.item_code) as vendors"))	
						->where('purchase_order_items.purchase_order_id',$id)->get();

	    $itemVendors = 	DB::table("purchase_order_items")
					->select(DB::Raw("(SELECT vendors FROM items WHERE items.id=purchase_order_items.item_code) as vendors"))
					->where('purchase_order_items.purchase_order_id',$id)->get();
		$itemDetails = json_decode($orderDetails, true);
		$Vendors 	 = json_decode($itemVendors, true);	
		$vendors = [];	
		$vendorName = [];	

		$received = DB::table("material_received_note")
						->where('material_received_note.purchase_order_no',$id)
						 ->sum('material_received_note.quantity_pass');
		// $remaining = $itemDetails->quantity-$received;
		return View::make("admin.Grn.order-details", compact("itemDetails","received","vendorName"));
	}

	

	
	
	  
}//end PurchaseOrderManagementController

