<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\User;
use App\Model\Leads;
use App\Model\LeadComments;
use App\Model\LeadFollowups;
use App\Model\LeadLogs;
use App\Model\LeadCommentAttachments;
use App\Model\LeadAttachments;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;

/**
* LeadsController Controller
*
* Add your methods in the class below
*
* This file will render views\LeadsController\Index
*/
class LeadsController extends BaseController {
  	public $model	=	'Leads';

  	public function __construct() {
		View::share('modelName',$this->model);
	}

	/*
	 * Function of leads  listing 
	 */
	public function index(){
		$dealer_id				=	0;
        $DB 		            = 	Leads::query();
        if(Auth::user()->user_role_id==ADMIN_STAFF_ROLE_ID){
			$DB = $DB->where('leads.sales_person_assigned',Auth::user()->id);
		}else{
			$DB = $DB->where('leads.dealer_id',$dealer_id);
		}
		
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
		if ((Input::get())) {
			$searchData			=	Input::get();
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
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == 'first_name'){
						$DB->where(function ($query) use($fieldValue){
							$query->Orwhere("leads.first_name","LIKE","%".$fieldValue."%");
							$query->Orwhere("leads.last_name","LIKE","%".$fieldValue."%");
						});	
					}
					if($fieldName == 'lead_num'){
						$DB->where("leads.lead_num",$fieldValue);
					}
					if($fieldName == 'opportunity'){
						$DB->where("leads.opportunity",$fieldValue);
					}
					if($fieldName == 'phone_number'){
						$DB->where("leads.phone_number","LIKE","%".$fieldValue."%");
					}
					if($fieldName == 'email'){
						$DB->where("leads.email","LIKE","%".$fieldValue."%");
					}
					if($fieldName == 'tags'){
						$DB->where("leads.tags","LIKE","%".$fieldValue."%");
					}
					if($fieldName == 'state'){
						$DB->where("leads.state",$fieldValue);
					}
					if($fieldName == 'team_ticket_assigned'){
						$DB->where("leads.team_ticket_assigned",$fieldValue);
					}
					if($fieldName == 'sales_person_assigned'){
						$DB->where("leads.sales_person_assigned",$fieldValue);
					}
					if($fieldName == 'status'){
						$DB->where("leads.status",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
	
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB
									->where('leads.is_deleted',0)
									->select("leads.*",
										DB::raw("(SELECT full_name FROM users WHERE id = leads.sales_person_assigned) as sales_person_assigned"),
										DB::raw("(SELECT full_name FROM users WHERE id = leads.team_ticket_assigned) as team_ticket_assigned"),
										DB::raw("(SELECT name FROM states WHERE id = leads.state) as state"),
										DB::raw("(SELECT name FROM cities WHERE id = leads.city) as city"))
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));

		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
	   
		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',COUNTRY_ID)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray();
		$sales_consultant	= User::where('user_role_id',ADMIN_STAFF_ROLE_ID)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();
							
	    $teamTicketsAssigned = User::where('user_role_id',ADMIN_STAFF_ROLE_ID)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();
							
		return view('admin.'.$this->model.'.index',compact('result' ,'searchVariable','sortBy','order','query_string','stateList','sales_consultant','teamTicketsAssigned'));
	}
    
    /*
	 * Function of show add leads page
	 */
	public function addLeads(){
	  $opportunities    = Config::get('leads_opportunities');
	  $dealer_id	    =	0;
	  $stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',COUNTRY_ID)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray();
	 $sales_consultant	= User::where('user_role_id',ADMIN_STAFF_ROLE_ID)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();
	 $teamTicketsAssigned = User::where('user_role_id',ADMIN_STAFF_ROLE_ID)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();

      return view('admin.'.$this->model.'.add',compact('opportunities','stateList','sales_consultant','teamTicketsAssigned'));
	}
    /*
	 * Function of show save leads 
	 */
	public function saveLeads(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//print_r($formData);die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'first_name' 					=>	 'required',
					'phone_number' 				    =>	 'required|numeric|digits:10',
					'opportunity'					=>	 'required',
					'email'                         =>   'required|email',
					'state'							=>	 'required',
					'city'							=>	 'required',
					'country'                       =>	 'required',
				 )/* ,
				array(
					'first_name.required'	    => 	 'First name is required.',
					'phone_number.required'  	=> 	 'Phone number is required.',
					'opportunitity.required'	=> 	 'Opportunitity is required.',
					'state.required'	        => 	 'State is required.',
					'city.required'	            => 	 'City is required.',
					'phone_number.numeric'	        => 	 'Phone number must have a numeric value.',
					'phone_number.integer'	        => 	 'Phone number must have 10 digits.',
				) */
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				  $dealer_id		  =	0;
				  
