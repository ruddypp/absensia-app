<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\Employee;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole(['super_admin', 'hrd'])) {
            $this->authorize('view all bonuses');
            $bonuses = Bonus::with('employee')->latest()->paginate(15);
        } else {
            $this->authorize('view own bonuses');
            $employee = $user->employee;
            abort_if(!$employee, 403);
            $bonuses = Bonus::where('employee_id', $employee->id)->latest()->paginate(15);
        }
        return view('bonuses.index', compact('bonuses'));
    }

    public function create()
    {
        $this->authorize('create bonus');
        $employees = Employee::where('status', 'active')->get();
        return view('bonuses.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->authorize('create bonus');
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type'        => 'required|in:performance,thr,project,referral,other',
            'title'       => 'required|string|max:100',
            'amount'      => 'required|numeric|min:1',
            'bonus_date'  => 'required|date',
            'description' => 'nullable|string',
        ]);

        Bonus::create($request->only('employee_id', 'type', 'title', 'amount', 'bonus_date', 'description'));
        return redirect()->route('bonuses.index')->with('success', 'Bonus berhasil ditambahkan.');
    }

    public function show(Bonus $bonus)
    {
        return view('bonuses.show', compact('bonus'));
    }

    public function approve(Bonus $bonus)
    {
        $this->authorize('approve bonus');
        $bonus->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        return back()->with('success', 'Bonus disetujui.');
    }

    public function destroy(Bonus $bonus)
    {
        $this->authorize('delete bonus');
        $bonus->delete();
        return redirect()->route('bonuses.index')->with('success', 'Bonus dihapus.');
    }
}
