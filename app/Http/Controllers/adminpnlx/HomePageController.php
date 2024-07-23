<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;


use App\Models\Category;
use App\Models\CategoriesDescription;
use App\Models\Language;

class HomepageController extends Controller
{
    public $model				=	'category';
    public function __construct(Request $request) {
        parent::__construct();
        View()->share('model',$this->model);
        $this->request = $request;
    }
    public function index(Request $request)
    {
        $DB					=	Category::query();
		$searchVariable		=	array();
		$inputGet			=	$request->all();
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
			if((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))){
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$DB->whereBetween('categories.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]);
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('categories.created_at','>=' ,[$dateS." 00:00:00"]);
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('categories.created_at','<=' ,[$dateE." 00:00:00"]);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "name"){
						$DB->where("categories.name",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("categories.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->where("is_deleted",0);
		$DB->select("categories.*");
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	    =	($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		return  View("admin.$this->model.index",compact('results','searchVariable','sortBy','order','query_string'));
	}

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
                'name'                  => $dafaultLanguageArray['name'],
            ),
            array(
                'name'                  => 'required',
            ),
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }  else{

            $obj 							= new Category;
			$obj->name   	            	= $dafaultLanguageArray['name'];

            if($request->hasFile('image')){
                $extension 				=	$request->file('image')->getClientOriginalExtension();
                $original_image_name 	=	$request->file('image')->getClientOriginalName();
                $fileName				=	time().'-image.'.$extension;

                $folderName     		= 	strtoupper(date('M'). date('Y'))."/";
                $folderPath				=	Config('constants.CATEGORY_IMAGE_PATH').$folderName;
                if(!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, $mode = 0777,true);
                }
                if($request->file('image')->move($folderPath, $fileName)){
                    $obj->image					=	$folderName.$fileName;

                }
            }


            $objSave				        = $obj->save();
            if(!$objSave) {
				Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
				return Redirect()->route($this->model.".index");
			}
            $last_id			=	$obj->id;
			foreach ($thisData['data'] as $language_id => $value) {
				$CategoryDescription_obj				=   new CategoriesDescription();
				$CategoryDescription_obj->language_id	=	$language_id;
				$CategoryDescription_obj->parent_id		=	$last_id;
				$CategoryDescription_obj->name			=	$value['name'];
				$CategoryDescription_obj->save();
			}
			Session()->flash('success',Config('constants.CATEGORY.CATEGORY_TITLE'). " has been added successfully");
			return Redirect()->route($this->model.".index");
        }

    }

    public function edit($encatid)
    {
          $cat_id   = '';
          $image    = "";
          $multiLanguage		 	=	array();
        if (!empty($encatid)) {
            $cat_id = base64_decode($encatid);
            $catDetails             =   Category::find($cat_id);
            $CategoryDescription	=	CategoriesDescription::where('parent_id',$cat_id)->get();
            if(!empty($CategoryDescription)){
                foreach($CategoryDescription as $description) {
                    $multiLanguage[$description->language_id]['name'] = $description->name;
                }
            }
            $languages = Language::where('is_active', 1)->get();
            $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            return  View("admin.$this->model.edit",array('languages' => $languages,'language_code' => $language_code,'catDetails' => $catDetails,'multiLanguage' => $multiLanguage));
        } else {
            return redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request, $encatid)
    {
        $cat_id = '';
        $image ="";
        $multiLanguage		 	=	array();
      if (!empty($encatid)) {
          $cat_id = base64_decode($encatid);
          $thisData                     =    $request->all();
          $default_language             =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
          $language_code                =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
          $dafaultLanguageArray         =    $thisData['data'][$language_code];
          $validator = Validator::make(
              array(
                  'name'      =>  $dafaultLanguageArray['name'],
              ),
              array(
                  'name'      => 'required',
              ),
          );
          if ($validator->fails()) {
              return redirect()->back()->withErrors($validator)->withInput();
          }else{

            $image = $request->old_image;
            if ($request->hasFile('image')) {
                $dir = Config('constants.CATEGORY_IMAGE');
                if (file_exists(base_path($dir . $image))) {
                    File::delete(base_path($dir . $image));
                }
                $image      = time() . '.' . $request->image->getClientOriginalExtension();
                $uploadImg  = $request->file('image')->move($dir, $image);
                $obj->image = $image ;
            }
            $obj        =   Category::find($cat_id);
            $obj->name 	=   $dafaultLanguageArray['name'];
            $objSave	=   $obj->save();

            if(!$objSave) {
				Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
				return Redirect()->route($this->model.".index");
			}
            $last_id =	$obj->id;
            CategoriesDescription::where('parent_id', $last_id)->delete();
			foreach ($thisData['data'] as $language_id => $value) {
				$CategoryDescription_obj			    =  new CategoriesDescription();
				$CategoryDescription_obj->language_id	=	$language_id;
				$CategoryDescription_obj->parent_id		=	$last_id;
				$CategoryDescription_obj->name			=	$value['name'];
				$CategoryDescription_obj->save();
			}
			Session()->flash('success',Config('constants.CATEGORY.CATEGORY_TITLE'). " has been updated successfully");
			return Redirect()->route($this->model.".index");
          }

        } else {
            return redirect()->route($this->model . ".index");
        }
    }

    public function destroy($encatid)
    {
        $cat_id = '';
        if (!empty($encatid)) {
            $cat_id = base64_decode($encatid);
        } else {
            return Redirect()->route($this->model . ".index");
        }
        Category::where('id', $cat_id)->update(array('is_deleted' => 1));

        Session()->flash('flash_notice', trans(Config('constants.CATEGORY.CATEGORY_TITLE') . " has been removed successfully"));
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0){
        if ($status == 1) {
            $statusMessage   =   trans(Config('constants.CATEGORY.CATEGORY_TITLE') . " has been deactivated successfully");
        } else {
            $statusMessage   =   trans(Config('constants.CATEGORY.CATEGORY_TITLE') . " has been activated successfully");
        }
        $user = Category::find($modelId);
        if ($user) {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $user->is_active = $NewStatus;
            $ResponseStatus = $user->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }


}
