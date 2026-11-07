@extends('student.master')


@section('content')
  @if (session('success'))
    <div class="alert alert-success" role="status">{{ session('success') }}</div>
  @endif

  <section class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-pin-angle-fill text-warning fs-5"></i>
        <h5 class="fw-bold mb-0">Pinned Saving Tips</h5>
      </div>
      <a href="/saving-tips" class="small text-decoration-none">Find More Tips</a>
    </div>
    <div class="row">
      @forelse ($pinnedTips as $bookmark)
        @php($tip = $bookmark['item'])
        <div class="col-md-6 col-lg-4 mb-3">
          <article class="card h-100 p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge bg-light text-dark border">{{ $tip['category'] ?? 'Savings' }}</span>
              @if (isset($tip['potentialSavings']))
                <span class="tip-savings-badge">Save {{ number_format((float) $tip['potentialSavings']) }}/mo</span>
              @endif
            </div>
            <h6 class="fw-bold mb-2">{{ $tip['title'] ?? 'Saved saving tip' }}</h6>
            <p class="text-muted small flex-grow-1">{{ $tip['description'] ?? '' }}</p>
            <form method="POST" action="{{ route('student.bookmarks.destroy', $bookmark['bookmark_id']) }}" class="pt-2 border-top mt-auto">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-x-circle me-1"></i> Remove</button>
            </form>
          </article>
        </div>
      @empty
        <div class="col-12 text-muted py-3">No pinned tips saved yet. Explore <a href="/saving-tips">Saving Tips</a> for recommendations based on your transactions.</div>
      @endforelse
    </div>
  </section>

  <section class="mb-5">
    <div class="d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-lightbulb-fill text-primary fs-5"></i>
      <h5 class="fw-bold mb-0">Saved Spending Insights</h5>
    </div>
    <div class="row">
      @forelse ($pinnedInsights as $bookmark)
        @php($insight = $bookmark['item'])
        <div class="col-md-6 col-lg-4 mb-3">
          <article class="card h-100 p-3 shadow-sm">
            <span class="badge bg-light text-dark border align-self-start mb-2">{{ $insight['category'] ?? 'Finance' }} · {{ $insight['month'] ?? '' }}</span>
            <h6 class="fw-bold mb-2">{{ $insight['pattern'] ?? 'Saved spending insight' }}</h6>
            <p class="text-muted small">{{ $insight['explanation'] ?? '' }}</p>
            <p class="small mb-3"><strong>Suggested action:</strong> {{ $insight['suggestedAction'] ?? '' }}</p>
            <form method="POST" action="{{ route('student.bookmarks.destroy', $bookmark['bookmark_id']) }}" class="pt-2 border-top mt-auto">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-x-circle me-1"></i> Remove</button>
            </form>
          </article>
        </div>
      @empty
        <div class="col-12 text-muted py-3">No spending insights saved yet. Refresh your insights from <a href="/saving-tips">Saving Tips</a>.</div>
      @endforelse
    </div>
  </section>

@endsection
