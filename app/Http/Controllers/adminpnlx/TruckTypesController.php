<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Config;
use App\Models\TruckType;
use App\Models\TruckTypeDescription;
use App\Models\Language;
use App\Models\TruckTypeQuestion;
use App\Models\TruckTypeQuestionDescription;
use Redirect;

class TruckTypesController extends Controller
{
    public $model      =   'truck-types';
    public $sectionNameSingular      =   'Truck Type';
    public function __construct(Request $request){
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $DB                    =    TruckType::query();
        $DB->join('truck_type_descriptions', 'truck_type_descriptions.parent_id','truck_types.id');
        $DB->select(
            'truck_types.id',
            'truck_types.is_active',
            'truck_types.created_at',
            'truck_type_descriptions.name',
        );
        $searchVariable        =    array();
        $inputGet            =    $request->all();
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
                $dateS = date("Y-m-d", strtotime($searchData['date_from']));
                $dateE =  date("Y-m-d", strtotime($searchData['date_to']));
                $DB->whereBetween('truck_types.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('truck_types.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('truck_types.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {

                    if ($fieldName == "name") {
                        $DB->where("truck_type_descriptions.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("truck_types.is_active", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where("truck_types.is_deleted", 0);
        $DB->where('truck_types.for_private_customers', 0);

        $DB->where("truck_type_descriptions.language_id", $this->current_language_id());
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'truck_types.created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page    =    ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
        return  View("admin.$this->model.index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }


    public function create(Request $request){
        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        return  View("admin.$this->model.add",compact('languages','language_code'));
    }

    public function save(Request $request){
        

        if ($request->isMethod('POST')) {
            $thisData                    =    $request->all();
            $language_code               =    Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray        =    $thisData['data'][$language_code];
            
            $validator = Validator::make(
                array(
                    'name'              => $dafaultLanguageArray['name'],
                ),
                array(
                    'name'              => 'required',
                ),
                array(
                    "name.required"             => trans("messages.This field is required"),
                )
            );

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $truck_type                  =   new TruckType;
            $truck_type->name            =   $dafaultLanguageArray['name'];
            $truck_type->multiple_stop_allow           = $request->input('multiple_stop_allow') ? 1 : 0;
            $truck_type->save();
            
            foreach ($thisData['data'] as $language_id => $value) {
                $TruckTypeDescription_obj					=  new TruckTypeDescription();
                $TruckTypeDescription_obj->language_id		=	$language_id;
                $TruckTypeDescription_obj->parent_id		=	$truck_type->id;
                $TruckTypeDescription_obj->name	 	    =	$value['name'] ?? "";
                $TruckTypeDescription_obj->save();
            }
            $SavedResponse = $truck_type->save();
            if (!$SavedResponse) {

                Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                return Redirect()->back()->withInput();
            } else {
                Session()->flash('success', ucfirst(trans("messages.admin_Truck_Type_has_been_added_successfully")));
                return Redirect()->route($this->model . ".index");
            }
        }

        return  View("admin.$this->model.add");
    }

    public function edit(Request $request,  $entruckid = null)
    {
        $truck_type_id = '';
        if (!empty($entruckid)) {
            $truck_type_id        = base64_decode($entruckid);
            $truckTypeDetails    = TruckType::find($truck_type_id);

            $TruckTypeDescription	=	TruckTypeDescription::where('parent_id',$truck_type_id)->get();
            $multiLanguage		 	=	array();
            if(!empty($TruckTypeDescription)){
                foreach($TruckTypeDescription as $description) {
                    $multiLanguage[$description->language_id]['name']=	$description->name;				
                }
            }
            $languages = Language::where('is_active', 1)->get();
            $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');


            return  View("admin.$this->model.edit", compact('truckTypeDetails','languages','language_code','multiLanguage'));
        } else {
            return redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request,  $enuserid = null)
    {
        $truck_type_id = base64_decode($enuserid);
        if ($request->isMethod('POST')) {
            $thisData                    =    $request->all();
            $default_language            =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code                 =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray        =    $thisData['data'][$language_code];
            
            $validator = Validator::make(
                array(
                    'name'              => $dafaultLanguageArray['name'],
                ),
                array(
                    'name'              => 'required',
                ),
                array(
                    "name.required"             => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }  else{
                
                $TruckTypes =  TruckType::find($truck_type_id);  
                $TruckTypes->name    						= $dafaultLanguageArray['name'];
                $TruckTypes->multiple_stop_allow           = $request->input('multiple_stop_allow') ? 1 : 0;
                $TruckTypes->save(); 
                $TruckTypesId								=	$TruckTypes->id;
                if(!$TruckTypesId){
                    Session()->flash('error', trans("messages.Something went wrong")); 
                    return Redirect()->back()->withInput();
                }
                TruckTypeDescription::where('parent_id', '=', $TruckTypesId)->delete();
                foreach ($thisData['data'] as $language_id => $value) {
                    $TruckTypeDescription_obj					=  new TruckTypeDescription();
                    $TruckTypeDescription_obj->language_id		=	$language_id;
                    $TruckTypeDescription_obj->parent_id		=	$TruckTypesId;
                    $TruckTypeDescription_obj->name	 	    =	$value['name'];
                    $TruckTypeDescription_obj->save();
                }
    
                Session()->flash('success', ucfirst(trans("messages.admin_Truck_Type_has_been_updated_successfully")));
                return Redirect()->route($this->model . ".index");
            }
        }
    }

    public function destroy($enuserid)
    {
        $truck_type_id = '';
        if (!empty($enuserid)) {
            $truck_type_id = base64_decode($enuserid);
        }
        $truckTypeDetails   =   TruckType::find($truck_type_id);
        if (empty($truckTypeDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($truck_type_id) {
            TruckType::where('id', $truck_type_id)->update(array(
                'is_deleted'    => 1,
            ));

            Session()->flash('flash_notice', ucfirst(trans("messages.admin_truck_type_has_been_removed_successfully")));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) 
        {
            $statusMessage   =   ucfirst(trans("messages.admin_Truck_Type_has_been_activated_successfully"));
        } else 
        {
            $statusMessage   =   ucfirst(trans("messages.admin_Truck_Type_has_been_deactivated_successfully"));
        }
        $user = TruckType::find($modelId);
        if ($user) 
        {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) 
            {
                $NewStatus = 1;
            } else 
            {
                $NewStatus = 0;
            }
            $user->is_active = $NewStatus;
            $ResponseStatus = $user->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

    public function truckTtypesQuestionnaireIndex(Request $request, $entruck_typeid)
    {
        $truck_typeid =  base64_decode($entruck_typeid);
        $DB                    =    TruckTypeQuestion::query();
        $DB->join('truck_type_question_descriptions', 'truck_type_questions.id' , 'truck_type_question_descriptions.parent_id');
        $searchVariable        =    array();
        $inputGet            =    $request->all();
        if ($request->all()) 
        {
            $searchData            =    $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);

            if (isset($searchData['order'])) 
            {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) 
            {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['page'])) 
            {
                unset($searchData['page']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) 
            {
                $dateS = date("Y-m-d", strtotime($searchData['date_from']));
                $dateE =  date("Y-m-d", strtotime($searchData['date_to']));
                $DB->whereBetween('truck_type_questions.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) 
            {
                $dateS = $searchData['date_from'];
                $DB->where('truck_type_questions.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) 
            {
                $dateE = $searchData['date_to'];
                $DB->where('truck_type_questions.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "code") {
                        $DB->where("truck_type_question_descriptions.name", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->select('truck_type_question_descriptions.name as mlname', 'truck_type_questions.*');
        $DB->where('truck_type_questions.truck_type_id', $truck_typeid);
        $DB->where('truck_type_question_descriptions.language_id', $this->current_language_id());
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'truck_type_questions.created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page    =    ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
        return  View("admin.$this->model.truck_types_questionnaire_index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string','entruck_typeid'));
    }
    public function truckTypesQuestionnaireCreate($entruck_typeid)
    {
        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        return View("admin.$this->model.truck_types_questionnaire_add", compact('languages', 'language_code', 'entruck_typeid'));
    }

    public function truckTypesQuestionnaireSave(Request $request,$entruck_typeId){
        $truck_typeId = base64_decode($entruck_typeId);
        $thisData                           = $request->all();
        $language_code                      = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $dafaultLanguageArray               = $thisData['data'][$language_code];
        $dafaultLanguageArray['input_type'] = $request->input_type;
        $validator = Validator::make(
            $dafaultLanguageArray,
            array(
                'input_type'                    => 'required',
                'question'                      => 'required',
                'input_description'             => (($request->input_type == 'choice' || $request->input_type == 'radio') ? 'required' : ''),
            ),
            array(
                "input_type.required"           => trans("messages.This field is required"),
                "question.required"             => trans("messages.This field is required"),
                "input_description.required"    => trans("messages.This field is required"),
            )
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $obj                        = new TruckTypeQuestion;
            $obj->name                  = $dafaultLanguageArray['question'];
            $obj->input_type	        = $dafaultLanguageArray['input_type'];
            $obj->question_descriptions  = $dafaultLanguageArray['question_descriptions'];
            $obj->input_description     = $dafaultLanguageArray['input_description'] ?? "";
            $obj->truck_type_id         = $truck_typeId;
            $obj->save();
            $lastId = $obj->id;
            if (!empty($thisData)) {
                foreach ($thisData['data'] as $language_id => $value) {
                    $subObj                         = new TruckTypeQuestionDescription();
                    $subObj->language_id            = $language_id;
                    $subObj->parent_id              = $lastId;
                    $subObj->name                   = $value['question'] ?? "";
                    $subObj->question_descriptions   = $value['question_descriptions'] ?? "";
                    $subObj->input_description      = $value['input_description'] ?? "";
                    $subObj->save();
                }
            }
            Session()->flash('success', ucfirst(trans("messages.admin_truck_type_question_has_been_added_successfully")));
            return Redirect()->route($this->model . ".questionnaire.index",array($entruck_typeId));
        }
    }

    public function truckTypesQuestionnaireEdit($entruck_typeid,$enid)
    {
        $truck_type_id = '';
        $multiLanguage =    array();
        if (!empty($entruck_typeid) && !empty($enid)) {
            $id = base64_decode($enid);
            $truck_typeFeatureDetails   =   TruckTypeQuestion::find($id);
            $TruckTypeQuestion_descriptiondetl = TruckTypeQuestionDescription::where('parent_id', $truck_typeFeatureDetails->id)->get();
            
            if (!empty($TruckTypeQuestion_descriptiondetl)) {
                foreach ($TruckTypeQuestion_descriptiondetl as $description) {
                    $multiLanguage[$description->language_id]['name']                   =   $description->name;
                    $multiLanguage[$description->language_id]['question_descriptions']  =   $description->question_descriptions;
                    $multiLanguage[$description->language_id]['input_description']      =   $description->input_description;
                }
            }
            $languages = Language::where('is_active', 1)->get();
            $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');

            return View("admin.$this->model.truck_types_questionnaire_edit", compact('multiLanguage', 'TruckTypeQuestion_descriptiondetl', 'truck_typeFeatureDetails', 'languages', 'language_code','entruck_typeid','enid'));
        } else {
            return Redirect()->route($this->model . ".-feature.edit");
        }
        
    }

    public function truckTypesQuestionnaireUpdate(Request $request,$entruck_typeid,$enid)
    {
        $thisData                    =    $request->all();
        
        if (!empty($entruck_typeid)) {
            $id = base64_decode($enid);
        } else {
            return Redirect()->route($this->model . ".questionnaire.index",array($entruck_typeid));
        }
        $thisData = $request->data ;
        $language_code                          = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $dafaultLanguageArray                   = $thisData[$language_code];
        $dafaultLanguageArray['input_type']     = $request->input_type;
        $validator = Validator::make(
            $dafaultLanguageArray,
            array(
                'input_type'                    => 'required',
                'question'                      => 'required',
                'input_description'             => (($request->input_type == 'choice' || $request->input_type == 'radio') ? 'required' : ''),
            ),
            array(
                "input_type.required"           => trans("messages.This field is required"),
                "question.required"             => trans("messages.This field is required"),
                "input_description.required"    => trans("messages.This field is required"),
            )
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $obj   =   TruckTypeQuestion::find($id);
            $obj->name                  = $dafaultLanguageArray['question'];
            $obj->question_descriptions = $dafaultLanguageArray['question_descriptions'];
            $obj->input_type	        = $dafaultLanguageArray['input_type'];
            $obj->input_description     = $dafaultLanguageArray['input_description'] ?? "";
            
            $obj->save();
            $lastId  =  $obj->id;
           
            TruckTypeQuestionDescription::where("parent_id", $lastId)->delete();
           
            if (!empty($thisData)) {
                foreach ($thisData as $language_id => $value) {
                    $subObj = new TruckTypeQuestionDescription();
                    $subObj->language_id            = $language_id;
                    $subObj->parent_id              = $lastId;
                    $subObj->name                   = $value['question'] ?? "";
                    $subObj->question_descriptions  = $value['question_descriptions'] ?? "";
                    $subObj->input_description      = $value['input_description'] ?? "";
                    $subObj->save();
                }
            }
            Session()->flash('success', ucfirst(trans("messages.admin_truck_type_question_has_been_updated_successfully")));
            return Redirect()->route($this->model . ".questionnaire.index",array($entruck_typeid));
        }
    }
    public function truckTypesQuestionnaireDestroy($enid)
    {
        $truck_type_id = '';
        if (!empty($enid)) {
            $id = base64_decode($enid);
        } else {
            return Redirect()->route($this->model . "questionnaire.index");
        }
        $TruckTypeQuestionDetails   =  TruckTypeQuestion::find($id)->delete();
        TruckTypeQuestionDescription::where("parent_id", $id)->delete();
        Session()->flash('flash_notice', ucfirst(trans("messages.admin_truck_type_question_has_been_removed_successfully")));
        return back();
    }

}
