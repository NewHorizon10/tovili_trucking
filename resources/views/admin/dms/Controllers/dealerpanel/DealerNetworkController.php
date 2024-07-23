<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\DealerNetwork;
use App\Model\Enquiry;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* DealerNetworkController Controller
*
* Add your methods in the class below
*
* This file will render views\DealerNetworkController\dashboard
*/
	class DealerNetworkController extends BaseController {
		
		public $model	=	'DealerNetwork';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function listNetwork(){
		$DB 					= 	DealerNetwork::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
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
					$DB->where("dealer_network.".$fieldName,'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			$dealer_id				=	$this->get_dealer_id();
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB
										->where('dealer_network.is_deleted',0)
										->where('dealer_network.dealer_id',$dealer_id)
										->leftjoin('dealer_location', 'dealer_network.location_name', '=', 'dealer_location.id')
										->select('dealer_network.*', 'dealer_location.location_name as dealer_location_name','dealer_location.location_code as dealer_location_code',
										DB::raw("(SELECT name FROM states WHERE id = dealer_network.state) as state"),
										DB::raw("(SELECT full_name FROM users WHERE id = dealer_network.dealer_id) as dealer_name"),
										DB::raw("(SELECT name FROM cities WHERE id = dealer_network.city) as city"))
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));
			//echo'<pre>'; print_r($result); echo'</pre>'; die;
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("network_search_data",$inputGet);
			$dealerLocationName  	=	DB::table('dealer_location')
										->where('is_active',1)
										->where('is_deleted',0)
										->where('dealer_id',$dealer_id)
										->orderBy('location_name','ASC')
										->pluck('location_name','id')
										->toArray();

			$dealerLocationCode  	=	DB::table('dealer_location')
										->where('is_active',1)
										->where('is_deleted',0)
										->where('dealer_id',$dealer_id)
										->orderBy('location_code','ASC')
										->pluck('location_code','id')
										->toArray();
			$dealerName  			=	DB::table('users')
										->where('dealer_id',$dealer_id)
										->where('user_role_id',DEALER_ROLE_ID)
										->where('is_active',1)
										->where('is_deleted',0)
										->orderBy('full_name','ASC')
										->pluck('full_name','id')->toArray(); 

			return  View::make('dealerpanel.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string','dealerLocationCode','dealerLocationName','dealerName'));
		}
	

	public function viewNetwork($id=""){
		$dealer_id				=	$this->get_dealer_id();
		$networkDetails	    =	DB::table('dealer_network')
								->where('dealer_network.id',$id)
								->where('dealer_network.dealer_id',$dealer_id)
								->leftjoin('dealer_location', 'dealer_network.location_name', '=', 'dealer_location.id')
										->select('dealer_network.*', 'dealer_location.location_name as location_name','dealer_location.location_code as location_code',
										DB::raw("(SELECT name FROM states WHERE id = dealer_network.state) as state"),
										DB::raw("(SELECT full_name FROM users WHERE id = dealer_network.dealer_id) as dealer_name"),
										DB::raw("(SELECT name FROM cities WHERE id = dealer_network.city) as city"))
								->first();
		
		if(empty($networkDetails)) {
			return Redirect::back();
		}else{
			
			return View::make('dealerpanel.'.$this->model.'.view', compact("networkDetails"));
		}
	}

	
	
	public function exportDealerNetworkToExcel(){
		$searchData			=	Session::get('network_search_data');
		$DB 				= 	DealerNetwork::query();	
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
				if($fieldValue != ""){
					if($fieldName == 'booking_date_start' || $fieldName == 'booking_date_end'){
						if($fieldName == 'booking_date_start'){  
							$DB->where('users.booking_date','>=',$fieldValue);
						}
						if($fieldName == 'booking_date_end'){  
							$DB->where('users.booking_date','<=',$fieldValue);
						}
					}else{
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB
									->where('dealer_network.is_deleted',0)
									->where('dealer_network.dealer_id',$dealer_id)
									->leftjoin('dealer_location', 'dealer_network.location_name', '=', 'dealer_location.id')
									->select('dealer_network.*', 'dealer_location.location_name as dealer_location_name','dealer_location.location_code as dealer_location_code',
									DB::raw("(SELECT name FROM states WHERE id = dealer_network.state) as state"),DB::raw("(SELECT name FROM cities WHERE id = dealer_network.city) as city"))
									->orderBy($sortBy, $order)
									->get()->toArray();

	
								
			
		$thead = array();
		$genderArr = Config::get("gender_type_array");
		$thead[]		= array("Branch","Location-Name","Location-Code","Address","Zipcode","State","City","Registration Date","Contact-Person-Name","Mobile Number","Email-ID","Status");
		if(!empty($result)) {
			foreach($result as $record) {
				if($record['is_active']	==1){
					$status = "Activated";
				}else{
					$status = "Deactivated";
				}
				
				$branch_name					=	!empty($record['branch_name'])?$record['branch_name']:'';
				$dealer_location_name			=	!empty($record['dealer_location_name'])?$record['dealer_location_name']:'';
				$dealer_location_code			=	!empty($record['dealer_location_code'])?$record['dealer_location_code']:'';
				$address					=	!empty($record['address_1'])?$record['address_1'].' '.$record['address_2']:'';
				$zipcode					=	!empty($record['zipcode'])?$record['zipcode']:'';
				$state							=	!empty($record['state'])?$record['state']:'';
				$city							=	!empty($record['city'])?$record['city']:'';
				$registration_date				=	!empty($record['registration_date'])?date(Config::get("Reading.date_format") , strtotime($record['registration_date'])):'';
				$contact_person_name						=	!empty($record['contact_person_name'])?$record['contact_person_name']:'';
				$mobile_number						=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$email						=	!empty($record['email'])?$record['email']:'';
				$status						=	!empty($status)?$status:'';
				
				$thead[]		= array($branch_name,$dealer_location_name,$dealer_location_code,$address,$zipcode,$state,$city,$registration_date,$contact_person_name,$mobile_number,$email,$status);
			}
		}													
		return  View::make('dealerpanel.'.$this->model.'.export_excel', compact('thead'));
		
	}
		
	public function GetDealerCode(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData		=	 Input::all();					
		if($formData != ''){
			$location_id			=	Input::get("location_name"); 
			$location_code 			=	DB::table('dealer_location')
										->where('id',$location_id)
										->orderBy('is_active',1)
										->value('location_code');
				
			$response	=	array(
				'success' 	=>	'1',
				'location_code' 	=>	$location_code,
				'errors' 	=>	 trans("Location code added successfully.")
				); 			
			return Response::json($response); 
			die;
		}else{
			$response	=	array(
				'success' 	=>	'2',
				'errors' 	=>	 trans("There is an error.")
			); 
			return Response::json($response); 
			die;
		} 
	}
	
} //end dealerNetworkController()
