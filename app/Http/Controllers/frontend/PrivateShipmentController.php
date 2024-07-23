<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\TruckTypeDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Config;
use App\Models\Lookup;
use App\Models\Shipment;
use App\Models\ShipmentAttchement;
use App\Models\ShipmentPrivateCustomerExtraInformation;
use App\Models\User;
use App\Models\UserVerificationCode;
use App\Models\UserCompanyInformation;
use App\Models\ShipmentOffer;
use App\Models\ShipmentStop;
use App\Models\Chat;
use App\Models\LookupDiscription;
use Carbon\Carbon;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use Redirect, Session, Auth;
// namespace Carbon;
class PrivateShipmentController extends Controller
{
	public function __construct(Request $request)
	{
		parent::__construct();
		$this->request              =   $request;
	}

	public function ShipmentDetails(Request $request, $request_number)
	{
		if ($request->wantsJson()) {
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
		} else {
			if (Auth::user()) {
				$user = Auth::user();
			}
		}

		$ShipmentDetails = Shipment::where('customer_id', $user->id)
			->where("request_number", $request_number)
			->with(
				[

					'ShipmentPrivateCustomer_ExtraInformation' => function ($query) {
					},
					'RequestTypeDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'RequestTimeDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'TruckTypeDescriptions' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'TruckTypeDescriptionsPrivate' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'SelectedShipmentOffers' => function ($query) {
						$query->where(['status' => 'approved_from_company']);
					},
					'shipmentDriverScheduleDetails',
					'shipmentDriverScheduleDetails.truckDriver',
					'shipment_attchement'
				]
			)
			->whereIn('status', ['shipment', 'end'])
			->first();



		if ($ShipmentDetails == null) {
			Session()->flash('error', trans("messages.shipment_request_not_found"));
			return redirect::route('private.customer-dashboard');
		}

		$shipmentOffer = ShipmentOffer::where("shipment_id", $ShipmentDetails->id)
		->where('status','approved_from_company')
			->with([
				'companyUser',
				'companyUser.userCompanyInformation',
				'companyUser.userCompanyInformation.getCompanyRefuelingDescription' => function ($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'TruckDetail',
				'TruckDetail.truckDriver',
				'TruckDetail.truckDriver.userDriverDetail',
			])
			->first();
		if (!empty($shipmentOffer && $shipmentOffer->TruckDetail && $shipmentOffer->TruckDetail->truckDriver)) {
			$driver_picture = $shipmentOffer->TruckDetail->truckDriver->userDriverDetail->driver_picture;
			if ($driver_picture) {
				$driver_picture = Config('constants.DRIVER_PICTURE_PATH') . $driver_picture;
			} else {
				$driver_picture = Config('constants.NO_IMAGE_PATH');
			}
			$shipmentOffer->TruckDetail->truckDriver->userDriverDetail->driver_picture = $driver_picture;
		}
		$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk = LookupDiscription::where(['parent_id' => $shipmentOffer->companyUser->userCompanyInformation->company_tidaluk, 'language_id' => getAppLocaleId()])->first()->code ?? "'";
		if($shipmentOffer->TruckDetail){
			$shipmentOffer->TruckDetail->type_of_truck = TruckTypeDescription::where(['parent_id' => $shipmentOffer->TruckDetail->type_of_truck, 'language_id' => getAppLocaleId()])->first()->name ?? "";
		}

		return View("frontend.customers.private.shipment.details", compact('user', 'ShipmentDetails', 'shipmentOffer'));
	}

	public function shipmentViewAll(Request $request)
	{

		if (Auth::user()) {
			$user = Auth::user();
		}


		$companyType = $user_company_informations = false;

		if ($user->user_role_id == 3) {

			$user_company_informations = UserCompanyInformation::where('user_id', $user->id)->get();
			$companyType = Lookup::where('lookup_type', "company-type")->with('')->get('lookupDiscription');
		} else if ($user->user_role_id == 4) {
			$companyType = $user_company_informations = false;
		}

		$whereShipmentStatusIs = array('shipment', 'end');
		if ($request->status) {
			$whereShipmentStatusIs = array($request->status);
		}

		$ShipmentLists = Shipment::where("customer_id", $user->id)

			->with(
				[
					'ShipmentOffers' => function ($query) {
					},
					'ShipmentStop' => function ($query) {
					},
					'TruckTypeDescriptions' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					},
					'RequestTimeDescription' => function ($query) {
						$query->where(['language_id' => getAppLocaleId()]);
					}
				]
			)
			->whereIn('status', $whereShipmentStatusIs)
			->orderBy('id', 'desc');

		$inputGet				=	$request->all();
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page  =   ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
		$results = $ShipmentLists->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string =  $request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string  =   http_build_query($complete_string);
		$results->appends($inputGet)->render();

		$ShipmentList = $results;
		return View("frontend.customers.private.shipment.index", compact('user_company_informations', 'user', 'companyType', 'ShipmentList'));
	}

	public function PrivateAllinvoice(Request $request) {
		if($request->wantsJson()){
            if(Auth::guard('api')->user()){
                $user = Auth::guard('api')->user();
            }
        } 
        else{
            if(Auth::user()){
                $user = Auth::user();
            }
        }
				
		$ShipmentLists  =  Shipment::where("customer_id",$user->id)
		->whereNotNull('invoice_file')
        ->with(
            [
                'ShipmentOffers' => function($query) {
				},
				'ShipmentStop' => function($query) {
				},
				'TruckTypeDescriptions' => function($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'RequestTimeDescription' => function($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'companyInformation'

            ]
        )
        ->orderBy('id','desc');
		$inputGet				=	$request->all();
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'shipments.id';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page  =   ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
		$results = $ShipmentLists->orderBy($sortBy, $order)->paginate($records_per_page);
		$complete_string =  $request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string  =   http_build_query($complete_string);
		$results->appends($inputGet)->render();

		$ShipmentList = $results;

		 return View("frontend.customers.private.shipment.all_invoice", compact('ShipmentList','user'));
	}

}
