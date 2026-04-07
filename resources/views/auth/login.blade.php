<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login — {{ config('app.name') }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: #fff;
      border-radius: 20px;
      padding: 40px 36px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0,0,0,.2);
    }
    .brand-logo {
      width: 52px; height: 52px;
      background: linear-gradient(135deg, #696cff, #764ba2);
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      font-size: 26px; font-weight: 800; color: white;
      margin: 0 auto 16px;
    }
    .login-title { font-size: 24px; font-weight: 700; color: #233446; }
    .login-subtitle { color: #8592a3; font-size: 14px; }
    .form-control {
      border-radius: 10px;
      border: 1.5px solid #e0e0e0;
      padding: 10px 14px;
      font-size: 14px;
      transition: border .2s;
    }
    .form-control:focus {
      border-color: #696cff;
      box-shadow: 0 0 0 3px rgba(105,108,255,.15);
    }
    .form-label { font-weight: 500; font-size: 14px; }
    .btn-login {
      background: linear-gradient(135deg, #696cff, #5a5cdb);
      border: none;
      border-radius: 10px;
      padding: 11px;
      font-weight: 600;
      font-size: 15px;
      letter-spacing: .3px;
      transition: all .2s;
    }
    .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(105,108,255,.4); }
    .input-group-text {
      background: #f8f8ff;
      border: 1.5px solid #e0e0e0;
      border-left: none;
      border-radius: 0 10px 10px 0;
      cursor: pointer;
    }
    .input-group .form-control {
      border-right: none;
      border-radius: 10px 0 0 10px;
    }
    .alert { border-radius: 10px; border: none; font-size: 14px; }
    .demo-accounts { background: #f8f8ff; border-radius: 10px; padding: 12px 14px; font-size: 12px; }
  </style>
</head>
<body>

<div class="login-card">
  <div class="text-center mb-4">
    <div class="brand-logo">A</div>
    <h4 class="login-title">Selamat Datang! 👋</h4>
    <p class="login-subtitle mb-0">Masuk ke sistem manajemen SDM</p>
  </div>

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible">
      <i class='bx bx-x-circle me-1'></i>{{ $errors->first() }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control"
        placeholder="email@contoh.com" value="{{ old('email') }}" autofocus required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <div class="input-group">
        <input type="password" name="password" id="passInput" class="form-control"
          placeholder="••••••••" required>
        <span class="input-group-text" onclick="togglePass()">
          <i class='bx bx-hide' id="eyeIcon"></i>
        </span>
      </div>
    </div>
    <div class="mb-4 d-flex align-items-center justify-content-between">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label" for="remember" style="font-size:13px">Ingat saya</label>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-login w-100 text-white">
      Masuk ke Sistem
    </button>
  </form>

  <div class="demo-accounts mt-4">
    <p class="fw-semibold mb-1" style="color:#696cff">Akun Demo:</p>
    <p class="mb-1">👑 <strong>superadmin@absensi.local</strong> — password</p>
    <p class="mb-1">📋 <strong>hrd@absensi.local</strong> — password</p>
    <p class="mb-0">👤 <strong>karyawan@absensi.local</strong> — password</p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass() {
  const input = document.getElementById('passInput');
  const icon  = document.getElementById('eyeIcon');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'bx bx-show';
  } else {
    input.type = 'password';
    icon.className = 'bx bx-hide';
  }
}
</script>
</body>
</html>
