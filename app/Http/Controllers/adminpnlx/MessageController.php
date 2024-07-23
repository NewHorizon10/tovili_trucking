<?php

namespace App\Http\Controllers\adminpnlx;

use Auth;
use Redirect;
use App\Config;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Chat;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Services\HomepageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

use App\Models\UserDriverDetail;
use App\Models\UserCompanyInformation;

class MessageController extends Controller
{
    protected $model = 'chat';

    public function __construct(){
       
    }

    public function getMessageThread($property_id,$receiver_id){ 

		$userdata = User::where('id',1)->first();

		$property_id            =   $property_id;
		$receiver_id            =   $receiver_id;
		$response	            =	array();
		$modelId            	=   $userdata->id;
		$messages			    = 	Chat::where('attachment_parent',0)->leftJoin("users as sender","sender.id","sender_id")
									->leftJoin("users as receiver","receiver.id","receiver_id");
		$messagesDetails     	= 	$messages->where(function ($query) use($modelId,$property_id,$receiver_id){
										$query->orWhere(function ($query) use($modelId,$property_id,$receiver_id){
											$query->where("chats.sender_id",$modelId);
											$query->where("chats.receiver_id",$receiver_id);
										});
										$query->orWhere(function ($query) use($modelId,$property_id,$receiver_id){
											$query->where("chats.receiver_id",$modelId);
											$query->where("chats.sender_id",$receiver_id);
										});
									})
									->where("chats.channel_id",$property_id)
									->select('chats.message_type','chats.sender_id','chats.receiver_id','chats.message','chats.created_at as date',"sender.name as sender_name","sender.image as sender_image","receiver.name as receiver_name","receiver.image as receiver_image",'chats.id')
									->orderBy('chats.id','ASC')
									->get();

		$receiver_data  = User::where('id',$receiver_id)->first(); 
								
		if(empty($receiver_data->image)){
			$receiver_data->image	= User::where('id',$modelId)->value('image');
		}

		$auth_data  	= User::where('id',1)->first();  

		if(!empty($receiver_data)){
			$receiver_data->date_created = date(config("Reading.date_time_format"),strtotime($receiver_data->created_at));
		}

		if(!empty($messagesDetails)){
			foreach($messagesDetails as &$messages){

				if(request()->wantsJson()){
					$messages->sender_image_attachments 	= Chat::where('attachment_parent',$messages->id)->where('chats.sender_id',1)->where('chats.receiver_id',$messages->receiver_id)->get();
				}else{
					$messages->sender_image_attachments 	= Chat::where('attachment_parent',$messages->id)->where('chats.sender_id',1)->where('chats.receiver_id',$messages->receiver_id)->get()->toArray();
				}

			
				$messages->receiver_image_attachments 	= Chat::where('attachment_parent',$messages->id)->where('chats.sender_id',$messages->sender_id)->where('chats.receiver_id',1)->get()->toArray();

			

				if(!empty($messages->date)){
					$messages->create_date = $messages->date;
					
					$messages->date   = (date("Y-m-d",strtotime($messages->date)) == date("Y-m-d")) ? date("h:i A", strtotime($messages->date)) : date(config("Reading.date_time_format"),strtotime($messages->date));
				}
				if(!empty($messages->sender_image) ){
					$messages->sender_image		=   Config('constants.USER_IMAGE_PATH').$messages->sender_image;
				}else {
					$messages->sender_image		=	asset('public/img/noimage.png');
				}
				if(!empty($messages->receiver_image) ){
					$messages->receiver_image	= Config('constants.USER_IMAGE_PATH').$messages->receiver_image;
				}else {
					$messages->receiver_image	=	asset('public/img/noimage.png');
				}
			} 
			if(request()->wantsJson()){
				foreach($messagesDetails as $detail){
					if(!empty($detail->sender_image_attachments)){
						foreach($detail->sender_image_attachments as $value){
							$value->message			= Config('constants.MESSAGE_IMAGES_IMAGE_PATH').$value->message;
						}
					}
				}
			}
		
			$chatdata = Chat::where(function ($query) use($modelId,$receiver_id){
				$query->orWhere(function ($query) use($modelId,$receiver_id){
					$query->where("chats.sender_id",$modelId);
					$query->where("chats.receiver_id",$receiver_id);
				});
				$query->orWhere(function ($query) use($modelId,$receiver_id){
					$query->where("chats.receiver_id",$modelId);
					$query->where("chats.sender_id",$receiver_id);
				});
			})
			->where('is_read','0')
			->where("chats.channel_id",1)
			->update(array("is_read"=>1));



			$mediaData = Chat::where(function ($query) use($modelId,$receiver_id){
				$query->orWhere(function ($query) use($modelId,$receiver_id){
					$query->where("chats.sender_id",$modelId);
					$query->where("chats.receiver_id",$receiver_id);
				});
				$query->orWhere(function ($query) use($modelId,$receiver_id){
					$query->where("chats.receiver_id",$modelId);
					$query->where("chats.sender_id",$receiver_id);
				});
			})
			->where('message_type','attachment')
			->select('chats.*')
			->orderBy('chats.id','DESC')
			->get();

			$response["status"]				    =	"success";
			$response["message"]				=	"";
			$response["data"]					=	$messagesDetails; 
			$response["receiver_data"]			=	$receiver_data; 
			$response["auth_data"]			    =	$auth_data; 
			$response["mediaData"]			    =	$mediaData; 
			return $response;
		}else{
			$response["status"]				    =	"error";
			$response["message"]				=	"no History Found";
			$response["data"]					=	$messagesDetails; 
			$response["receiver_data"]			=	$receiver_data; 
			$response["auth_data"]			    =	$auth_data; 
			$response["mediaData"]			    =	$mediaData; 
			return $response;
		}
	
    }  

