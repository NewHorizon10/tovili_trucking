<?php
namespace App\Http\Controllers\frontend;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RatingReview;
use App\Models\ShipmentOffer;  
use App\Models\RatingReviewPhoto;
use App\Models\ShipmentDriverSchedule;
use App\Models\UserCompanyInformation;
use App\Models\Shipment;

use Redirect,Session,Auth;

class RatingController extends Controller
{ 

	public function __construct(Request $request) {
		parent::__construct();
        $this->request              =   $request;
    }

	public function submitReview(Request $request)
    {   


        $objRatingReview                          =  RatingReview::where(["customer_id"=>Auth::user()->id,"shipment_id"=>$request->shipment_id])->first();
        if(!$objRatingReview){
            $shipmentOfferObj = ShipmentOffer::where(
                [
                    "shipment_id"=>$request->shipment_id,
                    "status"=>"approved_from_company"
                ]
            )->first();
            $objRatingReview                          =  new RatingReview;
            $objRatingReview->customer_id             =  Auth::user()->id;
            $objRatingReview->shipment_id             =  $request->shipment_id;
            $objRatingReview->truck_company_id        =  $shipmentOfferObj->truck_company_id ?? 0;
            $objRatingReview->overall_rating          =  $request->overall_rating ?? 0;
            $objRatingReview->driver_rating           =  $request->driver_rating ?? 0;
            $objRatingReview->professionality         =  $request->professionality ?? 0;
            $objRatingReview->meet_schedule           =  $request->meet_schedule ?? 0;
            $objRatingReview->review                  =  $request->review  ?? '';
            $Response                                 =  $objRatingReview->save();

            if($request->image) {
                foreach($request->image as $image){
                    $obj                                  =  new RatingReviewPhoto;
                    $obj->rating_review_id                =  $objRatingReview->id;
                    $obj->photo                           =  $image;
                    $obj->save();
                }
            }

            
            $usersLists = ShipmentDriverSchedule::leftjoin('shipments', 'shipment_driver_schedules.shipment_id', 'shipments.id')
            ->leftJoin('shipment_offers' ,'shipments.id', 'shipment_offers.shipment_id')
            ->leftJoin('users' ,'users.id', 'shipments.customer_id')
            ->where('shipments.id', $request->shipment_id)
            ->select(
                'shipments.*',
                'shipment_driver_schedules.shipment_id',
                'shipment_driver_schedules.truck_company_id',
                'shipment_driver_schedules.shipment_actual_end_time',
                'shipment_driver_schedules.shipment_status',
                'shipment_driver_schedules.truck_id as offers_truck_id',
                'users.customer_type',
                'users.name',
                'users.email',
                'users.phone_number',
                'users.language',
                'shipment_offers.price',
                'shipment_offers.extra_time_price',
                'shipment_offers.description as offers_description',
                'shipment_offers.status as offers_status',
                'shipment_offers.truck_id as offers_truck_id',
    
            )
            ->first();
    
            $userCompanyInformation = array();
            if($usersLists){
    
                $userCompanyInformation = UserCompanyInformation::where("user_id",$usersLists->customer_id)->first()->toArray();
    
                if($usersLists->request_type){
                    $shipmentType = $usersLists->request_type;
                }else{
                    $shipmentType = $usersLists->shipment_type;
                }
                $shipmentObj = Shipment::find($usersLists->id);
    
                $this->shipmentReviewAfterScheduleEndToTruckCompany($usersLists, $userCompanyInformation, $shipmentType);
            }
    
          

            Session()->flash('success', trans("messages.rating_has_been_added_successfully"));
            return Redirect()->back();
        }else{
            Session()->flash(trans("messages.Something went wrong"));
            return Redirect()->back()->withInput();
        }
        
    }


}



