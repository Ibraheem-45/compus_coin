@extends('admin.master')

@section('content')

<div class="container-fluid">

{{-- Page Header --}}
<div class="mb-4">
    <h4 class="fw-bold text-main mb-1">
        <i class="bi bi-bar-chart-line text-primary me-2"></i>
        Usage Analytics
    </h4>
    <p class="text-muted mb-0">
        System-wide usage statistics for CampusCoin.
    </p>
</div>

{{-- Main Statistics --}}
<div class="row g-4 mb-4">

    {{-- Active Students --}}
    <div class="col-md-4">
        <div class="admin-card card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">
                            Active Students
                        </div>

                        <h2 class="fw-bold text-main mb-0">
                            {{ number_format($activeStudents) }}
                        </h2>
                    </div>

                    <div class="fs-2 text-success">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>

                <small class="text-muted">
                    Currently active student accounts
                </small>
            </div>
        </div>
    </div>

    {{-- Total Transactions --}}
    <div class="col-md-4">
        <div class="admin-card card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">
                            Total Transactions
                        </div>

                        <h2 class="fw-bold text-main mb-0">
                            {{ number_format($totalTransactions) }}
                        </h2>
                    </div>

                    <div class="fs-2 text-primary">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                </div>

                <small class="text-muted">
                    All transactions recorded in the system
                </small>
            </div>
        </div>
    </div>

    {{-- Most Used Category --}}
    <div class="col-md-4">
        <div class="admin-card card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">
                            Most Used Category
                        </div>

                        <h4 class="fw-bold text-main mb-1">
                            {{ $mostUsedCategories->first()?->name ?? 'No Data' }}
                        </h4>
                    </div>

                    <div class="fs-2 text-warning">
                        <i class="bi bi-tags"></i>
                    </div>
                </div>

                <small class="text-muted">
                    Based on transaction usage
                </small>
            </div>
        </div>
    </div>

</div>


{{-- Most Used Categories --}}
<div class="admin-card card shadow-sm">

    <div class="card-header bg-transparent">
        <h5 class="fw-bold text-main mb-1">
            <i class="bi bi-bar-chart text-primary me-2"></i>
            Most Used Categories
        </h5>

        <small class="text-muted">
            Categories ordered by the number of recorded transactions.
        </small>
    </div>

    <div class="card-body">

        <div class="position-relative mb-4" style="height: 280px;" aria-busy="true" id="categoryChartRegion">
            <div id="categoryChartLoading" class="position-absolute top-50 start-50 translate-middle text-muted small" role="status">
                <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Preparing category chart…
            </div>
            <canvas id="adminCategoryUsageChart" aria-label="Most used categories by transaction count" role="img"></canvas>
        </div>

        @if($mostUsedCategories->count() > 0)

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th class="text-end">Transactions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($mostUsedCategories as $index => $category)

                            <tr>
                                <td>
                                    {{ $index + 1 }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $category->name }}
                                </td>

                                <td>
                                    @if($category->type === 'income')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            Income
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            Expense
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end fw-semibold">
                                    {{ number_format($category->transaction_count) }}
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-5">
                <i class="bi bi-bar-chart fs-1 text-muted"></i>

                <p class="text-muted mt-3 mb-0">
                    No transaction data is available yet.
                </p>
            </div>

        @endif

    </div>
</div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartCanvas = document.getElementById('adminCategoryUsageChart');
    const chartRegion = document.getElementById('categoryChartRegion');
    const loading = document.getElementById('categoryChartLoading');
    const categories = @json($categoryChartData);

    if (!chartCanvas || !window.Chart) {
        if (loading) loading.textContent = 'Category chart is unavailable.';
        return;
    }

    new Chart(chartCanvas, {
        type: 'bar',
        data: {
            labels: categories.map(category => category.name),
            datasets: [{
                label: 'Transactions',
                data: categories.map(category => category.count),
                backgroundColor: '#0d9488',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 850,
                easing: 'easeOutQuart',
                onProgress: () => {
                    if (loading) loading.classList.remove('d-none');
                },
                onComplete: () => {
                    if (loading) loading.classList.add('d-none');
                    if (chartRegion) chartRegion.setAttribute('aria-busy', 'false');
                }
            },
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
});
</script>
@endpush
