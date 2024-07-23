<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateDescription extends Model
{
    use HasFactory;

    public function EmailAction(){
        return $this->hasOne(EmailTemplate::class, 'id', 'parent_id');
    }
}
