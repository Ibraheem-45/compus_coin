@extends('student.master')

@section('title', 'Edit Transaction — Campus Coin')
@section('page_heading', 'Edit Transaction')
@section('page_description', 'Update or delete an existing transaction record.')

@section('breadcrumbs')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="/transactions">Transactions</a></li>
    <li class="breadcrumb-item active">Edit Transaction</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 p-4">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('transactions.update', $transaction) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label fw-semibold">Transaction Type</label>
                    <div class="d-flex gap-3">
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="type" id="typeExpense" value="expense" {{ $transaction->type === 'expense' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger w-100" for="typeExpense">
                                <i class="bi bi-dash-circle me-1"></i> Expense
                            </label>
                        </div>
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="type" id="typeIncome" value="income" {{ $transaction->type === 'income' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success w-100" for="typeIncome">
                                <i class="bi bi-plus-circle me-1"></i> Income
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label fw-semibold">Amount (PKR)</label>
                        <div class="input-group">
                            <span class="input-group-text">PKR</span>
                            <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" value="{{ old('amount', $transaction->amount) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="date" class="form-label fw-semibold">Date</label>
                        <input type="date" name="date" id="date" value="{{ old('date', $transaction->date) }}" class="form-control" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="description" class="form-label fw-semibold">Description</label>
                    <input type="text" name="description" id="description" value="{{ old('description', $transaction->description) }}" class="form-control" required>
                </div>

                <div class="mt-3">
                    <label for="category_id" class="form-label fw-semibold">Category</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ ucfirst($category->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-3 p-3 bg-light rounded border">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="recurring" name="recurring" value="1" {{ old('recurring', $transaction->recurring) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="recurring">Recurring transaction</label>
                    </div>

                    <div class="mt-3">
                        <label for="recurring_frequency" class="form-label fw-semibold">Frequency</label>
                        <select name="recurring_frequency" id="recurring_frequency" class="form-select">
                            <option value="">None</option>
                            <option value="weekly" {{ old('recurring_frequency', $transaction->recurring_frequency) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('recurring_frequency', $transaction->recurring_frequency) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('recurring_frequency', $transaction->recurring_frequency) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="notes" class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $transaction->notes) }}</textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteTransactionModal">
                        Delete
                    </button>

                    <div class="d-flex gap-2">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this transaction?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
@endsection
