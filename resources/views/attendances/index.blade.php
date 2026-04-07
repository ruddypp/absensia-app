@extends('layouts.app')
@section('title', 'Semua Absensi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Absensi /</span> Semua Absensi</h4>
  <span class="badge bg-label-primary fs-6">{{ now()->translatedFormat('l, d F Y') }}</span>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Karyawan</th><th>Departemen</th><th>Check-in</th><th>Check-out</th><th>Status</th><th>Terlambat</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($attendances as $att)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $att->employee->name ?? '-' }}</td>
          <td>{{ $att->employee->department->name ?? '-' }}</td>
          <td>{{ $att->check_in ?? '<span class="text-muted">-</span>' }}</td>
          <td>{{ $att->check_out ?? '<span class="text-muted">-</span>' }}</td>
          <td>{!! $att->status_badge !!}</td>
          <td>{{ $att->late_minutes > 0 ? $att->late_minutes . ' mnt' : '-' }}</td>
          <td>
            @can('correct attendance')
            <a href="{{ route('attendances.correction', $att) }}" class="btn btn-sm btn-icon btn-text-warning" title="Koreksi">
              <i class="bx bx-edit-alt"></i>
            </a>
            @endcan
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada absensi hari ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $attendances->links() }}</div>
</div>
@endsection
