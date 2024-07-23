<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookupDiscription extends Model
{
    use HasFactory;

    public function LookupParentId(){
        return $this->hasOne(Lookup::class, 'id', 'parent_id');
    }
}