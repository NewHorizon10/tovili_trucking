<?php

namespace App\Http\Controllers\frontend\Private;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Language;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\NotificationAction;
use App\Models\NotificationTemplateDescription;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class NotificationController extends Controller
{
    public $model                =    'notifications';
    public function __construct(Request $request){
        parent::__construct();
        $this->request = $request;
        View()->share('model', $this->model);
    }

    public function notificationsList(Request $request)
    {
        if($request->wantsJson() ) {
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
		}else{
			if (Auth::user()) {
				$user = Auth::user();
			}
		}
        Notification::where('user_id',$user->id)->update(["is_read"=>1]);
		$DB 					= 	Notification::query();
        $DB->leftjoin('shipments', 'shipments.id' , 'notifications.shipment_id');
        $DB->where('user_id',Auth::user()->id);
        $DB->where("language_id",getAppLocaleId());
        $DB->orderByDesc("notifications.id");
        $DB->select(
            'notifications.*',
            'shipments.status as shipments_status',
            'shipments.request_number as request_number'
        );
		$inputGet				=	$request->all();
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'notifications.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page  =   ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
		$results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string =  $request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string  =   http_build_query($complete_string);
		$results->appends($inputGet)->render();
        if(!$results->isEmpty()){
            foreach($results as &$item){
                if($item->is_read == 0){
                    Notification::where('map_id',$item->map_id)->update(["is_read"=>1]);
                    $item->is_read = 1;
                }
                $item->created_date = date(config("Reading.date_format"),strtotime($item->created_at));
            }
        }

        return View("frontend.customers.private.notifications.notification-list", compact('results', 'sortBy', 'order', 'query_string'));


    }

    function notificationsDelete($map_id)
    {
        
        $notificationDelete   =  Notification::where('map_id', $map_id)->delete();
        
        return redirect::route('business.notification-list')
					->withSuccess(trans("messages.notification_deleted_successfully"));

    }
}