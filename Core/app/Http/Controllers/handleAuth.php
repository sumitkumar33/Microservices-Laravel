<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class handleAuth extends Controller
{
    private $token;

    public function __construct(Request $request)
    {
        $this->token = $request->bearerToken() ?? '';
    }

    public function index()
    {
        $users = json_decode(Http::withToken($this->token)->get('users.myproject.com/api/users'));
        return response($users, 200);
    }

    public function authLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');
        $result = json_decode(Http::post('users.myproject.com/api/login', $credentials));
        return response()->json($result);
    }

    public function authLogout()
    {
        $result = json_decode(Http::withToken($this->token)->get('users.myproject.com/api/logout'));
        return response()->json($result);
    }

    public function authLogoutAll()
    {
        $result = json_decode(Http::withToken($this->token)->get('users.myproject.com/api/logoutAll'));
        return response()->json($result);
    }

    public function authRegister(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            "role_id" => "required|lte:2|gt:0",
            'address' => 'required',
            'profile_image' => 'required',
            'current_school' => 'required',
            'previous_school' => 'required',
        ]);
        if ($request['role_id'] == 1) { //Student
            $request->validate([
                'parent_name' => 'required',
                'parent_contact' => 'required',
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
            // return response()->json($result);
        } else {
            $request->validate([
                'expertise_subject' => 'required',
                'experience' => 'required',
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
            // return response()->json($result);
        }
        return response()->json($result);
    }

    public function authUpdate(Request $request){
        if($this->token == null){
            return response()->json(['Message' => 'Token is required.', 'statusCode' => 403]);
        }
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
        return response()->json($result);

    }
}
