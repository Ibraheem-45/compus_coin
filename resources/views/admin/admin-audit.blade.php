@extends('admin.master')


@section('content')
<!-- Explanation Banner -->
        <div class="alert alert-warning border-0 bg-warning-subtle text-dark shadow-sm mb-4 d-flex align-items-center gap-3">
          <i class="bi bi-shield-exclamation fs-3 text-warning"></i>
          <div>
            <strong>Automated Financial Integrity Monitoring</strong>
            <p class="mb-0 small text-muted">Transactions exceeding PKR 10,000 or duplicate canteen charges logged within a 5-minute timeframe are automatically flagged for administrator review.</p>
          </div>
        </div>

        <div class="admin-card card shadow-sm mb-4">
          <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
              <h5 class="fw-bold mb-0 text-main"><i class="bi bi-flag-fill text-warning me-2"></i>Flagged Transaction Watchlist</h5>
              <small class="text-muted">Review suspected student entries and approve or dismiss the flag.</small>
            </div>
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
              <i class="bi bi-eye-fill me-1"></i> Heuristic Audit Active
            </span>
          </div>

          <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Flag ID</th>
                  <th>Student Info</th>
                  <th>Transaction Note</th>
                  <th>Amount</th>
                  <th>Anomaly Reason</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th style="width: 170px;">Admin Action</th>
                </tr>
              </thead>
              <tbody id="auditTableBody">
                <!-- Dynamically populated by js/admin.js -->
              </tbody>
            </table>
          </div>
        </div>
@endsection

