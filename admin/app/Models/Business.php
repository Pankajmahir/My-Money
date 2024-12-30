<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = 'tbl_business';
    
    public function deleteBusiness($id)
    {
        Customer::where('business_id', $id)->delete();
        TransectionSheet::where('business_id', $id)->delete();
        Notification::where('business_id', $id)->delete();
        // SendNotification::where('business_id', $id)->delete();
        Reminder::where('business_id', $id)->delete();
        Reminderlogs::where('business_id', $id)->delete();
    }
}