<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Config;
use App\Models\Lookup;
use App\Models\LookupDiscription;
use App\Models\TruckTypeDescription;
use App\Models\ShipmentOfferRequestRejected;
use App\Models\User;
use App\Models\Truck;
use App\Models\UserCompanyInformation;
use App\Models\ShipmentDriverSchedule;
use App\Models\Shipment;
use App\Models\ShipmentAttchement;
use App\Models\ShipmentStop;
use App\Models\ShipmentStopAttchement;
use App\Models\ShipmentOffer;
use App\Models\ShipmentPrivateCustomerExtraInformation;    
use Carbon\Carbon;
use App\Models\EmailAction;
use App\Models\TruckType;
use App\Models\EmailTemplate;
use App\Models\RatingReview;
use Redirect,Session,Auth;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Exports\ReportExport;


class ShipmentRequestController extends Controller
{
    public $model      =   'shipment-request';
    public $sectionNameSingular      =   'shipment-request';
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
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

        $DB->whereIn('status', ['new', 'offers', 'offer_chosen']);
        $DB->withCount('shipment_offers');
        $DB->with(['shipmentOffersCount' => function($query) {
	        $query->where(['status' => 'waiting']);
        }, 'shipmentPrice','approvedShipmentPrice','rejectedShipmentOffersCount', 'AllselectedShipmentOffers']);
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
                                        (SELECT truck_type_descriptions.parent_id FROM truck_type_descriptions 
                                            WHERE truck_type_descriptions.parent_id = shipments.shipment_type 
                                                AND truck_type_descriptions.language_id = ?
                                        ),
                                        (SELECT truck_type_descriptions.parent_id FROM truck_type_descriptions 
                                        WHERE truck_type_descriptions.parent_id = shipments.request_type 
                                            AND truck_type_descriptions.language_id = ?)
                                    ) = ?
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
                        if (strpos($fieldValue, ',') !== false) {
                            $fieldValue = explode(',', $fieldValue);
                            $DB->whereIn("shipments.status", $fieldValue);
                        }else if($fieldValue == 'active'){
                            $DB->where('shipment_driver_schedules.shipment_status','start');
                        }else if($fieldValue == 'not_start'){
                            $DB->where('shipment_driver_schedules.shipment_status','not_start');
                        }else if($fieldValue == 'rejected'){
                            $DB->whereHas('rejectedShipmentOffersCount', function($query) use($fieldValue){
                                $query->where('status', $fieldValue);
                            });
                        }else{
                            $DB->where("shipments.status", $fieldValue);
                            if($fieldValue == "shipment" ){
                                $DB->whereNull('shipment_status');
                            }
                        }
                    }
				}
				$searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
			}
        }

        $DB->where("users.is_deleted", 0);
        $DB->where("users.user_role_id", 2);
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

        $offerCountsArray = array();

        foreach($results as $result) {
            $selectedofferCount = 0;
            $rejectedOfferCount = 0;
            $waitingOfferCount = 0;

            foreach($result->AllselectedShipmentOffers as $offers){
                if($offers->shipment_id == $result->id) {
                    if($offers->status == 'selected') {
                        $selectedofferCount++;
                    } elseif($offers->status == 'rejected') {
                        $rejectedOfferCount++;
                    }elseif($offers->status == 'waiting'){
                        $waitingOfferCount++;
                    }
                }
            }

            $result->waiting_offer = $waitingOfferCount;
            $result->selected_offer = $selectedofferCount;
            $result->rejected_offer = $rejectedOfferCount;
        }

        return  View("admin.$this->model.index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string','shipmentService', 'requesttypes', 'offerCountsArray'));
           
    }


    public function view($request_number)
    {

        $shipmentRequestDetails                 =    Shipment::where('request_number', $request_number)
            ->with(
                [
                    'customer',
                    'shipmentDriverScheduleDetails',
                    'shipmentDriverScheduleDetails.userDriverDetail',
                    'SelectedShipmentOffers' => function ($query)  {
                        $query->whereIn('status', ["selected","approved_from_company"]);
                    }
                ]
            )->first();
           $Shipment_id = $shipmentRequestDetails->id; 
           $free_driver = (Object)array();
           $truckList = (Object)array();
        if($shipmentRequestDetails->status == "offer_chosen"){
            $truckList               =   DB::table("trucks")
                ->leftJoin('users', 'users.id' , 'trucks.driver_id')
                ->select("trucks.id","trucks.truck_system_number","trucks.type_of_truck","trucks.truck_company_id","trucks.driver_id","users.name")
                ->where("trucks.is_active",1)
                ->where("trucks.is_deleted",0)
                ->where("trucks.truck_company_id",$shipmentRequestDetails->SelectedShipmentOffers ?->truck_company_id)
                ->where("trucks.type_of_truck",$shipmentRequestDetails->shipment_type)
                ->get();
            
            $free_driver = User::
                where("users.truck_company_id",$shipmentRequestDetails->SelectedShipmentOffers ?->truck_company_id)
                ->select("users.id","users.name","users.user_role_id")
                ->leftjoin('trucks','trucks.driver_id','users.id')
                ->whereRaw("trucks.driver_id IS NULL")
                ->whereIn('users.user_role_id', [3,4])
                ->where("users.is_active",1)
                ->where("users.is_deleted",0)
                ->get();
        }
           
        if($shipmentRequestDetails->customer->customer_type == "private"){
            $shipmentRequestDetails                 =    Shipment::where('shipments.id', $Shipment_id)
            ->with(
                [
                    'customer',
                    'ShipmentOffers',
                    'ShipmentOffers.companyUser',
                    'ShipmentOffers.companyUser.userCompanyInformation',
                    'shipment_attchement',
                    'ShipmentPrivateCustomer_ExtraInformation',
                    'shipmentDriverScheduleDetails',
                    'TruckTypeDescriptionsPrivate' => function($query) {
                        $query->where(['language_id' => getAppLocaleId()]);
                    },
                    'SelectedShipmentOffers' => function ($query)  {
                        $query->whereIn('status', ["selected","approved_from_company"]);
                    }
                ]
            )->first();
            $jsonData = $shipmentRequestDetails['request_pickup_details'];
            $arrayData = json_decode($jsonData, true);
            $request_time = LookupDiscription::where(['parent_id'=>$shipmentRequestDetails->request_time,'language_id'=> getAppLocaleId()])->first();
            if($request_time){
                $shipmentRequestDetails->request_time   = $request_time->code;
            }
			foreach ($shipmentRequestDetails->ShipmentOffers as &$shipmentOffer) {
                $shipmentOffer->rating = RatingReview::where("truck_company_id", $shipmentOffer->truck_company_id)
                    ->selectRaw("SUM(overall_rating) as overall_rating")
                    ->first();
                if ($shipmentOffer->rating->overall_rating) {
                    $count  = RatingReview::where("truck_company_id", $shipmentOffer->truck_company_id)->count();
                    $shipmentOffer->rating->overall_rating = $shipmentOffer->rating->overall_rating / $count;
                }
                $shipmentOffer->rating->overall_rating = round($shipmentOffer->rating->overall_rating);
            }
            return  View("admin.$this->model.private-request-view", compact('shipmentRequestDetails','arrayData','truckList','free_driver'));
        }else if($shipmentRequestDetails->customer->customer_type == "business"){

            $shipmentRequestDetails                 =    Shipment::where('shipments.id', $Shipment_id)
            ->with(
                [
                    'customer',
                    'ShipmentOffers',
                    'ShipmentOffers.companyUser',
                    'ShipmentOffers.companyUser.userCompanyInformation',
                    'shipment_attchement',
                    'ShipmentStop',
                    'ShipmentStop.ShipmentStopAttchements',
                    'shipmentDriverScheduleDetails',
                    'TruckTypeDescriptions' => function($query) {
                        $query->where(['language_id' => getAppLocaleId()]);
                    },
                    'RequestTimeDescription' => function($query) {
                        $query->where(['language_id' => getAppLocaleId()]);
                    },
                    'SelectedShipmentOffers' => function ($query)  {
                        $query->whereIn('status', ["selected","approved_from_company"]);
                    }
                ]
            )->first();
			foreach ($shipmentRequestDetails->ShipmentOffers as &$shipmentOffer) {
                $shipmentOffer->rating = RatingReview::where("truck_company_id", $shipmentOffer->truck_company_id)
                    ->selectRaw("SUM(overall_rating) as overall_rating")
                    ->first();
                if ($shipmentOffer->rating->overall_rating) {
                    $count  = RatingReview::where("truck_company_id", $shipmentOffer->truck_company_id)->count();
                    $shipmentOffer->rating->overall_rating = $shipmentOffer->rating->overall_rating / $count;
                }
                $shipmentOffer->rating->overall_rating = round($shipmentOffer->rating->overall_rating);
                
            }
            return  View("admin.$this->model.business-request-view", compact('shipmentRequestDetails','truckList','free_driver'));

        }
                                                        
    }

    public function ShipmentRequestDetails(Request $request,$request_number){
		if($request->wantsJson() ) {
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
		}else{
			if (Auth::user()) {
				$user = Auth::user();
			}
		}

		$shipment = Shipment::where("request_number",$request_number)
		->where("customer_id",Auth::user()->id)
		
		->with(
			[
				'ShipmentOffers' => function($query) {
					$query->where(['status' => 'waiting']);
					$query->where(['is_deleted' => '0']);
				},
				'ShipmentOffers.companyUser',
				'ShipmentOffers.companyUser.userCompanyInformation',
				'ShipmentStop' => function($query) {
				},
				'ShipmentStop.ShipmentStopAttchements',
				'TruckTypeDescriptions' => function($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'RequestTimeDescription' => function($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'SelectedShipmentOffers' => function($query) {
					$query->where(['status' => 'selected']);
				},
                'shipmentDriverScheduleDetails',
			    'shipmentDriverScheduleDetails.truckDriver',
			]
		)
		->whereIn('status', ['new','offers','offer_chosen','shipment'])
		->orderBy('request_date', 'desc')
		->first();
		if($shipment->status == "shipment"){
			Session()->flash('error', trans("messages.request_status_is_shipment"));
			return redirect::route('business.customer-dashboard');
		}else if($shipment->status == "end"){
			Session()->flash('error', trans("messages.request_status_is_end"));
			return redirect::route('business.customer-dashboard');
		}else if($shipment->status == "cancelled"){
			Session()->flash('error', trans("messages.request_status_is_cancelled"));
			return redirect::route('business.customer-dashboard');
		}
		$shipmentOffer = null;
		if($shipment->SelectedShipmentOffers){
			$shipmentOffer = ShipmentOffer::where("shipment_id",$shipment->id)
			->with([
				'companyUser',
				'companyUser.userCompanyInformation',
                'TruckDetail',
			    'TruckDetail.truckDriver',
			])
			->first();
			$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk = LookupDiscription::where(['parent_id'=>$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk,'language_id'=> getAppLocaleId()])->first()->code ?? ""; 

		}
		
		if($shipment == null){
				Session()->flash('error', trans("messages.shipment_request_not_found"));
				return redirect::route('business.customer-dashboard');
		}
		return View("frontend.customers.business.shipment-request-details", compact('user','shipment','shipmentOffer',));
	}

    public function ViewOfferDetails(Request $request,$system_id,$viewType = null){	
        	
		$shipmentOffer = ShipmentOffer::where("system_id",$system_id)
        ->first();
        $ShipmentOffer_id = $shipmentOffer->id;
        
		$shipment = Shipment::where("id",$shipmentOffer->shipment_id)
        
		->with(
            [
                'customer',
                'ShipmentOffers',
                'ShipmentOffers.companyUser',
                'ShipmentOffers.companyUser.userCompanyInformation',
                'shipment_attchement',
                'ShipmentStop',
                'ShipmentStop.ShipmentStopAttchements',
                'TruckTypeDescriptions' => function($query) {
                    $query->where(['language_id' => getAppLocaleId()]);
                },
                'RequestTimeDescription' => function($query) {
                    $query->where(['language_id' => getAppLocaleId()]);
                },
                'SelectedShipmentOffers',
                'shipmentDriverScheduleDetails',
			    'shipmentDriverScheduleDetails.truckDriver',
            ]
        )
		->first();
		if($shipment == null){
				Session()->flash('error', trans("messages.shipment_request_not_found"));
				return redirect::route('shipment-request.show');
		}

		$shipmentOffer = ShipmentOffer::where("system_id",$system_id)
		->with([
			'companyUser',
			'companyUser.userCompanyInformation',
            'TruckDetail',
			'TruckDetail.truckDriver',
		])
		->first();
        
        $shipmentOffer->companyUser->userCompanyInformation->company_tidaluk = LookupDiscription::where(['parent_id'=>$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk,'language_id'=> getAppLocaleId()])->first()->code ?? ""; 
        $shipmentOffer->companyUser->userCompanyInformation->company_refueling = LookupDiscription::where(['parent_id'=>$shipmentOffer->companyUser->userCompanyInformation->company_refueling,'language_id'=> getAppLocaleId()])->first()->code ?? ""; 

		return View("admin.$this->model.offer-detail-view", compact('shipmentOffer','shipment','viewType'));

	}

    public function offersList(Request $request){
        $language_id    = getAppLocaleId();
        $DB				= ShipmentOffer::query()
        ->join('trucks', 'trucks.id' , 'shipment_offers.truck_id')
        ->join('shipments', 'shipments.id' , 'shipment_offers.shipment_id')
        ->join('users', 'shipments.customer_id' , 'users.id')
        ->join('user_company_informations', 'user_company_informations.user_id' , 'shipment_offers.truck_company_id')
        ->leftjoin('shipment_driver_schedules','shipments.id','shipment_driver_schedules.shipment_id')
        ->select(
            'shipment_offers.*',
            'users.name as user_name',
            'user_company_informations.company_name',
            'shipments.request_number',
            DB::raw(" (SELECT name FROM truck_type_descriptions 
                        WHERE truck_type_descriptions.parent_id = trucks.type_of_truck 
                            AND truck_type_descriptions.language_id = $language_id)
                    as request_types ")
        );
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
                $DB->whereBetween('shipment_offers.request_offer_date', [date('Y-m-d H:i:s',strtotime($dateS." 00:00:00")), date('Y-m-d H:i:s',strtotime($dateE." 23:59:59"))]); 											
            }elseif(!empty($searchData['date_from'])){
                $dateS = $searchData['date_from'];
                $DB->where('shipment_offers.request_offer_date','>=' ,[date('Y-m-d H:i:s',strtotime($dateS." 00:00:00"))]); 
            }elseif(!empty($searchData['date_to'])){
                $dateE = $searchData['date_to'];
                $DB->where('shipment_offers.request_offer_date','<=' ,[date('Y-m-d H:i:s',strtotime($dateE." 23:59:59"))]); 						
            }

            $form_latitude = $request->current_lat;
            $form_longitude = $request->current_lng;
            if($request->city_name && $form_latitude != "" && $form_longitude != ""){
                $DB->where(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( shipments.company_latitude ) ) * cos( radians(  shipments.company_longitude  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( shipments.company_latitude ) ) ))"),"<=",15);
                $DB->orderBy(DB::raw("(3959 * acos( cos( radians(" . $form_latitude . ") ) * cos( radians( shipments.company_latitude ) ) * cos( radians(  shipments.company_longitude  ) - radians(" . $form_longitude . ") ) + sin( radians(" . $form_latitude . ") ) * sin( radians( shipments.company_latitude ) ) ))"),"ASC");
            }

            foreach ($searchData as $fieldName => $fieldValue) {
				if ($fieldValue != "") {
                    if ($fieldName == "company_name") {
                        $DB->where("user_company_informations.company_name", 'like', '%' . $fieldValue . '%');
                    }      
                    if ($fieldName == "name") {
                        $DB->where("name", 'like', '%' . $fieldValue . '%');
                    }                    
                    if ($fieldName == "request_types") {
                        $DB->where(function ($query) use ($fieldValue, $language_id) {
                            $query->whereRaw('
                            (SELECT parent_id FROM truck_type_descriptions 
                                    WHERE truck_type_descriptions.parent_id = trucks.type_of_truck 
                                        AND truck_type_descriptions.language_id = ?) like ? 
                            ',
                            [$language_id, $fieldValue ]
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
                        $DB->where("shipment_offers.status",$fieldValue);
                    }
				}
				$searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
			}
        }

        $DB->where("users.is_deleted", 0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");

        $DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_shipment_request_offers'=>$allData->get()]);
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
        return  View("admin.$this->model.offers-list", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string','shipmentService', 'requesttypes'));
           
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
		return Excel::download(new ReportExport($collection), 'Shipment request.xlsx');
	}


    public function offerExport(Request $request)
	{


         $list[0] = array(
            trans('messages.request_number'),
            trans('messages.Company Name'),
            trans('messages.customers_name'),
            trans('messages.request_type'),
            trans('messages.request_date'),
            trans('messages.admin_Created_On'),
            trans('messages.admin_common_Status'),
        );

		$customers_export = Session::get('export_data_shipment_request_offers');
		

		foreach ($customers_export as $key => $excel_export) {

            if($excel_export->shipment_status){
                if($excel_export->shipment_status == 'start'){
                    $status = trans('messages.active');
                }
                else{
                    $status = trans('messages.'.$excel_export->shipment_status);
                }
                
            }else{
                if($excel_export->status=='rejected'){
                    $status =  trans('messages.Rejected');
                }
                else{
                    $status =  trans('messages.'.$excel_export->status);
                }

            }
               
        $list[] = array(

                $excel_export->request_number,
                $excel_export->company_name,
                $excel_export->user_name,
                $excel_export->request_types,
                date(config("Reading.date_format"), strtotime($excel_export->request_offer_date)),
                date(config("Reading.date_format"), strtotime($excel_export->created_at)),
                $status,

            );
			
		}
		

        $collection = new Collection($list);
        return Excel::download(new ReportExport($collection), 'Shipment request offers.xlsx');

	}

    public function applyOffer(Request $request,$enid){
        $shipment_id = base64_decode($enid);
        $shipmentRequest = Shipment::find($shipment_id);
        if(!$shipmentRequest){
            Session()->flash('error', ucfirst(trans("messages.shipment_request_not_found")));
            return Redirect()->route($this->model . ".index");
        }
        if($request->post()){

            $thisData = $request->all();
            $validated = $request->validate(
                array(
                    'truck_company'                      => "required",
                    'duration'                           => "required|lt:24",
                    'price_nis'                                 => "required|numeric",
                    'term_of_payment'                       => "required",
                    'request_date'                    => "required|date_format:d-m-Y",
                ), 
                array(
                    "truck_company.required"             => trans("messages.This field is required"),
                    "duration.required"                     => trans("messages.This field is required"),
                    "duration.lt"                            => trans("messages.The duration must be less than 24 hours"),
                    "price_nis.required"                        => trans("messages.This field is required"),
                    "type_of_truck.required"                => trans("messages.This field is required"),
                    "truck_id.required"                     => trans("messages.This field is required"),
                    "shipment_id.required"                  => trans("messages.This field is required"),
                    "shipment_description.required"         => trans("messages.This field is required"),
                    "term_of_payment.required"              => trans("messages.This field is required"),
                    "request_date.required"           => trans("messages.This field is required"),
                )
            );

            $companyInfo = UserCompanyInformation::where('user_id', $request->truck_company)->first();

            if($companyInfo->company_description == null || $companyInfo->company_description == ''){
                session()->flash("flash_notice", trans("messages.please_fill_the_company_information_first"));
                return redirect()->back();
            }

            $shipment_offers =   DB::table("shipment_offers")->where("shipment_id",$shipment_id)->where("truck_company_id",$request->truck_company)->first();

            if(empty($shipment_offers)){
                $objShipmentOffer                        = new ShipmentOffer;
                $objShipmentOffer->duration              = $request->duration;
                $objShipmentOffer->request_offer_date    = $request->request_date ? Carbon::createFromFormat('d-m-Y', ($request->request_date))->format('Y-m-d') : NULL;
                $objShipmentOffer->truck_company_id      = $request->truck_company;
                $objShipmentOffer->shipment_id           = $shipment_id;
                $objShipmentOffer->system_id             = 0;
                $objShipmentOffer->price                 = $request->price_nis;
                $objShipmentOffer->extra_time_price      = (!empty($request->addtional_hours_cost) && $request->addtional_hours_cost > 0) ? $request->addtional_hours_cost : 0;
                $objShipmentOffer->description           = $request->shipmetnt_note_optional;
                $objShipmentOffer->payment_condition     = $request->term_of_payment;
                $objShipmentOffer->truck_id              = 0;
                $objShipmentOffer->status                = "waiting";
                $objShipmentOffer->save();

                $system_id  =   100000+$objShipmentOffer->id;
                $objShipmentOffer->system_id = $system_id;
                $objShipmentOffer->save();
                
                $objShipment = Shipment::where("id",$shipment_id)->first();
                $objShipment->status = 'offers';
                $objShipment->save();

                $this->new_offer_created_for_customer($objShipment,$objShipmentOffer);

                Session()->flash('success', ucfirst(trans("messages.Offer_applied_successfully")));
                return Redirect()->route($this->model . ".show",[$shipmentRequest->request_number,"tabs=offers"]);
            }else{
                Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                return Redirect()->back();
            }
        }
        $shipmentOfferAppliedCompanyIds = shipmentOffer::where("shipment_id",$shipmentRequest->id)->pluck('truck_company_id')->toArray();
        $removeShipentCompanyIds = ShipmentOfferRequestRejected::where("shipment_id",$shipmentRequest->id)->pluck('customer_id')->toArray();
        $shipmentOfferAppliedCompanyIds = array_merge($removeShipentCompanyIds,$shipmentOfferAppliedCompanyIds);

        ///////////////////////////////////////////
        $shipmentRequest->request_date = Carbon::createFromFormat('Y-m-d', $shipmentRequest->request_date);
        $frequest_date = $shipmentRequest->request_date->copy();
        $srequest_date = $shipmentRequest->request_date->copy();

        $shipmentRequest->request_start_date = $frequest_date
        ->subDay($shipmentRequest->request_date_flexibility);
        
        
        $shipmentRequest->request_end_date = $srequest_date
        ->addDay($shipmentRequest->request_date_flexibility);
        

        $nextDay = Carbon::now()->addDay(1);

        if($shipmentRequest->request_start_date->lt($nextDay)){
            $shipmentRequest->request_start_date = $nextDay;
        }

        if($shipmentRequest->request_end_date->lt($nextDay)){
            Session()->flash('error', ucfirst(trans("messages.shipment_request_has_expired")));
            return Redirect()->route($this->model . ".show",[$shipmentRequest->request_number,"tabs=offers"]);
        }

        $shipmentRequest->request_start_date = $shipmentRequest->request_start_date->format('Y-m-d');
        $shipmentRequest->request_end_date = $shipmentRequest->request_end_date->format('Y-m-d');



        $truckCompanyList = Truck::where("type_of_truck",$shipmentRequest->shipment_type)
            ->leftJoin("users","users.id","trucks.truck_company_id")
            ->leftJoin("user_company_informations","users.id","user_company_informations.user_id")
            ->where(["users.user_role_id"=>3, 'users.is_active' => 1,'users.is_approved'=>1,'users.is_deleted'=>0 ])	
            ->whereNotIn('users.id',$shipmentOfferAppliedCompanyIds)
			->select("users.*","user_company_informations.company_name")
			->groupBy("users.id")
			->get();
        return  View("admin.$this->model.apply-offers", compact('truckCompanyList', 'shipmentOfferAppliedCompanyIds', 'shipmentRequest'));
    }
    
    public function editApplyOffer(Request $request,$offer_id,$shipment_id){
       
        $shipmentRequest = Shipment::find($shipment_id);
        if(!$shipmentRequest){
            Session()->flash('error', ucfirst(trans("messages.shipment_request_not_found")));
            return Redirect()->route($this->model . ".index");
        }
        $shipmentOffer =   DB::table("shipment_offers")->where("shipment_id",$shipment_id)->where("id",$offer_id)->first();
           
        if($shipmentRequest->status != 'offers'){
            Session()->flash('flash_notice', ucfirst(trans("messages.invalid_requests")));
            return redirect()->back();
        }
        if($request->post()){
            
            $thisData = $request->all();
            $validated = $request->validate(
                array(
                    'truck_company'                         => "required",
                    'duration'                              => "required",
                    'price_nis'                             => "required|numeric",
                    'term_of_payment'                       => "required",
                    'request_date'                          => "required|date_format:d-m-Y",
                ), 
                array(
                    "truck_company.required"                => trans("messages.This field is required"),
                    "duration.required"                     => trans("messages.This field is required"),
                    "price_nis.required"                    => trans("messages.This field is required"),
                    "type_of_truck.required"                => trans("messages.This field is required"),
                    "truck_id.required"                     => trans("messages.This field is required"),
                    "shipment_id.required"                  => trans("messages.This field is required"),
                    "shipment_description.required"         => trans("messages.This field is required"),
                    "term_of_payment.required"              => trans("messages.This field is required"),
                    "request_date.required"                 => trans("messages.This field is required"),
                )
            );

            $objShipmentOffer =   shipmentOffer::where("shipment_id",$shipment_id)->where("id",$offer_id)->first();
            if($objShipmentOffer){
               
                $objShipmentOffer->duration              = $request->duration;
                $objShipmentOffer->request_offer_date    = $request->request_date ? Carbon::createFromFormat('d-m-Y', ($request->request_date))->format('Y-m-d') : NULL;
                $objShipmentOffer->truck_company_id      = $request->truck_company;
                $objShipmentOffer->shipment_id           = $shipment_id;
                $objShipmentOffer->system_id             = 0;
                $objShipmentOffer->price                 = $request->price_nis;
                $objShipmentOffer->extra_time_price      = (!empty($request->addtional_hours_cost) && $request->addtional_hours_cost > 0) ? $request->addtional_hours_cost : 0;
                $objShipmentOffer->description           = $request->shipmetnt_note_optional;
                $objShipmentOffer->payment_condition     = $request->term_of_payment;
                $objShipmentOffer->truck_id              = 0;
                $objShipmentOffer->status                = "waiting";
                $objShipmentOffer->save();

                $system_id  =   100000+$objShipmentOffer->id;
                $objShipmentOffer->system_id = $system_id;
                $objShipmentOffer->save();
                
               
                $objShipment = Shipment::where("id",$objShipmentOffer->shipment_id)->first();

                $this->update_offer_for_customer($objShipment,$objShipmentOffer);

                Session()->flash('success', ucfirst(trans("messages.Offer_applied_successfully")));
                return Redirect()->route($this->model . ".show",[$shipmentRequest->request_number,"tabs=offers"]);
            }else{
                Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                return Redirect()->back();
            }
        }
        $shipmentOfferAppliedCompanyIds = shipmentOffer::where("shipment_id",$shipmentRequest->id)
        ->whereNotIn('truck_company_id',[$shipmentOffer->truck_company_id])
        ->pluck('truck_company_id')->toArray();

        $removeShipentCompanyIds = ShipmentOfferRequestRejected::where("shipment_id",$shipmentRequest->id)->pluck('customer_id')->toArray();
        $shipmentOfferAppliedCompanyIds = array_merge($removeShipentCompanyIds,$shipmentOfferAppliedCompanyIds);

        $shipmentRequest->request_date = Carbon::createFromFormat('Y-m-d', $shipmentRequest->request_date);
        
        $shipmentRequest->request_start_date = $shipmentRequest->request_date
        ->subDay($shipmentRequest->request_date_flexibility);
        
        $shipmentRequest->request_end_date = $shipmentRequest->request_date
        ->addDay($shipmentRequest->request_date_flexibility);


        $nextDay = Carbon::now()->addDay(1);

        if($shipmentRequest->request_start_date->lt($nextDay)){
            $shipmentRequest->request_start_date = $nextDay;
        }

        if($shipmentRequest->request_end_date->lt($nextDay)){
            Session()->flash('error', ucfirst(trans("messages.shipment_request_has_expired")));
            return Redirect()->route($this->model . ".show",[$shipmentRequest->request_number,"tabs=offers"]);
        }
        $shipmentRequest->request_start_date = $shipmentRequest->request_start_date->format('Y-m-d');
        $shipmentRequest->request_end_date = $shipmentRequest->request_end_date->format('Y-m-d');


        $truckCompanyList = Truck::where("type_of_truck",$shipmentRequest->shipment_type)
            ->leftJoin("users","users.id","trucks.truck_company_id")
            ->leftJoin("user_company_informations","users.id","user_company_informations.user_id")
            ->where(["users.user_role_id"=>3, 'users.is_active' => 1,'users.is_approved'=>1,'users.is_deleted'=>0 ])	
            ->whereNotIn('users.id',$shipmentOfferAppliedCompanyIds)
			->select("users.*","user_company_informations.company_name")
			->groupBy("users.id")
			->get();
        return  View("admin.$this->model.edit-apply-offers", compact('truckCompanyList', 'shipmentOfferAppliedCompanyIds', 'shipmentRequest', 'shipmentOffer'));
    }

    public function rejectShipmentOffer(Request $request,$offer_id,$shipment_id){
        $shipmentRequest = Shipment::find($shipment_id);
        if(!$shipmentRequest){
            Session()->flash('error', ucfirst(trans("messages.shipment_request_not_found")));
            return Redirect()->route($this->model . ".index");    
        }

        $ShipmentOfferRequest = ShipmentOffer::find($offer_id);
        if(!$ShipmentOfferRequest){
            Session()->flash('error', ucfirst(trans("messages.shipment_request_not_found")));
            return Redirect()->route($this->model . ".index");    
        }
        $truck_company_id = $ShipmentOfferRequest->truck_company_id;

        $shipment_offer_request_rejected =   DB::table("shipment_offer_request_rejected")->where("shipment_id",$shipment_id)->where("customer_id",$truck_company_id)->first();
        if(empty($shipment_offer_request_rejected)){
            $obj                        = new ShipmentOfferRequestRejected; 
            $obj->customer_id           = $truck_company_id;
            $obj->shipment_id           = $shipment_id;
            $obj->save();

            $objShipment = Shipment::find($shipment_id);
            $objShipment->status = "offers";
            $objShipment->save();
            
            $objShipmentOffer = ShipmentOffer::where("shipment_id",$shipment_id)
                ->where("truck_company_id",$truck_company_id)->first();
            $objShipmentOffer->status = "rejected";
            $objShipmentOffer->save();

            $this->shipment_rejected_by_company($objShipment,$objShipmentOffer);

            $OffersCount = ShipmentOffer::where(["shipment_id"=>$shipment_id,"status"=>"waiting"])->get();
            if($OffersCount->count()==0){
                $objShipment->status = "new";
                $objShipment->save();
            }

            Session()->flash('success', ucfirst(trans("messages.Request_has_been_rejected_successfully")));
            return Redirect()->back();
        }else {
            Session()->flash('error', ucfirst(trans("messages.invalid_requests")));
            return Redirect()->back();
        }
    }

    public function approveShipmentOffer(Request $request,$offer_id,$shipment_id){

        $objShipment = Shipment::find($shipment_id);
        if(!$objShipment){
            Session()->flash('error', ucfirst(trans("messages.shipment_request_not_found")));
            return Redirect()->route($this->model . ".index");    
        }

        $ShipmentOfferRequest = ShipmentOffer::find($offer_id);
        if(!$ShipmentOfferRequest){
            Session()->flash('error', ucfirst(trans("messages.shipment_request_not_found")));
            return Redirect()->route($this->model . ".index");    
        }
        $truck_company_id = $ShipmentOfferRequest->truck_company_id;
        
        


        if($objShipment->status == "shipment"){
            Session()->flash('error', ucfirst(trans("messages.invalid_requests")));
            return Redirect()->back();
        }

        $objTruck = Truck::where("id",$request->truck_id)->first();

        $objTruckCompany       = UserCompanyInformation::where('user_id', $objTruck->truck_company_id)->first();    

        $licenceDate           = $objTruck->truck_licence_expiration_date;
        $insuranceDate         = $objTruck->truck_insurance_expiration_date;

        $lastDate              = now()->subDays(1);
        $checkExpirelicence    = ($licenceDate < $lastDate ? 'expired' : '');
        $checkExpireinsurance = ($insuranceDate < $lastDate ? 'expired' : '');

        $messageStr = '';
        $responceMessage ='';
        if ($objTruckCompany->company_tidaluk == null) {
            $messageStr .= '{{tidaluk_company}}'; 
        }
        if ($objTruck->truck_licence_expiration_date == null) {
            if($messageStr != ''){
                $messageStr .= ', '; 
            }
            $messageStr .= '{{licence_expiration}}'; 
        }
        if ($objTruck->truck_insurance_expiration_date == null) {
            if($messageStr != ''){
                $messageStr .= ', '; 
            }
            $messageStr .= '{{insurance_expiration}}'; 
        }
        if ($checkExpirelicence == "expired") {
            if($messageStr != ''){
                $messageStr .= ', '; 
            }
            $messageStr .= '{{licence_expiration}}'; 
        }
        if ($checkExpireinsurance == "expired") {
            if($messageStr != ''){
                $messageStr .= ', '; 
            }
            $messageStr .= '{{insurance_expiration}}'; 
        }

        if($messageStr != ''){
            $responceMessage = trans("messages.please_upldate_following_details") . ":" . " ";
            $const = array(
                '{{tidaluk_company}}',
                '{{licence_expiration}}',
                '{{insurance_expiration}}',
            );
            $replaceArrayString = array(
                trans("messages.admin_Tidaluk_Company"),
                trans("messages.licence_expiration"),
                trans("messages.insurance_expiration"),
            );

            $responceMessage .= str_replace($const, $replaceArrayString, $messageStr);

            Session()->flash('error', $responceMessage);
            return Redirect()->route('shipment-request.show', $objShipment->request_number . '?tabs=offers');
        }



        $shipment_driver_schedule =   ShipmentDriverSchedule::where("shipment_id",$shipment_id)->where("truck_company_id",$truck_company_id)->first();
        
        if(empty($shipment_driver_schedule)){
            $shipment_offers =   DB::table("shipment_offers")
            ->where("shipment_id",$shipment_id)
            ->where("truck_company_id",$truck_company_id)
            ->where("status",'selected')
            ->first();
            if($shipment_offers){

                $objTruckDriver = User::
                where(
                    [
                        'id'            => $request->driver_id,
                        'is_active'     => 1,
                        'is_deleted'    => 0,
                    ]
                )
                ->whereIn('user_role_id', [3,4])
                ->first();

                if($objTruckDriver){

                    $objTruck->driver_id              = $request->driver_id;
                    $objTruck->save();
            
                    $objShipment->status    = "shipment";
                    $objShipment->save();

                    $objShipmentOffer = ShipmentOffer::where("shipment_id",$shipment_id)
                    ->where("truck_company_id",$truck_company_id)->first();
                    $objShipmentOffer->status = "approved_from_company";
                    $objShipmentOffer->truck_id              = $request->truck_id;
                    $objShipmentOffer->save();


                    $ShipmentOffer  =   ShipmentOffer::where("shipment_id",$shipment_id)
                    ->where("truck_company_id","!=",$truck_company_id)
                    ->get();
                    if($ShipmentOffer->count()){
                        $ShipmentOffer = $ShipmentOffer->toArray();
                    }

                    if(!empty($ShipmentOffer)){
                        foreach($ShipmentOffer as $ShipmentOffer_v){
                            $obj                        = new ShipmentOfferRequestRejected;
                            $obj->customer_id           = $ShipmentOffer_v['truck_company_id'];
                            $obj->shipment_id           = $shipment_id;
                            $obj->save();
                        }
                    }
                    ShipmentOffer::where("shipment_id",$shipment_id)->where("truck_company_id","!=",$truck_company_id)->update(array("status"=>"rejected"));

                    $objShipmentDriverSchedule                     = new ShipmentDriverSchedule;
                    $objShipmentDriverSchedule->shipment_id        = $shipment_id;
                    $objShipmentDriverSchedule->truck_company_id   = $truck_company_id;
                    $objShipmentDriverSchedule->driver_id          = $objTruck->driver_id ?? 0;
                    $objShipmentDriverSchedule->truck_id           = $objTruck->id;


                    if($shipment_offers->request_offer_date){
                        $request->start_time = Carbon::createFromFormat('Y-m-d', ($shipment_offers->request_offer_date))->format('d/m/Y') . " " . $request->start_time ;
                    }else{
                        $request->start_time = $request->start_day . " " . $request->start_time;

                    }
                    $objShipmentDriverSchedule->start_time         = Carbon::createFromFormat('d/m/Y h:i A', ($request->start_time))->format('Y-m-d H:i:s');
                    $objShipmentDriverSchedule->duration           = $shipment_offers->duration;
                    $objShipmentDriverSchedule->shipment_status    = "not_start";
                    $objShipmentDriverSchedule->save();
                    $this->shipment_schedule_by_company($objShipment,$shipment_offers,$objShipmentDriverSchedule);

                }
            }
        }

        Session()->flash('success', ucfirst(trans("messages.Request_has_been_approved_successfully")));
        return Redirect()->route('shipment-request.show', $objShipment->request_number . '?tabs=offers');  
        
    }

    public function shipmentSchedule(Request $request,$offer_id,$shipment_id) {
        $thisData = $request->all();
        $shipment_offer =   ShipmentOffer::find($offer_id);
        $truck_company_id = $shipment_offer->truck_company_id;
        
        $shipment_driver_schedule =   ShipmentDriverSchedule::where("shipment_id",$shipment_id)->where("truck_company_id",$truck_company_id)->first();
        if(empty($shipment_driver_schedule)){
            $shipment_offers =   DB::table("shipment_offers")
                ->where("shipment_id",$shipment_id)
                ->where("truck_company_id",$truck_company_id)
                ->where("status",'approved_from_company')
                ->first();
            if($shipment_offers){

                $objShipment =   Shipment::where("id",$shipment_id)
                    ->where("status",'shipment')
                    ->first();
                if($objShipment){

                    $objTruck = Truck::find($shipment_offers->truck_id);
                    $objTruckDriver = User::
                        where(
                            [
                                'id'            => $objTruck->driver_id,
                                'is_active'     => 1,
                                'is_deleted'    => 0,
                            ]
                        )
                        ->whereIn('user_role_id', [3,4])
                        ->first();
                    if($objTruckDriver){
                        $objShipmentDriverSchedule                     = new ShipmentDriverSchedule;
                        $objShipmentDriverSchedule->shipment_id        = $shipment_id;
                        $objShipmentDriverSchedule->truck_company_id   = $truck_company_id;
                        $objShipmentDriverSchedule->driver_id          = $objTruck->driver_id ?? 0;
                        $objShipmentDriverSchedule->truck_id           = $objTruck->id;


                        if($shipment_offers->request_offer_date){
                            $request->start_time = Carbon::createFromFormat('Y-m-d', ($shipment_offers->request_offer_date))->format('d/m/Y') . " " . $request->start_time ;
                        }else{
                            $request->start_time = $request->start_day . " " . $request->start_time;

                        }
                        $objShipmentDriverSchedule->start_time         = Carbon::createFromFormat('d/m/Y h:i A', ($request->start_time))->format('Y-m-d H:i:s');
                        $objShipmentDriverSchedule->duration           = $shipment_offers->duration;
                        $objShipmentDriverSchedule->shipment_status    = "not_start";
                        $objShipmentDriverSchedule->save();


                        $this->shipment_schedule_by_company($objShipment,$shipment_offers,$objShipmentDriverSchedule);
                        Session()->flash('success', ucfirst(trans("messages.shipment_schedule_successfully")));
                        return Redirect()->route('shipment-request.show', $objShipment->request_number . '?tabs=offers');
                    }else{
                        Session()->flash('error', ucfirst(trans("messages.please_assign_a_driver_to_the_selected_truck_then_try_again")));
                        return Redirect()->back();
                    }
                }else{
                    Session()->flash('error', ucfirst(trans("messages.invalid_requests")));
                    return Redirect()->back();
                }

            }else{
                Session()->flash('error', ucfirst(trans("messages.invalid_requests")));
                return Redirect()->back();
            }
        }else{
            Session()->flash('error', ucfirst(trans("messages.invalid_requests")));
            return Redirect()->back();
        }
	}

    public function deleteShipmentSchedule(Request $request, $schedule_id) {
        
        $objShipmentDriverSchedule = ShipmentDriverSchedule::find($schedule_id);

        ShipmentDriverSchedule::where("id",$schedule_id)->delete();

        $objShipment    =   Shipment::where("id",$objShipmentDriverSchedule->shipment_id)
            ->where("status",'shipment')
            ->first();

        $shipment_offers =   DB::table("shipment_offers")
            ->where("shipment_id",$objShipmentDriverSchedule->shipment_id)
            ->where("truck_company_id",$objShipmentDriverSchedule->truck_company_id)
            ->where("status",'approved_from_company')
            ->first();

        $this->shipment_schedule_deleted_by_company($objShipment,$shipment_offers,$objShipmentDriverSchedule);
        Session()->flash('success', ucfirst(trans("messages.shipment_schedule_deleted_successfully")));
        return Redirect()->back();


    }

    public function shipmentScheduleStart(Request $request,$schedule_id) {
        
        $objSchedule = ShipmentDriverSchedule::find($schedule_id);
        $driver_id = $objSchedule->driver_id;

        $objStartedSchedule = ShipmentDriverSchedule::where(
            [
                'shipment_status'   =>  'start',
                'driver_id'         =>  $driver_id
            ]
        )->get();
        if($objStartedSchedule->count()==0){
            $objShipmentDriverSchedule = ShipmentDriverSchedule::where(
                [
                    'id'=>$schedule_id,
                    'driver_id'=>$driver_id
                ]
            )->first();
            if($objShipmentDriverSchedule && $objShipmentDriverSchedule->shipment_status == 'not_start'){
                $objShipmentDriverSchedule->shipment_status = 'start';
                $currentDateTime = Carbon::now();
                $currentDateTime->format('Y-m-d H:i:s');
                $objShipmentDriverSchedule->shipment_actual_start_time = $currentDateTime->format('Y-m-d H:i:s');
                $objShipmentDriverSchedule->save();

                $objShipment    =   Shipment::where("id",$objShipmentDriverSchedule->shipment_id)
                    ->where("status",'shipment')
                    ->first();
    
                $shipment_offers =   DB::table("shipment_offers")
                    ->where("shipment_id",$objShipmentDriverSchedule->shipment_id)
                    ->where("status",'approved_from_company')
                    ->first();

                $this->shipment_schedule_start_by_driver($objShipment,$shipment_offers,$objShipmentDriverSchedule);


                Session()->flash('success', ucfirst(trans("messages.shipment_start_successfully")));
                return Redirect()->route('shipment-request.show', $objShipment->request_number . '?tabs=offers');
            }else{
                Session()->flash('error', ucfirst(trans("messages.invalid_requests")));
                return Redirect()->back();
            }
        }else{
            Session()->flash('error', ucfirst(trans("messages.you_can_start_only_one_shipment_at_a_time")));
            return Redirect()->back();
        }
	}

    public function shipmentScheduleEnd(Request $request,$schedule_id) {
        
        $objShipmentDriverSchedule = ShipmentDriverSchedule::where(
            [
                'id'=>$schedule_id
            ]
        )->first();

        if($objShipmentDriverSchedule && $objShipmentDriverSchedule->shipment_status == 'start'){
            $shipmentStopsObj = ShipmentStop::with('shipmentDetails')->where("shipment_id",$objShipmentDriverSchedule->shipment_id)
                ->whereIn('request_certificate_type',['digital','physical'])
                ->get();
                if($shipmentStopsObj){
                    $endFlag = false;
                    foreach($shipmentStopsObj as $key => $Stops ){
                    if(($Stops->request_certificate == null && $Stops->request_certificate_skip == 0) || ($Stops->request_digital_signature == null && $Stops->request_signature_skip == 0)){
                        $endFlag = true;
                        break;
                    }
                    if($Stops->request_certificate_type == 'digital' && $Stops->request_certificate_type == 'physical' && $Stops->request_certificate_by_driver == 0){
                        $endFlag = true;
                        break;
                    }
                    if(($Stops->request_certificate == null && $Stops->request_certificate_skip == 1) || $Stops->request_digital_signature == null && $Stops->request_signature_skip == 1){
                        $endFlag = false;
                        break;
                    }

                }
                if($endFlag){
                    Session()->flash('error', ucfirst(trans("messages.please_upload_signature_and_documents_first")));
                    return Redirect()->route('shipment-request.show', $Stops->shipmentDetails->request_number . '?tabs=list_of_stops_documents');
                }
            }

            $objShipmentDriverSchedule->shipment_status         = 'end';
            $objShipmentDriverSchedule->shipment_end_comment    = $request->shipment_end_comment;
            $objShipmentDriverSchedule->time_taken_to_complete_shipment = $request->estimated_time;
            $currentDateTime = Carbon::now();
            $currentDateTime->format('Y-m-d H:i:s');
            $objShipmentDriverSchedule->shipment_actual_end_time = $currentDateTime->format('Y-m-d H:i:s');
            $objShipmentDriverSchedule->save();

            $objShipment    =   Shipment::where("id",$objShipmentDriverSchedule->shipment_id)
                ->first();
            
            $objShipment->status = "end";
            $objShipment->save();

            $shipment_offers =   DB::table("shipment_offers")
                ->where("shipment_id",$objShipmentDriverSchedule->shipment_id)
                ->where("status",'approved_from_company')
                ->first();

            $this->shipment_schedule_end_by_driver($objShipment,$shipment_offers,$objShipmentDriverSchedule);


            Session()->flash('success', ucfirst(trans("messages.shipment_end_successfully")));
            return Redirect()->route('shipment-request.show', $objShipment->request_number . '?tabs=offers');
        }else{
            Session()->flash('error', ucfirst(trans("messages.invalid_requests")));
            return Redirect()->back();
        }
	}


	public function sendShipmentInvoice(Request $request,$shipment_id)
	{
        if ($request->hasFile('invoice')) {
            $objShipment 						      = Shipment::find($shipment_id);
            $user = User::find($objShipment->customer_id);
            $userCompanyInformation = array();
            if($user->customer_type == "business" ){
                $userCompanyInformation = UserCompanyInformation::where("user_id",$user->id)->first()->toArray();
            }else{
                $userCompanyInformation = array(
                    "contact_person_name"			=>	$user->name,
                    "contact_person_email"			=>	$user->email,
                    "contact_person_phone_number"	=>	$user->phone_number
                );
            }
            if($userCompanyInformation['contact_person_email'] == ""){
                Session()->flash('error', ucfirst(trans("messages.email_id_has_not_been_updated_in_this_customer_profile")));
                return Redirect()->back();
            }

            
            $attechmentName = 'invoice_'.$objShipment->request_number.'.'.$request->invoice->getClientOriginalExtension();
            $objShipment->invoice_file  = $attechmentName;
            $objShipment->invoice_price = $request->invoice_price;
            $objShipment->invoice_send_time = Carbon::now()->toDateTimeString() ;
            $objShipment->save();
            $request->file('invoice')->move(Config('constants.INVOICE_FILE_ROOT_PATH'), $attechmentName);

            $uploadedFile = $request->file('invoice');
            $file = Config('constants.INVOICE_FILE_ROOT_PATH').$attechmentName;

            $shipment_stops_obj							= ShipmentStop::where("shipment_id",$objShipment->id)->get()->toArray();

            $emailActions           =    EmailAction::where('action', '=', 'truck_company_send_invoice_to_customer')->get()->toArray();
            $emailTemplates         =    EmailTemplate::where('action', '=', 'truck_company_send_invoice_to_customer')->get(array('name', 'subject', 'action', 'body'))->toArray();

            $emailActions = EmailAction::where('action', '=', 'truck_company_send_invoice_to_customer')->get()->toArray();
            $language_id = getAppLocaleId();
            $emailTemplates = EmailTemplate::where('action', '=', 'truck_company_send_invoice_to_customer')
                ->select(
                    "name",
                    "action",
                    DB::raw("(select subject from email_template_descriptions where parent_id=email_templates.id AND language_id=$language_id) as subject"),
                    DB::raw("(select body from email_template_descriptions where parent_id=email_templates.id AND language_id=$language_id) as body")
                )->get()->toArray();

            $cons                   =     explode(',', $emailActions[0]['options']);
            $constants              =     array();
            foreach ($cons as $key  => $val) {
                $constants[] = '{' . $val . '}';
            }
            $subject             =  $emailTemplates[0]['subject'];
            $userEmails = $userCompanyInformation['contact_person_email'];
            
            $userName   = $userCompanyInformation['contact_person_name'];

            $settingsEmail = Config('Site.email');
            $rep_Array 		= 	array(
                $userCompanyInformation['contact_person_name'],
                $userCompanyInformation['contact_person_email'],
                $userCompanyInformation['contact_person_phone_number'],
                $objShipment->request_number,
                $objShipment->status,
                (TruckTypeDescription::where(["parent_id"=>$objShipment->shipment_type,"language_id"=>$language_id])->first()->name),
                $objShipment->request_date,
                $objShipment->request_time,
                $objShipment->request_date_flexibility,
                $objShipment->pickup_address,
                $objShipment->pickup_city,
                $objShipment->pickup_zipcode,
                $objShipment->description,
                $objShipment->shipment_end_date,
                (isset($shipment_stops_obj) ? $shipment_stops_obj[0]['dropoff_address'] : '' ),
                (isset($shipment_stops_obj) ? $shipment_stops_obj[0]['dropoff_zip_code'] : '' ),
                (isset($shipment_stops_obj) ? $shipment_stops_obj[0]['dropoff_city'] : '' ),
                (isset($shipment_stops_obj) ? $shipment_stops_obj[0]['request_dropoff_contact_person_phone_number'] : '' ),
                (isset($shipment_stops_obj) ? $shipment_stops_obj[0]['request_dropoff_contact_person_name'] : '' ),
            );
            $messageBody        =  str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
            $this->sendMail($userEmails, $userName, $subject, $messageBody, $settingsEmail, true, $file, $attechmentName);

            $objShipmentOffer = ShipmentOffer::where([
                    "shipment_id"       => $shipment_id,
                ])->first();
            
            $this->send_invoice_to_customer_by_truck_company($objShipment,$objShipmentOffer);
        }
        Session()->flash('success', ucfirst(trans("messages.shipment_invoice_send_successfully")));
        return Redirect()->back();
	}

	public function makePaymentStatus(Request $request,$shipment_id,$status)
	{
		
        $objShipment 						      = Shipment::find($shipment_id);
        $objShipment->payment_status              = $status ;
        $objShipment->save();
        Session()->flash('success', ucfirst(trans("messages.payment_status_is_".$objShipment->payment_status."_successfully")));
        return Redirect()->back();
    }


    public function uploadShipmentStopDocuments(Request $request) {

        $stopId =  $request->stop_id;
        $ShipmentStopObj = ShipmentStop::find($stopId);
        $shipmentRequest = Shipment::find($ShipmentStopObj->shipment_id);
        
        
            if ($request->hasFile('certificate')) {
                if($ShipmentStopObj->request_certificate){
                    $path = Config('constants.GALLERY_MEDIA_IMAGE_ROOT_PATH').basename($ShipmentStopObj->request_certificate);
                    if(file_exists($path)){
                        unlink($path);
                    }
                }
                $file = rand() . '_certificate.' . $request->certificate->getClientOriginalExtension();
                $request->file('certificate')->move(Config('constants.GALLERY_MEDIA_IMAGE_ROOT_PATH'), $file);
                $ShipmentStopObj->request_certificate           = $file;
                $ShipmentStopObj->request_certificate_by_driver = 1;
                $ShipmentStopObj->save();
            }


            if($request->hasFile('signature')) {
                   
                if($ShipmentStopObj->request_digital_signature){
                    $path = Config('constants.SIGNATURE_IMAGES_ROOT_PATH').basename($ShipmentStopObj->request_digital_signature);
                    if(file_exists($path)){
                        unlink($path);
                    }
                }

                    $fileName = time() . '_signature.' . $request->signature->getClientOriginalExtension();
                    $request->file('signature')->move(Config('constants.SIGNATURE_IMAGES_ROOT_PATH'), $fileName);
                    $ShipmentStopObj->request_digital_signature = $fileName;
            }

        $ShipmentStopObj->save();

        Session()->flash('success', ucfirst(trans("messages.certificate_signature_submitted")));
        return Redirect()->route($this->model . ".show",[$shipmentRequest->request_number,"tabs=list_of_stops_documents"]);
	}

    public function shipmentRequestCertificateSkip(Request $request, $stop_id){
        $stopId = '';
        if($stop_id){
            $stopId = base64_decode($stop_id);
            
            $shipmentStop = ShipmentStop::with('shipmentDetails')->find($stopId);

            if($shipmentStop->request_certificate == null && $shipmentStop->request_digital_signature == null){
                $shipmentStop->request_certificate_skip = 1;
                $shipmentStop->request_signature_skip = 1;
            }elseif($shipmentStop->request_certificate != null && $shipmentStop->request_digital_signature == null){
                $shipmentStop->request_certificate_skip = 0;
                $shipmentStop->request_signature_skip = 1;
            }else if($shipmentStop->request_certificate == null && $shipmentStop->request_digital_signature != null){
                $shipmentStop->request_certificate_skip = 1;
                $shipmentStop->request_signature_skip = 0;
            }

            if($shipmentStop->save()){
                session()->flash('flash_notice', trans('messages.document_certificate_skipped'));
                return redirect()->route('shipment-request.show', $shipmentStop->shipmentDetails->request_number . '?tabs=' .$request->tabs);
            }
        }else{
            session()->flash('error', trans('messages.something_went_wrong'));
            return redirect()->back();
        }
    }
}
