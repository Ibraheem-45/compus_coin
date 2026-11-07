@extends('student.master')

@section('title', 'Personalized Saving Tips — Campus Coin')
@section('page_heading', 'Personalized Saving Tips')
@section('page_description', 'Smart, student-tested recommendations to optimize your monthly expenses.')

@section('content')
        <section class="mb-4 pb-4 border-bottom" aria-labelledby="monthlyInsightsHeading">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
              <h5 class="fw-bold mb-1" id="monthlyInsightsHeading">Monthly Spending Insights</h5>
              <p class="text-muted small mb-0">Based on your expense records and category budgets.</p>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" id="refreshInsightsBtn">
              <i class="bi bi-arrow-clockwise me-1"></i> Refresh insights
            </button>
          </div>
          <div class="row g-3" id="monthlyInsights" aria-live="polite">
            <div class="col-12 text-muted small">Refresh to see what your spending records say this month.</div>
          </div>
        </section>

<!-- Category Filter Buttons -->
        <div class="d-flex flex-wrap gap-2 mb-4">
          <button class="btn btn-sm btn-primary tips-filter-btn active" data-filter="All">All Categories</button>
          <button class="btn btn-sm btn-outline-secondary tips-filter-btn" data-filter="Food">Food & Dining</button>
          <button class="btn btn-sm btn-outline-secondary tips-filter-btn" data-filter="Transport">Transport</button>
          <button class="btn btn-sm btn-outline-secondary tips-filter-btn" data-filter="Academics">Academics</button>
          <button class="btn btn-sm btn-outline-secondary tips-filter-btn" data-filter="Subscriptions">Subscriptions</button>
          <button class="btn btn-sm btn-outline-secondary tips-filter-btn" data-filter="Entertainment">Entertainment</button>
        </div>

        <!-- Tips Grid -->
        <div class="row" id="savingTipsGrid">
          <!-- Populated dynamically by js/tips.js -->
        </div>
@endsection

@push('scripts')
  <script>
    window.studentTipsData = @json($tips ?? []);
    window.studentHasMonthExpenses = @json($hasMonthExpenses ?? false);
    window.studentInsightsUrl = @json(route('student.insights.refresh'));
  </script>
  <script src="{{ asset('js/student/tips.js') }}"></script>
@endpush
        