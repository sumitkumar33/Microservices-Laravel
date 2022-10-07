<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Notifications\ApprovalNotification;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/a', function () {
    echo '<pre>';
    $user = User::find(4);
    $data = [
        'user_id' => $user->user_id,
        'name' => $user->name,
        'email' => $user->email,
        'admin_name' => 'Sumit Kumar',
    ];
    $user->notify(new ApprovalNotification($data));
    // $user->refresh();
    $user = User::find(4);
    foreach ($user->notifications as $notification) {
        print_r(($notification->data));
    }
});