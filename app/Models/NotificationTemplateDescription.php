<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplateDescription extends Model
{
    use HasFactory;

    public function NotificationAction(){
        return $this->hasOne(NotificationTemplate::class, 'id', 'parent_id');
    }

}
