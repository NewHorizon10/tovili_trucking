<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckType extends Model
{
    use HasFactory;

    public function truckTypeDiscription($value='')
    {
        return $this->hasOne(TruckTypeDescription::class, 'parent_id', 'id');
    }

    public function TruckTypeQuestionsList()
    {
        return $this->hasMany(TruckTypeQuestion::class, 'truck_type_id', 'id');
    }

   
    
    
}
