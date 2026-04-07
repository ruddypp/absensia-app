@extends('layouts.app')
@section('title', 'Koreksi Absensi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Absensi /</span> Koreksi</h4>
  <a href="{{ route('attendances.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <p class="text-muted mb-4">Karyawan: <strong>{{ $attendance->employee->name }}</strong> | Tanggal: <strong>{{ $attendance->attendance_date->format('d M Y') }}</strong></p>
    <form action="{{ route('attendances.correction.update', $attendance) }}" method="POST">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Check-in <span class="text-danger">*</span></label>
          <input type="time" name="check_in" class="form-control" value="{{ old('check_in', $attendance->check_in) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Check-out</label>
          <input type="time" name="check_out" class="form-control" value="{{ old('check_out', $attendance->check_out) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-select" required>
            @foreach(['present' => 'Hadir', 'late' => 'Terlambat', 'absent' => 'Tidak Hadir', 'leave' => 'Cuti', 'holiday' => 'Libur', 'permission' => 'Izin'] as $val => $label)
            <option value="{{ $val }}" {{ old('status', $attendance->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Alasan Koreksi <span class="text-danger">*</span></label>
          <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" required placeholder="Jelaskan alasan koreksi...">{{ old('notes', $attendance->notes) }}</textarea>
          @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan Koreksi</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
