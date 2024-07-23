<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\AdvanceBooking;
use App\Model\Booking;
use App\Model\Enquiry;
use App\Model\DropDown;
use App\Model\AdvanceBookingFollowUp;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* bookingController Controller
*
* Add your methods in the class below
*
* This file will render views\bookingController\dashboard
*/
class FollowUpRecordController extends BaseController {

    public $model	=	'FollowUpRecords';

	public function __construct() {
		View::share('modelName',$this->model);
	}
    public function listFollowUpRecords(){
       
        $EN 					= 	DB::table('enquiries')
                                            ->where('enquiries.status', '!=',ENQUIRY_CLOSE_STATUS)
                                            ->leftjoin('dropdown_managers as modal', 'modal.id', '=', 'enquiries.vehicle_modal')
                                            ->leftjoin('dropdown_managers as color', 'color.id', '=', 'enquiries.vehicle_color');
        $AB 					= 	DB::table('advance_booking')
                                            ->where('advance_booking.status', '!=',ADVANCE_BOOKING_CANCEL_STATUS)
                                            ->leftjoin('dropdown_managers as modal', 'modal.id', '=', 'advance_booking.vehicle_modal')
                                            ->leftjoin('dropdown_managers as color', 'color.id', '=', 'advance_booking.vehicle_color');
		$searchVariable			=	array(); 
        $searchData			    =	Input::all();
        $today                  =   date('Y-m-d');
        $dealer_id				=	$this->get_dealer_id();
        $rows_per_page	        =	(!empty(Input::get("records"))) ? Input::get("records") : Config::get("Reading.records_per_page");
        if(!empty(Input::get("follow_up_date")) && empty(Input::get("follow_end_date"))){
            $EN->where("enquiries.next_follow_up_date","=",$searchData['follow_up_date']);
            $AB->where("advance_booking.next_follow_up_date","=",$searchData['follow_up_date']);
            $searchVariable	        =	array_merge($searchVariable,array('follow_up_date' => $searchData['follow_up_date']));
        }elseif(!empty(Input::get("follow_up_date")) && !empty(Input::get("follow_end_date"))){
            $EN->whereBetween("enquiries.next_follow_up_date",array($searchData['follow_up_date'],$searchData['follow_end_date']));
            $AB->whereBetween("advance_booking.next_follow_up_date",array($searchData['follow_up_date'],$searchData['follow_end_date']));
            $searchVariable	        =	array_merge($searchVariable,array('follow_up_date' => $searchData['follow_up_date'],'follow_end_date' => $searchData['follow_end_date']));
        }elseif(empty(Input::get("follow_up_date")) && !empty(Input::get("follow_end_date"))){
            $EN->where("enquiries.next_follow_up_date","=",$searchData['follow_up_date']);
            $AB->where("advance_booking.next_follow_up_date","=",$searchData['follow_up_date']);
            $searchVariable	        =	array_merge($searchVariable,array('follow_end_date' => $searchData['follow_end_date']));
        }else{
            $EN->where('enquiries.next_follow_up_date', '=',$today);
            $AB->where('advance_booking.next_follow_up_date', '=',$today);
        }
        if(Input::get("sales_consultant") !=''){
            $sales_consultant   =   Input::get("sales_consultant");
            $EN->where('enquiries.sales_consultant', '=',$sales_consultant);
            $AB->where('advance_booking.sales_consultant', '=',$sales_consultant);
            $searchVariable	        =	array_merge($searchVariable,array('sales_consultant' => $searchData['sales_consultant']));
        }
        $sortBy  =  (Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order   =  (Input::get('order')) ? Input::get('order')   : 'DESC';
          
        if(!empty($searchData['record_type']) && ($searchData['record_type'] == 'enquiries' || $searchData['record_type'] == 'advance_booking')){  
            if($searchData['record_type'] == 'enquiries'){
                // query for Enquiry
                    $result                 =   $EN->where('enquiries.is_deleted', 0)
                                                    ->select('enquiries.id','enquiries.next_follow_up_date as contact_date', 'enquiries.customer_name', 'enquiries.mobile_number', 'enquiries.enquiry_number as unique_number', DB::raw("NULL as advance_booking_amount"), 'modal.name as vehicle_modal', 'color.name as vehicle_color', DB::raw("('enquiries') as table_name"), 'enquiries.created_at');
                    $total_data  			= 	$EN->where('enquiries.is_deleted', 0)->get();
                    $total_filtered_data	=	count($total_data);
            }elseif($searchData['record_type'] == 'advance_booking'){
                // query for Advance Booking
                $result                 =   $AB->where('advance_booking.is_deleted', 0)
                                                ->select('advance_booking.id','advance_booking.next_follow_up_date as contact_date',  'advance_booking.customer_name', 'advance_booking.mobile_number', 'advance_booking.booking_number as unique_number', 'advance_booking.advance_booking_amount', 'modal.name as vehicle_modal', 'color.name as vehicle_color', DB::raw("('advance_booking') as table_name"),'advance_booking.created_at');
                $total_data  			= 	$AB->where('advance_booking.is_deleted', 0)->get();
                $total_filtered_data	=	count($total_data);
            }
            $searchVariable	        =	array_merge($searchVariable,array('record_type' => $searchData['record_type']));
        }else{
            // query for both Enquiry and Advance Booking
            $enquiry    =   $EN->where('enquiries.is_deleted', 0)
                                ->select('enquiries.id','enquiries.next_follow_up_date as contact_date', 'enquiries.customer_name', 'enquiries.mobile_number', 'enquiries.enquiry_number as unique_number', DB::raw("NULL as advance_booking_amount"), 'modal.name as vehicle_modal', 'color.name as vehicle_color', DB::raw("('enquiries') as table_name"), 'enquiries.created_at');
            $advance    =   $AB->where('advance_booking.is_deleted', 0)
                                ->select('advance_booking.id','advance_booking.next_follow_up_date as contact_date', 'advance_booking.customer_name', 'advance_booking.mobile_number', 'advance_booking.booking_number as unique_number', 'advance_booking.advance_booking_amount', 'modal.name as vehicle_modal', 'color.name as vehicle_color', DB::raw("('advance_booking') as table_name"),'advance_booking.created_at');
            $result                 =   $enquiry->union($advance);
            $total_data  			= 	$enquiry->union($advance)->get();
		    $total_filtered_data	=	count($total_data);
        }
        $results  	            =   $result->orderBy($sortBy, $order)
                                            ->get()->toArray();  
        $page                   =   (Input::get('page')) ? Input::get('page') : 1;
		$paginate               =   $rows_per_page;
		$offSet                 =   ($page * $paginate) - $paginate;
		$itemsForCurrentPage    =   array_slice($results, $offSet, $paginate, true);
		$result = new \Illuminate\Pagination\LengthAwarePaginator($itemsForCurrentPage, count($results), $paginate, $page,['path' => Request::url(), 'query' => Request::query()]);
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
        $query_string			=	http_build_query($complete_string);  
        $sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
                                        ->where('dealer_id',$dealer_id)
                                        ->where("is_active",1)
                                        ->where("is_deleted",0)
                                        ->orderBy('full_name', 'ASC')
                                        ->pluck('full_name','id')
                                        ->toArray();
        return View::make('dealerpanel.FollowUp.index', compact("result","sortBy","order","total_filtered_data", "query_string", "searchVariable","sales_consultant"));
    }

    // function uses for get the city list on state change
    public function GetCitiesList(){
        $state_id   =   Input::get('state_id');
        $city_id   =   Input::get('city_id');
        $response   =   array();
        if($state_id != ''){
            $cityList   =   DB::table('cities')->where('state_id', $state_id)->pluck('name', 'id')->toArray();
            if($cityList){
                $dropdown   =   '<option value="">'.trans("Select Your City").'</option>';
                if(isset($cityList ) && !empty($cityList)){
                    foreach($cityList  as $key=>$value){
                        $dropdown .= '<option value="'.$key.'">'.$value.'</option>';
                    }
                }
                $response['success']    =   1;
                $response['data']       =   $dropdown;
                $response['city_id']    =   $city_id;
            }else{
                $response['success'] =   0;
                $response['data']    =   '';
                $response['city_id']    =   '';
            }
        }else{
            $response['success'] =   0;
            $response['data']    =   '';
            $response['city_id']    =   '';
        }
        return Response::json($response);
    }

}
