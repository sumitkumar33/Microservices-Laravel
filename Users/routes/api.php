<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\Route;

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



Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/update', [AuthController::class, 'update']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/logoutAll', [AuthController::class, 'logoutAll']);
    Route::get('/users', function () {return json_encode(User::all());});
    //Notifications Crud for logged in users
    Route::get('/user/notifications', [NotificationsController::class, 'index']);
    Route::get('/user/notifications/markRead', [NotificationsController::class, 'markRead']);
    Route::get('/user/notifications/markUnread', [NotificationsController::class, 'markUnread']);
    Route::get('/user/notifications/delete', [NotificationsController::class, 'destroy']);
});

Route::group(['middleware' => ['auth:api', 'isAdmin']], function () {
    //Create a new notification for assign / approve
    Route::post('/notifications/create/{id}', [NotificationsController::class, 'makeNotification']);

    Route::post('/assign', [AdminController::class, 'setAssign']);
    Route::get('/approve/{id}', [AdminController::class, 'setApprove']);
    Route::get('/show/approved', [AdminController::class, 'showApproved']);
    Route::get('/show/approved/students', [AdminController::class, 'showApprovedStudents']);
    Route::get('/show/approved/teachers', [AdminController::class, 'showApprovedTeachers']);
    Route::get('/show/notApproved', [AdminController::class, 'showNotApproved']);
    Route::get('/show/notApproved/students', [AdminController::class, 'showNotApprovedStudents']);
    Route::get('/show/notApproved/teachers', [AdminController::class, 'showNotApprovedTeachers']);
});
