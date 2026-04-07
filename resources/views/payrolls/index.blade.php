@extends('layouts.app')
@section('title', 'Kelola Payroll')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Penggajian /</span> Kelola Payroll</h4>
  @can('generate payroll')
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
    <i class="bx bx-play me-1"></i> Generate Payroll
  </button>
  @endcan
</div>

{{-- Filter period --}}
<div class="card mb-4">
  <div class="card-body">
    <form method="GET">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Bulan</label>
          <select name="month" class="form-select">
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
              {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Tahun</label>
          <input type="number" name="year" class="form-control" value="{{ $year }}" min="2020" max="2030">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-secondary w-100">Filter</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Karyawan</th><th>Departemen</th><th>Gaji Pokok</th><th>Gaji Bersih</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($payrolls as $payroll)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $payroll->employee->name ?? '-' }}</td>
          <td>{{ $payroll->employee->department->name ?? '-' }}</td>
          <td>Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
          <td><strong>Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</strong></td>
          <td>{!! $payroll->status_badge !!}</td>
          <td>
            <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-sm btn-icon btn-text-info" title="Detail"><i class="bx bx-show"></i></a>
            @if($payroll->status === 'draft')
              @can('approve payroll')
              <form action="{{ route('payrolls.approve', $payroll) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-icon btn-text-success" title="Setujui"><i class="bx bx-check"></i></button>
              </form>
              @endcan
            @elseif($payroll->status === 'approved')
              @can('mark payroll paid')
              <form action="{{ route('payrolls.paid', $payroll) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-icon btn-text-primary" title="Tandai Dibayar"><i class="bx bx-money"></i></button>
              </form>
              @endcan
            @endif
            <a href="{{ route('payrolls.slip', $payroll) }}" class="btn btn-sm btn-icon btn-text-secondary" title="Download Slip"><i class="bx bx-download"></i></a>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada payroll untuk periode ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $payrolls->links() }}</div>
</div>

{{-- Modal Generate Payroll --}}
<div class="modal fade" id="generateModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Generate Payroll</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="form-label">Bulan</label>
            <select id="gen-month" class="form-select">
              @foreach(range(1,12) as $m)
              <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Tahun</label>
            <input type="number" id="gen-year" class="form-control" value="{{ $year }}">
          </div>
        </div>
        <label class="form-label">Pilih Karyawan</label>
        <div class="mb-2">
          <button type="button" class="btn btn-sm btn-secondary" onclick="selectAll()">Pilih Semua</button>
        </div>
        <div style="max-height:300px;overflow-y:auto;border:1px solid #ddd;border-radius:6px;padding:10px">
          @foreach($employees as $emp)
          <div class="form-check">
            <input class="form-check-input emp-check" type="checkbox" value="{{ $emp->id }}" id="emp-{{ $emp->id }}">
            <label class="form-check-label" for="emp-{{ $emp->id }}">
              {{ $emp->name }} — {{ $emp->department->name ?? '-' }}
            </label>
          </div>
          @endforeach
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="runGenerate()">
          <span id="btn-gen-text"><i class="bx bx-play me-1"></i> Generate</span>
        </button>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
function selectAll() {
  document.querySelectorAll('.emp-check').forEach(c => c.checked = true);
}
async function runGenerate() {
  const ids = [...document.querySelectorAll('.emp-check:checked')].map(c => c.value);
  if (!ids.length) { alert('Pilih minimal 1 karyawan'); return; }

  document.getElementById('btn-gen-text').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
  const res = await fetch('{{ route("payrolls.generate") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({
      month: document.getElementById('gen-month').value,
      year: document.getElementById('gen-year').value,
      employee_ids: ids
    })
  });
  const data = await res.json();
  if (data.success) {
    alert('Berhasil generate ' + data.generated + ' payroll!');
    location.reload();
  }
}
</script>
@endpush
