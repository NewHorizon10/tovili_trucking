<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentOffer extends Model
{
    use HasFactory;

    public function companyUser($value='')
    {
        return $this->hasOne(User::class, 'id', 'truck_company_id');
    }

    public function TruckTypeDetail($value='')
    {
        return $this->hasOne(TruckTypeDescription::class, 'parent_id', 'track_type_id');
    }

    public function TruckDetail($value='')
    {
        return $this->hasOne(Truck::class, 'id', 'truck_id');
    }
}
