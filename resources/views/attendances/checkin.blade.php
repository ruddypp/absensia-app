@extends('layouts.app')
@section('title', 'Check-in / Check-out')
@push('styles')
<style>
  #video-preview { width: 100%; border-radius: 12px; background: #000; min-height: 200px; }
  #canvas-preview { display: none; }
  .gps-badge { font-size: 0.8rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Check-in / Check-out</h5>
        <span class="badge bg-label-primary" id="current-time">{{ now()->format('H:i:s') }}</span>
      </div>
      <div class="card-body">
        {{-- Status hari ini --}}
        @if($todayAttendance)
          <div class="row mb-4">
            <div class="col-6">
              <div class="d-flex align-items-center p-3 rounded bg-label-success">
                <i class="bx bx-log-in fs-3 me-3 text-success"></i>
                <div>
                  <div class="text-muted small">Check-in</div>
                  <div class="fw-bold">{{ $todayAttendance->check_in ?? '-' }}</div>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center p-3 rounded {{ $todayAttendance->check_out ? 'bg-label-info' : 'bg-label-secondary' }}">
                <i class="bx bx-log-out fs-3 me-3"></i>
                <div>
                  <div class="text-muted small">Check-out</div>
                  <div class="fw-bold">{{ $todayAttendance->check_out ?? 'Belum' }}</div>
                </div>
              </div>
            </div>
          </div>
        @endif

        @if(!$todayAttendance || !$todayAttendance->check_out)
          {{-- Kamera --}}
          <div class="mb-3">
            <video id="video-preview" autoplay playsinline></video>
            <canvas id="canvas-preview"></canvas>
          </div>

          {{-- Status GPS --}}
          <div class="mb-3 d-flex align-items-center gap-2">
            <span class="badge bg-label-warning gps-badge" id="gps-status">
              <i class="bx bx-map-pin"></i> Mendeteksi lokasi...
            </span>
            <small class="text-muted" id="gps-address"></small>
          </div>

          {{-- Tombol --}}
          @if(!$todayAttendance)
            <button class="btn btn-primary w-100 btn-lg" id="btn-checkin" disabled>
              <i class="bx bx-camera me-2"></i> Check-in Sekarang
            </button>
          @elseif(!$todayAttendance->check_out)
            <button class="btn btn-warning w-100 btn-lg" id="btn-checkout" disabled>
              <i class="bx bx-log-out me-2"></i> Check-out Sekarang
            </button>
          @endif
        @else
          <div class="alert alert-success mb-0">
            <i class="bx bx-check-circle me-2"></i>
            Anda sudah menyelesaikan absensi hari ini. Sampai jumpa besok!
          </div>
        @endif
      </div>
    </div>

    {{-- Riwayat minggu ini --}}
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Absensi Minggu Ini</h5></div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr><th>Tanggal</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr>
            </thead>
            <tbody>
              @forelse(\App\Models\Attendance::where('employee_id', $employee->id)->whereBetween('attendance_date', [now()->startOfWeek(), now()->endOfWeek()])->get() as $att)
              <tr>
                <td>{{ $att->attendance_date->translatedFormat('l, d M') }}</td>
                <td>{{ $att->check_in ?? '-' }}</td>
                <td>{{ $att->check_out ?? '-' }}</td>
                <td>{!! $att->status_badge !!}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let stream, gpsData = null, locationId = null;

async function startCamera() {
  try {
    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
    document.getElementById('video-preview').srcObject = stream;
  } catch (e) {
    alert('Tidak bisa mengakses kamera: ' + e.message);
  }
}

function getLocation() {
  if (!navigator.geolocation) {
    document.getElementById('gps-status').textContent = 'GPS tidak tersedia';
    return;
  }
  navigator.geolocation.getCurrentPosition(async (pos) => {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;
    gpsData = { latitude: lat, longitude: lng };

    const res = await fetch('{{ route("api.attendance.validate-location") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ latitude: lat, longitude: lng })
    });
    const data = await res.json();
    const badge = document.getElementById('gps-status');
    if (data.valid) {
      badge.className = 'badge bg-label-success gps-badge';
      badge.innerHTML = '<i class="bx bx-map-pin"></i> ' + data.location;
      locationId = data.location_id;
      const btn = document.getElementById('btn-checkin') || document.getElementById('btn-checkout');
      if (btn) btn.disabled = false;
    } else {
      badge.className = 'badge bg-label-danger gps-badge';
      badge.innerHTML = '<i class="bx bx-x-circle"></i> Di luar radius kantor';
    }
  }, () => {
    document.getElementById('gps-status').innerHTML = '<i class="bx bx-x-circle"></i> GPS ditolak';
  });
}

function capturePhoto() {
  const video = document.getElementById('video-preview');
  const canvas = document.getElementById('canvas-preview');
  canvas.width = video.videoWidth || 640;
  canvas.height = video.videoHeight || 480;
  canvas.getContext('2d').drawImage(video, 0, 0);
  return canvas.toDataURL('image/jpeg', 0.8);
}

const btnCheckIn = document.getElementById('btn-checkin');
if (btnCheckIn) {
  btnCheckIn.addEventListener('click', async () => {
    btnCheckIn.disabled = true;
    btnCheckIn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    const photo = capturePhoto();
    const res = await fetch('{{ route("attendances.checkin.store") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ photo, ...gpsData, location_id: locationId })
    });
    const data = await res.json();
    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'Gagal check-in');
      btnCheckIn.disabled = false;
      btnCheckIn.innerHTML = '<i class="bx bx-camera me-2"></i> Check-in Sekarang';
    }
  });
}

const btnCheckOut = document.getElementById('btn-checkout');
if (btnCheckOut) {
  btnCheckOut.addEventListener('click', async () => {
    btnCheckOut.disabled = true;
    btnCheckOut.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    const photo = capturePhoto();
    const res = await fetch('{{ route("attendances.checkout.store") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ photo, ...gpsData })
    });
    const data = await res.json();
    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'Gagal check-out');
      btnCheckOut.disabled = false;
      btnCheckOut.innerHTML = '<i class="bx bx-log-out me-2"></i> Check-out Sekarang';
    }
  });
}

setInterval(() => {
  const el = document.getElementById('current-time');
  if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
}, 1000);

startCamera();
getLocation();
</script>
@endpush
