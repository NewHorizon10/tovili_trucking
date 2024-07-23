<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\DealerInventory;
use App\Model\AdvanceBooking;
use App\Model\DropDown;
use App\Model\EnquiryFollowUp;
use App\Model\Booking;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* EnquiryController Controller
*
* Add your methods in the class below
*
* This file will render views\EnquiryController\dashboard
*/
	class InventoryController extends BaseController {
		
		public $model	=	'enquiry';

	public function __construct() {
		View::share('modelName',$this->model);
	}
	/* Retail Report start*/	
	
	/* Search Chassis Report*/
	public function inventory_list(){

		//echo 123; die;
		$DB 					= 	DealerInventory::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
			if ((Input::get())) {
				$inputGet			=	Input::get();
				unset($inputGet['display']);
				unset($inputGet['_token']);
				if(isset($inputGet['order'])){
					unset($inputGet['order']);
				}
				if(isset($inputGet['sortBy'])){
					unset($inputGet['sortBy']);
				}
				if(isset($inputGet['page'])){
					unset($inputGet['page']);
				}
			
				foreach($inputGet as $fieldName => $fieldValue){
					if($fieldValue != ""){
						if($fieldName == 'status' && $fieldValue != ''){
							$DB->where("dealer_inventory.is_sold",$inputGet['status']);							
						}
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}

			}

													
				
			
			$dealer_id				=	$this->get_dealer_id();
			$DB->where("dealer_inventory.dealer_id",$dealer_id);
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'dealer_inventory.created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB->leftjoin('inventories', 'dealer_inventory.vehicle_id','=','inventories.id')
										->leftjoin('users as dealer', 'dealer_inventory.dealer_id','=','dealer.id')
										->leftjoin('dealer_location as location', 'dealer_inventory.dealer_id','=','location.dealer_id')
										->leftjoin('dropdown_managers as model', 'inventories.model_id','=','model.id')
										->leftjoin('dropdown_managers as color', 'inventories.color_id','=','color.id')
										->select('dealer_inventory.*','inventories.model_id','inventories.color_id','inventories.battery_voltage','inventories.vin_number','inventories.motor_number','inventories.chassis_number','inventories.battery_number','inventories.imei_number','model.name as model_name', 'color.name as model_color',
											'location.location_name as dealer_location', 'dealer.full_name  as dealer_name' )
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));

                
			foreach($result as &$booking){
				$booking->battery_details	=	DB::table('battery_details')
															->where('vehicle_id',$booking->vehicle_id)
															->pluck('battery_number')->toArray();
				$booking->battery_count		=	count($booking->battery_details);
			}	

			foreach($result as $customers){
				if($customers->customer_id != NULL){
			
					$customers->customer_data	=	DB::table('users')
					->where('id',$customers->customer_id)
					->select('full_name' ,'unique_id' , 'phone_number')->first(); 
					}else{
                 		$customers->customer_data = array();
					}
			   }

			   $total_vehicle   = DB::table('dealer_inventory')->where("dealer_inventory.dealer_id",$dealer_id)->count();
			   $total_sold_vehicle   = DB::table('dealer_inventory')->where("dealer_inventory.dealer_id",$dealer_id)->where("dealer_inventory.is_sold",1)->count();
			   $total_avalable_vehicle   = DB::table('dealer_inventory')->where("dealer_inventory.dealer_id",$dealer_id)->where("dealer_inventory.is_sold",0)->count();
			
		//	 echo "<pre>";print_r($result);die;
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			Session::put("inventory",Input::get());
			$result->appends(Input::all())->render();
		
		return  View::make('dealerpanel.Inventory.index', compact('result' ,'searchVariable','total_vehicle','total_sold_vehicle','total_avalable_vehicle','sortBy','order','query_string'));
	}

} //end AdvanceBookingReportController()
