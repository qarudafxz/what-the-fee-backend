<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//controllers
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//get all the list of admin
Route::get('/get-admin', [
    AdminController::class,
    'getAllAdminSpecificCollege',
]);

//add payment
Route::post('/add-payment', [PaymentController::class, 'addPayment']);
Route::get('/get-all-payment/{college_id}', [
    PaymentController::class,
    'getTotalPayment',
]);
