<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Language;
use App\Models\FaqCategory;
use App\Models\FaqCategoryDescription;

class FaqCategoryController extends Controller
{
    public $model = 'faq-category';
    public function __construct(Request $request){
        parent::__construct();
        View()->share('model', $this->model);
        $this->request = $request;
    }

    public function index(Request $request){
        $DB = FaqCategory::query();
        $searchVariable = array();
        $inputGet = $request->all();
        if ($request->all()) {
            $searchData = $request->all();
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
                if ($fieldValue != "") {
                    if ($fieldName == "title") {
                        $DB->where("faq_categories.title", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "description") {
                        $DB->where("faq_categories.description", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string = $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string = http_build_query($complete_string);
        $results->appends($inputGet)->render();
        return View("admin.$this->model.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }

    public function create()
    {
        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_ID');
        return View("admin.$this->model.add", compact('languages', 'language_code'));
    }

    public function store(Request $request){

        $thisData                    =    $request->all();
        $thisData = array_filter($request->data,  function ($item) {
            return $item['title'] != null;
        });
        $default_language            =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
        $language_code                 =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_ID');
        $dafaultLanguageArray        =    $thisData[$language_code];
        $validator = Validator::make(
            array(
                'title'             => $dafaultLanguageArray['title'],
            ),
            array(
                'title'             => 'required',
            ),
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $obj = new FaqCategory;
            $obj->title  = $dafaultLanguageArray['title'];
            $obj->description    = $dafaultLanguageArray['description'];
            $obj->save();
            $lastId = $obj->id;
            if (!empty($thisData)) {
                foreach ($thisData as $language_id => $value) {
                    $subObj = new FaqCategoryDescription();
                    $subObj->language_id = $language_id;
                    $subObj->parent_id = $lastId;
                    $subObj->title = $value['title'];
                    $subObj->description = $value['description'];
                    $subObj->save();
                }
            }
            Session()->flash('success', Config('constants.FAQ_CATEGORY.FAQ_CATEGORY_TITLE') . " has been added successfully");
            return Redirect()->route($this->model . ".index");
        }
    }

    public function show($encmsid){
        $cms_id = '';
        if (!empty($encmsid)) {
            $cms_id = base64_decode($encmsid);
        } else {
            return Redirect()->route($this->model . ".index");
        }
        $FaqDetails   =  FaqCategory::find($cms_id);
        $data = compact('FaqDetails');
        return view("admin.$this->model.view", $data);
    }

    public function edit($enfaqid){
        $faq_id = '';
        $multiLanguage =    array();
        if (!empty($enfaqid)) {
            $faq_id = base64_decode($enfaqid);
            $faqDetails   =   FaqCategory::find($faq_id);
            $Faq_descriptiondetl = FaqCategoryDescription::where('parent_id', $faq_id)->get();

            if (!empty($Faq_descriptiondetl)) {
                foreach ($Faq_descriptiondetl as $description) {
                    $multiLanguage[$description->language_id]['title']    =   $description->title;
                    $multiLanguage[$description->language_id]['description']    =   $description->description;
                }
            }
            $languages = Language::where('is_active', 1)->get();
            $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_ID');
            return View("admin.$this->model.edit", compact('multiLanguage', 'Faq_descriptiondetl', 'faqDetails', 'languages', 'language_code'));
        } else {
            return Redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request, $enfaqid){
       
        $faq_id = '';
        $multiLanguage =    array();
        if (!empty($enfaqid)) {
            $faq_id = base64_decode($enfaqid);
        } else {
            return Redirect()->route($this->model . ".index");
        }
        $thisData = array_filter($request->data,  function ($item) {
            return $item['title'] != null;
        });
        $default_language            =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
        $language_code                 =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_ID');
        $dafaultLanguageArray        =    $thisData[$language_code];
        $validator = Validator::make(
            array(
                'title'             => $dafaultLanguageArray['title'],
            ),
            array(
                'title'             => 'required',
            ),
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $obj   =   FaqCategory::find($faq_id);
            $obj->title = $dafaultLanguageArray['title'];
            $obj->description = $dafaultLanguageArray['description'];
            $obj->save();
            $lastId  =  $obj->id;
            FaqCategoryDescription::where("parent_id", $lastId)->delete();
            if (!empty($thisData)) {
                foreach ($thisData as $language_id => $value) {
                    $subObj                =  new FaqCategoryDescription();
                    $subObj->language_id = $language_id;
                    $subObj->parent_id = $lastId;
                    $subObj->title = $value['title'];
                    $subObj->description = $value['description'];
                    $subObj->save();
                }
            }
            Session()->flash('success', Config('constants.FAQ_CATEGORY.FAQ_CATEGORY_TITLE') .  " has been updated successfully");
            return Redirect()->route($this->model . ".index");
        }
    }

    public function destroy($enfaqid){
        $faq_id = '';
        if (!empty($enfaqid)) {
            $faq_id = base64_decode($enfaqid);
        } else {
            return Redirect()->route($this->model . ".index");
        }
        $FaqDetails   =  FaqCategory::find($faq_id)->delete();
        FaqCategoryDescription::where("parent_id", $faq_id)->delete();
        Session()->flash('flash_notice', trans(Config('constants.FAQ_CATEGORY.FAQ_CATEGORY_TITLE') . " has been removed successfully"));
        return back();
    }
}
