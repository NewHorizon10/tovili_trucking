<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Config;
use App\Models\Lookup;
use App\Models\Shipment;
use App\Models\User;
use App\Models\UserCompanyInformation;
use App\Models\TruckType;
use App\Models\TruckTypeDescription;
use Session;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Exports\ReportExport;


class ShipmentController extends Controller
{
    public $model      =   'shipments';
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        $this->request = $request;
    }

    public function index(Request $request){
        $language_id = getAppLocaleId();

        $DB				=	Shipment::query();
        $DB->leftjoin('users', 'shipments.customer_id' , 'users.id')
        ->leftjoin('shipment_driver_schedules','shipments.id','shipment_driver_schedules.shipment_id')
        ->select(
            'shipments.*',
            'users.name as user_name',
            'shipment_driver_schedules.shipment_status',
            DB::raw("
                IF(shipments.request_type = 0,
                    (SELECT name FROM truck_type_descriptions 
                        WHERE truck_type_descriptions.parent_id = shipments.shipment_type 
                            AND truck_type_descriptions.language_id = $language_id),
                    (SELECT name FROM truck_type_descriptions 
                    WHERE truck_type_descriptions.parent_id = shipments.request_type 
                        AND truck_type_descriptions.language_id = $language_id)) 
                    as request_types
            ")
        );
      
        $shipment = ['shipment', 'end', 'cancelled'];
        
        $DB->withCount('shipment_offers');

        $DB->with(['shipmentOffersCount' => function($query) {
	        $query->where(['status' => 'waiting']);
        }, 'shipmentPrice', 'shipmentOffersCount', 'approvedShipmentPrice', 'rejectedShipmentOffersCount']);

        $searchVariable	=	array(); 
        $inputGet		=	$request->input();
        if ($request->all()){
            $searchData	=	$request->input();
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
            if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('shipments.request_date', [date('Y-m-d H:i:s',strtotime($dateS." 00:00:00")), date('Y-m-d H:i:s',strtotime($dateE." 23:59:59"))]); 											
            }elseif(!empty($searchData['date_from'])){
                $dateS = $searchData['date_from'];
                $DB->where('shipments.request_date','>=' ,[date('Y-m-d H:i:s',strtotime($dateS." 00:00:00"))]); 
            }elseif(!empty($searchData['date_to'])){
                $dateE = $searchData['date_to'];
                $DB->where('shipments.request_date','<=' ,[date('Y-m-d H:i:s',strtotime($dateE." 23:59:59"))]); 						
            }

            $form_latitude = $request->current_lat;
            $form_longitude = $request->current_lng;
            if($request->city_name && $form_latitude != "" && $form_longitude != ""){
                $DB->where(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( shipments.company_latitude ) ) * cos( radians(  shipments.company_longitude  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( shipments.company_latitude ) ) ))"),"<=",15);
                $DB->orderBy(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( shipments.company_latitude ) ) * cos( radians(  shipments.company_longitude  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( shipments.company_latitude ) ) ))"),"ASC");
            }

            foreach ($searchData as $fieldName => $fieldValue) {

				if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("name", 'like', '%' . $fieldValue . '%');
                    }                    
                    if ($fieldName == "request_types") {

                        $DB->where(function ($query) use ($fieldValue, $language_id) {
                            $query->whereRaw("
                                    IF(shipments.request_type = 0,
                                        (SELECT parent_id FROM truck_type_descriptions 
                                            WHERE truck_type_descriptions.parent_id = shipments.shipment_type 
                                                AND truck_type_descriptions.language_id = ?),
                                        (SELECT parent_id FROM truck_type_descriptions 
                                        WHERE truck_type_descriptions.parent_id = shipments.request_type 
                                            AND truck_type_descriptions.language_id = ?)) like ? 
                                ",
                                [$language_id, $language_id, $fieldValue ]    
                            );
                        });
                        
                    }
                    if ($fieldName == "request_date") {
                        $DB->where("shipments.request_date", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "request_number") {
                        $DB->where("shipments.request_number", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "pickup_address") {
                        $DB->where("shipments.pickup_address", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "status") {

                        if($fieldValue == 'paid'){
                            $DB->where('payment_status', 'paid')->where('invoice_file', '!=', null)->where('status', 'end');
                        }
                        elseif($fieldValue == 'invoice'){
                            $DB->where('status', 'end')->where('invoice_file', '!=', null)->where('payment_status', 'unpaid');
                        }
                        elseif($fieldValue == 'cancelled'){
                            $DB->where('status', 'cancelled');
                        }
                        elseif($fieldValue == 'ended'){
                            $DB->where('status', 'end')->where('invoice_file', null)->where('payment_status', 'unpaid');;
                        }
                        elseif($fieldValue == 'active'){
                            $DB->where('status', 'shipment')->where('shipment_status', 'start');
                        }
                        elseif($fieldValue == 'not_start'){
                            $DB->where('status', 'shipment')->where('shipment_status', 'not_start');
                        }

                    }
				}
				$searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
			}
        }  
          
     
        $DB->where("users.is_deleted", 0);
        $DB->where("users.user_role_id", 2);
        $DB->whereIn('shipments.status', $shipment);


        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		
        $DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_shipment_request'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);


		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
        
        $resultcount = $results->count();
        $shipmentService    = Lookup::where('lookup_type',"shipment-service")->with(['lookupDiscription' => function($query) {
	        $query->where(['language_id' => getAppLocaleId()]);
        }])->get();

        $requesttypesids = TruckType::query()->pluck('id')->toArray();
        $requesttypes    = TruckTypeDescription::whereIn('parent_id', $requesttypesids)->where('language_id', getAppLocaleId())->get();
        return  View("admin.$this->model.index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string','shipmentService', 'requesttypes'));
           
    }

    
     public function export(Request $request)
    {

        

        $list[0] = array(
            trans('messages.request_number'),
            trans('messages.name'),
            trans('messages.request_type'),
            trans('messages.price'),
            trans('messages.request_date'),
            trans('messages.pickup_address'),
            trans('messages.offers'),
            trans('messages.admin_Created_On'),
            trans('messages.admin_common_Status'),
        );

        $customers_export = Session::get('export_data_shipment_request');
        

        foreach ($customers_export as $key => $excel_export) {

            if($excel_export->shipment_status && $excel_export->status != "cancelled"){
                if($excel_export->shipment_status == 'start'){
                    $status = trans('messages.active');
                }
                else{
                    $status = trans('messages.'.$excel_export->shipment_status);
                }
            }else{
                $status = trans('messages.'.$excel_export->status);
            }
            


             if($excel_export->shipmentOffersCount ?->count() == 0){

               if(isset($excel_export->approvedShipmentPrice->price)){

                 $price = number_format($excel_export->approvedShipmentPrice->price, 2);

               }elseif(isset($excel_export->rejectedShipmentOffersCount) && $excel_export->rejectedShipmentOffersCount->count() > 0){
                  $price = $excel_export->rejectedShipmentOffersCount->count() . ' offer';
               }else{
                $price = '--';
               }
               
            }elseif($excel_export->shipmentOffersCount ?->count() > 1){
                    $price = $excel_export->shipmentOffersCount ?->count() . 'offers'; 
            }elseif($excel_export->shipmentOffersCount ?->count() == 1){
                    $price = $excel_export->shipmentOffersCount ?->count() . 'offer'; 
            }else{
                    $price =   '--';
            }


            $list[] = array(

                $excel_export->request_number,
                $excel_export->user_name,
                $excel_export->request_types,
                $price,
                date(config("Reading.date_format"), strtotime($excel_export->request_date)),
                $excel_export->pickup_address,
                $excel_export->shipment_offers_count,
                date(config("Reading.date_format"), strtotime($excel_export->created_at)),
                $status,

            );
        }

        $collection = new Collection($list);
        return Excel::download(new ReportExport($collection), 'Shipment.xlsx');
    }

}