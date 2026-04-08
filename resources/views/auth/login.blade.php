<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login — {{ config('app.name') }}</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8f9fa;
      /* Clean light gray background */
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      padding: 20px;
    }

    .login-container {
      width: 100%;
      max-width: 450px;
    }

    .login-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 48px 40px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
      border: 1px solid #f1f1f4;
    }

    .brand-logo {
      width: 48px;
      height: 48px;
      background: #696cff;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 24px;
    }

    .login-title {
      font-size: 24px;
      font-weight: 700;
      color: #181c32;
      margin-bottom: 8px;
      letter-spacing: -0.5px;
    }

    .login-subtitle {
      color: #a1a5b7;
      font-size: 15px;
      font-weight: 400;
      margin-bottom: 32px;
    }

    .form-label {
      font-weight: 500;
      font-size: 14px;
      color: #3f4254;
      margin-bottom: 8px;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid #e4e6ef;
      padding: 12px 16px;
      font-size: 15px;
      color: #181c32;
      background-color: #ffffff;
      transition: all 0.2s ease;
    }

    .form-control::placeholder {
      color: #b5b5c3;
    }

    .form-control:focus {
      border-color: #696cff;
      box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.15);
      background-color: #ffffff;
    }

    .input-group-text {
      background-color: #ffffff;
      border: 1px solid #e4e6ef;
      border-left: none;
      border-radius: 0 10px 10px 0;
      color: #a1a5b7;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .input-group .form-control {
      border-right: none;
    }

    .input-group .form-control:focus+.input-group-text {
      border-color: #696cff;
      background-color: #ffffff;
      color: #181c32;
    }

    .form-check-input {
      border-color: #e4e6ef;
      cursor: pointer;
    }

    .form-check-input:checked {
      background-color: #696cff;
      border-color: #696cff;
    }

    .form-check-label {
      color: #7e8299;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
    }

    .btn-primary {
      background-color: #696cff;
      border: none;
      border-radius: 10px;
      padding: 14px;
      font-weight: 600;
      font-size: 15px;
      transition: all 0.2s ease;
      color: #ffffff;
    }

    .btn-primary:hover {
      background-color: #5f61e6;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(105, 108, 255, 0.25);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .alert {
      border-radius: 10px;
      border: none;
      font-size: 14px;
      padding: 14px 16px;
      display: flex;
      align-items: center;
    }

    .alert-danger {
      background-color: #fff5f8;
      color: #f1416c;
    }

    .alert .btn-close {
      font-size: 10px;
    }

    /* Subtle footer text */
    .footer-text {
      text-align: center;
      margin-top: 24px;
      color: #a1a5b7;
      font-size: 13px;
      font-weight: 400;
    }
  </style>
</head>

<body>

  <div class="login-container">
    <div class="login-card">

      <h1 class="login-title">Sign In</h1>
      <p class="login-subtitle">Masuk ke sistem manajemen AbsensiApp</p>

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class='bx bx-error-circle fs-5 me-2'></i>
          <span>{{ $errors->first() }}</span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="mb-4">
          <label class="form-label" for="email">Email</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="admin@example.com"
            value="{{ old('email') }}" autocomplete="email" autofocus required>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label mb-0" for="passInput">Password</label>
            <!-- Optional: add a "Forgot Password?" link here if needed in the future -->
          </div>
          <div class="input-group">
            <input type="password" name="password" id="passInput" class="form-control" placeholder="••••••••"
              autocomplete="current-password" required>
            <span class="input-group-text" onclick="togglePass()" id="toggleIconContainer">
              <i class='bx bx-hide fs-5' id="eyeIcon"></i>
            </span>
          </div>
        </div>

        <div class="mb-4 d-flex align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center">
          Sign In <i class='bx bx-right-arrow-alt ms-2 fs-5'></i>
        </button>
      </form>

    </div>

    <div class="footer-text">
      &copy; {{ date('Y') }} AbsensiApp. All rights reserved.
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePass() {
      const input = document.getElementById('passInput');
      const icon = document.getElementById('eyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bx bx-show fs-5';
      } else {
        input.type = 'password';
        icon.className = 'bx bx-hide fs-5';
      }
    }
  </script>
</body>

</html>