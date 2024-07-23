<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use App\Models\OurServices;
use App\Models\OurServicesDescription;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class OurServicesController extends Controller
{
    public $model =    'our-services';

    public function __construct(Request $request)
    {
        parent::__construct();
        View()->share('model', $this->model);
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $DB                    =    OurServices::query();
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
                $dateS = date("Y-m-d",strtotime($searchData['date_from']));
                $dateE =  date("Y-m-d",strtotime($searchData['date_to']));
                $DB->whereBetween('created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('created_at', '<=', [$dateE . " 00:00:00"]);
            }

            
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "title") {
                        $DB->where("title", 'like', '%' . $fieldValue . '%');
                    }
                  
                    if ($fieldName == "is_active") {
                        $DB->where("is_active", $fieldValue);
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->where("is_deleted", 0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page        =    ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        return View("admin.$this->model.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        return View("admin.$this->model.add", compact('languages', 'language_code'));
    }


    public function store(Request $request)
    {

            $image = "";
            $thisData                       =    $request->all();
            $default_language               =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code                  =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray           =    $thisData['data'][$language_code];
        
        $validator = Validator::make(
            

            array(
                'image'                     => $request->file('image'),
                'title'                         => $dafaultLanguageArray['title'],
                'description'                   => $dafaultLanguageArray['description'],
                'button_link'                   => $dafaultLanguageArray['button_link'],
                'button_text'                   => $dafaultLanguageArray['button_text'],
            ),
            array(
                'image'                     => 'required',
                'title'                     => 'required',
                'description'               => 'required',
                'button_link'               => 'required',
                'button_text'               => 'required',


            ),
           
            array(
                "image.required"       => trans("messages.This field is required"),
                "title.required"       => trans("messages.This field is required"),
                "description.required" => trans("messages.This field is required"),
                "button_link.required" => trans("messages.This field is required"),
                "button_text.required" => trans("messages.This field is required"),

            )

        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }  else{

            $obj = new OurServices;
            $obj->title = $dafaultLanguageArray['title'];
            $obj->button_text = $dafaultLanguageArray['button_text'];
            $obj->button_link = $dafaultLanguageArray['button_link'];
            $obj->description = $dafaultLanguageArray['description'];

               
            if ($request->hasFile('image')) {
                $folderName = strtoupper(date('M') . date('Y')) . "/image/";
                $folderPath = Config('constants.OURSERVICE_ROOT_PATH') . $folderName;
                $image = $folderName . time() . '.' . $request->image->getClientOriginalExtension();
                $uploadImg = $request->file('image')->move($folderPath, $image);
                $obj->image = $image;
            }


            
            $obj->save();
            $pid = $obj->id;

            foreach ($thisData['data'] as $langauage_id => $value) {

                $study = new OurServicesDescription;
                $study->parent_id = $pid;
                $study->language_id = $langauage_id;
                $study->title = $value['title'];
                $study->button_text = $value['button_text'];
                $study->button_link = $value['button_link'];
                $study->description = $value['description'];
                $study->save();
            }

            Session()->flash('success', ucfirst(trans("messages.admin_Our_Service_has_been_added_successfully")));
            return Redirect()->route($this->model . ".index");
        }
    }

    public function edit($id)
    {
        $multiLanguage    =    array();
        if (!empty($id)) {
            $cat_id = base64_decode($id);

            $catDetails             =   OurServices::find($cat_id);
            $CategoryDescription    =   OurServicesDescription::where('parent_id', $cat_id)->get();
  
            if (!empty($CategoryDescription)) {
                foreach ($CategoryDescription as $description) {
                    $multiLanguage[$description->language_id]['title'] = $description->title;
                    $multiLanguage[$description->language_id]['button_text'] = $description->button_text;
                    $multiLanguage[$description->language_id]['button_link'] = $description->button_link;
                    $multiLanguage[$description->language_id]['description'] = $description->description;
                }
            }
            $languages = Language::where('is_active', 1)->get();
            $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            return view("admin.our-services.edit", array('languages' => $languages, 'language_code' => $language_code, 'catDetails' => $catDetails, 'multiLanguage' => $multiLanguage));
        } else {
            return redirect()->route("ourservices.index");
        }
    }


    public function update(Request $request, $hostPageKeyFeature)
    {
        $language_code                =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        $dafaultLanguageArray         =    $request->data[$language_code];
        $obj                =   OurServices::findOrFail(base64_decode($hostPageKeyFeature));
        $obj->title = $dafaultLanguageArray['title'];
        $obj->button_text = $dafaultLanguageArray['button_text'];
        $obj->button_link = $dafaultLanguageArray['button_link'];
        $obj->description = $dafaultLanguageArray['description'];

        if ($request->hasFile('image')) {
            $folderName = strtoupper(date('M') . date('Y')) . "/image/";
            $folderPath = Config('constants.OURSERVICE_ROOT_PATH') . $folderName;
            $image = $folderName . time() . '.' . $request->image->getClientOriginalExtension();
            $uploadImg = $request->file('image')->move($folderPath, $image);
            $obj->image = $image;
        }
        $obj->save();

        $last_id =    $obj->id;
        $description_data = array_filter($request['data'], function ($item) {
            return $item['title'] != null;
        });
        if ($description_data && count($description_data) > 0) {
            foreach ($description_data as $language_id => $value) {
                OurServicesDescription::updateOrCreate([
                    'language_id' => $language_id,
                    'parent_id'   => $last_id
                ], [
                    'title'       => $value['title'],
                    'button_text'   => $value['button_text'],
                    'button_link'   => $value['button_link'],
                    'description' => $value['description'],
                ]);
            }
        }
        Session()->flash('success', ucfirst(trans("messages.admin_Our_Service_has_been_updated_successfully")));
        return Redirect()->route("our-services.index");
    }


    public function changeStatus($id, $status)
    {
        $page = OurServices::findOrFail(base64_decode($id));
        $page->is_active = $status;
        $page->save();
        return back()->with('success',  ucfirst(trans("messages.admin_Our_Service_status_has_been_updated_successfully")));
    }


    public function destroy($id)
    {
        $page = OurServices::findOrFail(base64_decode($id));
        $page->is_deleted = true;
        $page->save();
        return back()->with('success',  ucfirst(trans("messages.admin_Our_Service_has_been_deleted_successfully")));
    }
}
