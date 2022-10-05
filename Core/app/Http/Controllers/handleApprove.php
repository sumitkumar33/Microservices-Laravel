<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class handleApprove extends Controller
{
    private $token;

    public function __construct(Request $request)
    {
        $this->token = $request->bearerToken() ?? '';
    }

    public function approve($id)
    {
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/approve/'.$id));
        return response()->json($data);
    }

    public function showApproved()
    {
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/approved'));
        return response()->json($data);
    }

    public function showApprovedStudents()
    {
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/approved/students'));
        return response()->json($data);
    }

    public function showApprovedTeachers()
    {
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/approved/teachers'));
        return response()->json($data);
    }

    public function showNotApproved()
    {
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/notApproved'));
        return response()->json($data);
    }

    public function showNotApprovedStudents()
    {
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/notApproved/students'));
        return response()->json($data);
    }

    public function showNotApprovedTeachers()
    {
        $data = json_decode(Http::withToken($this->token)->get('http://users.myproject.com/api/show/notApproved/teachers'));
        return response()->json($data);
    }

}
