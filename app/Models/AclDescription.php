<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AclDescription extends Model
{
    use HasFactory;
    
    protected $table = "acls_descriptions";
}
