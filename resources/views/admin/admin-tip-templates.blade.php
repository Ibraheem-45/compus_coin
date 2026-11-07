@extends('admin.master')

@section('content')
<div class="container-fluid px-0">
    <!-- Top Action & Overview -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; color: #0a1f12;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold small text-muted" style="letter-spacing: 0.5px;">Total Templates</div>
                        <div class="fs-2 fw-bolder mt-1 text-dark">{{ $templates->count() }}</div>
                        <div class="small text-success fw-semibold mt-1"><i class="bi bi-broadcast me-1"></i>Live for students</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.15); color: #059669;">
                        <i class="bi bi-lightbulb-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; color: #0a1f12;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold small text-muted" style="letter-spacing: 0.5px;">Max Potential Saving</div>
                        <div class="fs-2 fw-bolder mt-1 text-dark">
                            <span data-amount="{{ $templates->max('impact_score') ?? 0 }}" class="currency-value">PKR {{ number_format($templates->max('impact_score') ?? 0) }}</span>
                        </div>
                        <div class="small text-muted mt-1">Per student impact</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(201, 162, 39, 0.15); color: #b45309;">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; color: #0a1f12;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fw-bold small text-muted" style="letter-spacing: 0.5px;">Distinct Topics</div>
                        <div class="fs-2 fw-bolder mt-1 text-dark">{{ $templates->pluck('category')->unique()->count() }}</div>
                        <div class="small text-primary fw-semibold mt-1"><i class="bi bi-tag-fill me-1"></i>Curated disciplines</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(59, 130, 246, 0.15); color: #2563eb;">
                        <i class="bi bi-collection-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-center align-items-start" style="background: linear-gradient(135deg, #05140A 0%, #0A1F12 100%); border: 1px solid rgba(201, 162, 39, 0.3) !important;">
                <div class="w-100 d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white fw-bold small text-uppercase">New Campaign</span>
                    <i class="bi bi-plus-circle text-gold fs-5"></i>
                </div>
                <button type="button" class="btn btn-warning w-100 fw-bold py-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#createTipModal" style="background: var(--gold); border: none; color: #05140a;">
                    <i class="bi bi-plus-lg me-1"></i> Publish New Tip
                </button>
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
            <strong>Please resolve the following issues:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Content Panel -->
    <div class="admin-card card shadow-sm mb-4 border-0">
        <!-- High Contrast Header Bar with White Text -->
        <div class="p-3 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: rgba(255, 255, 255, 0.06); border-bottom: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px 12px 0 0;">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> Library Catalog
                </span>
                <span class="fw-bold text-white fs-5">Financial Nudge Templates</span>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="max-width: 260px;">
                    <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="tipSearchInput" class="form-control border-0 bg-white text-dark" placeholder="Search templates...">
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="row g-4" id="tipGrid">
                @forelse($templates as $tip)
                    <div class="col-md-6 col-xl-4 tip-card-item" data-title="{{ strtolower($tip->title) }}" data-category="{{ strtolower($tip->category ?? '') }}">
                        <div class="card h-100 rounded-4 shadow-sm border" style="background: rgba(5, 20, 10, 0.7); border-color: rgba(255, 255, 255, 0.16) !important; transition: transform 0.2s, box-shadow 0.2s;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge bg-white text-dark fw-bold px-2 py-1 shadow-sm rounded-pill">
                                            <i class="bi bi-tag-fill me-1 text-primary"></i>{{ $tip->category ?? 'General' }}
                                        </span>
                                        <div class="d-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-light px-2 py-1 rounded-2" data-bs-toggle="modal" data-bs-target="#editTipModal{{ $tip->tip_id }}" title="Edit Template">
                                                <i class="bi bi-pencil-square text-warning"></i>
                                            </button>
                                            <form action="{{ route('admin.tip-templates.destroy', $tip->tip_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tip template?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-2" title="Delete Template">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <h5 class="fw-bold text-white mb-2" style="line-height: 1.35;">{{ $tip->title }}</h5>
                                    <p class="text-light opacity-75 small mb-3" style="line-height: 1.6;">{{ $tip->content }}</p>
                                </div>

                                <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                                    <span class="small text-white-50">Est. Monthly Savings:</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold fs-6 px-3 py-1 rounded-pill">
                                        <span data-amount="{{ $tip->impact_score }}" class="currency-value">+PKR {{ number_format($tip->impact_score) }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editTipModal{{ $tip->tip_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border shadow" style="background: #0A1F12; color: #fff; border-color: rgba(201, 162, 39, 0.4) !important;">
                                    <div class="modal-header border-bottom" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                                        <h5 class="modal-title fw-bold text-white"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Template</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.tip-templates.update', $tip->tip_id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-white small">Title / Recommendation</label>
                                                <input type="text" name="title" class="form-control bg-dark text-white border-secondary" value="{{ old('title', $tip->title) }}" required>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label fw-bold text-white small">Category</label>
                                                    <input type="text" name="category" class="form-control bg-dark text-white border-secondary" value="{{ old('category', $tip->category) }}" placeholder="e.g. Food, Books" required>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label fw-bold text-white small">Potential Savings (PKR)</label>
                                                    <input type="number" name="impact_score" class="form-control bg-dark text-white border-secondary" value="{{ old('impact_score', $tip->impact_score) }}" min="0" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-white small">Guidance & Advice Content</label>
                                                <textarea name="content" rows="4" class="form-control bg-dark text-white border-secondary" required>{{ old('content', $tip->content) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                                            <button type="button" class="btn btn-outline-light px-3" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-warning fw-bold px-4" style="background: var(--gold); border: none; color: #05140a;">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 rounded-4" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255, 255, 255, 0.15);">
                            <i class="bi bi-lightbulb fs-1 text-white-50"></i>
                            <h5 class="text-white mt-3 fw-bold">No Saving Tip Templates Yet</h5>
                            <p class="text-light opacity-75 small mb-3">Publish your first financial nudge card to help students optimize their daily allowance.</p>
                            <button type="button" class="btn btn-warning fw-bold px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#createTipModal" style="background: var(--gold); border: none; color: #05140a;">
                                <i class="bi bi-plus-lg me-1"></i> Create First Template
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createTipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border shadow" style="background: #0A1F12; color: #fff; border-color: rgba(201, 162, 39, 0.4) !important;">
            <div class="modal-header border-bottom" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-plus-circle text-warning me-2"></i>Publish Saving Tip Template</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.tip-templates.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-white small">Title / Recommendation</label>
                        <input type="text" name="title" class="form-control bg-dark text-white border-secondary" placeholder="e.g. Cook Weekend Meals in Campus Hostel" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold text-white small">Category</label>
                            <input type="text" name="category" class="form-control bg-dark text-white border-secondary" placeholder="e.g. Food & Dining" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold text-white small">Potential Savings (PKR)</label>
                            <input type="number" name="impact_score" class="form-control bg-dark text-white border-secondary" placeholder="2500" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-white small">Guidance & Advice Content</label>
                        <textarea name="content" rows="4" class="form-control bg-dark text-white border-secondary" placeholder="Explain how students can implement this practical saving hack..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                    <button type="button" class="btn btn-outline-light px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4" style="background: var(--gold); border: none; color: #05140a;">Publish to Students</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('tipSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const items = document.querySelectorAll('.tip-card-item');
                items.forEach(function (card) {
                    const title = card.getAttribute('data-title') || '';
                    const category = card.getAttribute('data-category') || '';
                    if (title.includes(query) || category.includes(query)) {
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
