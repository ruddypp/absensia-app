@extends('layouts.app')
@section('title', 'Laporan Gaji')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Laporan /</span> Laporan Gaji</h4>
  <a href="{{ route('reports.payroll.export', request()->all()) }}" class="btn btn-success">
    <i class="bx bx-download me-1"></i> Export CSV
  </a>
</div>
<div class="card mb-4">
  <div class="card-body">
    <form method="GET">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
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
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <h6 class="text-muted">Total Payroll</h6>
        <h4 class="text-primary">{{ $payrolls->total() }}</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <h6 class="text-muted">Total Gaji Bersih</h6>
        <h5>Rp {{ number_format($payrolls->sum('net_salary'), 0, ',', '.') }}</h5>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Karyawan</th><th>Departemen</th><th>Gaji Pokok</th><th>Tunjangan</th><th>Potongan</th><th>Gaji Bersih</th><th>Status</th></tr>
      </thead>
      <tbody>
        @forelse($payrolls as $p)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $p->employee->name ?? '-' }}</td>
          <td>{{ $p->employee->department->name ?? '-' }}</td>
          <td>Rp {{ number_format($p->base_salary, 0, ',', '.') }}</td>
          <td class="text-success">+Rp {{ number_format($p->total_allowance + $p->total_bonus + $p->total_overtime, 0, ',', '.') }}</td>
          <td class="text-danger">-Rp {{ number_format($p->total_deduction, 0, ',', '.') }}</td>
          <td><strong>Rp {{ number_format($p->net_salary, 0, ',', '.') }}</strong></td>
          <td>{!! $p->status_badge !!}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $payrolls->links() }}</div>
</div>
@endsection
