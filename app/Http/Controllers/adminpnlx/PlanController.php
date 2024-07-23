<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Config;
use App\Models\Plan;
use App\Models\TruckCompanySubscription;
use App\Models\Language;
use App\Models\PlanFeature;
use App\Models\PlanFeatureDescription;
use Carbon\Carbon;
use Redirect, Session;

class PlanController extends Controller
{
    public $model      =   'plan';
    public $sectionNameSingular      =   'plan';
    public function __construct(Request $request){
        parent::__construct();
        View()->share('model', $this->model);
        View()->share('sectionNameSingular', $this->sectionNameSingular);
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $DB                    =    Plan::query();
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
                $DB->whereBetween('plans.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('plans.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('plans.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {

                    if ($fieldName == "type") {
                        $DB->where("plans.type", 'like', '%' . $fieldValue . '%');
                    }
                   
                    if ($fieldName == "Price") {
                        $DB->where("plans.Price", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("plans.is_active", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "plan_name") {
                        $DB->where("plans.plan_name", 'like', '%' . $fieldValue . '%');
                    }
                    if($fieldName == "is_free"){
                        $DB->where('plans.is_free', 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where("plans.is_deleted", 0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'plans.created_at';
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
        return  View("admin.$this->model.add");
    }

    public function save(Request $request){
        

        if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $image = "";
            $validated = $request->validate([
                    'plan_name'           => 'required',
                    'plan_duration'       => 'required',
                    'price'               => ($request->is_free == 1 ? '' : 'required'),
                    'image'               => 'required',
                    'is_free'             => 'required',
                ],
                [
                    "plan_name.required"         => trans("messages.This field is required"),
                    "plan_duration.required"     => trans("messages.This field is required"),
                    "column_type.required"       => trans("messages.This field is required"),
                    "price.required"             => trans("messages.This field is required"),
                    "image.required"             => trans("messages.This field is required"),
                    "plan_features.required"     => trans("messages.This field is required"),
                    "is_free.required"           => trans("messages.This field is required"),

                ]
            );


            $plan                           =   new Plan;
            $plan->plan_name                =   $request->plan_name;
            $plan->type                     =   $request->input('plan_duration');
            $plan->column_type              =   1;
            $plan->price                    =   $request->input('price') ?? 0;
            $plan->plan_features            =   $request->input('plan_features') ?? '';
            $plan->is_free                  =   $request->input('is_free');

            if ($request->hasFile('image')) {
                $extension = $request->file('image')->getClientOriginalExtension();
                $fileName = time() . '-image.' . $extension;
                $folderName = strtoupper(date('M') . date('Y')) . "/";
                $folderPath = Config('constants.PLAN_IMAGE_ROOT_PATH') . $folderName;
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, $mode = 0777, true);
                }
                if ($request->file('image')->move($folderPath, $fileName)) {
                    $plan->image = $folderName . $fileName;
                }
            }
            $SavedResponse = $plan->save();
            $user_id = $plan->id;
            if (!$SavedResponse) {

                Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                return Redirect()->back()->withInput();
            } else {
                Session()->flash('success', ucfirst(trans("messages.admin_Plan_has_been_added_successfully")));
                return Redirect()->route($this->model . ".index");
            }
        }

        return  View("admin.$this->model.add");
    }

    public function edit(Request $request,  $enuserid = null)
    {


        $user_id = '';
        if (!empty($enuserid)) {
            $user_id        = base64_decode($enuserid);
            $userDetails    = Plan::find($user_id);

            return  View("admin.$this->model.edit", compact('userDetails'));
        } else {
            return redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request,  $enuserid = null)
    {
       

        if ($request->isMethod('POST')) {
            $thisData = $request->all();
            $user_id = '';
            $image = "";
            if (!empty($enuserid)) {
                $user_id = base64_decode($enuserid);
            } else {
                return redirect()->route($this->model . ".index");
            }
            $validator                    =   Validator::make(
                $request->all(),
                array(
                    'plan_name'                  => "required",
                    'plan_duration'              => "required",
                    'price'                      => ($request->is_free == 1 ? '' : 'required'),
                    'is_free'                    => 'required',

                ),
                array(
                    "plan_name.required"         => trans("messages.This field is required"),
                    "plan_duration.required"     => trans("messages.This field is required"),
                    "column_type.required"       => trans("messages.This field is required"),
                    "price.required"             => trans("messages.This field is required"),
                    "is_free.required"           => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {



                $plan                           =   Plan::where('id',$user_id)->first();
                $plan->plan_name                =   $request->plan_name;
                $plan->type                     =   $request->input('plan_duration');
                $plan->price                    =   $request->is_free == 1 ? '' : $request->input('price');
                $plan->is_free                  =   $request->input('is_free');
            if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = time() . '-image.' . $extension;
                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constants.PLAN_IMAGE_ROOT_PATH') . $folderName;
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777, true);
                    }
                    if ($request->file('image')->move($folderPath, $fileName)) {
                        $plan->image = $folderName . $fileName;
                    }
                }
                $SavedResponse = $plan->save();
                if (!$SavedResponse) {
                    Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                    return Redirect()->back()->withInput();
                }
                Session()->flash('success', ucfirst(trans("messages.admin_Plan_has_been_updated_successfully")));
                return Redirect()->route($this->model . ".index");
            }
        }
    }

    public function destroy($enuserid)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails   =   Plan::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            Plan::where('id', $user_id)->update(array(
                'is_deleted'    => 1,
            ));

            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Plan_has_been_removed_successfully")));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) 
        {
            $statusMessage   =   ucfirst(trans("messages.admin_Plan_has_been_activated_successfully"));
        } else 
        {
            $statusMessage   =   ucfirst(trans("messages.admin_Plan_has_been_deactivated_successfully"));
        }
        $user = Plan::find($modelId);
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

    public function view($enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) 
        {
            $user_id = base64_decode($enuserid);
        } else 
        {
            return redirect()->route($this->model . ".index");
        }
        $userDetails    =    Plan::where('plans.id', $user_id)->first();
        return  View("admin.$this->model.view", compact('userDetails'));
    }

    public function planFeatureIndex(Request $request, $enplanid)
    {
        $planid =  base64_decode($enplanid);
        $DB                    =    PlanFeature::query();
        $DB->join('plan_feature_descriptions', 'plan_features.id' , 'plan_feature_descriptions.parent_id');
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
                $DB->whereBetween('plan_features.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) 
            {
                $dateS = $searchData['date_from'];
                $DB->where('plan_features.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) 
            {
                $dateE = $searchData['date_to'];
                $DB->where('plan_features.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "code") {
                        $DB->where("plan_feature_descriptions.name", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->select('plan_feature_descriptions.name as mlname', 'plan_features.*');
        $DB->where('plan_features.plan_id', $planid);
        $DB->where('plan_feature_descriptions.language_id', $this->current_language_id());
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'plan_features.created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page    =    ($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        $resultcount = $results->count();
        return  View("admin.$this->model.plan_feature_index", compact('resultcount', 'results', 'searchVariable', 'sortBy', 'order', 'query_string','enplanid'));
    }
    public function planFeatureCreate($enplanid)
    {
        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        return View("admin.$this->model.plan_feature_add", compact('languages', 'language_code', 'enplanid'));
    }

    public function planFeatureSave(Request $request,$enplanId){
        $planId = base64_decode($enplanId);
        $thisData                    =    $request->all();
        $default_language            =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
        $language_code                 =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $dafaultLanguageArray        =    $thisData['data'][$language_code];
        $validator = Validator::make(
            $dafaultLanguageArray,
            array(
                'features'         => 'required',
            ),
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $obj = new PlanFeature;
            $obj->name      = $dafaultLanguageArray['features'];
            $obj->plan_id   = $planId;
            $obj->save();
            $lastId = $obj->id;
            if (!empty($thisData)) {
                foreach ($thisData['data'] as $language_id => $value) {
                    $subObj = new PlanFeatureDescription();
                    $subObj->language_id = $language_id;
                    $subObj->parent_id = $lastId;
                    $subObj->name = $value['features'];
                    $subObj->save();
                }
            }
            Session()->flash('success', ucfirst(trans("messages.admin_Plan_Feature_has_been_added_successfully")));
            return Redirect()->route($this->model . ".feature.index",array($enplanId));
        }
    }

    public function planFeatureEdit($enplanid,$enid)
    {
        
        $plan_id = '';
        $multiLanguage =    array();
        if (!empty($enplanid) && !empty($enid)) {
            $id = base64_decode($enid);
            $planFeatureDetails   =   PlanFeature::find($id);
            
            $PlanFeature_descriptiondetl = PlanFeatureDescription::where('parent_id', $planFeatureDetails->id)->get();
            
            if (!empty($PlanFeature_descriptiondetl)) {
                foreach ($PlanFeature_descriptiondetl as $description) {
                    $multiLanguage[$description->language_id]['features']    =   $description->name;
                   

                }
            }
            $languages = Language::where('is_active', 1)->get();
            $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');

            return View("admin.$this->model.plan_feature_edit", compact('multiLanguage', 'PlanFeature_descriptiondetl', 'planFeatureDetails', 'languages', 'language_code','enplanid','enid'));
        } else {
            return Redirect()->route($this->model . ".-feature.edit");
        }
        
    }

    public function planFeatureUpdate(Request $request,$enplanid,$enid)
    {
        $thisData                    =    $request->all();
        
        if (!empty($enplanid)) {
            $id = base64_decode($enid);
        } else {
            return Redirect()->route($this->model . ".feature.index",array($enplanid));
        }
        $thisData = $request->data ;
        $language_code                 =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $dafaultLanguageArray        =    $thisData[$language_code];
        $validator = Validator::make(
            $dafaultLanguageArray,
            array(
                'features'             => 'required',
            ),
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $obj   =   PlanFeature::find($id);
            $obj->name = $dafaultLanguageArray['features'];
            
            $obj->save();
            $lastId  =  $obj->id;
           
            PlanFeatureDescription::where("parent_id", $lastId)->delete();
           
            if (!empty($thisData)) {
                foreach ($thisData as $language_id => $value) {
                    $subObj                =  new PlanFeatureDescription();
                    $subObj->language_id = $language_id;
                    $subObj->parent_id = $lastId;
                    $subObj->name = $value['features'];
                    $subObj->save();
                }
            }
            Session()->flash('success', ucfirst(trans("messages.admin_Plan_Feature_has_been_updated_successfully")));
            return Redirect()->route($this->model . ".feature.index",array($enplanid));
        }
    }
    public function planFeatureDestroy($enid)
    {
        $plan_id = '';
        if (!empty($enid)) {
            $id = base64_decode($enid);
        } else {
            return Redirect()->route($this->model . "feature.index");
        }
        $PlanFeatureDetails   =  PlanFeature::find($id)->delete();
        PlanFeatureDescription::where("parent_id", $id)->delete();
        Session()->flash('flash_notice', ucfirst(trans("messages.admin_Plan_Feature_has_been_removed_successfully")));
        return back();
    }

    

}
