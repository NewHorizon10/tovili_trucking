<?php
/**
 * user Controller
 */
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\User;
use App\Model\Booking;
use App\Model\ServiceReminderFollowUps;
use App\Model\RetailServices;
use App\Model\UserNotification;
use App,Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;

class CronsController extends BaseController {

	public function send_service_fifteen_day_reminders(){
		$retailServices = RetailServices::where('is_close',0)
											->leftJoin('booking','booking.id','=','retail_services.booking_id')
											->leftJoin('inventories','inventories.id','=','booking.vehicle_id')
											->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id')
											->leftJoin('users','dealer_inventory.customer_id','=','users.id')
											->where('service_date','!=','')
											->where("retail_services.fifteen_day_before_reminder_date",date("Y-m-d"))
											->where("retail_services.is_sent_fifteen_day_reminder",0)
											->limit(10)
											->select('retail_services.*','users.phone_number','dealer_inventory.customer_id')
											->get()
											->toArray();
											
		if(!empty($retailServices)){
			foreach ($retailServices as $retailService) {
				$serviceDate			=	$retailService["service_date"];
				$phone_number			=	$retailService["phone_number"];
				$message				=	"Your+evolet+vehicle+service+is+due+on+$serviceDate.";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://trans.inexus.in/API/SMSHttp.aspx?UserId=arunpayal7@gmail.com&pwd=pwd2019&Message=$message&Contacts=$phone_number&SenderId=EVOLET&ServiceName=SMSTRANS");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$rsponse		=	curl_exec($ch);

				
				$user_device_tokens		=	DB::table("user_device_tokens")->where("user_id",$retailService["customer_id"])->get()->toArray();
				if(!empty($user_device_tokens)){
					foreach($user_device_tokens as $deviceDetail){
					
						$this->send_push_notification($deviceDetail->device_id,$deviceDetail->device_type,"Your evolet vehicle service is due on $serviceDate.","service_due",array(),"Service Due");
						
						$notficationArr                     	 =   new UserNotification();
						$notficationArr->user_id          		 =   $retailService["customer_id"];
						$notficationArr->nofication_string       =   "Your evolet vehicle service is due on $serviceDate.";
						$notficationArr->save();
					}
				}
				
				RetailServices::where('id',$retailService["id"])->update(array('is_sent_fifteen_day_reminder'=>1));
			}
	    }
		die("success");
	}
	
	public function send_service_seven_day_reminders(){
		$retailServices = RetailServices::where('is_close',0)
											->leftJoin('booking','booking.id','=','retail_services.booking_id')
											->leftJoin('inventories','inventories.id','=','booking.vehicle_id')
											->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id')
											->leftJoin('users','dealer_inventory.customer_id','=','users.id')
											->where('service_date','!=','')
											->where("retail_services.seven_day_before_reminder_date",date("Y-m-d"))
											->where("retail_services.is_sent_seven_day_reminder",0)
											->limit(10)
											->select('retail_services.*','users.phone_number','dealer_inventory.customer_id')
											->get()
											->toArray();
											
		if(!empty($retailServices)){
			foreach ($retailServices as $retailService) {
				$serviceDate			=	$retailService["service_date"];
				$phone_number			=	$retailService["phone_number"];
				$message				=	"Your+evolet+vehicle+service+is+due+on+$serviceDate.";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://trans.inexus.in/API/SMSHttp.aspx?UserId=arunpayal7@gmail.com&pwd=pwd2019&Message=$message&Contacts=$phone_number&SenderId=EVOLET&ServiceName=SMSTRANS");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_exec($ch);
				
				$user_device_tokens		=	DB::table("user_device_tokens")->where("user_id",$retailService["customer_id"])->get()->toArray();
				if(!empty($user_device_tokens)){
					foreach($user_device_tokens as $deviceDetail){
					
						$this->send_push_notification($deviceDetail->device_id,$deviceDetail->device_type,"Your evolet vehicle service is due on $serviceDate.","service_due",array(),"Service Due");
						
						$notficationArr                     	 =   new UserNotification();
						$notficationArr->user_id          		 =   $retailService["customer_id"];
						$notficationArr->nofication_string       =   "Your evolet vehicle service is due on $serviceDate.";
						$notficationArr->save();
					}
				}
				
				RetailServices::where('id',$retailService["id"])->update(array('is_sent_fifteen_day_reminder'=>1));
			}
	    }
		die("success");
	}

