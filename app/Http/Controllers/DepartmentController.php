<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $this->authorize('view departments');
        $departments = Department::withCount('employees')->latest()->paginate(15);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $this->authorize('create departments');
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create departments');
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:departments,code',
        ]);

        Department::create($request->only('name', 'code', 'is_active') + ['is_active' => $request->boolean('is_active', true)]);
        return redirect()->route('departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $department)
    {
        $this->authorize('edit departments');
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $this->authorize('edit departments');
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
        ]);

        $department->update($request->only('name', 'code') + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('departments.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete departments');
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
