<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminderlogs extends Model
{
    use HasFactory;
    protected $table = "tbl_reminders_logs";
    

    public function check_balance($id)
    {
       $user=User::find($id);
       if($user){
          if($user->total_call > 0){
              return 1;
          }
          return 0;
       }
       return 0;
    }

    public function check_sms_balance($id)
    {
       $user=User::find($id);
       if($user){
          if($user->total_message > 0){
              return 1;
          }
          return 0;
       }
       return 0;
    }
}
