<?php
namespace App\Http\Controllers\api\company\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCompanyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Shipment;
use App\Models\ShipmentOffer;
use App\Models\ShipmentOfferRequestRejected;
use App\Models\Truck;
use App\Models\Chat;
use App\Models\TruckType;
use App\Models\TruckTypeDescription;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use App\Models\ShipmentStop;
use App\Models\ShipmentDriverSchedule;
use App\Models\TruckCompanySubscription;

use Cache, Cookie, Input, Mail, Response, Session, URL, App, Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\NotificationAction; 
use App\Models\NotificationTemplate; 
use App\Models\NotificationTemplateDescription; 


class ShippingRequestsController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function newRequests(Request $request) {
        
        if(Auth::guard('api')->user()->is_approved == 0){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.please_wait_for_approval");
            $response["data"]                   =   (object)array();
            return response()->json($response);
		}else if(Auth::guard('api')->user()->is_approved == 2){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.account_rejected");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }

        $checkTruckPlan = TruckCompanySubscription::where('truck_company_id', Auth::guard('api')->user()->id)->where('status', 'activate')->count();
        
        if($checkTruckPlan == 0){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.you_have_no_active_plans");
            $response["data"]                   =   (object)array();
            return response()->json($response);
		}

        $truckTypeIds = Truck::where('truck_company_id',Auth::guard('api')->user()->id)
        ->groupBy('type_of_truck')
        ->pluck('type_of_truck')
        ->toArray();

		$DB					=	Shipment::query();
        $DB->leftjoin('users', 'users.id' , 'shipments.customer_id');
        $DB->whereIn('shipment_type',$truckTypeIds);
        $DB->select(
            'shipments.request_type',
            'shipments.shipment_type',
            'shipments.id',
            "shipments.request_number",
            "users.name as applicant_name",
            "shipments.pickup_city as pickup_address",
            "shipments.created_at",
            "shipments.request_date",
            "shipments.request_date_flexibility",
            DB::raw('DATE_ADD(shipments.request_date, INTERVAL shipments.request_date_flexibility DAY) as increased_request_date'),
    
        );

        $shipment_offer_request_rejected =   DB::table("shipment_offer_request_rejected")->where("customer_id",Auth::guard('api')->user()->id)->pluck("shipment_id")->toArray();
        $shipment_offer =   DB::table("shipment_offers")->where("truck_company_id",Auth::guard('api')->user()->id)->pluck("shipment_id")->toArray();
        $shipment_offer_request_rejected = array_merge($shipment_offer,$shipment_offer_request_rejected);

        $DB->whereRaw('
            (DATE_ADD(shipments.request_date, INTERVAL shipments.request_date_flexibility DAY)) > "'.now().'" 
        ');
        $DB->whereIn('shipments.status',["new", "offers"]);
        $DB->whereNotIn('shipments.id',$shipment_offer_request_rejected);

        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        if(!$results->isEmpty()){
            foreach($results as &$item){
                $item->created_date = date(config("Reading.date_format"),strtotime($item->created_at));
                $item->request_date = date(config("Reading.date_format"),strtotime($item->request_date));

                $shipmentStopList = DB:: table("shipment_stops")->where('shipment_id',$item->id)->get();
                if($shipmentStopList->count()>1){
                    $item->dropoff_address = trans('messages.multiple_destinations');
                }else{
                    foreach($shipmentStopList as $ShipmentStop ){
                        $item->dropoff_address = $ShipmentStop->dropoff_city;
                        break;
                    }
                }

                if($item->request_type != "" && $item->request_type != 0){
                    $item->request_type_name = DB::table("truck_type_descriptions")
                        ->where("language_id",getAppLocaleId())
                        ->where("parent_id",$item->request_type)
                        ->value("name");
                }else {
                    if($item->shipment_type != ""){
                        $item->request_type_name = DB::table("truck_type_descriptions")->where("language_id",getAppLocaleId())->where("parent_id",$item->shipment_type)->value("name");
                    }else {
                        $item->request_type_name = "";
                    }
                }
            }
        }
        

        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        return response()->json($response);
	}

    public function previousRequests(Request $request) {
        $user_id    =   Auth::guard('api')->user()->id;
		$DB					=	Shipment::query();
        $DB->leftjoin('users', 'users.id' , 'shipments.customer_id');
        $DB->select(
            'shipments.id',
            "shipments.request_number",
            "shipments.request_type",      
            "shipments.shipment_type",
            "users.name as applicant_name",
            "shipments.pickup_city as pickup_address",
            "shipments.created_at",
            "shipments.request_date",
            DB::raw("(select price from shipment_offers where shipment_id=shipments.id and truck_company_id= ".$user_id.") as offer_price"),
            DB::raw("(select status from shipment_offers where shipment_id=shipments.id and truck_company_id= ".$user_id.") as status"),
            DB::raw("(select extra_time_price from shipment_offers where shipment_id=shipments.id and shipment_offers.truck_company_id = ".Auth::guard('api')->user()->id." order by id asc limit 1) as extra_time_price"),
        );

        
        $shipment_offer_request_rejected =   DB::table("shipment_offer_request_rejected")->where("customer_id",Auth::guard('api')->user()->id)->pluck("shipment_id")->toArray();
        $DB->whereIn('shipments.id',$shipment_offer_request_rejected);

        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        if(!$results->isEmpty()){
            foreach($results as &$item){
                $item->status       = trans('messages.rejected');
                $item->created_date = date(config("Reading.date_format"),strtotime($item->created_at));
                $item->request_date = date(config("Reading.date_format"),strtotime($item->request_date));
                $shipmentStopList = DB:: table("shipment_stops")->where('shipment_id',$item->id)->get();
                if($shipmentStopList->count()>1){
                    $item->dropoff_address = trans('messages.multiple_destinations');
                }else{
                    foreach($shipmentStopList as $ShipmentStop ){
                        $item->dropoff_address = $ShipmentStop->dropoff_city;
                        break;
                    }
                }
                if($item->request_type != "" && $item->request_type != 0){
                    $item->request_type_name = DB::table("truck_type_descriptions")
                        ->where("language_id",getAppLocaleId())
                        ->where("parent_id",$item->request_type)
                        ->value("name");

                    }else {
                        if($item->shipment_type != ""){
                            $item->request_type_name = DB::table("truck_type_descriptions")->where("language_id",getAppLocaleId())->where("parent_id",$item->shipment_type)->value("name");
                        }else {
                            $item->request_type_name = "";
                        }
                    }
            }
        }
        
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        
        return response()->json($response);
	}

    public function waitingRequests(Request $request) {
        $user_id    =   Auth::guard('api')->user()->id;
		$DB					=	Shipment::query();
        $DB->leftjoin('users', 'users.id' , 'shipments.customer_id');
        $DB->select(
            'shipments.id',
            "shipments.request_number",
            "shipments.request_type",
            "shipments.shipment_type",
            "users.name as applicant_name",
            "shipments.pickup_city as pickup_address",
            "shipments.created_at",
            "shipments.request_date",
            DB::raw("(select price from shipment_offers where shipment_id=shipments.id and truck_company_id= $user_id) as offer_price"),
            DB::raw("(select status from shipment_offers where shipment_id=shipments.id and truck_company_id=$user_id) as status"),
            DB::raw("(select extra_time_price from shipment_offers where shipment_id=shipments.id and shipment_offers.truck_company_id = ".Auth::guard('api')->user()->id." order by id asc limit 1) as extra_time_price"),
        );


        $shipment_offer_request_rejected =   DB::table("shipment_offer_request_rejected")->where("customer_id",Auth::guard('api')->user()->id)->pluck("shipment_id")->toArray();
        $shipment_offers =   DB::table("shipment_offers")->where("truck_company_id",Auth::guard('api')->user()->id)->pluck("shipment_id")->toArray();

        $DB->whereIn('shipments.status',[
            "offers",
            "offer_chosen"
        ]);
        
        $DB->whereIn('shipments.id',$shipment_offers);
        $DB->whereNotIn('shipments.id',$shipment_offer_request_rejected);
        $DB->whereRaw('
            (DATE_ADD(shipments.request_date, INTERVAL shipments.request_date_flexibility DAY)) > "'.now().'" 
        ');
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        if(!$results->isEmpty()){
            foreach($results as &$item){
                $item->created_date = date(config("Reading.date_format"),strtotime($item->created_at));
                $item->request_date = date(config("Reading.date_format"),strtotime($item->request_date));

                $shipmentStopList = DB:: table("shipment_stops")->where('shipment_id',$item->id)->get();
                if($shipmentStopList->count()>1){
                    $item->dropoff_address = trans('messages.multiple_destinations');
                }else{
                    foreach($shipmentStopList as $ShipmentStop ){
                        $item->dropoff_address = $ShipmentStop->dropoff_city;
                        break;
                    }
                }

                if($item->request_type != "" && $item->request_type != 0){
                    $item->request_type_name = DB::table("truck_type_descriptions")
                        ->where("language_id",getAppLocaleId())
                        ->where("parent_id",$item->request_type)
                        ->value("name");
                }else {
                    if($item->shipment_type != ""){
                        $item->request_type_name = DB::table("truck_type_descriptions")->where("language_id",getAppLocaleId())->where("parent_id",$item->shipment_type)->value("name");
                    }else {
                        $item->request_type_name = "";
                    }
                }
                if($item->status == "waiting"){
                    $item->request_status = trans("messages.waiting");
                    $item->status = "waiting";
                }else if($item->status == "selected"){
                    $item->request_status = trans("messages.Won");
                    $item->status = "won";
                }else if($item->status == "rejected"){
                    $item->request_status = trans("messages.rejected");
                    $item->status = "rejected";
                }
            }
        }
        
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        return response()->json($response);
	}

    public function shipmentRequests(Request $request) {
        $user_id    =   Auth::guard('api')->user()->id;
		$DB					=	Shipment::query();
        $DB->leftjoin('users', 'users.id' , 'shipments.customer_id');
        $DB->select(
            'shipments.id',
            "shipments.request_number",
            "shipments.request_number",
            "users.name as applicant_name",
            "shipments.pickup_city as pickup_address",
            "shipments.created_at",
            "shipments.request_date",
            DB::raw("(select price from shipment_offers where shipment_id=shipments.id and truck_company_id=$user_id) as offer_price"),
            DB::raw("(select status from shipment_offers where shipment_id=shipments.id and truck_company_id=$user_id) as status")
        );


    
        $shipment_offers =   DB::table("shipment_offers")->where("truck_company_id",Auth::guard('api')->user()->id)->pluck("shipment_id")->toArray();
        $DB->where('shipments.status',"shipment");
        $DB->whereIn('shipments.id',$shipment_offers);

        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        if(!$results->isEmpty()){
            foreach($results as &$item){
                $item->created_date = date(config("Reading.date_format"),strtotime($item->created_at));
                $item->request_date = date(config("Reading.date_format"),strtotime($item->request_date));

                $shipmentStopList = DB:: table("shipment_stops")->where('shipment_id',$item->id)->get();
                if($shipmentStopList->count()>1){
                    $item->dropoff_address = trans('messages.multiple_destinations');
                }else{
                    foreach($shipmentStopList as $ShipmentStop ){
                        $item->dropoff_address = $ShipmentStop->dropoff_city;
                        break;
                    }
                }

                if($item->request_type != "" && $item->request_type != 0){
                    $item->request_type_name = DB::table("truck_type_descriptions")
                        ->where("language_id",getAppLocaleId())
                        ->where("parent_id",$item->request_type)
                        ->value("name");
                }else {
                    if($item->shipment_type != ""){
                        $item->request_type_name = DB::table("truck_type_descriptions")->where("language_id",getAppLocaleId())->where("parent_id",$item->shipment_type)->value("name");
                    }else {
                        $item->request_type_name = "";
                    }
                }
                    $item->status = trans("messages.$item->status");
               
            }
        }
        
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        return response()->json($response);
	}

    public function viewShipmentRequests(Request $request, $shipment_id) {
        $shipment = Shipment::where("id",$shipment_id)
		    ->select(
                "id",
                "customer_id",
                "request_number",
                "status",
                "request_type",
                "shipment_type",
                "request_date",
                "request_time",
                "request_date_flexibility",
                "pickup_city",
                "pickup_zipcode",
                "description",
                "shipment_end_date",
                "delivery_note",
                "created_at",
                "pickup_address",
                "company_latitude",
                "company_longitude",
                "request_pickup_details",
                DB::raw("(select extra_time_price from shipment_offers where shipment_id=shipments.id and shipment_offers.truck_company_id = ".Auth::guard('api')->user()->id." order by id asc limit 1) as extra_time_price"),
            )
		->with(
			[
                
                'shipmentRatingReviews',
                'customer' => function($query) {
                    $query->select(
                        "id",
                        "id as active_id",
                        "truck_company_id",
                        "system_id",
                        "user_role_id",
                        "customer_type",
                        "name",
                        "email",
                        "phone_number",
                        "image",
                        "location",
                        "language",
                        "is_active",
                        "is_approved",
                        "is_deleted",
                        "is_online",
                        "forgot_password_validate_string",
                        "last_activity_date_time",
                        "created_at",
                        "updated_at",
                    );
				},
                'customer.userCompanyInformation',
				'SelectedShipmentOffers' => function($query) {
					$query->where(['truck_company_id' => Auth::guard('api')->user()->id]);
					$query->where(['is_deleted' => '0']);
                    $query->select(
                        "id",
                        "system_id",
                        "shipment_id",
                        "truck_company_id",
                        "price",
                        "extra_time_price",
                        "duration_in_hours",
                        "duration",
                        "description",
                        "payment_condition",
                        "truck_id",
                        "status",
                        "created_at",
                        "request_offer_date"
                    );
				},
                'SelectedShipmentOffers.TruckDetail',
                'shipmentDriverScheduleDetails' => function($query) {
					$query->where(['truck_company_id' => Auth::guard('api')->user()->id]);
				},
                'shipmentDriverScheduleDetails.truckDriver',
				'ShipmentStop' => function($query) {
					$query->select(
                        "id",
                        "shipment_id",
                        "dropoff_address",
                        "dropoff_zip_code",
                        "dropoff_city",
                        "request_dropoff_details",
                        "request_dropoff_contact_person_name",
                        "request_certificate_type",
                        "dropoff_latitude",
                        "dropoff_longitude",
                        "request_certificate",
                        "request_digital_signature",
                        "request_dropoff_contact_person_phone_number",
                    );
				},
				'ShipmentStop.ShipmentStopAttchements' => function($query) {
					$query->select(
                        "id",
                        "shipment_id",
                        "shipment_stops_id",
                        "attachment",
                    );
				},
				'ShipmentPrivateCustomerExtraInformations',
				'shipment_attchement'
			]
		)
		->whereIn('status', ['shipment','end','cancelled'])
		->first();
        $keyvar = array("B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $track_location = array(
             array(
               
                "type"      => "drop",
                "drop"      => "A",
                "lat"       => $shipment->company_latitude,
                "lng"       => $shipment->company_longitude
            ) 
        ); 

        if($shipment->ShipmentStop->count()){
            foreach($shipment->ShipmentStop as $key =>  &$stops){
                $stops->shipment_stop_sr =  $keyvar[$key];
                if(!($shipment->SelectedShipmentOffers || ( $shipment->SelectedShipmentOffers && $shipment->SelectedShipmentOffers->status == "rejected") )){
                    $stops->request_certificate = null;
                    $stops->request_digital_signature = null;
                }
                $track_location[] = array(
                    "type"      => "drop",
                    "drop"      => $keyvar[$key],
                    "lat"       => $stops->dropoff_latitude,
                    "lng"       => $stops->dropoff_longitude
                ); 
            }
        }

		
		if($shipment == null){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   "";
            $response["data"]                   =   (object)array();
            return response()->json($response);
		}
        $shipment->track_location = $track_location;


        if($shipment->SelectedShipmentOffers){
            $shipment->SelectedShipmentOffers->request_offer_date = $shipment->SelectedShipmentOffers->request_offer_date ? date(Config('Reading.date_format'),strtotime($shipment->SelectedShipmentOffers->request_offer_date)) : NULL ;

            $shipment->SelectedShipmentOffers->TruckDetail->type_of_truck_name = TruckTypeDescription::where(
                [
                    "parent_id"=>$shipment->SelectedShipmentOffers->TruckDetail->type_of_truck,
                    "language_id"=>getAppLocaleId()

                ]
            )->first()->name;
            if($shipment->SelectedShipmentOffers->duration<=24){
                $shipment->SelectedShipmentOffers->duration_formated_hours = Carbon::createFromTime($shipment->SelectedShipmentOffers->duration, 0, 0)->format('H:i');
            }else{
                $shipment->SelectedShipmentOffers->duration_formated_hours = $shipment->SelectedShipmentOffers->duration;
            }
            $shipment->SelectedShipmentOffers->duration_formated_hours = Carbon::createFromTime($shipment->SelectedShipmentOffers->duration, 0, 0)->format('H:i');
        }
        $shipment->created_date = date(config("Reading.date_format"),strtotime($shipment->created_at));
        $shipment->request_start_date = Carbon::createFromFormat('Y-m-d', $shipment->request_date)->subDay($shipment->request_date_flexibility);

        $shipment->request_end_date = Carbon::createFromFormat('Y-m-d', $shipment->request_date)->addDay($shipment->request_date_flexibility);

        $nextDay = Carbon::now()->addDay(1);

        if($shipment->request_start_date->lt($nextDay)){
            $shipment->request_start_date = $nextDay;
        }

        if($shipment->request_end_date->lt($nextDay)){
            $shipment->request_end_date = $nextDay;
        }
        
        $shipment->request_start_date = $shipment->request_start_date->format(config("Reading.date_format"));
        $shipment->request_end_date   = $shipment->request_end_date->format(config("Reading.date_format"));


        $shipment->request_date = date(config("Reading.date_format"),strtotime($shipment->request_date));
        $shipment->shipmentDriverScheduleDetails->start_time = Carbon::createFromFormat('Y-m-d H:i:s', $shipment->shipmentDriverScheduleDetails->start_time)->format(Config('Reading.date_format'));
        if($shipment->shipmentDriverScheduleDetails->time_type != 0){
            if($shipment->shipmentDriverScheduleDetails->time_type == 21){
                $shipment->shipmentDriverScheduleDetails->start_time .= " ".trans("messages.afternoon"); 
            }elseif($shipment->shipmentDriverScheduleDetails->time_type == 20){
                $shipment->shipmentDriverScheduleDetails->start_time .= " ".trans("messages.morning"); 
            }elseif($shipment->shipmentDriverScheduleDetails->time_type == 22){
                $shipment->shipmentDriverScheduleDetails->start_time .= " ".trans("messages.evening"); 
            }
        }

        if($shipment->shipmentDriverScheduleDetails->shipment_actual_start_time){
            $shipment->shipmentDriverScheduleDetails->shipment_actual_start_time = Carbon::createFromFormat('Y-m-d H:i:s', $shipment->shipmentDriverScheduleDetails->shipment_actual_start_time)->format(Config('Reading.date_time_format'));
        }
        if($shipment->shipmentDriverScheduleDetails->shipment_actual_end_time){
            $shipment->shipmentDriverScheduleDetails->shipment_actual_end_time = Carbon::createFromFormat('Y-m-d H:i:s', $shipment->shipmentDriverScheduleDetails->shipment_actual_end_time)->format(Config('Reading.date_time_format'));
        }
        if($shipment->request_type != "" && $shipment->request_type != 0){
            $shipment->request_type_name = DB::table("truck_type_descriptions")
            ->where("language_id",getAppLocaleId())
            ->where("parent_id",$shipment->request_type)
            ->value("name");
        }else {
            if($shipment->shipment_type != ""){
                $shipment->request_type_name = DB::table("truck_type_descriptions")->where("language_id",getAppLocaleId())->where("parent_id",$shipment->shipment_type)->value("name");
            }else {
                $shipment->request_type_name = "";
            }
        }
        $shipment->shipment_type_name  = $shipment->request_type_name;

        if($shipment->status == 'shipment'){
            $shipment->request_status                   =   trans("messages.shipment");
        }else if($shipment->status == 'end'){
            $shipment->request_status                   =   trans("messages.end");
        }else if($shipment->status == 'cancelled'){
            $shipment->request_status                   =   trans("messages.cancelled");
        }
        if($shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->shipment_status == "start" && $shipment->status == "shipment"){
            $shipment->status = 'active';
            $shipment->request_status = trans("messages.active");
        }

        if($shipment->customer->customer_type == "private"){
            $shipment->request_pickup_details = json_decode($shipment->request_pickup_details);
        }else{
            $shipment->request_pickup_details = json_decode($shipment->request_pickup_details);
            $questionnaireArr = array();
            foreach($shipment->request_pickup_details as $key => $value){
                $questionnaireArray = array();
                $truck_type_question_descriptions = DB::table('truck_type_question_descriptions')->where('parent_id',$key)->where('language_id',getAppLocaleId())->first();
                $questionnaireArray['qus'] = $truck_type_question_descriptions->name;
                if(is_array($value)){
                    $opyionvalueStr = '';
                    $questionnaireArray['ans'] = '';
                    foreach($value as $valueKey => $opyionvalue){
                        $input_description_array = explode(",",($truck_type_question_descriptions->input_description ?? ""));
                        $questionnaireArray['ans'] .= (($opyionvalueStr == '' ? '' : ',')." ".$input_description_array[$opyionvalue]);
                        $opyionvalueStr = 'in'; 
                    }
                }else{
                    $questionnaireArray['ans'] = $value;
                }
                $questionnaireArr[] = $questionnaireArray;
            }
           
            $shipment['questionnaire'] = $questionnaireArr; 

            unset($shipment->request_pickup_details);
        }


        if($shipment->customer->customer_type == "business"){
            $shipment->customer->name       = UserCompanyInformation::where("user_id",$shipment->customer->id)->first()->company_name;
        }
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $shipment;
        return response()->json($response);
	}

    public function deleteRequests(Request $request){
        
        if(empty($request->shipment_id)){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }
        $shipment_offer_request_rejected =   DB::table("shipment_offer_request_rejected")->where("shipment_id",$request->shipment_id)->where("customer_id",Auth::guard('api')->user()->id)->first();

        if(empty($shipment_offer_request_rejected)){
            $obj                        = new ShipmentOfferRequestRejected;
            $obj->customer_id           = Auth::guard('api')->user()->id;
            $obj->shipment_id           = $request->shipment_id;
            $obj->save();


            $objShipmentOffer = ShipmentOffer::where("shipment_id",$request->shipment_id)
                                ->where("truck_company_id",Auth::guard('api')->user()->id)->first();

            if($objShipmentOffer){

                $objShipment = Shipment::find($request->shipment_id);
                $objShipment->status = "offers";
                $objShipment->save();


                $objShipmentOffer->status = "rejected";
                $objShipmentOffer->save();

                $OffersCount = ShipmentOffer::where(["shipment_id"=>$request->shipment_id,"status"=>"waiting"])->get();
                    if($OffersCount->count()==0){
                        $objShipment->status = "new";
                        $objShipment->save();
                    }

                $this->shipment_rejected_by_company($objShipment,$objShipmentOffer);
            
            }

            $response                           =   array();
            $response["status"]                 =   "success";
            $response["msg"]                    =   trans("messages.Request_has_been_rejected_successfully");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }else {
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }
    }

    public function rejectShipmentRequests(Request $request){
        if(empty($request->shipment_id)){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }
        $shipment_offer_request_rejected =   DB::table("shipment_offer_request_rejected")->where("shipment_id",$request->shipment_id)->where("customer_id",Auth::guard('api')->user()->id)->first();
        if(empty($shipment_offer_request_rejected)){
            $obj                        = new ShipmentOfferRequestRejected;
            $obj->customer_id           = Auth::guard('api')->user()->id;
            $obj->shipment_id           = $request->shipment_id;
            $obj->save();

            $objShipment = Shipment::find($request->shipment_id);
            $objShipment->status = "offers";
            $objShipment->save();
            
            $objShipmentOffer = ShipmentOffer::where("shipment_id",$request->shipment_id)
                ->where("truck_company_id",Auth::guard('api')->user()->id)->first();
            $objShipmentOffer->status = "rejected";
            $objShipmentOffer->save();

            
            $OffersCount = ShipmentOffer::where(["shipment_id"=>$request->shipment_id,"status"=>"waiting"])->get();
            if($OffersCount->count()==0){
                $objShipment->status = "new";
                $objShipment->save();
            }

            $this->shipment_rejected_by_company($objShipment,$objShipmentOffer);
            
            $response                           =   array();
            $response["status"]                 =   "success";
            $response["msg"]                    =   trans("messages.Request_has_been_rejected_successfully");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }else {
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }
    }

    public function approvedShipmentRequests(Request $request){
        if(empty($request->shipment_id)){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }
        $objShipment            = Shipment::find($request->shipment_id);
        if(!$objShipment){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);

        }elseif($objShipment->status == "shipment"){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }

        $objTruck              = Truck::where("id",$request->truck_id)->first();

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

            $response = array();
            $response["status"] = "error";
            $response["msg"] = $responceMessage;
            $response["data"] = (object) array();
            return response()->json($response); 
        }



        


        $shipment_driver_schedule =   ShipmentDriverSchedule::where("shipment_id",$request->shipment_id)->where("truck_company_id",Auth::guard('api')->user()->id)->first();
        if(empty($shipment_driver_schedule)){
            $shipment_offers =   DB::table("shipment_offers")
                ->where("shipment_id",$request->shipment_id)
                ->where("truck_company_id",Auth::guard('api')->user()->id)
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

                            $objShipmentOffer = ShipmentOffer::where("shipment_id",$request->shipment_id)
                                ->where("truck_company_id",Auth::guard('api')->user()->id)->first();
                            $objShipmentOffer->status = "approved_from_company";
                            $objShipmentOffer->truck_id              = $request->truck_id;
                            $objShipmentOffer->save();

                            $ShipmentOffer  =   ShipmentOffer::where("shipment_id",$request->shipment_id)
                            ->where("truck_company_id","!=",Auth::guard('api')->user()->id)
                            ->get();
                            if($ShipmentOffer->count()){
                                $ShipmentOffer = $ShipmentOffer->toArray();
                            }
                    
                            if(!empty($ShipmentOffer)){
                                foreach($ShipmentOffer as $ShipmentOffer_v){
                                    $obj                        = new ShipmentOfferRequestRejected;
                                    $obj->customer_id           = $ShipmentOffer_v['truck_company_id'];
                                    $obj->shipment_id           = $request->shipment_id;
                                    $obj->save();
                                }
                            }

                            ShipmentOffer::where("shipment_id",$request->shipment_id)->where("truck_company_id","!=",Auth::guard('api')->user()->id)->update(array("status"=>"rejected"));


                            $objShipmentDriverSchedule                     = new ShipmentDriverSchedule;
                            $objShipmentDriverSchedule->shipment_id        = $request->shipment_id;
                            $objShipmentDriverSchedule->truck_company_id   = Auth::guard('api')->user()->id;
                            $objShipmentDriverSchedule->driver_id          = $objTruck->driver_id ?? 0;
                            $objShipmentDriverSchedule->truck_id           = $objTruck->id;
                            $objShipmentDriverSchedule->time_type          = $request->time_type ?? 0;


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
                            $response                           =   array();
                            $response["status"]                 =   "success";
                            $response["msg"]                    =   trans("messages.shipment_schedule_successfully");
                            $response["data"]                   =   (object)array();
                            return response()->json($response);

                        }else{
                            $response                           =   array();
                            $response["status"]                 =   "error";
                            $response["msg"]                    =   trans("messages.please_assign_a_driver_to_the_selected_truck_then_try_again");
                            $response["data"]                   =   (object)array();
                            return response()->json($response);    
                        }
                        

                }else{
                    $response                           =   array();
                    $response["status"]                 =   "error";
                    $response["msg"]                    =   trans("messages.invalid_requests");
                    $response["data"]                   =   (object)array();
                    return response()->json($response);
                }
        }else{
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }
    }
    public function applyOffer(Request $request){
        $thisData = $request->all();
        $validator                    =   Validator::make(
            $request->all(), 
            array(
                'duration'                       => "required|lt:24",
                'price'                          => "required|numeric",
                'shipment_id'                    => "required",
                'term_of_payment'                => "required",
                'request_offer_date'             => "required|date_format:d/m/Y",
            ), 
            array(
                "duration.lt"                            => trans("messages.The duration must be less than 24 hours"),
                "duration.required"                      => trans("messages.This field is required"),
                "price.required"                         => trans("messages.This field is required"),
                "type_of_truck.required"                 => trans("messages.This field is required"),
                "truck_id.required"                      => trans("messages.This field is required"),
                "shipment_id.required"                   => trans("messages.This field is required"),
                "shipment_description.required"          => trans("messages.This field is required"),
                "term_of_payment.required"               => trans("messages.This field is required"),
                "request_offer_date.required"            => trans("messages.This field is required"),
            )
        );
        if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{
            $companyInfo = UserCompanyInformation::where('user_id', Auth::guard('api')->user()->id)->first();
            if($companyInfo->company_description == null  || $companyInfo->company_description == ''){
                $response                           =   array();
                $response["status"]                 =   "error";
                $response["msg"]                    =   trans("messages.please_fill_the_company_information_first");
                $response["data"]                   =   (object)array();
                return response()->json($response);
            }

            
            $shipment_offers =   DB::table("shipment_offers")->where("shipment_id",$request->shipment_id)->where("truck_company_id",Auth::guard('api')->user()->id)->first();
            if(empty($shipment_offers)){
                $objShipmentOffer                        = new ShipmentOffer;
                $objShipmentOffer->duration              = $request->duration;
                $objShipmentOffer->request_offer_date    = $request->request_offer_date ? Carbon::createFromFormat('d/m/Y', ($request->request_offer_date))->format('Y-m-d') : NULL;
                $objShipmentOffer->truck_company_id      = Auth::guard('api')->user()->id;
                $objShipmentOffer->shipment_id           = $request->shipment_id;
                $objShipmentOffer->system_id             = 0;
                $objShipmentOffer->price                 = $request->price;
                $objShipmentOffer->extra_time_price      = (!empty($request->extra_time_price) && $request->extra_time_price > 0) ? $request->extra_time_price : 0;
                $objShipmentOffer->description           = $request->shipment_description;
                $objShipmentOffer->payment_condition     = $request->term_of_payment;
                $objShipmentOffer->truck_id              = 0;
                $objShipmentOffer->status                = "waiting";
                $objShipmentOffer->save();

                $system_id  =   100000+$objShipmentOffer->id;
                $objShipmentOffer->system_id = $system_id;
                $objShipmentOffer->save();
                
                $objShipment = Shipment::where("id",$request->shipment_id)->first();
                $objShipment->status = 'offers';
                $objShipment->save();

                $this->new_offer_created_for_customer($objShipment,$objShipmentOffer);

                $response                           =   array();
                $response["status"]                 =   "success";
                $response["msg"]                    =   trans("messages.Offer_applied_successfully");
                $response["data"]                   =   (object)array();
                return response()->json($response);
            }else{
                $response                           =   array();
                $response["status"]                 =   "error";
                $response["msg"]                    =   trans("messages.invalid_requests");
                $response["data"]                   =   (object)array();
                return response()->json($response);
            }
        }
    }

    public function editApplyOffer(Request $request){
        $thisData = $request->all();
        $validator                    =   Validator::make(
            $request->all(), 
            array(
                'duration'                       => "required|lt:24",
                'offer_id'                       => "required|numeric",
                'price'                          => "required|numeric",
                'term_of_payment'                => "required",
                'request_offer_date'             => "required|date_format:d/m/Y",
            ), 
            array(
                "duration.lt"                           => trans("messages.The duration must be less than 24 hours"),
                "duration.required"                      => trans("messages.This field is required"),
                "offer_id.required"                      => trans("messages.This field is required"),
                "price.required"                         => trans("messages.This field is required"),
                "type_of_truck.required"                 => trans("messages.This field is required"),
                "shipment_id.required"                   => trans("messages.This field is required"),
                "shipment_description.required"          => trans("messages.This field is required"),
                "term_of_payment.required"               => trans("messages.This field is required"),
                "request_offer_date.required"            => trans("messages.This field is required"),
            )
        );
        if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{

            $shipment_offers =   DB::table("shipment_offers")->where("id",$request->offer_id)->where("truck_company_id",Auth::guard('api')->user()->id)->first();
            if($shipment_offers){
                    $ShipmentOffer = ShipmentOffer::find($shipment_offers->id);
                    $ShipmentOffer->request_offer_date    = $request->request_offer_date ? Carbon::createFromFormat('d/m/Y', ($request->request_offer_date))->format('Y-m-d') : NULL;
                    $ShipmentOffer->duration              = $request->duration;
                    $ShipmentOffer->price                 = $request->price;
                    $ShipmentOffer->extra_time_price      = (!empty($request->extra_time_price) && $request->extra_time_price > 0) ? $request->extra_time_price : 0;
                    $ShipmentOffer->description           = $request->shipment_description;
                    $ShipmentOffer->payment_condition     = $request->term_of_payment;
                    $ShipmentOffer->save();

                    $objShipment = Shipment::where("id",$ShipmentOffer->shipment_id)->first();
                    $this->update_offer_for_customer($objShipment,$ShipmentOffer);

                    $response                           =   array();
                    $response["status"]                 =   "success";
                    $response["msg"]                    =   trans("messages.update_offer_successfully");
                    $response["data"]                   =   (object)array();
                    return response()->json($response);
               
            }else{
                $response                           =   array();
                $response["status"]                 =   "error";
                $response["msg"]                    =   trans("messages.invalid_requests");
                $response["data"]                   =   (object)array();
                return response()->json($response);
            }
        }
    }

    public function viewRequests(Request $request, $shipment_id) {
        $shipment = Shipment::where("id",$shipment_id)
		    ->select(
                "id",
                "customer_id",
                "request_number",
                "status",
                "request_type",
                "shipment_type",
                "request_date",
                "request_time",
                "request_date_flexibility",
                "pickup_city",
                "pickup_zipcode",
                "description",
                "shipment_end_date",
                "delivery_note",
                "created_at",
                "pickup_address",
                "request_pickup_details",
                DB::raw("(select extra_time_price from shipment_offers where shipment_id=shipments.id and shipment_offers.truck_company_id = ".Auth::guard('api')->user()->id." order by id asc limit 1) as extra_time_price"),

            )
		->with(
			[
                'customer' => function($query) {
                    $query->select(
                        "id",
                        "id as active_id",
                        "truck_company_id",
                        "system_id",
                        "user_role_id",
                        "customer_type",
                        "name",
                        "email",
                        "phone_number",
                        "image",
                        "location",
                        "language",
                        "is_active",
                        "is_approved",
                        "is_deleted",
                        "is_online",
                        "forgot_password_validate_string",
                        "last_activity_date_time",
                        "created_at",
                        "updated_at",
                    );
				},
				'SelectedShipmentOffers' => function($query) {
					$query->where(['truck_company_id' => Auth::guard('api')->user()->id]);
					$query->where(['is_deleted' => '0']);
                    $query->select(
                        "id",
                        "system_id",
                        "shipment_id",
                        "truck_company_id",
                        "price",
                        "extra_time_price",
                        "duration_in_hours",
                        "duration",
                        "description",
                        "payment_condition",
                        "truck_id",
                        "status",
                        "created_at",
                        "request_offer_date"
                    );
				},
                'SelectedShipmentOffers.TruckDetail',
				'ShipmentStop' => function($query) {
					$query->select(
                        "id",
                        "shipment_id",
                        "dropoff_address",
                        "dropoff_zip_code",
                        "dropoff_city",
                        "request_dropoff_details",
                        "request_dropoff_contact_person_name",
                        "request_certificate_type",
                        "request_certificate",
                        "request_digital_signature",
                        "request_dropoff_contact_person_phone_number",
                    );
				},
				'ShipmentStop.ShipmentStopAttchements' => function($query) {
					$query->select(
                        "id",
                        "shipment_id",
                        "shipment_stops_id",
                        "attachment",
                    );
				},
				'ShipmentPrivateCustomerExtraInformations',
				'shipment_attchement'
			]
		)
		->whereIn('status', ['new','offers','offer_chosen'])
		->orderBy('request_date', 'desc')
		->first();
        //////
        $receiver_id 	= Auth::guard('api')->user()->id;
        $modelId 		= $shipment ?->customer->id;

        $chat_intiate_status = Chat::where(function ($query) use($modelId,$receiver_id){
            $query->orWhere(function ($query) use($modelId,$receiver_id){
                $query->where("chats.sender_id",$modelId);
                $query->where("chats.receiver_id",$receiver_id);
            });
            $query->orWhere(function ($query) use($modelId,$receiver_id){
                $query->where("chats.receiver_id",$modelId);
                $query->where("chats.sender_id",$receiver_id);
            });
        })
        ->where('channel_id','0')
        ->select('chats.*')
        ->orderBy('chats.id','DESC')
        ->first();
        
        if(!empty($chat_intiate_status)){
            if(!empty($shipment) && !empty($shipment->customer)){
              $shipment->customer->chat_option = 1;
            } 
        }else{
            if(!empty($shipment) && !empty($shipment->customer)){
              $shipment->customer->chat_option = 0;
            }
        }
        //////
        

		if($shipment == null){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   "";
            $response["data"]                   =   (object)array();
            return response()->json($response);
		}

        if($shipment->SelectedShipmentOffers){
            $shipment->SelectedShipmentOffers->request_offer_date = $shipment->SelectedShipmentOffers->request_offer_date ? date(config("Reading.date_format"),strtotime($shipment->SelectedShipmentOffers->request_offer_date)) : NULL;
            if($shipment->SelectedShipmentOffers->TruckDetail){
                $shipment->SelectedShipmentOffers->TruckDetail->type_of_truck_name = TruckTypeDescription::where(
                    [
                        "parent_id"=>$shipment->SelectedShipmentOffers->TruckDetail->type_of_truck,
                        "language_id"=>getAppLocaleId()

                    ]
                )->first()->name;
            }
        }
        $shipment->created_date = date(config("Reading.date_format"),strtotime($shipment->created_at));
        
        $shipment->request_start_date = Carbon::createFromFormat('Y-m-d', $shipment->request_date)->subDay($shipment->request_date_flexibility);
        $shipment->request_end_date = Carbon::createFromFormat('Y-m-d', $shipment->request_date)->addDay($shipment->request_date_flexibility);
        
        $shipment->request_date = date(config("Reading.date_format"),strtotime($shipment->request_date));
        if($shipment->request_type != "" && $shipment->request_type != 0){
            $shipment->request_type_name = DB::table("truck_type_descriptions")
            ->where("language_id",getAppLocaleId())
            ->where("parent_id",$shipment->request_type)
            ->value("name");

        }else {

            if($shipment->shipment_type != ""){

                $shipment->request_type_name = DB::table("truck_type_descriptions")->where("language_id",getAppLocaleId())->where("parent_id",$shipment->shipment_type)->value("name");
            }else {
                $shipment->request_type_name = "";
            }
        }
        $shipment->shipment_type_name  = $shipment->request_type_name;
        $shipment->request_status                   =   "";
        $shipment->is_show_delete_button            =   0;
        $shipment->is_show_apply_offer_button       =   0;
        $shipment->is_show_approved_offer_button    =   0;
        $shipment->is_show_reject_offer_button      =   0;
        if($shipment->status == "new" || $shipment->status == "offers"){
            $shipment_offer_request_rejected        =   DB::table("shipment_offer_request_rejected")->where("customer_id",Auth::guard('api')->user()->id)->where("shipment_id",$shipment->id)->first();
            if(!empty($shipment_offer_request_rejected)){
                $shipment->request_status               =   trans("messages.rejected");
            }else {
                $shipment_offers        =   DB::table("shipment_offers")->where("truck_company_id",Auth::guard('api')->user()->id)->where("shipment_id",$shipment->id)->first();
                if(empty($shipment_offers)){
                    $shipment->status                       =   'new';
                    $shipment->request_status               =   trans("messages.new");
                    $shipment->is_show_delete_button        =   1;
                    $shipment->is_show_apply_offer_button   =   1;
                }else{
                    if($shipment_offers->status == "waiting"){
                        $shipment->request_status               =   trans("messages.waiting");
                        $shipment->is_show_delete_button            =   1;
                        
                    }else if($shipment_offers->status == "selected"){
                        $shipment->request_status                   =   trans("messages.Won");

                        $shipment->is_show_approved_offer_button    =   1;
                        $shipment->is_show_reject_offer_button      =   1;
                    }

                }
            }
        }else {
            $shipment_offers        =   DB::table("shipment_offers")->where("truck_company_id",Auth::guard('api')->user()->id)->where("shipment_id",$shipment->id)->first();
            if($shipment_offers && $shipment_offers->status == "waiting"){
                $shipment->request_status               =   trans("messages.waiting");
                $shipment->is_show_delete_button        =   1;
            }else if($shipment_offers && $shipment_offers->status == "selected"){
                $shipment->request_status               =   trans("messages.Won");
                $shipment->is_show_approved_offer_button    =   1;
                $shipment->is_show_reject_offer_button      =   1;
            }

            $shipment_offer_request_rejected        =   DB::table("shipment_offer_request_rejected")->where("customer_id",Auth::guard('api')->user()->id)->where("shipment_id",$shipment->id)->first();
            if(!empty($shipment_offer_request_rejected)){
                $shipment->is_show_approved_offer_button    =   0;
                $shipment->is_show_reject_offer_button      =   0;
                $shipment->is_show_delete_button            =   0;
            }
        } 

        if($shipment->customer->customer_type == "private"){
            $shipment->request_pickup_details = json_decode($shipment->request_pickup_details);
        }else{
            $shipment->request_pickup_details = json_decode($shipment->request_pickup_details);
            $questionnaireArr = array();
            foreach($shipment->request_pickup_details as $key => $value){
                $questionnaireArray = array();
                $truck_type_question_descriptions = DB::table('truck_type_question_descriptions')->where('parent_id',$key)->where('language_id',getAppLocaleId())->first();
                $questionnaireArray['qus'] = $truck_type_question_descriptions->name ?? "";
                if(is_array($value)){
                    $opyionvalueStr = '';
                    $questionnaireArray['ans'] = '';
                    foreach($value as $valueKey => $opyionvalue){
                        $input_description_array = explode(",",($truck_type_question_descriptions->input_description ?? ""));

                        $questionnaireArray['ans'] .= (($opyionvalueStr == '' ? '' : ',')." ".($input_description_array[$opyionvalue] ?? "" ));
                        $opyionvalueStr = 'in'; 
                    }
                }else{
                    $questionnaireArray['ans'] = $value;
                }
                $questionnaireArr[] = $questionnaireArray;
            }
           
            $shipment['questionnaire'] = $questionnaireArr; 

            unset($shipment->request_pickup_details);
        }

        $shipment->request_time_name = DB::table("lookup_discriptions")->where("language_id",getAppLocaleId())->where("parent_id",$shipment->request_time)->value("code");
        if($shipment->request_time_name == null){
            $shipment->request_time_name = trans("messages.please_select_time");
        }
        if($shipment->customer->customer_type == "business"){
            $shipment->customer->name       = UserCompanyInformation::where("user_id",$shipment->customer->id)->first()->company_name;
        }
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $shipment;
        return response()->json($response);
	}

    public function shipmentTrucksList(Request $request) {
        $validator                    =   Validator::make(
            $request->all(), 
            array(
                'shipment_id'                => "required",
            ), 
            array(
                "shipment_id.required"                   => trans("messages.The_shipment_id_field_is_required"),
            )
        );
        if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }
        $shipment                 =    Shipment::where('shipments.id', $request->shipment_id)
            ->with(
                [
                    'TruckTypeDescriptions' => function($query) {
                        $query->where(['language_id' => getAppLocaleId()]);
                    },
                ]
            )->first();

        $results = array();
        $results["truck"]               =   DB::table("trucks")
            ->select("id","truck_system_number","type_of_truck","truck_company_id","driver_id")
            ->where("is_active",1)
            ->where("is_deleted",0)
            ->where("truck_company_id",Auth::guard('api')->user()->id);

        $results["shipment"]               =   [];

        if( $shipment && $shipment->shipment_type>0){
            $results["shipment"]['truck_type_name'] = $shipment->TruckTypeDescriptions->name;
            $results["shipment"]['request_date_flexibility'] = $shipment->request_date_flexibility;
            $results["shipment"]['request_date'] = Carbon::createFromFormat('Y-m-d', $shipment->request_date);
            $results["shipment"]['request_start_date'] = Carbon::createFromFormat('Y-m-d', $shipment->request_date)->subDay($shipment->request_date_flexibility);
            $results["shipment"]['request_end_date'] = Carbon::createFromFormat('Y-m-d', $shipment->request_date)->addDay($shipment->request_date_flexibility);


            $nextDay = Carbon::now()->addDay(1);

            if($results["shipment"]['request_start_date']->lt($nextDay)){
                $results["shipment"]['request_start_date'] = $nextDay;
            }
    
            if($results["shipment"]['request_end_date']->lt($nextDay)){
                $results["shipment"]['request_end_date'] = $nextDay;
            }


            $results["truck"]           =   $results["truck"]->where("type_of_truck",$shipment->shipment_type);
        }
        $results["truck"]               =   $results["truck"]->get()->toArray();
        foreach($results["truck"] as &$truck){

            $truck->selectedDriver = User::find($truck->driver_id);
            if($truck->selectedDriver)
            $truck->selectedDriver = array(
                "id" => $truck->selectedDriver->id,
                "name" => $truck->selectedDriver->name,
            );
        }

        $results['UserCompanyInformation']         = UserCompanyInformation::where("user_id",Auth::guard('api')->user()->id)
        ->select("company_trms")
        ->first();



        $free_driver = User::where("users.truck_company_id",Auth::guard('api')->user()->id)
        ->select("users.id","users.name","users.user_role_id")
        ->leftjoin('trucks','trucks.driver_id','users.id');
        if($request->truck_id){
            $trucks = DB::table('trucks')->where("id",$request->truck_id)->first();
            $free_driver = $free_driver->whereRaw("trucks.driver_id IS NULL or trucks.driver_id = ".$trucks->driver_id);
        }else{
            $free_driver = $free_driver->whereRaw("trucks.driver_id IS NULL");
        }

        $free_driver = $free_driver
            ->whereIn('users.user_role_id', [3,4])
            ->where("users.is_active",1)
            ->where("users.is_deleted",0)
            ->get()->toArray();
        $results['free_driver'] = array();
        
        foreach($free_driver as $driverRow){
            $results['free_driver'][] = array('id'=>$driverRow["id"],'name'=>$driverRow["name"]);
        }

        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        return response()->json($response);
	}
    public function currentShipment(Request $request) {
        $user_id            =   Auth::guard('api')->user()->id;
		$DB					=	Shipment::query();
        $DB->join('shipment_offers', 'shipment_offers.shipment_id' , 'shipments.id');
        $DB->leftjoin('users', 'users.id' , 'shipments.customer_id');
        $DB->leftjoin('shipment_driver_schedules', 'shipments.id' , 'shipment_driver_schedules.shipment_id');
        $DB->select(
            'shipments.id',
            "shipments.request_number",
            "shipments.request_number",
            "users.name as applicant_name",
            "users.customer_type as type",
            "shipments.pickup_city as pickup_address",
            "shipments.created_at",
            "shipments.request_date",
            "shipments.status as shipments_status",
            "shipments.request_date_flexibility",
            "shipments.request_time",
            "shipments.invoice_file",
            "shipments.request_type",
            "shipments.shipment_type",
            "shipments.invoice_price",
            "shipments.invoice_send_time as invoice_time",  
            "shipments.payment_status",
            "shipment_driver_schedules.shipment_end_comment",
            DB::raw("(select price from shipment_offers where shipment_id=shipments.id and truck_company_id=$user_id) as offer_price"),
            "shipment_offers.request_offer_date",
            "shipment_offers.duration",
            "shipment_driver_schedules.id as schedule_id",
            DB::raw('CASE WHEN shipment_driver_schedules.shipment_status IS NULL THEN "not_scheduled" ELSE shipment_driver_schedules.shipment_status END as schedule_shipment_status')
        );

        $shipmtne = array('shipment','end','cancelled');

        if($request->status == "invoice"){
            $shipmtne = array('end');
            $DB->whereNotNull('invoice_file');
        }elseif($request->status == 'active'){
            $fieldValue = 'start';
            $DB->where(function ($query) use ($fieldValue) {
                $query->whereRaw("shipment_driver_schedules.shipment_status = '".$fieldValue."' 
                    "
                );
            });
        }else if($request->status == 'upcoming'){
            $shipmtne = array('shipment');
            $DB->whereDate('shipments.request_date', '>=', now());
            $DB->where(function ($query) {
                $query->whereRaw("shipment_driver_schedules.shipment_status IS NULL or shipment_driver_schedules.shipment_status = 'not_start'");
            });
        }else if($request->status == 'schedule'){
            $shipmtne = array('shipment');
            $DB->whereDate('shipments.request_date', '>=', now());
            $DB->where(function ($query) {
                $query->whereRaw("shipment_driver_schedules.shipment_status IS NULL or shipment_driver_schedules.shipment_status = 'not_start'");

            });
        }else if($request->status == 'past'){
            $shipmtne = array('end','cancelled');
        }
        $DB->whereIn('shipments.status',$shipmtne);
        $DB->where('shipment_offers.truck_company_id',Auth::guard('api')->user()->id);
        $DB->where('shipment_offers.status','approved_from_company');

        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        if(!$results->isEmpty()){
            $nextDay = Carbon::now()->addDay(1);
            foreach($results as &$item){
                $item->created_date = date(config("Reading.date_format"),strtotime($item->created_at));

                $item->request_start_date = Carbon::createFromFormat('Y-m-d', $item->request_date)->subDay($item->request_date_flexibility);
                $item->request_end_date = Carbon::createFromFormat('Y-m-d', $item->request_date)->addDay($item->request_date_flexibility);
                if($item->duration<=24){
                    $item->duration_formated_hours = Carbon::createFromTime($item->duration, 0, 0)->format('H:i');
                }else{
                    $item->duration_formated_hours = $item->duration;
                }
                
                if($item->request_start_date->lt($nextDay)){
                    $item->request_start_date = $nextDay;
                }

                if($item->request_end_date->lt($nextDay)){
                    $item->request_end_date = $nextDay;
                }
                
                if($item->request_time == 21){
                    $item->request_time_zone = "afternoon"; 
                    $item->request_time_start_zone = date('h:i A', strtotime("2023-09-25 12:00 PM"));
                    $item->request_time_end_zone = date('h:i A', strtotime("2023-09-25 04:00 PM"));
                }elseif($item->request_time == 20){
                    $item->request_time_zone = "moring"; 
                    $item->request_time_start_zone = date('h:i A', strtotime("2023-09-25 07:00 AM"));
                    $item->request_time_end_zone = date('h:i A', strtotime("2023-09-25 12:00 PM"));
                }elseif($item->request_time == 22){
                    $item->request_time_zone = "evening"; 
                    $item->request_time_start_zone = date('h:i A', strtotime("2023-09-25 04:00 PM"));
                    $item->request_time_end_zone = date('h:i A', strtotime("2023-09-25 09:00 PM"));
                }else{
                    $item->request_time_zone = "not-seleted";
                }
                $item->request_time_name = DB::table("lookup_discriptions")->where("language_id",getAppLocaleId())->where("parent_id",$item->request_time)->value("code");
                
                if($item->request_time_name == null){
                    $item->request_time_name = trans("messages.please_select_time");
                }
                if($item->invoice_time){
                    $item->invoice_time = date(config("Reading.date_time_format"),strtotime($item->invoice_time));
                }
                $item->request_date = date(config("Reading.date_format"),strtotime($item->request_date));

                $item->request_offer_date = $item->request_offer_date ? date('d/m/Y',strtotime($item->request_offer_date)) : NULL ;



                $shipmentStopList = DB:: table("shipment_stops")->where('shipment_id',$item->id)->get();
                if($shipmentStopList->count()>1){
                    $item->dropoff_address = trans('messages.multiple_destinations');
                }else{
                    foreach($shipmentStopList as $ShipmentStop ){
                        $item->dropoff_address = $ShipmentStop->dropoff_city;
                        break;
                    }
                }
                
                if($item->request_type != "" && $item->request_type != 0){
                    $item->request_type_name = DB::table("truck_type_descriptions")
                        ->where("language_id",getAppLocaleId())
                        ->where("parent_id",$item->request_type)
                        ->value("name");
                }else {
                    if($item->shipment_type != ""){
                        $item->request_type_name = DB::table("truck_type_descriptions")->where("language_id",getAppLocaleId())->where("parent_id",$item->shipment_type)->value("name");
                    }else {
                        $item->request_type_name = "";
                    }
                }
                if($item->invoice_file){
                    $item->invoice_file = Config('constants.INVOICE_FILE_PATH').$item->invoice_file;
                }
                $item->status = trans("messages.$item->shipments_status");
                if($item->schedule_shipment_status == "start" && $item->shipments_status == "shipment"){
                    $item->shipments_status = 'active';
                    $item->status = trans("messages.active");
                }
                $item->type = trans("messages.".ucfirst($item->type));
                $item->payment_status_string = trans("messages.".$item->payment_status);
                $item->invoice_price = $item->invoice_price;

            }
        }
        
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        return response()->json($response);
	}

    public function cancelShipment(Request $request){
        if(empty($request->shipment_id)){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }
        $objShipmentOffer =   DB::table("shipment_offers")
            ->where("shipment_id",$request->shipment_id)
            ->where("status",'approved_from_company')
            ->where("truck_company_id",Auth::guard('api')
            ->user()->id)->first();
        if($objShipmentOffer == null){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }
        $objShipment                                    = Shipment::find($request->shipment_id);
        if($objShipment->status == 'cancelled'){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
        }
        $objShipment->status                 = 'cancelled';
        $objShipment->save();
        $this->shipment_cancelled_by_company($objShipment,$objShipmentOffer);

        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   trans("messages.shipment_has_been_cancelled_successfully");
        $response["data"]                   =   (object)array();
        return response()->json($response);
    }

    public function shipmentSchedule(Request $request) {
        
        $thisData = $request->all();

        $shipment_offer =   ShipmentOffer::where("shipment_id",$request->shipment_id)->where("truck_company_id",Auth::guard('api')->user()->id)->first();

        $validator                    =   Validator::make(
            $request->all(), 
            array(
                'shipment_id'           => "required|numeric",
                'start_time'            => "required|date_format:h:i A",
                'start_day'             => ($shipment_offer->request_offer_date ? "nullable" : "required" )."|date_format:d/m/Y",
                'duration'              => "required",
                'time_type'             => "required",
            ), 
            array(
                "shipment_id.required"  => trans("messages.This field is required"),
                "start_time.required"   => trans("messages.This field is required"),
                "start_day.required"   => trans("messages.This field is required"),
                "duration.required"     => trans("messages.This field is required"),
                "time_type.required"     => trans("messages.This field is required"),
            )
        );
        if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{
            $shipment_driver_schedule =   ShipmentDriverSchedule::where("shipment_id",$request->shipment_id)->where("truck_company_id",Auth::guard('api')->user()->id)->first();
            if(empty($shipment_driver_schedule)){
                $shipment_offers =   DB::table("shipment_offers")
                    ->where("shipment_id",$request->shipment_id)
                    ->where("truck_company_id",Auth::guard('api')->user()->id)
                    ->where("status",'approved_from_company')
                    ->first();
                if($shipment_offers){

                    $objShipment =   Shipment::where("id",$request->shipment_id)
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
                            $objShipmentDriverSchedule->shipment_id        = $request->shipment_id;
                            $objShipmentDriverSchedule->truck_company_id   = Auth::guard('api')->user()->id;
                            $objShipmentDriverSchedule->driver_id          = $objTruck->driver_id ?? 0;
                            $objShipmentDriverSchedule->truck_id           = $objTruck->id;
                            $objShipmentDriverSchedule->time_type           = $request->time_type;


                            if($shipment_offers->request_offer_date){
                                $request->start_time = Carbon::createFromFormat('Y-m-d', ($shipment_offers->request_offer_date))->format('d/m/Y') . " " . $request->start_time ;
                            }else{
                                $request->start_time = $request->start_day . " " . $request->start_time;

                            }
                            $objShipmentDriverSchedule->start_time         = Carbon::createFromFormat('d/m/Y h:i A', ($request->start_time))->format('Y-m-d H:i:s');
                            $objShipmentDriverSchedule->duration           = $request->duration;
                            $objShipmentDriverSchedule->shipment_status    = "not_start";
                            $objShipmentDriverSchedule->save();


                            $this->shipment_schedule_by_company($objShipment,$shipment_offers,$objShipmentDriverSchedule);

            
                            $response                           =   array();
                            $response["status"]                 =   "success";
                            $response["msg"]                    =   trans("messages.shipment_schedule_successfully");
                            $response["data"]                   =   (object)array();
                            return response()->json($response);
                        }else{
                            $response                           =   array();
                            $response["status"]                 =   "error";
                            $response["msg"]                    =   trans("messages.please_assign_a_driver_to_the_selected_truck_then_try_again");
                            $response["data"]                   =   (object)array();
                            return response()->json($response);    
                        }
                    }else{
                        $response                           =   array();
                        $response["status"]                 =   "error";
                        $response["msg"]                    =   trans("messages.invalid_requests");
                        $response["data"]                   =   (object)array();
                        return response()->json($response);    
                    }

                }else{
                    $response                           =   array();
                    $response["status"]                 =   "error";
                    $response["msg"]                    =   trans("messages.invalid_requests");
                    $response["data"]                   =   (object)array();
                    return response()->json($response);
                }
            }else{
                $response                           =   array();
                $response["status"]                 =   "error";
                $response["msg"]                    =   trans("messages.invalid_requests");
                $response["data"]                   =   (object)array();
                return response()->json($response);
            }
        }
	}

    public function deleteShipmentSchedule(Request $request) {
        
        $thisData = $request->all();
        $validator                    =   Validator::make(
            $request->all(), 
            array(
                'schedule_id'           => "required",
            ), 
            array(
                "schedule_id.required"  => trans("messages.The_shipment_id_field_is_required"),
            )
        );
        if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{
            $objShipmentDriverSchedule = ShipmentDriverSchedule::find($request->schedule_id);

            ShipmentDriverSchedule::where("id",$request->schedule_id)->delete();

            $objShipment    =   Shipment::where("id",$objShipmentDriverSchedule->shipment_id)
                ->where("status",'shipment')
                ->first();

            $shipment_offers =   DB::table("shipment_offers")
                ->where("shipment_id",$objShipmentDriverSchedule->shipment_id)
                ->where("truck_company_id",Auth::guard('api')->user()->id)
                ->where("status",'approved_from_company')
                ->first();

            $this->shipment_schedule_deleted_by_company($objShipment,$shipment_offers,$objShipmentDriverSchedule);

            
            $response                           =   array();
            $response["status"]                 =   "success";
            $response["msg"]                    =   trans("messages.shipment_schedule_deleted_successfully");
            $response["data"]                   =   (object)array();
            return response()->json($response);


        }
	}

	public function sendShipmentInvoice(Request $request)
	{

		$user = Auth::guard('api')->user();

		$validator                    =   Validator::make(
			$request->all(),

			array(
				'shipment_id'                   => 'required',
                'invoice'                       => 'required|mimes:pdf,docx,doc,png,jpg,jpeg|max:11264',
			),
			array(
				"shipment_id.required"         => trans("messages.This field is required"),
				"invoice.mimes"                => trans("messages.File must be pdf, doc, docx, png only"),
				"invoice.max"                => trans("messages.File size must be 11264 KB only"),
			)
		);
		if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{
			if ($request->hasFile('invoice')) {
                $objShipment 						      = Shipment::find($request->shipment_id);
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
                
                $attechmentName = 'invoice_'.$objShipment->request_number.'.'.$request->invoice->getClientOriginalExtension();
                $objShipment->invoice_file = $attechmentName;
                $objShipment->invoice_send_time = Carbon::now()->toDateTimeString();
                $objShipment->invoice_price = $request->invoice_price;
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

                if($userCompanyInformation['contact_person_email'] != ""){
                    $this->sendMail($userEmails, $userName, $subject, $messageBody, $settingsEmail, true, $file, $attechmentName);
                }
                $objShipmentOffer = ShipmentOffer::where([
                    "shipment_id"       => $request->shipment_id,
                    "truck_company_id"  => Auth::guard('api')->user()->id
                ])->first();

                $this->send_invoice_to_customer_by_truck_company($objShipment,$objShipmentOffer);
			}
			$response                           =   array();
            $response["status"]                 =   "success";
            $response["msg"]                    =   trans("messages.shipment_invoice_send_successfully");
            $response["data"]                   =   (object)array();
            return response()->json($response);
		}
	}

	public function resendShipmentInvoice(Request $request)
	{
		$validator                    =   Validator::make(
			$request->all(),

			array(
				'shipment_id'                   => 'required',
			),
			array(
				"shipment_id.required"         => trans("messages.This field is required"),
			)
		);
		if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{
                $objShipment 						      = Shipment::find($request->shipment_id);
                $user = User::find($objShipment->customer_id);
                $userCompanyInformation = array();
                if($user->customer_type == "business" ){
                    $userCompanyInformation = UserCompanyInformation::where("user_id",$user->id)->first()->toArray();
                }else{
                    $userCompanyInformation = array(
                        "contact_person_name"			=> $user->name,
                        "contact_person_email"			=> $user->email,
                        "contact_person_phone_number"	=> $user->phone_number
                    );
                }
                if($userCompanyInformation['contact_person_email'] == ""){
                    $response                           = array();
                    $response["status"]                 = "error";
                    $response["msg"]                    = trans("messages.email_id_has_not_been_updated_in_this_customer_profile");
                    $response["data"]                   = (object)array();
                    return response()->json($response);
                }
                $objShipment->invoice_send_time         = Carbon::now()->toDateTimeString() ;
                $objShipment->save();

                $file                                   = Config('constants.INVOICE_FILE_ROOT_PATH').$objShipment->invoice_file;
                $shipment_stops_obj						= ShipmentStop::where("shipment_id",$objShipment->id)->get()->toArray();

                $emailActions                           = EmailAction::where('action', '=', 'truck_company_send_invoice_to_customer')->get()->toArray();
                $emailTemplates                         = EmailTemplate::where('action', '=', 'truck_company_send_invoice_to_customer')->get(array('name', 'subject', 'action', 'body'))->toArray();
    
                $emailActions = EmailAction::where('action', '=', 'truck_company_send_invoice_to_customer')->get()->toArray();
                $language_id                            = getAppLocaleId();
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
                $this->sendMail($userEmails, $userName, $subject, $messageBody, $settingsEmail, true, $file, $objShipment->invoice_file);
			

            
            $objShipmentOffer = ShipmentOffer::where([
                "shipment_id"       => $request->shipment_id,
                "truck_company_id"  => Auth::guard('api')->user()->id
            ])->first();
            $this->send_invoice_to_customer_by_truck_company($objShipment,$objShipmentOffer);

			$response                           =   array();
            $response["status"]                 =   "success";
            $response["msg"]                    =   trans("messages.shipment_invoice_send_successfully");
            $response["data"]                   =   (object)array();
            return response()->json($response);
		}
	}

	public function makePaymentStatus(Request $request)
	{

		$validator                    =   Validator::make(
			$request->all(),
			array(
				'shipment_id'                   => 'required',
				'payment_status'                => 'required',
			),
			array(
				"shipment_id.required"          => trans("messages.This field is required"),
				"payment_status.required"       => trans("messages.This field is required"),
			)
		);
		if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{
            $objShipment 						      = Shipment::find($request->shipment_id);
            $objShipment->payment_status              = $request->payment_status ;
            $objShipment->save();

			$response                           =   array();
            $response["status"]                 =   "success";
            $response["msg"]                    =   trans("messages.payment_status_is_".$objShipment->payment_status."_successfully");
            $response["data"]                   =   (object)array();
            return response()->json($response);
		}
	}

    public function skipCerificateUpload(Request $request){
       
        $shipmentStop = ShipmentStop::find($request->shipment_stop_id);
 
        if($shipmentStop){
             if($request->request_certificate_skip){
             $shipmentStop->request_certificate_skip = $request->request_certificate_skip ?? 0;
             }
             if($request->request_signature_skip){
                 $shipmentStop->request_signature_skip = $request->request_signature_skip ?? 0;
             }
 
             if($shipmentStop->save()){  
                 $response                           =   array();
                 $response["status"]                 =   "success";
                 $response["msg"]                    =   trans("messages.document_certificate_skipped");
                 $response["data"]                   =   (object)array();
                 return response()->json($response);
             }
         }else{
            $response                          =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.admin_Data_Not_Found");
            $response["data"]                   =   (object)array();
            return response()->json($response);
         }
     
 
     }

}
