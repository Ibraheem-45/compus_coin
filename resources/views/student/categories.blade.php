@extends('student.master')

@section('title', 'Category Envelopes — Campus Coin')
@section('page_heading', 'Campus Categories')
@section('page_description', 'Configure university income channels and spending envelopes.')

@section('breadcrumbs')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none" style="color: var(--gold);">Dashboard</a></li>
    <li class="breadcrumb-item active text-muted">Categories</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="row g-4">
    <!-- Add Category Form -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 p-4" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.25) !important;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="p-2 rounded" style="background: rgba(201, 162, 39, 0.15); color: var(--gold);">
                    <i class="bi bi-tag-fill"></i>
                </span>
                <h5 class="fw-bold mb-0" style="color: #F5F5DC; font-family: var(--serif);">Create Custom Category</h5>
            </div>
            <p class="text-muted small mb-3">Add personalized labels for your campus expenses or gigs.</p>

            @if($errors->any())
                <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #EF4444;">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label small fw-bold" style="color: var(--gold);">Category Name *</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Hostel Mess, Freelancing" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label small fw-bold" style="color: var(--gold);">Taxonomy Type *</label>
                    <select name="type" id="type" class="form-select" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                        <option value="expense">Expense / Outflow</option>
                        <option value="income">Income / Inflow</option>
                    </select>
                </div>

                <button type="submit" class="btn fw-bold w-100" style="background: var(--gold); color: #05140A;">
                    <i class="bi bi-plus-lg me-1"></i> Save Category
                </button>
            </form>
        </div>
    </div>

    <!-- Available Categories List -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 p-4" style="background: rgba(13, 40, 24, 0.55); border: 1px solid rgba(201, 162, 39, 0.25) !important;">
            <h5 class="fw-bold mb-3" style="color: #F5F5DC; font-family: var(--serif);">Configured Campus Taxonomies</h5>

            <div class="row g-3">
                @php
                    $incomeCategories = $categories->where('type', 'income');
                    $expenseCategories = $categories->where('type', 'expense');
                @endphp

                <!-- Income Categories -->
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-1" style="color: #10B981;">
                        <i class="bi bi-arrow-down-left-circle"></i> Inflow Envelopes ({{ $incomeCategories->count() }})
                    </h6>
                    @foreach($incomeCategories as $category)
                        <div class="p-2 mb-2 rounded" style="background: rgba(5, 20, 10, 0.6); border: 1px solid rgba(16, 185, 129, 0.25);">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <span style="color: #F5F5DC;" class="small fw-semibold">{{ $category->name }}</span>
                                @if(!$category->is_default)
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning p-1 lh-1" data-category-edit-toggle title="Edit Category">
                                            <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                        </button>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger p-1 lh-1" type="submit" title="Delete Category">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="badge" style="background: rgba(201, 162, 39, 0.15); color: var(--gold); font-size: 0.65rem;">Default</span>
                                @endif
                            </div>

                            @if(!$category->is_default)
                                <form method="POST" action="{{ route('categories.update', $category) }}" class="category-edit-form mt-2 d-none pt-2 border-top border-dark-subtle">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12">
                                            <input type="text" name="name" value="{{ $category->name }}" class="form-control form-control-sm" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                                        </div>
                                        <div class="col-6">
                                            <select name="type" class="form-select form-select-sm" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                                                <option value="income" {{ $category->type === 'income' ? 'selected' : '' }}>Income</option>
                                                <option value="expense" {{ $category->type === 'expense' ? 'selected' : '' }}>Expense</option>
                                            </select>
                                        </div>
                                        <div class="col-6 d-flex gap-1">
                                            <button type="submit" class="btn btn-sm fw-bold w-100" style="background: var(--gold); color: #05140A; font-size: 0.75rem;">Save</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary w-100" data-category-edit-cancel style="font-size: 0.75rem;">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Expense Categories -->
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-1" style="color: #EF4444;">
                        <i class="bi bi-arrow-up-right-circle"></i> Outflow Envelopes ({{ $expenseCategories->count() }})
                    </h6>
                    @foreach($expenseCategories as $category)
                        <div class="p-2 mb-2 rounded" style="background: rgba(5, 20, 10, 0.6); border: 1px solid rgba(239, 68, 68, 0.25);">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <span style="color: #F5F5DC;" class="small fw-semibold">{{ $category->name }}</span>
                                @if(!$category->is_default)
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning p-1 lh-1" data-category-edit-toggle title="Edit Category">
                                            <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                        </button>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger p-1 lh-1" type="submit" title="Delete Category">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="badge" style="background: rgba(201, 162, 39, 0.15); color: var(--gold); font-size: 0.65rem;">Default</span>
                                @endif
                            </div>

                            @if(!$category->is_default)
                                <form method="POST" action="{{ route('categories.update', $category) }}" class="category-edit-form mt-2 d-none pt-2 border-top border-dark-subtle">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12">
                                            <input type="text" name="name" value="{{ $category->name }}" class="form-control form-control-sm" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                                        </div>
                                        <div class="col-6">
                                            <select name="type" class="form-select form-select-sm" required style="background: #05140A; border-color: rgba(201,162,39,0.3); color: #F5F5DC;">
                                                <option value="income" {{ $category->type === 'income' ? 'selected' : '' }}>Income</option>
                                                <option value="expense" {{ $category->type === 'expense' ? 'selected' : '' }}>Expense</option>
                                            </select>
                                        </div>
                                        <div class="col-6 d-flex gap-1">
                                            <button type="submit" class="btn btn-sm fw-bold w-100" style="background: var(--gold); color: #05140A; font-size: 0.75rem;">Save</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary w-100" data-category-edit-cancel style="font-size: 0.75rem;">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-category-edit-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const card = button.closest('.rounded');
                const form = card ? card.querySelector('.category-edit-form') : null;
                if (form) {
                    form.classList.toggle('d-none');
                }
            });
        });

        document.querySelectorAll('[data-category-edit-cancel]').forEach(function (button) {
            button.addEventListener('click', function () {
                const form = button.closest('.category-edit-form');
                if (form) {
                    form.classList.add('d-none');
                }
            });
        });
    });
</script>
@endpush
