@extends('layouts.app')
@section('title', 'Edit Lokasi Kerja')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Lokasi Kerja /</span> Edit</h4>
  <a href="{{ route('work-locations.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('work-locations.update', $workLocation) }}" method="POST">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $workLocation->name) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Latitude <span class="text-danger">*</span></label>
          <input type="number" name="latitude" step="any" class="form-control" value="{{ old('latitude', $workLocation->latitude) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Longitude <span class="text-danger">*</span></label>
          <input type="number" name="longitude" step="any" class="form-control" value="{{ old('longitude', $workLocation->longitude) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Radius (meter) <span class="text-danger">*</span></label>
          <input type="number" name="radius_meters" class="form-control" value="{{ old('radius_meters', $workLocation->radius_meters) }}" min="10" required>
        </div>
        <div class="col-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $workLocation->is_active ? 'checked' : '' }}>
            <label class="form-check-label" for="isActive">Aktif</label>
          </div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
