<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class EmailTemplate extends Model
{
    use HasFactory;

    public function EmailActionsDescription(){
        return $this->hasMany(EmailTemplateDescription::class, 'parent_id', 'id');
    }
}
