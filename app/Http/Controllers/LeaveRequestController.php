<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole(['super_admin', 'hrd', 'kepala_departemen'])) {
            $this->authorize('view all leaves');
            $leaves = LeaveRequest::with('employee')->latest()->paginate(15);
        } else {
            $this->authorize('view own leaves');
            $employee = $user->employee;
            abort_if(!$employee, 403);
            $leaves = LeaveRequest::where('employee_id', $employee->id)->latest()->paginate(15);
        }
        return view('leave-requests.index', compact('leaves'));
    }

    public function create()
    {
        $this->authorize('create leave');
        return view('leave-requests.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create leave');
        $employee = auth()->user()->employee;
        abort_if(!$employee, 403);

        $request->validate([
            'leave_type' => 'required|in:annual,sick,maternity,paternity,emergency,permission,unpaid',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $data = $request->only('leave_type', 'start_date', 'end_date', 'reason');
        $data['employee_id'] = $employee->id;
        $data['total_days']  = \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1;

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leaves', 'public');
        }

        LeaveRequest::create($data);
        return redirect()->route('leave-requests.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        return view('leave-requests.show', compact('leaveRequest'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $this->authorize('approve leave');
        $leaveRequest->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Cuti disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorize('reject leave');
        $request->validate(['rejection_reason' => 'required|string']);
        $leaveRequest->update([
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'Cuti ditolak.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        abort_if($leaveRequest->status !== 'pending', 403);
        $leaveRequest->delete();
        return redirect()->route('leave-requests.index')->with('success', 'Pengajuan cuti dibatalkan.');
    }
}
