<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\DealerLocation;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* DealerLocationController Controller
*
* Add your methods in the class below
*
* This file will render views\DealerLocationController\dashboard
*/
	class DealerLocationController extends BaseController {
		
		public $model	=	'DealerLocation';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function listLocation(){
		$DB 					= 	DealerLocation::query();
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
					if($fieldValue != ""){
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			$dealer_id				=	$this->get_dealer_id();
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB
										->where('is_deleted',0)
										->where('dealer_id',$dealer_id)
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page"));
			//echo'<pre>'; print_r($result); echo'</pre>'; die;
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			return  View::make('dealerpanel.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string'));
		}
		
	

	/**
	* Function for add Location page
	*
	* @param null
	*
	* @return view page. 
	*/

	public function addLocation(){
		return View::make('dealerpanel.'.$this->model.'.add');
	}
	
	/**
	* Function for save Location
	*
	* @param null
	*
	* @return view page. 
	*/
	public function saveLocation(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					
					'location_name' 		=>	 'required',
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				$dealer_id				=	$this->get_dealer_id();
				$network 						= 	new DealerLocation;
				$network->dealer_id				=	$dealer_id;
				$network->location_name			=	Input::get('location_name');
				$network->created_at			=  	date("Y-m-d H:i:s");
				$network->save();
				$locationId						=	$network->id;
				if($locationId != ''){
					$location_words				=	explode(' ',Input::get('location_name'));
					$location_code				=	'';
					foreach($location_words as &$word){
						$location_code			.=	ucfirst($word[0]);
					}
					$location_code				.=	$locationId;
					if($location_code != ''){
						DealerLocation::where('id', $locationId)->update(['location_code'=>$location_code]);
					}
				}
				Session::flash("success",trans("Dealer location added successfully."));
				return Redirect::to('/dealerpanel/dealer-location-management');
				//return Redirect::back();
			}
		}
	}
	
	public function editLocation($id = ""){
		$dealer_id				=	$this->get_dealer_id();
		$locationDetails	=	DB::table('dealer_location')
								->where('id',$id)
								->where("dealer_id",$dealer_id)
								->first();
								

		if(empty($locationDetails)) {
			return Redirect::back();
		}	
		return View::make('dealerpanel.'.$this->model.'.edit', compact("locationDetails"));
	
		
	} // end editUser()
	
	public function updateLocation($id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'location_name' 		=>	 'required',
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				
				$network						= 	DealerLocation::find($id);
				$network->location_name			=	Input::get('location_name');
				$network->location_code			=	!empty(Input::get('location_code')) ? Input::get('location_code') : '';
				$network->updated_at			=  date("Y-m-d H:i:s");
				$network->save();
				
				Session::flash('flash_notice', trans("Dealer location has been updated successfully.")); 
				return Redirect::to('/dealerpanel/dealer-location-management');
			}
		}
	}


	
	public function deleteLocation($id = ''){
		
		$locationDetails			=	DealerLocation::find($id); 
		if(empty($locationDetails)) {
			return Redirect::back();
		}
		if($id){	
			$userModel					=	DealerLocation::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans("Dealer location has been deleted successfully.")); 
		}
		return Redirect::back();
	}

	public function updateLocationStatus($id = 0, $Status = 0){
		if($Status == 0){
			$statusMessage	=	trans("Dealer location deactivated successfully.");
			$locationDetails		=	DealerLocation::find($id); 
		}else{
			$statusMessage	=	trans("Dealer location activated successfully.");
		}
		$this->_update_all_status("dealer_location",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} // end updateDealerNetworkstatus()
	
	
	
} //end dealerNetworkController()
