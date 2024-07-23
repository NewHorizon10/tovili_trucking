<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActionLog extends Model
{
    use HasFactory;
    protected $table = 'admin_action_logs';

    public function admin($value='')
    {
        return $this->hasOne(Admin::class, 'id', 'user_id');
    }
}
