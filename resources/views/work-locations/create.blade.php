@extends('layouts.app')
@section('title', 'Tambah Lokasi Kerja')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Lokasi Kerja /</span> Tambah</h4>
  <a href="{{ route('work-locations.index') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ route('work-locations.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Latitude <span class="text-danger">*</span></label>
          <input type="number" name="latitude" step="any" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude') }}" placeholder="-6.200000" required>
          @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Longitude <span class="text-danger">*</span></label>
          <input type="number" name="longitude" step="any" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude') }}" placeholder="106.816666" required>
          @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Radius (meter) <span class="text-danger">*</span></label>
          <input type="number" name="radius_meters" class="form-control @error('radius_meters') is-invalid @enderror" value="{{ old('radius_meters', 100) }}" min="10" max="5000" required>
          @error('radius_meters')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
            <label class="form-check-label" for="isActive">Aktif</label>
          </div>
        </div>
        <div class="col-12">
          <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            Tips: Gunakan <a href="https://www.google.com/maps" target="_blank">Google Maps</a> untuk mendapatkan koordinat yang akurat. Klik kanan di peta → Salin koordinat.
          </div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
