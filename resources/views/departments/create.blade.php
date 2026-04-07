@extends('layouts.app')
@section('title', 'Tambah Departemen')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Departemen /</span> Tambah</h4>
  <a href="{{ route('departments.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('departments.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name') }}" placeholder="Contoh: Teknologi Informasi" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label">Kode <span class="text-danger">*</span></label>
          <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code') }}" placeholder="Contoh: IT" maxlength="10" required>
          @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
            <label class="form-check-label" for="isActive">Aktif</label>
          </div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
