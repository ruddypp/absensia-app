@extends('layouts.app')
@section('title', 'Pengajuan Lembur')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Cuti & Lembur /</span> Pengajuan Lembur</h4>
  @can('create overtime')
  <a href="{{ route('overtime-requests.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Ajukan Lembur</a>
  @endcan
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Karyawan</th><th>Tanggal</th><th>Waktu</th><th>Total Jam</th><th>Upah</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($overtimes as $ot)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $ot->employee->name ?? '-' }}</td>
          <td>{{ $ot->overtime_date->format('d M Y') }}</td>
          <td>{{ $ot->start_time }} — {{ $ot->end_time }}</td>
          <td>{{ $ot->total_hours }} jam</td>
          <td>Rp {{ number_format($ot->total_pay, 0, ',', '.') }}</td>
          <td>{!! match($ot->status) {
            'pending'  => '<span class="badge bg-label-warning">Menunggu</span>',
            'approved' => '<span class="badge bg-label-success">Disetujui</span>',
            'rejected' => '<span class="badge bg-label-danger">Ditolak</span>',
            default    => '-'
          } !!}</td>
          <td>
            @if($ot->status === 'pending')
              @can('approve overtime')
              <form action="{{ route('overtime-requests.approve', $ot) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-icon btn-text-success" title="Setujui"><i class="bx bx-check"></i></button>
              </form>
              @endcan
              @can('reject overtime')
              <button class="btn btn-sm btn-icon btn-text-danger" title="Tolak"
                onclick="rejectOT({{ $ot->id }})"><i class="bx bx-x"></i></button>
              @endcan
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada pengajuan lembur</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $overtimes->links() }}</div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Tolak Lembur</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form id="rejectForm" method="POST">
        @csrf @method('PATCH')
        <div class="modal-body">
          <label class="form-label">Alasan <span class="text-danger">*</span></label>
          <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
function rejectOT(id) {
  document.getElementById('rejectForm').action = '/overtime-requests/' + id + '/reject';
  new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
