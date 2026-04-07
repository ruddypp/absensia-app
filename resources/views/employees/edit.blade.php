@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Karyawan /</span> Edit</h4>
  <a href="{{ route('employees.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Kode Karyawan <span class="text-danger">*</span></label>
          <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code', $employee->employee_code) }}" required>
          @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Departemen <span class="text-danger">*</span></label>
          <select name="department_id" class="form-select" required>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Jabatan <span class="text-danger">*</span></label>
          <select name="position_id" class="form-select" required>
            @foreach($positions as $pos)
            <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
          <select name="gender" class="form-select" required>
            <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
            <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            <option value="resigned" {{ old('status', $employee->status) == 'resigned' ? 'selected' : '' }}>Keluar</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Bergabung <span class="text-danger">*</span></label>
          <input type="date" name="join_date" class="form-control" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">No. Telepon</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Foto Baru</label>
          @if($employee->photo)
            <div class="mb-2"><img src="{{ $employee->photo_url }}" class="rounded" style="height:60px"></div>
          @endif
          <input type="file" name="photo" class="form-control" accept="image/*">
        </div>
        <div class="col-md-6">
          <label class="form-label">Alamat</label>
          <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nama Bank</label>
          <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">No. Rekening</label>
          <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account', $employee->bank_account) }}">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
