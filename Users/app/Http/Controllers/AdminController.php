<?php

namespace App\Http\Controllers;

use App\Jobs\AccountApprove;
use App\Jobs\NotifyTeacher;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * @method Set the teacher profile as approved.
     */
    public function setApprove($id)
    {
        $data = User::with('role', 'profile', 'extendedTeacher')->find($id);
        try {
            if (is_null($data->profile)) {
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
                'message' => 'AccountApproved',
                'user_id' => $data->user_id,
                'email' => $data->email,
                'name' => $data->name,
                'admin_name' => Auth::user()->name,
                'url' => url('/dashboard'),
            ];
            //Once the student/teacher account has been approved by the admin, trigger a mail to the
            //respective user.
            //Create in app notifications for above 2 cases.
            dispatch(new AccountApprove($data2, $data->user_id));
            return response()->json(["message" => "Teacher has been successfully approved."]);
        } catch (\ErrorException $e) {
            $err = "Error: " . $e->getMessage();
            return json_encode($err);
        }
    }

    /**
     * @method Assigns student_user_id to teacher_user_id in the database.
     *  Approves student profile after assigning of teacher.
     */
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
                'message' => "StudentAssigned",
                'user_id' => $dataStudent->extendedStudent->student_id,
                'name' => $dataStudent->name,
                'email' => $dataStudent->email,
                'admin_name' => Auth::user()->name,
            ];
            //Dispatch DB Notifications
            //Create a notification for the teacher, when there is a new student assigned to him.
            dispatch(new NotifyTeacher($data, $dataTeacher->user_id));
            $data2 = [
                'message' => 'AccountApproved',
                'user_id' => $dataStudent->user_id,
                'email' => $dataStudent->email,
                'name' => $dataStudent->name,
                'admin_name' => Auth::user()->name,
                'url' => url('/dashboard'),
            ];
            //Once the student/teacher account has been approved by the admin, trigger a mail to the
            //respective user.
            //Create in app notifications for above 2 cases.
            dispatch(new AccountApprove($data2, $dataStudent->user_id));
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

    /**
     * @method Returns all approved users from the database.
     */
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

    /**
     * @method Returns all approved students from the database.
     */
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

    /**
     * @method Returns all approved teachers from the database.
     */
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

    /**
     * @method Returns all not approved users from the database.
     */
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

    /**
     * @method Returns all not approved students from the database.
     */
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

    /**
     * @method Returns all not approved teachers from the database.
     */
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
