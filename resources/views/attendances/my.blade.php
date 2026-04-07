@extends('layouts.app')
@section('title', 'Absensi Saya')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Absensi /</span> Absensi Saya</h4>
  <a href="{{ route('attendances.checkin') }}" class="btn btn-primary"><i class="bx bx-camera me-1"></i> Check-in/out</a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>Tanggal</th><th>Check-in</th><th>Check-out</th><th>Status</th><th>Terlambat</th></tr>
      </thead>
      <tbody>
        @forelse($attendances as $att)
        <tr>
          <td>{{ $att->attendance_date->translatedFormat('l, d M Y') }}</td>
          <td>{{ $att->check_in ?? '-' }}</td>
          <td>{{ $att->check_out ?? '-' }}</td>
          <td>{!! $att->status_badge !!}</td>
          <td>{{ $att->late_minutes > 0 ? $att->late_minutes . ' mnt' : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data absensi</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $attendances->links() }}</div>
</div>
@endsection
