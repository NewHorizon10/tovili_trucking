<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use App\Models\TruckType;
use Illuminate\Http\Request;
use View, DB, Config, Session;
use Carbon\Carbon;
use App\Jobs\SendMail;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Exports\ReportExport;
use App\Models\NotificationAction;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateDescription;
use App\Models\User;
use App\Models\Notification;

use App\Models\EmailTemplateDescription;

use App\Jobs\InsertNotification;
use App\Jobs\SendPushNotification;
use App\Jobs\SendGreenApiMessage;
use Illuminate\Support\Facades\Log;



class TruckController extends Controller
{

	public $model = 'truck';
	public $sectionName = 'Trucks';
	public $sectionNameSingular = 'Trucks';

	public function __construct(Request $request)
	{
		parent::__construct();
		View::share('model', $this->model);
		View::share('modelName', $this->model);
		View::share('sectionName', $this->sectionName);
		View::share('sectionNameSingular', $this->sectionNameSingular);
		$this->request = $request;
	}

	public function index_insurance(Request $request)
	{
		$DB = Truck::query();
		$DB->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
			->leftjoin('user_company_informations', 'trucks.truck_company_id', "=", 'user_company_informations.user_id');

		$DB->select(
			'user_company_informations.company_name',
			'trucks.id',
			'trucks.truck_company_id',
			'trucks.truck_system_number',
			'trucks.truck_licence_expiration_date',
			'trucks.truck_insurance_expiration_date',
			'trucks.is_active',
			'trucks.created_at',
			'trucks.updated_at',
			DB::Raw("(select name from truck_type_descriptions where language_id = " . getAppLocaleId() . " and parent_id = trucks.type_of_truck) as type_of_truck")
		);
		$DB->where('truck_type_descriptions.language_id', getAppLocaleId());
		$DB->where('trucks.is_deleted', 0);

		$allResultCount = $DB->count();
			
		$searchVariable = array();
		$inputGet = $request->all();
		if ($request->all()) {
			$searchData = $request->all();
			unset($searchData['display']);
			unset($searchData['_token']);

			if (isset($searchData['order'])) {
				unset($searchData['order']);
			}
			if (isset($searchData['sortBy'])) {
				unset($searchData['sortBy']);
			}
			if (isset($searchData['page'])) {
				unset($searchData['page']);
			}
			 if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('trucks.truck_insurance_expiration_date', [date('Y-m-d H:i:s',strtotime($dateS)), date('Y-m-d H:i:s',strtotime($dateE))]); 											
            }elseif(!empty($searchData['date_from'])){
                $dateS = $searchData['date_from'];
                $DB->where('trucks.truck_insurance_expiration_date','>=' ,[date('Y-m-d H:i:s',strtotime($dateS." 00:00:00"))]); 
            }elseif(!empty($searchData['date_to'])){
                $dateE = $searchData['date_to'];
                $DB->where('trucks.truck_insurance_expiration_date','<=' ,[date('Y-m-d H:i:s',strtotime($dateE." 23:59:59"))]); 						
            }
			foreach ($searchData as $fieldName => $fieldValue) {
				if ($fieldValue != "") {
					if ($fieldName == "truck_type") {
						$DB->where("trucks.type_of_truck", $fieldValue);
					}
					if ($fieldName == "truck_system_number") {
						$DB->where("trucks.truck_system_number", 'like', '%' . $fieldValue . '%');
					}
					if ($fieldName == "company_name") {
						$DB->where("company_name", 'like', '%' . $fieldValue . '%');
					}

					if ($fieldName == "company_refueling") {
						$DB->where("company_refueling", 'like', '%' . $fieldValue . '%');
					}

					if ($fieldName == "select_truck_expiry_type") {

						if($fieldValue == "expired_truck_insurance"){
							$DB->where("trucks.truck_insurance_expiration_date", '<=', now()->toDateString());
						}elseif($fieldValue ==  "near_to_expiry_truck_insurance"){
							$DB->whereBetween("trucks.truck_insurance_expiration_date", [now()->toDateString(),now()->addDays(4)->toDateString()]);
						}elseif($fieldValue == "expired_truck_license"){
							$DB->where("trucks.truck_licence_expiration_date", '<=', now()->toDateString());
						}elseif($fieldValue ==  "near_to_expiry_truck_license"){
							$DB->whereBetween("trucks.truck_licence_expiration_date",[now()->toDateString(), now()->addDays(4)->toDateString()]);
						}
					}

				}
				$searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
			}
			$allResultCount = $DB->count();
		}

		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'trucks.created_at';
		$order = ($request->input('order')) ? $request->input('order') : 'DESC';
		$records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");		
		$DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_track_insurance'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);

