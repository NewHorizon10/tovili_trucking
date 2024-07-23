<?php
namespace App\Http\Controllers\api\driver\v1;

use App\Http\Controllers\Controller;
use App\Models\ShipmentStop;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Shipment;
use App\Models\TruckTypeDescription;
use App\Models\ShipmentDriverSchedule;
use App\Models\ShipmentOffer;
use App\Models\UserCompanyInformation;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class ShippingRequestsController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function currentShipment(Request $request) {
        $user_id            =   Auth::guard('api')->user()->id;
		$DB					=	Shipment::query();
        $DB->join('users', 'users.id' , 'shipments.customer_id');
        $DB->join('shipment_driver_schedules', 'shipments.id' , 'shipment_driver_schedules.shipment_id');
        $DB->select(
            'shipments.id',
            "shipments.request_number",
            "users.id as active_id",
            "users.name as applicant_name",
            "users.phone_number as applicant_phone_number",
            "users.image as applicant_image",
            "users.customer_type",
            "shipments.pickup_city as pickup_address",
            "shipments.created_at",
            "shipments.request_date",
            "shipments.status as shipments_status",
            "shipment_driver_schedules.shipment_end_comment",
            "company_latitude",
            "company_longitude",
            DB::raw("(select request_certificate_type from shipment_stops where shipment_id=shipments.id order by id asc limit 1) as request_certificate_type"),
            DB::raw("(select request_certificate from shipment_stops where shipment_id=shipments.id order by id asc limit 1) as request_certificate"),
            DB::raw("(select price from shipment_offers where shipment_id=shipments.id  and truck_company_id = ".Auth::guard('api')->user()->truck_company_id." limit 1) as offer_price"),
            DB::raw("(select duration from shipment_offers where shipment_id=shipments.id  and truck_company_id = ".Auth::guard('api')->user()->truck_company_id." limit 1) as duration"),
            "shipment_driver_schedules.id as schedule_id",
            "shipment_driver_schedules.start_time",
            DB::raw('CASE WHEN shipment_driver_schedules.shipment_status IS NULL THEN "not_scheduled" ELSE shipment_driver_schedules.shipment_status END as schedule_shipment_status')
        )
        ->where("shipment_driver_schedules.driver_id",$user_id);

        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.request_date';
		$order  = ($request->input('order')) ? $request->input('order')   : 'ASC';
        
        if($request->status == 'active'){
            $fieldValue = 'start';
            $DB->where(function ($query) use ($fieldValue) {
                $query->whereRaw(
                    "shipment_driver_schedules.shipment_status = '".$fieldValue."'"
                );
            });
        }else if($request->status == 'upcoming'){
            $DB->where(function ($query) {
                $query->whereRaw("shipment_driver_schedules.shipment_status = 'not_start'");
            });
        }else if($request->status == 'past'){
            $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
            $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
            $fieldValue = "end";
            $DB->where(function ($query) use ($fieldValue) {
                $query->whereRaw("shipment_driver_schedules.shipment_status = '".$fieldValue."' 
                    "
                );
            });
        }
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        if(!$results->isEmpty()){
            foreach($results as &$item){
                $item->created_date = date(config("Reading.date_format"),strtotime($item->created_at));
                $item->request_date = date(config("Reading.date_format"),strtotime($item->request_date));
                $shipmentStopList = DB:: table("shipment_stops")->where('shipment_id',$item->id)->get();
                
                if($shipmentStopList->count()>1){
                    $item->dropoff_address = trans('messages.multiple_destinations');
                    $item->multiple_destination_key = 1;
                }else{
                    foreach($shipmentStopList as $ShipmentStop ){
                        $item->dropoff_address = $ShipmentStop->dropoff_city;
                        $item->multiple_destination_key = 0;
                        break;
                    }
                }
                $item['shipment_stop'] = ShipmentStop::where('shipment_id',$item->id)->with("ShipmentStopAttchements")->get();

                if($item->applicant_image != "" && File::exists(Config('constants.CUSTOMER_IMAGE_ROOT_PATH').$item->applicant_image)){
                    $item->applicant_image = Config('constants.CUSTOMER_IMAGE_PATH').$item->applicant_image;
                }else{
                    $item->applicant_image = Config('constants.NO_IMAGE_PATH');
                }

                if($item->request_certificate != "" && File::exists(Config('constants.GALLERY_MEDIA_IMAGE_ROOT_PATH').$item->request_certificate)){
                    $item->request_certificate = Config('constants.GALLERY_MEDIA_IMAGE').$item->request_certificate;
                }else{
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
                $item->stop_id = $shipmentStopList[0]->id;
                $item->request_digital_signature = $shipmentStopList[0]->request_digital_signature;
                $item->request_certificate_by_driver = $shipmentStopList[0]->request_certificate_by_driver;

                $item->status = trans("messages.$item->shipments_status");
                if($item->schedule_shipment_status == "start" && $item->shipments_status == "shipment"){
                    $item->shipments_status = 'active';
                    $item->status = trans("messages.active");
                }
                if($item->duration<=24){
                    $item->duration_formated_hours = Carbon::createFromTime($item->duration, 0, 0)->format('H:i');
                }else{
                    $item->duration_formated_hours = $item->duration.":00";;
                }
                
            }
        }
        
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        return response()->json($response);
	}

    public function viewShipmentRequests(Request $request, $shipment_id) {
        
        $objShipmentDriverSchedule = ShipmentDriverSchedule::where(
            [
                'shipment_id'=>$shipment_id,
                'driver_id'=>Auth::guard('api')->user()->id
            ]
        )->first();
        if($objShipmentDriverSchedule == null){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
		}
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
                'customer'=> function($query) {
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
                    );
				},
                'SelectedShipmentOffers.TruckDetail',
                'shipmentDriverScheduleDetails',
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
                        "request_certificate_by_driver",
                        "request_certificate_skip",
                        "request_signature_skip",
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
		->whereIn('status', ['shipment','end'])
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
               $track_location[] = array(
                   "type"      => "drop",
                   "drop"      => $keyvar[$key],
                   "lat"       => $stops->dropoff_latitude,
                   "lng"       => $stops->dropoff_longitude
               ); 
           }
       }
       $shipment->track_location = $track_location;
       
		if($shipment == null){
            $response                           =   array();
            $response["status"]                 =   "error";
            $response["msg"]                    =   trans("messages.invalid_requests");
            $response["data"]                   =   (object)array();
            return response()->json($response);
		}

        if($shipment->SelectedShipmentOffers){
            if($shipment->SelectedShipmentOffers->TruckDetail != null){
                $shipment->SelectedShipmentOffers->TruckDetail->type_of_truck_name = TruckTypeDescription::where(
                    [
                        "parent_id"=>$shipment->SelectedShipmentOffers->TruckDetail->type_of_truck,
                        "language_id"=>getAppLocaleId()

                    ]
                )->first()->name;
            }

            if($shipment->SelectedShipmentOffers->duration<=24){
                $shipment->SelectedShipmentOffers->duration_formated_hours = Carbon::createFromTime($shipment->SelectedShipmentOffers->duration, 0, 0)->format('H:i');
            }else{
                $shipment->SelectedShipmentOffers->duration_formated_hours = $shipment->SelectedShipmentOffers->duration.":00";
            }
        }else{
            $shipment->SelectedShipmentOffers->TruckDetail->type_of_truck_name = "";
        }
        $shipment->created_date = date(config("Reading.date_format"),strtotime($shipment->created_at));
        $shipment->request_date = date(config("Reading.date_format"),strtotime($shipment->request_date));
        $shipment->shipmentDriverScheduleDetails->start_time = Carbon::createFromFormat('Y-m-d H:i:s', $shipment->shipmentDriverScheduleDetails->start_time)->format(Config('Reading.date_format'));
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

        $shipment->request_status                   =   trans("messages.".$shipment->status);
        if($shipment->shipmentDriverScheduleDetails->shipment_status == "start" && $shipment->status == "shipment"){
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
                $questionnaireArray['qus'] = $truck_type_question_descriptions->name ?? '';
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

    public function shipmentScheduleStart(Request $request) {
        
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
            $objStartedSchedule = ShipmentDriverSchedule::where(
                [
                    'shipment_status'   =>  'start',
                    'driver_id'         =>  Auth::guard('api')->user()->id
                ]
            )->get();
            if($objStartedSchedule->count()==0){

                $objShipmentDriverSchedule = ShipmentDriverSchedule::where(
                    [
                        'id'=>$request->schedule_id,
                        'driver_id'=>Auth::guard('api')->user()->id
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


                    $response                           =   array();
                    $response["status"]                 =   "success";
                    $response["msg"]                    =   trans("messages.shipment_start_successfully");
                    $response["data"]                   =   (object)array();
                    return response()->json($response);
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
                $response["msg"]                    =   trans("messages.you_can_start_only_one_shipment_at_a_time");
                $response["data"]                   =   (object)array();
                return response()->json($response);  
            }
        }
	}

    public function uploadShipmentStopDocument(Request $request) {

        $ShipmentStopObj = ShipmentStop::find($request->stop_id);
        $validator                    =   Validator::make(
            $request->all(), 
            array(
                'stop_id'                   => "required",
                'document'                  => (($ShipmentStopObj->request_certificate_type == "physical" || $ShipmentStopObj->request_certificate_type == "digital") ? "required" : "nullable") . "|mimes:png,jpg,jepg",
            ), 
            array(
                "stop_id.required"          => trans("messages.This field is required"),
                "document.required"         => trans("messages.This field is required"),
				"document.mimes"            => trans("messages.File must be jpg, jpeg, png only"),
            )
        );
        if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{
            if ($request->hasFile('document')) {
                if($ShipmentStopObj->request_certificate){
                    $path = Config('constants.GALLERY_MEDIA_IMAGE_ROOT_PATH').basename($ShipmentStopObj->request_certificate);
                    if(file_exists($path)){
                        unlink($path);
                    }
                }
                $file = rand() . '.' . $request->document->getClientOriginalExtension();
                $request->file('document')->move(Config('constants.GALLERY_MEDIA_IMAGE_ROOT_PATH'), $file);
                $ShipmentStopObj->request_certificate_type      = "physical";
                $ShipmentStopObj->request_certificate           = $file;
                $ShipmentStopObj->request_certificate_by_driver = 1;
                $ShipmentStopObj->save();

                $response                           =   array();
                $response["status"]                 =   "success";
                $response["msg"]                    =   trans("messages.document_upload_successfully");
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

    public function uploadShipmentStopSignature(Request $request) {

        $ShipmentStopObj = ShipmentStop::find($request->stop_id);
        $validator                    =   Validator::make(
            $request->all(), 
            array(
                'stop_id'               => "required",
                'signature'             => ($ShipmentStopObj->request_certificate_type == "physical" ? "required" : "nullable") . "",
            ), 
            array(
                "stop_id.required"      => trans("messages.This field is required"),
                "signature.required"    => trans("messages.This field is required"),
				"signature.mimes"       => trans("messages.File must be pdf, doc, docx, png only"),
            )
        );
        if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{
            if(!empty($request->signature)) {
                if($ShipmentStopObj->request_digital_signature){
                    $path = Config('constants.SIGNATURE_IMAGES_ROOT_PATH').basename($ShipmentStopObj->request_digital_signature);
                    if(file_exists($path)){
                        unlink($path);
                    }
                }

                $fileName =  time() . "_signature.png";
                $data = $request->signature;
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                file_put_contents(Config('constants.SIGNATURE_IMAGES_ROOT_PATH') . $fileName, $data);
                $ShipmentStopObj->request_digital_signature = $fileName;

                $ShipmentStopObj->save();

                $response                           =   array();
                $response["status"]                 =   "success";
                $response["msg"]                    =   trans("messages.signature_upload_successfully");
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

    public function shipmentScheduleEnd(Request $request) {
        $thisData = $request->all();
        $validator                    =   Validator::make(
            $request->all(), 
            array(
                'schedule_id'                     => "required",
                'shipment_end_comment'            => "",
            ), 
            array(
                "schedule_id.required"                        => trans("messages.This field is required"),
                "shipment_end_comment.required"               => trans("messages.This field is required"),
                "time_taken_to_complete_shipment.required"    => trans("messages.This field is required"),
            )
        );
        
        if ($validator->fails()) {
            return $this->change_error_msg_layout($validator->errors()->getMessages());
        }else{
            
            $completeTime = explode(":", $request->time_taken_to_complete_shipment);
            $endHours = isset($completeTime[0]) ? intval($completeTime[0]) : 0;
            $endMinutes = isset($completeTime[1]) ? intval($completeTime[1]) : 0;
            $total_time = $endHours * 60 + $endMinutes; 

            if($total_time == 0 || $total_time <= 0){
                $response                           =   array();
                $response["status"]                 =   "error";
                $response["msg"]                    =   trans("messages.time_taken_to_complete_shipment_validation");
                $response["data"]                   =   (object)array();
                return response()->json($response);
            }

            if(!isset($completeTime[1])){
                $response                           =   array();
                $response["status"]                 =   "error";
                $response["msg"]                    =   trans("messages.time_format_is_invalid");
                $response["data"]                   =   (object)array();
                return response()->json($response);
            }else if($completeTime[1] > 59){
                $response                           =   array();
                $response["status"]                 =   "error";
                $response["msg"]                    =   trans("messages.time_format_is_invalid");
                $response["data"]                   =   (object)array();
                return response()->json($response);
            }
            

            if(Auth::guard('api')->user()->user_role_id == 4){
                $objShipmentDriverSchedule = ShipmentDriverSchedule::where(
                    [
                        'id'=>$request->schedule_id,
                        'driver_id'=>Auth::guard('api')->user()->id
                    ]
                )->first();

                if($objShipmentDriverSchedule && $objShipmentDriverSchedule->shipment_status == 'start'){
                    $shipmentStopsObj = ShipmentStop::where("shipment_id",$objShipmentDriverSchedule->shipment_id)
                        ->whereIn('request_certificate_type',['physical'])
                        ->get();
                    if($shipmentStopsObj){
                        $endFlag = false;

                        if($endFlag){
                            $response                           =   array();
                            $response["status"]                 =   "error";
                            $response["msg"]                    =   trans("messages.please_upload_signature_and_documents_first");
                            $response["data"]                   =   (object)array();
                            return response()->json($response);
                        }
                    }

                    $objShipmentDriverSchedule->shipment_status         = 'end';
                    $objShipmentDriverSchedule->shipment_end_comment    = $request->shipment_end_comment;
                    $currentDateTime = Carbon::now();
                    $currentDateTime->format('Y-m-d H:i:s');
                    $objShipmentDriverSchedule->shipment_actual_end_time = $currentDateTime->format('Y-m-d H:i:s');
                    $objShipmentDriverSchedule->save();

                    $objShipment    =   Shipment::where("id",$objShipmentDriverSchedule->shipment_id)
                        ->first();
                    
                    $objShipment->status = "end";
                    $objShipment->save();

                    //  Save the time taken to the complete shipment
                    $objShipmentDriverSchedule->time_taken_to_complete_shipment = $endHours . ":" .$endMinutes;
                    $objShipmentDriverSchedule->save();
        
                    $shipment_offers =   DB::table("shipment_offers")
                        ->where("shipment_id",$objShipmentDriverSchedule->shipment_id)
                        ->where("status",'approved_from_company')
                        ->first();

                    $this->shipment_schedule_end_by_driver($objShipment,$shipment_offers,$objShipmentDriverSchedule);


                    $response                           =   array();
                    $response["status"]                 =   "success";
                    $response["msg"]                    =   trans("messages.shipment_end_successfully");
                    $response["data"]                   =   (object)array();
                    return response()->json($response);
                }else{
                    $response                           =   array();
                    $response["status"]                 =   "error";
                    $response["msg"]                    =   trans("messages.invalid_requests");
                    $response["data"]                   =   (object)array();
                    return response()->json($response);
                }
            }else if(Auth::guard('api')->user()->user_role_id == 3){
                ///////////////////////////////////////////

                $objShipmentDriverSchedule = ShipmentDriverSchedule::where(
                    [
                        'id'=>$request->schedule_id,
                        'truck_company_id' => Auth::guard('api')->user()->id
                    ]
                )->first();

                if($objShipmentDriverSchedule && $objShipmentDriverSchedule->shipment_status == 'start'){

                    $objShipmentDriverSchedule->shipment_status         = 'end';
                    $objShipmentDriverSchedule->shipment_end_comment    = $request->shipment_end_comment;
                    $currentDateTime = Carbon::now();
                    $currentDateTime->format('Y-m-d H:i:s');
                    $objShipmentDriverSchedule->shipment_actual_end_time = $currentDateTime->format('Y-m-d H:i:s');
                    $objShipmentDriverSchedule->save();

                    $objShipment    =   Shipment::where("id",$objShipmentDriverSchedule->shipment_id)
                        ->first();
                    
                    $objShipment->status = "end";
                    $objShipment->save();

                    //  Save the time taken to the complete shipment
                    $objShipmentDriverSchedule->time_taken_to_complete_shipment = $endHours . ":" .$endMinutes;
                    $objShipmentDriverSchedule->save();
                    $shipment_offers =   DB::table("shipment_offers")
                        ->where("shipment_id",$objShipmentDriverSchedule->shipment_id)
                        ->where("status",'approved_from_company')
                        ->first();

                    $this->shipment_schedule_end_by_truck_company($objShipment,$shipment_offers,$objShipmentDriverSchedule);


                    $response                           =   array();
                    $response["status"]                 =   "success";
                    $response["msg"]                    =   trans("messages.shipment_end_successfully");
                    $response["data"]                   =   (object)array();
                    return response()->json($response);
                }else{
                    $response                           =   array();
                    $response["status"]                 =   "error";
                    $response["msg"]                    =   trans("messages.invalid_requests");
                    $response["data"]                   =   (object)array();
                    return response()->json($response);
                }
                ///////////////////////////////////////////

            }else{
                $response                           =   array();
                $response["status"]                 =   "error";
                $response["msg"]                    =   trans("messages.invalid_requests");
                $response["data"]                   =   (object)array();
                return response()->json($response);
            }
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
