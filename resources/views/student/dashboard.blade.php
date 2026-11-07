@php
    $hour = now()->hour;
    $timeGreeting = $hour < 12 ? 'Good morning' : ($hour >= 18 ? 'Good evening' : 'Good afternoon');
    $firstName = trim(explode(' ', $user->name ?? 'Student')[0] ?? 'Student');
    $totalIncome = $stats['total_income'] ?? 0;
    $totalExpenses = $stats['total_expenses'] ?? 0;
    $currentBalance = $stats['current_balance'] ?? 0;
    $goalAmount = $stats['goal'] ?? 20000;
    $goalPercent = $stats['goal_percent'] ?? 0;
    $topCategory = $stats['top_category'] ?? 'Food';
    $topAmount = $stats['top_amount'] ?? 0;
    $expenseRatio = $stats['expense_ratio'] ?? 0;
    $balanceLabel = $stats['balance_label'] ?? 'Healthy Balance';
    $balanceMeta = $stats['balance_meta'] ?? 'Available for savings';
    $goalText = $stats['goal_text'] ?? '0% achieved';
    $bankedLabel = $stats['banked_label'] ?? 'PKR 0 banked';
    $topSharePercent = $stats['top_share_percent'] ?? 0;

    // Commercial Student SaaS Intelligence
    $daysInMonth = \Carbon\Carbon::now()->daysInMonth;
    $currentDay = \Carbon\Carbon::now()->day;
    $daysRemaining = max(1, $daysInMonth - $currentDay + 1);
    $safeDaily = max(0, round($currentBalance / $daysRemaining));
    $burnRate = $currentDay > 0 ? round($totalExpenses / $currentDay) : 0;
    $healthScore = $totalIncome > 0 ? max(10, min(100, round((($totalIncome - $totalExpenses) / $totalIncome) * 100))) : ($currentBalance > 0 ? 82 : 35);
    $healthBadge = $healthScore >= 75 ? 'Optimal' : ($healthScore >= 50 ? 'Moderate' : 'Caution');

    $expenseCategories = \App\Models\Category::where('type', 'expense')
        ->where(function($q) {
            $q->whereNull('user_id')->orWhere('user_id', auth()->id());
        })
        ->orderBy('name')
        ->get();
@endphp

@extends('student.master')

@section('title', 'Student Terminal — Campus Coin')
@section('page_heading', $timeGreeting . ', ' . $firstName . '')
@section('page_description', "Campus FinTech Terminal & Real-Time Student Cashflow Engine")

@section('breadcrumbs')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none" style="color: var(--gold);">Campus Coin</a></li>
    <li class="breadcrumb-item active text-muted">Financial Terminal</li>
  </ol>
</nav>
@endsection

