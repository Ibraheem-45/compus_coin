@php
    $profileUser = $user ?? Auth::user();
    $profileName = $profileUser?->name ?: 'Student';
    $profileEmail = $profileUser?->email ?: 'student@campuscoin.local';
    $profileAcademicYear = $profileUser?->academic_year ?: '';
    $profileUniversity = $profileUser?->university ?: '';
    $profileAllowance = $profileUser?->monthly_allowance_baseline ?? 0;
    $profileGoal = $profileUser?->monthly_saving_goal ?? 0;
    $profileInitials = collect(explode(' ', trim($profileName)))
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('') ?: 'ST';
@endphp

@extends('student.master')

@section('title', 'Student Profile — Campus Coin')
@section('page_heading', 'Student Profile')
@section('page_description', 'Manage your university details, baseline allowance, and savings goals.')

@section('content')
<div class="profile-shell row g-4">
          <!-- Profile Left Card (Avatar & Status) -->
          <div class="col-lg-4">
            <div class="profile-summary-card card p-4 text-center shadow-sm h-100">
              <div class="profile-top-actions">
                <button type="button" class="btn btn-sm btn-outline-primary" id="toggleEditProfileBtn">
                  <i class="bi bi-pencil me-1"></i> Edit Profile
                </button>
              </div>

              <div class="user-avatar-circle mx-auto mb-3" id="profileAvatarBadge" style="width: 80px; height: 80px; font-size: 2rem;">
                {{ $profileInitials }}
              </div>
              <h5 class="fw-bold mb-1" id="profileDisplayName">{{ $profileName }}</h5>
              <p class="text-muted small mb-2" id="profileDisplayEmail">{{ $profileEmail }}</p>
              <div class="mb-3">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                  <i class="bi bi-patch-check-fill me-1"></i> Active Student
                </span>
              </div>
              <div class="profile-meta-box p-3 rounded border text-start small mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-muted">Academic Status:</span>
                    <strong id="profileDisplayYear">{{ $profileAcademicYear ?: 'Not provided' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-muted">University:</span>
                    <strong>{{ $profileUniversity ?: 'Not provided' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted">Campus Member Since:</span>
                  <strong>{{ optional($profileUser)->created_at?->format('M Y') ?: 'Not provided' }}</strong>
                </div>
              </div>

              <div class="mt-auto">
                <small class="text-muted">Campus Coin ID: <strong>#{{ $profileUser?->id }}</strong></small>
              </div>
            </div>
          </div>

          <!-- Profile Right Form (Editable Fields) -->
          <div class="col-lg-8">
            <div class="profile-form-card card p-4 shadow-sm">
              <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <h5 class="fw-bold mb-0">Financial & Academic Information</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Student Record</span>
              </div>

              <form id="studentProfileForm" method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                @if (session('status') === 'profile-updated')
                  <div class="alert alert-success" role="status">Profile updated successfully.</div>
                @endif
                @if ($errors->any())
                  <div class="alert alert-danger" role="alert">Please check the highlighted profile information.</div>
                @endif
                <div class="row g-3 mb-3">
                  <div class="col-md-6">
                    <label for="profName" class="form-label small fw-semibold">Full Name</label>
                    <input type="text" class="form-control" id="profName" name="name" value="{{ $profileName }}" disabled required>
                  </div>
                  <div class="col-md-6">
                    <label for="profEmail" class="form-label small fw-semibold">University Email</label>
                    <input type="email" class="form-control" id="profEmail" name="email" value="{{ old('email', $profileEmail) }}" readonly required>
                  </div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-md-6">
                    <label for="profUniversity" class="form-label small fw-semibold">University / Institution</label>
                    <input type="text" class="form-control" id="profUniversity" name="university" disabled value="{{ old('university', $profileUniversity) }}">
                  </div>
                  <div class="col-md-6">
                    <label for="profYear" class="form-label small fw-semibold">Academic Year</label>
                    <select class="form-select" id="profYear" name="academic_year" disabled>
                      <option value="">Select academic year</option>
                      <option value="1st Year (Freshman)" {{ $profileAcademicYear === '1st Year (Freshman)' ? 'selected' : '' }}>1st Year (Freshman)</option>
                      <option value="2nd Year (Sophomore)" {{ $profileAcademicYear === '2nd Year (Sophomore)' ? 'selected' : '' }}>2nd Year (Sophomore)</option>
                      <option value="3rd Year (Junior)" {{ $profileAcademicYear === '3rd Year (Junior)' ? 'selected' : '' }}>3rd Year (Junior)</option>
                      <option value="4th Year (Senior)" {{ $profileAcademicYear === '4th Year (Senior)' ? 'selected' : '' }}>4th Year (Senior)</option>
                      <option value="Postgraduate (MS/PhD)" {{ $profileAcademicYear === 'Postgraduate (MS/PhD)' ? 'selected' : '' }}>Postgraduate (MS/PhD)</option>
                    </select>
                  </div>
                </div>

                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label for="profAllowance" class="form-label small fw-semibold">Monthly Allowance Baseline (PKR)</label>
                    <div class="input-group">
                      <span class="input-group-text fw-bold">PKR</span>
                      <input type="number" class="form-control" id="profAllowance" name="monthly_allowance_baseline" value="{{ old('monthly_allowance_baseline', $profileAllowance) }}" disabled min="0" step="0.01">
                    </div>
                    <small class="text-muted">Used by Spending Analytics to track monthly budget variations.</small>
                  </div>
                  <div class="col-md-6">
                    <label for="profGoal" class="form-label small fw-semibold">Monthly Savings Goal (PKR)</label>
                    <div class="input-group">
                      <span class="input-group-text fw-bold">PKR</span>
                      <input type="number" class="form-control" id="profGoal" name="monthly_saving_goal" value="{{ old('monthly_saving_goal', $profileGoal) }}" disabled min="0" step="0.01">
                    </div>
                    <small class="text-muted">Shown in your Dashboard progress widget.</small>
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                  <button type="submit" class="btn btn-primary px-4 d-none" id="saveProfileBtn">
                    <i class="bi bi-check2-circle me-1"></i> Save Changes
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/student/profile.js') }}"></script>
@endpush
