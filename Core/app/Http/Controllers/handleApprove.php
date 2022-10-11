<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class handleApprove extends Controller
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
     * @method handles approve request from administrators.
     * @var int is the User ID of teacher.
     *  After successful request the teacher id provided will be marked as approved.
     */
    public function approve($id)
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/approve/'.$id));
        return response()->json(!is_null($data) ? $data : ['Message: ' => "User doesn't seem to have completed his profile."]);
    }

    /**
     * @method shows all approved users (Student, Teacher and Administrator).
     */
    public function showApproved()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/approved'));
        return response()->json($data);

    }

    /**
     * @method shows all approved students.
     */
    public function showApprovedStudents()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/approved/students'));
        return response()->json($data);
    }

    /**
     * @method shows all approved teachers.
     */
    public function showApprovedTeachers()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/approved/teachers'));
        return response()->json($data);
    }

    /**
     * @method shows all not approved users (Student, Teacher and Administrator).
     */
    public function showNotApproved()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/notApproved'));
        return response()->json($data);
    }

    /**
     * @method shows all not approved students.
     */
    public function showNotApprovedStudents()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/notApproved/students'));
        return response()->json($data);
    }

    /**
     * @method shows all not approved teachers.
     */
    public function showNotApprovedTeachers()
    {
        if ($this->token == null || $this->token == '') {
            return response()->json(['Message' => 'Unauthenticated.']);
        }
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/notApproved/teachers'));
        return response()->json($data);
    }

}