		$complete_string = $request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string = http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$resultcount = $results->count();
		$truckType = TruckType::where('is_active', 1)
			->where('is_deleted', 0)
			->get();
		return View("admin.$this->model.index_truck_insurance", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string', 'truckType', 'allResultCount'));
	}	

    public function export_index_insurance(Request $request)
	{
		$list[0] = array(
			trans("messages.Type_of_truck"),
			trans("messages.Truck Number"),
			trans("messages.Company Name"),
			trans("messages.end_of_insurance_date"),
			trans("messages.insurance_status"),
			trans("messages.admin_common_Status"),
		);
		
		$customers_export = Session::get('export_data_track_insurance');
		
		foreach ($customers_export as $key => $excel_export) {
			$list[] = array(
				$excel_export->type_of_truck,
				$excel_export->truck_system_number,
				$excel_export->company_name,
				($excel_export->truck_insurance_expiration_date ? Carbon::parse($excel_export->truck_insurance_expiration_date)->format(config("Reading.date_format")) : ''),
				($excel_export->truck_insurance_expiration_date == null || ( $excel_export->truck_insurance_expiration_date != null && Carbon::parse($excel_export->truck_insurance_expiration_date)->lte(now()) ) ? trans("messages.admin_common_Expired") : trans("messages.admin_common_Valid")),
				($excel_export->is_active==1 ? trans("messages.admin_common_Activated") : trans("messages.admin_common_Deactivated") ),
			);
			
		}
		$collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'truck_insurance_list.xlsx');
	}

	public function index_license(Request $request)
	{
		$DB = Truck::query();
		$DB->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
			->leftjoin('user_company_informations', 'trucks.truck_company_id', "=", 'user_company_informations.user_id');

		$DB->select(
			'user_company_informations.company_name',
			'trucks.id',
			'trucks.truck_company_id',
			'trucks.truck_system_number',
			'trucks.truck_licence_expiration_date',
			'trucks.truck_insurance_expiration_date',
			'trucks.is_active',
			'trucks.created_at',
			'trucks.updated_at',
			DB::Raw("(select name from truck_type_descriptions where language_id = " . getAppLocaleId() . " and parent_id = trucks.type_of_truck) as type_of_truck")
		);
		$DB->where('truck_type_descriptions.language_id', getAppLocaleId());
		$DB->where('trucks.is_deleted', 0);

        $allResultCount = $DB->count();

		$searchVariable = array();
		$inputGet = $request->all();
		if ($request->all()) {
			$searchData = $request->all();
			unset($searchData['display']);
			unset($searchData['_token']);

			if (isset($searchData['order'])) {
				unset($searchData['order']);
			}
			if (isset($searchData['sortBy'])) {
				unset($searchData['sortBy']);
			}
			if (isset($searchData['page'])) {
				unset($searchData['page']);
			}
			 if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('trucks.truck_licence_expiration_date', [date('Y-m-d H:i:s',strtotime($dateS)), date('Y-m-d H:i:s',strtotime($dateE))]); 											
            }elseif(!empty($searchData['date_from'])){
                $dateS = $searchData['date_from'];
                $DB->where('trucks.truck_licence_expiration_date','>=' ,[date('Y-m-d H:i:s',strtotime($dateS." 00:00:00"))]); 
            }elseif(!empty($searchData['date_to'])){
                $dateE = $searchData['date_to'];
                $DB->where('trucks.truck_licence_expiration_date','<=' ,[date('Y-m-d H:i:s',strtotime($dateE." 23:59:59"))]); 						
            }
			foreach ($searchData as $fieldName => $fieldValue) {
				if ($fieldValue != "") {
					if ($fieldName == "truck_type") {
						$DB->where("trucks.type_of_truck", $fieldValue);
					}
					if ($fieldName == "truck_system_number") {
						$DB->where("trucks.truck_system_number", 'like', '%' . $fieldValue . '%');
					}
					if ($fieldName == "company_name") {
						$DB->where("company_name", 'like', '%' . $fieldValue . '%');
					}

					if ($fieldName == "company_refueling") {
						$DB->where("company_refueling", 'like', '%' . $fieldValue . '%');
					}

					if ($fieldName == "select_truck_expiry_type") {

						if($fieldValue == "expired_truck_insurance"){
							$DB->where("trucks.truck_insurance_expiration_date", '<=', now()->toDateString());
						}elseif($fieldValue ==  "near_to_expiry_truck_insurance"){
							$DB->whereBetween("trucks.truck_insurance_expiration_date", [now()->toDateString(),now()->addDays(4)->toDateString()]);
						}elseif($fieldValue == "expired_truck_license"){
							$DB->where("trucks.truck_licence_expiration_date", '<=', now()->toDateString());
						}elseif($fieldValue ==  "near_to_expiry_truck_license"){
							$DB->whereBetween("trucks.truck_licence_expiration_date", [now()->toDateString(),now()->addDays(4)->toDateString()]);
						}
					}

				}
				$searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
			}
			$allResultCount = $DB->count();
		}
		
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'trucks.created_at';
		$order = ($request->input('order')) ? $request->input('order') : 'DESC';
		$records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		
		$DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_track_licence'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);

		$complete_string = $request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string = http_build_query($complete_string);
		$results->appends($inputGet)->render();
		$resultcount = $results->count();
		$truckType = TruckType::where('is_active', 1)
			->where('is_deleted', 0)
			->get();
		return View("admin.$this->model.index_truck_license", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string', 'truckType', 'allResultCount'));
	}

    public function truckInsuranceExpiryNotification(Request $request){
	    if($request->isMethod("post") && $request->submit == null){
			$DB = Truck::query();
			$DB->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
				->leftjoin('user_company_informations', 'trucks.truck_company_id', "=", 'user_company_informations.user_id');
			$DB->select(
				'user_company_informations.company_name',
				'trucks.id',
				'trucks.truck_company_id',
				'trucks.truck_system_number',
				'trucks.truck_licence_expiration_date',
				'trucks.truck_insurance_expiration_date',
				'trucks.is_active',
				'trucks.created_at',
				'trucks.updated_at',
				DB::Raw("(select name from truck_type_descriptions where language_id = " . getAppLocaleId() . " and parent_id = trucks.type_of_truck) as type_of_truck")
			);
			$DB->where('truck_type_descriptions.language_id', getAppLocaleId());
			$DB->where('trucks.is_deleted', 0);

			//////////////////////////////////////////filter start
			$searchVariable = json_decode(html_entity_decode($request->searchVariable),true);
			if (count($searchVariable)>0) {
				$searchData = $searchVariable;
				 if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
					$dateS = $searchData['date_from'];
					$dateE = $searchData['date_to'];
					$DB->whereBetween('trucks.truck_insurance_expiration_date', [date('Y-m-d H:i:s',strtotime($dateS)), date('Y-m-d H:i:s',strtotime($dateE))]); 											
				}elseif(!empty($searchData['date_from'])){
					$dateS = $searchData['date_from'];
					$DB->where('trucks.truck_insurance_expiration_date','>=' ,[date('Y-m-d H:i:s',strtotime($dateS." 00:00:00"))]); 
				}elseif(!empty($searchData['date_to'])){
					$dateE = $searchData['date_to'];
					$DB->where('trucks.truck_insurance_expiration_date','<=' ,[date('Y-m-d H:i:s',strtotime($dateE." 23:59:59"))]); 						
				}
				foreach ($searchData as $fieldName => $fieldValue) {
					if ($fieldValue != "") {
						if ($fieldName == "truck_type") {
							$DB->where("trucks.type_of_truck", $fieldValue);
						}
						if ($fieldName == "truck_system_number") {
							$DB->where("trucks.truck_system_number", 'like', '%' . $fieldValue . '%');
						}
						if ($fieldName == "company_name") {
							$DB->where("company_name", 'like', '%' . $fieldValue . '%');
						}
	
						if ($fieldName == "company_refueling") {
							$DB->where("company_refueling", 'like', '%' . $fieldValue . '%');
						}
	
						if ($fieldName == "select_truck_expiry_type") {
	
							if($fieldValue == "expired_truck_insurance"){
								$DB->where("trucks.truck_insurance_expiration_date", '<=', now()->toDateString());
							}elseif($fieldValue ==  "near_to_expiry_truck_insurance"){
								$DB->whereBetween("trucks.truck_insurance_expiration_date", [now()->toDateString(),now()->addDays(4)->toDateString()]);
							}elseif($fieldValue == "expired_truck_license"){
								$DB->where("trucks.truck_licence_expiration_date", '<=', now()->toDateString());
							}elseif($fieldValue ==  "near_to_expiry_truck_license"){
								$DB->whereBetween("trucks.truck_licence_expiration_date",[now()->toDateString(), now()->addDays(4)->toDateString()]);
							}
						}
					}
				}
			}
			//////////////////////////////////////////filter stop

			if($request->checkType == "allIdsSelected"){
	           Session::put(['truck_insurance_ids'=>$DB->pluck('id')->toArray()]);
	           if(is_array(Session::get('truck_insurance_ids'))) {
				    $count = count(Session::get('truck_insurance_ids'));
				    $Selected = 'allIdsSelected';
				}
			}else if($request->checkType == "allIdsNotSelected"){
				Session::forget('truck_insurance_ids');
				$count = 0;
				$Selected = 'allIdsNotSelected';
			}if ($request->checkType == 'id' && $request->idSelected == 'IdSelected') {
			    $selectedIds = session('truck_insurance_ids', []);
			    $selectedIds[] = $request->id; 
			    Session::put('truck_insurance_ids', $selectedIds);
			    $count = count(Session::get('truck_insurance_ids'));
			    $Selected = '';
			} else if ($request->checkType == 'id' && $request->idSelected == 'IdNotSelected') {
			    $selectedIds = session('truck_insurance_ids', []);
			    $selectedIds = array_diff($selectedIds, [$request->id]);
			    Session::put('truck_insurance_ids', $selectedIds);
			    $count = count(Session::get('truck_insurance_ids'));
			    $Selected = '';
			}
			
	        return response()->json(['status' => true, 'allCount' => $count, 'selected' => $Selected]);

	    }

		$notificationTemplateActions =  NotificationAction::whereIn('action', ['truck_insurance_notification_after_expired', 'truck_insurance_notification_before_30_days'])->get();

		$notificationTemplateIds = NotificationTemplate::whereIn('action', ['truck_insurance_notification_after_expired', 'truck_insurance_notification_before_30_days'])->pluck('id')->toArray();

		$NotificationTemplateDescription = NotificationTemplateDescription::with('NotificationAction', 'NotificationAction.EmailActions.EmailActionsDescription')->whereIn('parent_id', $notificationTemplateIds)->where('language_id', $this->current_language_id())->get();
		
		
		$TemplateDescription = NotificationTemplateDescription::whereIn('parent_id', $notificationTemplateIds)->get();

		$options =  NotificationAction::whereIn('action', ['truck_insurance_notification_after_expired', 'truck_insurance_notification_before_30_days'])->value('options');
		$optionsvalue = explode(',', $options);

		// Email Notification...
		$emailTemplateActions =  EmailAction::whereIn('action', ['truck_insurance_notification_after_expired', 'truck_insurance_notification_before_30_days'])->get();


		$emailTemplatesIds       =  EmailTemplate::whereIn('action', ['truck_insurance_notification_after_expired', 'truck_insurance_notification_before_30_days'])->pluck('id')->toArray();

		$Email_Template_Description = EmailTemplateDescription::with('EmailAction')->whereIn('parent_id', $emailTemplatesIds)->where('language_id', $this->current_language_id())->get();

		$emailTemplateDescription = EmailTemplateDescription::whereIn('parent_id', $emailTemplatesIds)->get();

		$emailoptions =  EmailAction::whereIn('action', ['truck_insurance_notification_after_expired', 'truck_insurance_notification_before_30_days'])->value('options');
		$emailoptionsvalue = explode(',', $options);

		return view('admin.'.$this->model.'.truck_insurance_expired_notification', compact('notificationTemplateActions', 'notificationTemplateIds', 'NotificationTemplateDescription', 'TemplateDescription', 'optionsvalue', 'emailTemplateActions', 'emailTemplatesIds', 'emailTemplateDescription', 'Email_Template_Description', 'emailoptions', 'emailoptionsvalue'));

	}

	public function sendInsuranceExpireNotification(Request $request){
		
			   
		$truckIds = Session::get('truck_insurance_ids') ?? 0;
		$DB = Truck::with('truck_company_details', 'truckCompanyDetails', 'truckTypeDetails', 'companyRefueling', 'companyTidulakDetails')->whereIn('id', $truckIds);
		$truckLists = $DB->get();

   		foreach($request->input("notificationAction") as $notificationAction){
   			$notificationActions 	= 	NotificationAction::where('action','=', $notificationAction)
			->get()
			->toArray();

			$cons 			= 	explode(',',$notificationActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}

			$map_id = 0;
			
			$notification = $request->input($notificationAction);

			// Email actions
			$emailActions 	= 	EmailAction::where('action','=',$notificationAction)->get()->toArray();
			$emailCons 			= 	explode(',',$emailActions[0]['options']);
			$emailConstants 		= 	array();
			foreach($emailCons as $key => $val){
				$emailConstants[] = '{'.$val.'}';
			}
			
			foreach($truckLists as $truckList){

			    $rep_Array 		   = array(
					$truckList->truck_company_details ?->company_name,
					$truckList->truck_system_number,
					$truckList->companyRefueling ?->code,
					$truckList->truckTypeDetails ?->name,
					$truckList->companyTidulakDetails ?->code,
					$truckList->truck_insurance_expiration_date,
				);

				// Email send...
			    foreach($notification['email'] as $key => $description){

					if($description['language_id'] == $truckList->truckCompanyDetails ?->language){
					
						if($request->email_notification == 1 && $truckList->truckCompanyDetails->email != ""){
							$messageBody 	= 	str_replace($emailConstants, $rep_Array, $description['description']);
							$requestData = [
								"email" => $truckList->truckCompanyDetails->email,
								"name" => $truckList->truckCompanyDetails->name,
								"subject" => $description["subject"],
								"messageBody" => $messageBody,
							];
							SendMail::dispatch($requestData)->onQueue('send_mail');  
						}
							
					}

				}
               
				foreach($notification['notification'] as $key => $description){
                    if($request->system_notification == 1){
						$notificationObj = new Notification();
						$notificationObj->user_id				= $truckList->truck_company_id;
						$notificationObj->language_id			= $description['language_id'];
						$notificationObj->title					= $description["subject"];
						$notificationObj->description			= str_replace($constants, $rep_Array, $description['description']);
						$notificationObj->is_read				= 0;
						$notificationObj->shipment_id			= 0;
						$notificationObj->notification_type		= $notificationAction;
						$notificationObj->is_notification_sent	= 0;
						$notificationObj->map_id 				= $map_id;
						$notificationObj->save();
						if($map_id == 0){
							$notificationObj->map_id 			= $notificationObj->id;
							$notificationObj->save();
							$map_id = $notificationObj->id;
						}
				    }


					if($truckList->truckCompanyDetails->language == $description['language_id'] ){
						$selectedNotification   = $description;
						$message               = str_replace($constants, $rep_Array, $description['description']);
						$notification_type      = $notificationAction;
						$title                 = $description['subject'];
						$service_request_id    = 0;
						
						// System notification 
						if($request->system_notification == 1){
						 $service_number        = $notificationObj->id;
				     	}
						
						// Whatsapp notification 
						if($request->whatsapp_notification == 1){
							//send whatsapp message
							SendGreenApiMessage::dispatch($message,$truckList->truckCompanyDetails->toArray())->onQueue('send_green_api_message');
						}
					}

					
		
				}

                // System notification 
				if($request->system_notification == 1){
					$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

					$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckList->truckCompanyDetails->id)->orderBy("id","DESC")->first();

					if($user_device_tokens){
						$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
						$adwance_options = array(
							'type' 				=> 'shipment',
							'map_id'			=> $map_id,
							'shipments_status'	=> 0,
							'request_number'	=> 0,
						);
						SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
					}
			    }
			}  		
   		}


   		Session::forget('truck_insurance_ids');
		Session()->flash('flash_notice', trans('messages.truck_insurance_expiration_notification_message'));
		return redirect()->route('truck.insurance.index');


	}

	public function truckLicenceExpiryNotification(Request $request){

	    if($request->isMethod("post") && $request->submit == null){
			$DB = Truck::query();
			$DB->leftjoin('truck_type_descriptions', 'trucks.type_of_truck', 'truck_type_descriptions.parent_id')
				->leftjoin('user_company_informations', 'trucks.truck_company_id', "=", 'user_company_informations.user_id');
			$DB->select(
				'user_company_informations.company_name',
				'trucks.id',
				'trucks.truck_company_id',
				'trucks.truck_system_number',
				'trucks.truck_licence_expiration_date',
				'trucks.truck_insurance_expiration_date',
				'trucks.is_active',
				'trucks.created_at',
				'trucks.updated_at',
				DB::Raw("(select name from truck_type_descriptions where language_id = " . getAppLocaleId() . " and parent_id = trucks.type_of_truck) as type_of_truck")
			);
			$DB->where('truck_type_descriptions.language_id', getAppLocaleId());
			$DB->where('trucks.is_deleted', 0);

			//////////////////////////////////////////filter start
			$searchVariable = json_decode(html_entity_decode($request->searchVariable),true);

			if (count($searchVariable)>0) {
				$searchData = $searchVariable;
				 if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('trucks.truck_licence_expiration_date', [date('Y-m-d H:i:s',strtotime($dateS)), date('Y-m-d H:i:s',strtotime($dateE))]); 											
            }elseif(!empty($searchData['date_from'])){
                $dateS = $searchData['date_from'];
                $DB->where('trucks.truck_licence_expiration_date','>=' ,[date('Y-m-d H:i:s',strtotime($dateS." 00:00:00"))]); 
            }elseif(!empty($searchData['date_to'])){
                $dateE = $searchData['date_to'];
                $DB->where('trucks.truck_licence_expiration_date','<=' ,[date('Y-m-d H:i:s',strtotime($dateE." 23:59:59"))]); 						
            }
			foreach ($searchData as $fieldName => $fieldValue) {
				if ($fieldValue != "") {
					if ($fieldName == "truck_type") {
						$DB->where("trucks.type_of_truck", $fieldValue);
					}
					if ($fieldName == "truck_system_number") {
						$DB->where("trucks.truck_system_number", 'like', '%' . $fieldValue . '%');
					}
					if ($fieldName == "company_name") {
						$DB->where("company_name", 'like', '%' . $fieldValue . '%');
					}

					if ($fieldName == "company_refueling") {
						$DB->where("company_refueling", 'like', '%' . $fieldValue . '%');
					}

					if ($fieldName == "select_truck_expiry_type") {

						if($fieldValue == "expired_truck_insurance"){
							$DB->where("trucks.truck_insurance_expiration_date", '<=', now()->toDateString());
						}elseif($fieldValue ==  "near_to_expiry_truck_insurance"){
							$DB->whereBetween("trucks.truck_insurance_expiration_date", [now()->toDateString(),now()->addDays(4)->toDateString()]);
						}elseif($fieldValue == "expired_truck_license"){
							$DB->where("trucks.truck_licence_expiration_date", '<=', now()->toDateString());
						}elseif($fieldValue ==  "near_to_expiry_truck_license"){
							$DB->whereBetween("trucks.truck_licence_expiration_date", [now()->toDateString(),now()->addDays(4)->toDateString()]);
						}
					}

				}
				$searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
			}
			}
			//////////////////////////////////////////filter stop

			if($request->checkType == "allIdsSelected"){
	           Session::put(['licence_expiry_ids'=>$DB->pluck('id')->toArray()]);
	           if(is_array(Session::get('licence_expiry_ids'))) {
				    $count = count(Session::get('licence_expiry_ids'));
				    $Selected = 'allIdsSelected';
				}
			}else if($request->checkType == "allIdsNotSelected"){
				Session::forget('licence_expiry_ids');
				$count = 0;
				$Selected = 'allIdsNotSelected';
			}if ($request->checkType == 'id' && $request->idSelected == 'IdSelected') {
			    $selectedIds = session('licence_expiry_ids', []);
			    $selectedIds[] = $request->id; 
			    Session::put('licence_expiry_ids', $selectedIds);
			    $count = count(Session::get('licence_expiry_ids'));
			    $Selected = '';
			} else if ($request->checkType == 'id' && $request->idSelected == 'IdNotSelected') {
			    $selectedIds = session('licence_expiry_ids', []);
			    $selectedIds = array_diff($selectedIds, [$request->id]);
			    Session::put('licence_expiry_ids', $selectedIds);
			    $count = count(Session::get('licence_expiry_ids'));
			    $Selected = '';
			}
			
	        return response()->json(['status' => true, 'allCount' => $count, 'selected' => $Selected]);

	    }else{

			$notificationTemplateActions =  NotificationAction::whereIn('action', ['truck_licence_notification_after_expired', 'truck_licence_notification_before_30_days'])->get();

		    $notificationTemplateIds = NotificationTemplate::whereIn('action', ['truck_licence_notification_after_expired', 'truck_licence_notification_before_30_days'])->pluck('id')->toArray();

		    $NotificationTemplateDescription = NotificationTemplateDescription::with('NotificationAction')->whereIn('parent_id', $notificationTemplateIds)->where('language_id', $this->current_language_id())->get();

		    $TemplateDescription = NotificationTemplateDescription::whereIn('parent_id', $notificationTemplateIds)->get();

		    $options =  NotificationAction::whereIn('action', ['truck_licence_notification_after_expired', 'truck_licence_notification_before_30_days'])->value('options');
	        $optionsvalue = explode(',', $options);


	         // Email Notification...
	        $emailTemplateActions =  EmailAction::whereIn('action', ['truck_licence_notification_after_expired', 'truck_licence_notification_before_30_days'])->get();


            $emailTemplatesIds       =  EmailTemplate::whereIn('action', ['truck_licence_notification_after_expired', 'truck_licence_notification_before_30_days'])->pluck('id')->toArray();

            $Email_Template_Description = EmailTemplateDescription::with('EmailAction')->whereIn('parent_id', $emailTemplatesIds)->where('language_id', $this->current_language_id())->get();

            $emailTemplateDescription = EmailTemplateDescription::whereIn('parent_id', $emailTemplatesIds)->get();

            $emailoptions =  EmailAction::whereIn('action', ['truck_licence_notification_after_expired', 'truck_licence_notification_before_30_days'])->value('options');
	        $emailoptionsvalue = explode(',', $options);


		    return view('admin.'.$this->model.'.truck_licence_expired_notification', compact('notificationTemplateActions', 'notificationTemplateIds', 'NotificationTemplateDescription', 'TemplateDescription', 'optionsvalue', 'emailTemplateActions', 'emailTemplatesIds', 'emailTemplateDescription', 'Email_Template_Description', 'emailoptions', 'emailoptionsvalue'));

	    }

	}

	public function sendlicenceExpireNotification(Request $request){
		$truckIds = Session::get('licence_expiry_ids') ?? 0;
		$DB = Truck::with('truck_company_details', 'truckCompanyDetails', 'truckTypeDetails', 'companyRefueling', 'companyTidulakDetails')->whereIn('id', $truckIds);
		$truckLists = $DB->get();
   		foreach($request->input("notificationAction") as $notificationAction){
   			$notificationActions 	= 	NotificationAction::where('action','=', $notificationAction)
			->get()
			->toArray();

			$cons 			= 	explode(',',$notificationActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}

			
			
			$map_id = 0;
			
			$notification = $request->input($notificationAction);

			// Email actions
			$emailActions 	= 	EmailAction::where('action','=',$notificationAction)->get()->toArray();
			$emailCons 			= 	explode(',',$emailActions[0]['options']);
			$emailConstants 		= 	array();
			foreach($emailCons as $key => $val){
				$emailConstants[] = '{'.$val.'}';
			}

			foreach($truckLists as $truckList){

				$TruckCompany = User::where("id", $truckList->truck_company_id)->first();

				$rep_Array 		   = array(
					$truckList->truck_company_details ?->company_name,
					$truckList->truck_system_number,
					$truckList->companyRefueling ?->code,
					$truckList->truckTypeDetails ?->name,
					$truckList->companyTidulakDetails ?->code,
					$truckList->truck_licence_expiration_date,
				);

				// Email send...
			    foreach($notification['email'] as $key => $description){

					if($description['language_id'] == $truckList->truckCompanyDetails ?->language){
					
						if($request->email_notification == 1 && $truckList->truckCompanyDetails->email != ""){
							$messageBody 	= 	str_replace($emailConstants, $rep_Array, $description['description']);
							$requestData = [
								"email" => $truckList->truckCompanyDetails->email,
								"name" => $truckList->truckCompanyDetails->name,
								"subject" => $description["subject"],
								"messageBody" => $messageBody,
							];
							SendMail::dispatch($requestData)->onQueue('send_mail');  
						}
							
					}

				}

				foreach($notification['notification'] as $key => $description){
					
					if($request->system_notification == 1){
						$notificationObj = new Notification();
						$notificationObj->user_id				= $truckList->truck_company_id;
						$notificationObj->language_id			= $description['language_id'];
						$notificationObj->title					= $description["subject"];
						$notificationObj->description			= str_replace($constants, $rep_Array, $description['description']);
						$notificationObj->is_read				= 0;
						$notificationObj->shipment_id			= 0;
						$notificationObj->notification_type		= $notificationAction;
						$notificationObj->is_notification_sent	= 0;
						$notificationObj->map_id 				= $map_id;
						$notificationObj->save();
						if($map_id == 0){
							$notificationObj->map_id 			= $notificationObj->id;
							$notificationObj->save();
							$map_id = $notificationObj->id;
						}
				    }
					if($TruckCompany['language'] == $description['language_id'] ){
						$selectedNotification   = $description;
						$message               = str_replace($constants, $rep_Array, $description['description']);
						$notification_type      = $notificationAction;
						$title                 = $description['subject'];
						$service_request_id    = 0;
						
						// System notification 
						if($request->system_notification == 1){
						 $service_number        = $notificationObj->id;
					    }
						
						// Whatsapp notification 
						if($request->whatsapp_notification == 1){
						 //send whatsapp message
						 SendGreenApiMessage::dispatch($message,$truckList->truckCompanyDetails->toArray())->onQueue('send_green_api_message');
					    }
					}
		
				}

				// System notification 
				if($request->system_notification == 1){
					$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

					$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckList->truckCompanyDetails->id)->orderBy("id","DESC")->first();
					
					if($user_device_tokens){
						$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
						$adwance_options = array(
							'type' 				=> 'shipment',
							'map_id'			=> $map_id,
							'shipments_status'	=> 0,
							'request_number'	=> 0,
						);
						SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
					}
			    }
			}  		


   		}


   		Session::forget('licence_expiry_ids');
		Session()->flash('flash_notice', trans('messages.truck_licence_expiration_notification_message'));
		return redirect()->route('truck.license.index');


	}

    public function export_index_license(Request $request)
	{

		$list[0] = array(
			trans("messages.Type_of_truck"),
			trans("messages.Truck Number"),
			trans("messages.Company Name"),
			trans("messages.end_of_license_date"),
			trans("messages.license_status"),
			trans("messages.admin_common_Status"),
		);
		
		$customers_export = Session::get('export_data_track_licence');
		
		foreach ($customers_export as $key => $excel_export) {
			$list[] = array(
				$excel_export->type_of_truck,
				$excel_export->truck_system_number,
				$excel_export->company_name,
				($excel_export->truck_licence_expiration_date ? Carbon::parse($excel_export->truck_licence_expiration_date)->format(config("Reading.date_format")) : ''),
				($excel_export->truck_licence_expiration_date == null || ( $excel_export->truck_licence_expiration_date != null && Carbon::parse($excel_export->truck_licence_expiration_date)->lte(now()) ) ? trans("messages.admin_common_Expired") : trans("messages.admin_common_Valid")),
				($excel_export->is_active==1 ? trans("messages.admin_common_Activated") : trans("messages.admin_common_Deactivated") ),
			);
			
		}
		$collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'truck_licence_list.xlsx');
	}


	public function NotificationSet(Request $request){
		$thisData = $request->all();
		$returnArr = array();
		foreach($request->notificationAction  as $notificationAction){
			$returnArr['notification'][$notificationAction]['subject'] = $thisData[$notificationAction]['notification'][$this->current_language_id()]['subject'];
			$returnArr['notification'][$notificationAction]['description'] = $thisData[$notificationAction]['notification'][$this->current_language_id()]['description'];


			$returnArr['email'][$notificationAction]['subject'] = $thisData[$notificationAction]['email'][$this->current_language_id()]['subject'];
			$returnArr['email'][$notificationAction]['description'] = $thisData[$notificationAction]['email'][$this->current_language_id()]['description'];
		}
		return response()->json([
			'data' => $returnArr,
		]);
	}

}
