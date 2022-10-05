<?php

use App\Http\Controllers\handleApprove;
use App\Http\Controllers\handleAuth;
use App\Http\Controllers\handleAssign;
use Illuminate\Http\Request;
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

Route::get('/users', [handleAuth::class, 'index']);
Route::post('/update', [handleAuth::class, 'authUpdate']);
Route::post('/login', [handleAuth::class, 'authLogin']);
Route::get('/logout', [handleAuth::class, 'authLogout']);
Route::get('/logoutAll', [handleAuth::class, 'authLogoutAll']);
Route::post('/register', [handleAuth::class, 'authRegister']);

//Handle Approved 
Route::get('/approve/{id}', [handleApprove::class, 'approve']);
Route::get('/show/approved', [handleApprove::class, 'showApproved']);
Route::get('/show/approved/students', [handleApprove::class, 'showApprovedStudents']);
Route::get('/show/approved/teachers', [handleApprove::class, 'showApprovedTeachers']);
Route::get('/show/notApproved', [handleApprove::class, 'showNotApproved']);
Route::get('/show/notApproved/students', [handleApprove::class, 'showNotApprovedStudents']);
Route::get('/show/notApproved/teachers', [handleApprove::class, 'showNotApprovedTeachers']);

//Handle Assign
Route::post('/assign', [handleAssign::class, 'assign']);