    public function attachment_image(Request $request)
    {
		
        if(!empty($request->images)){
            foreach ($request->images as $value) {
                if($request->hasFile('images')){
                    $extension                          =   $value->getClientOriginalExtension();
                    $original_image_name                =   $value->getClientOriginalName();
                    $fileName                           =   rand().time().'-images.'.$extension;
                    $folderName                         =   strtoupper(date('M'). date('Y'))."/";
                    $mimeType                           =   $value->getMimeType();
                    $imageSize                          =   $value->getSize();
                    $folderPath                         =   Config('constants.MESSAGE_IMAGES_ROOT_PATH').$folderName;
                    if(!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777,true);
                    }
                    $existsFiles =  explode('/',$mimeType);
                    if ($existsFiles[1]=="jpg" || $existsFiles[1]=="jpeg" || $existsFiles[1]=="png" || $existsFiles[1]=="mp4" || $existsFiles[1]=="pdf" || $existsFiles[1]=="zip" || $existsFiles[1]=="odt" || $existsFiles[1]=="docx" || $existsFiles[1]=="xlsx"){
                        $value->move($folderPath, $fileName);
                    }
					
                    $images[] = [
                        'image'         =>  $request->wantsJson() ? Config('constants.MESSAGE_IMAGES_IMAGE_PATH').$folderName.$fileName : $folderName.$fileName,
                        'original_name' =>  $original_image_name,
                        'type'          =>  $mimeType,
                        'size'          =>  $imageSize,
						'msg'			=>  $folderName.$fileName
                    ];
                }
            }
            return $images;
        }
    }

 	public function portfolio_image_add_delete(Request $request)
 	{
 		if($request->isMethod('POST')){
 			$file_path = Config('constants.MESSAGE_IMAGES_ROOT_PATH').$request->image;
 			if(file_exists($file_path)){
 				unlink($file_path);
 				return 'success';
 			}
 		}
 	}

	public function sendSmsUser(Request $request){	
		if($request->isMethod('POST')){

			if($request->receiver_id > 1){
				$groupbY_id = '1'.'_'.$request->receiver_id.'_'.$request->property_id;
			}else{
				$groupbY_id = $request->receiver_id.'_'.'1'.'_'.$request->property_id;

			}
			$property_host_message                     	=  new Chat;
			$property_host_message->sender_id   		= 1;
			$property_host_message->receiver_id 		= $request->receiver_id;
			$property_host_message->groupbY_id 			= $groupbY_id;
			$property_host_message->channel_id 		    = 1;
			$property_host_message->message            	= $request->message;
			$property_host_message->is_read            	= '0';
			$property_host_message->save();
 
			 $images = array();
			 if(!empty($request->images)){

				foreach ($request->images as $key => $value) {
					$images[$key] = [
						'image'         => $value,
						'original_name' => $request->original_name[$key],
						'type'          => $request->original_name[$key],
						'size'          => $request->size[$key],
					];
					
					$types = explode('.',$request->original_name[$key]);
					$types = end($types);  

					$types_image = '' ;

					if(in_array($types,['png','jpg','jpeg']) ){
						$types_image = 'image';
					}elseif(in_array($types,['zip','odt','pdf','doc','docx','xlsx']) ){
						$types_image = $types;
					}elseif($types == 'mp4'){
						$types_image = 'video';
					}
			       
					$property_host_message1                     	= new Chat;
					$property_host_message1->sender_id   			= 1;
                    $property_host_message1->channel_id 		    = 1;
					$property_host_message1->groupbY_id 			= $groupbY_id;
					$property_host_message1->attachment_parent   	= $property_host_message->id;
					$property_host_message1->receiver_id 			= $request->receiver_id;
					$property_host_message1->message_type        	= 'attachment';
					$property_host_message1->image_name          	= $request->original_name[$key];
					$property_host_message1->is_read            	= '0';
					$property_host_message1->message            	= $value;
					$property_host_message1->types_image           = $types_image;
					$property_host_message1->save();
				}
			 }

				
			if(!empty($property_host_message->save())){
				return $data = ['status'=>'success'];
			}else{
				return 'error';
			}
		 };
	}

	public function toggleChat(Request $request){
		$selectedUser = $this->getMessageThread($request->id,$request->receiverid); 
		$auth 		  = User::where('id',1)->first();
		return response()->json($selectedUser);	

	}


	public function customersupport(Request $request){

		$user = User::where('id',1)->first();

		$admin_data = User::where('customer_type','admin')->where('id',1)->first();
		$response 				= array();
		$modelId 				= $user->id;
		$keyword 				= $request->get('keyword');
		$users 					= User::query();
		$auth 					= User::where('id',1)->first();
		$host_properties_ids	= array();

        if(!empty($auth->image)){
			$auth->image = $auth->image;
		}

		$messagesDetails = DB::table('chats')
									->select('chats.*')
									->where('channel_id',1)
									->where(function($query) use ($modelId) {
										$query->where('sender_id', $modelId)
											->orWhere('receiver_id', $modelId);
									})
									->whereIn('id', function($query) use ($modelId) {
										$query->select(DB::raw('MAX(id)'))
											->from('chats')
											->where('channel_id',1)
											->where(function($q) use ($modelId) {
												$q->where('sender_id', $modelId)
													->orWhere('receiver_id', $modelId);
											})
											->groupBy('groupbY_id');
									})
									->orderBy('created_at', 'DESC')
									->get();
		if($messagesDetails->isNotEmpty()){

			foreach($messagesDetails as &$my_messages){
				
				$my_messages->model_id 	= $modelId;
				$sender_detail 			= User::where('id',$my_messages->sender_id)->first();
				$receiver_detail 		= User::where('id',$my_messages->receiver_id)->first();

				if($sender_detail->id == $modelId){
					$my_messages->name 			= 	$receiver_detail->name ?? '';
					$my_messages->active_id 	= 	$receiver_detail->id;
					$property_id            	=   $my_messages->channel_id;
					$receiver_id            	=   $receiver_detail->id;

					if(!empty($receiver_detail->image)){
						if($receiver_detail->user_role_id == 4){
							$driverDetails = UserDriverDetail::where('user_id', $receiver_detail->id)->first();
							$my_messages->reciver_image = Config('constants.DRIVER_PICTURE_PATH') . $driverDetails->driver_picture;
						}
						else if($receiver_detail->user_role_id == 3){
							$companyDetails = UserCompanyInformation::where('user_id', $receiver_detail->id)->first();
							$my_messages->reciver_image = ($companyDetails->contact_person_picture != null) ? $companyDetails->contact_person_picture : config('constants.NO_IMAGE_PATH');
						}else{
							$my_messages->reciver_image = $receiver_detail->image;
						}

					}else{
						$my_messages->reciver_image	= config('constants.NO_IMAGE_PATH');
					}

					$total_unread_sms = Chat::where('channel_id',$my_messages->channel_id)->where('receiver_id',$receiver_detail->id)->where('is_read',0)->count();

				}else{
					$my_messages->name 			= 	$sender_detail->name;
					$my_messages->active_id 	= 	$sender_detail->id;
					$property_id            	=   $my_messages->channel_id;
					$receiver_id            	=   $sender_detail->id;

					if(!empty($sender_detail->image)){
						$my_messages->reciver_image = $sender_detail->image;
					}else{
						$my_messages->reciver_image	= config('constants.NO_IMAGE_PATH');
					}
					$total_unread_sms = Chat::where('channel_id',$my_messages->channel_id)->where('receiver_id',$sender_detail->id)->where('is_read',0)->count();
				}

				
				$messages			    = 	Chat::where('attachment_parent',0)->leftJoin("users as sender","sender.id","sender_id")
											->leftJoin("users as receiver","receiver.id","receiver_id");

				$chat_data     			= 	$messages->where(function ($query) use($modelId,$property_id,$receiver_id){
												$query->orWhere(function ($query) use($modelId,$property_id,$receiver_id){
													$query->where("chats.sender_id",$modelId);
													$query->where("chats.receiver_id",$receiver_id);
												});
												$query->orWhere(function ($query) use($modelId,$property_id,$receiver_id){
													$query->where("chats.receiver_id",$modelId);
													$query->where("chats.sender_id",$receiver_id);
												});
											})
											->where("chats.channel_id",$property_id)->orderBy('chats.id','DESC')
											->select('chats.*','receiver.user_role_id','receiver.name','receiver.name','receiver.name as business_name','receiver.image as business_logo','receiver.email','receiver.phone_number')
											->first();
				
				if(!empty($total_unread_sms)){
					$my_messages->total_unread_sms = $total_unread_sms;
				}else{
					$my_messages->total_unread_sms = 0;
				}

				if(!empty($chat_data)){
					$my_messages->last_message_date = (date("Y-m-d",strtotime($chat_data->created_at)) == date("Y-m-d")) ? date("h:i A", strtotime($chat_data->created_at)) : date(config("Reading.date_time_format"),strtotime($chat_data->created_at));
					$my_messages->last_message = $chat_data->message;
					$my_messages->message_type = $chat_data->message_type;
				}

				
			}
		}
		$userDetails        = User::where('id',1)->first();
		$selectedUser    	=	'';
		$count = 0;
		if($messagesDetails->isNotEmpty()){ 
			$count = $messagesDetails->count();
			$selectedUser = $this->getMessageThread($messagesDetails[0]->channel_id,$messagesDetails[0]->active_id);
		}


		if($request->wantsJson()){
			return response()->json([
				'status'=>'success',
				'messagesDetails'=>$messagesDetails,
				'auth'=>$auth,
				'userDetails'=>$userDetails,
				'selectedUser'=>$selectedUser,
				'count'=>$count
			]);
		}
		
		return View("admin.$this->model.chat",compact('messagesDetails','auth','userDetails','selectedUser','user','admin_data','count'));
	}

	public function toggle_chat_html(Request $request){
		$selectedUser = $this->getMessageThread($request->id,$request->receiverid); 
		$auth 		  = User::where('id',1)->first();
		return View("admin.$this->model.toggleChat",compact('selectedUser','auth'));
	}

	public function toggle_chat_media(Request $request){
		$selectedUser = $this->getMessageThread($request->id,$request->receiverid); 
		$auth 		  = User::where('id',1)->first();
		return View("admin.$this->model.appendtoggleChatmedia",compact('selectedUser','auth'));
	}
}

