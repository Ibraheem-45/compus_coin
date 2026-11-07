<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Administrator | Campus Coin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/responsive.css') }}">
  <link rel="stylesheet" href="{{ asset('css/campus-jungle-theme.css') }}">
</head>
<body class="admin-auth-page auth-page">
  <div class="auth-wrapper">
    <div class="auth-card" style="max-width: 480px; width: 100%; margin: 40px auto; background: rgba(5, 20, 10, 0.85); border: 1px solid rgba(201, 162, 39, 0.3); border-radius: 20px; padding: 36px;">
      <div class="text-center mb-4">
        <a href="{{ url('/') }}" class="brand-logo-text justify-content-center mb-2 fs-3 text-decoration-none">
          Campus<span class="brand-highlight">Coin</span>
        </a>
        <div class="mb-2">
          <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">Root Governance</span>
        </div>
        <h4 class="fw-bold mb-1 text-white">Create Administrator Account</h4>
        <p class="text-light opacity-75 small mb-0">Provision high-privilege credentials for campus budgeting governance</p>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('admin.register.store') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label small fw-semibold text-white">Administrator Full Name</label>
          <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-white-50"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control bg-dark border-secondary text-white" name="name" value="{{ old('name') }}" placeholder="e.g. Prof. Ibraheem" required autofocus>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-semibold text-white">Official Admin Email</label>
          <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-white-50"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control bg-dark border-secondary text-white" name="email" value="{{ old('email') }}" placeholder="admin@university.edu" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-semibold text-white">Root Password</label>
          <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-white-50"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control bg-dark border-secondary text-white" name="password" placeholder="At least 8 characters" required>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-semibold text-white">Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-white-50"><i class="bi bi-shield-lock"></i></span>
            <input type="password" class="form-control bg-dark border-secondary text-white" name="password_confirmation" placeholder="Re-enter password" required>
          </div>
        </div>

        <button type="submit" class="btn btn-warning w-100 py-2 fw-bold shadow-sm rounded-3" style="background: var(--gold); border: none; color: #05140a;">
          <i class="bi bi-shield-check me-1"></i> Register As Administrator
        </button>
      </form>

      <div class="text-center mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
        <span class="small text-white-50">Already have an admin account?</span>
        <a href="{{ route('admin.login') }}" class="small fw-bold text-decoration-none ms-1" style="color: var(--gold);">
          Sign In Here &rarr;
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
