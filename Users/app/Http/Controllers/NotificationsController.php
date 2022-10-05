<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class NotificationsController extends Controller
{

    public function index() //fetch
    {
        $id = Auth::user()->user_id;
        $data = Http::get('http://notifications.myproject.com/api/notifications/fetch/'.$id);
        return response($data, 200);
    }

    public function makeNotification(Request $request, $id)
    {
        $data = Http::post('http://notifications.myproject.com/api/notifications/notify/'.$id, $request->all());
        return response($data, 200);
    }

    public function markRead()
    {
        $id = Auth::user()->user_id;
        $data = Http::get('http://notifications.myproject.com/api/notifications/read/'.$id);
        return response($data, 200);
    }

    public function markUnread()
    {
        $id = Auth::user()->user_id;
        $data = Http::get('http://notifications.myproject.com/api/notifications/unread/'.$id);
        return response($data, 200);
    }

    public function destroy()
    {
        $id = Auth::user()->user_id;
        $data = Http::get('http://notifications.myproject.com/api/notifications/delete/'.$id);
        return response($data, 200);
    }
}
