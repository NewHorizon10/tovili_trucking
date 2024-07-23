<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;
    // protected $table = 'shipments';

    // protected $fillable = [
    //     'send_notification_one_day_before_shipment_starts', 
    //     'send_notification_one_hour_before_shipment_starts',
    // ];

    public function customer($value='')
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function shipment_attchement($value='')
    {
        return $this->hasMany(ShipmentAttchement::class, 'shipment_id', 'id');
    }

    public function ShipmentPrivateCustomer_ExtraInformation($value='')
    {
        return $this->hasOne(ShipmentPrivateCustomerExtraInformation::class, 'shipment_id', 'id');
    } 
    

    public function ShipmentOffers($value='')
    {
        return $this->hasMany(ShipmentOffer::class, 'shipment_id', 'id');
    } 
    // public function NotificationsList($value='')
    // {
    //     return $this->hasMany(Notification::class, 'shipment_id', 'id');
    // } 

    public function TruckTypeDescriptions($value='')
    {
        return $this->hasOne(TruckTypeDescription::class, 'parent_id', 'shipment_type');
    }
    public function TruckTypeDescriptionsPrivate($value='')
    {
        return $this->hasOne(TruckTypeDescription::class, 'parent_id', 'request_type');
    }  

    public function ShipmentStop($value='')
    {
        return $this->hasMany(ShipmentStop::class, 'shipment_id', 'id');
    } 

    public function ShipmentPrivateCustomerExtraInformations($value='')
    {
        return $this->hasMany(ShipmentPrivateCustomerExtraInformation::class, 'shipment_id', 'id');
    } 
    public function RequestTimeDescription($value='')
    {
        return $this->hasOne(LookupDiscription::class, 'parent_id', 'request_time');
    }
    public function RequestTypeDescription($value='')
    {
        return $this->hasOne(LookupDiscription::class, 'parent_id', 'request_type');
    }

    public function SelectedShipmentOffers($value='')
    {
        return $this->hasOne(ShipmentOffer::class, 'shipment_id', 'id');
    } 

    public function shipmentDriverScheduleDetails($value='')
    {
        return $this->hasOne(ShipmentDriverSchedule::class, 'shipment_id', 'id');
    } 

    

    public function shipment_offers()
    {
        return $this->hasMany(ShipmentOffer::class, 'shipment_id', 'id');
    }

    public function shipmentRatingReviews()
    {
        return $this->hasOne(RatingReview::class, 'shipment_id', 'id');
    }

    public function shipmentPrice(){
        return $this->hasOne(ShipmentOffer::class, 'shipment_id', 'id');
    }

    public function approvedShipmentPrice(){
        return $this->hasOne(ShipmentOffer::class, 'shipment_id', 'id')->where('status', 'approved_from_company');
    }

    public function shipmentOffersCount(){
        return $this->hasMany(ShipmentOffer::class, 'shipment_id', 'id')->where('status', 'waiting')->orWhere('status', 'selected')->orWhere('status', 'rejected');
    }
    
    public function rejectedShipmentOffersCount(){
        return $this->hasMany(ShipmentOffer::class, 'shipment_id', 'id')->where('status', 'rejected');
    }

    public function companyInformation(){
        return $this->hasOne(UserCompanyInformation::class, 'user_id','customer_id');
    }

    public function shipmentDriverSchedule(){
        return $this->hasOne(ShipmentDriverSchedule::class, 'shipment_id', 'id');
    }

    public function AllselectedShipmentOffers($value='')
    {
        return $this->hasMany(ShipmentOffer::class, 'shipment_id', 'id');
    } 



}
