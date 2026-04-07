<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $this->authorize('view employees');
        $employees = Employee::with('department', 'position')
            ->latest()->paginate(15);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $this->authorize('create employees');
        $departments = Department::where('is_active', true)->get();
        $positions   = Position::where('is_active', true)->get();
        return view('employees.create', compact('departments', 'positions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create employees');
        $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:employees,email',
            'department_id' => 'required|exists:departments,id',
            'position_id'   => 'required|exists:positions,id',
            'employee_code' => 'required|string|unique:employees,employee_code',
            'join_date'     => 'required|date',
            'gender'        => 'required|in:male,female',
            'phone'         => 'nullable|string|max:20',
            'nik'           => 'nullable|string|size:16|unique:employees,nik',
            'photo'         => 'nullable|image|max:2048',
        ]);

        $data = $request->except('photo', 'password', '_token');

        // Buat akun user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password ?? 'password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('karyawan');
        $data['user_id'] = $user->id;

        // Upload foto
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        Employee::create($data);
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        $this->authorize('view employees');
        $employee->load('department', 'position', 'user', 'attendances', 'payrolls', 'bonuses');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $this->authorize('edit employees');
        $departments = Department::where('is_active', true)->get();
        $positions   = Position::where('is_active', true)->get();
        return view('employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorize('edit employees');
        $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:employees,email,' . $employee->id,
            'department_id' => 'required|exists:departments,id',
            'position_id'   => 'required|exists:positions,id',
            'employee_code' => 'required|string|unique:employees,employee_code,' . $employee->id,
            'join_date'     => 'required|date',
            'gender'        => 'required|in:male,female',
            'phone'         => 'nullable|string|max:20',
            'nik'           => 'nullable|string|size:16|unique:employees,nik,' . $employee->id,
            'photo'         => 'nullable|image|max:2048',
        ]);

        $data = $request->except('photo', '_token', '_method');

        if ($request->hasFile('photo')) {
            if ($employee->photo) Storage::disk('public')->delete($employee->photo);
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        $employee->update($data);
        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $this->authorize('delete employees');
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
