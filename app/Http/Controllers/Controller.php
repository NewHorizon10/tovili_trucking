<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\path;
use Illuminate\Support\Facades\DB;
use App\Models\AdminActionLog;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\UserCompanyInformation;
use Illuminate\Support\Facades\File;

use App\Models\Shipment;
use App\Models\ShipmentStop;

use App\Models\Truck;
use App\Models\TruckTypeDescription;

use App\Models\Notification;
use App\Models\NotificationAction;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateDescription;

use App\Jobs\InsertNotification;
use App\Jobs\SendPushNotification;
use App\Jobs\SendGreenApiMessage;
use Illuminate\Support\Facades\Log;

use Config;
use Mail;
use Request,Str;
use Session,App;
use App\Jobs\SendMail;

class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $user;
	
	public function __construct() {
		$url	=	Request::fullUrl();


	}// end function __construct()

	public function checkPermission($url){
		$segment1	=	Request()->segment(1);
		$segment2	=	Request()->segment(2);
		$segment3	=	Request()->segment(3);
		
		$segment1_1 = explode(' ', $segment1);
		$segment1_1 = end($segment1_1);
		$segment2_2 = explode(' ', $segment2);
		$segment2_2 = end($segment2_2);
		$segment3_3 = explode(' ', $segment3);
		$segment3_3 = end($segment3_3);
		
		if (in_array($segment1_1,$actions_arr) || in_array($segment2_2,$actions_arr) || in_array($segment3_3,$actions_arr)){
			return 1;
		}
		
		$user_id				=	Auth::user()->id;
		$user_role_id			=	Auth::user()->user_role_id;
		$path					=	Request()->path();
		$action					=	Route::current()->getAction();
		
		$function_name	=	explode("\\",$action['controller']);
		$function_name	=	end($function_name);
		$permissionData			=	DB::table("user_permission_actions")
											->select("user_permission_actions.is_active")
											->leftJoin("acl_admin_actions","acl_admin_actions.id","=","user_permission_actions.admin_module_action_id")
											->where('user_permission_actions.user_id',$user_id)
											->where('user_permission_actions.is_active',1)
											->where('acl_admin_actions.function_name',$function_name)
											->first();
		
		$byDefaultPermissionData = DB::table("acl_admin_actions")
		->where('acl_admin_actions.is_show',0)
		->where('acl_admin_actions.function_name',$function_name)
		->first();
		if(!empty($permissionData) || !empty($byDefaultPermissionData)){
			return 1;
		}else{
			return 0;
		}
	}

    public function buildTree($parentId = null){
		
		$user_id	    =	Auth::guard('admin')->user()->id;
		$user_role_id	=	Auth::guard('admin')->user()->user_role_id;
		$branch         =   array();
		$elements       =   array();
		$superadmin = Config('constants.ROLE_ID.SUPER_ADMIN_ROLE_ID');
		$staffadmin = Config('constants.ROLE_ID.STAFF_ROLE_ID');
        $language_id  = Session()->get('sel_lang');
		if($user_role_id == $superadmin){
			$elements = DB::table("acls")
				->select("acls.*", "acls_descriptions.title as title")
				->where("acls.parent_id", $parentId)
				->where("acls.is_active", 1)
				->orderBy('acls.module_order', 'ASC')
				->where("acls_descriptions.language_id",$language_id)
                ->rightJoin("acls_descriptions","acls_descriptions.parent_id","acls.id")
				->get();
		}
		elseif($user_role_id == $staffadmin){
			if($parentId == null){
				$elements = DB::table("acls")
					->select("acls.*","acls_descriptions.title as title")
                    ->where("acls.parent_id",$parentId)
					->where("acls.is_active",1)
					->where("acls.id",DB::raw("(select admin_module_id from user_permissions where user_permissions.admin_module_id = acls.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
					->where("acls_descriptions.language_id",$language_id)
                    ->join("acls_descriptions","acls_descriptions.parent_id","acls.id")
					->orderBy('acls.module_order','ASC')
					->get();
			}else{ 

				$elements = 	DB::table("acls")
					->select("acls.*","acls_descriptions.title as title")
					->where("acls.parent_id",$parentId)
					->where("acls.is_active",1)
					->where("acls.id",DB::raw("(select admin_module_action_id from user_permission_actions where user_permission_actions.admin_module_action_id = acls.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
					->where("acls_descriptions.language_id",$language_id)
					->join("acls_descriptions","acls_descriptions.parent_id","acls.id")
					->orderBy('acls.module_order','ASC')
					->get();  
			}	

		}
		else {
			if($parentId == null){
				$elements = DB::table("acls")
                    ->select("acls.*","acls_descriptions.title as title")
                    ->where("acls.parent_id",$parentId)
					->where("acls.is_active",1)
                    ->where("acls.id",DB::raw("(select admin_module_id from user_permissions where user_permissions.admin_module_id = acls.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
					->where("acls_descriptions.language_id",$language_id)
					->join("acls_descriptions","acls_descriptions.parent_id","acls.id")
                    ->orderBy('acls.module_order','ASC')
                    ->get();
			}else{ 
				$elements = 	DB::table("acls")
					->select("acls.*","acls_descriptions.title as title")
                    ->where("acls.parent_id",$parentId)
					->where("acls.is_active",1)
                    ->where("acls.id",DB::raw("(select admin_module_id from user_permission_actions where user_permission_actions.admin_module_id = acls.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
					->where("acls_descriptions.language_id",$language_id)
					->join("acls_descriptions","acls_descriptions.parent_id","acls.id")
                    ->orderBy('acls.module_order','ASC')
                    ->get();  
			}
		}
        
		foreach($elements as $element){
			if ($element->parent_id == $parentId){
				$children = $this->buildTree($element->id);
				if ($children){
					$element->children = $children;
				}
				$branch[] = $element;
			}
		}
		return $branch;
	}

	public function arrayStripTags($array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            // Don't allow tags on key either, maybe useful for dynamic forms.
            $key = strip_tags($key, config('constants.ALLOWED_TAGS_XSS'));

            // If the value is an array, we will just recurse back into the
            // function to keep stripping the tags out of the array,
            // otherwise we will set the stripped value.
            if (is_array($value)) {
                $result[$key] = $this->arrayStripTags($value);
            } else {
                // I am using strip_tags(), you may use htmlentities(),
                // also I am doing trim() here, you may remove it, if you wish.
                $result[$key] = trim(strip_tags($value, config('constants.ALLOWED_TAGS_XSS')));
            }
        }

        return $result;

    }

	public function change_error_msg_layout($errors = array())
    {
        $response = array();
        $response["status"] = "error";
        if (!empty($errors)) {
            $error_msg = "";
            foreach ($errors as $errormsg) {
                $error_msg1 = (!empty($errormsg[0])) ? $errormsg[0] : "";
                $error_msg .= $error_msg1 . ", ";
            }
            $response["msg"] = trim($error_msg, ", ");
        } else {
            $response["msg"] = "";
        }
        $response["data"] = (object) array();
        $response["errors"] = $errors;
        return $response;
    }

    public function change_error_msg_layout_with_array($errors = array())
    {
        $response = array();
        $response["status"] = "error";
        if (!empty($errors)) {
            $error_msg = "";
            foreach ($errors as $errormsg) {
                $error_msg1 = (!empty($errormsg[0])) ? $errormsg[0] : "";
                $error_msg .= $error_msg1 . ", ";
            }
            $response["msg"] = trim($error_msg, ", ");
        } else {
            $response["msg"] = "";
        }
        $response["data"] = array();
        $response["errors"] = $errors;
        return $response;
    }

	public function getVerificationCode(){
		$code = 9999;
	   
		return $code;
	}

	public function setEmailTemplate($action='',$sendData="",$email='')
	{
		$email                                  =  $email ?? Config('Site.email');
        $settingsEmail                          =  Config('Site.email');
        $emailActions                           =  EmailAction::where('action', '=',$action)->get()->toArray();
        $emailTemplates                         =  EmailTemplate::where('action', '=',$action)->select("name", "action","body",'subject')->get()->toArray();
        $cons = explode(',', $emailActions[0]['options']);
        $constants = array();
        foreach ($cons as $key => $val) {
            $constants[] = '{' . $val . '}';
        }
        $subject             = $emailTemplates[0]['subject'];
        $messageBody         = str_replace($constants, $sendData, $emailTemplates[0]['body']);
        $this->sendMail($email,$emailTemplates[0]['subject'],$emailTemplates[0]['subject'],$messageBody, $settingsEmail);
	}

	public function sendMail($to, $fullName, $subject, $messageBody, $from = '', $files = false, $path = '', $attachmentName = '')
    {
        $from = Config::get("Site.from_email");
        $data = array();
        $data['to'] = $to;
        $data['from'] = (!empty($from) ? $from : Config::get("Site.email"));
        $data['fullName'] = $fullName;
        $data['subject'] = $subject;
        $data['filepath'] = $path;
        $data['attachmentName'] = $attachmentName;

        DB::table('email_logs')->insert(
            array(
                'email_to' => $data['to'],
                'email_from' => $from,
                'subject' => $data['subject'],
                'message' => $messageBody,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            )
        );
        if ($files === false) {
            Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject']);
            });
        } else {
            if ($attachmentName != '') {
                Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                    $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath'], array('as' => $data['attachmentName']));
                });
            } else {
                Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                    $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath']);
                });
            }
        }
        
    }

    public function current_language_id(){
		$language_code  = session()->get('admin_applocale');
        $language        = DB::table('languages')->where('lang_code',$language_code)->first();
        $language_id    = $language->id ?? 1;
		
		return $language_id;
	}

    public function language_system_id(){
		$language_system_id = Config('Site.system_will_receive_mail_in_language');
        $language       	= DB::table('languages')->where('id',$language_system_id)->first();
		if($language == null){
			$language_code  	= Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
			$language       	= DB::table('languages')->where('lang_code',$language_code)->first();
			$language_system_id = $language->id ?? 1;
		}		
		return $language_system_id;
	}

	public function genrateSlug($model,$title)
	{
		$slug = Str::slug($title);

		$check = DB::table($model)->where('slug',$slug)->count();
		if($check>0){
			$count = $check++;
			return $slug.$count;
		}else{
			return $slug;
		}
	}

	public function genrateAdminLog($data=array())
	{
		if(Auth::guard('admin')->id()){
			$user_id = Auth::guard('admin')->id();
		}else{
			$user_id = isset($data['user_id'])?$data['user_id']:0;
		} 
		$log = new AdminActionLog();
		$log->user_id 				= $user_id;
		$log->record_id 			= isset($data['record_id'])?$data['record_id']:0;
		$log->module_name 			= isset($data['module_name'])?$data['module_name']:0;
		$log->action_name 			= isset($data['action_name'])?$data['action_name']:0;
		$log->action_description 	= isset($data['action_description'])?$data['action_description']:0;
		$log->record_url 			= isset($data['record_url'])?$data['record_url']:0;
		$log->user_agent 			= isset($data['user_agent'])?$data['user_agent']:0;
		$log->browser_device 		= isset($data['browser_device'])?$data['browser_device']:0;
		$log->location 				= isset($data['location'])?$data['location']:0;
		$log->ip_address 			= isset($data['ip_address'])?$data['ip_address']:0;
		$log->save();
	}
	public function last_activity_date_time($users_ids)
	{
		if (!$users_ids==null) {
			$user = User::query();
			if (is_array($users_ids)) {
				$user->whereIn('id',$users_ids);
			}else{
				$user->where('id',$users_ids);
			}
			 $user->update(['last_activity_date_time'=>date('Y-m-d G:i:s')]);

		}
	}

	/////////////////////Notification/////////////////////
	function new_shipment_request_to_company($objShipment,$already_created_array)
	{
		$notificationActions 	= 	NotificationAction::where('action','=','new_shipment_request_to_company')
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=','new_shipment_request_to_company')
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

        $emailActions 	= 	EmailAction::where('action','=','new_shipment_request_to_company')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','new_shipment_request_to_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];
        	

		// user_company_informations
		$user = Auth::user();
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

		$forNotification = array();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$objShipment->shipment_type,"language_id"=>$description['language_id']])->first()->name),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['destination_address'] : '' ),
				(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['dropoff_zip_code'] : '' ),
				(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['dropoff_city'] : '' ),
				(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['recipients_phone_number'] : '' ),
				(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['name_of_the_receiver'] : '' ),
			);
			$forNotification[$key] = array();
			$forNotification[$key]['language_id'] 		           = $description['language_id'];
			$forNotification[$key]['subject'] 			           = $description["subject"];
			$forNotification[$key]['notification_type']            = "new_shipment_request_to_company";
			$forNotification[$key]['messageBody']  		           = str_replace($constants, $rep_Array, $description['body']);
			$forNotification[$key]['system_notification_enable']   = $description->NotificationAction['system_notification_enable'];
			$forNotification[$key]['whatsapp_notification_enable'] = $description->NotificationAction['whatsapp_notification_enable'];
		
		}

		$rep_Email_Array 		= 	array(
			$userCompanyInformation['contact_person_name'],
			$userCompanyInformation['contact_person_email'],
			$userCompanyInformation['contact_person_phone_number'],
			$objShipment->request_number,
			$objShipment->status,
			(TruckTypeDescription::where(["parent_id"=>$objShipment->shipment_type,"language_id"=>$description['language_id']])->first()->name),
			$objShipment->request_date,
			$objShipment->request_time,
			$objShipment->request_date_flexibility,
			$objShipment->pickup_address,
			$objShipment->pickup_city,
			$objShipment->pickup_zipcode,
			$objShipment->description,
			$objShipment->shipment_end_date,
			(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['destination_address'] : '' ),
			(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['dropoff_zip_code'] : '' ),
			(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['dropoff_city'] : '' ),
			(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['recipients_phone_number'] : '' ),
			(isset($already_created_array['shipment_stops']) ? $already_created_array['shipment_stops'][0]['name_of_the_receiver'] : '' ),
		);


		$messageBody 	= 	str_replace($emailconstants, $rep_Email_Array, $emailTemplates[0]['body']);

        $requestData = [
            "email" => $userCompanyInformation['contact_person_email'],
            "name" => $userCompanyInformation['company_name'] ?? '',
            "subject" => $subject,
            "messageBody" => $messageBody,
        ];

		if($emailTemplates[0]['mail_enable'] == 1){
			SendMail::dispatch($requestData)->onQueue('send_mail');
		}


		$truckCompanyList = Truck::where("type_of_truck",$already_created_array['shipments']['shipment_type'])
			->select("users.*")
			->leftJoin("users","users.id","trucks.truck_company_id")
			->Join("truck_company_subscription_plans","truck_company_subscription_plans.truck_company_id","users.id")
			->where("users.is_approved",1)	
			->where("truck_company_subscription_plans.status" ,'activate')
			->groupBy("users.id")
			->get()->toArray();


		$chunkTruckCompanyList = array_chunk($truckCompanyList,200);
		foreach($chunkTruckCompanyList as $chunkTruckCompany ){
			InsertNotification::dispatch($chunkTruckCompany, $forNotification, $objShipment)->onQueue('insert_notifications');
		}
	}
	function new_offer_created_for_customer($objShipment,$objShipmentOffer)
	{

		$user = User::find($objShipment->customer_id);
		if($user->customer_type == "business"){
			$userCompanyInformation = UserCompanyInformation::where("user_id",$user->id)->first()->toArray();
			$shipmentType = $objShipment->shipment_type;
			$actionType = "new_offer_created_for_busness_customer"; 
		}else{
			$userCompanyInformation = array(
				'contact_person_name'           => $user->name,
				'contact_person_email'          => $user->email,
				'contact_person_phone_number'   => $user->phone_number,
			);
			$shipmentType = $objShipment->request_type;
			$actionType = "new_offer_created_for_private_customer"; 
		}
		$notificationActions 	= 	NotificationAction::where('action','=',$actionType)
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',$actionType)
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();
			
		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		
		$shipmentStops =  ShipmentStop::where("shipment_id",$objShipment->id)->first();
		$map_id = 0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				($shipmentStops->request_dropoff_contact_person_name ?? ""),
				($shipmentStops->request_dropoff_contact_person_name ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);

			if($description->NotificationAction['system_notification_enable'] == 1){ 
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= $actionType;
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id = $notificationObj->id;
				}
	     	}

	        if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
				 //send whatsapp message
				 $whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				 SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');
	            }
		    }


		}
		$rep_Array 		= 	array(
			$user['name'],
			$user['email'],
			$user['phone_number'],
			$objShipment->request_number,
			$objShipment->status,
			(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE')])->first()->name),
			$objShipment->request_date,
			$objShipment->request_time,
			$objShipment->request_date_flexibility,
			$objShipment->pickup_address,
			$objShipment->pickup_city,
			$objShipment->pickup_zipcode,
			$objShipment->description,
			$objShipment->shipment_end_date,
			($shipmentStops->dropoff_address ?? ""),
			($shipmentStops->dropoff_zip_code ?? ""),
			($shipmentStops->dropoff_city ?? ""),
			($shipmentStops->request_dropoff_contact_person_name ?? ""),
			($shipmentStops->request_dropoff_contact_person_name ?? ""),
			$objShipmentOffer->price,
			$objShipmentOffer->extra_time_price,
			$objShipmentOffer->description,
			$objShipmentOffer->payment_condition,
			$objShipmentOffer->status,
			(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			
			($userCompanyInformation['company_name'] ?? ""),
			($userCompanyInformation['company_hp_number'] ?? ""),
			$userCompanyInformation['contact_person_name'],
			$userCompanyInformation['contact_person_email'],
			$userCompanyInformation['contact_person_phone_number'],
		);
	
        $emailActions 	= 	EmailAction::where('action','=','support_mail_for_receiving_offer')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','support_mail_for_receiving_offer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$cons 			= 	explode(',',$emailActions[0]['options']);
        $constants 		= 	array();
        foreach($cons as $key => $val){
            $constants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];
        $messageBody 	= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);

        $requestData = [
            "email" => $user->email,
            "name" => $user->name,
            "subject" => $subject,
            "messageBody" => $messageBody,
        ];

		if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
			SendMail::dispatch($requestData)->onQueue('send_mail');
		}


	}

	function update_offer_for_customer($objShipment,$objShipmentOffer)
	{
		$user = User::find($objShipment->customer_id);
		if($user->customer_type == "business"){
			$userCompanyInformation = UserCompanyInformation::where("user_id",$user->id)->first()->toArray();
			$shipmentType = $objShipment->shipment_type;
			$actionType = "update_offer_for_busness_customer"; 
		}else{
			$userCompanyInformation = array(
				'contact_person_name'           => $user->name,
				'contact_person_email'          => $user->email,
				'contact_person_phone_number'   => $user->phone_number,
			);
			$shipmentType = $objShipment->request_type;
			$actionType = "update_offer_for_private_customer"; 
		}
		$notificationActions 	= 	NotificationAction::where('action','=',$actionType)
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',$actionType)
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

        $TruckCompanyInfo = UserCompanyInformation::where('user_id', $objShipmentOffer->truck_company_id)->first();
		$shipmentStops =  ShipmentStop::where("shipment_id",$objShipment->id)->first();
		$map_id = 0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				($shipmentStops->request_dropoff_contact_person_phone_number ?? ""),
				($shipmentStops->request_dropoff_contact_person_name ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
				$TruckCompanyInfo->company_name,
				$TruckCompanyInfo->contact_person_name,
				$TruckCompanyInfo->contact_person_email,
				$TruckCompanyInfo->contact_person_phone_number,
			);

			if($description->NotificationAction['system_notification_enable'] == 1){ 
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= $actionType;
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id = $notificationObj->id;
				}
		    }

			if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
					SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');
	            }
		    }


		}

		$rep_Array 		= 	array(
			$user['name'],
			$user['email'],
			$user['phone_number'],
			$objShipment->request_number,
			$objShipment->status,
			(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE')])->first()->name),
			$objShipment->request_date,
			$objShipment->request_time,
			$objShipment->request_date_flexibility,
			$objShipment->pickup_address,
			$objShipment->pickup_city,
			$objShipment->pickup_zipcode,
			$objShipment->description,
			$objShipment->shipment_end_date,
			($shipmentStops->dropoff_address ?? ""),
			($shipmentStops->dropoff_zip_code ?? ""),
			($shipmentStops->dropoff_city ?? ""),
			($shipmentStops->request_dropoff_contact_person_phone_number ?? ""),
			($shipmentStops->request_dropoff_contact_person_name ?? ""),
			$objShipmentOffer->price,
			$objShipmentOffer->extra_time_price,
			$objShipmentOffer->description,
			$objShipmentOffer->payment_condition,
			$objShipmentOffer->status,
			(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			$TruckCompanyInfo->company_name,
			$TruckCompanyInfo->contact_person_name,
			$TruckCompanyInfo->contact_person_email,
			$TruckCompanyInfo->contact_person_phone_number,
		);

        $emailActions 	= 	EmailAction::where('action','=','support_mail_for_receiving_offer')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','support_mail_for_receiving_offer')->get(array('name','subject','action','body', 'mail_enable'))-> toArray();
        $cons 			= 	explode(',',$emailActions[0]['options']);
        $constants 		= 	array();
        foreach($cons as $key => $val){
            $constants[] = '{'.$val.'}';
        }
        $subject 		= 	$emailTemplates[0]['subject'];
        $messageBody 	= 	str_replace($constants, $rep_Array, $emailTemplates[0]['body']);

		$requestData = [
            "email" => $user->email,
            "name" => $user->name,
            "subject" => $subject,
            "messageBody" => $messageBody,
        ];

		if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
			SendMail::dispatch($requestData)->onQueue('send_mail');
		}
	}


	function chosen_offer($objShipment,$objShipmentOffer)
	{
		
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
		
		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}

		$notificationActions 	= 	NotificationAction::where('action','=',"chosen_offer")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"chosen_offer")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','chosen_offer')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','chosen_offer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];

		
		$shipmentStops =  ShipmentStop::where("shipment_id",$objShipment->id)->first();
		$map_id = 0;
		$truckCompany = User::where("id",$objShipmentOffer->truck_company_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);

			if($description->NotificationAction['system_notification_enable'] == 1){ 
				$notificationObj = new Notification();
				$notificationObj->user_id				= $objShipmentOffer->truck_company_id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "chosen_offer";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id = $notificationObj->id;
				}
		    }
			if($truckCompany['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){ 
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "chosen_offer";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}
				
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
			     	SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');
				}

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompany['email'],
					"name" => $truckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}
	
		}
            
		if($description->NotificationAction['system_notification_enable'] == 1){ 
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options,$user)->onQueue('push_notifications');
			}
	    }
	}
	function shipment_approved_by_company($objShipment,$objShipmentOffer)
	{
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
		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}

		$notificationActions 	= 	NotificationAction::where('action','=',"shipment_approved_by_company")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"shipment_approved_by_company")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','shipment_approved_by_company')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','shipment_approved_by_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];

		
		$shipmentStops =  ShipmentStop::where("shipment_id",$objShipment->id)->first();
		$map_id = 0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);

            if($description->NotificationAction['system_notification_enable'] == 1){ 
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "shipment_approved_by_company";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }

	        if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
				  //send whatsapp message
				  $whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				  SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');				
	            }

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}

			}
		}
		
	}
	function shipment_rejected_by_company($objShipment,$objShipmentOffer)
	{
		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}

		$notificationActions 	= 	NotificationAction::where('action','=',"shipment_rejected_by_company")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"shipment_rejected_by_company")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','shipment_rejected_by_company')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','shipment_rejected_by_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];

		
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();
		$map_id			=  0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "shipment_rejected_by_company";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }

	        if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');				
	            }

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
		    }
		}
		
	}

	function shipment_rejected_by_user($objShipment,$objShipmentOffer)
	{
		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}

		$notificationActions 	= 	NotificationAction::where('action','=',"shipment_rejected_by_user")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"shipment_rejected_by_user")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','shipment_rejected_by_user')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','shipment_rejected_by_user')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];

		
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();
		$map_id			=  0;
		$truckCompany   = User::where('id', $objShipmentOffer->truck_company_id)->first(); 
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckCompany->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "shipment_rejected_by_user";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }

	        if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');				
	            }

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompany->email,
					"name" => $truckCompany->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];

				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
		    }
		}
		
	}

	function shipment_schedule_by_company($objShipment,$objShipmentOffer,$objShipmentDriverSchedule)
	{
		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();

		$notificationActions 	= 	NotificationAction::where('action','=',"shipment_schedule_by_company_to_driver")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"shipment_schedule_by_company_to_driver")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','shipment_schedule_by_company_to_driver')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','shipment_schedule_by_company_to_driver')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];

		

		$map_id			=  0;
		$truckDriver = User::where("id",$objShipmentDriverSchedule->driver_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckDriver['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "shipment_schedule_by_company_to_driver";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckDriver['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type     = "shipment_schedule_by_company_to_driver";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckDriver)->onQueue('send_green_api_message');
				}
				
				
				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckDriver['email'],
					"name" => $truckDriver['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckDriver['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}


			}
		}
            
		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckDriver['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_driver_android_sever_api_key");

				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
     	}

		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"shipment_schedule_by_company_to_customer")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"shipment_schedule_by_company_to_customer")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','shipment_schedule_by_company_to_customer')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','shipment_schedule_by_company_to_customer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	

		$map_id			=  0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "shipment_rejected_by_company";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }

			if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');				
				}

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
		    }


		}
	}
	function shipment_schedule_deleted_by_company($objShipment,$objShipmentOffer,$objShipmentDriverSchedule)
	{
		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();

		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_removed_by_the_company_to_driver")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_removed_by_the_company_to_driver")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_removed_by_the_company_to_driver')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_removed_by_the_company_to_driver')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	

		

		$map_id			=  0;
		$truckDriver = User::where("id",$objShipmentDriverSchedule->driver_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckDriver['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_removed_by_the_company_to_driver";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckDriver['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "scheduled_shipment_has_been_removed_by_the_company_to_driver";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckDriver)->onQueue('send_green_api_message');
				}

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckDriver['email'],
					"name" => $truckDriver['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckDriver['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
	        
			}
		}

            
		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckDriver['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_driver_android_sever_api_key");
				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }

		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_removed_by_the_company_to_customer")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_removed_by_the_company_to_customer")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_removed_by_the_company_to_customer')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_removed_by_the_company_to_customer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	

		

		$map_id			=  0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_removed_by_the_company_to_customer";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }

			if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');				
	            }

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
		    }
		}
	}
	function shipment_schedule_start_by_driver($objShipment,$objShipmentOffer,$objShipmentDriverSchedule)
	{
		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();

		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_started_by_the_driver_to_company")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_started_by_the_driver_to_company")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_started_by_the_driver_to_company')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_started_by_the_driver_to_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	

		$driverDetails = User::where('id', $objShipmentDriverSchedule->driver_id)->first();
		$map_id			=  0;
		$truckCompany = User::where("id",$objShipmentDriverSchedule->truck_company_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
				$driverDetails->name,
				$driverDetails->phone_number,
			);

			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckCompany['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_started_by_the_driver_to_company";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckCompany['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "scheduled_shipment_has_been_started_by_the_driver_to_company";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');
				}	
				
				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompany['email'],
					"name" => $truckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
	        
			}
		}

            
		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");

				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }

		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_started_by_the_driver_to_customer")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_started_by_the_driver_to_customer")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_started_by_the_driver_to_customer')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_started_by_the_driver_to_customer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	

		

		$map_id			=  0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_started_by_the_driver_to_customer";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }

	        if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');				
	            }

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
		    }

		}
	}
	function shipment_schedule_end_by_driver($objShipment,$objShipmentOffer,$objShipmentDriverSchedule)
	{
		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();

		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_end_by_the_driver_to_company")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_end_by_the_driver_to_company")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_end_by_the_driver_to_company')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_end_by_the_driver_to_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	
		

		$map_id			=  0;
		$truckCompany = User::where("id",$objShipmentDriverSchedule->truck_company_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckCompany['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_end_by_the_driver_to_company";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckCompany['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "scheduled_shipment_has_been_end_by_the_driver_to_company";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');
				}	
				
				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompany['email'],
					"name" => $truckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
	        
			}
		}

            
		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }


		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_end_by_the_driver_to_driver")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_end_by_the_driver_to_driver")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}


		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_end_by_the_driver_to_driver')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_end_by_the_driver_to_driver')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	
		

		$map_id			=  0;
		$truckCompanyDriver = User::where("id",$objShipmentDriverSchedule->driver_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckCompanyDriver['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_end_by_the_driver_to_driver";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckCompanyDriver['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "scheduled_shipment_has_been_end_by_the_driver_to_driver";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckCompanyDriver)->onQueue('send_green_api_message');	
				}
				
				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompanyDriver['email'],
					"name" => $truckCompanyDriver['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompanyDriver['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
	        
			}
		}


		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompanyDriver['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");

				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }



		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_end_by_the_driver_to_customer")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_end_by_the_driver_to_customer")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_end_by_the_driver_to_customer')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_end_by_the_driver_to_customer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	
		

		$map_id			=  0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_end_by_the_driver_to_customer";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }

	        if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');				
	            }

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
		    }

		}
	}
	function shipment_schedule_end_by_truck_company($objShipment,$objShipmentOffer,$objShipmentDriverSchedule)
	{
		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();

		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_end_by_the_company_to_company")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_end_by_the_company_to_company")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_end_by_the_company_to_company')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_end_by_the_company_to_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	

		

		$map_id			=  0;
		$truckCompany = User::where("id",$objShipmentDriverSchedule->truck_company_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckCompany['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_end_by_the_company_to_company";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckCompany['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "scheduled_shipment_has_been_end_by_the_company_to_company";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');
				}	
				
				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompany['email'],
					"name" => $truckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
	        
			}
		}

            
		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }


		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_end_by_the_company_to_driver")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_end_by_the_company_to_driver")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_end_by_the_company_to_driver')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_end_by_the_company_to_driver')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	

		

		$map_id			=  0;
		$truckCompanyDriver = User::where("id",$objShipmentDriverSchedule->driver_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckCompanyDriver['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_end_by_the_company_to_driver";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckCompanyDriver['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "scheduled_shipment_has_been_end_by_the_company_to_driver";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckCompanyDriver)->onQueue('send_green_api_message');	
				}	
				
				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompanyDriver['email'],
					"name" => $truckCompanyDriver['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompanyDriver['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
	        
			}
		}

            
		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompanyDriver['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");

				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }



		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"scheduled_shipment_has_been_end_by_the_company_to_customer")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"scheduled_shipment_has_been_end_by_the_company_to_customer")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','scheduled_shipment_has_been_end_by_the_company_to_customer')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','scheduled_shipment_has_been_end_by_the_company_to_customer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	
		

		$map_id			=  0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "scheduled_shipment_has_been_end_by_the_company_to_customer";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }

	        if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');
				}	
				
				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
	        }

		}
	}
	function shipment_cancelled_by_company($objShipment,$objShipmentOffer)
	{
		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}

		$notificationActions 	= 	NotificationAction::where('action','=',"shipment_cancelled_by_company")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"shipment_cancelled_by_company")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}


		$emailActions 	= 	EmailAction::where('action','=','shipment_cancelled_by_company')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','shipment_cancelled_by_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	

		
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();
		$map_id			=  0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);

			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "shipment_cancelled_by_company";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }

			if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');				
	            }

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
		    }

		}
	}

	function shipment_cancelled_by_customer($objShipment,$objShipmentOffer)
	{
		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}

		$notificationActions 	= 	NotificationAction::where('action','=',"shipment_cancelled_by_customer")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"shipment_cancelled_by_customer")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','shipment_cancelled_by_customer')->get()->toArray();
        $emailTemplates = 	EmailTemplate::where('action','=','shipment_cancelled_by_customer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
        $emailconstants 	= 	array();
        foreach($emailcons as $key => $val){
            $emailconstants[] = '{'.$val.'}';
        }
		$subject 		= 	$emailTemplates[0]['subject'];	

		
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();
		$map_id			=  0;
		$truckCompany = User::where("id",$objShipmentOffer->truck_company_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckCompany['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "shipment_cancelled_by_customer";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckCompany['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "shipment_cancelled_by_customer";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');
				}	
				
				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompany['email'],
					"name" => $truckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
	        
			}
		}
            
		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }
	}
	function push_notification_for_message($sender_id,$receiver_id,$message)
	{
		$user = User::find($receiver_id);
		$senderUser = User::find($sender_id);
		$truckCompany = User::find($receiver_id)->toArray();

		$notificationActions 	= 	NotificationAction::where('action','=',"chat")->get()->toArray();
		$notificationTemplates = 	NotificationTemplate::where('action','=',"chat")->first();

		$notificationTemplateDescriptions =	NotificationTemplateDescription::where('parent_id','=',$notificationTemplates->id)->get(array('name','subject','body','language_id'))->toArray();
		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}
		$map_id			=  0;
		
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array                  =   array(
				$senderUser->name,
				$message,
			);

			$notificationObj = new Notification();
			$notificationObj->user_id				= $truckCompany['id'];
			$notificationObj->language_id			= $description['language_id'];
			$notificationObj->title					= $description["subject"];
			$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
			$notificationObj->is_read				= 0;
			$notificationObj->shipment_id			= 0;
			$notificationObj->notification_type		= "chat";
			$notificationObj->is_notification_sent	= 0;
			$notificationObj->map_id 				= $map_id;
			$notificationObj->save();
			if($map_id == 0){
				$notificationObj->map_id 			= $notificationObj->id;
				$notificationObj->save();
				$map_id			 					= $notificationObj->id;
			}
		}

		$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$user->id)->orderBy("id","DESC")->first();
		if($user_device_tokens){
			if($user->user_role_id == 3){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
			}elseif($user->user_role_id == 4){
				$server_key				=	Config::get("Site.truck_driver_android_sever_api_key");
			}
			$title = $senderUser->name;
			$notification_type = '';
			$map_id = 0;
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>0,"service_number"=>0);
			$adwance_options = array(
				'type' 				=> 'chat',
				'active_id'			=> $senderUser->id,
				'reciver_image'		=> $senderUser->image,
				'name'				=> $senderUser->name,
			);

			SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
		}
	}

	function truckCompanySendSubscriptionNotification($getId, $Information, $truckCompany)
	{
		$notificationActions 	= 	NotificationAction::where('action','=',"company_subscription_plan")
				->get()
				->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"company_subscription_plan")->first();

		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

			$cons 			= 	explode(',',$notificationActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}

			$emailActions 	= 	EmailAction::where('action','=','company_subscription_plan')->get()->toArray();
			$emailTemplates = 	EmailTemplate::where('action','=','company_subscription_plan')->get(array('name','subject','action','body','mail_enable'))->toArray();
			$emailcons 			= 	explode(',',$emailActions[0]['options']);
			$emailconstants 	= 	array();
			foreach($emailcons as $key => $val){
				$emailconstants[] = '{'.$val.'}';
			}
			$subject 		= 	$emailTemplates[0]['subject'];

			$map_id = 0;
		
			$rep_Array 		   = array(
				$Information['name'],
				$Information['email'],
				$Information['phone_number'],
				$Information['price'],
				($Information['discount']>0 ? " ".trans("messages.discount")." : ".$Information['discount']."," : ''),
				$Information['total_price'],
				$Information['type'],
				$Information['column_type'],
				$Information['paymentUrl'],
			);

			foreach($notificationTemplateDescriptions as $key => $description){
				
				if($description->NotificationAction['system_notification_enable'] == 1){
					$notificationObj                        = new Notification();
					$notificationObj->user_id				= $getId;
					$notificationObj->language_id			= $description['language_id'];
					$notificationObj->title					= $description["subject"];
					$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
					$notificationObj->is_read				= 0;
					$notificationObj->notification_type		= "company_subscription_plan";
					$notificationObj->is_notification_sent	= 0;
					$notificationObj->map_id 				= $map_id;
					$notificationObj->url 					= $Information['paymentUrl'];
					$notificationObj->save();
					if($map_id == 0){
						$notificationObj->map_id 			= $notificationObj->id;
						$notificationObj->save();
						$map_id = $notificationObj->id;
					}
			    }
				if($truckCompany['language'] == $description['language_id'] ){
					if($description->NotificationAction['system_notification_enable'] == 1){
						$selectedNotification    = $description;
						$message                 = $notificationObj->description;
						$notification_type       = "company_subscription_plan";
						$title                   = $description['subject'];
						$service_request_id      = 0;
						$service_number          = $notificationObj->id;
					}

					if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
						//send whatsapp message
						$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				        SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');
					}

					$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

					$requestData = [
						"email" => $truckCompany['email'],
						"name" => $truckCompany['name'],
						"subject" => $subject,
						"messageBody" => $messageBody,
					];
			
					if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
						SendMail::dispatch($requestData)->onQueue('send_mail');
					}
				}

			}

			if($description->NotificationAction['system_notification_enable'] == 1){
				$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number,"url"=>$rep_Array[8]);

				$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
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

	function company_subscription_plan_expire_date($getId, $Information, $truckCompany)
	{
		
			$notificationActions 	= 	NotificationAction::where('action','=',"company_subscription_plan_expire_date")
					->get()
					->toArray();
		
			$notificationTemplates = 	NotificationTemplate::where('action','=',"company_subscription_plan_expire_date")->first();
		
			$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();
		
				$cons 			= 	explode(',',$notificationActions[0]['options']);
				$constants 		= 	array();
				foreach($cons as $key => $val){
					$constants[] = '{'.$val.'}';
				}

				$emailActions 	= 	EmailAction::where('action','=','company_subscription_plan_expire_date')->get()->toArray();
				$emailTemplates = 	EmailTemplate::where('action','=','company_subscription_plan_expire_date')->get(array('name','subject','action','body','mail_enable'))->toArray();
				$emailcons 			= 	explode(',',$emailActions[0]['options']);
				$emailconstants 	= 	array();
				foreach($emailcons as $key => $val){
					$emailconstants[] = '{'.$val.'}';
				}
				$subject 		= 	$emailTemplates[0]['subject'];
				
				$map_id = 0;
				
				$rep_Array 		   = array(
					$Information['name'],
					$Information['email'],
					$Information['phone_number'],
					$Information['price'],
					($Information['discount']>0 ? trans("messages.discount")." : ".$Information['discount'] : ''),
					$Information['total_price'],
					$Information['type'],
					$Information['column_type'],
					$Information['expireDate'],
					$Information['paymentUrl'],
				);
				foreach($notificationTemplateDescriptions as $key => $description){
					
					if($description->NotificationAction['system_notification_enable'] == 1){
						$notificationObj                        = new Notification();
						$notificationObj->user_id				= $getId;
						$notificationObj->language_id			= $description['language_id'];
						$notificationObj->title					= $description["subject"];
						$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
						$notificationObj->is_read				= 0;
						$notificationObj->notification_type		= "company_subscription_plan_expire_date";
						$notificationObj->is_notification_sent	= 0;
						$notificationObj->url 					= $Information['paymentUrl'];
						$notificationObj->map_id 				= $map_id;
						$notificationObj->save();
						if($map_id == 0){
							$notificationObj->map_id 			= $notificationObj->id;
							$notificationObj->save();
							$map_id = $notificationObj->id;
						}
				    }
					if($truckCompany['language'] == $description['language_id'] ){
						if($description->NotificationAction['system_notification_enable'] == 1){
							$selectedNotification    = $description;
							$message                 = $notificationObj->description;
							$notification_type       = "company_subscription_plan_expire_date";
							$title                   = $description['subject'];
							$service_request_id      = 0;
							$service_number          = $notificationObj->id;
						}

						if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
							//send whatsapp message
							$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				            SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');
						}

						$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

						$requestData = [
							"email" => $truckCompany['email'],
							"name" => $truckCompany['name'],
							"subject" => $subject,
							"messageBody" => $messageBody,
						];
				
						if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
							SendMail::dispatch($requestData)->onQueue('send_mail');
						}
					}
		
				}

				if($description->NotificationAction['system_notification_enable'] == 1){
					$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number,"url"=>$rep_Array[9]);
			
					$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
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


	function sendTruckNotificationInsuranceExpiryBefore30Days($Information, $companyId, $TruckCompany)
	{
		
					
		$notificationActions 	= 	NotificationAction::where('action','=',"truck_insurance_notification_before_30_days")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"truck_insurance_notification_before_30_days")->first();
	
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();
	
			$cons 			= 	explode(',',$notificationActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			
			$map_id = 0;
			
			$rep_Array 		   = array(
				$Information['truckCompanyName'],
				$Information['truckSystemNumber'],
				$Information['companyRefueling'],
				$Information['truckType'],
				$Information['companyTidulak'],
				$Information['expiryInsuranceDate'],
			);
			
			foreach($notificationTemplateDescriptions as $key => $description){
				
				if($description->NotificationAction['system_notification_enable'] == 1){
					$notificationObj                        = new Notification();
					$notificationObj->user_id				= $companyId;
					$notificationObj->language_id			= $description['language_id'];
					$notificationObj->title					= $description["subject"];
					$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
					$notificationObj->is_read				= 0;
					$notificationObj->notification_type		= "truck_insurance_notification_before_30_days";
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
					if($description->NotificationAction['system_notification_enable'] == 1){
						$selectedNotification    = $description;
						$message                 = $notificationObj->description;
						$notification_type       = "truck_insurance_notification_before_30_days";
						$title                   = $description['subject'];
						$service_request_id      = 0;
						$service_number          = $notificationObj->id;
					}

					if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
						//send whatsapp message
						$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				        SendGreenApiMessage::dispatch($whatsappDescription,$TruckCompany)->onQueue('send_green_api_message');
					}			
				}
	
			}

			if($description->NotificationAction['system_notification_enable'] == 1){
				$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);
		
				$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$TruckCompany['id'])->orderBy("id","DESC")->first();
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

	function sendTruckNotificationInsuranceAfterExpired($Information, $companyId, $TruckCompany)
	{		
					
		$notificationActions 	= 	NotificationAction::where('action','=',"truck_insurance_notification_after_expired")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"truck_insurance_notification_after_expired")->first();
	
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();
	
			$cons 			= 	explode(',',$notificationActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			
			$map_id = 0;
			
			$rep_Array 		   = array(
				$Information['truckCompanyName'],
				$Information['truckSystemNumber'],
				$Information['companyRefueling'],
				$Information['truckType'],
				$Information['companyTidulak'],
				$Information['expiryInsuranceDate'],
			);
			
			foreach($notificationTemplateDescriptions as $key => $description){
				
				if($description->NotificationAction['system_notification_enable'] == 1){
					$notificationObj                        = new Notification();
					$notificationObj->user_id				= $companyId;
					$notificationObj->language_id			= $description['language_id'];
					$notificationObj->title					= $description["subject"];
					$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
					$notificationObj->is_read				= 0;
					$notificationObj->notification_type		= "truck_insurance_notification_after_expired";
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
					if($description->NotificationAction['system_notification_enable'] == 1){
						$selectedNotification    = $description;
						$message                 = $notificationObj->description;
						$notification_type       = "truck_insurance_notification_after_expired";
						$title                   = $description['subject'];
						$service_request_id      = 0;
						$service_number          = $notificationObj->id;
					}

					if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
						//send whatsapp message
						$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				        SendGreenApiMessage::dispatch($whatsappDescription,$TruckCompany)->onQueue('send_green_api_message');	
					}		
				}
	
			}

			if($description->NotificationAction['system_notification_enable'] == 1){
				$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);
		
				$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$TruckCompany['id'])->orderBy("id","DESC")->first();
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

	

	function sendTruckNotificationLicenceExpiryBefore30Days($Information, $companyId, $Truck)
	{
				
		$notificationActions 	= 	NotificationAction::where('action','=',"truck_licence_notification_before_30_days")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"truck_licence_notification_before_30_days")->first();
	
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();
	
			$cons 			= 	explode(',',$notificationActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			
			$map_id = 0;
			
			$rep_Array 		   = array(
				$Information['truckCompanyName'],
				$Information['truckSystemNumber'],
				$Information['companyRefueling'],
				$Information['truckType'],
				$Information['companyTidulak'],
				$Information['expiryLicenceDate'],
			);
			
			foreach($notificationTemplateDescriptions as $key => $description){
				
				if($description->NotificationAction['system_notification_enable'] == 1){
					$notificationObj                        = new Notification();
					$notificationObj->user_id				= $companyId;
					$notificationObj->language_id			= $description['language_id'];
					$notificationObj->title					= $description["subject"];
					$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
					$notificationObj->is_read				= 0;
					$notificationObj->notification_type		= "truck_licence_notification_before_30_days";
					$notificationObj->is_notification_sent	= 0;
					$notificationObj->map_id 				= $map_id;
					$notificationObj->save();
					if($map_id == 0){
						$notificationObj->map_id 			= $notificationObj->id;
						$notificationObj->save();
						$map_id = $notificationObj->id;
					}
			    }
				if($Truck['language'] == $description['language_id'] ){
					if($description->NotificationAction['system_notification_enable'] == 1){
						$selectedNotification    = $description;
						$message                 = $notificationObj->description;
						$notification_type       = "truck_licence_notification_before_30_days";
						$title                   = $description['subject'];
						$service_request_id      = 0;
						$service_number          = $notificationObj->id;
					}

					if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
						//send whatsapp message
						$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				        SendGreenApiMessage::dispatch($whatsappDescription,$Truck)->onQueue('send_green_api_message');
					}
				}
	
			}

			if($description->NotificationAction['system_notification_enable'] == 1){
				$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);
		
				$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$Truck['id'])->orderBy("id","DESC")->first();
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

	function sendTruckNotificationLicenceAfterExpired($Information, $companyId, $Truck)
	{
				
		$notificationActions 	= 	NotificationAction::where('action','=',"truck_licence_notification_after_expired")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"truck_licence_notification_after_expired")->first();
	
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();
	
			$cons 			= 	explode(',',$notificationActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}
			
			$map_id = 0;
			
			$rep_Array 		   = array(
				$Information['truckCompanyName'],
				$Information['truckSystemNumber'],
				$Information['companyRefueling'],
				$Information['truckType'],
				$Information['companyTidulak'],
				$Information['expiryLicenceDate'],
			);
			
			foreach($notificationTemplateDescriptions as $key => $description){
				
				if($description->NotificationAction['system_notification_enable'] == 1){
					$notificationObj                        = new Notification();
					$notificationObj->user_id				= $companyId;
					$notificationObj->language_id			= $description['language_id'];
					$notificationObj->title					= $description["subject"];
					$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
					$notificationObj->is_read				= 0;
					$notificationObj->notification_type		= "truck_licence_notification_after_expired";
					$notificationObj->is_notification_sent	= 0;
					$notificationObj->map_id 				= $map_id;
					$notificationObj->save();
					if($map_id == 0){
						$notificationObj->map_id 			= $notificationObj->id;
						$notificationObj->save();
						$map_id = $notificationObj->id;
					}
			    }
				if($Truck['language'] == $description['language_id'] ){
					if($description->NotificationAction['system_notification_enable'] == 1){
						$selectedNotification    = $description;
						$message                 = $notificationObj->description;
						$notification_type       = "truck_licence_notification_after_expired";
						$title                   = $description['subject'];
						$service_request_id      = 0;
						$service_number          = $notificationObj->id;
					}

					if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
						//send whatsapp message
						$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				        SendGreenApiMessage::dispatch($whatsappDescription,$Truck)->onQueue('send_green_api_message');
					}
				}
	
			}

			if($description->NotificationAction['system_notification_enable'] == 1){
				$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);
		
				$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$Truck['id'])->orderBy("id","DESC")->first();
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

	function send_notification_one_day_before_shipment_starts($objShipment,$objShipmentOffer,$objShipmentDriverSchedule)
	{


		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();

		$notificationActions 	= 	NotificationAction::where('action','=',"send_notification_one_day_before_shipment_starts_to_company")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"send_notification_one_day_before_shipment_starts_to_company")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','send_notification_one_day_before_shipment_starts_to_company')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','send_notification_one_day_before_shipment_starts_to_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];

		

		$map_id			=  0;
		$truckCompany = User::where("id",$objShipmentOffer->truck_company_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckCompany['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "send_notification_one_day_before_shipment_starts_to_company";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckCompany['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "send_notification_one_day_before_shipment_starts_to_company";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');
				}	

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompany['email'],
					"name" => $truckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}
		}

		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }


		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"send_notification_one_day_before_shipment_starts_to_driver")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"send_notification_one_day_before_shipment_starts_to_driver")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','send_notification_one_day_before_shipment_starts_to_driver')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','send_notification_one_day_before_shipment_starts_to_driver')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];
		

		$map_id			=  0;
		if($objShipmentDriverSchedule){
			$truckCompanyDriver = User::where("id",$objShipmentDriverSchedule->driver_id)->first()->toArray();
			foreach($notificationTemplateDescriptions as $key => $description){
				$rep_Array 		= 	array(
					$userCompanyInformation['contact_person_name'],
					$userCompanyInformation['contact_person_email'],
					$userCompanyInformation['contact_person_phone_number'],
					$objShipment->request_number,
					$objShipment->status,
					(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
					$objShipment->request_date,
					$objShipment->request_time,
					$objShipment->request_date_flexibility,
					$objShipment->pickup_address,
					$objShipment->pickup_city,
					$objShipment->pickup_zipcode,
					$objShipment->description,
					$objShipment->shipment_end_date,
					($shipmentStops->dropoff_address ?? ""),
					($shipmentStops->dropoff_zip_code ?? ""),
					($shipmentStops->dropoff_city ?? ""),
					$objShipmentOffer->price,
					$objShipmentOffer->extra_time_price,
					$objShipmentOffer->description,
					$objShipmentOffer->payment_condition,
					$objShipmentOffer->status,
					(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
				);


				if($description->NotificationAction['system_notification_enable'] == 1){
					$notificationObj = new Notification();
					$notificationObj->user_id				= $truckCompanyDriver['id'];
					$notificationObj->language_id			= $description['language_id'];
					$notificationObj->title					= $description["subject"];
					$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
					$notificationObj->is_read				= 0;
					$notificationObj->shipment_id			= $objShipment->id;
					$notificationObj->notification_type		= "send_notification_one_day_before_shipment_starts_to_driver";
					$notificationObj->is_notification_sent	= 0;
					$notificationObj->map_id 				= $map_id;
					$notificationObj->save();
					if($map_id == 0){
						$notificationObj->map_id 			= $notificationObj->id;
						$notificationObj->save();
						$map_id			 					= $notificationObj->id;
					}
			    }
				if($truckCompanyDriver['language'] == $description['language_id'] ){
					if($description->NotificationAction['system_notification_enable'] == 1){
						$selectedNotification   = $description;
						$message               = $notificationObj->description;
						$notification_type      = "send_notification_one_day_before_shipment_starts_to_driver";
						$title                 = $description['subject'];
						$service_request_id    = $objShipment->id;
						$service_number        = $notificationObj->id;
					}

					if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
						//send whatsapp message
						$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				        SendGreenApiMessage::dispatch($whatsappDescription,$truckCompanyDriver)->onQueue('send_green_api_message');	
					}

					$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

					$requestData = [
						"email" => $truckCompanyDriver['email'],
						"name" => $truckCompanyDriver['name'],
						"subject" => $subject,
						"messageBody" => $messageBody,
					];
			
					if($emailTemplates[0]['mail_enable'] == 1 && $truckCompanyDriver['email'] != null){
						SendMail::dispatch($requestData)->onQueue('send_mail');
					}
				}
			}

				
			if($description->NotificationAction['system_notification_enable'] == 1){
				$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

				$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompanyDriver['id'])->orderBy("id","DESC")->first();
				if($user_device_tokens){
					$server_key				=	Config::get("Site.truck_company_android_sever_api_key");

					$adwance_options = array(
						'type' 				=> 'shipment',
						'map_id'			=> $map_id,
						'shipments_status'	=> $objShipment->status,
						'request_number'	=> $objShipment->request_number,
					);
					SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
				}
				}
	        }

		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"send_notification_one_day_before_shipment_starts_to_customer")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"send_notification_one_day_before_shipment_starts_to_customer")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','send_notification_one_day_before_shipment_starts_to_customer')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','send_notification_one_day_before_shipment_starts_to_customer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];
		

		$map_id			=  0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "send_notification_one_day_before_shipment_starts_to_customer";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');	
			    }

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
		    }
		}
	}

	function send_notification_one_hour_before_shipment_starts($objShipment,$objShipmentOffer,$objShipmentDriverSchedule)
	{


		$user = User::find($objShipment->customer_id);
            
		$userCompanyInformation = array(
			'contact_person_name'           => $user->name,
			'contact_person_email'          => $user->email,
			'contact_person_phone_number'   => $user->phone_number,
		);

		if($objShipment->request_type){
			$shipmentType = $objShipment->request_type;
		}else{
			$shipmentType = $objShipment->shipment_type;
		}
		$shipmentStops 	=  ShipmentStop::where("shipment_id",$objShipment->id)->first();

		$notificationActions 	= 	NotificationAction::where('action','=',"send_notification_one_hour_before_shipment_starts_to_company")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"send_notification_one_hour_before_shipment_starts_to_company")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','send_notification_one_hour_before_shipment_starts_to_company')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','send_notification_one_hour_before_shipment_starts_to_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];
		

		$map_id			=  0;
		$truckCompany = User::where("id",$objShipmentOffer->truck_company_id)->first()->toArray();
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $truckCompany['id'];
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "send_notification_one_hour_before_shipment_starts_to_company";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($truckCompany['language'] == $description['language_id'] ){
				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "send_notification_one_hour_before_shipment_starts_to_company";
					$title                 = $description['subject'];
					$service_request_id    = $objShipment->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');	
				}

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $truckCompany['email'],
					"name" => $truckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			
			}
		}

            
		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $objShipment->status,
					'request_number'	=> $objShipment->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }


		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"send_notification_one_hour_before_shipment_starts_to_driver")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"send_notification_one_hour_before_shipment_starts_to_driver")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','send_notification_one_hour_before_shipment_starts_to_driver')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','send_notification_one_hour_before_shipment_starts_to_driver')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];

		

		$map_id			=  0;
		if($objShipmentDriverSchedule){
			$truckCompanyDriver = User::where("id",$objShipmentDriverSchedule->driver_id)->first()->toArray();
			foreach($notificationTemplateDescriptions as $key => $description){
				$rep_Array 		= 	array(
					$userCompanyInformation['contact_person_name'],
					$userCompanyInformation['contact_person_email'],
					$userCompanyInformation['contact_person_phone_number'],
					$objShipment->request_number,
					$objShipment->status,
					(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
					$objShipment->request_date,
					$objShipment->request_time,
					$objShipment->request_date_flexibility,
					$objShipment->pickup_address,
					$objShipment->pickup_city,
					$objShipment->pickup_zipcode,
					$objShipment->description,
					$objShipment->shipment_end_date,
					($shipmentStops->dropoff_address ?? ""),
					($shipmentStops->dropoff_zip_code ?? ""),
					($shipmentStops->dropoff_city ?? ""),
					$objShipmentOffer->price,
					$objShipmentOffer->extra_time_price,
					$objShipmentOffer->description,
					$objShipmentOffer->payment_condition,
					$objShipmentOffer->status,
					(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
				);


				if($description->NotificationAction['system_notification_enable'] == 1){
					$notificationObj = new Notification();
					$notificationObj->user_id				= $truckCompanyDriver['id'];
					$notificationObj->language_id			= $description['language_id'];
					$notificationObj->title					= $description["subject"];
					$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
					$notificationObj->is_read				= 0;
					$notificationObj->shipment_id			= $objShipment->id;
					$notificationObj->notification_type		= "send_notification_one_hour_before_shipment_starts_to_driver";
					$notificationObj->is_notification_sent	= 0;
					$notificationObj->map_id 				= $map_id;
					$notificationObj->save();
					if($map_id == 0){
						$notificationObj->map_id 			= $notificationObj->id;
						$notificationObj->save();
						$map_id			 					= $notificationObj->id;
					}
			    }
				if($truckCompanyDriver['language'] == $description['language_id'] ){
					if($description->NotificationAction['system_notification_enable'] == 1){
						$selectedNotification   = $description;
						$message               = $notificationObj->description;
						$notification_type      = "send_notification_one_hour_before_shipment_starts_to_driver";
						$title                 = $description['subject'];
						$service_request_id    = $objShipment->id;
						$service_number        = $notificationObj->id;
					}

					if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
						//send whatsapp message
						$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				        SendGreenApiMessage::dispatch($whatsappDescription,$truckCompanyDriver)->onQueue('send_green_api_message');	
					}

					$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

					$requestData = [
						"email" => $truckCompanyDriver['email'],
						"name" => $truckCompanyDriver['name'],
						"subject" => $subject,
						"messageBody" => $messageBody,
					];
			
					if($emailTemplates[0]['mail_enable'] == 1 && $truckCompanyDriver['email'] != null){
						SendMail::dispatch($requestData)->onQueue('send_mail');
					}
			
				}
			}

				
			if($description->NotificationAction['system_notification_enable'] == 1){
				$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

				$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompanyDriver['id'])->orderBy("id","DESC")->first();
				if($user_device_tokens){
					$server_key				=	Config::get("Site.truck_company_android_sever_api_key");

					$adwance_options = array(
						'type' 				=> 'shipment',
						'map_id'			=> $map_id,
						'shipments_status'	=> $objShipment->status,
						'request_number'	=> $objShipment->request_number,
					);
					SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
				}
		    }
		}

		/////////////////////////////
		$notificationActions 	= 	NotificationAction::where('action','=',"send_notification_one_hour_before_shipment_starts_to_customer")
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"send_notification_one_hour_before_shipment_starts_to_customer")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','send_notification_one_hour_before_shipment_starts_to_customer')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','send_notification_one_hour_before_shipment_starts_to_customer')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];

		

		$map_id			=  0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name ?? ""),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);


			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= "send_notification_one_hour_before_shipment_starts_to_customer";
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id			 					= $notificationObj->id;
				}
		    }
			if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');	
				}

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);

				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}
		}
	}

	public function shipmentReviewAfterScheduleEnd($list, $userCompanyInformation, $shipmentType){

		$notificationActions    =   NotificationAction::where('action','=',"shipment_review_after_schedule_end")
			->get()
			->toArray();

		$notificationTemplates =    NotificationTemplate::where('action','=',"shipment_review_after_schedule_end")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons           =   explode(',',$notificationActions[0]['options']);
		$constants      =   array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','shipment_review_after_schedule_end')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','shipment_review_after_schedule_end')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];
		
		$shipmentStops =  ShipmentStop::where("shipment_id",$list->id)->first();
		$map_id = 0;
		
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array      =   array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$list->request_number,
				$list->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name),
				$list->request_date,
				$list->request_time,
				$list->request_date_flexibility,
				$list->pickup_address,
				$list->pickup_city,
				$list->pickup_zipcode,
				$list->description,
				date(config("Reading.date_time_format"),strtotime($list->shipment_actual_end_time)),
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$list->price,
				$list->extra_time_price,
				$list->offers_description,
				$list->payment_condition,
				$list->offers_status,
				(Truck::find($list->offers_truck_id)->truck_system_number ?? ''),
			);

			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id               = $userCompanyInformation['user_id'];
				$notificationObj->language_id           = $description['language_id'];
				$notificationObj->title                 = $description["subject"];
				$notificationObj->description           = str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read               = 0;
				$notificationObj->shipment_id           = $list->id;
				$notificationObj->notification_type     = "shipment_review_after_schedule_end";
				$notificationObj->is_notification_sent  = 0;
				$notificationObj->map_id                = $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id            = $notificationObj->id;
					$notificationObj->save();
					$map_id = $notificationObj->id;
				}
	    	}
			if($list['language'] == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$list)->onQueue('send_green_api_message');
				}

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);
				
				$requestData = [
					"email" => $list['email'],
					"name" => $list['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $list['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}
		}

		

	}

	public function shipmentReviewAfterScheduleEndToTruckCompany($list, $userCompanyInformation, $shipmentType){

		$notificationActions    =   NotificationAction::where('action','=',"shipment_review_after_schedule_end_to_truck_company")
			->get()
			->toArray();

		$notificationTemplates =    NotificationTemplate::where('action','=',"shipment_review_after_schedule_end_to_truck_company")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons           =   explode(',',$notificationActions[0]['options']);
		$constants      =   array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','shipment_review_after_schedule_end_to_truck_company')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','shipment_review_after_schedule_end_to_truck_company')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];
		
		$shipmentStops =  ShipmentStop::where("shipment_id",$list->id)->first();
		$map_id = 0;
		$truckCompany = User::where("id",$list['truck_company_id'])->first()->toArray();
		
		
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array      =   array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$list->request_number,
				$list->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name),
				$list->request_date,
				$list->request_time,
				$list->request_date_flexibility,
				$list->pickup_address,
				$list->pickup_city,
				$list->pickup_zipcode,
				$list->description,
				date(config("Reading.date_time_format"),strtotime($list->shipment_actual_end_time)),
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				$list->price,
				$list->extra_time_price,
				$list->offers_description,
				$list->payment_condition,
				$list->offers_status,
				(Truck::find($list->offers_truck_id)->truck_system_number ?? ''),
			);

			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id               = $truckCompany['id'];
				$notificationObj->language_id           = $description['language_id'];
				$notificationObj->title                 = $description["subject"];
				$notificationObj->description           = str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read               = 0;
				$notificationObj->shipment_id           = $list->id;
				$notificationObj->notification_type     = "shipment_review_after_schedule_end_to_truck_company";
				$notificationObj->is_notification_sent  = 0;
				$notificationObj->map_id                = $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id            = $notificationObj->id;
					$notificationObj->save();
					$map_id = $notificationObj->id;
				}
	    	}
			if($truckCompany['language'] == $description['language_id']){

				if($description->NotificationAction['system_notification_enable'] == 1){
					$selectedNotification   = $description;
					$message               = $notificationObj->description;
					$notification_type      = "send_notification_one_hour_before_shipment_starts_to_driver";
					$title                 = $description['subject'];
					$service_request_id    = $list->id;
					$service_number        = $notificationObj->id;
				}

				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');
				}

				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);
				
				$requestData = [
					"email" => $truckCompany['email'],
					"name" => $truckCompany['name'],
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}
		}

		     
		if($description->NotificationAction['system_notification_enable'] == 1){
			$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

			$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$userCompanyInformation['user_id'])->orderBy("id","DESC")->first();
			if($user_device_tokens){
				$server_key				=	Config::get("Site.truck_company_android_sever_api_key");
				$adwance_options = array(
					'type' 				=> 'shipment',
					'map_id'			=> $map_id,
					'shipments_status'	=> $list->status,
					'request_number'	=> $list->request_number,
				);
				SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options)->onQueue('push_notifications');
			}
	    }

		

	}

	function sendTruckCompanySubscriptionPlanExpiryDateExtend($getId, $Information, $truckCompany)
	{

		$notificationActions 	= 	NotificationAction::where('action','=',"company_plan_expire_date_extend")
				->get()
				->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',"company_plan_expire_date_extend")->first();

		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

			$cons 			= 	explode(',',$notificationActions[0]['options']);
			$constants 		= 	array();
			foreach($cons as $key => $val){
				$constants[] = '{'.$val.'}';
			}

			$emailActions 	= 	EmailAction::where('action','=','company_plan_expire_date_extend')->get()->toArray();
			$emailTemplates = 	EmailTemplate::where('action','=','company_plan_expire_date_extend')->get(array('name','subject','action','body','mail_enable'))->toArray();
			$emailcons 			= 	explode(',',$emailActions[0]['options']);
			$emailconstants 	= 	array();
			foreach($emailcons as $key => $val){
				$emailconstants[] = '{'.$val.'}';
			}
			$subject 		= 	$emailTemplates[0]['subject'];

			$map_id = 0;
		
			$rep_Array 		   = array(
				$Information['name'],
				$Information['email'],
				$Information['phone_number'],
				$Information['price'],
				($Information['discount']>0 ? " ".trans("messages.discount")." : ".$Information['discount']."," : ''),
				$Information['total_price'],
				$Information['type'],
				$Information['expiry_date'],
			);


			foreach($notificationTemplateDescriptions as $key => $description){
				
				if($description->NotificationAction['system_notification_enable'] == 1){
					$notificationObj                        = new Notification();
					$notificationObj->user_id				= $getId;
					$notificationObj->language_id			= $description['language_id'];
					$notificationObj->title					= $description["subject"];
					$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
					$notificationObj->is_read				= 0;
					$notificationObj->notification_type		= "company_plan_expire_date_extend";
					$notificationObj->is_notification_sent	= 0;
					$notificationObj->map_id 				= $map_id;
					$notificationObj->url 					= '';
					$notificationObj->save();
					if($map_id == 0){
						$notificationObj->map_id 			= $notificationObj->id;
						$notificationObj->save();
						$map_id = $notificationObj->id;
					}
			    }
				if($truckCompany['language'] == $description['language_id'] ){
					if($description->NotificationAction['system_notification_enable'] == 1){
						$selectedNotification    = $description;
						$message                 = $notificationObj->description;
						$notification_type       = "company_plan_expire_date_extend";
						$title                   = $description['subject'];
						$service_request_id      = 0;
						$service_number          = $notificationObj->id;
					}

					if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
						//send whatsapp message
						$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				        SendGreenApiMessage::dispatch($whatsappDescription,$truckCompany)->onQueue('send_green_api_message');	
					}

					$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);
				
					$requestData = [
						"email" => $truckCompany['email'],
						"name" => $truckCompany['name'],
						"subject" => $subject,
						"messageBody" => $messageBody,
					];
			
					if($emailTemplates[0]['mail_enable'] == 1 && $truckCompany['email'] != null){
						SendMail::dispatch($requestData)->onQueue('send_mail');
					}
				}

			}

			if($description->NotificationAction['system_notification_enable'] == 1){
				$data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number,"url"=>$rep_Array[7]);

				$user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
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
	function send_invoice_to_customer_by_truck_company($objShipment,$objShipmentOffer)
	{
		$user = User::find($objShipment->customer_id);
		if($user->customer_type == "business"){
			$userCompanyInformation = UserCompanyInformation::where("user_id",$user->id)->first()->toArray();
			$shipmentType = $objShipment->shipment_type;
			$actionType = "send_invoice_to_customer_by_truck_company"; 
		}else{
			$userCompanyInformation = array(
				'contact_person_name'           => $user->name,
				'contact_person_email'          => $user->email,
				'contact_person_phone_number'   => $user->phone_number,
			);
			$shipmentType = $objShipment->request_type;
			$actionType = "send_invoice_to_customer_by_truck_company"; 
		}
		$notificationActions 	= 	NotificationAction::where('action','=',$actionType)
			->get()
			->toArray();

		$notificationTemplates = 	NotificationTemplate::where('action','=',$actionType)
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons 			= 	explode(',',$notificationActions[0]['options']);
		$constants 		= 	array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=',$actionType)->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=',$actionType)->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];

		
		$shipmentStops =  ShipmentStop::where("shipment_id",$objShipment->id)->first();
		$map_id = 0;
		foreach($notificationTemplateDescriptions as $key => $description){
			$rep_Array 		= 	array(
				$userCompanyInformation['contact_person_name'],
				$userCompanyInformation['contact_person_email'],
				$userCompanyInformation['contact_person_phone_number'],
				$objShipment->request_number,
				$objShipment->status,
				(TruckTypeDescription::where(["parent_id"=>$shipmentType,"language_id"=>$description['language_id']])->first()->name),
				$objShipment->request_date,
				$objShipment->request_time,
				$objShipment->request_date_flexibility,
				$objShipment->pickup_address,
				$objShipment->pickup_city,
				$objShipment->pickup_zipcode,
				$objShipment->description,
				$objShipment->shipment_end_date,
				($shipmentStops->dropoff_address ?? ""),
				($shipmentStops->dropoff_zip_code ?? ""),
				($shipmentStops->dropoff_city ?? ""),
				($shipmentStops->request_dropoff_contact_person_name ?? ""),
				($shipmentStops->request_dropoff_contact_person_name ?? ""),
				$objShipmentOffer->price,
				$objShipmentOffer->extra_time_price,
				$objShipmentOffer->description,
				$objShipmentOffer->payment_condition,
				$objShipmentOffer->status,
				(Truck::find($objShipmentOffer->truck_id)->truck_system_number ?? ''),
			);

			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id				= $user->id;
				$notificationObj->language_id			= $description['language_id'];
				$notificationObj->title					= $description["subject"];
				$notificationObj->description			= str_replace($constants, $rep_Array, $description['body']);
				$notificationObj->is_read				= 0;
				$notificationObj->shipment_id			= $objShipment->id;
				$notificationObj->notification_type		= $actionType;
				$notificationObj->is_notification_sent	= 0;
				$notificationObj->map_id 				= $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id 			= $notificationObj->id;
					$notificationObj->save();
					$map_id = $notificationObj->id;
				}
		    }
			if($user->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $rep_Array, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$user->toArray())->onQueue('send_green_api_message');
				}
				
				$messageBody 	= 	str_replace($emailconstants, $rep_Array, $emailTemplates[0]['body']);
				
				$requestData = [
					"email" => $user->email,
					"name" => $user->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $user->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}
		}
	}

	public function send_notification_when_digital_and_no_certificate_uploaded($Information, $shipment){
		$notificationActions    =   NotificationAction::where('action','=',"when_digital_and_no_certificate_uploaded")
			->get()
			->toArray();

		$notificationTemplates =    NotificationTemplate::where('action','=',"when_digital_and_no_certificate_uploaded")
		->first();
		$notificationTemplateDescriptions =	NotificationTemplateDescription::with('NotificationAction')->where('parent_id','=',$notificationTemplates->id)
			->get();

		$cons           =   explode(',',$notificationActions[0]['options']);
		$constants      =   array();
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		}

		$emailActions 	= 	EmailAction::where('action','=','when_digital_and_no_certificate_uploaded')->get()->toArray();
		$emailTemplates = 	EmailTemplate::where('action','=','when_digital_and_no_certificate_uploaded')->get(array('name','subject','action','body','mail_enable'))->toArray();
		$emailcons 			= 	explode(',',$emailActions[0]['options']);
		$emailconstants 	= 	array();
		foreach($emailcons as $key => $val){
			$emailconstants[] = '{'.$val.'}';
		}
		$subject 		= 	$emailTemplates[0]['subject'];

		$map_id = 0;

		foreach($notificationTemplateDescriptions as $key => $description){
			if($description->NotificationAction['system_notification_enable'] == 1){
				$notificationObj = new Notification();
				$notificationObj->user_id               = $shipment->customer ?->id;
				$notificationObj->language_id           = $description['language_id'];
				$notificationObj->title                 = $description["subject"];
				$notificationObj->url                   = $Information['url'];
				$notificationObj->description           = str_replace($constants, $Information, $description['body']);
				$notificationObj->is_read               = 0;
				$notificationObj->shipment_id           = $shipment->shipment_id;
				$notificationObj->notification_type     = "when_digital_and_no_certificate_uploaded";
				$notificationObj->is_notification_sent  = 0;
				$notificationObj->map_id                = $map_id;
				$notificationObj->save();
				if($map_id == 0){
					$notificationObj->map_id            = $notificationObj->id;
					$notificationObj->save();
					$map_id = $notificationObj->id;
				}
	    	}
			if($shipment->customer ?->language == $description['language_id']){
				if($description->NotificationAction['whatsapp_notification_enable'] == 1){ 
					//send whatsapp message
					$whatsappDescription = str_replace($constants, $Information, $description['body']);
				    SendGreenApiMessage::dispatch($whatsappDescription,$shipment->customer)->onQueue('send_green_api_message');
				}

				$messageBody 	= 	str_replace($emailconstants, $Information, $emailTemplates[0]['body']);
				
				$requestData = [
					"email" => $shipment->customer ?->email,
					"name" => $shipment->customer ?->name,
					"subject" => $subject,
					"messageBody" => $messageBody,
				];
		
				if($emailTemplates[0]['mail_enable'] == 1 && $shipment->customer ?->email != null){
					SendMail::dispatch($requestData)->onQueue('send_mail');
				}
			}
		}
	}
}