				  $leads              = New Leads;
				  $leads->first_name  = Input::get('first_name');
                  $leads->last_name   = (Input::get('last_name')) ? Input::get('last_name'): '';
				  $leads->dealer_id        = $dealer_id;
				  $leads->user_id          = Auth::user()->id;
				  $leads->phone_number     = Input::get('phone_number');
				  $leads->opportunity      = Input::get('opportunity');
				  $leads->email            = Input::get('email');
				  $leads->country          = Input::get('country');
				  $leads->state            = Input::get('state');
				  $leads->city             = Input::get('city');

				  if(Auth::user()->user_role_id==ADMIN_STAFF_ROLE_ID){
						$sales_id =Auth::user()->id;
						$leads->sales_person_assigned=$sales_id;
		           }else{
						$leads->sales_person_assigned = (Input::get('sales_person_assigned'))?Input::get('sales_person_assigned'): '';
		           }
				  
				  $leads->team_ticket_assigned  = (Input::get('team_ticket_assigned'))? Input::get('team_ticket_assigned'):'';
				  $leads->phone_number  = Input::get('phone_number');
				  $leads->comments  = (Input::get('comments')) ? Input::get('comments'): '';
				  $leads->tags  = (Input::get('tags')) ? Input::get('tags'): '';
                  if($leads->sales_person_assigned ==''){
                  	$leads->status=NEW_LEAD;
                  }else{
                  	$leads->status=ASSIGNED;
                  }
				  $leads->save();
				  $id  = $leads->id;
				  if(!empty($id)){
				  	$leadNum = 10000+$id;
				  	//$leadNum = '#'.rand(0000000,9999999).$id;
				     Leads::where('id',$id)->update(array('lead_num'=>'#'.$leadNum));
				  	
				  	if(Input::hasFile('attachment')){
				  	 $i=0;
				     foreach (Input::file("attachment") as $file) {
				      $image = $file;
				      $newImageName ='-'.$i.'-lead-file';
				      $file_name  = $file->getClientOriginalName();
				      $file_size  = $this->fileSizeReadable(filesize($file));
                      $imageSrc  = $this->imageUpload($image,LEAD_IMAGE_ROOT_PATH,$newImageName);
                       if($imageSrc){

                          $leadAttachment             = new LeadAttachments;
                          $leadAttachment->lead_id    = $id;
                          $leadAttachment->attachment = $imageSrc;
                          $leadAttachment->is_deleted = 0;
                          $leadAttachment->file_name  = $file_name;
                          $leadAttachment->file_size  = $file_size;
                          $leadAttachment->save();
                        }
                        $i++;
                       }
                       
                    }
				  	$res[LEAD_ADDED_ID] = $id;
	                $action      = LEAD_ADDED;
	                $user_id     = Auth::user()->id;
	                $data_string = json_encode($res);
	                $lead_id     = $id;
	                
	                $this->AddleadLogs($lead_id,$user_id,$data_string,$action);
                    Session::flash("success",trans($this->model."has been added successfully."));
				    return Redirect::to(route("admin.".strtolower($this->model).'.index'));
				  }else{
				  	Session::flash("error",trans("Something Went Wrong"));
				  	return Redirect::back();
				  }
			}

		}else{
            Session::flash("error",trans("Something Went Wrong"));
		  	return Redirect::back();
		}

		
	}//End Save Leads 

	public function editLeads($id){
		if(Auth::user()->user_role_id==ADMIN_STAFF_ROLE_ID){
			$data   = Leads::where(['id'=>$id])->where('leads.sales_person_assigned',Auth::user()->id)->first();
		}else{
			$dealer_id				=	0;
			$data   = Leads::where(['id'=>$id])->where('leads.dealer_id',$dealer_id)->first();
		}
		if(empty($data)) {
			return Redirect::back();
		}
		$opportunities    = Config::get('leads_opportunities');
		$dealer_id				=	0;
		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',COUNTRY_ID)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray();
	   $sales_consultant	= User::where('user_role_id',ADMIN_STAFF_ROLE_ID)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();
	    $teamTicketsAssigned = User::where('user_role_id',ADMIN_STAFF_ROLE_ID)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();
		$cityList		=	DB::table('cities')
						   ->where('state_id',$data->state)
						   ->distinct('name')
						   ->pluck('name','id')
						   ->toArray(); 
						   
        $imageDetails 	=  LeadAttachments::where('lead_id',$id)->select("attachment","id")->get()->toArray();
		
		return view('admin.'.$this->model.'.edit',compact('opportunities','stateList','sales_consultant','teamTicketsAssigned','data','cityList','imageDetails'));
	}
     /*
     *
     *Update Leads Function
     *
     */
	public function updateLeads($id){
		if(Auth::user()->user_role_id==ADMIN_STAFF_ROLE_ID){
			$data   = Leads::where(['id'=>$id])->where('leads.sales_person_assigned',Auth::user()->id)->first();
		}else{
			$dealer_id				=	0;
			$data   = Leads::where(['id'=>$id])->where('leads.dealer_id',$dealer_id)->first();
		}
		if(empty($data)) {
			return Redirect::back();
		}
	  
	   Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					'first_name' 					=>	 'required',
					'phone_number' 				    =>	 'required|numeric|digits:10',
					'opportunity'					=>	 'required',
					'email'                         =>   'required|email',
					'state'							=>	 'required',
					'city'							=>	 'required',
					'country'                       =>	 'required',
				 )
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
					
					$res[LEAD_EDITED_ID] = $id;
					$action      = LEAD_EDITED;
					$user_id     = Auth::user()->id;
					$data_string = json_encode($res);
					$lead_id     = $id;
					$this->AddleadLogs($lead_id,$user_id,$data_string,$action);
					
				  $dealer_id		  =	0;
				  
				  $leads              = Leads::find($id);
				  $leads->first_name  = Input::get('first_name');
                  $leads->last_name   = (Input::get('last_name')) ? Input::get('last_name'): '';
				  $leads->phone_number     = Input::get('phone_number');
				  $leads->opportunity      = Input::get('opportunity');
				  $leads->email            = Input::get('email');
				  $leads->country          = Input::get('country');
				  $leads->state            = Input::get('state');
				  $leads->city             = Input::get('city');

				  $leads->team_ticket_assigned  = (Input::get('team_ticket_assigned'))? Input::get('team_ticket_assigned'):'';
				  $leads->phone_number  = Input::get('phone_number');
				  $leads->comments  = (Input::get('comments')) ? Input::get('comments'): '';
				  $leads->tags  = (Input::get('tags')) ? Input::get('tags'): '';
					if(!empty(Input::get('sales_person_assigned'))){
						if(Auth::user()->user_role_id==ADMIN_STAFF_ROLE_ID){
							$leads->sales_person_assigned=Auth::user()->id;
						}else{
							if($leads->sales_person_assigned == ""){
								$leads->sales_person_assigned	=	Input::get('sales_person_assigned');
								$leads->status = ASSIGNED;
								$res[LEAD_STATUS_CHANGE_ID] = ASSIGNED;
								$action  = LEAD_STATUS_CHANGED;
								$user_id = Auth::user()->id;
								$data_string= json_encode($res);
								$this->AddleadLogs($id,$user_id,$data_string,$action);
							}else if($leads->sales_person_assigned != Input::get('sales_person_assigned')){
								$leads->sales_person_assigned	=	Input::get('sales_person_assigned');
								$res[SALES_PERSON_ASSIGNED_ID] = Input::get('sales_person_assigned');
							    $action      = NEW_SALES_PERSON_ASSIGNED;
							    $user_id     = Auth::user()->id;
							    $data_string = json_encode($res);
							    $lead_id     = $id;
							    $this->AddleadLogs($lead_id,$user_id,$data_string,$action);
							}
						}
					}
				  $leads->save();
				  $id  = $leads->id;
				  if(!empty($id)){
				  	if(Input::hasFile('attachment')){
				  	 $i=0;
				     foreach (Input::file("attachment") as $file) {
				      $image = $file;
				      $newImageName ='-'.$i.'-lead-file';
				      $file_name  = $file->getClientOriginalName();
				      $file_size  = $this->fileSizeReadable(filesize($file));
                      $imageSrc  = $this->imageUpload($image,LEAD_IMAGE_ROOT_PATH,$newImageName);
                       if($imageSrc){

                          $leadAttachment             = new LeadAttachments;
                          $leadAttachment->lead_id    = $id;
                          $leadAttachment->attachment = $imageSrc;
                          $leadAttachment->is_deleted = 0;
                          $leadAttachment->file_name  = $file_name;
                          $leadAttachment->file_size  = $file_size;
                          $leadAttachment->save();
                        }
                        $i++;
                       }
                       
                    }
					
					
                    Session::flash("success",trans($this->model."has been updated successfully."));
				    return Redirect::to(route("admin.".strtolower($this->model).'.index'));
				  }else{
				  	Session::flash("error",trans("Something Went Wrong"));
				  	return Redirect::back();
				  }
			}
		}else{
            Session::flash("error",trans("Something Went Wrong"));
		  	return Redirect::back();
		}
	}//end
   
    //End Update Function 

	/*
     *
     *Delete Leads Function
     *
     */

    public function deleteLead($id = ''){
		$leadsDetail			=	Leads::find($id); 
		if(empty($leadsDetail)) {
			return Redirect::back();
		}
		if($id){	
		  $Model =Leads::where('id',$id)->update(array('is_deleted'=>1));
          Session::flash('flash_notice',trans( $this->model." has deleted successfully."));
         }
		return Redirect::back();
	}

	/*
     *
     * View Leads Function
     *
     */

	public function viewLeads($id){
		if(Auth::user()->user_role_id==ADMIN_STAFF_ROLE_ID){
			 $dealer_id =	0;
			$data      =  Leads::where('id',$id)
                    ->where('leads.is_deleted',0)
					->where('leads.sales_person_assigned',Auth::user()->id)
					->select("leads.*",
					DB::raw("(SELECT profile_image FROM users WHERE id = leads.user_id) AS UserImage"),
					DB::raw("(SELECT profile_image FROM users WHERE id = leads.dealer_id) AS DealerImage"),
					DB::raw("(SELECT full_name FROM users WHERE id = leads.sales_person_assigned) as sales_person_assigned"),
					DB::raw("(SELECT profile_image FROM users WHERE id = leads.sales_person_assigned) as SalesPersonImage"),
					DB::raw("(SELECT full_name FROM users WHERE id = leads.team_ticket_assigned) as team_ticket_assigned"),
					DB::raw("(SELECT profile_image FROM users WHERE id = leads.team_ticket_assigned) as TeamMemberImage"),
					DB::raw("(SELECT name FROM states WHERE id = leads.state) as state"),
					DB::raw("(SELECT name FROM cities WHERE id = leads.city) as city"))
					->first();
		}else{
			$dealer_id				=	0;
			$data      =  Leads::where('id',$id)
                    ->where('leads.is_deleted',0)
					->where('leads.dealer_id',$dealer_id)
					->select("leads.*",
					DB::raw("(SELECT full_name FROM users WHERE id = leads.sales_person_assigned) as sales_person_assigned"),
					DB::raw("(SELECT full_name FROM users WHERE id = leads.team_ticket_assigned) as team_ticket_assigned"),
					DB::raw("(SELECT name FROM states WHERE id = leads.state) as state"),
					DB::raw("(SELECT name FROM cities WHERE id = leads.city) as city"))
					->first();
		}
		if(empty($data)) {
			return Redirect::back();
		}
	 
        $leadsComments  = LeadComments::where('lead_id',$id)
                           ->select("lead_comments.*",
					         DB::raw("(SELECT full_name FROM users WHERE id = lead_comments.user_id) as userName"))
                           ->orderby('id','DESC')
                           ->get();

        
      
        $leadFollowups   =   LeadFollowups::where('lead_id',$id)
                            ->select("lead_followups.*",
					         DB::raw("(SELECT full_name FROM users WHERE id = lead_followups.user_id) as userName"))->orderby('id','DESC')->get();
        $leadLogs        =   LeadLogs::where('lead_id',$id)->select("lead_logs.*",
					         DB::raw("(SELECT full_name FROM users WHERE id = lead_logs.user_id) as userName"))->orderby('id','DESC')->get();
        $leadAttachments =   LeadAttachments::where('lead_attachments.lead_id',
        	                  $id)
                             ->leftJoin('leads','lead_attachments.lead_id','leads.id')
                             ->select("lead_attachments.*",
                              	DB::raw("(SELECT full_name FROM users WHERE id = leads.user_id) as userName"))
                             ->orderby('id','DESC')->get();

        $LeadCommentAttachments =   LeadComments::where('lead_comments.lead_id',$id)
                                     ->leftJoin('lead_comment_attachments','lead_comment_attachments.comment_id','lead_comments.id')
                                     ->leftJoin('leads','leads.id','lead_comments.lead_id')
                                     ->orderby('lead_comment_attachments.id','DESC')->get();

        return view('admin.'.$this->model.'.view',compact('data','leadsComments','leadFollowups','leadLogs','leadAttachments','LeadCommentAttachments'));
	}//End view lead function 

    /*
     *
     * View Leads Function - Add Comment function 
     *
     */
	public function addLeadComment(){
        $response   = array();
        $user_id    =Auth::user()->id;
        $thisData	= Input::all();
        $res=array();
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'lead_id' 	=>	 'required',
					'comment' 	=>	 'required',
				),
				array(
					"comment.required" =>trans("The comment field is required."),
				)
			);
			if ($validator->fails()) {	
				$response				=	array(
					'status' 			=> 	'error',
					'errors' 			=> 	$validator->errors(),
				);
				
			}else{
				$lead_id	=	input::get("lead_id");
				$cmnt	=	input::get("comment");
				$dealer_id				=	0;
				$comment            = new LeadComments;
				$comment->lead_id   = $lead_id;
				$comment->comments  = $cmnt;
				$comment->user_id   = $user_id;
				$comment->dealer_id = $dealer_id;
				$comment->save();
				$commentId= $comment->id;
				if(Input::hasFile('image')){
              	  $i = 1;
				  foreach (Input::file("image") as $file) {
				     $image =$file;
				     $newImageName ='-lead-comment-file';
				     $file_name  = $file->getClientOriginalName();
				     $file_size  = $this->fileSizeReadable(filesize($file));
                     $imageSrc  = $this->imageUpload($image,LEAD_COMMENT_IMAGE_ROOT_PATH,$newImageName);
                       if($imageSrc){
                         $commentAttachment             = new LeadCommentAttachments;
                         $commentAttachment->comment_id = $commentId;
                         $commentAttachment->attachment = $imageSrc;
                         $commentAttachment->file_name  = $file_name;
                         $commentAttachment->file_size  = $file_size;
                         $commentAttachment->save();
                       }
                        
                   }
                }
				 
				$res1[LEAD_COMMENT_ID] =$comment->id;
				$action1 =LEAD_COMMENTED;
				$data_string1 = json_encode($res1);
				$this->AddleadLogs($lead_id,$user_id,$data_string1,$action1);
				$response['status']  ='success';
				$response['errors']  ='';
				Session::flash("success",trans("Comment Added Successfully"));
				
				if(!empty(Input::get('status_type'))){
					if(Input::get('status_type') == "work_in_progress"){
						$db_status		=	"work_in_progress";
						$db_log_status	=	WORK_IN_PROGRESS;
					}elseif(Input::get('status_type') == "converted"){
						$db_status		=	"Converted";
						$db_log_status	=	CONVERTED;
					}elseif(Input::get('status_type') == "closed"){
						$db_status		=	"Closed";
						$db_log_status	=	LEAD_CLOSED;
					}elseif(Input::get('status_type') == "assigned"){
						$db_status		=	"assigned";
						$db_log_status	=	ASSIGNED;
					}else{
						$db_status		=	"new_lead";
						$db_log_status	=	"new_lead"; 
					}
					Leads::where('id',$lead_id)->update(array('status'=>$db_status));
					$res[LEAD_STATUS_CHANGE_ID] = $db_log_status;
					$action  = LEAD_STATUS_CHANGED;
					$data_string= json_encode($res);
					$this->AddleadLogs($lead_id,$user_id,$data_string,$action);
				}
            }
		}
		return Response::json($response); 
	}//End view addLeadComment function 
	
	
	public function addLeadCommentFromView(){
        $response   = array();
        $user_id    =Auth::user()->id;
        $thisData	= Input::all();
        $res=array();
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'lead_id' 	=>	 'required',
					'comment' 	=>	 'required',
				),
				array(
					"comment.required" =>trans("The comment field is required."),
				)
			);
			if ($validator->fails()) {	
				$response				=	array(
					'status' 			=> 	'error',
					'errors' 			=> 	$validator->errors(),
				);
				
			}else{
				$lead_id	=	input::get("lead_id");
				$cmnt	=	input::get("comment");
				$dealer_id				=	0;
				$comment            = new LeadComments;
				$comment->lead_id   = $lead_id;
				$comment->comments  = $cmnt;
				$comment->user_id   = $user_id;
				$comment->dealer_id = $dealer_id;
				$comment->save();
				$commentId= $comment->id;
				if(Input::hasFile('image')){
              	  $i = 1;
				  foreach (Input::file("image") as $file) {
				     $image =$file;
				     $newImageName ='-lead-comment-file';
				     $file_name  = $file->getClientOriginalName();
				     $file_size  = $this->fileSizeReadable(filesize($file));
                     $imageSrc  = $this->imageUpload($image,LEAD_COMMENT_IMAGE_ROOT_PATH,$newImageName);
                       if($imageSrc){
                         $commentAttachment             = new LeadCommentAttachments;
                         $commentAttachment->comment_id = $commentId;
                         $commentAttachment->attachment = $imageSrc;
                         $commentAttachment->file_name  = $file_name;
                         $commentAttachment->file_size  = $file_size;
                         $commentAttachment->save();
                       }
                        
                   }
                }
				 
				$res1[LEAD_COMMENT_ID] =$comment->id;
				$action1 =LEAD_COMMENTED;
				$data_string1 = json_encode($res1);
				$this->AddleadLogs($lead_id,$user_id,$data_string1,$action1);
				$response['status']  ='success';
				$response['errors']  ='';
				Session::flash("success",trans("Comment has been added Successfully"));
				
            }
		}
		return Redirect::back();
	}

    /*
     *
     * View Leads Function - Add Follow Up function 
     *
     */
	public function addLeadFollowup(){
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'lead_id' 					=>	 'required',
					'next_follow_up_date' 				=>	 'required',
					'remark' 				=>	 'required',
					),
				array(
					"remark.required"					=>	trans("The remarks field is required."),
				)
			);
			if ($validator->fails()) {	
				$response				=	array(
					'success' 			=> 	2,
					'errors' 			=> 	$validator->errors()
				);
				return Response::json($response); 
				die;;
			}else{
				 $dealer_id				=	0;

				$followUpObj 						= new LeadFollowups;
				$followUpObj->user_id 				= Auth::user()->id;
				$followUpObj->lead_id 			    = Input::get('lead_id');
				$followUpObj->next_follow_up_date   = !empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
				$followUpObj->remark 				= Input::get('remark');
				$followUpObj->dealer_id = $dealer_id;
				$followUpObj->save();
				
				$response				=	array(
					'success' 			=> 	1,
					'errors' 			=> 	'',
				);
				
				$res[FOLLOW_UP_ADDED_ID] =$followUpObj->id;
                $action      = FOLLOW_UP_ADDED;
                $user_id     = Auth::user()->id;
                $data_string = json_encode($res);
                $lead_id     =   Input::get('lead_id');
                $this->AddleadLogs($lead_id,$user_id,$data_string,$action);
				
				Session::flash('flash_notice',trans("Follow up added successfully.")); 
				return Response::json($response); 
				die;
			}
		}
	}//End view addLeadComment function
    
    /*
     *
     * Function for Change Lead Status
     * -Work in progress
     * -Converted
     *
     */
	public function changeLeadStatus($id='',$status=''){
		$leadsDetail			=	Leads::find($id); 
		if(empty($leadsDetail)) {
			return Redirect::back();
		}
		if($id){	
		  $Model =Leads::where('id',$id)->update(array('status'=>$status));
		  Session::flash('flash_notice',trans( $this->model." has updated successfully.")); 
		  
		  $res[LEAD_STATUS_CHANGE_ID] = $status;
          $action  = LEAD_STATUS_CHANGED;
          $user_id = Auth::user()->id;
          $data_string= json_encode($res);
          $this->AddleadLogs($id,$user_id,$data_string,$action);
		}
        return Redirect::back();
	}//end changeLeadStatus
    

    /*
     *
     * Function for Delete Lead files
     * -Work in progress
     * -Converted
     *
     */
    public function deleteLeadFile(){
        $id = Input::get('id');
		$image = LeadAttachments::where('id', $id)->pluck('attachment');
        @unlink(LEAD_IMAGE_ROOT_PATH . $image);
		DB::table('lead_attachments')->where('id', '=', $id)->delete();
		die;
    }//end deleteLeadFile



    public function exportLead(){
       $DB 		            = 	Leads::query();
        if(Auth::user()->user_role_id==ADMIN_STAFF_ROLE_ID){
          $sales_id =Auth::user()->id;
          $DB = $DB->where('leads.sales_person_assigned',$sales_id);
		}
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
		if ((Input::get())) {
			$searchData			=	Input::get();
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
			foreach($searchData as $fieldName => $fieldValue){
				   if($fieldValue != ""){
				   	  if($fieldName=='first_name'){
				   	    $DB->where('first_name','like','%'.$fieldValue.'%')->orWhere('last_name','like','%'.$fieldValue.'%');	
				   	  }else{
				   	  	$DB->where("$fieldName",'like','%'.$fieldValue.'%');
				   	  }
					  
				   }
				   $searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			
			
		}
		
		$dealer_id				=	0;
         
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB
									->where('leads.is_deleted',0)
									->where('leads.dealer_id',$dealer_id)
									->select("leads.*",
									DB::raw("(SELECT full_name FROM users WHERE id = leads.sales_person_assigned) as sales_person_assigned"),
									DB::raw("(SELECT full_name FROM users WHERE id = leads.team_ticket_assigned) as team_ticket_assigned"),
									DB::raw("(SELECT name FROM states WHERE id = leads.state) as state"),
									DB::raw("(SELECT name FROM cities WHERE id = leads.city) as city"))
									->orderBy($sortBy, $order)
									->get()->toArray();
									
	 return view('admin.'.$this->model.'.export_lead',compact('result' ,'searchVariable','sortBy','order','query_string'));
    }
	 /*
	 * Function of show add leads page
	 */
	public function importLeads(){
      return view('admin.'.$this->model.'.importLead');
	}
	public function saveImportLeads(){
		$formData				=	Input::all(); 
		$validator 					=	Validator::make(
			Input::all(),
			array(
				'imported_file' 	=> 'required',
			)
		);	
		if ($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
			
			$extension 				=	Input::file('imported_file')->getClientOriginalExtension();
			if($extension != 'csv'){
				Session::flash('error',trans("Invalid filetype. Please upload text/csv document only."));
				return Redirect::back();
			}
			
			$fileName = $_FILES["imported_file"]["tmp_name"];
			if ($_FILES["imported_file"]["size"] > 0) {
				$file = fopen($fileName, "r");
				$column = fgetcsv($file, 10000, ",");
				
				$dataArray = array();
				$emails = array();
				$error = 0;
				
				while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
					if(count($column) > 1){
						$subarray  = array();
						$subarray['name']  		= '';
						$subarray['contact_number']  	= '';
						$subarray['city']  	= '';
						$subarray['state']  			= '';
						$subarray['email']  			= '';
						$subarray['status']  			= '';
						$subarray['remarks']  			= '';
						$subarray['zsm-asm']  			= '';
						$subarray['old_id']  			= '';
						
						
						$num = 0;
						for($num =0;$num < 8; $num++){
							$column[$num] = trim($column[$num]);
							if($num == 0){	
								$subarray['name'] = $column[$num];
							}
							if($num == 1){
								if(!empty($column[$num])){ 	
									$isAlreadyNumber = DB::table('leads')->where('phone_number',$column[$num])->select('id')->first();
									if($isAlreadyNumber){
										$subarray['old_id'] = $isAlreadyNumber->id;
									}
								}
								$subarray['contact_number'] = $column[$num];
							}
							if($num == 2){	
								$cityid = DB::table('cities')->where("name","LIKE","%".$column[$num]."%")->select('id')->first();
								if($cityid){
									$subarray['city'] = $cityid->id;
								}else{
									$subarray['city'] = 0;
								}
								
							}
							if($num == 3){	
								$stateid = DB::table('states')->where("name","LIKE","%".$column[$num]."%")->select('id')->first();
								if($stateid){
									$subarray['state'] = $stateid->id;
								}else{
									$subarray['state'] = 0;
								}
								
							}
							if($num == 4){	
								if(!empty($column[$num])){
									$isAlreadyEmail = DB::table('leads')->where('email',$column[$num])->select('id')->first();
									if($isAlreadyEmail){
										$subarray['old_id'] = $isAlreadyEmail->id;
									}
								}
								$subarray['email'] = $column[$num];
							}
							if($num == 5){	
								$subarray['status'] = $column[$num];
							}
							if($num == 6){	
								$subarray['remarks'] = $column[$num];
							}
							if($num == 7){	
								$userid = DB::table('users')->where("full_name","LIKE","%".$column[$num]."%")->select('id')->first();
								if($userid){
									$subarray['zsm-asm'] = $userid->id; 
								}else{
									$subarray['zsm-asm'] = 0;
								}
							}
							
						}
						
						$dataArray[] = $subarray;
					}
				}
				//echo '<pre>'; print_r($dataArray); die;
				if(!empty($dataArray)){
					foreach($dataArray as $data ){
						/* New leads add*/
							if(isset($data['old_id']) && !empty($data['old_id'])){
								$leads             		 		= 	Leads::find($data['old_id']);;
							}else{
								$leads             		 		= 	New Leads;
							}
							
							$leads->first_name  	 		= 	(!empty($data['name'])) ? $data['name'] : "";
							$leads->dealer_id        		= 	0;
							$leads->user_id          		= 	ADMIN_ID;
							$leads->phone_number     		= 	(!empty($data['contact_number'])) ? $data['contact_number'] : "";
							$leads->email            		= 	(!empty($data['email'])) ? $data['email'] : "";
							$leads->state           		= 	(!empty($data['state'])) ? $data['state'] : "";
							$leads->city             		= 	(!empty($data['city'])) ? $data['city'] : "";
							$leads->sales_person_assigned 	= 	(!empty($data['zsm-asm'])) ? $data['zsm-asm'] : "";
							$leads->comments  				= 	(!empty($data['remarks'])) ? $data['remarks'] : "";
							$leads->status					=	(!empty($data['status'])) ? $data['status'] : "";;
							$leads->save();
							$id  = $leads->id;
							if(empty($data['old_id'])){
								if(!empty($id)){
									$leadNum = 10000+$id;
									//$leadNum = '#'.rand(0000000,9999999).$id;
									 Leads::where('id',$id)->update(array('lead_num'=>'#'.$leadNum));
								}
							}
						
						/* Create site based on customer data end */
					}
				}
				Session::flash('flash_notice', trans("Leads imported successfully."));
				return Redirect::to('adminpnlx/leads-management');
				
			}
		}
		
	}
}//End Class

