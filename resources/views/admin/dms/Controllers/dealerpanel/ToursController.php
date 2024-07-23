<?php
namespace App\Http\Controllers\dealerpanel;

use App\Http\Controllers\BaseController;
use App\Model\User;
use App\Model\Leads;
use App\Model\Tours;
use App\Model\ToursLocation;
use App\Model\TourAttachments;
use App\Model\UserLocation;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;

/**
* Tour Controller
*
* Add your methods in the class below
*
* This file will render views\ToursController\Index
*/
class ToursController extends BaseController {
  	public $model	=	'Tours';

  	public function __construct() {
		View::share('model',strtolower($this->model));
	}
	public function index(){
		$leadList=$this->getLeadList();
        $DB   = 	Tours::query();
        $searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
		if ((Input::get())) {
			$searchData			=	Input::get();
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
				   	    if($fieldName == 'name'){
							$DB->where("tours.name","LIKE","%".$fieldValue."%");
						}
						if($fieldName == 'lead_id'){
							$DB->where("leads.lead_num","LIKE","%".$fieldValue."%");
						}
				    }
				   $searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			
			
		}
		
		$dealer_id				=	$this->get_dealer_id();
         
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		if(Auth::user()->user_role_id == STAFF_USER_ROLE_ID){
			$assignedLeads = DB::table("leads")->where('sales_person_assigned',Auth::user()->id)->pluck('id');
			//echo '<pre>'; print_r($assignedLeads); die;
			$DB->whereIn('lead_id',$assignedLeads);
		}
		
		
		$result 				= 	$DB
									->where('tours.is_deleted',0)
									->where('tours.dealer_id',$dealer_id)
									->leftJoin('leads','leads.id','tours.lead_id')
									->select("tours.*","leads.lead_num as lead_number","leads.status as lead_status","leads.sales_person_assigned as sales_person_assigned",DB::raw("(select count(*) from user_locations where user_locations.tour_id=tours.id) as total_shared_locations"))
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));

		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
	    
	//echo '<pre>'; print_r($result); die;
		return view('dealerpanel.'.$this->model.'.index',compact('result' ,'searchVariable','sortBy','order','query_string'));
	}

	public function addTour(){
		$leadList=$this->getLeadList();
		$cityList		=	DB::table('cities')
						   ->distinct('name')
						   ->pluck('name','id')
						   ->toArray();
		return view('dealerpanel.'.$this->model.'.add',compact('leadList','cityList'));
	}

	/*
	 * Function of saveTour
	 */
	public function saveTour(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();

		//echo '<pre>'; print_r($formData); die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 					        =>	 'required',
					'expenses'					    =>	 'required|numeric',
					'lead_id'                      =>   'required',
					'start_time'                    =>   'required|date|before:end_time',
					'end_time'                      =>   'required|date|after:start_time',
					'tour_from'                     =>   'required',
					'tour_end'                      =>   'required',
					'billable_time'                 =>   'required',
				 )
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$dealer_id				=	$this->get_dealer_id();
				  $tours                   = New Tours;
				  $tours->name             = Input::get('name');
				  $tours->lead_id         = Input::get('lead_id');
				  $tours->description      = Input::get('description');
				  $tours->user_id          = Auth::user()->id;
				  $tours->dealer_id         = $dealer_id;
				  $tours->expenses         = Input::get('expenses');
				  $tours->exp_des          = Input::get('exp_des');
				  $tours->start_time       = Input::get('start_time');
				  $tours->end_time         = Input::get('end_time');
				  $tours->unbillable_hour  = Input::get('unbillable_hour');
				  $tours->unbillable_minute= Input::get('unbillable_minute');
                  $tours->billable_time    = Input::get('billable_time');
                  $tours->tour_from        = Input::get('tour_from');
                  $tours->tour_end        = Input::get('tour_end');
                  $tours->save();
				  $id  = $tours->id;

				 

				  if(!empty($id)){
				  	
				  	if(Input::hasFile('attachment')){
				  	 $i=0;
				     foreach (Input::file("attachment") as $file) {
				      $image = $file;
				      $newImageName ='-'.$i.'-tour-file';
				      $file_name  = $file->getClientOriginalName();
				      $file_size  = $this->fileSizeReadable(filesize($file));
                      $imageSrc  = $this->imageUpload($image,TOUR_IMAGE_ROOT_PATH,$newImageName);
                       if($imageSrc){

                          $tourAttachment             = new TourAttachments;
                          $tourAttachment->tour_id    = $id;
                          $tourAttachment->attachment = $imageSrc;
                          $tourAttachment->is_deleted = 0;
                          $tourAttachment->file_name  = $file_name;
                          $tourAttachment->file_size  = $file_size;
                          $tourAttachment->save();
                        }
                        $i++;
                       }
                       
					}
					if(!empty(input::get("location"))){
						$location_lists							=	input::get("location");
						foreach($location_lists as $location){
							if(!empty($location)){
								$obj1 								=  new ToursLocation;
								$obj1->tour_id 						=  $id;
								$obj1->start_location 				=  $location["start_location"];
								$obj1->end_location 				=  $location["end_location"];
								$obj1->save();
						
							}
						}
					}
				  	Session::flash("success",trans($this->model." has been added succesfully"));
				    return Redirect::to(route(strtolower($this->model).'.index'));
				  }else{
				  	Session::flash("error",trans("Something Went Wrong"));
				  	return Redirect::back();
				  }
			 }

		}else{
            Session::flash("error",trans("Something Went Wrong"));
		  	return Redirect::back();
		}

		
	}//End saveTour
     
     /*
	 * Function of edit Tour
	 */
	public function editTour($id){
		$dealer_id				=	$this->get_dealer_id();
	
      $data =Tours::where('id',$id)->where('tours.dealer_id',$dealer_id)->first();
	  if(empty($data)){
		 Session::flash("error",trans("Something Went Wrong"));
		 return Redirect::to(route(strtolower($this->model).'.index'));
	 }
	  $res =  $this->checkLeadAssign($data->lead_id);
		 if($res == false){
			 Session::flash("error",trans("Something Went Wrong"));
			  return Redirect::to(route(strtolower($this->model).'.index'));
		 }
      $leadList=$this->getLeadList();
	  $imageDetails 	=  TourAttachments::where('tour_id',$id)->select("attachment","id")->get();
	  $ToursLocationList 				= 	ToursLocation::where('tour_id',$id)->select("start_location","end_location","id")->get();

      return view('dealerpanel.'.$this->model.'.edit',compact('data','leadList','imageDetails','ToursLocationList'));
	} //end edit Tour
	 
	 /*
	 * Function of Update Tour
	 */
	public function updateTour($id){
        Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 					        =>	 'required',
					'expenses'					    =>	 'required|numeric',
					'lead_id'                      =>   'required',
					'start_time'                    =>   'required|date|before:end_time',
					'end_time'                      =>   'required|date|after:start_time',
					'tour_from'                     =>   'required',
					'tour_end'                      =>   'required',
					'billable_time'                 =>   'required',

				 )
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
                
                  $tours = Tours::find($id);
                  $tours->name             = Input::get('name');
				  $tours->lead_id         = Input::get('lead_id');
				  $tours->description      = Input::get('description');
				  $tours->expenses         = Input::get('expenses');
				  $tours->exp_des          = Input::get('exp_des');
				  $tours->start_time       = Input::get('start_time');
				  $tours->end_time         = Input::get('end_time');
				  $tours->unbillable_hour  = Input::get('unbillable_hour');
				  $tours->unbillable_minute= Input::get('unbillable_minute');
                  $tours->billable_time    = Input::get('billable_time');
                  $tours->tour_from        = Input::get('tour_from');
                  $tours->tour_end        = Input::get('tour_end');
                  $tours->save();
				if (Input::hasFile('attachment')) {
					$i                  = 1;
					$image              = TourAttachments::where('tour_id', $id)->select("attachment")->get();
					foreach ($image as $files){
						@unlink(TOUR_IMAGE_ROOT_PATH.$image);
						
					}
					$i=0;
					foreach (Input::file("attachment") as $file) {
						 $image = $file;
				         $newImageName ='-'.$i.'-lead-file';
				         $file_name  = $file->getClientOriginalName();
				         $file_size  = $this->fileSizeReadable(filesize($file));
                         $imageSrc  =  $this->imageUpload($image,TOUR_IMAGE_ROOT_PATH,$newImageName);
						  $tourAttachment             = new TourAttachments;
                          $tourAttachment->tour_id    = $id;
                          $tourAttachment->attachment = $imageSrc;
                          $tourAttachment->is_deleted = 0;
                          $tourAttachment->file_name  = $file_name;
                          $tourAttachment->file_size  = $file_size;
                          $tourAttachment->save();
                     $i++;
					}
					
					
				}
				if(!empty(input::get("location"))){
					$location_lists							=	input::get("location");
					ToursLocation::where('tour_id', $id)->delete();
					
					foreach($location_lists as $location){
						if(!empty($location)){
							$obj1 								=  new ToursLocation;
							$obj1->tour_id 						=  $id;
							$obj1->start_location 				=  $location["start_location"];
							$obj1->end_location 				=  $location["end_location"];
							$obj1->save();
					
						}
					}
				}
			      
			    Session::flash("success",trans($this->model." has been updated succesfully."));
			    return Redirect::route(strtolower($this->model).'.index');
			}
		}else{
            
            Session::flash("error",trans("Something Went Wrong"));
		  	return Redirect::back();
		}
	}//endUpdate 
  
   /**
	 * Function for Delete Files
	 * 
	 * 
	 * */

	public function deleteFile(){
		 $id = Input::get('id');
		 $image = TourAttachments::where('id', $id)->pluck('attachment');
         @unlink(TOUR_IMAGE_ROOT_PATH . $image);
		 DB::table('tour_attachments')->where('id', '=', $id)->delete();
		 die;
	}//end deleteFile

   /**
	 * Function for Delete Tour
	 * 
	 * 
	 * */

	public function deleteTour($id = ''){
		$toursDetails			=	Tours::find($id); 
		if(empty($toursDetails)){
		 Session::flash("error",trans("Something Went Wrong"));
		 return Redirect::to(route(strtolower($this->model).'.index'));
	 }
		$res =  $this->checkLeadAssign($toursDetails->lead_id);
		 if($res == false){
			 Session::flash("error",trans("Something Went Wrong"));
			  return Redirect::back();
		 }
		if($id){	
		  $Model =Tours::where('id',$id)->update(array('is_deleted'=>1));
		  ToursLocation::where('tour_id',$id)->delete();
          Session::flash('flash_notice',trans( $this->model." has deleted successfully."));
         }
		return Redirect::back();
	}//end deleteTour


	/**
	 * Function for View Tour
	 * 
	 * 
	 * */

	public function viewTour($id){
		$dealer_id				=	$this->get_dealer_id();
      $data =Tours::where('id',$id)->where('tours.dealer_id',$dealer_id)->first();
	  	if(empty($data)){
		 Session::flash("error",trans("Something Went Wrong"));
		 return Redirect::to(route(strtolower($this->model).'.index'));
	 }
	  $res =  $this->checkLeadAssign($data->lead_id);
		 if($res == false){
			 Session::flash("error",trans("Something Went Wrong"));
			  return Redirect::back();
		 }
		
        $leadList=$this->getLeadList();
        $data      =  Tours::where('id',$id)
                     ->where('is_deleted',0)
					 ->where('tours.dealer_id',$dealer_id)
					 ->select("tours.*",
					  DB::raw("(SELECT lead_num FROM leads WHERE id = tours.lead_id) as lead_number"),
					  DB::raw("(SELECT full_name FROM users WHERE id = tours.user_id) as userName"))
					 ->first();
		 $imageDetails 	=  TourAttachments::where('tour_id',$id)->select("attachment","id")->get();
		 $ToursLocationList 				= 	ToursLocation::where('tour_id',$id)->select("start_location","end_location","id")->get();
	   return view('dealerpanel.'.$this->model.'.view',compact('data','imageDetails','ToursLocationList'));
	}////end viewTour

	/**
	 * Function for export Tour
	 * 
	 * 
	 * */

	public function exportTour(){
		$leadList=$this->getLeadList();
        $DB   = 	Tours::query();
        $searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
		if ((Input::get())) {
			$searchData			=	Input::get();
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
         
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB
									->where('tours.is_deleted',0)
									->select("tours.*",
									DB::raw("(SELECT lead_num FROM leads WHERE id = tours.lead_id) as lead_number"))
									->orderBy($sortBy, $order)
									->get()->toArray();
									
	
		return view('dealerpanel.'.$this->model.'.export_tours',compact('result' ,'searchVariable','sortBy','order','query_string'));
	}

	public function addMoreLocation(){
		$counter		=	Input::get('counter');

		//print_r($counter); die;
		return view('dealerpanel.'.$this->model.'.add_more_location',compact("counter"));
	}
	
	public function deleteLoation(){
		$location_id		=	Input::get('location_id');
		ToursLocation::where('id',$location_id)->delete();
		die;
	}

	public function shareLoation(){
		$formData						=	Input::all();
		if(!empty($formData) && !empty($formData['lat']) && !empty($formData['long'])){
			$geolocation = $formData['lat'].','.$formData['long'];
			$key = Config::get("Site.googlemapkey");
			$request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=false&key='.$key; 
			$file_contents = file_get_contents($request);
			$json_decode = json_decode($file_contents);
			if($json_decode->status == 'OK'){
				$location = $json_decode->results[0]->formatted_address;
			}else{
				$location 					= '';
			}
			$obj 						=  new UserLocation;
			$obj->latitude 				=  Input::get('lat');
			$obj->longitude 			=  Input::get('long');
			$obj->location 				=  $location;
			$obj->user_id 				=   Auth::user()->id;
			$obj->tour_id 				=  Input::get('tour_id');
			$obj->save();
		}
		
	}
	
	public function listSharedLocation($id){
		 $dealer_id				=	$this->get_dealer_id();
		$tourData =Tours::where('id',$id)->where('tours.dealer_id',$dealer_id)->first();
		$res =  $this->checkLeadAssign($tourData->lead_id);
		 if($res == false){
			 Session::flash("error",trans("Something Went Wrong"));
			  return Redirect::back();
		 }
        $DB   = 	UserLocation::query();
        $searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
		if ((Input::get())) {
			$searchData			=	Input::get();
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
				   	    if($fieldName == 'location'){
							$DB->where("user_locations.location","LIKE","%".$fieldValue."%");
						}
						
				    }
				   $searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			
			
		}
        $dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'ASC';
		$result 				= 	$DB
									
									->leftJoin('tours','tours.id','user_locations.tour_id')
									->where('tours.dealer_id',$dealer_id)
									->select("user_locations.*")
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));

		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
	    
	//echo '<pre>'; print_r($result); die;
		return view('dealerpanel.'.$this->model.'.listSharedLocations',compact('result' ,'searchVariable','sortBy','order','query_string','tourData'));
	}
	public function checkLeadAssign($leadId=0){
		$res = true;
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedLeads = DB::table("leads")->where('id',$leadId)->where('sales_person_assigned',Auth::user()->id)->first();
			if(empty($assignedLeads)){
				$res = false;
			}
		}
		return $res;
		
	}
	 
}