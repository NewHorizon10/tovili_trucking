<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use  App\Models\Setting;
use Redirect,Config,Artisan;

class SettingsController extends Controller
{
    public $model      =   'settings';
    public function __construct(Request $request)
    {   
        parent::__construct();
        View()->share('model', $this->model);
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $DB = Setting::query();
        $searchVariable  =  array();
        $inputGet    =  $request->all();
        if ($inputGet) {
            $searchData  =  $request->all();
            foreach ($searchData as $fieldName => $fieldValue) {
                if (!empty($fieldValue)) {
                    if ($fieldName == "title") {
                        $DB->where("settings.title", 'like', '%' . $fieldValue . '%');
                    }
                    $searchVariable  =  array_merge($searchVariable, array($fieldName => $fieldValue));
                }
            }
        }
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'id';
        $order  = ($request->input('order')) ? $request->input('order')   : 'ASC';
        $result = $DB->orderBy($sortBy, $order)->paginate(Config("Reading.records_per_page"));
        return  view("admin.$this->model.index", compact('result', 'searchVariable', 'sortBy', 'order'));
    }

    public function create()
    {
        return  View("admin.$this->model.add");
    }


    public function store(Request $request)
    {   
   
        $validated = $request->validate([
            'title' => 'required',
            'key' => 'required',
            'key'  => 'required',
            'input_type' => 'required',
        ]);
        $obj              = new Setting;
        $obj->title       = $request->title;
        $obj->key         = $request->key;
        $obj->value       = $request->value;
        $obj->input_type  = $request->input_type;
        $obj->editable    = 1;
        $savedata = $obj->save();
        if ($savedata) {
            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Settings_added_successfully")));
            return Redirect()->route($this->model . ".index");
        }
    }

    public function edit($ensetid)
    {
        $Set_id = '';
        $setdetails =    array();
        if (!empty($ensetid)) {
            $Set_id = base64_decode($ensetid);
            $setdetails   =   Setting::find($Set_id);
            $data = compact('setdetails');
            return  View("admin.$this->model.edit", $data);
        } else {
            return Redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request, $ensetid)
    {   
     
        $Set_id = '';
        $setdetails =    array();
        if (!empty($ensetid)) {
            $Set_id = base64_decode($ensetid);
        } else {
            return Redirect()->route($this->model . ".index");
        }
    
        $validated = $request->validate([
            'title' => 'required',
            'key' => 'required',
            'key'  => 'required',
            'input_type' => 'required',
        ]);
        $obj   = Setting::find($Set_id);
        $obj->title        = $request->title;
        $obj->key         = $request->key;
        $obj->value       = $request->value;
        $obj->input_type       = $request->input_type;
        $obj->editable      = 1;
        $savedata = $obj->save();
        if ($savedata) {
            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Settings_updated_successfully")));
            return Redirect()->route($this->model . ".index");
        }
    }

    public function destroy($ensetid)
    {
        $id = '';
        if (!empty($ensetid)) {
            $id = base64_decode($ensetid);
        }
        $settingDetails   =  Setting::find($id);
        if ($settingDetails) {
            $settingDetails->delete();
            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Settings_deleted_successfully")));
        }
        return back();
    }

    public function prefix(Request $request, $enslug = null)
    {      
        if($request->v==1){
            Artisan::call('config:cache');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear'); 
            dd('clear cache');
        }
        $prefix = $enslug;
        if ($request->isMethod('POST')) {
            $allData        =  $request->all();
            if (!empty($allData)) {
                if (!empty($allData['Setting'])) {
                    foreach ($allData['Setting'] as $key => $value) {
                        if (!empty($value["'id'"]) && !empty($value["'key'"])) {
                            if ($value["'type'"] == 'checkbox') {
                                $val  =  (isset($value["'value'"])) ? 1 : 0;
                            } else {
                                $val  =  (isset($value["'value'"])) ? $value["'value'"] : '';
                            }
                            Setting::where('id', $value["'id'"])->update(array(
                                'key'          =>  $value["'key'"],
                                'value'       =>  $val
                            ));
                        }
                    }
                }
            }
            $this->settingFileWrite();
            Artisan::call('config:cache');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            shell_exec('/usr/bin/php8.0 -dmemory_limit=-1 artisan optimize:clear');
            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Settings_updated_successfully")));
            return  Redirect()->back();
        }
        $result = Setting::where('key', 'like', $prefix . '%')->orderBy('id', 'ASC')->get()->toArray();
        return  View('admin.settings.prefix', compact('result', 'prefix'));
    }

    public function settingFileWrite()
    {
        $DB    =  Setting::query();
        $list  =  $DB->orderBy('key', 'ASC')->get(array('key', 'value'))->toArray();
        $file = Config::get('constants.SETTING_FILE_PATH');
        $settingfile = '<?php ' . "\n";
        $append_string    =  "";
        foreach ($list as $value) {
            $val      =   str_replace('"', "'", $value['value']);
            $settingfile .=  'config(["' . $value['key'] . '"=>"' . $val . '"]);' . "\n";
        }
        $bytes_written = File::put($file, $settingfile);
        if ($bytes_written === false) {
            die("Error writing to file");
        }
    }
}
