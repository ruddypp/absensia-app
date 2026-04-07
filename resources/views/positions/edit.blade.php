@extends('layouts.app')
@section('title', 'Edit Jabatan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Jabatan /</span> Edit</h4>
  <a href="{{ route('positions.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('positions.update', $position) }}" method="POST">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $position->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Departemen <span class="text-danger">*</span></label>
          <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}" {{ old('department_id', $position->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
          </select>
          @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" name="base_salary" class="form-control" value="{{ old('base_salary', $position->base_salary) }}" min="0" required>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Status</label>
          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $position->is_active ? 'checked' : '' }}>
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
