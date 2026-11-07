@extends('student.master')

@section('title', 'Settings — Campus Coin')
@section('page_heading', 'Preferences & Settings')
@section('page_description', 'Customize your appearance, accessibility, notification alerts, and account security.')

@section('content')
<div class="row g-4">
          <!-- Left Column: Preferences -->
          <div class="col-lg-6">
            <!-- Appearance -->
            <div class="card p-4 shadow-sm mb-4">
              <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-palette text-primary"></i> Appearance
              </h5>
              <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background: rgba(5, 20, 10, 0.6); border: 1px solid rgba(201, 162, 39, 0.25);">
                <div>
                  <strong class="d-block" style="color: var(--gold-light);">Active Terminal Theme</strong>
                  <small class="text-muted">Dark Obsidian Jungle & Imperial Gold Suite (Active).</small>
                </div>
                <span class="badge" style="background: rgba(201, 162, 39, 0.2); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.4);">
                  <i class="bi bi-shield-check me-1"></i> Locked
                </span>
              </div>

              <div class="d-flex justify-content-between align-items-center p-3 rounded mt-3" style="background: rgba(5, 20, 10, 0.6); border: 1px solid rgba(201, 162, 39, 0.25);">
                <div>
                  <strong class="d-block" style="color: var(--gold-light);">International Currency Mode</strong>
                  <small class="text-muted">Select active currency for student ledger.</small>
                </div>
                <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-outline-warning currency-option" data-currency="PKR">🇵🇰 PKR</button>
                  <button type="button" class="btn btn-outline-warning currency-option" data-currency="USD">🇺🇸 USD</button>
                </div>
              </div>
            </div>

            <!-- Accessibility Font Scaling -->
            <div class="card p-4 shadow-sm mb-4">
              <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-fonts text-info"></i> Accessibility & Font Scaling
              </h5>
              <p class="text-muted small">Select the typography scale that is most comfortable for your reading:</p>
              
              <div class="btn-group w-100 mb-2" role="group">
                <button type="button" class="btn btn-outline-primary font-size-btn" data-size="sm">
                  Small (A-)
                </button>
                <button type="button" class="btn btn-outline-primary font-size-btn active" data-size="md">
                  Medium (Default)
                </button>
                <button type="button" class="btn btn-outline-primary font-size-btn" data-size="lg">
                  Large (A+)
                </button>
              </div>
              <small class="text-muted">Live preview applies across all Campus Coin pages immediately.</small>
            </div>

            <!-- Notifications Preferences -->
            <div class="card p-4 shadow-sm mb-4">
              <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-bell text-warning"></i> Notification Preferences
              </h5>
              
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                  <strong class="d-block small">Budget Alerts & Overspending Warnings</strong>
                  <small class="text-muted">Notify me when an expense reaches 75% or 100% of category budget.</small>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="notifBudgetSwitch" checked>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                  <strong class="d-block small">Weekly Student Saving Tips</strong>
                  <small class="text-muted">Receive money-saving student life recommendations.</small>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="notifTipsSwitch" checked>
                </div>
              </div>

            </div>
          </div>

          <!-- Right Column: Security -->
          <div class="col-lg-6">
            <!-- Security / Change Password -->
            <div class="card p-4 shadow-sm mb-4">
              <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock text-success"></i> Security & Password
              </h5>

              @if (session('status') === 'password-updated')
                <div class="alert alert-success" role="status">Your password has been updated.</div>
              @endif

              @if ($errors->updatePassword->any())
                <div class="alert alert-danger" role="alert">{{ $errors->updatePassword->first() }}</div>
              @endif

              <form id="changePasswordForm" method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                  <label for="currentPass" class="form-label small fw-semibold">Current Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="currentPass" name="current_password" placeholder="Enter your current password" autocomplete="current-password" required>
                    <button type="button" class="btn btn-outline-secondary password-visibility-toggle" data-target="currentPass" aria-label="Show current password" title="Show password">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="newPass" class="form-label small fw-semibold">New Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="newPass" name="password" placeholder="Enter a new password" autocomplete="new-password" required>
                    <button type="button" class="btn btn-outline-secondary password-visibility-toggle" data-target="newPass" aria-label="Show new password" title="Show password">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="confirmNewPass" class="form-label small fw-semibold">Confirm New Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="confirmNewPass" name="password_confirmation" placeholder="Re-enter your new password" autocomplete="new-password" required>
                    <button type="button" class="btn btn-outline-secondary password-visibility-toggle" data-target="confirmNewPass" aria-label="Show password confirmation" title="Show password">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>

                <button type="submit" class="btn btn-outline-primary btn-sm px-3">
                  Update Account Password
                </button>
              </form>
            </div>
          </div>
        </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
      CampusCoin.init('settings');

      const themeSwitch = document.getElementById('settingThemeSwitch');
      const fontBtns = document.querySelectorAll('.font-size-btn');

      const settings = CampusDB.getSettings();



      // Font size switcher
      fontBtns.forEach(btn => {
        const size = btn.getAttribute('data-size');
        if (size === (settings.fontSize || 'md')) {
          fontBtns.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
        }

        btn.onclick = function() {
          fontBtns.forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          settings.fontSize = size;
          CampusDB.saveSettings(settings);
          CampusCoin.initFontSize();
          CampusCoin.showToast(`Font scaling set to ${size.toUpperCase()}`, 'info');
        };
      });

      document.querySelectorAll('.password-visibility-toggle').forEach(button => {
        button.onclick = function() {
          const input = document.getElementById(this.dataset.target);
          if (!input) return;

          const showPassword = input.type === 'password';
          input.type = showPassword ? 'text' : 'password';
          this.innerHTML = `<i class="bi bi-eye${showPassword ? '-slash' : ''}"></i>`;
          this.setAttribute('aria-label', `${showPassword ? 'Hide' : 'Show'} password`);
          this.title = `${showPassword ? 'Hide' : 'Show'} password`;
        };
      });

    });
</script>
@endpush
