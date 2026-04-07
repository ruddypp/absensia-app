@extends('layouts.app')
@section('title', 'Pengajuan Cuti')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Cuti & Lembur /</span> Pengajuan Cuti</h4>
  @can('create leave')
  <a href="{{ route('leave-requests.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Ajukan Cuti</a>
  @endcan
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Karyawan</th><th>Jenis</th><th>Periode</th><th>Hari</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($leaves as $leave)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $leave->employee->name ?? '-' }}</td>
          <td>{{ $leave->leave_type_label }}</td>
          <td>{{ $leave->start_date->format('d M') }} — {{ $leave->end_date->format('d M Y') }}</td>
          <td>{{ $leave->total_days }} hari</td>
          <td>{!! $leave->status_badge !!}</td>
          <td>
            <a href="{{ route('leave-requests.show', $leave) }}" class="btn btn-sm btn-icon btn-text-info"><i class="bx bx-show"></i></a>
            @if($leave->status === 'pending')
              @can('approve leave')
              <form action="{{ route('leave-requests.approve', $leave) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-icon btn-text-success" title="Setujui"><i class="bx bx-check"></i></button>
              </form>
              @endcan
              @can('reject leave')
              <button class="btn btn-sm btn-icon btn-text-danger" title="Tolak"
                onclick="rejectLeave({{ $leave->id }})"><i class="bx bx-x"></i></button>
              @endcan
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan cuti</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $leaves->links() }}</div>
</div>

{{-- Modal tolak --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Tolak Cuti</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form id="rejectForm" method="POST">
        @csrf @method('PATCH')
        <div class="modal-body">
          <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
          <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak Cuti</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
function rejectLeave(id) {
  document.getElementById('rejectForm').action = '/leave-requests/' + id + '/reject';
  new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
