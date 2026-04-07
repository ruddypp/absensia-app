<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $this->authorize('view positions');
        $positions = Position::with('department')->latest()->paginate(15);
        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        $this->authorize('create positions');
        $departments = Department::where('is_active', true)->get();
        return view('positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $this->authorize('create positions');
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:100',
            'base_salary'   => 'required|numeric|min:0',
        ]);

        Position::create($request->only('department_id', 'name', 'base_salary') + ['is_active' => $request->boolean('is_active', true)]);
        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Position $position)
    {
        $this->authorize('edit positions');
        $departments = Department::where('is_active', true)->get();
        return view('positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        $this->authorize('edit positions');
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:100',
            'base_salary'   => 'required|numeric|min:0',
        ]);

        $position->update($request->only('department_id', 'name', 'base_salary') + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        $this->authorize('delete positions');
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
