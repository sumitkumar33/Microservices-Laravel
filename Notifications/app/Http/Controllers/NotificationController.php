<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\dbNotify;
use PDO;

class NotificationController extends Controller
{
    public function fetch($id)
    {
        $data = dbNotify::where('user_id', '=', $id)->get();
        if ($data->count() == 0) {
            return json_encode(['Message' => 'No notification found.']);
        } else {
            return json_encode($data);
        }
    }

    public function notify(Request $request, $id)
    {
        $data = $request->all();
        $data = dbNotify::insert([
            'type' => 'Notification',
            'data' => json_encode($data),
            'user_id' => $id,
        ]);
        if ($data) {
            return json_encode($data);
        } else {
            return json_encode(['Message' => 'Cannot register the notification.']);
        }
    }

    public function markRead($id)
    {
        $datas = dbNotify::where('user_id', '=', $id)->get();
        foreach ($datas as $data) {
            $data->update([
                'read_at' => now(),
            ]);
        }
        return json_encode(["Message" => 'All notifications has been marked as read.']);
    }

    public function markUnread($id)
    {
        $datas = dbNotify::where('user_id', '=', $id)->get();
        foreach ($datas as $data) {
            $data->update([
                'read_at' => NULL,
            ]);
        }
        return json_encode(["Message" => 'All notifications has been marked as unread.']);
    }

    public function destroy($id)
    {
        dbNotify::where('user_id', '=', $id)->delete();
        return json_encode(["Message" => 'All notifications have been deleted from table.']);
    }
}
