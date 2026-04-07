@extends('layouts.app')
@section('title', 'Slip Gaji Saya')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Penggajian /</span> Slip Gaji Saya</h4>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>Periode</th><th>Gaji Bersih</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($payrolls as $p)
        <tr>
          <td>{{ $p->period_label }}</td>
          <td><strong>Rp {{ number_format($p->net_salary, 0, ',', '.') }}</strong></td>
          <td>{!! $p->status_badge !!}</td>
          <td>
            <a href="{{ route('payrolls.show', $p) }}" class="btn btn-sm btn-text-info"><i class="bx bx-show me-1"></i> Detail</a>
            <a href="{{ route('payrolls.slip', $p) }}" class="btn btn-sm btn-text-success"><i class="bx bx-download me-1"></i> Download</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data slip gaji</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $payrolls->links() }}</div>
</div>
@endsection
