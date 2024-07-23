<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;


use App\Models\HomepageSlider;
use App\Models\HomepageSliderDescriptions;
use App\Models\Language;

class HomepageSliderController extends Controller
{
    public $model				=	'homepage-slider';
    public function __construct(Request $request) {
        parent::__construct();
        View()->share('model',$this->model);
        $this->request = $request;
    }
    public function index(Request $request)
    {
        $DB					=	HomepageSlider::query();
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
				$DB->whereBetween('homepagesliders.created_at', [$dateS." 00:00:00", $dateE." 23:59:59"]);
			}elseif(!empty($searchData['date_from'])){
				$dateS = $searchData['date_from'];
				$DB->where('homepagesliders.created_at','>=' ,[$dateS." 00:00:00"]);
			}elseif(!empty($searchData['date_to'])){
				$dateE = $searchData['date_to'];
				$DB->where('homepagesliders.created_at','<=' ,[$dateE." 00:00:00"]);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == "title"){
						$DB->where("homepagesliders.title",'like','%'.$fieldValue.'%');
					}
					if($fieldName == "is_active"){
						$DB->where("homepagesliders.is_active",$fieldValue);
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$DB->select("homepagesliders.*");
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
                'title'                     => $dafaultLanguageArray['title'],
                'subtitle'                  => $dafaultLanguageArray['subtitle'],
                'description'               => $dafaultLanguageArray['description'],
                'buttontext'                => $dafaultLanguageArray['buttontext'],
                'buttonlink'                => $dafaultLanguageArray['buttonlink'],
                'image'                     => $request->file('image'),

            ),
            array(
                'title'                     => 'required',
                'subtitle'                  => 'required',
                'description'               => 'required',
                'buttontext'                => 'required',
                'buttonlink'                => 'required',
                'image'                     => 'required|mimes:png,jpg,jpeg',
  
            ),
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }  else{





            $obj 							= new HomepageSlider;
			$obj->title   	            	= $dafaultLanguageArray['title'];
            if($request->hasFile('image')){
                $extension 				=	$request->file('image')->getClientOriginalExtension();
                $original_image_name 	=	$request->file('image')->getClientOriginalName();
                $fileName				=	time().'-image.'.$extension;

                $folderName     		= 	strtoupper(date('M'). date('Y'))."/";
                $folderPath				=	Config('constants.SLIDER_IMAGE_ROOT_PATH').$folderName;
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
				$homepage_slider_obj				=   new HomepageSliderDescriptions;
				$homepage_slider_obj->language_id	=	$language_id;
				$homepage_slider_obj->parent_id		=	$last_id;
				$homepage_slider_obj->title			=	$value['title'];
				$homepage_slider_obj->subtitle		=	$value['subtitle'];
				$homepage_slider_obj->description	=	$value['description'];
				$homepage_slider_obj->buttontext	=	$value['buttontext'];
				$homepage_slider_obj->buttonlink	=	$value['buttonlink'];
				$homepage_slider_obj->save();
			}
			Session()->flash('success', ucfirst(trans("messages.admin_Home_slider_has_been_added_successfully")));
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
            $homepage_slider             =   HomepageSlider::find($cat_id);
            $HomePageSliderDescription	=	HomepageSliderDescriptions::where('parent_id',$cat_id)->get();
            if(!empty($HomePageSliderDescription)){
                foreach($HomePageSliderDescription as $description) {
                    $multiLanguage[$description->language_id]['title'] = $description->title;
                    $multiLanguage[$description->language_id]['subtitle'] = $description->subtitle	;
                    $multiLanguage[$description->language_id]['buttontext'] = $description->buttontext;
                    $multiLanguage[$description->language_id]['buttonlink'] = $description->buttonlink;
                    $multiLanguage[$description->language_id]['description'] = $description->description;
                }
            }
            $languages = Language::where('is_active', 1)->get();
            $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            return  View("admin.$this->model.edit",array('languages' => $languages,'language_code' => $language_code,'homepage_slider' => $homepage_slider,'multiLanguage' => $multiLanguage));
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
                  'title'      =>  $dafaultLanguageArray['title'],
              ),
              array(
                  'title'      => 'required',
                  'image'      => 'nullable|mimes:png, jpg, jpeg',
              ),
          );
          if ($validator->fails()) {
              return redirect()->back()->withErrors($validator)->withInput();
          }else{
            $obj            =   HomepageSlider::find($cat_id);
            $obj->title 	=   $dafaultLanguageArray['title'];
            if($request->hasFile('image')){
                $extension 				=	$request->file('image')->getClientOriginalExtension();
                $original_image_name 	=	$request->file('image')->getClientOriginalName();
                $fileName				=	time().'-image.'.$extension;

                $folderName     		= 	strtoupper(date('M'). date('Y'))."/";
                $folderPath				=	Config('constants.SLIDER_IMAGE_ROOT_PATH').$folderName;
                if(!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, $mode = 0777,true);
                }
                if($request->file('image')->move($folderPath, $fileName)){
                    $obj->image					=	$folderName.$fileName;

                }
            }
            $objSave	=   $obj->save();

            if(!$objSave) {
				Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
				return Redirect()->route($this->model.".index");
			}
            $last_id =	$obj->id;
            HomepageSliderDescriptions::where('parent_id', $last_id)->delete();
			foreach ($thisData['data'] as $language_id => $value) {
				$Home_Paage_Slider_Description_obj			            =   new HomepageSliderDescriptions;
				$Home_Paage_Slider_Description_obj->language_id  	    =	$language_id;
				$Home_Paage_Slider_Description_obj->parent_id		    =	$last_id;
				$Home_Paage_Slider_Description_obj->title			    =	$value['title'];
                $Home_Paage_Slider_Description_obj->subtitle			=	$value['subtitle'];
                $Home_Paage_Slider_Description_obj->buttontext			=	$value['buttontext'];
                $Home_Paage_Slider_Description_obj->buttonlink			=	$value['buttonlink'];
                $Home_Paage_Slider_Description_obj->description			=	$value['description'];
				$Home_Paage_Slider_Description_obj->save();
			}
			Session()->flash('success', ucfirst(trans("messages.admin_Home_slider_has_been_updated_successfully")));
			return Redirect()->route($this->model.".index");
          }

        } else {
            return redirect()->route($this->model . ".index");
        }
    }

    public function destroy($encatid)
    {
        $homepage_slider_id = '';
        if (!empty($encatid)) {
            $homepage_slider_id = base64_decode($encatid);
        } else {
            return Redirect()->route($this->model . ".index");
        }
        HomepageSlider::where('id', $homepage_slider_id)->delete();
        HomepageSliderDescriptions::where('parent_id', $homepage_slider_id)->delete();
        Session()->flash('flash_notice', ucfirst(trans("messages.admin_Home_Slider_has_been_removed_successfully")));
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0){
        if ($status == 1) {
            $statusMessage   =   ucfirst(trans("messages.admin_Homepage_Slider_has_been_deactivated_successfully"));
        } else {
            $statusMessage   =   ucfirst(trans("messages.admin_Homepage_Slider_has_been_activated_successfully"));
        }
        $user = HomepageSlider::find($modelId);
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
