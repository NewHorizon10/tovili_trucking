<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckCompanySubscription extends Model
{
    use HasFactory;

    protected $table = 'truck_company_subscription_plans';

    public function companyName(){
        return $this->hasOne(UserCompanyInformation::class, 'user_id', 'truck_company_id');
    }

    public function companyUser(){
        return $this->hasOne(User::class, 'id', 'truck_company_id');
    }

    public function planDetail(){
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }

}