@extends('admin.master')

@section('content')
<div class="container-fluid px-0">
    <!-- Top KPI White Metric Cards for High Contrast & Readability -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; color: #0a1f12;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold small text-muted" style="letter-spacing: 0.5px;">All Categories</div>
                        <div class="fs-2 fw-bolder mt-1 text-dark">{{ $categories->count() }}</div>
                        <div class="small text-primary fw-semibold mt-1"><i class="bi bi-tags-fill me-1"></i>Active campus tags</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(59, 130, 246, 0.15); color: #2563eb;">
                        <i class="bi bi-tag-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; color: #0a1f12;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold small text-muted" style="letter-spacing: 0.5px;">Expense Categories</div>
                        <div class="fs-2 fw-bolder mt-1 text-danger">{{ $categories->where('type', 'expense')->count() }}</div>
                        <div class="small text-muted mt-1">Student spending trackers</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(239, 68, 68, 0.15); color: #dc2626;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; color: #0a1f12;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold small text-muted" style="letter-spacing: 0.5px;">Income Categories</div>
                        <div class="fs-2 fw-bolder mt-1 text-success">{{ $categories->where('type', 'income')->count() }}</div>
                        <div class="small text-muted mt-1">Stipend & cash inflow</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.15); color: #059669;">
                        <i class="bi bi-cash-coin fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-center align-items-start" style="background: linear-gradient(135deg, #05140A 0%, #0A1F12 100%); border: 1px solid rgba(201, 162, 39, 0.3) !important;">
                <div class="w-100 d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white fw-bold small text-uppercase">New Taxonomy</span>
                    <i class="bi bi-plus-circle text-gold fs-5"></i>
                </div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-warning w-100 fw-bold py-2 shadow-sm rounded-3" style="background: var(--gold); border: none; color: #05140a;">
                    <i class="bi bi-plus-circle me-1"></i> Add Default Category
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Category Table & Management Card -->
    <div class="admin-card category-management-card card shadow-sm mb-4 border-0">
        <!-- CRITICAL FIX: High Contrast Header with Prominent Category Classification & White Styling -->
        <div class="p-3 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: rgba(255, 255, 255, 0.08); border-bottom: 1px solid rgba(255, 255, 255, 0.15); border-radius: 14px 14px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-funnel-fill text-primary me-1"></i> Filter
                </span>
                <span class="fw-bold text-white fs-5" style="letter-spacing: -0.2px;">Category Classification:</span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="btn-group btn-group-sm shadow-sm" role="group">
                    <a href="{{ route('admin.categories') }}"
                       class="btn px-3 py-2 fw-semibold {{ !request('type') ? 'btn-light text-dark fw-bold' : 'btn-outline-light text-white' }}">
                        All Categories ({{ $categories->count() }})
                    </a>

                    <a href="{{ route('admin.categories', ['type' => 'expense']) }}"
                       class="btn px-3 py-2 fw-semibold {{ request('type') === 'expense' ? 'btn-danger text-white fw-bold' : 'btn-outline-light text-white' }}">
                        Expense ({{ $categories->where('type', 'expense')->count() }})
                    </a>

                    <a href="{{ route('admin.categories', ['type' => 'income']) }}"
                       class="btn px-3 py-2 fw-semibold {{ request('type') === 'income' ? 'btn-success text-white fw-bold' : 'btn-outline-light text-white' }}">
                        Income ({{ $categories->where('type', 'income')->count() }})
                    </a>
                </div>

                <div class="input-group input-group-sm ms-md-2" style="max-width: 220px;">
                    <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="categorySearchInput" class="form-control border-0 bg-white text-dark" placeholder="Search category...">
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="row g-4" id="categoryGrid">
                @forelse($categories as $category)
                    <div class="col-md-6 col-lg-4 category-card-item" data-name="{{ strtolower($category->name) }}" data-type="{{ strtolower($category->type) }}">
                        <div class="card h-100 rounded-4 shadow-sm border" style="background: rgba(5, 20, 10, 0.7); border-color: rgba(255, 255, 255, 0.16) !important; transition: transform 0.2s, box-shadow 0.2s;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-2 text-white" style="letter-spacing: -0.2px;">{{ $category->name }}</h5>
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            @if($category->type === 'income')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1 rounded-pill">
                                                    <i class="bi bi-arrow-down-left me-1"></i>Income
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold px-2 py-1 rounded-pill">
                                                    <i class="bi bi-arrow-up-right me-1"></i>Expense
                                                </span>
                                            @endif

                                            @if($category->is_default)
                                                <span class="badge bg-white text-dark fw-bold px-2 py-1 rounded-pill shadow-sm">
                                                    <i class="bi bi-shield-check me-1 text-primary"></i>Campus Default
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-light rounded-circle p-1"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                style="width: 32px; height: 32px;"
                                                title="Actions">
                                            <i class="bi bi-three-dots-vertical text-white"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow border" style="background: #0A1F12; border-color: rgba(255, 255, 255, 0.15) !important;">
                                            <li>
                                                <a href="{{ route('admin.categories.edit', $category) }}"
                                                   class="dropdown-item text-white">
                                                    <i class="bi bi-pencil me-2 text-warning"></i>Edit
                                                </a>
                                            </li>

                                            <li>
                                                <form action="{{ route('admin.categories.destroy', $category) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="pt-3 border-top d-flex justify-content-between align-items-center text-white-50 small" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                                    <span>Classification:</span>
                                    <span class="fw-semibold text-white">{{ ucfirst($category->type) }} Tag</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 rounded-4" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255, 255, 255, 0.15);">
                            <i class="bi bi-tags fs-1 text-white-50"></i>
                            <h5 class="text-white mt-3 fw-bold">No Categories Found</h5>
                            <p class="text-light opacity-75 small mb-3">Add standard university expense and income categories for students.</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-warning fw-bold px-4 rounded-pill" style="background: var(--gold); border: none; color: #05140a;">
                                <i class="bi bi-plus-circle me-1"></i> Add Category
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('categorySearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const items = document.querySelectorAll('.category-card-item');
                items.forEach(function (card) {
                    const name = card.getAttribute('data-name') || '';
                    if (name.includes(query)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection
