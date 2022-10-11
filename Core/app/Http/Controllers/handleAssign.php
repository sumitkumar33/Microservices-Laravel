<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class handleAssign extends Controller
{
    /**
     * stores the authentication bearerToken.
     * @var string
     */
    public $token;

    /**
     * @method is called when class is instantiated.
     *  also used to store the authentication token.
     */
    public function __construct(Request $request)
    {
        $this->token = $request->bearerToken() ?? '';
    }

    /**
     * @method handles assign request from administrators
     *  After successful request student will be assigned to a teacher.
     */
    public function assign(Request $request)
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
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
