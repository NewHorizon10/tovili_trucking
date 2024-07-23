<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\Booking;
use App\Model\Enquiry;
use App\Model\UserMeta;
use App\Model\DropDown;
use App\Model\AdvanceBookingFollowUp;
use App\Model\BookingOtherCharge;
use App\Model\TaxManager;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* BookingController Controller
*
* Add your methods in the class below
*
* This file will render views\bookingController\dashboard
*/
class BasicAnalysisReportController extends BaseController {

    public function enquiryLevelPerformance(){
        $DB 					= 	Enquiry::query();
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
			if(!empty($searchData['booking_date_start']) && empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
				$DB->whereDate('booking_date',$date_from);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
			}elseif(empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_end']));
				$DB->whereDate('booking_date',$date_from);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
			}elseif(!empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
				$date_to	=	date('Y-m-d',strtotime($searchData['booking_date_end']));
				$DB->whereBetween('booking_date',[$date_from,$date_to]);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start'],'booking_date_end' => $searchData['booking_date_end']));
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != "" && $fieldName != "booking_date_start" && $fieldName != "booking_date_end"){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
        $result1 				= 	$DB->where("is_deleted",0)
                                    ->where('enquiry_type', '!=', '')
                                    ->where('dealer_id',$dealer_id);
        // all count                           
        $all_count              =   $result1->count();
        $result                 =   $DB->groupBy('enquiry_type')        
                                        ->select("enquiry_type",DB::raw("count(enquiry_type) as enquiry_type_count"))
                                        ->orderBy($sortBy, $order)
                                        // ->get()->toArray();
                                        ->paginate(Config::get("Reading.records_per_page"));                                 
		foreach($result as &$value){
            $value->ratio  =   $this->ratio($value->enquiry_type_count, $all_count);
        }
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("enquiry_level_performance_data",$inputGet);
		
		// $status_type 	=  $this->getDropDownListBySlug('advancebookingstatus');
        // $vehiclemodel 	=  $this->getDropDownListBySlug('vehiclemodel');
		$enquirymode 		=  $this->getDropDownListBySlug('enquirymode');
		$vehiclemodel =  $this->getDropDownListBySlug('vehiclemodel');
		$outletList 	=  $this->getDropDownListBySlug('outlet');
        
        // echo "<pre>";print_r($enquirymode);die;                                    		
		return  View::make('dealerpanel.BasicAnalysisReport.enquiry_level_performance', compact('result' ,'searchVariable','sortBy','order','query_string','enquirymode','vehiclemodel','outletList'));
    }

    public function followUpPerformance(){
        $DB 					= 	Enquiry::query();
        $DB1 					= 	Enquiry::query();
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
			if(!empty($searchData['booking_date_start']) && empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
				$DB->whereDate('booking_date',$date_from);
				$DB1->whereDate('booking_date',$date_from);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
			}elseif(empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_end']));
				$DB->whereDate('booking_date',$date_from);
				$DB1->whereDate('booking_date',$date_from);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
			}elseif(!empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
				$date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
				$date_to	=	date('Y-m-d',strtotime($searchData['booking_date_end']));
				$DB->whereBetween('booking_date',[$date_from,$date_to]);
				$DB1->whereBetween('booking_date',[$date_from,$date_to]);
				$searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start'],'booking_date_end' => $searchData['booking_date_end']));
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != "" && $fieldName != "booking_date_start" && $fieldName != "booking_date_end"){
                    $DB->where("$fieldName",'like','%'.$fieldValue.'%');
                    $DB1->where("$fieldName",'like','%'.$fieldValue.'%');
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
        $result_for_count 		= 	$DB->where("is_deleted",0)
                                    ->where('enquiry_type', '!=', '')
                                    ->where('dealer_id',$dealer_id);
        // all count                           
        $all_count              =   $result_for_count->count();
        $result                 =   $DB->groupBy('enquiry_type')        
                                        ->select("enquiry_type",DB::raw("count(enquiry_type) as enquiry_type_count"))
                                        ->orderBy($sortBy, $order)
                                        ->paginate(Config::get("Reading.records_per_page")); 
        // echo "<pre>";print_r($result);;                                    		

		foreach($result as &$value){
            $value->ratio =   $this->ratio($value->enquiry_type_count, $all_count);
            $DB1 			= 	Enquiry::query();
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
                if(!empty($searchData['booking_date_start']) && empty($searchData['booking_date_end'])){
                    $date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
                    $DB1->whereDate('booking_date',$date_from);
                    $searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
                }elseif(empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
                    $date_from	=	date("Y-m-d",strtotime($searchData['booking_date_end']));
                    $DB1->whereDate('booking_date',$date_from);
                    $searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start']));
                }elseif(!empty($searchData['booking_date_start']) && !empty($searchData['booking_date_end'])){
                    $date_from	=	date("Y-m-d",strtotime($searchData['booking_date_start']));
                    $date_to	=	date('Y-m-d',strtotime($searchData['booking_date_end']));
                    $DB1->whereBetween('booking_date',[$date_from,$date_to]);
                    $searchVariable	=	array_merge($searchVariable,array('booking_date_start' => $searchData['booking_date_start'],'booking_date_end' => $searchData['booking_date_end']));
                }
                foreach($searchData as $fieldName => $fieldValue){
                    if($fieldValue != "" && $fieldName != "booking_date_start" && $fieldName != "booking_date_end"){
                        $DB1->where("$fieldName",'like','%'.$fieldValue.'%');
                    }
                    $searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
                }
            }
            $value->customer_category = $DB1->where("is_deleted",0)
                                                    ->where('enquiry_type',$value->enquiry_type)
                                                    ->where('customer_category', '!=', '')
                                                    ->where('dealer_id',$dealer_id)
                                                    ->groupBy('customer_category')        
                                                    ->select("customer_category",DB::raw("count(customer_category) as customer_category_count"))
                                                    // ->orderBy($sortBy, $order)
                                                    ->get();
        }
        // echo "<pre>";print_r($result);die;                                    		

		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("enquiry_level_performance_data",$inputGet);
		
		// $status_type 	=  $this->getDropDownListBySlug('advancebookingstatus');
        // $vehiclemodel 	=  $this->getDropDownListBySlug('vehiclemodel');
		$enquirymode 		=  $this->getDropDownListBySlug('enquirymode');
        
        // echo "<pre>";print_r($enquirymode);die;                                    		
		return  View::make('dealerpanel.BasicAnalysisReport.followup_performance', compact('result' ,'searchVariable','sortBy','order','query_string','enquirymode'));
    }

    public function ratio($a, $b){
		$gcd = function($a, $b) use (&$gcd) {
		return ($a % $b) ? $gcd($b, $a % $b) : $b;
		};
		$g = $gcd($a, $b);
		return $a/$g . ':' . $b/$g;
	}
}