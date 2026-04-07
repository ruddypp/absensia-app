@extends('layouts.app')
@section('title', 'Karyawan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master Data /</span> Karyawan</h4>
  @can('create employees')
  <a href="{{ route('employees.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Tambah Karyawan</a>
  @endcan
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Karyawan</th><th>Kode</th><th>Departemen</th><th>Jabatan</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($employees as $emp)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm me-3">
                <img src="{{ $emp->photo_url }}" alt class="rounded-circle" />
              </div>
              <div>
                <div class="fw-semibold">{{ $emp->name }}</div>
                <small class="text-muted">{{ $emp->email }}</small>
              </div>
            </div>
          </td>
          <td><span class="badge bg-label-secondary">{{ $emp->employee_code }}</span></td>
          <td>{{ $emp->department->name ?? '-' }}</td>
          <td>{{ $emp->position->name ?? '-' }}</td>
          <td>
            @if($emp->status === 'active') <span class="badge bg-label-success">Aktif</span>
            @elseif($emp->status === 'inactive') <span class="badge bg-label-warning">Tidak Aktif</span>
            @else <span class="badge bg-label-danger">Keluar</span>
            @endif
          </td>
          <td>
            <a href="{{ route('employees.show', $emp) }}" class="btn btn-sm btn-icon btn-text-info"><i class="bx bx-show"></i></a>
            @can('edit employees')
            <a href="{{ route('employees.edit', $emp) }}" class="btn btn-sm btn-icon btn-text-secondary"><i class="bx bx-edit"></i></a>
            @endcan
            @can('delete employees')
            <form action="{{ route('employees.destroy', $emp) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus karyawan ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-icon btn-text-danger"><i class="bx bx-trash"></i></button>
            </form>
            @endcan
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data karyawan</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $employees->links() }}</div>
</div>
@endsection
