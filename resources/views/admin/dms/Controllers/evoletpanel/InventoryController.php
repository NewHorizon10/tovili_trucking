<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\DealerEnquiry;
use App\Model\DropDown;
use App\Model\Inventory;
use App\Model\BatteryDetail;
use App\Model\DealerInventory;
use App\Model\PdiCategories;
use App\Model\DeliveryInventoryPdi;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* DealerEnquiryController Controller
*
* Add your methods in the class below
*
* This file will render views\DealerEnquiryController\dashboard
*/
class InventoryController extends BaseController {
        

        /**
	* Function is used for list inventory
	*
	* @param null
	*
	* @return view page. 
	*/

	public function listInventory(){
		$model_name =   $this->getDropDownListBySlug('vehiclemodel');
		$color_name =   $this->getDropDownListBySlug('vehiclecolor');
		$DB						=	Inventory::query();
		$searchVariable			=	array(); 
		$inputGet					=	Input::get();
		$searchData				=	Input::get();
		/* seacrching on the basis of username and email */ 
		if ($searchData) {
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
				if($fieldValue != "" && $fieldName != 'status' && $fieldName != 'dealer_id'){
					$DB->where("$fieldName","like","%".$fieldValue."%");
				}
				if($fieldValue != "" && $fieldName == 'status'){
					$DB->where("is_sent_to_dealer","like","%".$fieldValue."%");
				}
				if($fieldValue != "" && $fieldName == 'dealer_id'){
					$DB->where("dealer_inventory.dealer_id","=",$fieldValue);
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'inventories.updated_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result					=	$DB->leftjoin('dropdown_managers as model', 'inventories.model_id','=','model.id')
										->leftjoin('dropdown_managers as color', 'inventories.color_id','=','color.id')
										->leftjoin('dealer_inventory', 'inventories.id','=','dealer_inventory.vehicle_id')
										->leftjoin('users', 'dealer_inventory.dealer_id','=','users.id')
										->select('inventories.*', 'model.name as model_name', 'color.name as color_name','dealer_inventory.dealer_id','dealer_inventory.created_at as send_date','users.full_name as dealer_name','dealer_inventory.is_sold as is_sold')
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));
										
		//echo '<pre>'; print_r($result); die;

        $complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
        $result->appends(Input::all())->render();
        
        $dealersList = $this->get_dealer_list();
        
		Session::put("inventory_search_data",$inputGet);
       // echo '<pre>'; print_r($dealersList); die;
        
        return View::make('admin.inventory.index',compact('result','searchVariable','sortBy','order','query_string','model_name', 'color_name','dealersList'));
    }

