<?php
namespace App\Http\Controllers\frontend;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\Contact;
use DatePeriod;
use DateTime;
use PDF;
use DateInterval;
use App\Models\User;
use App\Models\UserCompanyInformation;
use App\Models\Lookup;
use App\Models\UserVerificationCode;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use App\Models\UserDeviceToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Helper,Config;
use Stripe;
use Cache, Cookie, Input, Mail, mongoDate, Response, Session, URL;
use Illuminate\Validation\Rules\Password;

class HomeController extends Controller
{ 

	public function __construct(Request $request) {
		parent::__construct();
        $this->request              =   $request;
    }

	public function privacy_policy(Request $request)
	{	
		$privacy_policy = DB::table('cms')->where('slug','privacy-policy')
		->leftjoin('cms_descriptions', 'cms_descriptions.parent_id','cms.id')
		->where('cms_descriptions.language_id',getAppLocaleId())
		->select('cms_descriptions.*')
		->first();
		
		return View("frontend.privacy_policy", compact('privacy_policy'));
		
	}

	public function term_condition(Request $request)
	{	

		$term_conditions = DB::table('cms')->where('slug','terms-conditions')
		->leftjoin('cms_descriptions', 'cms_descriptions.parent_id','cms.id')
		->where('cms_descriptions.language_id',getAppLocaleId())
		->select('cms_descriptions.*')
		->first();

		return View("frontend.term_condition", compact('term_conditions'));
		
	}



	public function contactEnquiry(Request $request)
    {   
		$language_id = $this->current_language_id();
        $formData                        =    $request->all();
        if (!empty($formData)) {
            $validated = $request->validate(
				[
                'name'                  =>  'required|min:2|max:40',
                'email'                 =>  'required|email:rfc,dns|max:60|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/',
                'phone_number'          =>  'required|regex:' . Config('constants.MOBILE_VALIDATION_STRING'),
                'message'               =>  'required',
				],
				[
					'name.required'          => trans('messages.the_name_field_is_required'),
					'email.required'         => trans('messages.the_email_field_is_required'),
					'email.email'            => trans('messages.the_email_address_should_be_in_valid_format'),
					'email.regex'		     => trans('messages.the_email_address_should_be_in_valid_format'),
					'email.unique'           => trans('messages.the_email_already_has_taken'),
					'phone_number.required'  => trans('messages.the_phone_number_field_is_required'),
					"phone_number.regex" 	 => trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0"),
					'message.required'       => trans('messages.the_message_field_is_required'),
				]
		);
          
            $obj                        =  new Contact;
            $obj->name                  =  $request->input('name');
            $obj->email                 =  $request->input('email');
            $obj->phone_number          =  $request->input('phone_number');
            $obj->comments              =  $request->input('message');
            $Response                   =  $obj->save();
			/* Send Mail To Admin */
			$settingsEmail              =  Config('Site.email');

			
			$language_system_id = $this->language_system_id();
			$emailActions = EmailAction::where('action', '=', 'contact_enquiry_to_admin')->get()->toArray();
			$emailTemplates = EmailTemplate::where('action', '=', 'contact_enquiry_to_admin')->select(
				"name",
				"action",
				DB::raw("(select subject from email_template_descriptions where parent_id=email_templates.id AND language_id=$language_system_id) as subject"),
				DB::raw("(select body from email_template_descriptions where parent_id=email_templates.id AND language_id=$language_system_id) as body")
			)
			->get()
			->toArray();
			
			$cons = explode(',', $emailActions[0]['options']);
			$constants = array();
			foreach ($cons as $key => $val) {
				$constants[] = '{' . $val . '}';
			}
			$subject                    = $emailTemplates[0]['subject'];
			$email                      = Config('Site.to_email');
			$full_name                  = $request->name;
			$sendData                   = array($request->name, $request->email, $request->message);
			$messageBody                = str_replace($constants, $sendData, $emailTemplates[0]['body']);
			$this->sendMail($email, $full_name, $subject,$messageBody, $settingsEmail);
           
            if (!$Response) {
                Session()->flash(trans("messages.Something went wrong"));
                return Redirect()->back()->withInput();
            }
            Session()->flash('success', trans("Contact enquiry has been added successfully"));
            return Redirect()->back();
        }
    }


}



