<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    public function index()
    {
        $notifications = Notification::orderby('created_at', 'desc')->paginate(10);
        return view('notifications.index', compact('notifications'));
    }

}