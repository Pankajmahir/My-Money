<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhonepayTransaction extends Model
{
    use HasFactory;
    protected $table = "phone_pay_transactions";

    protected $fillable = [
        'merchantId',
        'merchantTransactionId',
        'merchantUserId',
        'amount',
        'mobileNumber',
        'post_response', 
        'get_response'
    ];
}
