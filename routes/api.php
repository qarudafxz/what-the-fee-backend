<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//controllers
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\StudentController;

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

//search student
Route::get('/search-student/{student_id}', [
    PaymentController::class,
    'searchStudent',
]);

//get all the list of admin
Route::get('/get-admin/{college_id}', [
    AdminController::class,
    'getAllAdminSpecificCollege',
]);

//add payment
Route::post('/add-payment', [PaymentController::class, 'addPayment']);

//get the sum of total payments in a specific college
Route::get('/get-total-payment/{college_id}', [
    PaymentController::class,
    'getTotalPayment',
]);
//get all payment
Route::get('/get-all-payment', [PaymentController::class, 'getAllPayment']);
//get latest payment
Route::get('/get-latest-payment', [
    PaymentController::class,
    'getLastPaymentAr',
]);

//get data of all progrma
Route::get('/get-count-programs', [
    ProgramController::class,
    'getCountOfPrograms',
]);

// // get specific student record
// Route::get(
//     '/data-student' . [PaymentController::class, 'getPaymentByStudentId']
// );
