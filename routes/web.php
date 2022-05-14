<?php

use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentsController;
use Illuminate\Support\Facades\Route;

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
})->name('homepage');

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::group(['middleware' => ['auth:finance']], function () {
    Route::get('finance/dashboard', [FinanceController::class, 'index'])->name('finance.dashboard');
    Route::get('finance/dashboard/students', [FinanceController::class, 'showStudents'])->name('finance.students');
    Route::get('finance/dashboard/student/{student}', [FinanceController::class, 'showStudentInfo'])->name('finance.studentInfo');
    Route::get('finance/dashboard/payments', [FinanceController::class, 'showPayments'])->name('finance.payments');
    Route::get('finance/dashboard/reports/students', [FinanceController::class, 'showStudentsReport'])->name('reports.students');
    Route::post('finance/dashboard/reports/students', [FinanceController::class, 'reportStudents'])->name('reports.students.filter');
    Route::get('finance/dashboard/reports/students/pdf', [FinanceController::class, 'students_report_pdf'])->name('reports.students.pdf');
});

Route::post('/payments', [PaymentsController::class, 'store'])->middleware(['auth'])->name('payments.store');

require __DIR__ . '/auth.php';
