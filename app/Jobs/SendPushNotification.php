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
use Config;

use Illuminate\Support\Facades\DB;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deviceToken;
    protected $device_type;
    protected $message;
    protected $notification_type;
    protected $data;
    protected $notification_title;
    protected $map_id;
    protected $server_key;
    protected $adwance_options;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($deviceToken, $device_type, $message, $notification_type, $data, $notification_title, $map_id, $server_key,$adwance_options = array())
    {
        $this->deviceToken              = $deviceToken;
        $this->device_type              = $device_type;
        $this->message                  = $message;
        $this->notification_type        = $notification_type;
        $this->data                     = $data;
        $this->notification_title       = $notification_title;
        $this->map_id                   = $map_id;
		$this->server_key				= $server_key;
		$this->adwance_options			= $adwance_options;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Job push Notification processing started.');
        $this->send_push_notification($this->deviceToken, $this->device_type, $this->message, $this->notification_type, $this->data, $this->notification_title, $this->map_id, $this->server_key,$this->adwance_options);
        Log::info('Job push Notification processing completed.');
    }

    public function send_push_notification($deviceToken = "",$device_type = "",$message = "",$notification_type = "",$data = array(),$notification_title = "" ,$map_id,$server_key,$adwance_options = array()){
		$message = strip_tags(html_entity_decode($message));
        if($device_type == "android"){
			$registrationIds 		= array($deviceToken); 
			$msg = array (
				'body'				=> $message,
				'message'			=> $message,
				'title'				=> $notification_title,
				'vibrate'			=> 1,
				'android_channel_id'=> "channel-id",
				'sound'				=> "rush", 
				'response_data'		=> $data, 
				'data'		=> $data, 
				'notification_type'	=> $notification_type,
				'image'				=> $data['image'] ?? '',
				'service_request_id'=> $data['service_request_id'] ?? '',
				'service_number'	=> $data['service_number'] ?? '',
				'url'				=> $data['url'] ?? '',
			);
			$msg = array_merge($msg,$adwance_options);
			
			$fields = array (
				'registration_ids' 	=> $registrationIds,
				'data'				=> $msg,
				'notification'		=> $msg
			);
			$headers = array (
				'Authorization: key=' . $server_key,
				'Content-Type: application/json'
			);
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields) );
			$result = curl_exec($ch);
			curl_close( $ch );
			
            Notification::where("map_id",$map_id)->update(["is_notification_sent"=>1]);
			return array("response"=>$result,"request"=>$fields);

		}else {
			$url = "https://fcm.googleapis.com/fcm/send";
			$token = $deviceToken;
			$serverKey = Config('constants.android_sever_api_key') ; 
			$title = $notification_title;
			$body = $message;
			$notification = array('title' =>$title , 'text' => $body,'body' => $body, 'sound' => 'default', 'badge' => '1',"data"=>$data,'notification_type'=>$notification_type); 
			$arrayToSend = array('to' => $token, 'notification' => $notification, 'data' => $data,'priority'=>'high',"content_available"=>true);
			$json = json_encode($arrayToSend);
			$headers = array();
			$headers[] = 'Content-Type: application/json';
			$headers[] = 'Authorization: key='. $serverKey;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
			$response = curl_exec($ch);
			curl_close( $ch );
            Notification::where("map_id",$map_id)->update(["is_notification_sent"=>1]);
			return array("response"=>$response,"request"=>$arrayToSend);
		}
	}
}
