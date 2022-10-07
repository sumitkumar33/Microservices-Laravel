<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\ApprovalNotification;
use App\Notifications\AssignNotification;

class NotificationController extends Controller
{
    public function fetch($id)
    {
        $user = User::find($id)->notifications;
        return json_encode($user);
    }

    public function notify(Request $request, $id)
    {
        $user = User::find($id);
        $data = $request->only('message', 'user_id', 'name', 'email', 'admin_name');
        if ($request['message'] == 'AccountApproved') {
            $user->notify(new ApprovalNotification($data));
        } elseif ($request['message'] == 'StudentAssigned') {
            $user->notify(new AssignNotification($data));
        } else {
            return json_encode(['message' => 'Cannot store notification try again later.']);
        }
    }

    public function markRead($id)
    {
        $user = User::find($id);
        $user->unreadNotifications->markAsRead();
        return json_encode(['Message' => 'Marked all unread notifications Read.'], 200);
    }

    public function markUnread($id)
    {
        $user = User::find($id);
        $user->notifications->markAsUnread();
        return json_encode(['Message' => 'Marked all read notifications Unread.'], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->notifications()->delete();
        return json_encode(["Message" => 'All notifications have been deleted from table.']);
    }
}
