<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PaymentController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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


Route::get('/addDoctor', function () {
    return view('addDoctor');
})->name('addDoctor');

Route::get('/addLabService', function () {
    return view('lab.addLabService');
});

// Route::get('/clients', function () {
//     return view('clients');
// });

Route::post('/addDoctor', [DoctorController::class, 'store']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');


Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/activityLogs', [App\Http\Controllers\HomeController::class, 'activityLogs'])->name('activityLogs');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/healthworkers', [App\Http\Controllers\DoctorController::class, 'index'])->name('healthworkers');
Route::get('/edit-doctor/{id}', [App\Http\Controllers\DoctorController::class, 'edit']);
Route::post('/update-doctor/{id}', [App\Http\Controllers\DoctorController::class, 'update']);
Route::get('/clients', [App\Http\Controllers\ClientController::class, 'index']);

Auth::routes(['verify' => true]);
Route::get('delete-doctor/{id}', [DoctorController::class, 'destroy']);

Route::get('/admin', [App\Http\Controllers\HomeController::class, "index"])->name('admin')->middleware('verified');
Auth::routes();

Route::get('/labServices', [App\Http\Controllers\LabController::class, 'index'])->name('labServices');
Route::get('/labRequest', [App\Http\Controllers\LabController::class, 'displayLabRequests'])->name('labRequest');

Route::post('/import', [App\Http\Controllers\LabController::class, 'importLabServices'])->name('import');

Route::post('/addLabService', [App\Http\Controllers\LabController::class, 'create']);
Route::get('/editLabService/{id}', [App\Http\Controllers\LabController::class, 'edit']);
Route::post('/update-service/{id}', [App\Http\Controllers\LabController::class, 'update']);
Route::get('delete-service/{id}', [App\Http\Controllers\LabController::class, 'destroy']);
Route::get('/Requests', [App\Http\Controllers\Api\RequestController::class, 'showRequests'])->name('Requests');

Route::post('/update-request/{id}', [App\Http\Controllers\Api\RequestController::class, 'update']);
Route::get('/edit-request/{id}', [App\Http\Controllers\Api\RequestController::class, 'edit']);
Route::get('delete-request/{id}', [App\Http\Controllers\Api\RequestController::class, 'destroy']);
//show details
Route::get('/show-details/{id}', [App\Http\Controllers\Api\RequestController::class, 'showDetails']);

Route::post('/update-LabRequest/{id}', [App\Http\Controllers\LabController::class, 'updateRequest']);
Route::get('/edit-LabRequest/{id}', [App\Http\Controllers\LabController::class, 'editRequest']);
Route::get('delete-LabRequest/{id}', [App\Http\Controllers\LabController::class, 'destroyRequest']);

//create payment resource
Route::resource('payments', PaymentController::class);
