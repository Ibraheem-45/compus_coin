@extends('student.master')

@section('title', 'Log Campus Transaction — Campus Coin')
@section('page_heading', 'Record Cashflow Entry')
@section('page_description', 'Log university expenses, hostel dues, freelancing stipends, or monthly allowances.')

@section('breadcrumbs')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none" style="color: var(--gold);">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="/transactions" class="text-decoration-none" style="color: var(--gold);">Transactions</a></li>
    <li class="breadcrumb-item active text-muted">Add Entry</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 p-4" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.25) !important;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="p-2 rounded" style="background: rgba(201, 162, 39, 0.15); color: var(--gold);">
                    <i class="bi bi-wallet-fill"></i>
                </span>
                <h5 class="fw-bold mb-0" style="color: #F5F5DC; font-family: var(--serif); font-size: 1.35rem;">Transaction Details</h5>
            </div>

            @if($errors->any())
                <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #EF4444;">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('transactions.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label small fw-bold" style="color: var(--gold);">Transaction Flow Type *</label>
                    <div class="d-flex gap-3">
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="type" id="typeExpense" value="expense" @checked(old('type', $selectedType) === 'expense')>
                            <label class="btn btn-outline-danger w-100 fw-bold py-2" for="typeExpense">
                                <i class="bi bi-dash-circle me-1"></i> Expense / Outflow
                            </label>
                        </div>
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="type" id="typeIncome" value="income" @checked(old('type', $selectedType) === 'income')>
                            <label class="btn btn-outline-success w-100 fw-bold py-2" for="typeIncome">
                                <i class="bi bi-plus-circle me-1"></i> Income / Inflow
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label small fw-bold" style="color: var(--gold);">Amount (PKR) *</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: var(--gold); font-family: var(--mono);">PKR</span>
                            <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" placeholder="e.g. 500" value="{{ old('amount') }}" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="date" class="form-label small fw-bold" style="color: var(--gold);">Transaction Date *</label>
                        <input type="date" name="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-control" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                    </div>
                </div>

                <div class="mt-3">
                    <label for="description" class="form-label small fw-bold" style="color: var(--gold);">Description / Memo *</label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}" class="form-control" placeholder="e.g. University Cafeteria, Rickshaw Fare, Book Fair" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                </div>

                <div class="mt-3">
                    <label for="category_id" class="form-label small fw-bold" style="color: var(--gold);">Category Envelope *</label>
                    <select name="category_id" id="category_id" class="form-select" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-category-type="{{ $category->type }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ ucfirst($category->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4 p-3 rounded" style="background: rgba(5, 20, 10, 0.6); border: 1px solid rgba(201, 162, 39, 0.2);">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="recurring" name="recurring" value="1" {{ old('recurring') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="recurring" style="color: #F5F5DC;">Recurring Campus Transaction</label>
                    </div>

                    <div class="mt-3">
                        <label for="recurring_frequency" class="form-label small text-muted">Frequency Pattern</label>
                        <select name="recurring_frequency" id="recurring_frequency" class="form-select" style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                            <option value="">None (One-time)</option>
                            <option value="weekly" {{ old('recurring_frequency') === 'weekly' ? 'selected' : '' }}>Weekly (e.g. Weekly allowance)</option>
                            <option value="monthly" {{ old('recurring_frequency') === 'monthly' ? 'selected' : '' }}>Monthly (e.g. Hostel rent / Mess bill)</option>
                            <option value="yearly" {{ old('recurring_frequency') === 'yearly' ? 'selected' : '' }}>Yearly (e.g. Annual semester fee)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="notes" class="form-label small fw-bold" style="color: var(--gold);">Supplementary Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Roommate names, receipt reference number, or transaction notes..." style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-dark-subtle">
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn fw-bold px-4" style="background: var(--gold); color: #05140A;">
                        <i class="bi bi-check2-circle me-1"></i> Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const categorySelect = document.getElementById('category_id');
        const typeInputs = document.querySelectorAll('input[name="type"]');

        const filterCategories = () => {
            const selectedType = document.querySelector('input[name="type"]:checked')?.value;
            const selectedOption = categorySelect.selectedOptions[0];

            if (selectedOption?.dataset.categoryType && selectedOption.dataset.categoryType !== selectedType) {
                categorySelect.value = '';
            }

            Array.from(categorySelect.options).forEach(option => {
                if (option.dataset.categoryType) {
                    option.hidden = option.dataset.categoryType !== selectedType;
                }
            });
        };

        typeInputs.forEach(input => input.addEventListener('change', filterCategories));
        filterCategories();
    });
</script>
@endpush
