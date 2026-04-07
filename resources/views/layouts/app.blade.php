<!doctype html>
<html lang="id" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  @stack('styles')

  <style>
    :root {
      --primary: #696cff;
      --primary-dark: #5a5cdb;
      --sidebar-width: 260px;
      --sidebar-bg: #fff;
      --topbar-height: 64px;
      --body-bg: #f5f5f9;
      --text-muted: #a1acb8;
      --border-color: #e7e7ff;
    }

    * { box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--body-bg);
      color: #566a7f;
      margin: 0;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      width: var(--sidebar-width);
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      background: var(--sidebar-bg);
      border-right: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      z-index: 1000;
      overflow-y: auto;
      transition: transform .25s ease;
    }
    .sidebar::-webkit-scrollbar { width: 4px; }
    .sidebar::-webkit-scrollbar-thumb { background: #e0e0ff; border-radius: 4px; }

    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 20px 20px 16px;
      text-decoration: none;
      border-bottom: 1px solid var(--border-color);
    }
    .sidebar-brand .brand-icon {
      width: 36px; height: 36px;
      background: var(--primary);
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      color: white; font-weight: 700; font-size: 18px;
    }
    .sidebar-brand .brand-name {
      font-weight: 700; font-size: 16px; color: #233446;
    }

    .sidebar-menu { padding: 8px 0; flex: 1; }

    .menu-header {
      padding: 16px 20px 6px;
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      color: var(--text-muted);
      letter-spacing: .8px;
    }

    .menu-item { display: block; }
    .menu-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 20px;
      color: #566a7f;
      text-decoration: none;
      font-size: 14px;
      border-radius: 0;
      transition: all .15s ease;
      position: relative;
    }
    .menu-link:hover {
      color: var(--primary);
      background: rgba(105,108,255,.06);
    }
    .menu-link.active {
      color: var(--primary);
      background: rgba(105,108,255,.1);
      font-weight: 600;
    }
    .menu-link.active::before {
      content: '';
      position: absolute;
      left: 0; top: 4px; bottom: 4px;
      width: 3px;
      background: var(--primary);
      border-radius: 0 4px 4px 0;
    }
    .menu-link i { font-size: 20px; flex-shrink: 0; }

    /* ===== TOPBAR ===== */
    .topbar {
      height: var(--topbar-height);
      background: #fff;
      border-bottom: 1px solid var(--border-color);
      position: fixed;
      top: 0;
      left: var(--sidebar-width);
      right: 0;
      z-index: 999;
      display: flex;
      align-items: center;
      padding: 0 24px;
      gap: 16px;
    }

    .topbar-toggle {
      background: none; border: none; color: #566a7f;
      font-size: 22px; cursor: pointer; display: none;
      padding: 4px;
    }

    .topbar-right {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .user-avatar {
      width: 38px; height: 38px;
      border-radius: 50%;
      background: var(--primary);
      color: white;
      display: flex; align-items: center; justify-content: center;
      font-weight: 600; font-size: 15px;
      cursor: pointer;
    }

    /* ===== MAIN CONTENT ===== */
    .main-content {
      margin-left: var(--sidebar-width);
      padding-top: var(--topbar-height);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .content-area {
      padding: 24px;
      flex: 1;
    }

    /* ===== CARDS ===== */
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(67,89,113,.08);
    }
    .card-header {
      background: none;
      border-bottom: 1px solid var(--border-color);
      padding: 16px 20px;
    }
    .card-body { padding: 20px; }
    .card-footer {
      background: none;
      border-top: 1px solid var(--border-color);
      padding: 12px 20px;
    }

    /* ===== BADGES ===== */
    .badge { font-weight: 500; font-size: 12px; padding: 4px 10px; border-radius: 6px; }
    .bg-label-primary   { background: rgba(105,108,255,.16) !important; color: #696cff !important; }
    .bg-label-success   { background: rgba(113,221,55,.16) !important;  color: #71dd37 !important; }
    .bg-label-danger    { background: rgba(255,62,29,.16) !important;   color: #ff3e1d !important; }
    .bg-label-warning   { background: rgba(255,171,0,.16) !important;   color: #ffab00 !important; }
    .bg-label-info      { background: rgba(3,195,236,.16) !important;   color: #03c3ec !important; }
    .bg-label-secondary { background: rgba(133,146,163,.16) !important; color: #8592a3 !important; }

    /* ===== STATS CARD ===== */
    .stat-card {
      border-radius: 12px;
      padding: 20px;
      background: #fff;
      box-shadow: 0 2px 6px rgba(67,89,113,.08);
    }
    .stat-icon {
      width: 46px; height: 46px;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px;
    }

    /* ===== TABLE ===== */
    .table { font-size: 14px; }
    .table thead th {
      font-weight: 600;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .5px;
      color: var(--text-muted);
      border-bottom: 1px solid var(--border-color);
      background: #f8f8ff;
    }
    .table-hover tbody tr:hover { background: rgba(105,108,255,.04); }

    /* ===== BUTTONS ===== */
    .btn-primary { background: var(--primary); border-color: var(--primary); }
    .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
    .btn-icon {
      width: 32px; height: 32px;
      padding: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
    }
    .btn-text-secondary { background: none; border: none; color: #566a7f; }
    .btn-text-secondary:hover { background: rgba(133,146,163,.12); color: #566a7f; }
    .btn-text-info { background: none; border: none; color: #03c3ec; }
    .btn-text-info:hover { background: rgba(3,195,236,.12); }
    .btn-text-success { background: none; border: none; color: #71dd37; }
    .btn-text-success:hover { background: rgba(113,221,55,.12); }
    .btn-text-danger { background: none; border: none; color: #ff3e1d; }
    .btn-text-danger:hover { background: rgba(255,62,29,.12); }
    .btn-text-warning { background: none; border: none; color: #ffab00; }
    .btn-text-warning:hover { background: rgba(255,171,0,.12); }

    /* ===== FORM ===== */
    .form-control, .form-select {
      border-radius: 8px;
      border: 1px solid #d9dee3;
      font-size: 14px;
      padding: 8px 14px;
      transition: border .15s;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(105,108,255,.15);
    }
    .form-label { font-weight: 500; font-size: 14px; margin-bottom: 6px; }

    /* ===== ALERTS ===== */
    .alert { border-radius: 10px; border: none; }
    .alert-success { background: rgba(113,221,55,.15); color: #3a7e00; }
    .alert-danger  { background: rgba(255,62,29,.12);  color: #c0392b; }
    .alert-warning { background: rgba(255,171,0,.15);  color: #8a6900; }
    .alert-info    { background: rgba(3,195,236,.12);  color: #006f8b; }

    /* ===== PAGE TITLE ===== */
    .page-title { font-size: 22px; font-weight: 700; color: #233446; }
    .breadcrumb-text { font-size: 14px; color: var(--text-muted); }

    /* ===== AVATAR ===== */
    .avatar { display: inline-flex; align-items: center; }
    .avatar img { border-radius: 50%; width: 38px; height: 38px; object-fit: cover; }
    .avatar-sm img { width: 30px; height: 30px; }

    /* ===== FOOTER ===== */
    .footer {
      padding: 14px 24px;
      background: #fff;
      border-top: 1px solid var(--border-color);
      font-size: 13px;
      color: var(--text-muted);
    }

    /* ===== SIDEBAR OVERLAY (mobile) ===== */
    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.5);
      z-index: 998;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 991px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .sidebar-overlay.show { display: block; }
      .main-content { margin-left: 0; }
      .topbar { left: 0; }
      .topbar-toggle { display: block; }
    }
  </style>
</head>
<body>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <a href="{{ route('dashboard') }}" class="sidebar-brand">
    <div class="brand-icon">A</div>
    <span class="brand-name">AbsensiApp</span>
  </a>

  <nav class="sidebar-menu">
    <div class="menu-item">
      <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class='bx bx-home-circle'></i> Dashboard
      </a>
    </div>

    @canany(['view employees','view departments','view positions','view work locations','view salary components'])
    <div class="menu-header">Master Data</div>
    @can('view employees')
    <div class="menu-item">
      <a href="{{ route('employees.index') }}" class="menu-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
        <i class='bx bx-group'></i> Karyawan
      </a>
    </div>
    @endcan
    @can('view departments')
    <div class="menu-item">
      <a href="{{ route('departments.index') }}" class="menu-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
        <i class='bx bx-building'></i> Departemen
      </a>
    </div>
    @endcan
    @can('view positions')
    <div class="menu-item">
      <a href="{{ route('positions.index') }}" class="menu-link {{ request()->routeIs('positions.*') ? 'active' : '' }}">
        <i class='bx bx-badge'></i> Jabatan
      </a>
    </div>
    @endcan
    @can('view work locations')
    <div class="menu-item">
      <a href="{{ route('work-locations.index') }}" class="menu-link {{ request()->routeIs('work-locations.*') ? 'active' : '' }}">
        <i class='bx bx-map'></i> Lokasi Kerja
      </a>
    </div>
    @endcan
    @can('view salary components')
    <div class="menu-item">
      <a href="{{ route('salary-components.index') }}" class="menu-link {{ request()->routeIs('salary-components.*') ? 'active' : '' }}">
        <i class='bx bx-money'></i> Komponen Gaji
      </a>
    </div>
    @endcan
    @endcanany

    <div class="menu-header">Absensi</div>
    @can('view all attendances')
    <div class="menu-item">
      <a href="{{ route('attendances.index') }}" class="menu-link {{ request()->routeIs('attendances.index') ? 'active' : '' }}">
        <i class='bx bx-calendar-check'></i> Semua Absensi
      </a>
    </div>
    @endcan
    @can('view own attendance')
    <div class="menu-item">
      <a href="{{ route('attendances.checkin') }}" class="menu-link {{ request()->routeIs('attendances.checkin') ? 'active' : '' }}">
        <i class='bx bx-camera'></i> Check-in / Check-out
      </a>
    </div>
    <div class="menu-item">
      <a href="{{ route('attendances.my') }}" class="menu-link {{ request()->routeIs('attendances.my') ? 'active' : '' }}">
        <i class='bx bx-list-check'></i> Absensi Saya
      </a>
    </div>
    @endcan

    <div class="menu-header">Cuti & Lembur</div>
    @canany(['view all leaves','view own leaves'])
    <div class="menu-item">
      <a href="{{ route('leave-requests.index') }}" class="menu-link {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}">
        <i class='bx bx-calendar-x'></i> Pengajuan Cuti
      </a>
    </div>
    @endcanany
    @canany(['view all overtimes','view own overtimes'])
    <div class="menu-item">
      <a href="{{ route('overtime-requests.index') }}" class="menu-link {{ request()->routeIs('overtime-requests.*') ? 'active' : '' }}">
        <i class='bx bx-time'></i> Pengajuan Lembur
      </a>
    </div>
    @endcanany

    <div class="menu-header">Penggajian</div>
    @can('view all payrolls')
    <div class="menu-item">
      <a href="{{ route('payrolls.index') }}" class="menu-link {{ request()->routeIs('payrolls.index') ? 'active' : '' }}">
        <i class='bx bx-wallet'></i> Kelola Payroll
      </a>
    </div>
    @endcan
    @can('view own payroll')
    <div class="menu-item">
      <a href="{{ route('payrolls.my') }}" class="menu-link {{ request()->routeIs('payrolls.my') ? 'active' : '' }}">
        <i class='bx bx-receipt'></i> Slip Gaji Saya
      </a>
    </div>
    @endcan
    @canany(['view all bonuses','view own bonuses'])
    <div class="menu-item">
      <a href="{{ route('bonuses.index') }}" class="menu-link {{ request()->routeIs('bonuses.*') ? 'active' : '' }}">
        <i class='bx bx-gift'></i> Bonus
      </a>
    </div>
    @endcanany

    @can('view reports')
    <div class="menu-header">Laporan</div>
    <div class="menu-item">
      <a href="{{ route('reports.attendance') }}" class="menu-link {{ request()->routeIs('reports.attendance') ? 'active' : '' }}">
        <i class='bx bx-bar-chart-alt-2'></i> Laporan Absensi
      </a>
    </div>
    <div class="menu-item">
      <a href="{{ route('reports.payroll') }}" class="menu-link {{ request()->routeIs('reports.payroll') ? 'active' : '' }}">
        <i class='bx bx-spreadsheet'></i> Laporan Gaji
      </a>
    </div>
    @endcan
  </nav>
</aside>

<!-- Top Bar -->
<nav class="topbar">
  <button class="topbar-toggle" onclick="openSidebar()">
    <i class='bx bx-menu'></i>
  </button>

  <div class="topbar-right">
    <div class="dropdown">
      <div class="user-avatar" data-bs-toggle="dropdown" title="{{ auth()->user()->name }}">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
      <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:200px; border:none; border-radius:12px;">
        <li class="px-3 py-2">
          <div class="fw-semibold">{{ auth()->user()->name }}</div>
          <div class="text-muted small">{{ auth()->user()->getRoleNames()->first() }}</div>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dropdown-item text-danger" type="submit">
              <i class='bx bx-power-off me-2'></i> Keluar
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="main-content">
  <div class="content-area">

    {{-- Flash Messages --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class='bx bx-x-circle me-2'></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @yield('content')
  </div>

  <footer class="footer">
    © {{ date('Y') }} <strong>AbsensiApp</strong> — Sistem Manajemen SDM
  </footer>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>

<script>
function openSidebar() {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebarOverlay').classList.add('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('show');
}
</script>

@stack('scripts')
</body>
</html>
