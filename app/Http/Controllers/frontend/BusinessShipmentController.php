<?php

namespace App\Http\Controllers\frontend;

use App\Models\TruckTypeDescription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lookup;
use App\Models\TruckType;
use App\Models\UserCompanyInformation;
use App\Models\UserVerificationCode;
use App\Models\Shipment;
use App\Models\ShipmentStop;
use App\Models\ShipmentStopAttchement;
use App\Models\ShipmentOffer;
use App\Models\Chat;
use App\Models\ShipmentPrivateCustomerExtraInformation;
use App\Models\LookupDiscription;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Session,DB;
use Carbon\Carbon;

class BusinessShipmentController extends Controller
{

	public function __construct(Request $request)
	{
		parent::__construct();
		$this->request              =   $request;
	}

	public function ShipmentDetails(Request $request,$request_number){
		
		if($request->delivery_note){
			foreach($request->delivery_note as $key => $file){
				$shipment_stops_obj	= ShipmentStop::find($key);
				
				if ($request->hasFile('certificate_number.'.$key)) {	
					$file = rand() . '.' . $request->certificate_number[$key]->getClientOriginalExtension();
					$request->file('certificate_number.'.$key)->move(Config('constants.GALLERY_MEDIA_IMAGE_ROOT_PATH'), $file);
					$shipment_stops_obj->request_certificate = $file;
				}
					
				if($request->delivery_note[$key] == "physical_certificate"){
					$shipment_stops_obj->request_certificate_type = "physical";
				}else if($request->delivery_note[$key] == "digital_certificate"){
					$shipment_stops_obj->request_certificate_type = "digital";
				}else if($request->delivery_note[$key] == "no"){
					$shipment_stops_obj->request_certificate_type = "no";
				}
				
				$shipment_stops_obj->save();
			}

			Session()->flash('success', trans("messages.shipment_certificates_updated_successfully"));
			return Redirect()->back();
		}

		if($request->wantsJson() ) {
			if (Auth::guard('api')->user()) {
				$user = Auth::guard('api')->user();
			}
		}else{
			if (Auth::user()) {
				$user = Auth::user();
			}
		}

		$shipment = Shipment::where("request_number",$request_number)
		->where("customer_id",Auth::user()->id)
		
		->with(
			[
				'ShipmentStop' => function($query) {
				},
				
				'ShipmentStop.ShipmentStopAttchements',
				'TruckTypeDescriptions' => function($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'RequestTimeDescription' => function($query) {
					$query->where(['language_id' => getAppLocaleId()]);
				},
				'SelectedShipmentOffers' => function($query) {
					$query->where(['status' => 'approved_from_company']);
				},
				'shipmentDriverScheduleDetails',
                'shipmentDriverScheduleDetails.truckDriver',
                'shipmentRatingReviews',
                'shipmentRatingReviews.photos',
			]
		)
		->whereIn('status', ['shipment','end'])
		->first();
		
		if($shipment == null){
			Session()->flash('error', trans("messages.request_status_is_shipment"));
			return redirect::route('business.customer-dashboard');
		}
		$shipmentOffer = ShipmentOffer::where("shipment_id",$shipment->id)
		->where('status','approved_from_company')
		->with([
			'companyUser',
			'companyUser.userCompanyInformation',
			'companyUser.userCompanyInformation.getCompanyRefuelingDescription' => function($query) {
				$query->where(['language_id' => getAppLocaleId()]);
			},
			'TruckDetail',
			'TruckDetail.truckDriver',
			'TruckDetail.truckDriver.userDriverDetail', 
			'TruckTypeDetail' => function($query) {
				$query->where(['language_id' => getAppLocaleId()]);
			}
		])
		->first();
		
		if(!empty($shipmentOffer&&$shipmentOffer->TruckDetail && $shipmentOffer->TruckDetail->truckDriver)){
			if($shipmentOffer->TruckDetail->truckDriver->userDriverDetail){

				$driver_picture = $shipmentOffer->TruckDetail->truckDriver->userDriverDetail->driver_picture;
				if($driver_picture){
					$driver_picture = Config('constants.DRIVER_PICTURE_PATH').$driver_picture;
				}else{
					$driver_picture = Config('constants.NO_IMAGE_PATH');
				}
				$shipmentOffer->TruckDetail->truckDriver->userDriverDetail->driver_picture = $driver_picture;
			}
		}

		
		$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk = LookupDiscription::where(['parent_id'=>$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk,'language_id'=> getAppLocaleId()])->first()->code ?? "";
		
		$typeOfTruck = ''; 
		if ($shipmentOffer && $shipmentOffer->TruckDetail) {
			$truckTypeDescription = TruckTypeDescription::where([
				'parent_id' => $shipmentOffer->TruckDetail->type_of_truck,
				'language_id' => getAppLocaleId()
			])->first();

			if ($truckTypeDescription) {
				$typeOfTruck = $truckTypeDescription->name;
			}
		}

		$shipmentOfferTypeOfTruck = $typeOfTruck;
		
		return View("frontend.customers.business.shipment.details", compact('user','shipment','shipmentOffer'));
	}

    public function shipmentViewAll(Request $request)
    {
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
        

        $companyType = $user_company_informations = false;

        if($user->user_role_id == 3){

            $user_company_informations = UserCompanyInformation::where('user_id', $user->id)->get();
            $companyType = Lookup::where('lookup_type',"company-type")->with('')->get('lookupDiscription');
        }
        else if($user->user_role_id == 4){
            $companyType = $user_company_informations = false;
        }

		$whereShipmentStatusIs = array('shipment','end');
		if($request->status){
			$whereShipmentStatusIs = array($request->status);
		}

        $ShipmentLists  =  Shipment::where("customer_id",$user->id)
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
				}
            ]
        )
        ->whereIn('status',$whereShipmentStatusIs)
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
        
        return View("frontend.customers.business.shipment.index", compact('user_company_informations', 'user', 'companyType', 'ShipmentList'));
    }

	public function  BusinessAllinvoice(Request $request) {

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
		if($user->user_role_id == 2){

            $user_company_informations = UserCompanyInformation::where('user_id', $user->id)->first();
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
				'companyInformation',
				'shipmentDriverSchedule.companyInformation',

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
		 return View("frontend.customers.business.shipment.all_invoice", compact('ShipmentList','user','user_company_informations'));
	}

	
}
