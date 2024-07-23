<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction;

use Session;
use Illuminate\Support\Collection;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;


class TransactionController extends Controller
{
    public $model =  'transaction';
    public function __construct(Request $request){
        parent::__construct();
        View()->share('model', $this->model);
        $this->request  = $request;
    }

    public function index(Request $request)
    {
        $DB                    =    Transaction::query();
        $DB->with('truckCompanyName', 'planName', 'CompanyName');
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
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];

                $DB->whereBetween(DB::raw('date(created_at)'),[date('Y-m-d',strtotime($dateS)),date('Y-m-d',strtotime($dateE))] );
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('admins.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('admins.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "status") {
                        $DB->where("status", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "transaction_id") {
                        $DB->where("transaction_id", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "truck_company") {
                        $DB->whereHas("truckCompanyName", function($query) use($fieldValue){
                            $query->where("users.name", 'like', '%' . $fieldValue . '%');
                        });
                    }
                    if ($fieldName == "plan_name") {
                        $DB->where("plan_name", 'like', '%' . $fieldValue . '%');
                    }
                    if($fieldName == "plan_duration"){
                        $DB->where('plan_type', $fieldValue);
                    }
                    if($fieldName == "payment_type"){
                        if($fieldValue == 0 ){
                            $DB->where("amount", 0);
                        }else{
                            $DB->where("amount",">", 0);
                        }
                    }

                    if($request->payment_type == null || $request->payment_type != 0 || $request->payment_type == ''){
                        if($fieldName == "minprice"){
                            $DB->whereRaw("amount >= $fieldValue");    
                        }
                        if($fieldName == "maxprice"){
                            $DB->whereRaw("amount <= $fieldValue");    
                        }
                    }
                    
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'transactions.created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page    =    ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");

        $DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_transaction'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);

        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        

        return  View("admin.$this->model.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }

    public function export(Request $request){

        $list[0] = array(
            trans("messages.admin_plan_name"),
			trans("messages.admin_transaction_id"),
			trans("messages.admin_Truck_Company"),
			trans("messages.admin_plan_name"),
			trans("messages.price"),
			trans("messages.admin_common_Status"),
		);
		
		$customers_export = Session::get('export_data_transaction');

        foreach ($customers_export as $key => $excel_export) {

            $typeData = '';


            if ($excel_export->planName ?->type == '0') {
                $typeData = trans('messages.monthly');
            } elseif ($excel_export->planName ?->type == '1') {
                $typeData = trans('messages.quarterly');
            } elseif ($excel_export->planName ?->type == '2') {
                $typeData = trans('messages.Half Yearly');
            } elseif ($excel_export->planName ?->type == '3') {
                $typeData = trans('messages.Yearly');
            }

            if($excel_export->planName == null){
                if ($excel_export->requestPlanName ?->type == '0') {
                    $typeData = trans('messages.monthly');
                } elseif ($excel_export->requestPlanName ?->type == '1') {
                    $typeData = trans('messages.quarterly');
                } elseif ($excel_export->requestPlanName ?->type == '2') {
                    $typeData = trans('messages.Half Yearly');
                } elseif ($excel_export->requestPlanName ?->type == '3') {
                    $typeData = trans('messages.Yearly');
                } 
            }


			$list[] = array(
            $excel_export->plan_name,
            $excel_export->transaction_id,
            $excel_export->truckCompanyName ?->name,
            $typeData,
            $excel_export->amount,
            $excel_export->status,

            );
            }

        $collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'Transaction Report.xlsx');

    }

}