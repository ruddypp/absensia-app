@extends('layouts.app')
@section('title', 'Detail Cuti')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Cuti /</span> Detail</h4>
  <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6"><strong>Karyawan:</strong> {{ $leaveRequest->employee->name ?? '-' }}</div>
      <div class="col-md-6"><strong>Jenis Cuti:</strong> {{ $leaveRequest->leave_type_label }}</div>
      <div class="col-md-6"><strong>Tanggal Mulai:</strong> {{ $leaveRequest->start_date->format('d M Y') }}</div>
      <div class="col-md-6"><strong>Tanggal Selesai:</strong> {{ $leaveRequest->end_date->format('d M Y') }}</div>
      <div class="col-md-6"><strong>Total Hari:</strong> {{ $leaveRequest->total_days }} hari</div>
      <div class="col-md-6"><strong>Status:</strong> {!! $leaveRequest->status_badge !!}</div>
      <div class="col-12"><strong>Alasan:</strong><p class="mb-0">{{ $leaveRequest->reason }}</p></div>
      @if($leaveRequest->rejection_reason)
      <div class="col-12">
        <div class="alert alert-danger">
          <strong>Alasan Penolakan:</strong> {{ $leaveRequest->rejection_reason }}
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
