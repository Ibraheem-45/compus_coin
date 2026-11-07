@extends('admin.master')

@section('title', 'Institutional Terminal — Campus Coin Admin')

@section('content')
<div class="container-fluid py-4">

    <!-- Top Admin Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background: rgba(201, 162, 39, 0.2); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.4);">
                    <i class="bi bi-shield-lock-fill me-1"></i> INSTITUTIONAL GOVERNANCE
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="bi bi-circle-fill me-1 small"></i> Live Node
                </span>
            </div>
            <h1 class="h3 mb-1 mt-2" style="font-family: var(--serif); color: var(--gold-light);">University Administrative Terminal</h1>
            <p class="text-muted mb-0 small">CampusCoin Multi-Tenant FinTech Core & Student Ledger Oversight</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="px-3 py-2 rounded text-end" style="background: rgba(13, 40, 24, 0.6); border: 1px solid rgba(201, 162, 39, 0.2);">
                <small class="text-muted d-block" style="font-size: 0.75rem;">SYSTEM TIME</small>
                <span class="fw-bold" style="color: #F5F5DC; font-family: var(--mono);">{{ now()->format('d M Y · H:i:s') }}</span>
            </div>
            <a href="{{ route('admin.announcements') }}" class="btn btn-sm fw-bold px-3 py-2" style="background: var(--gold); color: #05140A;">
                <i class="bi bi-megaphone-fill me-1"></i> Broadcast Alert
            </a>
        </div>
    </div>

    <!-- Commercial SaaS License Ribbon -->
    <div class="card p-3 mb-4 shadow-sm" style="background: linear-gradient(135deg, rgba(13, 40, 24, 0.75), rgba(5, 20, 10, 0.95)); border: 1px solid rgba(201, 162, 39, 0.35);">
        <div class="row g-3 align-items-center">
            <div class="col-lg-3 col-md-6 border-end border-dark-subtle">
                <small class="text-muted text-uppercase tracking-wider d-block" style="font-size: 0.7rem;">University License</small>
                <div class="fw-bold mt-1" style="color: var(--gold-light); font-size: 1.05rem;">CampusCoin Enterprise v2.5</div>
                <small class="text-muted">Multi-Campus Unlimited Node</small>
            </div>
            <div class="col-lg-3 col-md-6 border-end border-dark-subtle">
                <small class="text-muted text-uppercase tracking-wider d-block" style="font-size: 0.7rem;">Student Capacity Quota</small>
                <div class="fw-bold mt-1" style="color: #F5F5DC; font-family: var(--mono);">{{ number_format($totalStudents) }} / 10,000 Seats</div>
                <div class="progress mt-1" style="height: 4px; background: rgba(255,255,255,0.06);">
                    @php $seatPercent = min(100, max(2, ($totalStudents / 10000) * 100)); @endphp
                    <div class="progress-bar" style="width: {{ $seatPercent }}%; background: var(--gold);"></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 border-end border-dark-subtle">
                <small class="text-muted text-uppercase tracking-wider d-block" style="font-size: 0.7rem;">Campus Ledger Security</small>
                <div class="fw-bold mt-1 text-success"><i class="bi bi-patch-check-fill me-1"></i> SHA-256 Audit Log Active</div>
                <small class="text-muted">Zero unauthorized breaches</small>
            </div>
            <div class="col-lg-3 col-md-6 text-lg-end">
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-light" style="border-color: rgba(201, 162, 39, 0.35); color: #F5F5DC;">
                    <i class="bi bi-person-lines-fill me-1" style="color: var(--gold);"></i> Directory & Roles &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Summary KPI Cards -->
    <div class="row g-4 mb-4 align-items-start">

        <!-- Total Students -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small text-uppercase fw-semibold tracking-wider">Enrolled Students</p>
                            <h2 class="mb-0 fw-bold" style="color: #F5F5DC; font-family: var(--serif);">{{ number_format($totalStudents) }}</h2>
                        </div>
                        <div class="rounded p-2 flex-shrink-0" style="background: rgba(201, 162, 39, 0.15); color: var(--gold);">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top border-dark-subtle d-flex justify-content-between align-items-center small text-muted">
                        <span>Active verified scholars</span>
                        <span class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> 100% Verified</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small text-uppercase fw-semibold tracking-wider">Total Transactions</p>
                            <h2 class="mb-0 fw-bold" style="color: var(--gold-light); font-family: var(--serif);">{{ number_format($totalTransactions) }}</h2>
                        </div>
                        <div class="rounded p-2 flex-shrink-0" style="background: rgba(16, 185, 129, 0.15); color: #10B981;">
                            <i class="bi bi-arrow-left-right fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top border-dark-subtle d-flex justify-content-between align-items-center small text-muted">
                        <span>All student cashflows</span>
                        <span style="color: var(--gold);"><i class="bi bi-lightning-fill"></i> Realtime</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default Categories -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small text-uppercase fw-semibold tracking-wider">Campus Envelopes</p>
                            <h2 class="mb-0 fw-bold" style="color: #F5F5DC; font-family: var(--serif);">{{ number_format($defaultCategories) }}</h2>
                        </div>
                        <div class="rounded p-2 flex-shrink-0" style="background: rgba(201, 162, 39, 0.15); color: var(--gold);">
                            <i class="bi bi-tags fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top border-dark-subtle d-flex justify-content-between align-items-center small text-muted">
                        <span>System standard taxonomies</span>
                        <a href="{{ route('admin.categories') }}" class="small text-decoration-none" style="color: var(--gold);">Manage &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Burn Category -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small text-uppercase fw-semibold tracking-wider">Highest Volume Sector</p>
                            <h4 class="mb-0 fw-bold" style="color: var(--gold); font-family: var(--serif);">
                                {{ $topCategory?->name ?? 'No Data' }}
                            </h4>
                        </div>
                        <div class="rounded p-2 flex-shrink-0" style="background: rgba(56, 189, 248, 0.15); color: #38BDF8;">
                            <i class="bi bi-bar-chart fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top border-dark-subtle d-flex justify-content-between align-items-center small text-muted">
                        <span>
                            @if($topCategory)
                                {{ number_format($topCategory->transaction_count) }} campus logs
                            @else
                                Awaiting entries
                            @endif
                        </span>
                        <span class="badge" style="background: rgba(201,162,39,0.15); color: var(--gold);">Volume #1</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">

        <!-- Left Column: Recent Transactions Table -->
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm h-100" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" style="color: #F5F5DC; font-family: var(--serif); font-size: 1.25rem;">Live Campus Audit Stream</h5>
                        <p class="text-muted small mb-0">Decentralized student financial interactions recorded across nodes</p>
                    </div>
                    <span class="badge" style="background: rgba(201, 162, 39, 0.15); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.3);">
                        Latest {{ $recentTransactions->count() }} Entries
                    </span>
                </div>

                <div class="card-body px-4">
                    @if($recentTransactions->count())
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" style="color: #F5F5DC;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(201, 162, 39, 0.2); font-size: 0.8rem; color: var(--gold);">
                                        <th class="pb-2">STUDENT</th>
                                        <th class="pb-2">MEMO / DESCRIPTION</th>
                                        <th class="pb-2">ENVELOPE</th>
                                        <th class="pb-2">FLOW</th>
                                        <th class="pb-2 text-end">AMOUNT</th>
                                        <th class="pb-2 text-end">DATE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                            <td class="py-3">
                                                <div class="fw-semibold" style="color: #F5F5DC;">
                                                    {{ $transaction->user?->name ?? 'Anonymous Scholar' }}
                                                </div>
                                            </td>

                                            <td class="py-3 text-muted" title="{{ $transaction->description }}">
                                                {{ Str::limit($transaction->description, 28) }}
                                            </td>

                                            <td class="py-3">
                                                <span class="badge" style="background: rgba(255,255,255,0.06); color: #F5F5DC; border: 1px solid rgba(255,255,255,0.1);">
                                                    {{ $transaction->category?->name ?? 'Uncategorized' }}
                                                </span>
                                            </td>

                                            <td class="py-3">
                                                @if($transaction->type === 'income')
                                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                                        + Inflow
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.3);">
                                                        - Outflow
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="py-3 text-end fw-bold currency-val" data-pkr="{{ (float) $transaction->amount }}" style="font-family: var(--mono); color: {{ $transaction->type === 'income' ? '#10B981' : '#EF4444' }};">
                                                PKR {{ number_format((float) $transaction->amount, 0) }}
                                            </td>

                                            <td class="py-3 text-end small text-muted">
                                                {{ \Carbon\Carbon::parse($transaction->date)->format('d M') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt fs-1" style="color: var(--gold); opacity: 0.5;"></i>
                            <h6 class="mt-3" style="color: #F5F5DC;">No Transactions Yet</h6>
                            <p class="text-muted mb-0 small">
                                When students log canteen, transport or allowance entries, they will stream here live.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Category Distribution & System Actions -->
        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm h-100" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" style="color: #F5F5DC; font-family: var(--serif); font-size: 1.25rem;">Taxonomy Burn Distribution</h5>
                        <p class="text-muted small mb-0">System-wide frequency across categories</p>
                    </div>
                    <a href="{{ route('admin.categories') }}" class="small text-decoration-none" style="color: var(--gold);">All Taxonomies &rarr;</a>
                </div>

                <div class="card-body px-4">
                    @if($categoryUsage->count())
                        <div class="d-flex flex-column gap-3">
                            @foreach($categoryUsage as $category)
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <span class="fw-semibold" style="color: #F5F5DC;">
                                                {{ $category->name }}
                                            </span>

                                            <span class="badge ms-2" style="background: {{ $category->type === 'income' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $category->type === 'income' ? '#10B981' : '#EF4444' }};">
                                                {{ ucfirst($category->type) }}
                                            </span>
                                        </div>

                                        <span class="small fw-bold" style="color: var(--gold-light); font-family: var(--mono);">
                                            {{ number_format($category->transaction_count) }} logs
                                        </span>
                                    </div>

                                    @php
                                        $maxUsage = $categoryUsage->max('transaction_count');
                                        $percentage = $maxUsage > 0
                                            ? ($category->transaction_count / $maxUsage) * 100
                                            : 0;
                                    @endphp

                                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.06);">
                                        <div
                                            class="progress-bar"
                                            role="progressbar"
                                            style="width: {{ $percentage }}%; background: linear-gradient(90deg, #C9A227, #8FBC8F);"
                                        ></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bar-chart fs-1" style="color: var(--gold); opacity: 0.5;"></i>
                            <h6 class="mt-3" style="color: #F5F5DC;">No Category Telemetry</h6>
                            <p class="text-muted mb-0 small">
                                Usage ratios will calculate once transactions are entered.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Administrative Quick Access Deck -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: rgba(13, 40, 24, 0.45); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h5 class="mb-1" style="color: #F5F5DC; font-family: var(--serif); font-size: 1.25rem;">Commercial Deployment & Multi-Campus Control Deck</h5>
                            <p class="text-muted mb-0 small">
                                Rapidly manage institutional taxonomy templates, student enrollments, and university financial advisories.
                            </p>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.categories') }}" class="btn btn-sm fw-bold px-3 py-2" style="background: rgba(201, 162, 39, 0.15); border: 1px solid rgba(201, 162, 39, 0.4); color: var(--gold);">
                                <i class="bi bi-tags me-1"></i> Manage Envelopes
                            </a>

                            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-light px-3 py-2" style="border-color: rgba(201, 162, 39, 0.3); color: #F5F5DC;">
                                <i class="bi bi-people me-1" style="color: var(--gold);"></i> Student Directory
                            </a>

                            <a href="{{ route('admin.announcements') }}" class="btn btn-sm btn-outline-light px-3 py-2" style="border-color: rgba(201, 162, 39, 0.3); color: #F5F5DC;">
                                <i class="bi bi-broadcast me-1" style="color: var(--gold);"></i> Campus Announcements
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
