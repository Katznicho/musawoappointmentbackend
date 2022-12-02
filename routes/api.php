<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\RequestController;
use Illuminate\Support\Facades\Mail;

//registration and login Apis
Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [RegisterController::class, 'login']);
Route::post('/confirm', [OtpController::class, 'verify']);
Route::post('/resend', [OtpController::class, 'resend']);

//push Token

Route::post('/retrieveToken/{id}', [OtpController::class, 'retrieveToken']);
Route::post('/updateToken/{id}', [OtpController::class, 'updateToken']);


//forgot Password Apis
Route::post('/forgotPassword', [OtpController::class, 'forgotPassword']);
Route::post('/verifyOtp', [OtpController::class, 'verifyOtp']);
Route::post('/resetPassword/{email}', [OtpController::class, 'resetPassword']);


//Doctor Update Apis
Route::get('/get-workers', [App\Http\Controllers\DoctorController::class, 'doctors']);
Route::post('/update-doctor/{id}', [App\Http\Controllers\DoctorController::class, 'updateDoctor']);
Route::post('/activate-doctor/{id}', [App\Http\Controllers\DoctorController::class, 'activateDoctor']);
Route::post('/deactivate-doctor/{id}', [App\Http\Controllers\DoctorController::class, 'deactivateDoctor']);
Route::post('/doctor-status/{id}', [App\Http\Controllers\DoctorController::class, 'DoctorStatus']);
//Client Update Apis
Route::post('/update-client/{id}', [App\Http\Controllers\ClientController::class, 'updateClient']);
Route::get('/get-clients', [App\Http\Controllers\ClientController::class, 'clients']);

//request APis
Route::get('/getDoctor/{id}', [RequestController::class, 'getDoctor']);
Route::post('/acceptRequest/{id}', [RequestController::class, 'acceptRequest']);
Route::post('/cancelRequest/{id}', [RequestController::class, 'cancelRequest']);
Route::post('/getRequests/{id}', [RequestController::class, 'doctorRequests']);
Route::post('/cancelClient/{id}', [RequestController::class, 'cancelRequestClient']);
Route::post('/completeRequest/{id}', [RequestController::class, 'completeRequest']);
 //history
Route::post('/doctorHistory/{id}', [RequestController::class, 'doctorHistory']);
Route::post('/clientHistory/{id}', [RequestController::class, 'clientHistory']);
Route::post('/currentRequest/{id}', [RequestController::class, 'currentRequest']);
Route::post('/completeClient/{id}', [RequestController::class, 'completeClient']);

//Laboratory Services
Route::post('/requestService/{id}', [App\Http\Controllers\LabController::class, 'requestService']);
Route::post('/allServices', [App\Http\Controllers\LabController::class, 'allServices']);
Route::post('/cancelLabRequest/{id}', [App\Http\Controllers\LabController::class, 'cancelLabRequest']);
Route::post('/rateLabRequest/{id}', [App\Http\Controllers\LabController::class, 'rateLabRequest']);
Route::post('/currentLabRequest/{id}', [App\Http\Controllers\LabController::class, 'currentLabRequest']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


