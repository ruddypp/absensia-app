<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $month = $today->month;
        $year  = $today->year;

        if ($user->hasRole(['super_admin', 'hrd'])) {
            $data = [
                'total_employees'    => Employee::where('status', 'active')->count(),
                'present_today'      => Attendance::whereDate('attendance_date', $today)->where('status', 'present')->count(),
                'late_today'         => Attendance::whereDate('attendance_date', $today)->where('status', 'late')->count(),
                'absent_today'       => Attendance::whereDate('attendance_date', $today)->where('status', 'absent')->count(),
                'pending_leaves'     => LeaveRequest::where('status', 'pending')->count(),
                'pending_overtime'   => OvertimeRequest::where('status', 'pending')->count(),
                'total_payrolls'     => Payroll::where('period_month', $month)->where('period_year', $year)->count(),
                'recent_attendances' => Attendance::with('employee')->whereDate('attendance_date', $today)->latest()->take(10)->get(),
                'chart_attendance'   => $this->getAttendanceChartData($month, $year),
            ];
        } else {
            $employee       = $user->employee ?? null;
            $todayAttendance = $employee
                ? Attendance::where('employee_id', $employee->id)->whereDate('attendance_date', $today)->first()
                : null;

            $data = [
                'employee'        => $employee,
                'today_attendance' => $todayAttendance,
                'monthly_present'  => $employee ? Attendance::where('employee_id', $employee->id)
                    ->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)
                    ->whereIn('status', ['present', 'late'])->count() : 0,
                'monthly_late'     => $employee ? Attendance::where('employee_id', $employee->id)
                    ->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)
                    ->where('status', 'late')->count() : 0,
                'pending_leaves'   => $employee ? LeaveRequest::where('employee_id', $employee->id)->where('status', 'pending')->count() : 0,
                'latest_payroll'   => $employee ? Payroll::where('employee_id', $employee->id)->latest()->first() : null,
            ];
        }

        return view('dashboard', $data);
    }

    private function getAttendanceChartData(int $month, int $year): array
    {
        $days    = Carbon::create($year, $month)->daysInMonth;
        $labels  = [];
        $present = [];
        $late    = [];

        for ($d = 1; $d <= min($days, Carbon::today()->day); $d++) {
            $date      = Carbon::create($year, $month, $d);
            $labels[]  = $d;
            $present[] = Attendance::whereDate('attendance_date', $date)->where('status', 'present')->count();
            $late[]    = Attendance::whereDate('attendance_date', $date)->where('status', 'late')->count();
        }

        return compact('labels', 'present', 'late');
    }
}
