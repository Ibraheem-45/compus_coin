@extends('student.master')

@section('title', 'Campus Cashflow Ledger — Campus Coin')
@section('page_heading', 'Transactions Ledger')
@section('page_description', 'Real-time student cashflow logs, micro-expenses, and semester allowances.')

@section('breadcrumbs')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none" style="color: var(--gold);">Dashboard</a></li>
    <li class="breadcrumb-item active text-muted">Transactions</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h5 class="mb-1 fw-bold" style="color: #F5F5DC; font-family: var(--serif); font-size: 1.35rem;">Live Campus Audit Ledger</h5>
        <p class="text-muted small mb-0">Review past expenditures, hostel bills, and scholarship inflows.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('student.csv-import') }}" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1" style="border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
            <i class="bi bi-file-earmark-spreadsheet" style="color: var(--gold);"></i> Import CSV
        </a>
        <a href="{{ route('transactions.create') }}" class="btn btn-sm fw-bold px-3 d-flex align-items-center gap-1" style="background: var(--gold); color: #05140A;">
            <i class="bi bi-plus-lg"></i> Add Entry
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10B981;">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('budget_warning'))
    <div class="alert alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #EF4444;">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ session('budget_warning') }}</span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 overflow-hidden" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.2) !important;">
    <div class="table-responsive">
        <table class="table align-middle mb-0" style="color: #F5F5DC;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(201, 162, 39, 0.25); background: rgba(5, 20, 10, 0.6); color: var(--gold); font-size: 0.8rem;">
                    <th class="py-3 px-3">DATE</th>
                    <th class="py-3">MEMO / DESCRIPTION</th>
                    <th class="py-3">CATEGORY ENVELOPE</th>
                    <th class="py-3">FLOW TYPE</th>
                    <th class="py-3 text-end">AMOUNT</th>
                    <th class="py-3 px-3 text-end">OPERATIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                        <td class="py-3 px-3 small text-muted" style="font-family: var(--mono);">
                            {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                        </td>
                        <td class="py-3 fw-semibold" style="color: #F5F5DC;">
                            {{ $transaction->description }}
                        </td>
                        <td class="py-3">
                            <span class="badge" style="background: rgba(201, 162, 39, 0.12); color: var(--gold-light); border: 1px solid rgba(201, 162, 39, 0.25);">
                                {{ $transaction->category?->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="py-3">
                            <span class="badge" style="background: {{ $transaction->type === 'income' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $transaction->type === 'income' ? '#10B981' : '#EF4444' }}; border: 1px solid {{ $transaction->type === 'income' ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)' }};">
                                {{ $transaction->type === 'income' ? '+ Inflow' : '- Outflow' }}
                            </span>
                        </td>
                        <td class="py-3 text-end fw-bold currency-val" data-pkr="{{ (float) $transaction->amount }}" style="font-family: var(--mono); color: {{ $transaction->type === 'income' ? '#10B981' : '#EF4444' }};">
                            {{ $transaction->type === 'income' ? '+' : '-' }} PKR {{ number_format($transaction->amount, 0) }}
                        </td>
                        <td class="py-3 px-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm" style="background: rgba(201,162,39,0.1); border: 1px solid rgba(201,162,39,0.3); color: var(--gold); padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete this transaction record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="p-3">
                                <i class="bi bi-receipt fs-1" style="color: var(--gold); opacity: 0.5;"></i>
                                <h6 class="mt-3" style="color: #F5F5DC;">No Transactions Recorded Yet</h6>
                                <p class="text-muted small mb-3">Begin tracking your daily campus allowances and expenses.</p>
                                <a href="{{ route('transactions.create') }}" class="btn btn-sm fw-bold px-3" style="background: var(--gold); color: #05140A;">
                                    <i class="bi bi-plus-lg me-1"></i> Record First Expense
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
