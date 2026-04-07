@extends('layouts.app')
@section('title', 'Bonus')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Penggajian /</span> Bonus</h4>
  @can('create bonus')
  <a href="{{ route('bonuses.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Tambah Bonus</a>
  @endcan
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Karyawan</th><th>Jenis</th><th>Judul</th><th>Nominal</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($bonuses as $bonus)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $bonus->employee->name ?? '-' }}</td>
          <td><span class="badge bg-label-info">{{ $bonus->type_label }}</span></td>
          <td>{{ $bonus->title }}</td>
          <td>Rp {{ number_format($bonus->amount, 0, ',', '.') }}</td>
          <td>{{ $bonus->bonus_date->format('d M Y') }}</td>
          <td>{!! match($bonus->status) {
            'pending'  => '<span class="badge bg-label-warning">Menunggu</span>',
            'approved' => '<span class="badge bg-label-success">Disetujui</span>',
            'paid'     => '<span class="badge bg-label-primary">Dibayar</span>',
            default    => '-'
          } !!}</td>
          <td>
            @if($bonus->status === 'pending')
              @can('approve bonus')
              <form action="{{ route('bonuses.approve', $bonus) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-icon btn-text-success" title="Setujui"><i class="bx bx-check"></i></button>
              </form>
              @endcan
              @can('delete bonus')
              <form action="{{ route('bonuses.destroy', $bonus) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus bonus ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-icon btn-text-danger"><i class="bx bx-trash"></i></button>
              </form>
              @endcan
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data bonus</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $bonuses->links() }}</div>
</div>
@endsection
