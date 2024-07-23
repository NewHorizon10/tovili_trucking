<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\TruckCompanySubscription;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\NotificationAction;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateDescription;
use Config, Session, DB;
use App\Jobs\SendMail;
use App\Models\Notification;
use App\Models\TruckCompanyRequestSubscription;
use App\Models\EmailTemplateDescription;

use Illuminate\Support\Collection;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;


use App\Jobs\InsertNotification;
use App\Jobs\SendPushNotification;
use App\Jobs\SendGreenApiMessage;
use Illuminate\Support\Facades\Log;


class CompanySubscriptionPlansController extends Controller
{
    public $model				=	'company-subscription-plans';
    public function __construct(Request $request) {
        parent::__construct();
        View()->share('model',$this->model);
        $this->request = $request;
    }

    public function index(Request $request)
    {

        $DB					=	TruckCompanySubscription::query();
        $DB->leftjoin('plans', 'plans.id', 'truck_company_subscription_plans.plan_id');
        $DB->with('companyName');
        $allResultCount = $DB->count();
		$searchVariable		=	array();
		$inputGet			=	$request->all();
		if ($request->all()) {
			$searchData			=	$request->all();
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
				$start_time = date("y-m-d", strtotime($dateS));
				$end_time = date("y-m-d", strtotime($dateE));
				$DB->whereBetween('truck_company_subscription_plans.end_time', [$start_time." 00:00:00", $end_time." 23:59:59"]);
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$start_time = date("y-m-d", strtotime($dateS));
				$DB->where('truck_company_subscription_plans.end_time','>=' ,[$start_time." 00:00:00"]);
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$end_time = date("y-m-d", strtotime($dateE));
				$DB->where('truck_company_subscription_plans.end_time','<=' ,[$end_time." 00:00:00"]);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "truck_company_name"){
						$DB->whereHas("companyName", function($query) use($fieldValue){
							$query->where('company_name', 'like', '%' . $fieldValue . '%');
						});
					}
					if ($fieldName == "plan_duration") {
                        $DB->where('truck_company_subscription_plans.type', $fieldValue);
                    }
                    if ($fieldName == "plan_name") {
                        $DB->where('plans.plan_name', 'like', '%' . $fieldValue . '%');
                    }
                    if($fieldName == "price"){
						$DB->where("truck_company_subscription_plans.price",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "discount"){
						$DB->where("truck_company_subscription_plans.discount",'=', $fieldValue);
					}
					if($fieldName == "total_price"){
						$DB->where("truck_company_subscription_plans.total_price",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_free"){
						$DB->where("truck_company_subscription_plans.is_free",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "status"){
						$DB->where("truck_company_subscription_plans.status",'like','%'.$fieldValue.'%');
					}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		    }
		    $allResultCount = $DB->count();
	    }
	 
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'truck_company_subscription_plans.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	    =	($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");

		$DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_company_subscription_plans'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);

		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();

		return  View("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string', 'allResultCount'));
  }


  public function truckCompanySubscriptionPlanNotification(Request $request){

      if($request->isMethod("post")){      
        $DB					=	TruckCompanySubscription::query();
        $DB->leftjoin('plans', 'plans.id', 'truck_company_subscription_plans.plan_id');
        $DB->with('companyName');

        //////////////////////////////////////////filter start
		$searchVariable = json_decode(html_entity_decode($request->searchVariable),true);
		if (count($searchVariable)>0) {
			$searchData = $searchVariable;

			if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$start_time = date("y-m-d", strtotime($dateS));
				$end_time = date("y-m-d", strtotime($dateE));
				$DB->whereBetween('truck_company_subscription_plans.end_time', [$start_time." 00:00:00", $end_time." 23:59:59"]);
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$start_time = date("y-m-d", strtotime($dateS));
				$DB->where('truck_company_subscription_plans.end_time','>=' ,[$start_time." 00:00:00"]);
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$end_time = date("y-m-d", strtotime($dateE));
				$DB->where('truck_company_subscription_plans.end_time','<=' ,[$end_time." 00:00:00"]);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "truck_company_name"){
						$DB->whereHas("companyName", function($query) use($fieldValue){
							$query->where('company_name', 'like', '%' . $fieldValue . '%');
						});
					}
					if ($fieldName == "plan_duration") {
                        $DB->where('truck_company_subscription_plans.type', $fieldValue);
                    }
                    if ($fieldName == "plan_name") {
                        $DB->where('plans.plan_name', 'like', '%' . $fieldValue . '%');
                    }
                    if($fieldName == "price"){
						$DB->where("truck_company_subscription_plans.price",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "discount"){
						$DB->where("truck_company_subscription_plans.discount",'=', $fieldValue);
					}
					if($fieldName == "total_price"){
						$DB->where("truck_company_subscription_plans.total_price",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_free"){
						$DB->where("truck_company_subscription_plans.is_free",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "status"){
						$DB->where("truck_company_subscription_plans.status",'like','%'.$fieldValue.'%');
					}
				 $searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			    }
		    }

		}
        //////////////////////////////////////////filter stop

        if($request->checkType == "allIdsSelected"){
	           Session::put(['company_subscription_plans_ids'=>$DB->pluck('truck_company_subscription_plans.id')->toArray()]);
	           if(is_array(Session::get('company_subscription_plans_ids'))) {
				    $count = count(Session::get('company_subscription_plans_ids'));
				    $Selected = 'allIdsSelected';
				}
			}else if($request->checkType == "allIdsNotSelected"){
				Session::forget('company_subscription_plans_ids');
				$count = 0;
				$Selected = 'allIdsNotSelected';
			}if ($request->checkType == 'id' && $request->idSelected == 'IdSelected') {
			    $selectedIds = session('company_subscription_plans_ids', []);
			    $selectedIds[] = $request->id; 
			    Session::put('company_subscription_plans_ids', $selectedIds);
			    $count = count(Session::get('company_subscription_plans_ids'));
			    $Selected = '';
			} else if ($request->checkType == 'id' && $request->idSelected == 'IdNotSelected') {
			    $selectedIds = session('company_subscription_plans_ids', []);
			    $selectedIds = array_diff($selectedIds, [$request->id]);
			    Session::put('company_subscription_plans_ids', $selectedIds);
			    $count = count(Session::get('company_subscription_plans_ids'));
			    $Selected = '';
			}

			
	        return response()->json(['status' => true, 'allCount' => $count, 'selected' => $Selected]);

      }else{


  	    $notificationTemplateActions =  NotificationAction::whereIn('action', ['company_subscription_plan_expire_date', 'company_subscription_menu_notification_to_truck_company'])->get();

	    $notificationTemplateIds = NotificationTemplate::whereIn('action', ['company_subscription_plan_expire_date', 'company_subscription_menu_notification_to_truck_company'])->pluck('id')->toArray();

	    $NotificationTemplateDescription = NotificationTemplateDescription::with('NotificationAction')->whereIn('parent_id', $notificationTemplateIds)->where('language_id', $this->current_language_id())->get();

	    $TemplateDescription = NotificationTemplateDescription::whereIn('parent_id', $notificationTemplateIds)->get();

	    $options =  NotificationAction::whereIn('action', ['company_subscription_plan_expire_date', 'company_subscription_menu_notification_to_truck_company'])->value('options');
        $optionsvalue = explode(',', $options);


         // Email Notification...
        $emailTemplateActions =  EmailAction::whereIn('action', ['company_subscription_plan_expire_date', 'company_subscription_menu_notification_to_truck_company'])->get();


        $emailTemplatesIds       =  EmailTemplate::whereIn('action', ['company_subscription_plan_expire_date', 'company_subscription_menu_notification_to_truck_company'])->pluck('id')->toArray();

        $Email_Template_Description = EmailTemplateDescription::with('EmailAction')->whereIn('parent_id', $emailTemplatesIds)->where('language_id', $this->current_language_id())->get();

        $emailTemplateDescription = EmailTemplateDescription::whereIn('parent_id', $emailTemplatesIds)->get();

        $emailoptions =  EmailAction::whereIn('action', ['company_subscription_plan_expire_date', 'company_subscription_menu_notification_to_truck_company'])->value('options');
        $emailoptionsvalue = explode(',', $options);


	    return view('admin.'.$this->model.'.subscription-plan-notification', compact('notificationTemplateActions', 'notificationTemplateIds', 'NotificationTemplateDescription', 'TemplateDescription', 'optionsvalue', 'emailTemplateActions', 'emailTemplatesIds', 'emailTemplateDescription', 'Email_Template_Description', 'emailoptions', 'emailoptionsvalue'));



      }

  }

  public function sendCompanySubscriptionPlanNotification(Request $request){
	$subscriptionPlanIds = Session::get('company_subscription_plans_ids') ?? 0;
	$DB					=	TruckCompanySubscription::query();
	$DB->with('companyUser');
	$DB->leftjoin('plans', 'plans.id', 'truck_company_subscription_plans.plan_id');
	$DB->whereIn('truck_company_subscription_plans.truck_company_id', $subscriptionPlanIds);
	$DB->with('companyName');
	$subscriptionPlanLists = $DB->get();
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

			

			foreach($subscriptionPlanLists as $planList){

				$typeData = '';
		        $columntypeData = '';
		        if ($planList['type'] == '0') {
		            $typeData = trans('messages.monthly');
		        } elseif ($planList['type'] == '1') {
		            $typeData = trans('messages.quarterly');
		        } elseif ($planList['type'] == '2') {
		            $typeData = trans('messages.Half Yearly');
		        } elseif ($planList['type'] == '3') {
		            $typeData = trans('messages.Yearly');
		        }

		    
		        if ($planList['column_type'] == '0') {
		            $columntypeData = trans('messages.Up to 5 Trucks');
		        }  else if ($planList['column_type'] == '1'){
		            $columntypeData = trans('messages.More then 5');
		        }



		        $characters = 'abcdefghijklmnopqrstuvwxyz';
		        $validate_string = '';

		        for ($i = 0; $i < strlen($characters); $i++) {
		            $validate_string .= $characters[rand(0, strlen($characters) - 1)];
		        }

		        $paymentUrl = route('plan-subscription', $validate_string);	


				$rep_Array 		   = array(
					$planList->companyUser->name,
					$planList->companyUser->email,
					$planList->companyUser->phone_number,
					$planList['price'],
					($planList['discount']>0 ? " ".trans("messages.discount")." : ".$planList['discount']."," : ''),
					$planList['total_price'],
					$typeData,
					$columntypeData,
					date('Y-m-d', strtotime($planList['end_time'])),
					$paymentUrl
				);

				// Email send...
			foreach($notification['email'] as $key => $description){

				if($description['language_id'] == $planList->companyUser ?->language){
				
					if($request->email_notification == 1 && $planList->companyUser->email != ""){
						$messageBody 	= 	str_replace($emailConstants, $rep_Array, $description['description']);
						$requestData = [
							"email" => $planList->companyUser->email,
							"name" => $planList->companyUser->name,
							"subject" => $description["subject"],
							"messageBody" => $messageBody,
						];
						SendMail::dispatch($requestData)->onQueue('send_mail');  
					}
						
				}

			}

		        $oldPlanDetails = TruckCompanyRequestSubscription::where('truck_company_id', $planList->truck_company_id)->first();


		        $obj                    = new TruckCompanyRequestSubscription;
		        $obj->truck_company_id  = $planList->truck_company_id;
		        $obj->is_free           = $planList->is_free;
		        $obj->plan_id           = $planList->plan_id;
		        $obj->price             = $planList->price;
		        $obj->discount          = $planList->discount;
		        $obj->total_price       = $planList->total_price;
		        $obj->type              = $planList->type;
		        $obj->column_type       = $planList->column_type;
		        $obj->total_truck       = 0;
		        $obj->validate_string   = $validate_string;
		        $obj->save();


				foreach($notification['notification'] as $key => $description){

					if($request->system_notification == 1){
						$notificationObj = new Notification();
						$notificationObj->user_id				= $planList->truck_company_id;
						$notificationObj->language_id			= $description['language_id'];
						$notificationObj->title					= $description["subject"];
						$notificationObj->description			= str_replace($constants, $rep_Array, $description['description']);
						$notificationObj->is_read				= 0;
						$notificationObj->shipment_id			= 0;
						$notificationObj->notification_type		= $notificationAction;
						$notificationObj->url		            = $paymentUrl;
						$notificationObj->is_notification_sent	= 0;
						$notificationObj->map_id 				= $map_id;
						$notificationObj->save();
						if($map_id == 0){
							$notificationObj->map_id 			= $notificationObj->id;
							$notificationObj->save();
							$map_id = $notificationObj->id;
						}
				    }
					if($planList->companyUser->language == $description['language_id'] ){
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
						 SendGreenApiMessage::dispatch($message,$planList->companyUser->toArray())->onQueue('send_green_api_message');
					    }
					}

					
		
				}

				// System notification 
				if($request->system_notification == 1){
					$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

					$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$planList->companyUser->id)->orderBy("id","DESC")->first();

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


   		Session::forget('company_subscription_plans_ids');
		Session()->flash('flash_notice', trans('messages.truck_company_subscription_plan_expiration_notification_message'));
		return redirect()->route($this->model.'.index');

  }

  public function sendNotification($ids){
  	
  	    $TruckCompanySubscription = TruckCompanySubscription::with('companyUser')->whereIn('truck_company_id', $ids)->where('status', 'activate')
			->get();

 
        $settingsEmail 	= 	Config::get("Site.from_email");

		$emailActions 	= 	EmailAction::where('action','=','company_subscription_plan_expire_date')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','company_subscription_plan_expire_date')->get(array('name','subject','action','body', 'mail_enable'))-> toArray();
		$cons 			= 	explode(',',$emailActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];


        if($TruckCompanySubscription->count() > 0){

			foreach($TruckCompanySubscription as $key => $value){
				$type       = $value->type;
				$columnType = $value->column_type;

				if ($type == '0') {
					$typeData = trans('messages.monthly');
				} elseif ($type == '1') {
					$typeData = trans('messages.quarterly');
				} elseif ($type == '2') {
					$typeData = trans('messages.Half Yearly');
				} else {
					$typeData = trans('messages.Yearly');
				}
		
				if ($columnType == '0') {
					$columntypeData = trans('messages.Up to 5 Trucks');
				}  else {
					$columntypeData = trans('messages.More then 5');
				}

				$characters = 'abcdefghijklmnopqrstuvwxyz';
				$validateString = '';

				for ($i = 0; $i < strlen($characters); $i++) {
					$validateString .= $characters[rand(0, strlen($characters) - 1)];
				}
				
				if($value->is_free == 0){
					$paymentUrl = route('plan-subscription', $validateString);
				}else{
					$paymentUrl = route('subscribe-plan', $validateString);
				}
				$truckCompany = $value->companyUser->toArray();

				$Information = array(
					'name'         => $truckCompany['name'],
					'email'        => $truckCompany['email'],
					'phone_number' => $truckCompany['phone_number'],
					'price'        => $value->price,
					'discount'     => $value->discount,
					'total_price'  => round($value->total_price, 2),
					'type'         => $typeData,
					'column_type'  => $columntypeData,
					'expireDate'   => date(Config('Reading.date_format'), strtotime($value->end_time)),
					'paymentUrl'   => $paymentUrl,
				);


				$this->company_subscription_plan_expire_date($value->truck_company_id, $Information, $truckCompany);
				
				$full_name 		= 	$truckCompany['name'];
				$email 			=	$truckCompany['email'];
				$messageBody 	= 	str_replace($constants, $Information, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $email,
					"name" => $full_name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
				if($emailTemplates[0]['mail_enable'] == 1){
				  SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}

		}
  }


  public function export(Request $request){

        $list[0] = array(
			trans("messages.admin_Truck_Company") . trans('messages.name'),
			trans("messages.admin_plan_name"),
			trans("messages.admin_common_Plan_Duration"),
			trans("messages.price"),
			trans("messages.discount"),
			trans("messages.total_price"),
			trans("messages.admin_common_Is_Free"),
			trans("messages.end_time"),
			trans("messages.admin_common_Status"),
		);
		
		$customers_export = Session::get('export_data_company_subscription_plans');

        foreach ($customers_export as $key => $excel_export) {

            $typeData = '';
            if ($excel_export->type == '0') {
                $typeData = trans('messages.monthly');
            } elseif ($excel_export->type == '1') {
                $typeData = trans('messages.quarterly');
            } elseif ($excel_export->type == '2') {
                $typeData = trans('messages.Half Yearly');
            } elseif ($excel_export->type == '3') {
                $typeData = trans('messages.Yearly');
            }



			$list[] = array(
            
            $excel_export->companyName ?->company_name,
            $excel_export->plan_name,
            $typeData,
            $excel_export->price,
            $excel_export->discount,
            $excel_export->total_price,
            ($excel_export->is_free == 0 ? trans('messages.paid') : trans('messages.Free')),
            date(Config('Reading.date_format'), strtotime($excel_export->end_time)),
            $excel_export->status,

            );
            }

        $collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'Company Subscription Plans Report.xlsx');

    }


}