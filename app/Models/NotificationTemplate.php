<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class NotificationTemplate extends Model
{
    use HasFactory;

    public function EmailActions(){
        return $this->hasOne(EmailTemplate::class, 'action', 'action');
    }
}
