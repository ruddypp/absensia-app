@extends('layouts.app')
@section('title', 'Lokasi Kerja')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master Data /</span> Lokasi Kerja</h4>
  @can('create work locations')
  <a href="{{ route('work-locations.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Tambah Lokasi</a>
  @endcan
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Nama</th><th>Koordinat</th><th>Radius</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($locations as $loc)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $loc->name }}</td>
          <td><small class="text-muted">{{ $loc->latitude }}, {{ $loc->longitude }}</small></td>
          <td>{{ $loc->radius_meters }} m</td>
          <td>{!! $loc->is_active ? '<span class="badge bg-label-success">Aktif</span>' : '<span class="badge bg-label-secondary">Non-aktif</span>' !!}</td>
          <td>
            @can('edit work locations')
            <a href="{{ route('work-locations.edit', $loc) }}" class="btn btn-sm btn-icon btn-text-secondary"><i class="bx bx-edit"></i></a>
            @endcan
            @can('delete work locations')
            <form action="{{ route('work-locations.destroy', $loc) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-icon btn-text-danger"><i class="bx bx-trash"></i></button>
            </form>
            @endcan
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada lokasi kerja</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $locations->links() }}</div>
</div>
@endsection
