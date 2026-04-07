@extends('layouts.app')
@section('title', 'Jabatan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master Data /</span> Jabatan</h4>
  @can('create positions')
  <a href="{{ route('positions.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Tambah Jabatan</a>
  @endcan
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Jabatan</th><th>Departemen</th><th>Gaji Pokok</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($positions as $pos)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $pos->name }}</td>
          <td><span class="badge bg-label-info">{{ $pos->department->name ?? '-' }}</span></td>
          <td>Rp {{ number_format($pos->base_salary, 0, ',', '.') }}</td>
          <td>{!! $pos->is_active ? '<span class="badge bg-label-success">Aktif</span>' : '<span class="badge bg-label-secondary">Non-aktif</span>' !!}</td>
          <td>
            @can('edit positions')
            <a href="{{ route('positions.edit', $pos) }}" class="btn btn-sm btn-icon btn-text-secondary"><i class="bx bx-edit"></i></a>
            @endcan
            @can('delete positions')
            <form action="{{ route('positions.destroy', $pos) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jabatan ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-icon btn-text-danger"><i class="bx bx-trash"></i></button>
            </form>
            @endcan
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data jabatan</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $positions->links() }}</div>
</div>
@endsection
