<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Bonus;
use App\Models\OvertimeRequest;
use App\Models\SalaryComponent;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index()
    {
        $this->authorize('view all payrolls');
        $month = request('month', Carbon::now()->month);
        $year  = request('year', Carbon::now()->year);

        $payrolls = Payroll::with('employee.department')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->paginate(20);

        $employees = Employee::where('status', 'active')->get();
        return view('payrolls.index', compact('payrolls', 'month', 'year', 'employees'));
    }

    public function my()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Akun Anda belum terhubung ke data karyawan.');
        }
        $payrolls = Payroll::where('employee_id', $employee->id)->latest()->paginate(12);
        return view('payrolls.my', compact('payrolls', 'employee'));
    }

    public function generate(Request $request)
    {
        $this->authorize('generate payroll');
        $request->validate([
            'month'        => 'required|integer|between:1,12',
            'year'         => 'required|integer',
            'employee_ids' => 'required|array',
        ]);

        $generated = 0;
        foreach ($request->employee_ids as $empId) {
            $employee = Employee::find($empId);
            if (!$employee) continue;

            $payroll = $this->generateForEmployee($employee, $request->month, $request->year);
            if ($payroll) $generated++;
        }

        return response()->json(['success' => true, 'generated' => $generated]);
    }

    private function generateForEmployee(Employee $employee, int $month, int $year): ?Payroll
    {
        $existing = Payroll::where('employee_id', $employee->id)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->first();
        if ($existing) return $existing;

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $workingDays = 0;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $day = Carbon::create($year, $month, $d)->dayOfWeek;
            if ($day >= 1 && $day <= 5) $workingDays++;
        }

        $attendances  = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)->get();
        $presentDays  = $attendances->whereIn('status', ['present', 'late'])->count();
        $absentDays   = max(0, $workingDays - $presentDays - $attendances->where('status', 'leave')->count());
        $lateCount    = $attendances->where('status', 'late')->count();
        $lateMinutes  = $attendances->sum('late_minutes');

        $baseSalary   = (float) $employee->position->base_salary;
        $dailyRate    = $workingDays > 0 ? $baseSalary / $workingDays : 0;
        $hourlyRate   = $workingDays > 0 ? $baseSalary / ($workingDays * 8) : 0;

        $components     = SalaryComponent::where('is_active', true)->get();
        $totalAllowance = 0;
        $totalDeduction = 0;
        $details        = [];

        foreach ($components as $comp) {
            $amount = $comp->calculation_type === 'fixed'
                ? (float) $comp->amount
                : ($baseSalary * (float) $comp->amount / 100);

            if ($comp->type === 'allowance') {
                $totalAllowance += $amount;
                $details[] = ['label' => $comp->name, 'type' => 'allowance', 'amount' => $amount, 'salary_component_id' => $comp->id];
            } else {
                $totalDeduction += $amount;
                $details[] = ['label' => $comp->name, 'type' => 'deduction', 'amount' => $amount, 'salary_component_id' => $comp->id];
            }
        }

        $lateDeduction   = min($lateMinutes * 5000, $baseSalary * 0.1);
        $absentDeduction = $absentDays * $dailyRate;
        $totalDeduction += $lateDeduction + $absentDeduction;

        $bonuses     = Bonus::where('employee_id', $employee->id)->where('status', 'approved')
            ->whereMonth('bonus_date', $month)->whereYear('bonus_date', $year)->whereNull('payroll_id')->get();
        $totalBonus  = $bonuses->sum('amount');

        $overtimes     = OvertimeRequest::where('employee_id', $employee->id)->where('status', 'approved')
            ->whereMonth('overtime_date', $month)->whereYear('overtime_date', $year)->get();
        $totalOvertime = $overtimes->sum('total_pay');

        $netSalary = $baseSalary + $totalAllowance + $totalBonus + $totalOvertime - $totalDeduction;

        $payroll = Payroll::create([
            'employee_id'         => $employee->id,
            'period_month'        => $month,
            'period_year'         => $year,
            'base_salary'         => $baseSalary,
            'total_allowance'     => $totalAllowance,
            'total_deduction'     => $totalDeduction,
            'total_bonus'         => $totalBonus,
            'total_overtime'      => $totalOvertime,
            'total_late_deduction'=> $lateDeduction,
            'net_salary'          => $netSalary,
            'working_days'        => $workingDays,
            'present_days'        => $presentDays,
            'absent_days'         => $absentDays,
            'late_count'          => $lateCount,
        ]);

        foreach ($details as $detail) {
            $payroll->details()->create($detail);
        }

        foreach ($bonuses as $bonus) {
            $payroll->details()->create(['label' => $bonus->title, 'type' => 'bonus', 'amount' => (float)$bonus->amount]);
            $bonus->update(['payroll_id' => $payroll->id, 'status' => 'paid']);
        }

        foreach ($overtimes as $overtime) {
            $payroll->details()->create([
                'label'  => 'Lembur ' . $overtime->overtime_date->format('d M'),
                'type'   => 'overtime',
                'amount' => (float)$overtime->total_pay,
            ]);
        }

        return $payroll;
    }

    public function show(Payroll $payroll)
    {
        $this->authorize($payroll->employee->user_id === auth()->id() ? 'view own payroll' : 'view all payrolls');
        $payroll->load('employee.department', 'employee.position', 'details');
        return view('payrolls.show', compact('payroll'));
    }

    public function approve(Payroll $payroll)
    {
        $this->authorize('approve payroll');
        $payroll->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        return back()->with('success', 'Payroll disetujui.');
    }

    public function markPaid(Payroll $payroll)
    {
        $this->authorize('mark payroll paid');
        $payroll->update(['status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Payroll ditandai sudah dibayar.');
    }

    public function downloadSlip(Payroll $payroll)
    {
        $payroll->load('employee.department', 'employee.position', 'details');
        $pdf = Pdf::loadView('payrolls.slip-pdf', compact('payroll'));
        return $pdf->download('slip-gaji-' . $payroll->employee->employee_code . '-' . $payroll->period_label . '.pdf');
    }
}
