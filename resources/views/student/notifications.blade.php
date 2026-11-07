@extends('student.master')



@section('content')
<div class="d-flex justify-content-end mb-3">
  <button type="button" class="btn btn-sm btn-outline-primary" id="markAllReadBtn" @disabled(!$notifications->contains(fn ($notification) => !$notification['read']))>
    <i class="bi bi-check2-all me-1"></i> Mark all as read
  </button>
</div>
<div class="row">
          <div class="col-lg-8 mx-auto" id="notificationsList">
            @if ($notifications->isEmpty())
              <div class="text-center py-5 text-muted">No notifications yet. Budget alerts and campus notices will appear here.</div>
            @endif
          </div>
        </div>
@endsection

@push('scripts')
  <script>
    window.studentNotifications = @json($notifications ?? []);
  </script>
  <script src="{{ asset('js/student/notifications.js') }}"></script>
@endpush
