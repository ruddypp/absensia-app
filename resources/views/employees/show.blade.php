@extends('layouts.app')
@section('title', 'Detail Karyawan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Karyawan /</span> Detail</h4>
  <div>
    @can('edit employees')
    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary me-2"><i class="bx bx-edit me-1"></i> Edit</a>
    @endcan
    <a href="{{ route('employees.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-xl-4">
    <div class="card">
      <div class="card-body text-center">
        <img src="{{ $employee->photo_url }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover">
        <h5 class="mb-1">{{ $employee->name }}</h5>
        <p class="text-muted mb-2">{{ $employee->position->name ?? '-' }}</p>
        <span class="badge bg-label-info">{{ $employee->department->name ?? '-' }}</span>
        <hr>
        <div class="text-start">
          <p class="mb-2"><i class="bx bx-envelope me-2"></i>{{ $employee->email }}</p>
          <p class="mb-2"><i class="bx bx-phone me-2"></i>{{ $employee->phone ?? '-' }}</p>
          <p class="mb-2"><i class="bx bx-id-card me-2"></i>{{ $employee->employee_code }}</p>
          <p class="mb-0"><i class="bx bx-calendar me-2"></i>Bergabung: {{ $employee->join_date?->format('d M Y') }}</p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-8">
    <div class="card mb-4">
      <div class="card-header"><h5 class="mb-0">Rekap Absensi</h5></div>
      <div class="card-body">
        <p class="text-muted">Total Absensi: {{ $employee->attendances->count() }} catatan</p>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Riwayat Payroll</h5></div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light"><tr><th>Periode</th><th>Gaji Bersih</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($employee->payrolls->take(5) as $p)
            <tr>
              <td>{{ $p->period_label }}</td>
              <td>Rp {{ number_format($p->net_salary, 0, ',', '.') }}</td>
              <td>{!! $p->status_badge !!}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada payroll</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
