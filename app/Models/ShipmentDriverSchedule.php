<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDriverSchedule extends Model
{
    use HasFactory;
    // protected $table = 'shipments';

    public function customer($value='')
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function truckDriver($value='')
    {
        return $this->hasOne(User::class, 'id', 'driver_id');
    }

    public function userDriverDetail($value='')
    {
        return $this->hasOne(UserDriverDetail::class, 'user_id', 'driver_id');
    }

    public function companyInformation(){
        return $this->hasOne(UserCompanyInformation::class, 'user_id', 'truck_company_id');
    }

    // public function userCompanyInformation(){
    //     return $this->hasOne(UserCompanyInformation::class, 'truck_company_id', 'user_id');
    // }
}
