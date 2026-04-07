@extends('layouts.app')
@section('title', 'Laporan Absensi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Laporan /</span> Laporan Absensi</h4>
  <a href="{{ route('reports.attendance.export', request()->all()) }}" class="btn btn-success">
    <i class="bx bx-download me-1"></i> Export CSV
  </a>
</div>
<div class="card mb-4">
  <div class="card-body">
    <form method="GET">
      <div class="row g-2 align-items-end">
        <div class="col-md-2">
          <label class="form-label">Bulan</label>
          <select name="month" class="form-select">
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Tahun</label>
          <input type="number" name="year" class="form-control" value="{{ $year }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Departemen</label>
          <select name="department_id" class="form-select">
            <option value="">Semua Departemen</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Karyawan</label>
          <select name="employee_id" class="form-select">
            <option value="">Semua Karyawan</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>Tanggal</th><th>Karyawan</th><th>Departemen</th><th>Check-in</th><th>Check-out</th><th>Status</th><th>Terlambat</th></tr>
      </thead>
      <tbody>
        @forelse($attendances as $att)
        <tr>
          <td>{{ $att->attendance_date->format('d M Y') }}</td>
          <td>{{ $att->employee->name ?? '-' }}</td>
          <td>{{ $att->employee->department->name ?? '-' }}</td>
          <td>{{ $att->check_in ?? '-' }}</td>
          <td>{{ $att->check_out ?? '-' }}</td>
          <td>{!! $att->status_badge !!}</td>
          <td>{{ $att->late_minutes > 0 ? $att->late_minutes . ' mnt' : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $attendances->links() }}</div>
</div>
@endsection
