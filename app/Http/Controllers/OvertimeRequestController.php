<?php

namespace App\Http\Controllers;

use App\Models\OvertimeRequest;
use Illuminate\Http\Request;

class OvertimeRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole(['super_admin', 'hrd', 'kepala_departemen'])) {
            $this->authorize('view all overtimes');
            $overtimes = OvertimeRequest::with('employee')->latest()->paginate(15);
        } else {
            $this->authorize('view own overtimes');
            $employee = $user->employee;
            abort_if(!$employee, 403);
            $overtimes = OvertimeRequest::where('employee_id', $employee->id)->latest()->paginate(15);
        }
        return view('overtime-requests.index', compact('overtimes'));
    }

    public function create()
    {
        $this->authorize('create overtime');
        return view('overtime-requests.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create overtime');
        $employee = auth()->user()->employee;
        abort_if(!$employee, 403);

        $request->validate([
            'overtime_date'   => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'required|after:start_time',
            'description'     => 'required|string',
            'rate_multiplier' => 'nullable|numeric|min:1|max:3',
        ]);

        $totalHours = \Carbon\Carbon::parse($request->start_time)
            ->diffInMinutes(\Carbon\Carbon::parse($request->end_time)) / 60;

        $multiplier = $request->rate_multiplier ?? 1.5;
        $hourlyRate = (float) $employee->position->base_salary / (22 * 8);
        $totalPay   = $hourlyRate * $totalHours * $multiplier;

        OvertimeRequest::create([
            'employee_id'     => $employee->id,
            'overtime_date'   => $request->overtime_date,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'total_hours'     => $totalHours,
            'rate_multiplier' => $multiplier,
            'total_pay'       => $totalPay,
            'description'     => $request->description,
        ]);

        return redirect()->route('overtime-requests.index')->with('success', 'Pengajuan lembur berhasil dikirim.');
    }

    public function show(OvertimeRequest $overtimeRequest)
    {
        return view('overtime-requests.show', compact('overtimeRequest'));
    }

    public function approve(OvertimeRequest $overtimeRequest)
    {
        $this->authorize('approve overtime');
        $overtimeRequest->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Lembur disetujui.');
    }

    public function reject(Request $request, OvertimeRequest $overtimeRequest)
    {
        $this->authorize('reject overtime');
        $request->validate(['rejection_reason' => 'required|string']);
        $overtimeRequest->update([
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'Lembur ditolak.');
    }

    public function destroy(OvertimeRequest $overtimeRequest)
    {
        abort_if($overtimeRequest->status !== 'pending', 403);
        $overtimeRequest->delete();
        return redirect()->route('overtime-requests.index')->with('success', 'Pengajuan lembur dibatalkan.');
    }
}
