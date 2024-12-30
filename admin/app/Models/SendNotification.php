<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendNotification extends Model
{
    use HasFactory;

    protected $table = "tbl_send_notification";

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
