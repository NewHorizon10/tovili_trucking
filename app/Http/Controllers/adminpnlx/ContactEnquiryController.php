<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Config;
use App\Models\ContactEnquiry;
use App\Models\ContactEnquiryReply;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\{DB, Session, Validator, Redirect, Auth, Hash};
use Illuminate\Support\Facades\{URL, View, Response, Cookie, File, Mail, Blade, Cache, Http,App};
use Illuminate\Support\Str;

class ContactEnquiryController extends Controller
{
    public $model = 'contact-enquiry';
    public $modelName = 'contact-enquiry';
    public function __construct(Request $request)
    {
        View()->Share('model', $this->model);
        View()->Share('modelName', $this->modelName);
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $DB                    =    ContactEnquiry::query();
        $searchVariable      =   array();
        $inputGet         =   $request->all();
        if ($request->all()) {
            $searchData            =    $request->all();
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
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('contact_us_enquiry.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('contact_us_enquiry.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('contact_us_enquiry.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("contact_us_enquiry.name", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "email") {
                        $DB->where("contact_us_enquiry.email", 'like', '%' . $fieldValue . '%');
                    }

                    if ($fieldName == "phone_number") {
                        $DB->where("contact_us_enquiry.phone_number", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $data = $DB->get();
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page  =   ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        if(Session::has('contact_export_all_data')) {
			Session::forget('contact_export_all_data');
		}
        Session::put('contact_export_all_data', $data);
        return  View("admin.$this->modelName.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }



    public function show($encmsid = null)
    {
        $contest_id = '';
        if (!empty($encmsid)) {
            $modelId = base64_decode($encmsid);
        } else {
            return Redirect()->route($this->model . ".index");
        }
        $modelDetails				    =	ContactEnquiry::where('id',$modelId)->first();
		if(empty($modelDetails)) {
			return Redirect::route($this->model.".index");
        }
		$contactReplies = ContactEnquiryReply::where('contact_id',$modelId)->orderBy('id','desc')->get();
        return  View("admin.$this->modelName.view", compact('modelDetails', 'contactReplies'));
    }





    public function reply($modelId=0, Request $request){

		$language_id = $this->current_language_id();

        $result				=	ContactEnquiry::where('id',$modelId)->first();
		if(empty($result)) {
			return Redirect::route($this->modelName.".index");
		}
		$formData  =  $request->all();
		$validator = Validator::make(
            $request->all(),
           
            array(
                'message'     =>  'required',
            ),
            array(
                'message.required'   => trans("messages.This field is required"),
            )
        );
		if ($validator->fails()) {
			Session::flash('error', ucfirst(trans("messages.admin_Please_write_a_reply_message")));
            return redirect()->back()->withErrors($validator)->withInput(array("selected"=>"reply"));
        }
		
		else{
            if(!empty(Auth::guard('admin')->user())){
            $user_id            =   Auth::guard('admin')->user()->id;

            if($request->all()){
				$obj                = new ContactEnquiryReply;
                $obj->contact_id    =  $modelId;
                $obj->user_id    	=  $user_id;
				$obj->message       = $request->input('message');				
				$userId          	=  $obj->save();

                $emailActions = EmailAction::where('action', '=', 'contact_reply_to_user')->get()->toArray();
                $emailTemplates = EmailTemplate::where('action', '=', 'contact_reply_to_user')->select("name", "action", DB::raw("(select subject from email_template_descriptions where parent_id=email_templates.id AND language_id=$language_id) as subject"), DB::raw("(select body from email_template_descriptions where parent_id=email_templates.id AND language_id=$language_id) as body"))->get()->toArray();
				$cons 				=  explode(',',$emailActions[0]['options']);
				$constants 			=  array();
				foreach($cons as $key => $val){
					$constants[]    = '{'.$val.'}';
				}
				$receiver_email 	=  	$result->email;
                $receiver_full_name	=  	$result->name;
                $receiver_message   =  	$result->message;
				$replyMsg        	=  	$request->get('message');
				$subject 			=   $emailTemplates[0]['subject'];

				$rep_Array 			=    array($receiver_full_name,$receiver_email,$replyMsg);
				$messageBody		=    str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
                $settingsEmail      =  Config('Site.email');

				$this->sendMail($receiver_email, $receiver_full_name, $subject, $messageBody, $settingsEmail);
              
                if(!$userId){
                    Session::flash('error', ucfirst(trans("messages.something_went_wrong")));
                    return Redirect::route($this->model.".index");
                }
				$contactDetail				=	ContactEnquiry::where('id',$modelId)->first();
				if(empty($contactDetail)) {
					return Redirect::route($this->modelName.".index");
				}
				$contactReplies = ContactEnquiryReply::where('contact_id',$modelId)->get();
                Session::flash('success', ucfirst(trans("messages.admin_Contact_has_been_replied_successfully")));
				return redirect()->back()->withInput(array("selected"=>"reply"));
            }
		}

        }


    }



    public function destroy($endepid)
    {
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id     = base64_decode($endepid);
        }
        $depDetails     =   ContactUs::find($dep_id);
        if (empty($depDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($dep_id) {
            ContactUs::where('id', $dep_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', trans(Config('constants.DEPARTMENT.DEPARTMENT_TITLE') . " has been removed successfully"));
        }
        return back();
    }




	public function Approvestatus($modelId = 0, $status = 0){
		$result		=	ContactUs::where('id',$modelId)->first();
		if(empty($result)) {
			return Redirect::route($this->model.".index");
        }
		if($status == 'on_going'){
			$statusMessage	=	trans($this->sectionNameSingular." status has been updated to on going successfully.");
		}elseif($status == 'close'){
			$statusMessage	=	trans($this->sectionNameSingular." status has been updated to close successfully.");
		}elseif($status == 'archive'){
			$statusMessage	=	trans($this->sectionNameSingular." status has been updated to archive successfully.");
		}else{
			$statusMessage	=	trans($this->sectionNameSingular." status is not correct.");
			Session::flash('error', trans("messages.Status is not correct"));
		}
			if($status == 'on_going' || $status == 'close' || $status == 'archive'){
				$update 		=	ContactUs::where('id',$modelId)->update(['status'=>$status]);
				Session::flash('success', $statusMessage);
			}

		return Redirect::back();
	}// end changeApproveStatus()

    public function export(Request $request)
    {  

     

        $output = "";
        $output .='
        <table border="1" id="example">
        <thead>
        <th style="width:230px">Name</th>
        <th style="width:300px">Email</th>
        <th style="width:130px">Phone Number</th>
        <th style="width:300px">Subject</th>
        <th style="width:100px">Message</th>
        <th style="width:100px">Received</th>
        </thead>
        <tbody>'; 

        $customers_export_all_data = Session::get('contact_export_all_data');
        if(empty($customers_export_all_data)){
            $webisteId 	=  Auth::guard('admin')->user()->loginin_with_role_id;
  
            $table = ContactUs::where('contact_us.is_deleted',0)->get();
        }else{
            $table      = $customers_export_all_data;
        }
        foreach($table as $key=>$excel_export){
            

            $output .= '<tr style="height:100px">'.
                '<td style="text-align:center; vertical-align: middle;">'.$excel_export->name.'</td>'.
                '<td style="text-align:center; vertical-align: middle;">'.$excel_export->email.'</td>'.
                '<td style="text-align:center; vertical-align: middle;">'.$excel_export->phone.'</td>'.
                '<td style="text-align:center; vertical-align: middle;">'.$excel_export->subject.'</td>'.
                '<td style="text-align:center; vertical-align: middle;">'.$excel_export->message.'</td>'.
                '<td style="text-align:center; vertical-align: middle;">'.$excel_export->created_at.'</td>'.
            '</tr>';
        }


        $output .= '</tbody></table>';

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=contact_us.xls"); 
        header("Cache-Control: max-age=0");
        echo $output;


       
    }

    
}
