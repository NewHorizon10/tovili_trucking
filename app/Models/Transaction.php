<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Transaction extends Model
{
    
    use HasFactory;

    public function truckCompanyName(){
        return $this->belongsTo(User::class, 'truck_company_id', 'id');
    }

    public function CompanyName(){
        return $this->hasOne(UserCompanyInformation::class, 'user_id', 'truck_company_id');
    }
    public function planName(){
        return $this->belongsTo(TruckCompanySubscription::class, 'truck_company_id', 'truck_company_id');
    }
    public function requestPlanName(){
        return $this->belongsTo(TruckCompanyRequestSubscription::class, 'truck_company_id', 'truck_company_id');
    }

}