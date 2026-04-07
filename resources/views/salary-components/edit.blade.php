@extends('layouts.app')
@section('title', 'Edit Komponen Gaji')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Komponen Gaji /</span> Edit</h4>
  <a href="{{ route('salary-components.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('salary-components.update', $salaryComponent) }}" method="POST">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama Komponen <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $salaryComponent->name) }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tipe <span class="text-danger">*</span></label>
          <select name="type" class="form-select" required>
            <option value="allowance" {{ old('type', $salaryComponent->type) == 'allowance' ? 'selected' : '' }}>Tunjangan</option>
            <option value="deduction" {{ old('type', $salaryComponent->type) == 'deduction' ? 'selected' : '' }}>Potongan</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Perhitungan</label>
          <select name="calculation_type" class="form-select" required>
            <option value="fixed" {{ old('calculation_type', $salaryComponent->calculation_type) == 'fixed' ? 'selected' : '' }}>Tetap</option>
            <option value="percentage" {{ old('calculation_type', $salaryComponent->calculation_type) == 'percentage' ? 'selected' : '' }}>Persentase</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Nilai</label>
          <input type="number" name="amount" step="any" class="form-control" value="{{ old('amount', $salaryComponent->amount) }}" min="0" required>
        </div>
        <div class="col-md-8">
          <label class="form-label">Keterangan</label>
          <input type="text" name="description" class="form-control" value="{{ old('description', $salaryComponent->description) }}">
        </div>
        <div class="col-12">
          <div class="form-check form-switch me-4 d-inline-block">
            <input class="form-check-input" type="checkbox" name="is_taxable" id="isTaxable" {{ $salaryComponent->is_taxable ? 'checked' : '' }}>
            <label class="form-check-label" for="isTaxable">Kena Pajak</label>
          </div>
          <div class="form-check form-switch d-inline-block">
            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $salaryComponent->is_active ? 'checked' : '' }}>
            <label class="form-check-label" for="isActive">Aktif</label>
          </div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
