<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class handleNotifications extends Controller
{
    private $token;

    public function __construct(Request $request)
    {
        $this->token = $request->bearerToken() ?? '';
    }

    public function fetch()
    {
        $data = json_decode(Http::withToken($this->token)
                    ->get('http://users.myproject.com/api/user/notifications'));
        return response()->json($data);
    }

    public function read()
    {
        $data = json_decode(Http::withToken($this->token)
                    ->get('http://users.myproject.com/api/user/notifications/markRead'));
        return response()->json($data);
    }

    public function unread()
    {
        $data = json_decode(Http::withToken($this->token)
                    ->get('http://users.myproject.com/api/user/notifications/markUnread'));
        return response()->json($data);
    }

    public function destroy()
    {
        $data = json_decode(Http::withToken($this->token)
                    ->get('http://users.myproject.com/api/user/notifications/delete'));
        return response()->json($data);
    }
}