<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Language;
use App\Models\NotificationTemplate;
use App\Models\NotificationAction;
use App\Models\NotificationTemplateDescription;

class NotificationTemplateController extends Controller
{
    public $model                =    'notification-templates';
    public function __construct(Request $request){
        parent::__construct();
        $this->request = $request;
        View()->share('model', $this->model);
    }

    public function index(Request $request)
    {
        $DB                =    NotificationTemplateDescription::query();
        $DB->join('notification_templates','notification_template_descriptions.parent_id','notification_templates.id');
        $DB->select(
            'notification_templates.id',
            'notification_templates.action',
            'notification_templates.created_at',
            'notification_templates.updated_at',
            'notification_templates.system_notification_enable',
            'notification_templates.whatsapp_notification_enable',
            'notification_template_descriptions.name',
            'notification_template_descriptions.body',
            'notification_template_descriptions.subject',
            'notification_template_descriptions.language_id',
        );
        $searchVariable    =    array();
        $inputGet        =    $request->all();
        if ($request->all()) {
            $searchData    =    $request->all();
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
            foreach ($searchData as $fieldName => $fieldValue) {
                if (!empty($fieldValue)) {
                    if ( $fieldName == "name") {
                        $DB->where("notification_template_descriptions.$fieldName", 'like', '%' . $fieldValue . '%');
                    }
                    $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
                }
            }
        }
        $DB->where("language_id", getAppLocaleId());
        

        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'notification_templates.id';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page   =   ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($request->all())->render();
        return  View("admin.$this->model.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }

    public function create()
    {
        $Action_options    =    NotificationAction::get();
        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        return  View("admin.$this->model.add", compact('Action_options', 'languages', 'language_code'));
    }

    public function store(Request $request)
    {
        $thisData                    =    $request->all();
        $default_language            =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
        $language_code                 =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $dafaultLanguageArray        =    $thisData['data'][$language_code];
        $validator = Validator::make(
            array(
                'name'         =>  $dafaultLanguageArray['name'],
                'subject'       =>  $dafaultLanguageArray['subject'],
                'action'       =>  $dafaultLanguageArray['action'],
                'body'       =>  $dafaultLanguageArray['body'],
            ),
            array(
                'name'         => 'required',
                'subject'         => 'required',
                'action'         => 'required',
                'body'         => 'required',
            )
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $obj                                    = new NotificationTemplate;
            $obj->name                              = $dafaultLanguageArray['name'];
            $obj->subject                           = $dafaultLanguageArray['subject'];
            $obj->body                              = $dafaultLanguageArray['body'];
            $obj->action                            = $dafaultLanguageArray['action'];
            $objSave                                = $obj->save();
            $last_id                                =    $obj->id;
            foreach ($thisData['data'] as $language_id => $value) {
                $NotificationTemplateDescription_obj                    =  new NotificationTemplateDescription();
                $NotificationTemplateDescription_obj->language_id       =    $language_id;
                $NotificationTemplateDescription_obj->parent_id         =    $last_id;
                $NotificationTemplateDescription_obj->name              =    $value['name'];
                $NotificationTemplateDescription_obj->subject           =    $value['subject'];
                $NotificationTemplateDescription_obj->body              =    $value['body'];
                $NotificationTemplateDescription_obj->save();
                Session()->flash('flash_notice', trans("Notification template added successfully"));
                return redirect()->route($this->model.'.index');
            }
        }
    }

    public function edit($enmaiman)
    {
        $Id = '';
        if (!empty($enmaiman)) {
            $Id = base64_decode($enmaiman);
            $notificationTemplate               =    NotificationTemplate::find($Id);
            $NotificationTemplateDescription    =    NotificationTemplateDescription::where('parent_id', '=',  $Id)->get();
            $multiLanguage              =    array();
            if (!empty($NotificationTemplateDescription)) {
                foreach ($NotificationTemplateDescription as $description) {
                    $multiLanguage[$description->language_id]['name']            =    $description->name;
                    $multiLanguage[$description->language_id]['subject']        =    $description->subject;
                    $multiLanguage[$description->language_id]['body']            =    $description->body;
                }
            }
            $options =  NotificationAction::where('action', $notificationTemplate->action)->value('options');
            $optionsvalue = explode(',', $options);
            $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $languages = Language::where('is_active', 1)->get();
            return  View("admin.$this->model.edit", compact('languages', 'language_code', 'multiLanguage', 'notificationTemplate', 'optionsvalue'));
        }else{
            return Redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request, $enmaiman)
    {
        $Id = '';
        if (!empty($enmaiman)) {
            $Id = base64_decode($enmaiman);
        }else{
            return Redirect()->route($this->model . ".index");
        }
        $thisData                    =    $request->all();     
        $default_language            =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
        $language_code                 =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $dafaultLanguageArray        =    $thisData['data'][$language_code];
        $validator = Validator::make(
            array(
                'name'         =>  $dafaultLanguageArray['name'],
                'subject'       =>  $dafaultLanguageArray['subject'],
                'body'       =>  $dafaultLanguageArray['body'],
            ),
            array(
                'name'         => 'required',
                'subject'         => 'required',
                'body'         => 'required',
            )
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $model                    = NotificationTemplate::find($Id);
            $obj                      = $model;
            $obj->name                = $dafaultLanguageArray['name'];
            $obj->subject             = $dafaultLanguageArray['subject'];
            $obj->body                = $dafaultLanguageArray['body'];
            $obj->save();
            $last_id            =    $obj->id;
            NotificationTemplateDescription::where('parent_id', '=', $last_id)->delete();
            if (!empty($thisData)) {
                foreach ($thisData['data'] as $language_id => $value) {
                    $NotificationTemplateDescription_obj                       =  new NotificationTemplateDescription();
                    $NotificationTemplateDescription_obj->language_id          =    $language_id;
                    $NotificationTemplateDescription_obj->parent_id            =    $last_id;
                    $NotificationTemplateDescription_obj->name                 =    $value['name'];
                    $NotificationTemplateDescription_obj->subject              =    $value['subject'];
                    $NotificationTemplateDescription_obj->body                 =    $value['body'];
                    $NotificationTemplateDescription_obj->save();
                }
                Session()->flash('flash_notice', ucfirst(trans("messages.admin_Notification_Template_updated_successfully")));
                return Redirect()->route("$this->model.index");
            }
        }
    }

    public function getConstant(Request $request){
        if ($request->all()) {
            $constantName     =     $request->input('constant');
            $options        =     NotificationAction::where('action', '=', $constantName)->pluck('options', 'action');
            $a                 =     explode(',', $options[$constantName]);
            return json_encode($a);
        }
        exit;
    }

    public function notificationEnable(Request $request){
        $notificationTemplate = NotificationTemplate::find($request->checkedId);

        if($request->checkedType == 'system'){
             $notificationTemplate->system_notification_enable  = $request->checkedValue;
        }else if($request->checkedType == 'whatsapp'){
            $notificationTemplate->whatsapp_notification_enable = $request->checkedValue;
        }
        if($notificationTemplate->save()){
            return response()->json(['status' => 'success', 'msg' => "change successfully."]);
        } else{
            return response()->json(['status' => 'error', 'msg' => "Something went wrong."]);
        }
    }
}