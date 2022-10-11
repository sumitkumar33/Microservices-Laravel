<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\ApprovalNotification;
use App\Notifications\AssignNotification;

class NotificationController extends Controller
{
    /**
     * @method Fetches all the notifications that belongs to user $id
     */
    public function fetch($id)
    {
        $user = User::find($id)->notifications;
        return json_encode($user);
    }

    /**
     * @method Stores the notifications in the database for
     *  Account approved and Student Assigned events.
     */
    public function notify(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|alpha_dash',
            'user_id' => 'required|numeric',
            'name' => 'required|alpha_dash',
            'email' => 'required|email',
            'admin_name' => 'required|alpha_dash',
        ]);
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

    /**
     * @method marks all the notifications as read.
     */
    public function markRead($id)
    {
        $user = User::find($id);
        $user->unreadNotifications->markAsRead();
        return json_encode(['Message' => 'Marked all unread notifications Read.'], 200);
    }

    /**
     * @method marks all the notifications as unread.
     */
    public function markUnread($id)
    {
        $user = User::find($id);
        $user->notifications->markAsUnread();
        return json_encode(['Message' => 'Marked all read notifications Unread.'], 200);
    }

    /**
     * @method deletes all the notifications belongs to user $id.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->notifications()->delete();
        return json_encode(["Message" => 'All notifications have been deleted from table.']);
    }
}
