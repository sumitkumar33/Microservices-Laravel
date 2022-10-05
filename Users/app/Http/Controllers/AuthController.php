<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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

    public function logout()
    {
        auth()->user()->token()->revoke();
        return json_encode(['Message' => 'Successfully logged out.']);
    }

    public function logoutAll()
    {
        Token::where('user_id', Auth::user()->user_id)->update(['revoked' => true]);
        return json_encode(['message' => 'Logout from all devices successful and all user tokens are revoked']);
    }

    public function register(Request $request)
    {
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
                $data->extendedStudent()->create([
                    "parent_name" => $request['parent_name'],
                    "parent_contact" => $request['parent_contact'],
                    "profile_id" => $data->profile->profile_id,
                    'created_at' => $data->created_at,
                ]);
            } elseif ($data->role_id == 2) {
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

    public function update(Request $request)
    {
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
