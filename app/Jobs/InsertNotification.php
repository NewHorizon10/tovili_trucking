<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Config;

class InsertNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $truckCompanyList;
    protected $forNotification;
    protected $objShipment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($truckCompanyList,$forNotification,$objShipment)
    {
        $this->truckCompanyList = $truckCompanyList;
        $this->forNotification = $forNotification;
        $this->objShipment = $objShipment;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Job processing started.');
        $this->insert_notification($this->truckCompanyList, $this->forNotification, $this->objShipment);
        Log::info('Job processing completed.');
    }

    public function insert_notification($truckCompanyList, $forNotification, $objShipment){
		foreach($truckCompanyList as $key => $truckCompany ){
			$map_id = 0;
            $selectedNotification = array();
			foreach($forNotification as  $key1 => $notification ){
				// $notificationObj = $notificationObjyujhbbjhb;

                if($notification['system_notification_enable'] == 1){    
                    $notificationObj = new Notification();
                    $notificationObj->user_id				= $truckCompany['id'];
                    $notificationObj->language_id			= $notification['language_id'];
                    $notificationObj->title					= $notification['subject'];
                    $notificationObj->description			= $notification['messageBody'];
                    $notificationObj->is_read				= 0;
                    $notificationObj->shipment_id			= $objShipment->id;
                    $notificationObj->notification_type		= $notification['notification_type'];
                    $notificationObj->is_notification_sent	= 0;
                    $notificationObj->map_id 				= $map_id;
                    $notificationObj->save();
                    if($map_id == 0){
                        $notificationObj->map_id 			= $notificationObj->id;
                        $notificationObj->save();
                        $map_id = $notificationObj->id;
                    }
                } 
                if($truckCompany['language'] == $notification['language_id'] ){
                    $selectedNotification   = $notification;
                    $message                = $notification['messageBody'];
                    $notification_type      = $notification['notification_type'];
                    $title                  = $notification['subject'];
                    $service_request_id     = $objShipment->id;
                    if($notification['system_notification_enable'] == 1){   
                    $service_number         = $notificationObj->id;
                    }
                
                    //send whatsapp message
                    if($notification['whatsapp_notification_enable'] == 1){   
                    SendGreenApiMessage::dispatch($notification['messageBody'],$truckCompany)->onQueue('send_green_api_message');
                    }
                }
			}

            
            if($notification['system_notification_enable'] == 1){    
                $data = array('title'=>$title,'message'=>$message,"image"=>"","service_request_id"=>$service_request_id,"service_number"=>$service_number);

                $user_device_tokens = DB::table("user_device_tokens")->where("user_id",$truckCompany['id'])->orderBy("id","DESC")->first();
               
                if($user_device_tokens){
                    $server_key = Config::get("Site.truck_company_android_sever_api_key");
                    $adwance_options = array(
                        'type' 				=> 'shipment',
                        'shipments_status'	=> $objShipment->status,
                        'map_id'			=> $map_id,
                        'request_number'	=> $objShipment->request_number,
                    );
                    SendPushNotification::dispatch($user_device_tokens->device_id, $user_device_tokens->device_type,$message,$notification_type,$data,$title,$map_id,$server_key,$adwance_options,$truckCompany['phone_number'])->onQueue('push_notifications');
                }
            }

		}
	}
}
