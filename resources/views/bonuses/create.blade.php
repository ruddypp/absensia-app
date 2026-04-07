@extends('layouts.app')
@section('title', 'Tambah Bonus')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Bonus /</span> Tambah</h4>
  <a href="{{ route('bonuses.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('bonuses.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Karyawan <span class="text-danger">*</span></label>
          <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
            <option value="">-- Pilih Karyawan --</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
          </select>
          @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Jenis Bonus <span class="text-danger">*</span></label>
          <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="">-- Pilih --</option>
            <option value="performance">Kinerja</option>
            <option value="thr">THR</option>
            <option value="project">Proyek</option>
            <option value="referral">Referral</option>
            <option value="other">Lainnya</option>
          </select>
          @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-8">
          <label class="form-label">Judul <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
          @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal Bonus <span class="text-danger">*</span></label>
          <input type="date" name="bonus_date" class="form-control @error('bonus_date') is-invalid @enderror" value="{{ old('bonus_date', now()->format('Y-m-d')) }}" required>
          @error('bonus_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
          <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" min="1" required>
          @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-8">
          <label class="form-label">Keterangan</label>
          <input type="text" name="description" class="form-control" value="{{ old('description') }}">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
