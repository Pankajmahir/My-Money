<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'tbl_customers';

    public function deleteCustomer($id)
    {
        Notification::where('customer_id', $id)->delete();
        Reminder::where('customer_id', $id)->delete();
        TransectionSheet::where('customer_id', $id)->delete();
    }
}