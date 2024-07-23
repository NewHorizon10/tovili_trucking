<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckTypeQuestion extends Model
{
    use HasFactory;

    public function TruckTypeQuestionDiscription($value='')
    {
        return $this->hasOne(TruckTypeQuestionDescription::class, 'parent_id', 'id');
    }
    
  
    
}
