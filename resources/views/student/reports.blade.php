@extends('student.master')

@section('title', 'Analytics & Reports — Campus Coin')
@section('page_heading', 'Financial Intelligence & Reports')
@section('page_description', 'Comprehensive analytics of your university cash flow and seasonal trends.')

@section('breadcrumbs')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none" style="color: var(--gold);">Dashboard</a></li>
    <li class="breadcrumb-item active text-muted">Reports</li>
  </ol>
</nav>
@endsection

@section('content')
        <!-- Report Filters -->
        <div class="card p-3 mb-4 shadow-sm no-print" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.25) !important;">
          <div class="row g-3 align-items-end">
            <div class="col-sm-6 col-xl-2">
              <label class="form-label small fw-bold mb-1" style="color: var(--gold);" for="reportDateFrom">From</label>
              <input class="form-control form-control-sm" id="reportDateFrom" type="date" value="{{ $reportData['filters']['date_from'] }}" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
            </div>
            <div class="col-sm-6 col-xl-2">
              <label class="form-label small fw-bold mb-1" style="color: var(--gold);" for="reportDateTo">To</label>
              <input class="form-control form-control-sm" id="reportDateTo" type="date" value="{{ $reportData['filters']['date_to'] }}" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
            </div>
            <div class="col-sm-6 col-xl-3">
              <label class="form-label small fw-bold mb-1" style="color: var(--gold);" for="reportCategoryFilter">Category / Source</label>
              <select class="form-select form-select-sm" id="reportCategoryFilter" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                <option value="all">All Categories</option>
                @foreach ($categories as $category)
                  <option value="{{ $category->id }}" @selected((string) $reportData['filters']['category_id'] === (string) $category->id)>{{ $category->name }} ({{ ucfirst($category->type) }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-6 col-xl-3">
              <label class="form-label small fw-bold mb-1" style="color: var(--gold);" for="reportFlowFilter">Transaction Flow</label>
              <select class="form-select form-select-sm" id="reportFlowFilter" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                <option value="both" @selected($reportData['filters']['flow'] === 'both')>Income &amp; Expenses</option>
                <option value="expense" @selected($reportData['filters']['flow'] === 'expense')>Expenses Only</option>
                <option value="income" @selected($reportData['filters']['flow'] === 'income')>Income Only</option>
              </select>
            </div>
            <div class="col-xl-2">
              <button type="button" class="btn btn-sm fw-bold w-100" id="applyReportFiltersBtn" style="background: var(--gold); color: #05140A;">
                <i class="bi bi-funnel me-1"></i> Apply Filter
              </button>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4 no-print">
          <button type="button" class="btn btn-sm btn-outline-light" id="exportPdfBtn" title="Print or save report as PDF" style="border-color: rgba(201,162,39,0.3); color: #F5F5DC;"><i class="bi bi-file-earmark-pdf me-1" style="color: var(--gold);"></i> PDF Export</button>
          <button type="button" class="btn btn-sm btn-outline-light" id="exportImageBtn" title="Download report chart as an image" style="border-color: rgba(201,162,39,0.3); color: #F5F5DC;"><i class="bi bi-image me-1" style="color: var(--gold);"></i> Chart Image</button>
        </div>

        <!-- Charts Grid -->
        <div class="row g-4 mb-4">
          <!-- 1. Monthly Spending Chart -->
          <div class="col-lg-6">
            <div class="card h-100 shadow-sm" style="background: rgba(13, 40, 24, 0.45); border: 1px solid rgba(201, 162, 39, 0.2);">
              <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(5, 20, 10, 0.6); border-bottom: 1px solid rgba(201, 162, 39, 0.15);">
                <span class="fw-bold" style="color: #F5F5DC;"><i class="bi bi-bar-chart-fill me-2" style="color: var(--gold);"></i>Monthly Spending</span>
                <span class="badge" id="reportRangeLabel" style="background: rgba(201, 162, 39, 0.15); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.3);">{{ $reportData['month_label'] }}</span>
              </div>
              <div class="card-body">
                <div style="height: 270px;">
                  <canvas id="reportMonthlySpending"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Income vs Expense -->
          <div class="col-lg-6">
            <div class="card h-100 shadow-sm" style="background: rgba(13, 40, 24, 0.45); border: 1px solid rgba(201, 162, 39, 0.2);">
              <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(5, 20, 10, 0.6); border-bottom: 1px solid rgba(201, 162, 39, 0.15);">
                <span class="fw-bold" style="color: #F5F5DC;"><i class="bi bi-arrow-left-right me-2" style="color: #10B981;"></i>Income vs Expense</span>
                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.3);">Cash Flow</span>
              </div>
              <div class="card-body">
                <div style="height: 270px;">
                  <canvas id="reportIncomeVsExpense"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Daily Spending -->
          <div class="col-lg-6">
            <div class="card h-100 shadow-sm" style="background: rgba(13, 40, 24, 0.45); border: 1px solid rgba(201, 162, 39, 0.2);">
              <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(5, 20, 10, 0.6); border-bottom: 1px solid rgba(201, 162, 39, 0.15);">
                <span class="fw-bold" style="color: #F5F5DC;"><i class="bi bi-graph-up me-2" style="color: #f59e0b;"></i>Daily Spending Outflow</span>
                <span class="badge" id="reportDailyLabel" style="background: rgba(201, 162, 39, 0.15); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.3);">Current month · {{ $reportData['daily_label'] }}</span>
              </div>
              <div class="card-body">
                <div style="height: 270px;">
                  <canvas id="reportDailySpending"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Weekly Spending -->
          <div class="col-lg-6">
            <div class="card h-100 shadow-sm" style="background: rgba(13, 40, 24, 0.45); border: 1px solid rgba(201, 162, 39, 0.2);">
              <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(5, 20, 10, 0.6); border-bottom: 1px solid rgba(201, 162, 39, 0.15);">
                <span class="fw-bold" style="color: #F5F5DC;"><i class="bi bi-calendar4-week me-2" style="color: #38BDF8;"></i>Weekly Spending</span>
                <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38BDF8; border: 1px solid rgba(56, 189, 248, 0.3);">Current month</span>
              </div>
              <div class="card-body">
                <div style="height: 270px;">
                  <canvas id="reportWeeklySpending"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- 5. Category Breakdown (Full Width) -->
          <div class="col-12">
            <div class="card shadow-sm" style="background: rgba(13, 40, 24, 0.45); border: 1px solid rgba(201, 162, 39, 0.2);">
              <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(5, 20, 10, 0.6); border-bottom: 1px solid rgba(201, 162, 39, 0.15);">
                <span class="fw-bold" style="color: #F5F5DC;"><i class="bi bi-pie-chart-fill me-2" style="color: var(--gold);"></i>Category Breakdown Distribution</span>
                <span class="badge" style="background: rgba(201, 162, 39, 0.15); color: var(--gold); border: 1px solid rgba(201, 162, 39, 0.3);">Selected date range</span>
              </div>
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-7">
                    <div style="height: 300px;">
                      <canvas id="reportCategoryBreakdown"></canvas>
                    </div>
                  </div>
                  <div class="col-md-5 mt-4 mt-md-0">
                    <h6 class="fw-bold mb-3" id="reportTakeawayHeading" style="color: var(--gold-light); font-family: var(--serif);">Key Takeaways for {{ $reportData['month_label'] ?? now()->translatedFormat('F Y') }}</h6>
                    <ul class="list-group list-group-flush small" id="reportHighlights" style="background: transparent;">
                      @foreach(($reportData['highlights'] ?? []) as $index => $highlight)
                        <li class="list-group-item d-flex justify-content-between px-0" style="background: transparent; border-color: rgba(255,255,255,0.06); color: #F5F5DC;">
                          <span>
                            <i class="bi bi-circle-fill me-1" style="color: {{ ['#C9A227','#10B981','#38BDF8','#F59E0B','#8FBC8F','#A78BFA'][$index % 6] }}"></i>
                            {{ $highlight['name'] }}
                          </span>
                          <strong class="currency-val" data-pkr="{{ $highlight['amount'] }}" style="font-family: var(--mono); color: var(--gold);">
                            PKR {{ number_format($highlight['amount'], 0) }} <span class="text-muted fw-normal">({{ $highlight['share'] }}%)</span>
                          </strong>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
@endsection

@push('scripts')
  <script>
    window.reportData = @json($reportData ?? []);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="{{ asset('js/student/reports.js') }}"></script>
@endpush
