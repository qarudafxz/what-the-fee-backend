<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//controllers
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ExpensesController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ReceiptController;

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

//payment apis ===================================================================

Route::controller(PaymentController::class)
    ->middleware('admin')
    ->group(function () {
        //logs of the students about their payment
        Route::get('/student-logs/{student_id}', 'getStudentLogs');
        //get all the list of payments of college fee of a specific college
        Route::get('/get-all-payment/{college_id}', 'getAllPaymentOfCollege');
        // search a specific student
        Route::get('/search-student/{student_id}', 'searchStudent');
        //add new payment of college fee
        Route::post('/add-payment', 'addPayment');
        //get the total collection of college fee of a specific college
        Route::get('/get-total-payment/{college_id}', 'getTotalPayment');
        //get all payment
        Route::get('/get-all-payment', 'getAllPayment');
        //this is for scanning the qr code of the student, to get the student id
        Route::get('/get-student-payment/{id}', 'getStudentBalance');
        //get the latest payment
        Route::get('/get-latest-payment', 'getLastPaymentAr');
        //get the last 7 days of collection of a specific college with percentange
        Route::get('/last-7-days/{id}', 'getPercentageOfLast7daysCollection');
        //get the last 30 days of collection of a specific college with percentange
        Route::get('/last-30-days/{id}', 'getPercentageOfLast30daysCollection');
        //get the total amount of already paid of a specific student
        Route::get(
            '/get-total-student-payment/{id}',
            'getTotalPaymentOfStudent'
        );
        //get the total amount collected per month in a specific college
        Route::get(
            '/get-total-payment-per-month/{id}',
            'getTotalPaymentPerMonthInCurrentYear'
        );
        //save the receipt of the student
        Route::post('/save-receipt/{ar_no}', 'savePayment');
    });

//admin apis ===================================================================

Route::controller(AdminController::class)
    ->middleware('admin')
    ->group(function () {
        //get all the admins under a specific college
        Route::get('/get-admin/{college_id}', 'getAllAdminSpecificCollege');
    })
    ->middleware('admin');

//program apis ===================================================================

Route::controller(ProgramController::class)->group(function () {
    //get the total number of student per program
    Route::get('/get-count-programs', 'getCountOfPrograms');
});

//expenses apis ===================================================================

Route::controller(ExpensesController::class)
    ->middleware('admin')
    ->group(function () {
        //get all expenses of a specific college
        Route::get('/expenses/{college_id}', 'getAllExpensesInSpecificCollege');
        //add new expenses
        Route::post('/add-expenses/{college_id}', 'addNewExpenses');
    });

//receipt apis ===================================================================

Route::controller(ReceiptController::class)
    ->middleware('admin')
    ->group(function () {
        //get all receipts
        Route::get('/receipts', 'getReceipts');
        //get all information of receipt
        Route::get('/receipt/{ar_no}', 'getFullDetailsOfReceipt');
    });

//permission apis ===================================================================

Route::controller(PermissionController::class)
    ->middleware('admin')
    ->group(function () {
        //get all permissions
        Route::get('/permissions', 'getAllPermissionsOfAllAdmins');
        //update permission of an admin
        Route::put('/update-permission/{admin_id}', 'updatePermission');
    });
