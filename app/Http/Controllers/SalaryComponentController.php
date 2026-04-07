<?php

namespace App\Http\Controllers;

use App\Models\SalaryComponent;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    public function index()
    {
        $this->authorize('view salary components');
        $components = SalaryComponent::latest()->paginate(15);
        return view('salary-components.index', compact('components'));
    }

    public function create()
    {
        $this->authorize('create salary components');
        return view('salary-components.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create salary components');
        $request->validate([
            'name'             => 'required|string|max:100',
            'type'             => 'required|in:allowance,deduction',
            'calculation_type' => 'required|in:fixed,percentage',
            'amount'           => 'required|numeric|min:0',
        ]);

        SalaryComponent::create($request->only('name', 'type', 'calculation_type', 'amount', 'description') + [
            'is_taxable' => $request->boolean('is_taxable'),
            'is_active'  => $request->boolean('is_active', true),
        ]);
        return redirect()->route('salary-components.index')->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    public function edit(SalaryComponent $salaryComponent)
    {
        $this->authorize('edit salary components');
        return view('salary-components.edit', compact('salaryComponent'));
    }

    public function update(Request $request, SalaryComponent $salaryComponent)
    {
        $this->authorize('edit salary components');
        $request->validate([
            'name'             => 'required|string|max:100',
            'type'             => 'required|in:allowance,deduction',
            'calculation_type' => 'required|in:fixed,percentage',
            'amount'           => 'required|numeric|min:0',
        ]);

        $salaryComponent->update($request->only('name', 'type', 'calculation_type', 'amount', 'description') + [
            'is_taxable' => $request->boolean('is_taxable'),
            'is_active'  => $request->boolean('is_active'),
        ]);
        return redirect()->route('salary-components.index')->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    public function destroy(SalaryComponent $salaryComponent)
    {
        $this->authorize('delete salary components');
        $salaryComponent->delete();
        return redirect()->route('salary-components.index')->with('success', 'Komponen gaji berhasil dihapus.');
    }
}
