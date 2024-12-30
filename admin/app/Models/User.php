<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'referral_code',
        'device_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function deleteUser($id)
    {
        Business::where('user_id', $id)->delete();
        Transection::where('user_id', $id)->delete();
        Membership::where('user_id', $id)->delete();
        Notification::where('user_id', $id)->delete();
        SendNotification::where('user_id', $id)->delete();
        TransectionSheet::where('user_id', $id)->delete();
        Reminder::where('user_id', $id)->delete();
        DeviceToken::where('user_id', $id)->delete();
        BankAccount::where('user_id', $id)->delete();
        Reminderlogs::where('user_id', $id)->delete();
    }

    public function getdevicetoken(){
        return $this->hasOne('App\Models\DeviceToken','user_id', 'id');
    }

}
