@extends('admin.master')



@section('content')
<div class="row g-4">
          <!-- Left: Broadcast Form & Feed -->
          <div class="col-lg-6">
            <div class="admin-card card h-100 shadow-sm">
              <div class="card-header bg-transparent">
                <span class="fw-bold text-main fs-5"><i class="bi bi-megaphone-fill text-warning me-2"></i>Post Campus-Wide Advisory</span>
                <p class="text-muted small mb-0">Broadcasts instant banner alert to all student home dashboards.</p>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Notice Title</label>
                  <input type="text" class="form-control" id="annTitleInput" placeholder="e.g. Hostel Mess Dues Deadline Reminder">
                </div>
                <div class="row g-2 mb-3">
                  <div class="col-sm-6">
                    <label class="form-label small fw-semibold">Priority Level</label>
                    <select class="form-select" id="annPrioritySelect">
                      <option value="Normal">Normal Priority</option>
                      <option value="High" selected>High Priority</option>
                      <option value="Urgent">Urgent Notice</option>
                    </select>
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label small fw-semibold">Issuing Office</label>
                    <input type="text" class="form-control" id="annAuthorInput" placeholder="e.g. Student Affairs" value="Student Affairs Center">
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Broadcast Message</label>
                  <textarea class="form-control" id="annMessageInput" rows="3" placeholder="Enter notice details visible to all students..."></textarea>
                </div>
                <button type="button" class="btn btn-primary w-100 fw-semibold" id="postAnnouncementBtn">
                  <i class="bi bi-send-fill me-1"></i> Broadcast Announcement
                </button>

                <hr class="my-4">

                <h6 class="fw-bold mb-3 small text-uppercase text-muted">Active Campus Broadcasts</h6>
                <div id="announcementsFeed">
                  <!-- Populated dynamically by js/admin.js -->
                </div>
              </div>
            </div>
          </div>

          <!-- Right: Saving Tip Templates -->
          <div class="col-lg-6">
            <div class="admin-card card h-100 shadow-sm">
              <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                  <span class="fw-bold text-main fs-5"><i class="bi bi-lightbulb-fill text-success me-2"></i>Saving Tip Templates</span>
                  <p class="text-muted small mb-0">Pre-seeded advice available across student saving feeds.</p>
                </div>
                <button id="newTipTemplateBtn" type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTipTemplateModal">
                  <i class="bi bi-plus-circle me-1"></i> New Tip Template
                </button>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table admin-table align-middle mb-0 admin-tip-template-table">
                    <thead class="table-light">
                      <tr>
                        <th>Tip Headline</th>
                        <th>Category</th>
                        <th>Est. Savings</th>
                        <th>Actionable Advice</th>
                        <th style="width: 110px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody id="tipTemplatesTbody">
                      <!-- Populated dynamically by js/admin.js -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

<div class="modal fade" id="addTipTemplateModal" tabindex="-1" aria-labelledby="tipTemplateModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="addTipTemplateForm">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="tipTemplateModalTitle">New Saving Tip Template</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="tipTemplateId">
          <div class="mb-3">
            <label for="tipTitleInput" class="form-label">Tip headline</label>
            <input id="tipTitleInput" class="form-control" type="text" maxlength="180" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-sm-7">
              <label for="tipCategorySelect" class="form-label">Category</label>
              <select id="tipCategorySelect" class="form-select" required>
                <option>Food</option>
                <option>Transport</option>
                <option>Hostel/Rent</option>
                <option>Academics</option>
                <option>Subscriptions</option>
                <option>Entertainment</option>
                <option>Miscellaneous</option>
                <option>General</option>
              </select>
            </div>
            <div class="col-sm-5">
              <label for="tipSavingInput" class="form-label">Estimated monthly savings (PKR)</label>
              <input id="tipSavingInput" class="form-control" type="number" min="0" step="1" required>
            </div>
          </div>
          <div>
            <label for="tipTextInput" class="form-label">Actionable advice</label>
            <textarea id="tipTextInput" class="form-control" rows="4" maxlength="2000" required></textarea>
          </div>
          <div id="tipTemplateError" class="text-danger small mt-3" role="alert"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button id="saveTipTemplateBtn" type="submit" class="btn btn-primary">Save Template</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

