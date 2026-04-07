@extends('layouts.app')
@section('title', 'Ajukan Lembur')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Lembur /</span> Ajukan</h4>
  <a href="{{ route('overtime-requests.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('overtime-requests.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Tanggal Lembur <span class="text-danger">*</span></label>
          <input type="date" name="overtime_date" class="form-control @error('overtime_date') is-invalid @enderror" value="{{ old('overtime_date') }}" required>
          @error('overtime_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
          <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
          @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
          <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
          @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Multiplier</label>
          <select name="rate_multiplier" class="form-select">
            <option value="1.5">1.5x (Normal)</option>
            <option value="2.0">2.0x (Hari Libur)</option>
            <option value="3.0">3.0x (Libur Nasional)</option>
          </select>
        </div>
        <div class="col-md-8">
          <label class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
          <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" required>
          @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
