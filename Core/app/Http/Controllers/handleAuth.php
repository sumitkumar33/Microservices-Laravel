<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class handleAuth extends Controller
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
    }

    /**
     * @method used to fetch all the registered users from the database.
     */
    public function index()
    {
        $users = json_decode(Http::withToken($this->token)->get('users.myproject.com/api/users'));
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        return response()->json($users, 200);
    }

    /**
     * @method handles authentication and receive generated auth token.
     */
    public function authLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        $result = json_decode(Http::post('users.myproject.com/api/login', $credentials));
        return response()->json($result);
    }

    /**
     * @method revokes the supplied token linked to logged in user.
     */
    public function authLogout()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $result = json_decode(Http::withToken($this->token)->get('users.myproject.com/api/logout'));
        return response()->json($result, 200);
    }

    /**
     * @method revokes all tokens linked to user sessions.
     * @method can be used to logout user from all devices.
     */
    public function authLogoutAll()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated']);
        }
        $result = json_decode(Http::withToken($this->token)->get('users.myproject.com/api/logoutAll'));
        return response()->json($result, 200);
    }

    /**
     * @method handles the registration process for new users.
     */
    public function authRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|alpha_dash',
            'email' => 'required|email',
            'password' => 'required|string',
            "role_id" => "required|lte:2|gt:0",
            'address' => 'required|alpha_dash',
            'profile_image' => 'required',
            'current_school' => 'required|alpha_dash',
            'previous_school' => 'required|alpha_dash',
        ]);
        if ($request['role_id'] == 1) { //Student
            $request->validate([
                'parent_name' => 'required|alpha_dash',
                'parent_contact' => 'required|numeric',
            ]);
            $data = $request->only(
                'name',
                'email',
                'password',
                'role_id',
                'address',
                'profile_image',
                'current_school',
                'previous_school',
                'parent_name',
                'parent_contact',
            );
            $result = json_decode(Http::post('users.myproject.com/api/register', $data));
        } else {
            $request->validate([
                'expertise_subject' => 'required|alpha_dash',
                'experience' => 'required|numeric',
            ]);
            $data = $request->only(
                'name',
                'email',
                'password',
                'role_id',
                'address',
                'profile_image',
                'current_school',
                'previous_school',
                'expertise_subject',
                'experience',
            );
            $result = json_decode(Http::post('users.myproject.com/api/register', $data));
        }
        return response()->json(!is_null($result) ? $result : ['Message: ' => 'Request failed try again later.']);
    }

    /**
     * @method handles the update requests of users.
     */
    public function authUpdate(Request $request)
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated']);
        }
        $request->validate([
            'name' => 'alpha_dash',
            'email' => 'email',
            'password' => 'string',
            'address' => 'alpha_dash',
            'role_id' => 'numeric|gte:1|lte:2',
            'profie_image' => 'max:1999',
            'current_school' => 'alpha_dash',
            'previous_school' => 'alpha_dash',
            'parent_name' => 'alpha_dash',
            'parent_contact' => 'numeric',
            'expertise_subject' => 'alpha_dash',
            'experience' => 'numeric',
        ]);
        $data = $request->only(
            'name',
            'email',
            'password',
            'role_id',
            'address',
            'profile_image',
            'current_school',
            'previous_school',
            'parent_name',
            'parent_contact',
            'expertise_subject',
            'experience'
        );
        $result = json_decode(Http::withToken($this->token)
            ->post('users.myproject.com/api/update', $data));
        return response()->json($result, 200);
    }
}
