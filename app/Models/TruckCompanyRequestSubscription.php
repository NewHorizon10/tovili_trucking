<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckCompanyRequestSubscription extends Model
{
    use HasFactory;

    protected $table = 'truck_company_request_subscription_plans';

    public function planDetail(){
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }

}