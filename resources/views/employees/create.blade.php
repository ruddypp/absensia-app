@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Karyawan /</span> Tambah</h4>
  <a href="{{ route('employees.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Kode Karyawan <span class="text-danger">*</span></label>
          <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code') }}" placeholder="EMP001" required>
          @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Departemen <span class="text-danger">*</span></label>
          <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
            <option value="">-- Pilih --</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
          </select>
          @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Jabatan <span class="text-danger">*</span></label>
          <select name="position_id" class="form-select @error('position_id') is-invalid @enderror" required>
            <option value="">-- Pilih --</option>
            @foreach($positions as $pos)
            <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
            @endforeach
          </select>
          @error('position_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
          <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
            <option value="">-- Pilih --</option>
            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
          </select>
          @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal Bergabung <span class="text-danger">*</span></label>
          <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror" value="{{ old('join_date') }}" required>
          @error('join_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">No. Telepon</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">NIK (16 digit)</label>
          <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" maxlength="16">
          @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Foto</label>
          <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
          @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Password Akun</label>
          <input type="password" name="password" class="form-control" placeholder="Kosongkan = default 'password'">
        </div>
        <div class="col-md-6">
          <label class="form-label">Alamat</label>
          <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nama Bank</label>
          <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">No. Rekening</label>
          <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account') }}">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
