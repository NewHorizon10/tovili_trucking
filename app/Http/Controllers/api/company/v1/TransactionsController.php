<?php

namespace App\Http\Controllers\api\company\v1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\UserCompanyInformation;
use App\Models\Lookup;
use App\Models\UserDriverDetail;
use App\Models\UserDeviceToken;
use App\Models\Shipment;
use App\Models\UserVerificationCode;

use App\Models\ShipmentOffer;
use App\Models\Language;
use App\Models\ShipmentDriverSchedule;
use App\Models\Truck;
use App\Models\Notification;
use App\Models\Transaction;

use Cache, Cookie, Input, Mail, mongoDate, Response, Session, URL, App, Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class TransactionsController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }
    public function transactions(Request $request) {

        $DB					=	Transaction::query();
        $DB->with('planName');

        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'transactions.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
        $DB->where('truck_company_id',Auth::guard('api')->user()->id);
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        if(!$results->isEmpty()){
            foreach($results as &$item){
                if ($item->planName ?->type == '0') {
                    $item->plan_type_name = trans('messages.monthly');
                } elseif ($item->planName ?->type == '1') {
                    $item->plan_type_name = trans('messages.quarterly');
                } elseif ($item->planName ?->type == '2') {
                    $item->plan_type_name = trans('messages.Half Yearly');
                } elseif ($item->planName ?->type == '3') {
                    $item->plan_type_name = trans('messages.Yearly');
                }
                if($item->status  == 'success'){
                    $item->status_string = trans("messages.Success");
                }else if($item->status  == 'pending'){
                    $item->status_string = trans("messages.pending");
                }elseif($item->status  == 'failed'){
                    $item->status_string = trans("messages.failed");
                }elseif($item->status  == 'process'){
                    $item->status_string = trans("messages.process");
                }
                $item->is_free = $item->planName ?->is_free;
                $item->is_free_string = ($item->planName ?->is_free == 1 ? trans('messages.Free') : trans('messages.paid'));

                $item->created_date = date(config("Reading.date_format"),strtotime($item->created_at));
                
                unset(
                    $item->id,
                    $item->company_subscription_plan_id,
                    $item->responce_json,
                    $item->planName,
                    $item->truckCompanyName,
                    $item->created_at,
                    $item->updated_at,
                );
            }
        }
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        return response()->json($response);
	}
}
