@extends('layouts.app')
@section('title', 'Ajukan Cuti')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Cuti /</span> Ajukan</h4>
  <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
          <select name="leave_type" class="form-select @error('leave_type') is-invalid @enderror" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="annual">Cuti Tahunan</option>
            <option value="sick">Sakit</option>
            <option value="maternity">Cuti Melahirkan</option>
            <option value="paternity">Cuti Ayah</option>
            <option value="emergency">Darurat</option>
            <option value="permission">Izin</option>
            <option value="unpaid">Tanpa Gaji</option>
          </select>
          @error('leave_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
          <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
          @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
          <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
          @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Alasan <span class="text-danger">*</span></label>
          <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required>{{ old('reason') }}</textarea>
          @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Lampiran (opsional)</label>
          <input type="file" name="attachment" class="form-control">
          <small class="text-muted">Contoh: surat dokter, surat keterangan</small>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