	public function send_service_third_day_reminders(){
		$retailServices = RetailServices::where('is_close',0)
											->leftJoin('booking','booking.id','=','retail_services.booking_id')
											->leftJoin('inventories','inventories.id','=','booking.vehicle_id')
											->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id')
											->leftJoin('users','dealer_inventory.customer_id','=','users.id')
											->where('service_date','!=','')
											->where("retail_services.third_day_before_reminder_date",date("Y-m-d"))
											->where("retail_services.is_sent_three_day_reminder",0)
											->limit(10)
											->select('retail_services.*','users.phone_number','dealer_inventory.customer_id')
											->get()
											->toArray();
											
		if(!empty($retailServices)){
			foreach ($retailServices as $retailService) {
				$serviceDate			=	$retailService["service_date"];
				$phone_number			=	$retailService["phone_number"];
				$message				=	"Your+evolet+vehicle+service+is+due+on+$serviceDate.";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://trans.inexus.in/API/SMSHttp.aspx?UserId=arunpayal7@gmail.com&pwd=pwd2019&Message=$message&Contacts=$phone_number&SenderId=EVOLET&ServiceName=SMSTRANS");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_exec($ch);
				
				$user_device_tokens		=	DB::table("user_device_tokens")->where("user_id",$retailService["customer_id"])->get()->toArray();
				if(!empty($user_device_tokens)){
					foreach($user_device_tokens as $deviceDetail){
					
						$this->send_push_notification($deviceDetail->device_id,$deviceDetail->device_type,"Your evolet vehicle service is due on $serviceDate.","service_due",array(),"Service Due");
						
						$notficationArr                     	 =   new UserNotification();
						$notficationArr->user_id          		 =   $retailService["customer_id"];
						$notficationArr->nofication_string       =   "Your evolet vehicle service is due on $serviceDate.";
						$notficationArr->save();
					}
				}
				
				RetailServices::where('id',$retailService["id"])->update(array('is_sent_fifteen_day_reminder'=>1));
			}
	    }
		die("success");
	}
	
	public function send_service_one_day_reminders(){
		$retailServices = RetailServices::where('is_close',0)
											->leftJoin('booking','booking.id','=','retail_services.booking_id')
											->leftJoin('inventories','inventories.id','=','booking.vehicle_id')
											->leftJoin('dealer_inventory','dealer_inventory.vehicle_id','=','booking.vehicle_id')
											->leftJoin('users','dealer_inventory.customer_id','=','users.id')
											->where('service_date','!=','')
											->where("retail_services.one_day_before_reminder_date",date("Y-m-d"))
											->where("retail_services.is_sent_one_day_reminder",0)
											->limit(10)
											->select('retail_services.*','users.phone_number','dealer_inventory.customer_id')
											->get()
											->toArray();
											
		if(!empty($retailServices)){
			foreach ($retailServices as $retailService) {
				$serviceDate			=	$retailService["service_date"];
				$phone_number			=	$retailService["phone_number"];
				$message				=	"Your+evolet+vehicle+service+is+due+on+$serviceDate.";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://trans.inexus.in/API/SMSHttp.aspx?UserId=arunpayal7@gmail.com&pwd=pwd2019&Message=$message&Contacts=$phone_number&SenderId=EVOLET&ServiceName=SMSTRANS");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_exec($ch);
				
				$user_device_tokens		=	DB::table("user_device_tokens")->where("user_id",$retailService["customer_id"])->get()->toArray();
				if(!empty($user_device_tokens)){
					foreach($user_device_tokens as $deviceDetail){
					
						$this->send_push_notification($deviceDetail->device_id,$deviceDetail->device_type,"Your evolet vehicle service is due on $serviceDate.","service_due",array(),"Service Due");
						
						$notficationArr                     	 =   new UserNotification();
						$notficationArr->user_id          		 =   $retailService["customer_id"];
						$notficationArr->nofication_string       =   "Your evolet vehicle service is due on $serviceDate.";
						$notficationArr->save();
					}
				}
				
				RetailServices::where('id',$retailService["id"])->update(array('is_sent_fifteen_day_reminder'=>1));
			}
	    }
		die("success");
	}
}// end CronsController class
