<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\SalaryComponentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\WorkLocationController;
use App\Http\Controllers\ReportController;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::resource('employees', EmployeeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('work-locations', WorkLocationController::class);
    Route::resource('salary-components', SalaryComponentController::class);

    // Absensi
    Route::prefix('attendances')->name('attendances.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/my', [AttendanceController::class, 'my'])->name('my');
        Route::get('/checkin', [AttendanceController::class, 'checkInPage'])->name('checkin');
        Route::post('/checkin', [AttendanceController::class, 'checkIn'])->name('checkin.store');
        Route::post('/checkout', [AttendanceController::class, 'checkOut'])->name('checkout.store');
        Route::get('/correction/{attendance}', [AttendanceController::class, 'correctionForm'])->name('correction');
        Route::put('/correction/{attendance}', [AttendanceController::class, 'correct'])->name('correction.update');
    });

    // Cuti
    Route::resource('leave-requests', LeaveRequestController::class);
    Route::patch('leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

    // Lembur
    Route::resource('overtime-requests', OvertimeRequestController::class);
    Route::patch('overtime-requests/{overtimeRequest}/approve', [OvertimeRequestController::class, 'approve'])->name('overtime-requests.approve');
    Route::patch('overtime-requests/{overtimeRequest}/reject', [OvertimeRequestController::class, 'reject'])->name('overtime-requests.reject');

    // Payroll
    Route::prefix('payrolls')->name('payrolls.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/my', [PayrollController::class, 'my'])->name('my');
        Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
        Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::patch('/{payroll}/approve', [PayrollController::class, 'approve'])->name('approve');
        Route::patch('/{payroll}/paid', [PayrollController::class, 'markPaid'])->name('paid');
        Route::get('/{payroll}/slip', [PayrollController::class, 'downloadSlip'])->name('slip');
    });

    // Bonus
    Route::resource('bonuses', BonusController::class);
    Route::patch('bonuses/{bonus}/approve', [BonusController::class, 'approve'])->name('bonuses.approve');

    // Laporan
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/payroll', [ReportController::class, 'payroll'])->name('payroll');
        Route::get('/attendance/export', [ReportController::class, 'exportAttendance'])->name('attendance.export');
        Route::get('/payroll/export', [ReportController::class, 'exportPayroll'])->name('payroll.export');
    });

    // API untuk absensi (AJAX)
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/attendance/validate-location', [AttendanceController::class, 'validateLocation'])->name('attendance.validate-location');
        Route::get('/attendance/today-status', [AttendanceController::class, 'todayStatus'])->name('attendance.today-status');
    });
});

require __DIR__.'/auth.php';