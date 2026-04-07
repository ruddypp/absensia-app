@extends('layouts.app')
@section('title', 'Komponen Gaji')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Master Data /</span> Komponen Gaji</h4>
  @can('create salary components')
  <a href="{{ route('salary-components.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Tambah Komponen</a>
  @endcan
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr><th>#</th><th>Nama</th><th>Tipe</th><th>Perhitungan</th><th>Nilai</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($components as $comp)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $comp->name }}</td>
          <td>{!! $comp->type === 'allowance' ? '<span class="badge bg-label-success">Tunjangan</span>' : '<span class="badge bg-label-danger">Potongan</span>' !!}</td>
          <td><span class="badge bg-label-secondary">{{ $comp->calculation_type === 'fixed' ? 'Tetap' : 'Persentase' }}</span></td>
          <td>
            @if($comp->calculation_type === 'fixed')
              Rp {{ number_format($comp->amount, 0, ',', '.') }}
            @else
              {{ $comp->amount }}%
            @endif
          </td>
          <td>{!! $comp->is_active ? '<span class="badge bg-label-success">Aktif</span>' : '<span class="badge bg-label-secondary">Non-aktif</span>' !!}</td>
          <td>
            @can('edit salary components')
            <a href="{{ route('salary-components.edit', $comp) }}" class="btn btn-sm btn-icon btn-text-secondary"><i class="bx bx-edit"></i></a>
            @endcan
            @can('delete salary components')
            <form action="{{ route('salary-components.destroy', $comp) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-icon btn-text-danger"><i class="bx bx-trash"></i></button>
            </form>
            @endcan
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada komponen gaji</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $components->links() }}</div>
</div>
@endsection
