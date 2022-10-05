<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

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

Route::prefix('/notifications')->group(function (){
    Route::get('/fetch/{id}', [NotificationController::class, 'fetch']);
    Route::get('/read/{id}', [NotificationController::class, 'markRead']);
    Route::get('/unread/{id}', [NotificationController::class, 'markUnread']);
    Route::get('/delete/{id}', [NotificationController::class, 'destroy']);
    Route::post('/notify/{id}', [NotificationController::class, 'notify']);
});
