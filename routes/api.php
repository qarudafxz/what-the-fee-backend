<?php

use Illuminate\Support\Facades\Route;

//controllers
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ExpensesController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ReceiptController;
use App\Http\Controllers\Api\LogsController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GCashController;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

//Student platform APIs
Route::middleware('auth:sanctum')->group(function () {
    //getting the remaining balance of the student
    Route::get('/balance/{id}', [
        PaymentController::class,
        'getStudentBalance',
    ]);
    //getting the payment logs of the student
    Route::get('/logs/{id}', [PaymentController::class, 'getStudentLogs']);
    //getting the total amount being paid by the student
    Route::get('/student-payment/{id}', [
        PaymentController::class,
        'getTotalPaymentOfStudent',
    ]);
    //getting the total amount being collected by the college
    Route::get('/college/{id}', [PaymentController::class, 'getTotalPayment']);
});

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
        Route::get('/latest-payment', 'getLastPaymentAr');
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
        //archive receipt
        Route::post('/archive-receipt/{ar_no}', 'archiveReceipt');
        Route::get('/archives', 'getArchivedReceipts');
        Route::post('/restore-receipt/{ar_no}', 'restoreReceiptFromArchives');
    });

//permission apis ===================================================================

Route::controller(PermissionController::class)
    // ->middleware('admin')
    ->group(function () {
        //get all permissions
        Route::get('/permissions', 'getAllPermissionsOfAllAdmins');
        //update permission of an admin
        Route::put('/can-update-permission/{admin_id}', 'canUpdatePermission');
        Route::put('/can-delete-permission/{admin_id}', 'canDeletePermission');
    });

//Logs apis ===================================================================
Route::controller(LogsController::class)
    ->middleware('admin')
    ->group(function () {
        //get all the logs
        Route::get('/logs', 'getLogs');
    });

//Requests apis =======================================================================
Route::controller(RequestController::class)
    // ->middleware('admin')
    ->group(function () {
        //get all requests
        Route::get('/requests', 'getAllRequests');
        //creating a new request
        Route::post('/create-request', 'createRequest');
        Route::get('/request/{id}', 'getSelectedRequest');
        //grant request
        Route::post('/grant-request/{id}', 'grantRequest');
        //decline request
        Route::post('/decline-request/{id}', 'declineRequest');
    });

//Gcash payment controller
Route::controller(GCashController::class)->group(function () {
    Route::get('/pay/{student_id}/{amount}', 'pay');
    Route::post('/webhook', 'webhook');
});
