@extends('layouts.app')
@section('title', 'Detail Lembur')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Lembur /</span> Detail</h4>
  <a href="{{ route('overtime-requests.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6"><strong>Karyawan:</strong> {{ $overtimeRequest->employee->name ?? '-' }}</div>
      <div class="col-md-6"><strong>Tanggal:</strong> {{ $overtimeRequest->overtime_date->format('d M Y') }}</div>
      <div class="col-md-6"><strong>Waktu:</strong> {{ $overtimeRequest->start_time }} — {{ $overtimeRequest->end_time }}</div>
      <div class="col-md-6"><strong>Total Jam:</strong> {{ $overtimeRequest->total_hours }} jam</div>
      <div class="col-md-6"><strong>Upah Lembur:</strong> Rp {{ number_format($overtimeRequest->total_pay, 0, ',', '.') }}</div>
      <div class="col-md-6"><strong>Status:</strong> {!! match($overtimeRequest->status) {
        'pending'=>'<span class="badge bg-label-warning">Menunggu</span>',
        'approved'=>'<span class="badge bg-label-success">Disetujui</span>',
        'rejected'=>'<span class="badge bg-label-danger">Ditolak</span>',
        default=>'-'
      } !!}</div>
      <div class="col-12"><strong>Deskripsi:</strong> {{ $overtimeRequest->description }}</div>
    </div>
  </div>
</div>
@endsection
