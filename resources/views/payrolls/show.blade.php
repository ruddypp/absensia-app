@extends('layouts.app')
@section('title', 'Detail Payroll')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Payroll /</span> Detail</h4>
  <div>
    <a href="{{ route('payrolls.slip', $payroll) }}" class="btn btn-success me-2">
      <i class="bx bx-download me-1"></i> Download Slip
    </a>
    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
  </div>
</div>
<div class="row g-4">
  <div class="col-xl-4">
    <div class="card">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">Informasi Karyawan</h6>
        <p class="mb-1"><strong>{{ $payroll->employee->name }}</strong></p>
        <p class="mb-1 text-muted">{{ $payroll->employee->employee_code }}</p>
        <p class="mb-1">{{ $payroll->employee->department->name }}</p>
        <p class="mb-0">{{ $payroll->employee->position->name }}</p>
        <hr>
        <h6 class="fw-semibold mb-2">Periode: {{ $payroll->period_label }}</h6>
        <p class="mb-1">Hari Kerja: {{ $payroll->working_days }}</p>
        <p class="mb-1">Hadir: {{ $payroll->present_days }}</p>
        <p class="mb-1">Tidak Hadir: {{ $payroll->absent_days }}</p>
        <p class="mb-0">Terlambat: {{ $payroll->late_count }}x</p>
        <hr>
        <h5 class="mb-0">Status: {!! $payroll->status_badge !!}</h5>
      </div>
    </div>
  </div>
  <div class="col-xl-8">
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Rincian Gaji</h5></div>
      <div class="card-body">
        <table class="table table-sm">
          <tbody>
            <tr class="table-light"><th colspan="2">Gaji Pokok</th><th class="text-end">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</th></tr>

            @foreach($payroll->details->where('type', 'allowance') as $d)
            <tr>
              <td colspan="2" class="text-muted ps-3">+ {{ $d->label }}</td>
              <td class="text-end text-success">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            @foreach($payroll->details->where('type', 'bonus') as $d)
            <tr>
              <td colspan="2" class="text-muted ps-3">+ {{ $d->label }}</td>
              <td class="text-end text-success">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            @foreach($payroll->details->where('type', 'overtime') as $d)
            <tr>
              <td colspan="2" class="text-muted ps-3">+ {{ $d->label }}</td>
              <td class="text-end text-success">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            @foreach($payroll->details->where('type', 'deduction') as $d)
            <tr>
              <td colspan="2" class="text-muted ps-3">- {{ $d->label }}</td>
              <td class="text-end text-danger">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr class="table-primary fw-bold">
              <th colspan="2">TOTAL GAJI BERSIH</th>
              <th class="text-end">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
