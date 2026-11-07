@extends('student.master')

@section('title', 'Campus Category Envelopes — Campus Coin')
@section('page_heading', 'Budget Envelopes')
@section('page_description', 'Enforce disciplined spending limits per category to secure positive semester runway.')

@section('breadcrumbs')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none" style="color: var(--gold);">Dashboard</a></li>
    <li class="breadcrumb-item active text-muted">Budgets</li>
  </ol>
</nav>
@endsection

@section('content')
@if($monthlyAllowance > 0)
    <div class="alert d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4" role="status" style="background: {{ $monthlyExpenses > $monthlyAllowance ? 'rgba(239, 68, 68, 0.15)' : 'rgba(201, 162, 39, 0.1)' }}; border: 1px solid {{ $monthlyExpenses > $monthlyAllowance ? 'rgba(239, 68, 68, 0.4)' : 'rgba(201, 162, 39, 0.3)' }}; color: #F5F5DC;">
        <div>
            <strong style="color: {{ $monthlyExpenses > $monthlyAllowance ? '#EF4444' : 'var(--gold-light)' }};">
                {{ $monthlyExpenses > $monthlyAllowance ? 'Monthly Limit Exceeded' : 'Active Monthly Allowance' }}:
            </strong>
            PKR {{ number_format($monthlyExpenses, 0) }} burned of PKR {{ number_format($monthlyAllowance, 0) }} total allowance ({{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}).
        </div>
        @if($monthlyExpenses > $monthlyAllowance)
            <span class="badge" style="background: rgba(239, 68, 68, 0.25); color: #EF4444; border: 1px solid #EF4444;">
                Deficit: PKR {{ number_format($monthlyExpenses - $monthlyAllowance, 0) }}
            </span>
        @else
            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.3);">
                Surplus: PKR {{ number_format($monthlyAllowance - $monthlyExpenses, 0) }} Safe
            </span>
        @endif
    </div>
@endif

<div class="row g-4">
    <!-- Set Budget Form -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 p-4" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="p-2 rounded" style="background: rgba(201, 162, 39, 0.15); color: var(--gold);">
                    <i class="bi bi-shield-plus"></i>
                </span>
                <h5 class="fw-bold mb-0" style="color: #F5F5DC; font-family: var(--serif);">Create Spending Envelope</h5>
            </div>
            <p class="text-muted small mb-3">Cap specific categories like Canteen, Van Fare, or Entertainment.</p>

            @if($errors->any())
                <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #EF4444;">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('budgets.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="category_id" class="form-label small fw-bold" style="color: var(--gold);">Category Envelope *</label>
                    <select name="category_id" id="category_id" class="form-select" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }} ({{ ucfirst($category->type) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="month" class="form-label small fw-bold" style="color: var(--gold);">Target Month *</label>
                    <input type="month" name="month" id="month" class="form-control" value="{{ old('month', now()->format('Y-m')) }}" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                </div>

                <div class="mb-3">
                    <label for="limit_amount" class="form-label small fw-bold" style="color: var(--gold);">Maximum Ceiling (PKR) *</label>
                    <input type="number" name="limit_amount" id="limit_amount" class="form-control" min="1" step="1" placeholder="e.g. 5000" value="{{ old('limit_amount') }}" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                </div>

                <button type="submit" class="btn fw-bold w-100" style="background: var(--gold); color: #05140A;">
                    <i class="bi bi-check2-circle me-1"></i> Lock Budget Envelope
                </button>
            </form>
        </div>
    </div>

    <!-- Active Budget Envelopes -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 p-4" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: #F5F5DC; font-family: var(--serif);">Active Envelopes</h5>
                <span class="badge" style="background: rgba(201, 162, 39, 0.15); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.3);">
                    {{ count($budgets) }} Allocations
                </span>
            </div>

            @forelse($budgets as $budget)
                <div class="rounded p-3 mb-3" style="background: rgba(5, 20, 10, 0.6); border: 1px solid rgba(201, 162, 39, 0.25);">
                    @php
                        $spent = (float) \App\Models\Transaction::query()
                            ->where('user_id', auth()->id())
                            ->where('category_id', $budget->category_id)
                            ->where('type', 'expense')
                            ->whereBetween('date', [
                                \Carbon\Carbon::createFromFormat('Y-m', $budget->month)->startOfMonth()->toDateString(),
                                \Carbon\Carbon::createFromFormat('Y-m', $budget->month)->endOfMonth()->toDateString(),
                            ])
                            ->sum('amount');
                        $usage = $budget->limit_amount > 0 ? min(100, round($spent / $budget->limit_amount * 100)) : 0;
                        $overBudget = $spent > (float) $budget->limit_amount;
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="mb-0 fw-bold" style="color: #F5F5DC;">{{ $budget->category?->name ?? 'Uncategorized' }}</h6>
                            <small class="text-muted">{{ \Carbon\Carbon::createFromFormat('Y-m', $budget->month)->format('F Y') }}</small>
                        </div>
                        <div class="fw-bold" style="font-family: var(--mono); color: {{ $overBudget ? '#EF4444' : 'var(--gold)' }};">
                            <span data-pkr="{{ $spent }}">PKR {{ number_format($spent, 0) }}</span> / <span data-pkr="{{ $budget->limit_amount }}">PKR {{ number_format($budget->limit_amount, 0) }}</span>
                        </div>
                    </div>
                    @if($overBudget)
                        <div class="alert py-2 mb-2 small" role="alert" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #EF4444;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Deficit Alert: Exceeded envelope ceiling by PKR {{ number_format($spent - $budget->limit_amount, 0) }}.
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                            <span>{{ $usage }}% burned</span>
                            <span>PKR {{ number_format(max(0, $budget->limit_amount - $spent), 0) }} headroom</span>
                        </div>
                    @endif
                    <div class="progress mb-3" style="height: 6px; background: rgba(255,255,255,0.06);">
                        <div class="progress-bar" style="width: {{ $usage }}%; background: {{ $overBudget ? '#EF4444' : 'linear-gradient(90deg, #10B981, #C9A227)' }};"></div>
                    </div>

                    <div class="text-end">
                        <form method="POST" action="{{ route('budgets.destroy', $budget) }}" class="d-inline-block" onsubmit="return confirm('Remove this budget envelope?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">
                                <i class="bi bi-trash me-1"></i> Release Envelope
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-pie-chart fs-1" style="color: var(--gold); opacity: 0.5;"></i>
                    <h6 class="mt-3" style="color: #F5F5DC;">No Budgets Assigned</h6>
                    <p class="text-muted small mb-0">Define spending ceilings using the form on the left to activate envelope alerts.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
  <script src="{{ asset('js/student/budgets.js') }}"></script>
@endpush
