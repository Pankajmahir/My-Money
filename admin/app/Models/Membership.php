<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $table = 'tbl_membership';
    
    public function user(Type $var = null)
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function package(Type $var = null)
    {
        return $this->hasOne('App\Models\Package', 'id', 'package_id');
    }
}