@section('content')
    @foreach ($announcements as $announcement)
      <div class="alert alert-dismissible fade show d-flex align-items-start gap-3 mb-4" role="status" style="background: rgba(201, 162, 39, 0.08); border: 1px solid rgba(201, 162, 39, 0.3); color: #F5F5DC;">
        <i class="bi bi-megaphone-fill fs-5" style="color: var(--gold);"></i>
        <div class="flex-grow-1">
          <strong class="d-block" style="color: var(--gold-light);">{{ $announcement->title }}</strong>
          <span class="small">{{ $announcement->message }}</span>
          <small class="d-block mt-1 text-muted">{{ $announcement->office }} · {{ $announcement->created_at->format('M j, Y') }}</small>
        </div>
        <a href="{{ route('student.notifications') }}" class="btn btn-sm btn-outline-warning text-nowrap" style="border-color: rgba(201, 162, 39, 0.4); color: var(--gold-light);">Details</a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endforeach

    <!-- Commercial Intelligence Ribbon: Daily Safe-To-Spend & Streak -->
    <div class="card p-3 mb-4 safe-spend-card shadow-sm">
      <div class="row align-items-center g-3">
        <div class="col-md-5 d-flex align-items-center gap-3">
          <div class="p-3 rounded-circle" style="background: rgba(201, 162, 39, 0.15); border: 1px solid rgba(201, 162, 39, 0.4); color: var(--gold);">
            <i class="bi bi-shield-check fs-3"></i>
          </div>
          <div>
            <span class="badge mb-1" style="background: rgba(201, 162, 39, 0.2); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.4);">
              <i class="bi bi-cpu-fill me-1"></i> AI Safe-To-Spend
            </span>
            <div class="fs-3 fw-bold" style="font-family: var(--serif); color: var(--gold-light);">
              <span id="safeSpendDisplay" data-raw-pkr="{{ $safeDaily }}">PKR {{ number_format($safeDaily, 0) }}</span> <span class="fs-6 fw-normal text-muted" style="font-family: var(--sans);">/ day</span>
            </div>
            <div class="small text-muted">
              Paced for {{ $daysRemaining }} days remaining in {{ now()->format('F') }}
            </div>
          </div>
        </div>

        <div class="col-md-4 border-start border-dark-subtle px-md-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small text-muted">Discipline Health Score</span>
            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.3);">
              {{ $healthScore }}/100 • {{ $healthBadge }}
            </span>
          </div>
          <div class="progress" style="height: 6px; background: rgba(255,255,255,0.06);">
            <div class="progress-bar" role="progressbar" style="width: {{ $healthScore }}%; background: linear-gradient(90deg, #10B981, #C9A227);"></div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2 small text-muted">
            <span>🔥 5-Day Logging Streak</span>
            <span>Avg Burn: <span data-pkr="{{ $burnRate }}">PKR {{ number_format($burnRate, 0) }}</span>/d</span>
          </div>
        </div>

        <div class="col-md-3 d-flex flex-column gap-2 justify-content-center">
          <button type="button" class="btn btn-sm fw-bold d-flex align-items-center justify-content-center gap-2" style="background: var(--gold); color: #05140A;" data-bs-toggle="modal" data-bs-target="#fastLogModal">
            <i class="bi bi-lightning-charge-fill"></i> Fast Log Expense
          </button>
          <button type="button" class="btn btn-sm btn-outline-light d-flex align-items-center justify-content-center gap-2" style="border-color: rgba(201, 162, 39, 0.3); color: #F5F5DC;" data-bs-toggle="modal" data-bs-target="#splitBillModal">
            <i class="bi bi-people-fill" style="color: var(--gold);"></i> Split Roommate Bill
          </button>
        </div>
      </div>
    </div>

    <!-- 4 Summary KPI Cards -->
    <div class="row g-3 mb-4">
      <!-- 1. Total Income -->
      <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
          <div class="kpi-card-header">
            <div>
              <span class="kpi-title">Total Inflow</span>
              <div class="kpi-amount" id="kpiTotalIncome" data-pkr="{{ $totalIncome }}">PKR {{ number_format($totalIncome, 0) }}</div>
            </div>
            <div class="kpi-icon-box kpi-icon-green">
              <i class="bi bi-cash-stack"></i>
            </div>
          </div>
          <div class="kpi-comparison">
            <span class="kpi-pill pill-up"><i class="bi bi-arrow-up-short"></i> Live Inflow</span>
            <span class="kpi-text-muted">Recorded allowance/stipend</span>
          </div>
        </div>
      </div>

      <!-- 2. Total Expenses -->
      <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
          <div class="kpi-card-header">
            <div>
              <span class="kpi-title">Total Outflow</span>
              <div class="kpi-amount text-danger" id="kpiTotalExpenses" data-pkr="{{ $totalExpenses }}">PKR {{ number_format($totalExpenses, 0) }}</div>
            </div>
            <div class="kpi-icon-box kpi-icon-red">
              <i class="bi bi-credit-card"></i>
            </div>
          </div>
          <div class="kpi-comparison">
            <span class="kpi-pill pill-down"><i class="bi bi-arrow-down-short"></i> {{ $expenseRatio }}%</span>
            <span class="kpi-text-muted">of total monthly allowance</span>
          </div>
        </div>
      </div>

      <!-- 3. Current Balance -->
      <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
          <div class="kpi-card-header">
            <div>
              <span class="kpi-title">Net Liquid Pool</span>
              <div class="kpi-amount" style="color: var(--gold);" id="kpiCurrentBalance" data-pkr="{{ $currentBalance }}">PKR {{ number_format($currentBalance, 0) }}</div>
            </div>
            <div class="kpi-icon-box kpi-icon-teal">
              <i class="bi bi-wallet2"></i>
            </div>
          </div>
          <div class="kpi-comparison">
            <span class="badge" style="background: rgba(201, 162, 39, 0.18); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.35);">{{ $balanceLabel }}</span>
            <span class="kpi-text-muted ms-1">{{ $balanceMeta }}</span>
          </div>
        </div>
      </div>

      <!-- 4. Savings Goal -->
      <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
          <div class="kpi-card-header">
            <div>
              <span class="kpi-title">Semester Savings Goal</span>
              <div class="kpi-amount" id="kpiSavingsGoal" data-pkr="{{ $goalAmount }}">PKR {{ number_format($goalAmount, 0) }}</div>
            </div>
            <div class="kpi-icon-box kpi-icon-blue">
              <i class="bi bi-bullseye"></i>
            </div>
          </div>
          <div class="progress mb-2" style="height: 6px; background: rgba(255,255,255,0.06);">
            <div class="progress-bar" id="goalProgressBar" style="width: {{ $goalPercent }}%; background: var(--gold);"></div>
          </div>
          <div class="d-flex justify-content-between align-items-center small text-muted">
            <span id="goalPercentText" class="fw-semibold" style="color: var(--gold-light);">{{ $goalText }}</span>
            <span>{{ $bankedLabel }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="card p-3 mb-4 shadow-sm" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2);">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <span class="fw-bold small text-uppercase tracking-wider" style="color: var(--gold);">
          <i class="bi bi-grid-fill me-1"></i> Fast Terminal Ops:
        </span>
        <div class="d-flex flex-wrap gap-2 quick-actions-bar">
          <button type="button" class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#fastLogModal">
            <i class="bi bi-lightning-charge-fill" style="color: var(--gold);"></i> Fast Log
          </button>
          <a href="{{ route('transactions.create') }}?type=income" class="quick-action-btn">
            <i class="bi bi-plus-circle-fill text-success"></i> Add Income
          </a>
          <a href="{{ route('transactions.create') }}?type=expense" class="quick-action-btn">
            <i class="bi bi-dash-circle-fill text-danger"></i> Add Expense
          </a>
          <button type="button" class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#splitBillModal">
            <i class="bi bi-people text-info"></i> Roommate Split
          </button>
          <a href="/budgets" class="quick-action-btn">
            <i class="bi bi-pie-chart text-warning"></i> Set Budget
          </a>
          <a href="/reports" class="quick-action-btn">
            <i class="bi bi-file-earmark-bar-graph" style="color: var(--gold-light);"></i> Financial Analytics
          </a>
        </div>
      </div>
    </div>

    <!-- Charts Row 1: Budget vs Actual & Spending Breakdown -->
    <div class="row g-4 mb-4">
      <!-- Budget vs Actual Bar Chart -->
      <div class="col-lg-8">
        <div class="card h-100 shadow-sm" style="background: rgba(13, 40, 24, 0.45); border: 1px solid rgba(201, 162, 39, 0.18);">
          <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(5, 20, 10, 0.6); border-bottom: 1px solid rgba(201, 162, 39, 0.15);">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-bar-chart-fill" style="color: var(--gold);"></i>
              <span class="fw-bold" style="color: #F5F5DC;">Budget Allocation vs Actual Burn</span>
            </div>
            <span class="badge" style="background: rgba(201, 162, 39, 0.15); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.3);">Campus Envelopes</span>
          </div>
          <div class="card-body">
            <div style="height: 290px;">
              <canvas id="budgetVsActualChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Spending Breakdown Doughnut Chart -->
      <div class="col-lg-4">
        <div class="card h-100 shadow-sm" style="background: rgba(13, 40, 24, 0.45); border: 1px solid rgba(201, 162, 39, 0.18);">
          <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(5, 20, 10, 0.6); border-bottom: 1px solid rgba(201, 162, 39, 0.15);">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-pie-chart-fill" style="color: var(--gold);"></i>
              <span class="fw-bold" style="color: #F5F5DC;">Category Distribution</span>
            </div>
            <a href="/transactions" class="small text-decoration-none" style="color: var(--gold);">All Log &rarr;</a>
          </div>
          <div class="card-body">
            <div style="height: 290px;">
              <canvas id="spendingBreakdownChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row 2: 6-Month Trend & Top Category Spotlight -->
    <div class="row g-4 mb-4">
      <!-- Six-Month Trend Chart -->
      <div class="col-lg-8">
        <div class="card h-100 shadow-sm" style="background: rgba(13, 40, 24, 0.45); border: 1px solid rgba(201, 162, 39, 0.18);">
          <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(5, 20, 10, 0.6); border-bottom: 1px solid rgba(201, 162, 39, 0.15);">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-graph-up-arrow" style="color: #10B981;"></i>
              <span class="fw-bold" style="color: #F5F5DC;">Six-Month Runway Trend (Inflow vs Outflow)</span>
            </div>
            <small class="text-muted">{{ now()->subMonths(5)->format('M Y') }} — {{ now()->format('M Y') }}</small>
          </div>
          <div class="card-body">
            <div style="height: 260px;">
              <canvas id="sixMonthTrendChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Spending Category Dynamic Card -->
      <div class="col-lg-4">
        <div class="top-category-card h-100 d-flex flex-column justify-content-between shadow-sm" style="background: linear-gradient(145deg, rgba(13, 40, 24, 0.75), rgba(5, 20, 10, 0.9)); border: 1px solid rgba(201, 162, 39, 0.35); border-radius: 12px; padding: 1.5rem;">
          <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="badge" style="background: rgba(201, 162, 39, 0.2); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.4); padding: 0.35rem 0.75rem; font-weight: 600;">
                <i class="bi bi-fire me-1"></i> Highest Burn Category
              </span>
              <div class="p-2 rounded" style="background: rgba(201, 162, 39, 0.15); color: var(--gold); font-size: 1.25rem;">
                <i class="bi bi-cup-hot-fill"></i>
              </div>
            </div>
            <span class="text-muted small text-uppercase fw-bold letter-spacing-1">Dominant Spending Sector</span>
            <h2 class="display-6 fw-bold mt-1 mb-2" id="topCategoryName" style="color: var(--gold-light); font-family: var(--serif);">{{ $topCategory }}</h2>
            <div class="fs-4 fw-bold text-danger" id="topCategoryAmount" data-pkr="{{ $topAmount }}">PKR {{ number_format($topAmount, 0) }}</div>
            <p class="text-muted small mt-2">
              Represents <strong style="color: #F5F5DC;">{{ number_format($topSharePercent, 1) }}%</strong> of your total expenditures logged this month.
            </p>
          </div>
          <div class="pt-3 border-top border-dark-subtle mt-auto">
            <a href="/budgets" class="btn btn-sm w-100 fw-bold" style="background: rgba(201, 162, 39, 0.15); border: 1px solid rgba(201, 162, 39, 0.4); color: var(--gold);">
              <i class="bi bi-sliders me-1"></i> Fine-Tune {{ $topCategory }} Envelope
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Personalized Saving Tips Spotlight -->
    <div class="mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h5 class="fw-bold mb-0" style="color: #F5F5DC; font-family: var(--serif); font-size: 1.35rem;">AI Student Wealth & Frugality Signals</h5>
          <p class="text-muted small mb-0">Algorithmic heuristics tuned for university hostel and commuting life.</p>
        </div>
        <a href="/saving-tips" class="btn btn-sm" style="border: 1px solid rgba(201, 162, 39, 0.3); color: var(--gold);">
          All Tips <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
      <div class="row" id="dashboardTipsContainer"></div>
    </div>

    <!-- MODAL 1: Fast Log Expense Modal (Commercial High-Demand Feature) -->
    <div class="modal fade" id="fastLogModal" tabindex="-1" aria-labelledby="fastLogModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #092013; border: 1px solid rgba(201, 162, 39, 0.35); color: #F5F5DC;">
          <div class="modal-header" style="border-bottom: 1px solid rgba(201, 162, 39, 0.2);">
            <div class="d-flex align-items-center gap-2">
              <span class="p-2 rounded" style="background: rgba(201, 162, 39, 0.15); color: var(--gold);">
                <i class="bi bi-lightning-charge-fill"></i>
              </span>
              <div>
                <h5 class="modal-title fw-bold mb-0" id="fastLogModalLabel" style="color: var(--gold-light); font-family: var(--serif);">Fast Log Expense</h5>
                <small class="text-muted">Instant 1-Click Campus Micro-Spending Record</small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('transactions.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="expense">
            <input type="hidden" name="date" value="{{ now()->toDateString() }}">
            <div class="modal-body">
              <!-- Fast Preset Chips -->
              <label class="form-label small text-muted text-uppercase fw-bold mb-2">Campus 1-Click Presets:</label>
              <div class="d-flex flex-wrap gap-2 mb-3">
                <button type="button" class="btn btn-sm preset-chip" data-preset-pkr="80" data-preset-label="☕ Chai" onclick="applyPreset('Chai / Dhabba Tea', 80, 'Food')" style="background: rgba(201,162,39,0.1); border: 1px solid rgba(201,162,39,0.3); color: #F5F5DC;">
                  ☕ Chai (Rs 80)
                </button>
                <button type="button" class="btn btn-sm preset-chip" data-preset-pkr="350" data-preset-label="🍛 Lunch" onclick="applyPreset('Canteen Lunch', 350, 'Food')" style="background: rgba(201,162,39,0.1); border: 1px solid rgba(201,162,39,0.3); color: #F5F5DC;">
                  🍛 Lunch (Rs 350)
                </button>
                <button type="button" class="btn btn-sm preset-chip" data-preset-pkr="150" data-preset-label="🚌 Ride" onclick="applyPreset('Rickshaw / Metro Bus Fare', 150, 'Transport')" style="background: rgba(201,162,39,0.1); border: 1px solid rgba(201,162,39,0.3); color: #F5F5DC;">
                  🚌 Ride (Rs 150)
                </button>
                <button type="button" class="btn btn-sm preset-chip" data-preset-pkr="120" data-preset-label="🖨️ Prints" onclick="applyPreset('Course Notes & Printouts', 120, 'Academics')" style="background: rgba(201,162,39,0.1); border: 1px solid rgba(201,162,39,0.3); color: #F5F5DC;">
                  🖨️ Prints (Rs 120)
                </button>
                <button type="button" class="btn btn-sm preset-chip" data-preset-pkr="450" data-preset-label="📶 4G Data" onclick="applyPreset('Mobile 4G Data Bundle', 450, 'Subscriptions')" style="background: rgba(201,162,39,0.1); border: 1px solid rgba(201,162,39,0.3); color: #F5F5DC;">
                  📶 4G Data (Rs 450)
                </button>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold" style="color: var(--gold);">Amount (Base Currency) *</label>
                <div class="input-group">
                  <span class="input-group-text currency-symbol-label" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: var(--gold);">PKR</span>
                  <input type="number" step="1" name="amount" id="fastLogAmount" class="form-control" placeholder="e.g. 150" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold" style="color: var(--gold);">Description *</label>
                <input type="text" name="description" id="fastLogDescription" class="form-control" placeholder="e.g. Canteen Samosa & Tea" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold" style="color: var(--gold);">Category Envelope *</label>
                <select name="category_id" id="fastLogCategory" class="form-select" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                  @foreach($expenseCategories as $cat)
                    <option value="{{ $cat->id }}" {{ strtolower($cat->name) === 'food' ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(201, 162, 39, 0.2);">
              <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-sm fw-bold px-4" style="background: var(--gold); color: #05140A;">
                <i class="bi bi-check2-circle me-1"></i> Record Instantly
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL 2: Roommate & Peer Bill Splitter (High-Demand Student Feature) -->
    <div class="modal fade" id="splitBillModal" tabindex="-1" aria-labelledby="splitBillModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #092013; border: 1px solid rgba(201, 162, 39, 0.35); color: #F5F5DC;">
          <div class="modal-header" style="border-bottom: 1px solid rgba(201, 162, 39, 0.2);">
            <div class="d-flex align-items-center gap-2">
              <span class="p-2 rounded" style="background: rgba(16, 185, 129, 0.15); color: #10B981;">
                <i class="bi bi-people-fill"></i>
              </span>
              <div>
                <h5 class="modal-title fw-bold mb-0" id="splitBillModalLabel" style="color: var(--gold-light); font-family: var(--serif);">Roommate & Bill Splitter</h5>
                <small class="text-muted">Equitable Hostel, Food & Flat Expense Calculator</small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label small fw-bold" style="color: var(--gold);">Occasion / Item *</label>
              <input type="text" id="splitItemName" class="form-control" placeholder="e.g. Friday Dorm Biryani Order" value="Dorm Shared Dinner" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
            </div>

            <div class="row g-2 mb-3">
              <div class="col-7">
                <label class="form-label small fw-bold" style="color: var(--gold);">Total Bill Amount *</label>
                <input type="number" id="splitTotalAmount" class="form-control" placeholder="1200" value="1200" oninput="calculateSplit()" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
              </div>
              <div class="col-5">
                <label class="form-label small fw-bold" style="color: var(--gold);">Number of Roommates *</label>
                <input type="number" id="splitPersonCount" min="2" max="20" class="form-control" value="3" oninput="calculateSplit()" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
              </div>
            </div>

            <div class="p-3 rounded mb-3 text-center" style="background: rgba(5, 20, 10, 0.7); border: 1px solid rgba(201, 162, 39, 0.3);">
              <span class="small text-muted text-uppercase tracking-wider">Each Person Owes</span>
              <div class="display-6 fw-bold" id="splitPerPersonDisplay" style="color: var(--gold); font-family: var(--serif);">
                PKR 400
              </div>
              <small class="text-muted" id="splitSummaryText">Total PKR 1,200 divided equally among 3 students</small>
            </div>

            <div class="mb-2">
              <label class="form-label small fw-bold text-muted">Your Payment Details (Optional for WhatsApp request):</label>
              <input type="text" id="splitPayHandle" class="form-control form-control-sm" placeholder="e.g. Easypaisa / Nayapay: 0300-1234567" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(201, 162, 39, 0.2);">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-sm btn-success fw-bold d-flex align-items-center gap-1" onclick="shareViaWhatsApp()">
              <i class="bi bi-whatsapp"></i> Share on WhatsApp
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Floating Gold Fast-Log Button -->
    <button type="button" class="floating-fast-log-btn shadow-lg" data-bs-toggle="modal" data-bs-target="#fastLogModal" title="Fast Log Expense">
      <i class="bi bi-plus-lg fs-4"></i>
    </button>
@endsection

@push('scripts')
  <script>
    window.studentDashboardStats = @json($stats ?? []);
    window.studentDashboardData = @json($dashboardData ?? []);

    // Preset helper for Fast Log Modal
    function applyPreset(desc, amount, categoryName) {
      document.getElementById('fastLogDescription').value = desc;
      document.getElementById('fastLogAmount').value = amount;
      
      const select = document.getElementById('fastLogCategory');
      for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].text.toLowerCase().includes(categoryName.toLowerCase())) {
          select.selectedIndex = i;
          break;
        }
      }
    }

    // Live Roommate Split Calculator
    function calculateSplit() {
      const total = parseFloat(document.getElementById('splitTotalAmount').value) || 0;
      const count = parseInt(document.getElementById('splitPersonCount').value) || 1;
      const perPerson = count > 0 ? Math.ceil(total / count) : total;
      
      const formattedTotal = window.CampusCurrency ? window.CampusCurrency.format(total) : 'PKR ' + total.toLocaleString();
      const formattedPerPerson = window.CampusCurrency ? window.CampusCurrency.format(perPerson) : 'PKR ' + perPerson.toLocaleString();

      document.getElementById('splitPerPersonDisplay').innerText = formattedPerPerson;
      document.getElementById('splitSummaryText').innerText = `Total ${formattedTotal} divided equally among ${count} students`;
    }

    // Generate formatted WhatsApp message
    function shareViaWhatsApp() {
      const total = document.getElementById('splitTotalAmount').value || '0';
      const count = document.getElementById('splitPersonCount').value || '1';
      const item = document.getElementById('splitItemName').value || 'Campus Shared Expense';
      const handle = document.getElementById('splitPayHandle').value || '';
      const perPerson = Math.ceil(parseFloat(total) / parseInt(count));

      const formattedTotal = window.CampusCurrency ? window.CampusCurrency.format(total) : 'PKR ' + total;
      const formattedPerPerson = window.CampusCurrency ? window.CampusCurrency.format(perPerson) : 'PKR ' + perPerson;

      let msg = `*Campus Coin Bill Split: ${item}*\n`;
      msg += `Total Bill: ${formattedTotal}\n`;
      msg += `Per Person Share (${count} people): *${formattedPerPerson}*\n`;
      if (handle) {
        msg += `Send to: ${handle}\n`;
      }
      msg += `\n_Logged via Campus Coin Student Terminal_`;

      const url = `https://wa.me/?text=${encodeURIComponent(msg)}`;
      window.open(url, '_blank');
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="{{ asset('js/student/dashboard.js') }}"></script>
@endpush
