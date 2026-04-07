<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $this->authorize('view reports');
        $month       = $request->get('month', Carbon::now()->month);
        $year        = $request->get('year', Carbon::now()->year);
        $departmentId = $request->get('department_id');
        $employeeId  = $request->get('employee_id');

        $query = Attendance::with('employee.department')
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year);

        if ($departmentId) {
            $query->whereHas('employee', fn($q) => $q->where('department_id', $departmentId));
        }
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->orderBy('attendance_date')->paginate(30);
        $departments = Department::all();
        $employees   = Employee::where('status', 'active')->get();

        return view('reports.attendance', compact('attendances', 'departments', 'employees', 'month', 'year', 'departmentId', 'employeeId'));
    }

    public function payroll(Request $request)
    {
        $this->authorize('view reports');
        $month = $request->get('month', Carbon::now()->month);
        $year  = $request->get('year', Carbon::now()->year);

        $payrolls    = Payroll::with('employee.department')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->paginate(30);
        $departments = Department::all();

        return view('reports.payroll', compact('payrolls', 'departments', 'month', 'year'));
    }

    public function exportAttendance(Request $request)
    {
        $this->authorize('export reports');
        // Export Excel (simple implementation)
        $month = $request->get('month', Carbon::now()->month);
        $year  = $request->get('year', Carbon::now()->year);

        $data = Attendance::with('employee.department')
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();

        $rows   = [['No', 'Karyawan', 'Departemen', 'Tanggal', 'Check-in', 'Check-out', 'Status', 'Terlambat (mnt)']];
        $no     = 1;
        foreach ($data as $att) {
            $rows[] = [
                $no++,
                $att->employee->name ?? '-',
                $att->employee->department->name ?? '-',
                $att->attendance_date->format('d/m/Y'),
                $att->check_in ?? '-',
                $att->check_out ?? '-',
                $att->status,
                $att->late_minutes,
            ];
        }

        $filename = 'laporan-absensi-' . $month . '-' . $year . '.csv';
        $handle   = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPayroll(Request $request)
    {
        $this->authorize('export reports');
        $month = $request->get('month', Carbon::now()->month);
        $year  = $request->get('year', Carbon::now()->year);

        $data = Payroll::with('employee.department')
            ->where('period_month', $month)->where('period_year', $year)->get();

        $rows = [['No', 'Karyawan', 'Departemen', 'Gaji Pokok', 'Tunjangan', 'Potongan', 'Bonus', 'Lembur', 'Gaji Bersih', 'Status']];
        $no   = 1;
        foreach ($data as $p) {
            $rows[] = [
                $no++,
                $p->employee->name ?? '-',
                $p->employee->department->name ?? '-',
                $p->base_salary,
                $p->total_allowance,
                $p->total_deduction,
                $p->total_bonus,
                $p->total_overtime,
                $p->net_salary,
                $p->status,
            ];
        }

        $filename = 'laporan-gaji-' . $month . '-' . $year . '.csv';
        $handle   = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
