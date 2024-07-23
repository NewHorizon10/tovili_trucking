<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamDescription extends Model
{
    use HasFactory;

    protected $fillable = ['name','designation','parent_id','language_id'];
}
