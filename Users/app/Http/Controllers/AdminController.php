<?php

namespace App\Http\Controllers;

use App\Jobs\AccountApproved;
use App\Jobs\NotifyStudent;
use App\Jobs\NotifyTeacher;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Facade\FlareClient\Http\Response;

class AdminController extends Controller
{
    public function setApprove($id)
    {
        $data = User::with('role', 'profile', 'extendedTeacher')->find($id);
        try {
            if(is_null($data->profile)) {
                throw new \Exception('Teacher has not completed his profile.');
            }
            if (is_null($data)) {
                throw new \ErrorException('UserID is not found in database.');
            }
            if ($data->role_id != 2) {
                throw new \ErrorException('Provided user id belongs to ' . $data->role->role);
            }
            $data->profile()->update([
                "isApproved" => 1,
            ]);
            $data2 = [
                'Message' => 'AccountApproved',
                'user_id' => $data->user_id,
                'name' => $data->name,
                'admin_name' => Auth::user()->name,
                'url' => url('/dashboard'),
            ];
            dispatch(new NotifyTeacher($data2, $data->user_id));
            return response()->json(["message" => "Teacher has been successfully approved."]);
        } catch (\ErrorException $e) {
            $err = "Error: ".$e->getMessage();
            return json_encode($err);
        }
    }

    public function setAssign(Request $request)
    {
        try {
            $request->validate([
                'student_user_id' => 'required|numeric',
                'teacher_user_id' => 'required|numeric',
            ]);
            $req = $request->only('student_user_id', 'teacher_user_id');
            $dataStudent = User::with('role', 'profile', 'extendedStudent', 'getAssignStudent')->find($req['student_user_id']);
            $dataTeacher = User::with('role', 'profile', 'extendedTeacher', 'getAssignTeacher')->find($req['teacher_user_id']);
            if ($dataStudent->role_id != 1 || $dataTeacher->role_id != 2) {
                throw new \ErrorException('User roles does not matche');
            }
            $dataStudent->getAssignStudent()->insert([
                "student_id" => $dataStudent->extendedStudent->student_id ?? $dataStudent->getAssignStudent->student_id ?? '',
                "teacher_id" => $dataTeacher->extendedTeacher->teacher_id ?? $dataTeacher->getAssignTeacher->teacher_id ?? '',
                "created_at" => now(),
                "updated_at" => now(),
            ]);
            $dataStudent->profile()->update([
                "isApproved" => 1,
            ]);
            $data = [
                'Message' => "StudentAssigned",
                'id' => $dataStudent->extendedStudent->student_id,
                'name' => $dataStudent->name,
                'email' => $dataStudent->email,
            ];
            //Notify Teacher for assign of student
            dispatch(new NotifyTeacher($data, $dataTeacher->user_id));
            //Notify Student for approval of account
            $data2 = [
                'Message' => 'AccountApproved',
                'user_id' => $dataStudent->user_id,
                'name' => $dataStudent->name,
                'admin_name' => Auth::user()->name,
                'url' => url('/dashboard'),
            ];
            dispatch(new NotifyStudent($data2, $dataStudent->user_id));
            return response()->json($dataStudent);
        } catch (\ErrorException $e) {
            return response()->json(['Message' => 'User roles do not match', 'Error' => $e->getMessage()]);
        } catch (QueryException $qe) {
            if ($qe->getCode() == 23000) {
                return response()->json([
                    "Message" => $dataStudent->name . ' has already been assigned to ' . $dataTeacher->name,
                    "Error" => $qe->getCode(),
                ]);
            } else {
                return response()->json(['Message' => 'User has not completed his profile.', 'ErrorCode' => $qe->getCode()]);
            }
        }
    }

    public function showApproved()
    {
        $data = User::with('getApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();
        foreach ($data as $d) {
            if (is_null($d->getApproved) || is_null($d->getApproved->profile_id))
                continue;
            else
                array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showApprovedStudents()
    {
        $data = User::with('getApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();
        foreach ($data as $d) {
            if (is_null($d->getApproved) || is_null($d->getApproved->profile_id) || $d->role_id != 1)
                continue;
            else
                array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showApprovedTeachers()
    {
        $data = User::with('getApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();
        foreach ($data as $d) {
            if (is_null($d->getApproved) || is_null($d->getApproved->profile_id) || $d->role_id != 2)
                continue;
            else
                array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showNotApproved()
    {
        $data = User::with('getNotApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();

        foreach ($data as $d) {
            if (($d->getNotApproved->isApproved ?? '') != 0) {
                continue;
            }
            array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showNotApprovedStudents()
    {
        $data = User::with('getNotApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();

        foreach ($data as $d) {
            if (($d->getNotApproved->isApproved ?? '') != 0 || $d->role_id != 1) {
                continue;
            }
            array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showNotApprovedTeachers()
    {
        $data = User::with('getNotApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();

        foreach ($data as $d) {
            if (($d->getNotApproved->isApproved ?? '') != 0 || $d->role_id != 2) {
                continue;
            }
            array_push($response, $d);
        }
        return response()->json($response);
    }
}
