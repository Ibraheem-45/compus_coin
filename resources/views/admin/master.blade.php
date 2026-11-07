<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $pageTitle ?? 'Administrator Console' }} | Campus Coin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/responsive.css') }}">
  <link rel="stylesheet" href="{{ asset('css/campus-jungle-theme.css') }}">
</head>
<body>
  <div class="app-container">
    <aside class="app-sidebar admin-sidebar" id="appSidebar">
      <div class="sidebar-header">
        <a href="{{ url('/admin/dashboard') }}" class="sidebar-brand">
          <div class="brand-icon-box"><i class="bi bi-shield-lock-fill"></i></div>
          <div class="brand-logo-text">Campus<span>Admin</span></div>
        </a>
        <button class="btn btn-sm btn-link d-lg-none text-white p-0" id="closeSidebarBtn" type="button">
          <i class="bi bi-x-lg fs-5"></i>
        </button>
      </div>

      <ul class="sidebar-menu">
        <li class="sidebar-label">Management</li>
        
        <li class="sidebar-item">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i><span>System Overview</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ route('admin.users') }}" class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill"></i><span>Student Accounts</span>
                </div>
                <span class="badge badge-light-green sidebar-badge" id="sidebarUserCount">{{ $adminSidebarStudentCount }}</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ route('admin.categories') }}" class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-tags-fill"></i><span>Default Categories</span>
                </div>
                <span class="badge badge-light-green sidebar-badge" id="sidebarCategoryCount">{{ $adminSidebarCategoryCount }}</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ route('admin.announcements') }}" class="sidebar-link {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
                <i class="bi bi-megaphone-fill"></i><span>Announcements</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ route('admin.tip-templates.index') }}" class="sidebar-link {{ request()->routeIs('admin.tip-templates.*') ? 'active' : '' }}">
                <i class="bi bi-lightbulb-fill"></i><span>Saving Tips Library</span>
            </a>
        </li>

        <li class="sidebar-label">Intelligence</li>
        <li class="sidebar-item">
            <a href="{{ route('admin.analytics') }}" class="sidebar-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i><span>Global Analytics</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="{{ route('admin.audit') }}" class="sidebar-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i><span>Audit Watchlist</span>
            </a>
        </li>
      </ul>

      <div class="sidebar-footer">
        <div class="user-profile-summary">
          <div class="admin-avatar">AD</div>
          <div class="overflow-hidden"><div class="admin-info-name text-truncate">Root Administrator</div><div class="admin-info-sub">Institutional Core</div></div>
          <form method="POST" action="{{ route('logout') }}" class="ms-auto">
            @csrf
            <button type="submit" class="btn-admin-logout" title="Sign Out"><i class="bi bi-box-arrow-right"></i></button>
          </form>
        </div>
      </div>
    </aside>

    <main class="app-main">
      <header class="app-topbar admin-topbar">
        <div class="d-flex align-items-center gap-3">
          <button class="icon-button d-lg-none" id="mobileNavToggle" title="Open Menu" type="button"><i class="bi bi-list fs-4"></i></button>
          <div class="topbar-greeting"><h2>{{ $pageHeading ?? ($pageTitle ?? 'Administrator Console') }}</h2><p>{{ $pageSubtitle ?? 'Global campus budgeting telemetry and student account governance.' }}</p></div>
        </div>
        <div class="topbar-actions d-flex align-items-center gap-2">
          <div class="system-health-pill d-none d-md-inline-flex"><span class="dot"></span><span>Node Active</span></div>
          
          <!-- Multi-Currency Switcher (PKR / USD) for Global Enterprise -->
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

          @if (!empty($showExport))
          <button class="btn btn-topbar-action d-none d-sm-inline-flex align-items-center gap-1" id="exportDbSnapshotBtn" title="Export Snapshot" type="button"><i class="bi bi-download"></i> Export</button>
          @endif
          
          <div class="dropdown">
            <button class="btn p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false"><div class="admin-avatar" style="width:38px; height:38px;">AD</div></button>
            <ul class="dropdown-menu dropdown-menu-end shadow border">
              <li><h6 class="dropdown-header text-main fw-bold">Admin Root</h6></li>
              <li><a class="dropdown-item" href="{{ url('/admin/users') }}"><i class="bi bi-people me-2"></i> Student Accounts</a></li>
              <li><a class="dropdown-item" href="{{ url('/admin/audit') }}"><i class="bi bi-shield-check me-2"></i> Audit Watchlist</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </header>

      <div class="app-content">
        @yield('content')
      </div>

      <footer class="app-footer border-top px-4 py-3 text-muted small">&copy; {{ date('Y') }} Campus Coin. Enterprise Multi-Currency Console.</footer>
    </main>
  </div>

  <script>
    window.csrfToken = @json(csrf_token());
    window.adminSidebarStudentCount = @json($adminSidebarStudentCount);
    window.adminSidebarCategoryCount = @json($adminSidebarCategoryCount);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="{{ asset('js/currency-engine.js') }}"></script>
  <script src="{{ asset('js/admin/app.js') }}"></script>
  <script src="{{ asset('js/admin/admin.js') }}"></script>
  @stack('scripts')
</body>
</html>
