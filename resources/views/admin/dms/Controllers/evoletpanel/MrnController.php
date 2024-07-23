<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\Users;
use App\Model\DropDown;
use App\Model\Item;
use App\Model\MaterialReceivedNote;
use App\Model\MaterialReceivedNoteItem;	
use App\Model\StockHistory;	
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
	class MrnController extends BaseController {
	/**
	* Function for display adminpnlx dashboard
	*
	* @param null
	*
	* @return view page. 
	*/
	public function mrnList(){	
		
		$DB			=	MaterialReceivedNote::query();
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
										->select('material_received_note.*',
										DB::Raw("(SELECT order_number FROM purchase_order WHERE purchase_order.id=material_received_note.purchase_order_no) as order_number"),
										DB::Raw("(SELECT company_name FROM vendors WHERE vendors.id=material_received_note.vendor_name) as vendor_name"),
										DB::Raw("(SELECT full_name FROM users WHERE users.id=material_received_note.received_by) as received_by_name"),
										DB::Raw("(SELECT grn_no FROM good_received_note WHERE good_received_note.id=material_received_note.grn_no) as grn_no"))
										->orderBy($sortBy,$order)
										->paginate(Config::get("Reading.records_per_page"));
										
		$complete_string			=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		
		// echo '<pre>'; print_r($result); die;
		return View::make('admin.Mrn.index',compact('result','searchVariable','sortBy','order','query_string'));
	}
	//end function GNRList()

	/**
	* Function for GNR Add
	*
	* @param null
	*
	* @return view page. 
	*/
	public function mrnAdd(){
       // $date           =   date('d/m/Y');
		$vendors 			=   DB::table('vendors')->where('is_active',1)->pluck('company_name','id');
		$purchase_orders 	=   DB::table('purchase_order')->where('is_active',1)->pluck('order_number','id');	
		$grns 				=   DB::table('good_received_note')->pluck('grn_no','id');	
		return  View::make('admin.Mrn.add',compact('vendors','purchase_orders','grns'));
	}
	// end function GNRAdd()


	/**
	* Function for purchaseOrder Save
	*
	* @param null
	*
	* @return view page. 
	*/

	public function mrnSave(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData	     				=	Input::all();
		//echo "<pre>"; print_r($thisData); die;
		if(!empty($thisData)){
			
			$validator	 =	 Validator::make(
								$thisData,
								array(
									// 'purchase_order_no' 						=>	 'required',
									// 'challan_no' 								=>	 'required',
									// 'grn_no' 									=>	 'required',
									// 'quantity' 									=>	 'required|numeric',
								),
								array
								(
                                    // "purchase_order_no.required"	=>	trans("The Purchase Order Number field is required."),
									// "challan_no.required"			=>	trans("The Challen No. field is required."),
									// "quantity.required"				=>	trans("The Quantity field is required."),
									// "grn_no.required"				=>	trans("The GRN No. field is required."),
									)	
								
							);
			if ($validator->fails()) {	
				return Redirect::to('adminpnlx/material-receive-note/add-mrn')->withErrors($validator)->withInput();
			}else{
				$grns = DB::table("good_received_note")->where('good_received_note.id',Input::get('grn_no'))->first();
				$orderDetail = DB::table("purchase_order")->where('id',Input::get('purchase_order_no'))->first();
				

				DB::beginTransaction();
				$mrn 								= 	new MaterialReceivedNote;
				$mrn->grn_no   					    = 	Input::get('grn_no');
				$mrn->mrn_no   					    = 	'';
                $mrn->delivery_date   				= 	date("Y-m-d");
				$mrn->purchase_order_no   			= 	Input::get('purchase_order_no');
				$mrn->challen_no   					= 	$grns->challen_no;
				$mrn->received_by   				= 	Auth::user()->id;
				$mrn->save();
				DB::commit();
				$id  = $mrn->id;
				$unique_id = '';
				if(!empty($id)){
					$unique_id		=	'#MRN'.date('m').''.date('y').'000'.$id;
					MaterialReceivedNote::where('id',$id)->update(array('mrn_no'=>$unique_id));
					if (!empty($thisData['item'])) {
						foreach ($thisData['item'] as $key => $value) {
							$quality_params = '';
							if(in_array('quality_params',$value)){
								if(!empty($value['quality_params'])){
									$quality_params = serialize($value['quality_params']);
								}
							}
							$grnData = DB::table("good_received_note_items")->where('good_received_note_items.grn_id',Input::get('grn_no'))->where('good_received_note_items.item_code',$key)->first();
							$mrn 						= 	new MaterialReceivedNoteItem;
							$mrn->mrn_id    			=	$id;
							$mrn->quality_params 		= $quality_params;
							$mrn->quantity_passed 		= $value['quantity_pass'];
							$mrn->quantity_failed 		= $value['quantity_failed'];
							$mrn->save();	
							
							$ItemDetail 	   = DB::table("items")->where('id',$key)->first();
							$current          = intval($value['quantity_pass']) + intval($ItemDetail->stock);
							$old         	 =  intval($ItemDetail->stock);
							DB::table('items')->where('id',$key)->update(array('stock' => $current));		

							$stock = new StockHistory;
							$stock->purchase_order_no = Input::get('purchase_order_no');
							$stock->grn_no			  = $grns->id;
							$stock->mrn_no			  = $id;
							$stock->item_no			  = $key;
							$stock->last_stock		  = $old;
							$stock->current_stock	  = $current;
							$stock->order_quantity	  = ($grnData->quantity)?$grnData->quantity:0;
							$stock->received_quantity = ($grnData->received_quantity)?$grnData->received_quantity:0;
							$stock->quantity_passed	  = $value['quantity_pass'];
							$stock->quantity_failed	  = $value['quantity_failed'];
							$stock->save();
						}
					}
				}
			}


				Session::flash('flash_notice', trans("MRN has been added successfully")); 
				return Redirect::to('adminpnlx/material-receive-note');
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
		$Details	    =	DB::table('material_received_note')
								->where('material_received_note.id',$id)
								->first();

		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}else{	
			return View::make('admin.GRN.view', compact("Details"));
		}
	}
	public function getQualityParams(){
		$id = Input::get('id');
		$orderDetail = DB::table("purchase_order")->where('id',$id)->first();
		$itemDetails = '';
		if($orderDetail){
			$itemDetails = DB::table("items")
						->where('items.id',$orderDetail->item_code)
						->select('quality_params')
						->first();
		}
		
		return View::make("admin.Mrn.item-details", compact("itemDetails"));
	}
	
	public function getGrn(){
		$html = '<option value="">Select GRN</option>';
		$id = Input::get('id');
		$grns = DB::table("good_received_note")->where('good_received_note.purchase_order_no',$id)->pluck('grn_no','id');
		if(!empty($grns)){
			foreach($grns as $key=>$value){
				// $mrn 			=   DB::table('material_received_note')->where("grn_no",$key)->first();
				// if(empty($mrn)){
					$html .= '<option value="'.$key.'">'.$value.'</option>';
				// }
			}
		}	
		echo $html; 		
	}
	// public function getQuantity(){
	// 	$qunatity = 0;
	// 	$id = Input::get('id');
	// 	$grns = DB::table("good_received_note")->where('good_received_note.id',$id)->select("quantity")->first();
	// 	if(!empty($grns)){
	// 		$qunatity = $grns->quantity;
	// 	}	
	// 	echo $qunatity; 		
	// }

	public function getQuantity(){
		$id = Input::get('id');
		$challen = DB::table("good_received_note")->where('good_received_note.id',$id)->select("challen_no")->first();
		$data = DB::table("good_received_note_items")->where('good_received_note_items.grn_id',$id)
		->select('good_received_note_items.*',DB::Raw("(SELECT unique_id FROM items WHERE items.id=good_received_note_items.item_code) as unique_id"))->get();
		$grns_item = json_decode($data, true);
		foreach($grns_item as &$item){
			 $quality_params = DB::table("quality_params_questions")->where("item_id",$item['item_code'])->get();
			$quality_params = json_decode($quality_params, true);
			$item['quality_params'] = $quality_params;
		}
		//echo '<pre>'; print_r($grns_item); die;
		$orderDetail = DB::table("purchase_order")->where('id',$id)->first();
		$itemDetails = '';
		return View::make("admin.Mrn.item-details", compact("challen","grns_item","orderDetail"));
	}
	
	
	  
}//end PurchaseOrderManagementController

