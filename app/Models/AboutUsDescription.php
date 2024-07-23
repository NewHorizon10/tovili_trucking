<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUsDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id','language_id','heading','description','goal_description'];
}
