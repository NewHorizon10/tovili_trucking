<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lookup extends Model
{
    use HasFactory;

    public function lookupDiscription($value='')
    {
        return $this->hasOne(LookupDiscription::class, 'parent_id', 'id');
    }

    public function lookupDiscriptionList()
    {
        return $this->hasMany(LookupDiscription::class, 'parent_id', 'id')->where('language_id' , getAppLocaleId());
    }
}
