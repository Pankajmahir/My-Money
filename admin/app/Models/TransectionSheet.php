<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransectionSheet extends Model
{
    protected $table = 'tbl_transection_sheets';

   public function user()
   {
       return $this->hasOne('App\Models\User', 'id', 'user_id');
   }

   public function business()
   {
       return $this->hasOne('App\Models\Business', 'id', 'business_id');
   }
}
