<?php
namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\BaseController;
use App\Model\TextSetting;
use App\Model\User;
use App\Model\Setting;
use App\Model\Language;
use App\Model\TextDescription;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth, Blade, Config, Cache, Cookie, DB, File, Hash, Input, Mail, mongoDate, Redirect, Request, Response, Session, URL, View, Validator;
/**
* text Settings Controller
*
* Add your methods in the class below
*
* This file will render views from views/textsetting
*/
	class TextSettingController extends BaseController{
/**
* function for list all settings
*
* @param  null
* 
* @return view page
*/
    public function textList(){
        $DB 				= 	TextSetting::query();
        $searchVariable 	= 	array();
        $inputGet       	= 	Input::get();
        if ((Input::get() && isset($inputGet['display'])) || isset($inputGet['page'])) {
            $searchData 	= 	Input::get();
            unset($searchData['display']);
            unset($searchData['_token']);
            if (isset($searchData['page'])) {
                unset($searchData['page']);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if (!empty($fieldValue)) {
                    if ($fieldName == 'module') {
                        $DB->where("key_value", 'like', '%' . $fieldValue . '%');
                    } else {
                        $DB->where("$fieldName", 'like', '%' . $fieldValue . '%');
                    }
                    $searchVariable = array_merge($searchVariable, array(
                        $fieldName => $fieldValue
                    ));
                }
            }
        }
		$sortBy 			= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
        $order  			= 	(Input::get('order')) ? Input::get('order') : 'DESC';
        $result        		= 	$DB->where('js_constant_type',0)->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
        $languageArray 		= 	language::where('is_active', '=', '1')->pluck('title', 'id')->toArray();
        return View::make('admin.textsetting.index', compact('sortBy', 'order', 'result', 'searchVariable', 'languageArray'));
    } // end listSetting()
/**
* function for display add text page  
*
* @param  null
* 
* @return view page
*/
    public function addText(){
        $languages 			= 	Language::where('is_active', '=', '1')->get(array(
            'title',
            'id'
        ));
        $default_language 	= 	Config::get('default_language');
		$language_code    	= 	$default_language['language_code'];
        return View::make('admin.textsetting.add', compact('languages', 'language_code', 'type'));
    } // end addText()
/**
* function for save added text
*
* @param  null
* 
* @return view page
*/
    public function saveText(){
		Input::replace($this->arrayStripTags(Input::all()));
        $thisData 				= 	Input::all();
        $default_language   	= 	Config::get('default_language');
        $language_code      	= 	$default_language['language_code'];
        $dafaultLanguageArray 	= 	$thisData['data'][$language_code];
        if (!empty($thisData)) {
            $validator 			= 	Validator::make(array(
                'key' 			=> 	Input::get('key'),
                'value' 		=> 	$dafaultLanguageArray
            ), array(
                'key' 			=> 	'required',
                'value' 		=> 	'required'
            ));
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $keyValue = Input::get('key');
                foreach ($thisData['data'] as $key => $val) {
                    if ($val) {
                        $obj              = new TextSetting;
                        $obj->key_value   = trim($keyValue);
                        $obj->language_id = $key;
                        $obj->value       = $val;
                        $obj->save();
                    }
                }
                $this->settingFileWrite();
            }
            Session::flash('flash_notice', trans("messages.settings.app_setting_has_been_saved_successfully"));
            return Redirect::to('text-setting');
        }
    } //end saveText()
/**
* function for display edit text page 
*
* @param  $Id as text id 
* 
* @return view page
*/
    public function editText($Id = 0){
        $result 			= 	TextSetting::where('id', $Id)->first();
        $result 			= 	TextSetting::find($Id);
		if(empty($result)) {
			return Redirect::to('adminpnlx/dashboard');
		}
        return View::make('admin.textsetting.edit', compact( 'result'));
    } //end editText()
/**
* function for update text
*
* @param $Id as text id
* 
* @return view page
*/
    public function updateText($Id = 0){
		Input::replace($this->arrayStripTags(Input::all()));
        $thisData 			= 	Input::all();
		if (!empty($thisData)) {
            $validator 		= 	Validator::make(array(
                'value' 	=> 	Input::get('value')
            ), array(
                'value' 	=> 'required'
            ));
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $obj        = TextSetting::find($Id);
                $obj->value = trim(Input::get('value'));
                $obj->save();
                $this->settingFileWrite();
                Session::flash('flash_notice', trans("messages.settings.text_setting_has_been_saved_successfully"));
                return Redirect::to('text-setting');
            }
        }
    } //end updateText()
/**
* function for delete text
*
* @param $Id as text id
* 
* @return view page
*/
    public function deleteText($Id = 0){
        if ($Id) {
           /*  $result 		= 	TextSetting::where('id', $Id)->delete(); */
		   $this->_delete_table_entry('textsettings',$Id,'id');
        }
        Session::flash('flash_notice', trans("messages.settings.setting_has_been_deleted"));
        return Redirect::to('text-setting');
    } //end deleteText()
/**
* Function for write file on create and update text  or message 
*
* @param null
*
* @return void. 
*/
    public function settingFileWrite(){
        $DB   		= 	TextSetting::query();
        $list 		= 	$DB->get()->toArray();
        $languages = Language::where('is_active', '=', '1')->get(array(
            'id',
            'folder_code',
            'lang_code'
        ));
        foreach ($languages as $key => $val) {
            $currLangArray = '<?php return array(';
            foreach ($list as $listDetails) {
                if ($listDetails['language_id'] == $val->id || $listDetails['language_id'] == 0) {
                    $currLangArray .= '"' . $listDetails['key_value'] . '"=>"' . $listDetails['value'] . '",' . "\n";
                }
            }
            $currLangArray .= ');';
            $file = ROOT . DS . 'resources' . DS . 'lang' . DS . $val->lang_code . DS . 'messages.php';
            $bytes_written = File::put($file, $currLangArray);
            if ($bytes_written === false) {
                die("Error writing to file");
            }
        }
     } //end settingFileWrite()
} //end TextSettingController class