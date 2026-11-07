<div>
    <!-- No surplus words or unnecessary actions. - Marcus Aurelius -->
</div>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrator Sign In - Campus Coin</title>
  
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/responsive.css') }}">
  <style>
    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 2.5rem 1rem;
      background: radial-gradient(circle at 50% 15%, rgba(13, 148, 136, 0.08) 0%, transparent 60%),
                  radial-gradient(circle at 85% 85%, rgba(37, 99, 235, 0.05) 0%, transparent 50%);
      position: relative;
    }

    .auth-card {
      width: 100%;
      max-width: 420px;
      border-radius: var(--radius-xl);
      border: 1px solid var(--border-color);
      background-color: var(--bg-card);
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
      padding: 2.25rem;
      transition: all var(--transition-normal);
    }

    .theme-top-toggle {
      position: absolute;
      top: 1.5rem;
      right: 1.5rem;
    }

    .auth-input-group .input-group-text {
      background-color: transparent;
      border-right: none;
      color: var(--text-muted);
    }

    .auth-input-group .form-control {
      border-left: none;
      padding-left: 0;
    }

    .auth-input-group .form-control:focus {
      border-color: var(--border-color);
      box-shadow: none;
    }

    .auth-input-group:focus-within {
      box-shadow: 0 0 0 3px var(--primary-glow);
      border-radius: var(--radius-md);
    }

    .auth-input-group:focus-within .input-group-text,
    .auth-input-group:focus-within .form-control,
    .auth-input-group:focus-within .btn {
      border-color: var(--primary);
    }
  </style>
</head>
<body>

  <div class="auth-wrapper">
    <!-- Top Floating Theme Toggle & Home Button -->
    <div class="theme-top-toggle d-flex align-items-center gap-2">
      <a href="{{ route('admin.login') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Admin Sign In">
        <i class="bi bi-house me-1"></i> Home
      </a>
      <button class="btn btn-sm btn-outline-secondary rounded-circle" id="themeToggleBtn" title="Toggle Dark/Light Mode" style="width: 36px; height: 36px;">
        <i class="bi bi-moon-stars"></i>
      </button>
    </div>

    <!-- Central Clean Auth Card -->
    <div class="auth-card">
      <!-- Brand Logo Header with Admin Badge -->
      <div class="text-center mb-4">
        <a href="{{ route('admin.login') }}" class="brand-logo-text justify-content-center mb-2 fs-3">
          Campus<span class="brand-highlight">Coin</span>
        </a>
        <div class="mb-2">
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">Administrator Portal</span>
        </div>
        <h4 class="fw-bold mb-1">System Console Sign In</h4>
        <p class="text-muted small mb-0">Authorized university system administrators only</p>
      </div>

      <!-- Alert Box -->
      @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
      @endif

      <!-- Admin Login Form -->
      <form id="adminLoginForm" action="{{ route('login') }}" method="POST">
        @csrf
        <!-- Email -->
        <div class="mb-3">
          <label for="adminEmail" class="form-label small fw-semibold">Admin Account Email</label>
          <div class="input-group auth-input-group">
            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
            <input type="email" class="form-control" id="adminEmail" name="email" placeholder="Enter Your Email" value="{{ old('email') }}" autocomplete="username" required>
          </div>
        </div>

        <!-- Password -->
        <div class="mb-4">
          <label for="adminPassword" class="form-label small fw-semibold">Root Password</label>
          <div class="input-group auth-input-group">
            <span class="input-group-text"><i class="bi bi-key"></i></span>
            <input type="password" class="form-control" id="adminPassword" name="password" placeholder="Enter Your Password" autocomplete="current-password" required>
            <button class="btn btn-outline-secondary border-start-0 toggle-password-btn" type="button" data-target="adminPassword" title="Show/Hide">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <!-- Sign In Button -->
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
          Authenticate Administrator
        </button>
      </form>

      <!-- Footer switch -->
      <div class="text-center mt-4 pt-3 border-top">
        <a href="{{ route('admin.login') }}" class="small fw-semibold text-decoration-none">
          <i class="bi bi-speedometer2 me-1"></i> Open Administrator Console
        </a>
      </div>
    </div>

    <!-- Return to Home -->
    <div class="text-center mt-3">
      <a href="{{ route('admin.login') }}" class="text-muted small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Return to Administrator Console
      </a>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="{{ asset('js/admin/app.js') }}"></script>
  <script src="{{ asset('js/admin/auth.js') }}"></script>
</body>
</html>

