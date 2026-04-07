@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endpush

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="mb-1">Selamat Datang, <strong>{{ auth()->user()->name }}</strong>!</h5>
          <p class="text-muted mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
      </div>
    </div>
  </div>

  @if(auth()->user()->hasRole(['super_admin', 'hrd']))
    {{-- Dashboard HRD / Super Admin --}}
    <div class="row g-4 mb-4">
      <div class="col-sm-6 col-xl-3">
        <div class="card">
          <div class="card-body d-flex justify-content-between align-items-start">
            <div>
              <span class="fw-semibold d-block mb-1">Total Karyawan</span>
              <h3 class="card-title mb-0">{{ $total_employees }}</h3>
            </div>
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-group bx-lg"></i></span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card">
          <div class="card-body d-flex justify-content-between align-items-start">
            <div>
              <span class="fw-semibold d-block mb-1">Hadir Hari Ini</span>
              <h3 class="card-title mb-0 text-success">{{ $present_today }}</h3>
            </div>
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-square bx-lg"></i></span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card">
          <div class="card-body d-flex justify-content-between align-items-start">
            <div>
              <span class="fw-semibold d-block mb-1">Terlambat</span>
              <h3 class="card-title mb-0 text-warning">{{ $late_today }}</h3>
            </div>
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time bx-lg"></i></span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card">
          <div class="card-body d-flex justify-content-between align-items-start">
            <div>
              <span class="fw-semibold d-block mb-1">Cuti Pending</span>
              <h3 class="card-title mb-0 text-info">{{ $pending_leaves }}</h3>
            </div>
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded bg-label-info"><i class="bx bx-calendar-x bx-lg"></i></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      {{-- Chart Absensi --}}
      <div class="col-xl-8">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0">Grafik Absensi Bulan Ini</h5>
          </div>
          <div class="card-body">
            <div id="chartAttendance" style="min-height:250px"></div>
          </div>
        </div>
      </div>
      {{-- Stats samping --}}
      <div class="col-xl-4">
        <div class="card mb-4">
          <div class="card-body">
            <h6 class="fw-semibold mb-3">Status Hari Ini</h6>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Hadir</span>
              <span class="badge bg-label-success">{{ $present_today }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Terlambat</span>
              <span class="badge bg-label-warning">{{ $late_today }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Tidak Hadir</span>
              <span class="badge bg-label-danger">{{ $absent_today }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Cuti Pending</span>
              <span class="badge bg-label-info">{{ $pending_leaves }}</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Lembur Pending</span>
              <span class="badge bg-label-secondary">{{ $pending_overtime }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Tabel absensi terkini --}}
    <div class="card mt-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Absensi Terkini Hari Ini</h5>
        <a href="{{ route('attendances.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>Karyawan</th>
              <th>Departemen</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recent_attendances as $att)
              <tr>
                <td>{{ $att->employee->name ?? '-' }}</td>
                <td>{{ $att->employee->department->name ?? '-' }}</td>
                <td>{{ $att->check_in ?? '-' }}</td>
                <td>{{ $att->check_out ?? '-' }}</td>
                <td>{!! $att->status_badge !!}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">Belum ada absensi hari ini</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  @else
    {{-- Dashboard Karyawan --}}
    @php $emp = $employee; @endphp

    @if(!$emp)
      <div class="alert alert-warning">
        <i class="bx bx-info-circle me-2"></i>
        Akun Anda belum terhubung ke data karyawan. Hubungi HRD.
      </div>
    @else
      <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <span class="fw-semibold d-block mb-1">Hadir Bulan Ini</span>
              <h3 class="text-success mb-0">{{ $monthly_present }}</h3>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <span class="fw-semibold d-block mb-1">Terlambat Bulan Ini</span>
              <h3 class="text-warning mb-0">{{ $monthly_late }}</h3>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <span class="fw-semibold d-block mb-1">Cuti Pending</span>
              <h3 class="text-info mb-0">{{ $pending_leaves }}</h3>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <span class="fw-semibold d-block mb-1">Gaji Terakhir</span>
              <h5 class="mb-0">
                @if($latest_payroll)
                  Rp {{ number_format($latest_payroll->net_salary, 0, ',', '.') }}
                @else
                  <span class="text-muted">Belum ada</span>
                @endif
              </h5>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Status Absensi Hari Ini</h5>
          <a href="{{ route('attendances.checkin') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-camera me-1"></i> Check-in / Check-out
          </a>
        </div>
        <div class="card-body">
          @if($today_attendance)
            <div class="row">
              <div class="col-6">
                <div class="d-flex align-items-center p-3 rounded bg-label-success">
                  <i class="bx bx-log-in fs-3 me-3 text-success"></i>
                  <div>
                    <div class="text-muted small">Check-in</div>
                    <div class="fw-bold">{{ $today_attendance->check_in ?? '-' }}</div>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div
                  class="d-flex align-items-center p-3 rounded {{ $today_attendance->check_out ? 'bg-label-info' : 'bg-label-secondary' }}">
                  <i class="bx bx-log-out fs-3 me-3"></i>
                  <div>
                    <div class="text-muted small">Check-out</div>
                    <div class="fw-bold">{{ $today_attendance->check_out ?? 'Belum' }}</div>
                  </div>
                </div>
              </div>
            </div>
          @else
            <div class="alert alert-warning mb-0">
              <i class="bx bx-time me-2"></i> Anda belum melakukan absensi hari ini.
            </div>
          @endif
        </div>
      </div>
    @endif
  @endif
@endsection

@push('scripts')
  <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
  <script>
    @if(auth()->user()->hasRole(['super_admin', 'hrd']))
      const chartData = @json($chart_attendance);
      const options = {
        chart: { type: 'bar', height: 250, toolbar: { show: false } },
        series: [
          { name: 'Hadir', data: chartData.present },
          { name: 'Terlambat', data: chartData.late },
        ],
        xaxis: { categories: chartData.labels },
        colors: ['#696cff', '#ffab00'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
        legend: { position: 'top' },
      };
      if (document.getElementById('chartAttendance')) {
        new ApexCharts(document.getElementById('chartAttendance'), options).render();
      }
    @endif
  </script>
@endpush