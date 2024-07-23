<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use App\Models\Cms;
use App\Models\CmsDescription;
use Illuminate\Support\Str;

class CmspagesController extends Controller
{
    public $model = 'cms-manager';
    public function __construct(Request $request){
        parent::__construct();
        View()->share('modelName', $this->model);
        $this->request = $request;
    }
    public function index(Request $request)
    {  
        $DB = Cms::query();
        $searchVariable = array();
        $inputGet = $request->all();
        if ($request->all()) {
			$searchData			=	$request->all();
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
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "page_name") {
                        $DB->where("cms.page_name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "title") {
                        $DB->where("cms.title", 'like', '%' . $fieldValue . '%');
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
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
        return View("admin.$this->model.add", compact('languages', 'language_code'));
    }

    public function store(Request $request)
    {
        $thisData                       =    $request->all();
            $default_language           =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code              =    Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray       =    $thisData['data'][$language_code];
            $validator = Validator::make(
                array(
                    'page_name'         => $request->input('page_name'),
                    'title'             => $dafaultLanguageArray['title'],
                    'body'              => $dafaultLanguageArray['body'],
                ),
                array(
                    'page_name'         => 'required',
                    'title'             => 'required',
                    'body'              => 'required',
                )
            );
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $obj = new Cms;
                $obj->page_name = $request->input('page_name');
                $obj->title     = $dafaultLanguageArray['title'];
                $obj->slug      = Str::slug($request->input('page_name'), "-"); 
                $obj->body      = $dafaultLanguageArray['body'];
                $obj->save();
                $lastId = $obj->id;
                if (!empty($thisData)) {
                    foreach ($thisData['data'] as $language_id => $value) {
                        $subObj                 = new CmsDescription();
                        $subObj->language_id    = $language_id;
                        $subObj->parent_id      = $lastId;
                        $subObj->title          = $value['title'];
                        $subObj->body           = $value['body'];
                        $subObj->save();
                    }
                }
                Session()->flash('success', ucfirst(trans("messages.admin_Cms_Page_has_been_added_successfully")));
                return Redirect()->route($this->model . ".index");
            }
    }


    public function show($encmsid= null)
    {
        $cms_id = '';
        if (!empty($encmsid)) {
            $cms_id = base64_decode($encmsid);
        }else{
            return Redirect()->route($this->model . ".index");
        }
        $CmsDetails   =  Cms::find($cms_id);
        $data = compact('CmsDetails');
        return view("admin.$this->model.view", $data);
    }

   
    public function edit($encmsid)
    {
        $cms_id = '';
        $multiLanguage =    array();
        if (!empty($encmsid)) {
            $cms_id = base64_decode($encmsid);
            $cmsDetails         =   Cms::find($cms_id);
            $cmsdescriptiondetl = CmsDescription::where('parent_id', $cms_id)->get();
            if (!empty($cmsdescriptiondetl)) {
                foreach ($cmsdescriptiondetl as $description) {
                    $multiLanguage[$description->language_id]['title']    =    $description->title;
                    $multiLanguage[$description->language_id]['body']    =    strip_tags($description->body);
                }
            }
            $languages = Language::where('is_active', 1)->get();
            $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            return View("admin.$this->model.edit", compact('multiLanguage', 'cmsdescriptiondetl', 'cmsDetails', 'languages', 'language_code'));

        }else{
            return Redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request, $encmsid)
    {
        $cms_id = '';
        $multiLanguage =    array();
        if (!empty($encmsid)) {
            $cms_id = base64_decode($encmsid);
            $thisData                    =    $request->all();
            $default_language            =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code                 =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray        =    $thisData['data'][$language_code];
            $validator = Validator::make(
                array(
                    'page_name'         => $request->input("page_name"),
                    'title'             => $dafaultLanguageArray['title'],
                    'body'                => $dafaultLanguageArray['body'],
                ),
                array(
                    'page_name'         => 'required',
                    'title'             => 'required',
                    'body'              => 'required',
                )
            );
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $obj   =   Cms::find($cms_id);
                $obj->page_name             = $request->input('page_name');
                $obj->title                 = $dafaultLanguageArray['title'];
                $obj->body                  = $dafaultLanguageArray['body'];
                $obj->save();
                $lastId                     =    $obj->id;
                CmsDescription::where("parent_id", $lastId)->delete();
                if (!empty($thisData)) {
                    foreach ($thisData['data'] as $language_id => $value) {
                        $subObj                =    new CmsDescription();
                        $subObj->language_id   =    $language_id;
                        $subObj->parent_id     =    $lastId;
                        $subObj->title         =    $value['title'];
                        $subObj->body          =    $value['body'];
                        $subObj->save();
                    }
                }
                Session()->flash('success', ucfirst(trans("messages.admin_Cms_Page_has_been_updated_successfully")));
                return Redirect()->route($this->model . ".index");
            }
        
        }else{
            return Redirect()->route($this->model . ".index");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($encmsid)
    {
        $cms_id = '';
        if (!empty($encmsid)) {
            $cms_id     = base64_decode($encmsid);
        }
        $CmsDetails     =  Cms::find($cms_id);
        
        if ($CmsDetails) {
            $CmsDetails->delete();
            CmsDescription::where("parent_id", $cms_id)->delete();
            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Cms_Page_has_been_removed_successfully")));
        }
        return back();
    }
}
