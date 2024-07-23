<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Relations\HasOne;

class RatingReview extends Model
{
    use HasFactory;

    public function photos()
    {
        return $this->hasMany(RatingReviewPhoto::class, 'rating_review_id', 'id');
    }

    public function getUser(): HasOne
    {
        return $this->hasOne(User::class,'id','customer_id');
    }  

    public function getTruckCompany(): HasOne
    {
        return $this->hasOne(User::class,'id','truck_company_id');
    }
    
    

    public function getPhotos(): HasMany
    {
        return $this->hasMany(RatingReviewPhoto::class,'rating_review_id','id');
    }
    
    public function shipmentDetails(): HasOne
    {
        return $this->hasOne(Shipment::class,'id','shipment_id');
    }


}
