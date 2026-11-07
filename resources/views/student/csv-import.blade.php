@extends('student.master')

@section('title', 'Import Transactions — Campus Coin')
@section('page_heading', 'Import Transactions from CSV')
@section('page_description', 'Upload a CSV file to bulk import your historical income and expense records.')

@section('breadcrumbs')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="/transactions">Transactions</a></li>
    <li class="breadcrumb-item active">CSV Import</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-file-earmark-arrow-up text-primary fs-4"></i>
                <h5 class="fw-bold mb-0">Upload CSV File</h5>
            </div>

            @if(session('success'))
                <div class="alert alert-success d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill mt-1"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="alert alert-warning">
                    <strong><i class="bi bi-exclamation-circle me-1"></i>Import Issues:</strong>
                    <ul class="mb-0 mt-2 small">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('student.csv-import.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="csv_file" class="form-label fw-semibold">Select CSV File</label>
                    <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,.txt" required>
                    <small class="text-muted">Accepted formats: .csv — Maximum file size: 2 MB</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> Import Transactions
                    </button>
                    <a href="{{ route('student.csv-import.template') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-download me-1"></i> Download Template
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm border-0 p-4 h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-info-circle text-info fs-4"></i>
                <h5 class="fw-bold mb-0">CSV Format Guide</h5>
            </div>

            <p class="text-muted small">Your CSV file must include a header row with these exact column names:</p>

            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered small">
                    <thead class="table-light">
                        <tr>
                            <th>Column</th>
                            <th>Format</th>
                            <th>Example</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>date</code></td>
                            <td>YYYY-MM-DD</td>
                            <td>2026-01-15</td>
                        </tr>
                        <tr>
                            <td><code>description</code></td>
                            <td>Text</td>
                            <td>Campus Lunch</td>
                        </tr>
                        <tr>
                            <td><code>amount</code></td>
                            <td>Number</td>
                            <td>350</td>
                        </tr>
                        <tr>
                            <td><code>type</code></td>
                            <td>income / expense</td>
                            <td>expense</td>
                        </tr>
                        <tr>
                            <td><code>category</code></td>
                            <td>Category name</td>
                            <td>Food</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h6 class="fw-bold small">Available Categories:</h6>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <span class="badge bg-success-subtle text-success w-100 py-2">Income</span>
                    <ul class="list-unstyled small mt-1 ps-2">
                        @foreach($categories->where('type', 'income') as $cat)
                            <li class="text-muted">• {{ $cat->name }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-6">
                    <span class="badge bg-danger-subtle text-danger w-100 py-2">Expense</span>
                    <ul class="list-unstyled small mt-1 ps-2">
                        @foreach($categories->where('type', 'expense') as $cat)
                            <li class="text-muted">• {{ $cat->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="alert alert-light border small mb-0">
                <i class="bi bi-lightbulb me-1 text-warning"></i>
                <strong>Tip:</strong> Category names must match exactly (case-insensitive). Rows with unrecognized categories will be skipped.
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 p-4 mt-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-table me-2"></i>Sample CSV Preview</h6>
    <div class="table-responsive">
        <table class="table table-sm table-striped small mb-0">
            <thead class="table-dark">
                <tr>
                    <th>date</th>
                    <th>description</th>
                    <th>amount</th>
                    <th>type</th>
                    <th>category</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>2026-01-15</td><td>Monthly Allowance from Family</td><td>25000</td><td>income</td><td>Allowance</td></tr>
                <tr><td>2026-01-16</td><td>Campus Cafe Lunch</td><td>350</td><td>expense</td><td>Food</td></tr>
                <tr><td>2026-01-17</td><td>Bus Pass Weekly</td><td>500</td><td>expense</td><td>Transport</td></tr>
                <tr><td>2026-01-20</td><td>Part-time Tutoring</td><td>5000</td><td>income</td><td>Part-time Job</td></tr>
                <tr><td>2026-01-22</td><td>Textbook Purchase</td><td>1200</td><td>expense</td><td>Academics</td></tr>
                <tr><td>2026-02-01</td><td>Hostel Rent February</td><td>8000</td><td>expense</td><td>Hostel/Rent</td></tr>
                <tr><td>2026-02-05</td><td>Netflix Subscription</td><td>1500</td><td>expense</td><td>Subscriptions</td></tr>
                <tr><td>2026-02-10</td><td>Birthday Gift from Uncle</td><td>3000</td><td>income</td><td>Gift</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
