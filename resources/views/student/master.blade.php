@php
    $currentUser = Auth::user();
    $profileName = $currentUser?->name ?: 'Student';
    $profileEmail = $currentUser?->email ?: 'student@campuscoin.local';
    $profileInitials = collect(explode(' ', trim($profileName)))
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('') ?: 'ST';
    $profileAcademicYear = $currentUser?->academic_year ?: '';
    $profileAllowance = $currentUser?->monthly_allowance_baseline ?? 0;
    $profileGoal = $currentUser?->monthly_saving_goal ?? 0;
    $unreadNotificationsCount = $currentUser
      ? \App\Models\StudentNotification::query()->where('user_id', $currentUser->id)->whereNull('read_at')->count()
      : 0;
@endphp

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Campus Coin')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/student/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/student/css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('css/student/css/responsive.css') }}">
  <link rel="stylesheet" href="{{ asset('css/campus-jungle-theme.css') }}">
  @stack('styles')
</head>
<body>
  <div class="app-container">
    <aside class="app-sidebar" id="appSidebar">
      <div class="sidebar-header">
        <a href="{{ url('/dashboard') }}" class="sidebar-brand">
          <div class="brand-icon-box"><i class="bi bi-coin"></i></div>
          <div class="brand-logo-text">Campus<span class="brand-highlight">Coin</span></div>
        </a>
        <button class="btn btn-sm btn-link d-lg-none text-white p-0" id="closeSidebarBtn" title="Close Menu" type="button">
          <i class="bi bi-x-lg fs-5"></i>
        </button>
      </div>

      <ul class="sidebar-menu">
        <li class="sidebar-label">Main Menu</li>
        @foreach ([
          'dashboard' => ['Dashboard', 'bi-grid-1x2'],
          'transactions' => ['Transactions', 'bi-arrow-left-right'],
          'add-transaction' => ['Add Transaction', 'bi-plus-circle'],
          'csv-import' => ['CSV Import', 'bi-file-earmark-arrow-up'],
          'budgets' => ['Budgets', 'bi-pie-chart'],
          'reports' => ['Reports', 'bi-file-earmark-bar-graph'],
          'saving-tips' => ['Saving Tips', 'bi-lightbulb'],
          'categories' => ['Categories', 'bi-tags'],
          'bookmarks' => ['Bookmarks', 'bi-bookmark-star'],
          'notifications' => ['Notifications', 'bi-bell'],
          'profile' => ['Student Profile', 'bi-person-circle'],
          'settings' => ['Settings', 'bi-gear']
        ] as $route => $item)
          <li class="sidebar-item">
            @php
              $isActive = match ($route) {
                'transactions' => request()->routeIs('transactions.index', 'transactions.edit'),
                'add-transaction' => request()->routeIs('transactions.create', 'student.add-transaction'),
                default => request()->is($route),
              };
            @endphp
            <a href="{{ url('/' . $route) }}" class="sidebar-link {{ $isActive ? 'active' : '' }}">
              <i class="bi {{ $item[1] }}"></i>
              <span>{{ $item[0] }}</span>
            </a>
          </li>
        @endforeach
      </ul>

      <div class="sidebar-footer">
        <div class="user-profile-summary">
          <div class="user-avatar-circle user-avatar-sm">{{ $profileInitials }}</div>
          <div class="overflow-hidden flex-grow-1">
            <div class="user-name text-truncate">{{ $profileName }}</div>
            <div class="user-sub">{{ $profileAcademicYear ?: 'Student' }}</div>
          </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
          @csrf
          <button type="submit" class="btn btn-logout w-100">
            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
          </button>
        </form>
      </div>
    </aside>

    <main class="app-main">
      <header class="app-topbar">
        <div class="d-flex align-items-center gap-3">
          <button class="icon-button d-lg-none mobile-toggle-btn" id="mobileNavToggle" title="Open Menu" type="button">
            <i class="bi bi-list fs-4"></i>
          </button>
          <div class="topbar-greeting">
            <h2>@yield('page_heading', 'Campus Coin')</h2>
            <p>@yield('page_description', 'Manage your student finances with confidence.')</p>
          </div>
        </div>

        <div class="topbar-actions">
          <!-- Global Multi-Currency Switcher (PKR / USD) for International Sales -->
          <div class="dropdown currency-switcher-dropdown">
            <button class="currency-toggle-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="currencySwitcherBtn" title="Select Global Currency">
              <span class="currency-flag">🇵🇰</span> <span class="currency-code">PKR</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
              <li>
                <button class="dropdown-item currency-option active" type="button" data-currency="PKR">
                  <span>🇵🇰 Pakistani Rupee</span>
                  <span class="badge">PKR (Rs)</span>
                </button>
              </li>
              <li>
                <button class="dropdown-item currency-option" type="button" data-currency="USD">
                  <span>🇺🇸 US Dollar</span>
                  <span class="badge">USD ($)</span>
                </button>
              </li>
            </ul>
          </div>

          <a href="{{ url('/notifications') }}" class="icon-button text-decoration-none" title="Notifications">
            <i class="bi bi-bell"></i>
            @if ($unreadNotificationsCount > 0)
              <span class="badge-dot"></span>
            @endif
          </a>
          <a href="{{ url('/profile') }}" class="d-flex align-items-center gap-2 text-decoration-none" title="Student Profile">
            <span class="user-avatar-circle user-avatar-sm">{{ $profileInitials }}</span>
            <span class="text-white fw-semibold d-none d-md-inline">{{ $profileName }}</span>
          </a>
        </div>
      </header>

      <div class="app-content">
        @yield('breadcrumbs')
        @yield('content')
      </div>

      <footer class="app-footer">
        <span>&copy; {{ date('Y') }} Campus Coin</span>
        <span>Student FinTech Terminal & Multi-Currency Engine</span>
      </footer>
    </main>
  </div>

  <script>
    window.currentUser = {
      name: {!! json_encode($profileName) !!},
      email: {!! json_encode($profileEmail) !!},
      academic_year: {!! json_encode($profileAcademicYear) !!},
      monthly_allowance_baseline: {!! json_encode((float) $profileAllowance) !!},
      monthly_saving_goal: {!! json_encode((float) $profileGoal) !!},
      university: {!! json_encode(optional($currentUser)->university ?: '') !!},
      joined_date: {!! json_encode(optional($currentUser)->created_at?->format('M Y') ?: '') !!}
    };
    window.logoutRoute = {!! json_encode(route('logout')) !!};
    window.accountStatusRoute = {!! json_encode(route('account.status')) !!};
    window.csrfToken = {!! json_encode(csrf_token()) !!};
    window.serverUnreadNotificationsCount = {{ $unreadNotificationsCount }};
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/currency-engine.js') }}"></script>
  <script src="{{ asset('js/student/app.js') }}"></script>
  @stack('scripts')
</body>
</html>
