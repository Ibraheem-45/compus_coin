@extends('admin.master')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Student Details</h1>
            <p class="text-muted mb-0">View student account information.</p>
        </div>

        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
            Back to Students
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">

        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="mb-4">Account Information</h5>

                    <div class="mb-3">
                        <small class="text-muted d-block">Name</small>
                        <strong>{{ $user->name }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <strong>{{ $user->email }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">University</small>
                        <strong>{{ $user->university ?: 'Not provided' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Academic Year</small>
                        <strong>{{ $user->academic_year ?: 'Not provided' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Monthly Allowance</small>
                        <strong>
                            {{ number_format((float) $user->monthly_allowance_baseline, 2) }}
                        </strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Monthly Saving Goal</small>
                        <strong>
                            {{ number_format((float) $user->monthly_saving_goal, 2) }}
                        </strong>
                    </div>

                    <div>
                        <small class="text-muted d-block">Transactions</small>
                        <strong>{{ number_format($transactionCount) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h5 class="mb-4">Reset Password</h5>

                    <form
                        method="POST"
                        action="{{ route('admin.users.reset-password', $user) }}"
                    >
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                New Password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                            >

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirm New Password
                            </label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Reset Password
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>

</div>
@endsection