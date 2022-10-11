<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class handleNotifications extends Controller
{
    /**
     * stores the authentication bearerToken.
     * @var string
     */
    private $token;

    /**
     * @method is called when class is instantiated.
     *  also used to store the authentication token.
     */
    public function __construct(Request $request)
    {
        $this->token = $request->bearerToken() ?? '';
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Token not found.'], 401);
        }
    }

    /**
     * @method used to fetch notifications of logged in user.
     */
    public function fetch()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)
            ->get('http://users.myproject.com/api/user/notifications'));
        return response()->json($data);
    }

    /**
     * @method used to mark notifications as read.
     */
    public function read()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)
            ->get('http://users.myproject.com/api/user/notifications/markRead'));
        return response()->json($data);
    }

    /**
     * @method used to mark notifications as unread.
     */
    public function unread()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)
            ->get('http://users.myproject.com/api/user/notifications/markUnread'));
        return response()->json($data);
    }

    /**
     * @method used to delete the logged in user's notifications
     */
    public function destroy()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)
            ->get('http://users.myproject.com/api/user/notifications/delete'));
        return response()->json($data);
    }
}
