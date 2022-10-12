<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @method Handles login requests from API Gateway.
     *  Also generates Personal Authentication Tokens and returns to user.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return json_encode([
                'msg' => 'Invalid credentials.',
                'statusCode' => 401,
            ]);
        }
        $token = auth()->user()->createToken(Auth::user());
        return json_encode(['userData' => Auth::user(), 'token' => $token->accessToken, 'statusCode' => 200,]);
    }

    /**
     * @method Revokes the current tokens and logout user session.
     */
    public function logout()
    {
        auth()->user()->token()->revoke();
        return response(['Message' => 'Successfully logged out.'], 200);
    }

    /**
     * @method Revokes all tokens and logout authenticated user session.
     */
    public function logoutAll()
    {
        Token::where('user_id', Auth::user()->user_id)->update(['revoked' => true]);
        return response(['message' => 'Logout from all devices successful and all user tokens are revoked'],200);
    }

    /**
     * @method Handles registration functionality.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'alpha_dash|required',
            'email' => 'email|required',
            'password' => 'required',
            'role_id' => 'required|gte:1|lte:2',
            'address' => 'required|alpha_dash',
            'profile_image' => 'max:1999',
            'current_school' => 'required|alpha_dash',
            'previous_school' => 'required|alpha_dash',
        ]);
        try {
            $data = new User;
            $data->name = $request['name'];
            $data->email = $request['email'];
            $data->password = bcrypt($request['password']);
            $data->role_id = $request['role_id'];
            $data->created_at = now();
            $data->save();

            //Create profile
            $data->profile()->create([
                'address' => $request['address'],
                'profile_image' => $request['profile_image'],
                'current_school' => $request['current_school'],
                'previous_school' => $request['previous_school'],
                'isApproved' => 0,
                'user_id' => $data->user_id,
                'created_at' => $data->created_at,
            ]);
            //Create data according to roles
            if ($data->role_id == 1) {
                $request->validate([
                    'parent_name' => 'required|alpha_dash',
                    'parent_contact' => 'required|alpha_dash',
                ]);
                $data->extendedStudent()->create([
                    "parent_name" => $request['parent_name'],
                    "parent_contact" => $request['parent_contact'],
                    "profile_id" => $data->profile->profile_id,
                    'created_at' => $data->created_at,
                ]);
            } elseif ($data->role_id == 2) {
                $request->validate([
                    'expertise_subject' => 'required|alpha_dash',
                    'experience' => 'required|numeric',
                ]);
                $data->extendedTeacher()->create([
                    "expertise_subject" => $request['expertise_subject'],
                    "experience" => $request['experience'],
                    "profile_id" => $data->profile->profile_id,
                    'created_at' => $data->created_at,
                ]);
            }
            //Registration Complete
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                // Authentication fails...
                return json_encode(['Error' => 'Authentication failed', 'statusCode' => '401']);
            }

            $token = auth()->user()->createToken(Auth::user());
            return json_encode([
                "User" => Auth::user(),
                "token" => $token->accessToken,
            ]);
        } catch (QueryException $qe) {
            if ($qe->getCode() == 23000) {
                return json_encode([
                    "Error" => "Email already exists",
                    "ErrorCode" => $qe->getCode(),
                ]);
            }
        }
    }

    /**
     * @method Handles user data update functions.
     *  not all fields are necessary!
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'regex:/^[a-zA-Z\s]+$/',
            'email' => 'email',
            'password' => 'string',
            'role_id' => 'gte:1|lte:2',
            'address' => 'alpha_dash',
            'profile_image' => 'max:1999',
            'current_school' => 'alpha_dash',
            'previous_school' => 'alpha_dash',
            'parent_name' => 'alpha_dash',
            'parent_contact' => 'numeric',
            'expertise_subject' => 'alpha_dash',
            'experience' => 'numeric',
        ]);
        $id = Auth::user()->user_id;
        $data = User::with('role', 'profile', 'extendedTeacher', 'extendedStudent')->find($id);
        if (is_null($data)) {
            return json_encode(
                [
                    'error' => [
                        "reason" => "required",
                        "message" => "User not found.",
                        "locationType" => "header",
                        "location" => "API",
                    ],
                    "statusCode" => 404,
                    "message" => "User not found in the database",
                ],
            );
        }
        $data->update([
            "name" => $request['name'] ?? $data->name,
            "email" => $request['email'] ?? $data->email,
            "password" => $request['password'] ?? $data->password,
            "role_id" => $data->role_id,
            "updated_at" => now(),
        ]);
        if (!is_null($data->profile)) {
            $data->profile()->update([
                "address" => $request['address'] ?? $data->profile->address,
                "profile_image" => $request['profile_image'] ?? $data->profile->profile_image,
                "current_school" => $request['current_school'] ?? $data->profile->current_school,
                "previous_school" => $request['previous_school'] ?? $data->profile->previous_school,
                "user_id" => $data->profile->user_id,
                "updated_at" => now(),
            ]);
        }
        if (!is_null($data->extendedTeacher) || !is_null($data->extendedStudent)) {
            if ($data->role_id == 1) {
                $data->extendedStudent()->update([
                    "parent_name" => $request['parent_name'] ?? $data->extendedStudent->parent_name,
                    "parent_contact" => $request['parent_contact'] ?? $data->extendedStudent->parent_contact,
                ]);
            } else {
                $data->extendedTeacher()->update([
                    "expertise_subject" => $request['expertise_subject'] ?? $data->extendedTeacher->expertise_subject,
                    "experience" => $request['experience'] ?? $data->extendedTeacher->experience,
                ]);
            }
        }

        return json_encode($data);
    }
}
