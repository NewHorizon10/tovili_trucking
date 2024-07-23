<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use  App\Models\Department;

class RolesController extends Controller
{
    public $model        =    'roles';
    public function __construct(Request $request){
        parent::__construct();
        View()->share('model', $this->model);
        $this->request = $request;
    }
    public function index(Request $request){
        $DB                    =    Department::query();
        $searchVariable      =   array();
        $inputGet         =   $request->all();
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
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('departments.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('departments.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('departments.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("departments.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("departments.is_active", $fieldValue);
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->where("is_deleted", 0);
        $DB->select("departments.*");
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page  =   ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        return  View("admin.$this->model.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }

    public function create(){
        return view("admin.$this->model.add");
    }

    public function store(Request $request){

        $validated = $request->validate([
            'name' => 'required'
        ]);
        $obj           =  new Department;
        $obj->name     =  $request->name;
        $SavedResponse =      $obj->save();
        if (!$SavedResponse) {
            Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
            return Redirect()->back()->withInput();
        } else {
            
            Session()->flash('success', ucfirst(trans("messages.admin_Department_has_been_added_successfully")));
            return Redirect()->route($this->model . ".index");
        }
    }

    public function edit($endepid){
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id = base64_decode($endepid);
            $depDetails   =   Department::find($dep_id);
            return  View("admin.$this->model.edit", compact('depDetails'));
        } else {
            return redirect()->route($this->model . ".index");
        }
    }

    public function update(Request $request, $endepid){
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id = base64_decode($endepid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        $validated = $request->validate([
            'name' => 'required'
        ]);
        $obj           =  Department::find($dep_id);
        $obj->name     =  $request->name;
        $SavedResponse =  $obj->save();
        if (!$SavedResponse) {
            Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
            return Redirect()->back()->withInput();
        }
        Session()->flash('success', ucfirst(trans("messages.admin_Department_has_been_updated_successfully")));
        return Redirect()->route($this->model . ".index");
    }

    public function destroy($endepid){
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id     = base64_decode($endepid);
        }
        $depDetails     =   Department::find($dep_id);
        if (empty($depDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($dep_id) {
            Department::where('id', $dep_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Department_has_been_removed_successfully")));
        }
        return back();
    }

    public function changeStatus(Request $request,$modelId = 0, $status = 0){
        dd($request->all());
        if ($status == 1) {
            $statusMessage   =   ucfirst(trans("messages.admin_Department_has_been_deactivated_successfully"));
        } else {
            $statusMessage   =   ucfirst(trans("messages.admin_Department_has_been_activated_successfully"));
        }
        $user = Department::find($modelId);
        if ($user) {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
                $actionType = 'Activated';
            } else {
                $NewStatus = 0;
                $actionType = 'Deactivated';
            }
            $user->is_active = $NewStatus;
            $ResponseStatus = $user->save();

            $logData = array(
                'record_id' => $user->id,
                'module_name' => 'Department',
                'action_name' => $actionType,
                'action_description' => ucfirst($actionType) . ' Department',
                'record_url' => route('roles.show', base64_encode($user->id)),
                'user_agent' => $request->header('User-Agent'),
                'browser_device' => '',
                'location' => '',
                'ip_address' => $request->ip()
            );

            $this->genrateAdminLog($logData);
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }
}