	public function exportInventoryToExcel(){
		$searchData					=	Session::get('inventory_search_data');
		$DB							=	Inventory::query();
		$searchVariable			=	array(); 
		if ($searchData) {
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
				if($fieldValue != "" && $fieldName != 'status' && $fieldName != 'dealer_id'){
					$DB->where("$fieldName","like","%".$fieldValue."%");
				}
				if($fieldValue != "" && $fieldName == 'status'){
					$DB->where("is_sent_to_dealer","like","%".$fieldValue."%");
				}
				if($fieldValue != "" && $fieldName == 'dealer_id'){
					$DB->where("dealer_inventory.dealer_id","=",$fieldValue);
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'inventories.updated_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result					=	$DB->leftjoin('dropdown_managers as model', 'inventories.model_id','=','model.id')
										->leftjoin('dropdown_managers as color', 'inventories.color_id','=','color.id')
										->leftjoin('dealer_inventory', 'inventories.id','=','dealer_inventory.vehicle_id')
										->leftjoin('users', 'dealer_inventory.dealer_id','=','users.id')
										->select('inventories.*', 'model.name as model_name', 'color.name as color_name','dealer_inventory.dealer_id','dealer_inventory.created_at as send_date','users.full_name as dealer_name')
										->orderBy($sortBy, $order)
										->get()->toArray();							
									
												
		$thead = array();
		$thead[]		= array("Model Name","Model Color","VIN Number","Motor Number","HSN/SAC","Chassis Number","IMEI Number","Battery Voltage","Added on","PDI Status","Send Status","Dealer Name","Send Date");
		if(!empty($result)) {
			foreach($result as $record) {
				$model_name					=	!empty($record['model_name'])?$record['model_name']:'';
				$color_name						=	!empty($record['color_name'])?$record['color_name']:'';
				$vin_number						=	!empty($record['vin_number'])?$record['vin_number']:'';
				$motor_number					=	!empty($record['motor_number'])?$record['motor_number']:'';
				$hsn_sac						=	!empty($record['hsn_sac'])?$record['hsn_sac']:'';
				$chassis_number					=	!empty($record['chassis_number'])?$record['chassis_number']:'';
				$imei_number					=	!empty($record['imei_number'])?$record['imei_number']:'';
				$battery_voltage				=	!empty($record['battery_voltage'])?$record['battery_voltage']:'';

				if(!empty($record['created_at'])){
						$date = date(Config::get("Reading.date_format") , strtotime($record['created_at']));
				}
				$added_on					=	$date;

				if($record['is_pdi_complete'] == 1){
					$pdi_status = "PDI Completed";
				}else{
					$pdi_status = "PDI Pending";
				}
				$pdi_status					=	$pdi_status;

				if($record['is_sent_to_dealer'] == 1){
					$sent_to_dealer = "Sent to dealer";
				}else{
					$sent_to_dealer = "In Stock";
				}
				$sent_to_dealer					=	$sent_to_dealer;

				if(!empty($record['send_date'])){
					$send_date = date(Config::get("Reading.date_format") , strtotime($record['send_date']));
				}
				if($record['is_sent_to_dealer'] == 1){
					$send_date= $send_date;
				}else{
					$send_date= '';
				}
				$dealer_name				=	!empty($record['dealer_name'])?$record['dealer_name']:'';

				$thead[]		= array($model_name,$color_name,$vin_number,$motor_number,$hsn_sac,$chassis_number,$imei_number,$battery_voltage,$added_on,$pdi_status,$sent_to_dealer,$dealer_name,$send_date);
			}
		}								
		//echo '<pre>'; print_r($thead); die;					
		return  View::make('admin.inventory.export_excel', compact('thead'));
		
	}

    /**
	* Function is used for add inventory
	*
	* @param null
	*
	* @return view page. 
	*/

	public function addInventory(){
        $model_name =   DB::table('dropdown_managers')->where('dropdown_type','vehiclemodel')->where('is_active',1)->pluck('name','id');
        $color_name =   DB::table('dropdown_managers')->where('dropdown_type','vehiclecolor')->where('is_active',1)->pluck('name','id');
        return View::make('admin.inventory.add',compact('model_name','color_name'));
    }

    /**
	* Function is used for save inventory
	*
	* @param null
	*
	* @return view page. 
	*/

	public function saveInventory(){
        Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'model_id' 					=>	 'required',
					'color_id' 					=>	 'required',
					'vin_number'				=>	 'required|unique:inventories',
					'motor_number'				=>	 'required|unique:inventories',
					'hsn_sac'					=>	 'required',
					'chassis_number'			=>	 'required|unique:inventories',
					// 'dealer_rate'				=>	 'required|integer',
					// 'discount'					=>	 'required|integer',
					// 'net_rate'					=>	 'required|integer',
					'battery_number'			=>	 'required|array|min:4',
					'imei_number'				=>	 'unique:inventories',
					'battery_voltage' 			=>	 'required',
					
				),
				array(
					'model_id.required' 				=>	 'Please select model name',
					'color_id.required' 				=>	 'Please select color name',
					'vin_number.required'				=>	 'The VIN number field is required.',
					'motor_number.required'				=>	 'The motor number field is required.',
					'hsn_sac.required'					=>	 'The HSN/SAC field is required.',
					'chassis_number.required'			=>	 'The chassis number field is required.',
					'dealer_rate.required'				=>	 'The dealer rate field is required.',
					'discount.required'					=>	 'The discount field is required.',
					'net_rate.required'					=>	 'The net rate field is required.',
					'battery_number.required'			=>	 'The battery number field is required.',
					'battery_number.min'				=>	 'Please input at least four battery number.',
					// 'imei_number.required'				=>	 'The IMEI number field is required.',
					'imei_number.unique'				=>	 'The IMEI number has already been taken.',
					'vin_number.unique'					=>	 'The VIN number has already been taken.',
					'motor_number.unique'				=>	 'The motor number has already been taken.',
				)
			);
			if ($validator->fails()){
				// echo "<pre>";print_r($validator);die;
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$inventory 						= 	new Inventory; 
				$inventory->model_id			=	$formData['model_id'];
				$inventory->color_id			=	$formData['color_id'];
				$inventory->vin_number			=	$formData['vin_number'];
				$inventory->motor_number		=	$formData['motor_number'];
				$inventory->hsn_sac				=	$formData['hsn_sac'];
				$inventory->chassis_number		=	$formData['chassis_number'];
				// $inventory->dealer_rate			=	$formData['dealer_rate'];
				// $inventory->discount			=	$formData['discount'];
				// $inventory->net_rate			=	$formData['net_rate'];
				$inventory->imei_number			=	$formData['imei_number'];
				$inventory->battery_voltage		=	$formData['battery_voltage'];
				$inventory->save();
				$id  = $inventory->id;
				if($id){
					foreach($formData['battery_number'] as &$battery){
						if($battery != ''){
							$obj					=	new BatteryDetail;
							$obj->vehicle_id		=	$id;
							$obj->battery_number	=	$battery;
							$obj->save();
						}
					}
				}
				Session::flash("success",trans("Inventory added successfully."));
				return Redirect::to('/adminpnlx/inventory');
				//return Redirect::back();
			}
		}
	}
	
	/**
	* Function is used for edit inventory
	*
	* @param null
	*
	* @return view page. 
	*/

	public function editInventory($id){
		$details			=	Inventory::where('id',$id)->where('is_sent_to_dealer', 0)->first(); 
		if(empty($details)) {
			return Redirect::back();
		}
		$batteryDetails		=	BatteryDetail::where('vehicle_id', $id)->pluck('battery_number')->toArray();
		$model_name =   $this->getDropDownListBySlug('vehiclemodel');
        $color_name =   $this->getDropDownListBySlug('vehiclecolor');
        return View::make('admin.inventory.edit',compact('details','model_name','color_name','batteryDetails'));
	}
	
	/**
	* Function is used for update inventory
	*
	* @param null
	*
	* @return view page. 
	*/

	public function updateInventory($id){
        Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'model_id' 					=>	 'required',
					'color_id' 					=>	 'required',
					'vin_number'				=>	 "required|unique:inventories,vin_number,$id",
					'motor_number'				=>	 "required|unique:inventories,motor_number,$id",
					'hsn_sac'					=>	 "required",
					'chassis_number'			=>	 "required|unique:inventories,chassis_number,$id",
					// 'dealer_rate'				=>	 'required|integer',
					// 'discount'					=>	 'required|integer',
					// 'net_rate'					=>	 'required|integer',
					'battery_number'			=>	 'required|array|min:1',
					'imei_number'				=>	 "unique:inventories,imei_number,$id",
					'battery_voltage' 			=>	 'required',
				),
				array(
					'model_id.required' 				=>	 'Please select model name',
					'color_id.required' 				=>	 'Please select color name',
					'vin_number.required'				=>	 'The VIN number field is required.',
					'motor_number.required'				=>	 'The motor number field is required.',
					'hsn_sac.required'					=>	 'The HSN/SAC field is required.',
					'chassis_number.required'			=>	 'The chassis number field is required.',
					// 'dealer_rate.required'				=>	 'The dealer rate field is required.',
					// 'discount.required'					=>	 'The discount field is required.',
					// 'net_rate.required'					=>	 'The net rate field is required.',
					'battery_number.required'			=>	 'The battery number field is required.',
					'battery_number.min'				=>	 'Please input at least four battery number.',
					// 'imei_number.required'				=>	 'The IMEI number field is required.',
					'imei_number.unique'				=>	 'The IMEI number has already been taken.',
					'vin_number.unique'					=>	 'The VIN number has already been taken.',
					'motor_number.unique'				=>	 'The motor number has already been taken.',
				)
			);
			if ($validator->fails()){
				// echo "<pre>";print_r($validator);die;
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$inventory 						= 	Inventory::find($id); 
				$inventory->model_id			=	$formData['model_id'];
				$inventory->color_id			=	$formData['color_id'];
				$inventory->vin_number			=	$formData['vin_number'];
				$inventory->motor_number		=	$formData['motor_number'];
				$inventory->hsn_sac				=	$formData['hsn_sac'];
				$inventory->chassis_number		=	$formData['chassis_number'];
				// $inventory->dealer_rate			=	$formData['dealer_rate'];
				// $inventory->discount			=	$formData['discount'];
				// $inventory->net_rate			=	$formData['net_rate'];
				$inventory->imei_number			=	$formData['imei_number'];
				$inventory->battery_voltage		=	$formData['battery_voltage'];
				$inventory->save();
				if(count($formData['battery_number']) > 0 && is_array($formData['battery_number'])){
					BatteryDetail::where('vehicle_id', $id)->delete();
					foreach($formData['battery_number'] as &$battery){
							if($battery != ''){
								$obj					=	new BatteryDetail;
								$obj->vehicle_id		=	$id;
								$obj->battery_number	=	$battery;
								$obj->save();
							}
					}
				}
				Session::flash("success",trans("Inventory updated successfully."));
				return Redirect::to('/adminpnlx/inventory');
				//return Redirect::back();
			}
		}
	}


	/**
	* Function is used to list vehicle 
	*
	* @param null
	*
	* @return view page. 
	*/

	public function sentToDealer(){
		$model_name =   $this->getDropDownListBySlug('vehiclemodel');
		$color_name =   $this->getDropDownListBySlug('vehiclecolor');
		$dealer_list =   $this->get_dealer_list();
		
		$DB						=	Inventory::query();
		$searchVariable			=	array(); 
		$searchData				=	Input::get();
		/* seacrching on the basis of username and email */ 
		if ($searchData) {
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
				if($fieldValue != ""){
					$DB->where("$fieldName","like","%".$fieldValue."%");
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'inventories.created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result					=	$DB->where('is_sent_to_dealer', 0)
										->leftjoin('dropdown_managers as model', 'inventories.model_id','=','model.id')
										->leftjoin('dropdown_managers as color', 'inventories.color_id','=','color.id')
										->select('inventories.*', 'model.name as model_name', 'color.name as color_name')
										//->where('inventories.is_pdi_complete',1)
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));

        $complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
        $result->appends(Input::all())->render();
        return View::make('admin.inventory.sent_to_dealer',compact('result','searchVariable','sortBy','order','query_string','model_name', 'color_name', 'dealer_list'));
	}

	/**
	* Function is used to view vehicle info
	*
	* @param null
	*
	* @return view page. 
	*/

	public function viewVehicle($id){
		if($id == ''){
			return Redirect::back();
		}
		$vehicleInfo		=	Inventory::where('inventories.id', $id)
											->leftjoin('dropdown_managers as model', 'inventories.model_id','=','model.id')
											->leftjoin('dropdown_managers as color', 'inventories.color_id','=','color.id')
											->select('inventories.*', 'model.name as model_name', 'color.name as color_name')
											->first();
											// echo "<pre>";print_r($vehicleInfo);die;
		$batteryDetails		=	BatteryDetail::where('vehicle_id', $id)->pluck('battery_number')->toArray();
		if(empty($vehicleInfo)){
			Session::flash("error",trans("Vehicles not found."));
			return Redirect::back();
		}
		return View::make('admin.inventory.view',compact('vehicleInfo','batteryDetails'));
	}


	/**
	* Function is used to save vehicle data assign to dealer
	*
	* @param null
	*
	* @return view page. 
	*/

	public function assignToDealer(){
		$dealer_id			=	Input::get('dealer_id');
		$vehicle_ids		=	Input::get('vehicle_ids');
		$previous_url	=  redirect()->back()->getTargetUrl();
		$response			=	array();
		if($dealer_id != '' && is_array($vehicle_ids) && count($vehicle_ids) > 0){
			foreach($vehicle_ids as $vehicle_id){
				$obj				=	new DealerInventory;
				$obj->vehicle_id	=	$vehicle_id;
				$obj->dealer_id		=	$dealer_id;
				$obj->is_sold		=	0;
				$obj->save();
				if($obj->id){
					Inventory::where('id', $vehicle_id)->update(array('is_sent_to_dealer'=> 1));
				}
			}
			$response['success']=1;
			$response['url']=$previous_url;
			Session::flash("success",trans("Vehicles have been sent to dealer."));
			return Response::json($response);
		}else{
			$response['success']=0;
			$response['url']=$previous_url;
			Session::flash("error",trans("Please select at least one vehicle."));
			return Response::json($response);
		}
	}
	
	public function pdiform($inventoryid){	
		$DeliveryInventoryPdiData = array();
		$result			=	PdiCategories::with('subcategories')->where('parent_id',0)->groupBy('id')->get()->toArray();
		if(empty($result)) {
			return Redirect::back();
		}
		$DeliveryInventoryPdi			=	DeliveryInventoryPdi::where('dealer_inventory_id',$inventoryid)->get()->toArray();

		if(!empty($DeliveryInventoryPdi)) {
			foreach($DeliveryInventoryPdi as $DeliveryInventory){
				$DeliveryInventoryPdiData[$DeliveryInventory['pdi_category_id']][$DeliveryInventory['pdi_sub_category_id']]	=	$DeliveryInventory['answer'];
			}
		}

		$vehicleInfo		=	Inventory::where('inventories.id', $inventoryid)
											->leftjoin('dropdown_managers as model', 'inventories.model_id','=','model.id')
											->leftjoin('dropdown_managers as color', 'inventories.color_id','=','color.id')
											->select('inventories.*', 'model.name as model_name', 'color.name as color_name')
											->first();
	//echo '<pre>'; print_r($vehicleInfo); die;

		return View::make('admin.inventory.pdiform',compact('inventoryid','result','DeliveryInventoryPdis','DeliveryInventoryPdiData','vehicleInfo'));
	}
   
	public function savepdiform($inventoryid){
		$pdi_category_id_count = 0;
		$pdi_category_yes_count = 0;
		Input::replace($this->arrayStripTags(Input::all()));
		$formData		=	Input::all();
		if(!empty($formData)){
			$data = Input::get("data");
			if(!empty($data)){
				foreach($data as $key => $value){
					$pdi_category_id_count  = $pdi_category_id_count + count($value);
					
					if(!empty($value)){
						foreach($value as $res){
							if(isset($res['answer']) && $res['answer'] == 'yes'){
								$pdi_category_yes_count++;
							}
							$checkAlreadyExist	=	DB::table("dealer_inventory_pdi")
													->where("dealer_inventory_id",$inventoryid)
													->where("pdi_category_id",$key)
													->where("pdi_sub_category_id",$res['sub_cat_id'])
													->first();
							if(!empty($checkAlreadyExist)){
								$obj             =   DeliveryInventoryPdi::find($checkAlreadyExist->id);

							}else{
								$obj             =   new DeliveryInventoryPdi();
							}
							
							$obj->dealer_inventory_id	=	$inventoryid;
							$obj->pdi_category_id		=	$key;
							$obj->pdi_sub_category_id	=	$res['sub_cat_id'];
							$obj->answer				=	isset($res['answer']) ? $res['answer'] : '';
							$obj->save();
						}
					}
				}
				
				if(!empty( $pdi_category_id_count) && !empty( $pdi_category_yes_count)){
					if($pdi_category_id_count == $pdi_category_yes_count){
						Inventory::where('id', $inventoryid)->update(array('is_pdi_complete'=> '1','pdi_invoice_name'=> ""));
						
					}else{
						Inventory::where('id', $inventoryid)->update(array('is_pdi_complete'=> '0','pdi_invoice_name'=> ""));
					}
				}
			}
		}	
		return Redirect::to('/adminpnlx/inventory');
	}
		
	
   
	public function generatePdiPdf($inventoryid){
		
		$result			=	PdiCategories::with('subcategories')->where('parent_id',0)->groupBy('id')->get()->toArray();

		//echo '<pre>'; print_r($result); die;
		if(empty($result)) {
			return Redirect::back();
		}
		$DeliveryInventoryPdi			=	DeliveryInventoryPdi::where('dealer_inventory_id',$inventoryid)->get()->toArray();

		if(!empty($DeliveryInventoryPdi)) {
			foreach($DeliveryInventoryPdi as $DeliveryInventory){
				$DeliveryInventoryPdiData[$DeliveryInventory['pdi_category_id']][$DeliveryInventory['pdi_sub_category_id']]	=	$DeliveryInventory['answer'];
			}
		}
		// create file name and path
		$fileName			=	time().'-pdi-'.$inventoryid.'.pdf';
		$newCategory     	= 	strtoupper(date('M'). date('Y'))."/";
		$pdiPdfPath			=	PDI_PDF_ROOT_PATH.$newCategory;
		if(!File::exists($pdiPdfPath)) {
			File::makeDirectory($pdiPdfPath, $mode = 0777,true);
		}
		$fullFileName		=	$newCategory.$fileName;
		// file name in booking table
		if($pdiPdfPath != ''){
			Inventory::where('id', $inventoryid)->update(array('pdi_invoice_name'=> "$fullFileName"));
		}

		$vehicleInfo		=	Inventory::where('inventories.id', $inventoryid)
											->leftjoin('dropdown_managers as model', 'inventories.model_id','=','model.id')
											->leftjoin('dropdown_managers as color', 'inventories.color_id','=','color.id')
											->select('inventories.*', 'model.name as model_name', 'color.name as color_name')
											->first();
	//echo '<pre>'; print_r($vehicleInfo); die;

		return View::make('admin.inventory.generate_pdi_pdf', compact('inventoryid','result','DeliveryInventoryPdis','DeliveryInventoryPdiData','pdiPdfPath','fileName','vehicleInfo'));
	}
	
	/*
	 * Function of show add leads page
	 */
	public function importInventory(){
		$dataArray = array();
      return view('admin.inventory.importInventory',compact('dataArray'));
	}
	public function saveImportInventory(){
		$formData				=	Input::all(); 
		$validator 					=	Validator::make(
			Input::all(),
			array(
				'imported_file' 	=> 'required',
			)
		);	
		if ($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
			
			$extension 				=	Input::file('imported_file')->getClientOriginalExtension();
			if($extension != 'csv'){
				Session::flash('error',trans("Invalid filetype. Please upload text/csv document only."));
				return Redirect::back();
			}
			
			$fileName = $_FILES["imported_file"]["tmp_name"];
			if ($_FILES["imported_file"]["size"] > 0) {
				$file = fopen($fileName, "r");
				$column = fgetcsv($file, 10000, ",");
				
				$dataArray = array();
				$emails = array();
				$error = 0;
				$exitsError = 0;
				
				while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
					if(count($column) > 1){
						$subarray  = array();
						$subarray['model_name']  			= '';
						$subarray['model_name_error']  			= '';
						$subarray['model_orignel_name']  			= '';
						
						$subarray['model_color']  			= '';
						$subarray['model_color_error']  			= '';
						$subarray['model_orignal_color']  			= '';
						$subarray['motor_number_error']  			= '';
						$subarray['chassis_number_error']  			= '';
						$subarray['vin_number']  			= '';
						$subarray['motor_number']  			= '';
						$subarray['hsn_sac']  				= '';
						$subarray['chassis_number']  		= '';
						$subarray['imei_number']  			= '';
						$subarray['battery_voltage']  		= '';
						$subarray['added_on']  				= '';
						$subarray['status']  				= '';
						$subarray['exiest_id']  			= '';
						
						
						$num = 0;
						for($num =0;$num < 10; $num++){
							$column[$num] = trim($column[$num]);
							
							if($num == 0){
								if(!empty($column[$num])){
									$model_id =   DB::table('dropdown_managers')->where('dropdown_type','vehiclemodel')->where('is_active',1)->where("name","LIKE","%".$column[$num]."%")->select('id')->first();
									if(!empty($model_id)){
										$subarray['model_name'] = $model_id->id;
										$subarray['model_orignel_name'] = $column[$num];
										$subarray['model_name_error'] = '';
									}else{
										$error = 1;
										$subarray['model_name'] = '';
										$subarray['model_name_error'] = 'Wrong model name entered. Please change and retry.';
									}
								}else{
									$error = 1;
									$subarray['model_name'] = '';
									$subarray['model_name_error'] = 'Please enter model name.';
								}	
							}
							if($num == 1){
								if(!empty($column[$num])){
									$color_id =   DB::table('dropdown_managers')->where('dropdown_type','vehiclecolor')->where('is_active',1)->where("name","LIKE","%".$column[$num]."%")->select('id')->first();
									if(!empty($color_id)){
										$subarray['model_color'] 			= $color_id->id;
										$subarray['model_orignal_color'] 			= $column[$num];
										$subarray['model_color_error']    = '';
									}else{
										$error = 1;
										$subarray['model_color'] 			= '';
										$subarray['model_color_error'] = 'Wrong model color entered. Please change and retry.';
									}
									
								}else{
									$error = 1;
									$subarray['model_color'] 			= '';
									$subarray['model_color_error']    = 'Please enter model color';
								}	
							}
							if($num == 2){	
								$subarray['vin_number'] = $column[$num];
							}
							if($num == 3){
								if(!empty($column[$num])){
									$checkMoterNumber = DB::table('inventories')->where('motor_number',$column[$num])->select('id')->first();
									if(!empty($checkMoterNumber)){
										$exitsError = 1;
										$subarray['exiest_id'] = $checkMoterNumber->id;
									}
									$subarray['motor_number'] = $column[$num];
								}else{
									$subarray['motor_number_error'] 			= 'Motor number already exists.';
								}
							}
							if($num == 4){	
								$subarray['hsn_sac'] = $column[$num];
							}
							if($num == 5){	
								if(!empty($column[$num])){
									$checkChassisNumber = DB::table('inventories')->where('chassis_number',$column[$num])->select('id')->first();
									if(!empty($checkChassisNumber)){
										$exitsError = 1;
										$subarray['exiest_id'] = $checkChassisNumber->id;
									}
									$subarray['chassis_number'] = $column[$num];
								}else{
									$subarray['chassis_number_error'] 			= 'Chassis number already exists.';
								}
							}
							if($num == 6){	
								$subarray['imei_number'] = $column[$num];
							}
							if($num == 7){	
								$subarray['battery_voltage'] = $column[$num];
							}
							if($num == 8){	
								$subarray['added_on'] = $column[$num];
							}
							if($num == 9){	
								$subarray['status'] = $column[$num];
							}
						}
						
						$dataArray[] = $subarray;
					}
				}
				//echo '<pre>'; print_r($dataArray); die;
				if($error == 1){
					Session::flash('error',trans("There is some errors in csv file. Please check and fix."));
					return  View::make('admin.inventory.importInventory',compact('dataArray'));
				}else{
					if(!empty($dataArray)){
						foreach($dataArray as $data ){
							if(!empty($data['exiest_id'])){
								$inventory 						=  Inventory::find($data['exiest_id']);
							}else{
								$inventory 						= 	new Inventory;
							}
							 
							$inventory->model_id			=	(!empty($data['model_name'])) ? $data['model_name'] : "";
							$inventory->color_id			=	(!empty($data['model_color'])) ? $data['model_color'] : "";
							$inventory->vin_number			=	(!empty($data['vin_number'])) ? $data['vin_number'] : "";
							$inventory->motor_number		=	(!empty($data['motor_number'])) ? $data['motor_number'] : "";
							$inventory->hsn_sac				=	(!empty($data['hsn_sac'])) ? $data['hsn_sac'] : "";
							$inventory->chassis_number		=	(!empty($data['chassis_number'])) ? $data['chassis_number'] : "";
							$inventory->imei_number			=	(!empty($data['imei_number'])) ? $data['imei_number'] : "";
							$inventory->battery_voltage		=	(!empty($data['battery_voltage'])) ? $data['battery_voltage'] : "";
							//$inventory->created_at		=	(!empty($data['added_on'])) ? $data['added_on'] : "";
							$inventory->save();
							/* Create site based on customer data end */
						}
					}
					Session::flash('flash_notice', trans("Inventory imported successfully."));
					return Redirect::to('adminpnlx/inventory');
				}
			}
		}
		
	}
	/**
	* Function for update DealerStatus    
	*
	* @param user_id,status
	*
	* @return view page. 
	*/
	public function returnToStock($id = 0){
		$statusMessage	=	trans("Item returned successfully");
		
		Inventory::where('id', $id)->update(array('is_sent_to_dealer'=> 0));
		DealerInventory::where('vehicle_id', $id)->delete();
				
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	}
}
