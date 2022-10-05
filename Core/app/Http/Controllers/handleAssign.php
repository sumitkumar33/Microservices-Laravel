<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class handleAssign extends Controller
{
    public $token;

    public function __construct(Request $request)
    {
        $this->token = $request->bearerToken() ?? '';
    }

    public function assign(Request $request)
    {
        $request->validate([
            'student_user_id' => 'required|numeric',
            'teacher_user_id' => 'required|numeric',
        ]);
        $data = $request->only('student_user_id', 'teacher_user_id');
        $result = json_decode(Http::withToken($this->token)
                    ->post('http://users.myproject.com/api/assign', $data));
        return response()->json($result);
    }
}
