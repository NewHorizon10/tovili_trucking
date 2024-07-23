<?php
namespace App\Http\Controllers\adminpnlx;
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
		$inputGet				=	Input::all();
		/* seacrching on the basis of username and email */ 
			if ((Input::all())) {
				$searchData			=	Input::all(); 
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
						if($fieldName == 'dealer_id'){
							$DB->where("dealer_location.dealer_id",'=',"$fieldValue");
						}elseif($fieldName == 'location_name'){
							$DB->where("users.full_name",'like','%'.$fieldValue.'%');							
						}else{
							$DB->where("dealer_location.$fieldName",'like','%'.$fieldValue.'%');
						}
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
				$assignedDealer		=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
				if(!empty($assignedDealer)){
					$result 			= 	$DB->whereIn('dealer_location.dealer_id', $assignedDealer);
				}
			}
			$result 				= 	$DB
										->where('dealer_location.is_deleted',0)
										->leftjoin('users', 'dealer_location.dealer_id', '=','users.id')
										->select('dealer_location.*', 'users.full_name as dealer_name')
										// ->where('dealer_id',Auth::user()->id)
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page")); 
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			// dealer name
			$dealer_list = $this->get_dealer_list();
			return  View::make('admin.'.$this->model.'.index', compact('dealer_list','result' ,'searchVariable','sortBy','order','query_string'));
		}
		
	

	/**
	* Function for add Location page
	*
	* @param null
	*
	* @return view page. 
	*/

	public function addLocation(){ 
		$dealer_list  			=	$this->get_dealer_list(); 
		return View::make('admin.'.$this->model.'.add', compact('dealer_list'));
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
					'dealer_id'	 			=>	 'required',
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$network 						= 	new DealerLocation;
				$network->dealer_id				=	Input::get('dealer_id');
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
				return Redirect::to('/adminpnlx/dealer-location-management');
				//return Redirect::back();
			}
		}
	}
	
	public function editLocation($id = ""){
		$locationDetails	=	DB::table('dealer_location')
								->where('dealer_location.id',$id)
								->leftjoin('users', 'dealer_location.dealer_id', '=','users.id')
								->select('dealer_location.*','users.full_name as dealer_id')
								->first();
		if(empty($locationDetails)) {
			return Redirect::back();
		}	
		return View::make('admin.'.$this->model.'.edit', compact("locationDetails"));
	
		
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
				$network->updated_at			=  	date("Y-m-d H:i:s");
				$network->save();
				Session::flash('flash_notice', trans("Dealer location has been updated successfully.")); 
				return Redirect::to('/adminpnlx/dealer-location-management');
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
