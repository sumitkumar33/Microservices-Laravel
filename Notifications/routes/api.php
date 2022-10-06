<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Jobs\AdminDigest;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/approveMail', function (Request $request){
    $request->validate([
        'email' => 'required',
        'name' => 'required',
        'admin_name' => 'required',
    ]);
    $email = $request->get('email');
    $data = $request->only('name', 'admin_name', 'email');
    Mail::to($email)->send(new \App\Mail\AccountApprove($data));
    return json_encode(['Message'=>'Mail sent successfully.']);
});

Route::post('/DailyDigest', function (Request $request){
    $request->validate([
        'name' => 'required',
        'email' => 'required',
        'count' => 'required',
    ]);
    $data = $request->only('name', 'count', 'email');
    Mail::to($request['email'])->send(new \App\Mail\AdminDigest($data));
});

Route::prefix('/notifications')->group(function (){
    Route::get('/fetch/{id}', [NotificationController::class, 'fetch']);
    Route::get('/read/{id}', [NotificationController::class, 'markRead']);
    Route::get('/unread/{id}', [NotificationController::class, 'markUnread']);
    Route::get('/delete/{id}', [NotificationController::class, 'destroy']);
    Route::post('/notify/{id}', [NotificationController::class, 'notify']);
});
