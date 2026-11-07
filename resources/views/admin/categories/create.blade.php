@extends('admin.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card card shadow-sm mb-4 border-0 rounded-4" style="background: rgba(5, 20, 10, 0.7); border: 1px solid rgba(255, 255, 255, 0.15) !important;">
            <div class="p-3 d-flex align-items-center justify-content-between" style="background: rgba(255, 255, 255, 0.08); border-bottom: 1px solid rgba(255, 255, 255, 0.12); border-radius: 16px 16px 0 0;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle text-primary me-1"></i> New
                    </span>
                    <h5 class="fw-bold mb-0 text-white">Add Default Campus Category</h5>
                </div>
                <a href="{{ route('admin.categories') }}" class="btn btn-sm btn-outline-light px-3 py-1 rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back to Categories
                </a>
            </div>

            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold text-white small">Category Name</label>
                        <input type="text"
                               name="name"
                               class="form-control bg-dark text-white border-secondary"
                               value="{{ old('name') }}"
                               placeholder="e.g. Laboratory Equipment, Mess Fee, Library"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-white small">Category Type</label>
                        <select name="type" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">Select type</option>
                            <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>
                                Expense (Student spending)
                            </option>
                            <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>
                                Income (Allowance, Stipend, Scholarship)
                            </option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 justify-content-end pt-3 border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                        <a href="{{ route('admin.categories') }}" class="btn btn-outline-light px-4">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-warning fw-bold px-4" style="background: var(--gold); border: none; color: #05140a;">
                            <i class="bi bi-check-circle me-1"></i>
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
