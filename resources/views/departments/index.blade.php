@extends('layouts.app')
@section('title', 'Departemen')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master Data /</span> Departemen</h4>
  @can('create departments')
  <a href="{{ route('departments.create') }}" class="btn btn-primary">
    <i class="bx bx-plus me-1"></i> Tambah Departemen
  </a>
  @endcan
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th><th>Nama</th><th>Kode</th><th>Karyawan</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($departments as $dept)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $dept->name }}</td>
          <td><span class="badge bg-label-primary">{{ $dept->code }}</span></td>
          <td>{{ $dept->employees_count }}</td>
          <td>{!! $dept->is_active ? '<span class="badge bg-label-success">Aktif</span>' : '<span class="badge bg-label-secondary">Non-aktif</span>' !!}</td>
          <td>
            @can('edit departments')
            <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-icon btn-text-secondary">
              <i class="bx bx-edit"></i>
            </a>
            @endcan
            @can('delete departments')
            <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Hapus departemen ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-icon btn-text-danger"><i class="bx bx-trash"></i></button>
            </form>
            @endcan
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data departemen</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $departments->links() }}</div>
</div>
@endsection
