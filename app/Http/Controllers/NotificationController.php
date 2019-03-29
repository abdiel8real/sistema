<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
    public function get()
    {
//        return Notification::all();
        $unreadNotifications = Auth::user()->unreadNotifications;
        $fechaActual = date('Y-m-d');
        foreach ($unreadNotifications as $notification)
        {
            if ($fechaActual != $notification->created_at->toDateString())
            {
                $notification->markedAsRead();
            }
        }
        return Auth::user()->unreadNotifications;
    }
}
