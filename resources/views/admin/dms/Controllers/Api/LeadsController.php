<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use App\Model\Leads;
use App\Model\LeadComments;
use App\Model\LeadFollowups;
use App\Model\LeadLogs;
use App\Model\LeadCommentAttachments;
use App\Model\LeadAttachments;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Image,Toast;
use Illuminate\Http\Request;

/**
* Leads Controller
*
* Add your methods in the class below
*
* This file will render views from views/api
*/
 
class LeadsController extends BaseController {
	
	/**
	* Function use for signup a user
	*
	* @param null
	*
	* @return response
	*/
	public function createLead(){
		$formData	=	Input::all();
		$response	=	array();
		if(!empty($formData)){
			Input::replace($this->arrayStripTags(Input::all()));

			$validator 					=	Validator::make(
				Input::all(),
				array(
					'name' 					=>	 'required',
					'phone' 				    =>	 'required',
					//'opportunity'					=>	 'required',
					'email'                         =>   'required|email',
					//'state'							=>	 'required',
					'city'							=>	 'required',
					//'country'                       =>	 'required',
				)
			);
			if ($validator->fails()){
				$response				=	$this->change_error_msg_layout($validator->errors());
			}else{
				
				$dealer_id		  	 =	0;
				$stateId       		 =  0;
				$cityId       		 =  0;
				
				$stateName = (Input::get('state')) ? Input::get('state'): '';
				$statedata = DB::table('states')->where("name",$stateName)->select("id")->first();
				if(!empty($statedata)){
					$stateId = $statedata->id;
				}
				
				$cityName = (Input::get('city')) ? Input::get('city'): '';
				$citydata = DB::table('cities')->where("name",'like','%'.$cityName.'%')->select("id")->first();
				if(!empty($citydata)){
					$cityId = $citydata->id;
				}
				
				
				  
				$leads              	 = New Leads;
				$leads->first_name  	 = Input::get('name');
				$leads->last_name   	 = (Input::get('last_name')) ? Input::get('last_name'): '';
				$leads->dealer_id        = $dealer_id;
				$leads->user_id          = ADMIN_ID;
				$leads->phone_number     = Input::get('phone');
				$leads->opportunity      = 'website';
				$leads->email            = Input::get('email');
				$leads->country          = (Input::get('country')) ? Input::get('country'): '';
				$leads->state            = $stateId;
				$leads->city             = $cityId;
				$leads->sales_person_assigned = 0; 
				$leads->team_ticket_assigned  = 0;
				$leads->comments  			= (Input::get('message')) ? Input::get('message'): '';
				$leads->tags  				= (Input::get('tags')) ? Input::get('tags'): '';
                $leads->status				= NEW_LEAD;
				$leads->save();
				$id  = $leads->id;
				if(!empty($id)){
				  	$leadNum = 10000+$id;
				  	//$leadNum = '#'.rand(0000000,9999999).$id;
				    Leads::where('id',$id)->update(array('lead_num'=>'#'.$leadNum));
				  	$res[LEAD_ADDED_ID] = $id;
	                $action      = LEAD_ADDED;
	                $user_id     = ADMIN_ID;
	                $data_string = json_encode($res);
	                $lead_id     = $id;
	                $this->AddleadLogs($lead_id,$user_id,$data_string,$action);
					
					$response				=	array();
					$response["status"]		=	"success";
					$response["data"]		=	(object)array('lead_number'=>$leadNum);
					$response["msg"]		=	"Lead has been added successfully.";
					
				}else{
					$response				=	array();
					$response["status"]		=	"error";
					$response["data"]		=	(object)array();
					$response["msg"]		=	"Something went wrong.";
				}
			}
		}else{
			$response				=	array();
			$response["status"]		=	"error";
			$response["data"]		=	(object)array();
			$response["msg"]		=	"Invalid Request.";
		}
		return response()->json($response,200);
	}
}//end LeadsController
