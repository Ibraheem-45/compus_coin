/**
 * Campus Coin - Budget Management Module
 */

document.addEventListener('DOMContentLoaded', function() {
  CampusCoin.init('budgets');

  const budgetGrid = document.getElementById('budgetCardsGrid');
  const budgetModalEl = document.getElementById('budgetModal');
  const budgetForm = document.getElementById('budgetForm');
  const modalTitle = document.getElementById('budgetModalTitle');
  const categorySelect = document.getElementById('budgetCategory');
  const amountInput = document.getElementById('budgetLimit');

  const budgetModal = budgetModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(budgetModalEl) : null;

  function loadBudgets() {
    const budgets = CampusDB.getBudgets();
    const transactions = CampusDB.getTransactions();

    // Sum September 2026 expenses per category
    const categorySpending = {};
    transactions.forEach(t => {
      if (t.type === 'expense' && t.date && t.date.startsWith('2026-09')) {
        categorySpending[t.category] = (categorySpending[t.category] || 0) + parseFloat(t.amount || 0);
      }
    });

    // Populate category dropdown in modal
    if (categorySelect) {
      const cats = CampusDB.getCategories().expense;
      categorySelect.innerHTML = cats.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
    }

    if (!budgetGrid) return;

    budgetGrid.innerHTML = budgets.map(b => {
      const spent = categorySpending[b.category] || 0;
      const limit = b.limit || 1;
      const remaining = limit - spent;
      const percentage = Math.min(Math.round((spent / limit) * 100), 100);

      let statusClass = 'status-on-track';
      let statusLabel = 'On Track';
      let progressClass = 'bg-success';

      if (spent >= limit) {
        statusClass = 'status-over-budget';
        statusLabel = 'Over Budget';
        progressClass = 'bg-danger';
      } else if (percentage >= 75) {
        statusClass = 'status-near-limit';
        statusLabel = 'Near Limit';
        progressClass = 'bg-warning';
      }

      const catIcons = {
        'Food': 'bi-cup-hot text-warning',
        'Transport': 'bi-bus-front text-info',
        'Hostel/Rent': 'bi-building text-secondary',
        'Academics': 'bi-book text-primary',
        'Subscriptions': 'bi-play-btn text-danger',
        'Entertainment': 'bi-controller text-purple',
        'Miscellaneous': 'bi-three-dots text-secondary'
      };

      const iconClass = catIcons[b.category] || 'bi-tag text-teal';

      return `
        <div class="col-md-6 col-xl-4 mb-4">
          <div class="card budget-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="p-2 rounded bg-light border">
                  <i class="bi ${iconClass} fs-5"></i>
                </div>
                <h6 class="mb-0 fw-bold">${b.category}</h6>
              </div>
              <span class="budget-status-pill ${statusClass}">${statusLabel}</span>
            </div>

            <div class="mb-3">
              <div class="d-flex justify-content-between small text-muted mb-1">
                <span>Spent: <strong>${CampusCoin.formatPKR(spent)}</strong></span>
                <span>Budget: <strong>${CampusCoin.formatPKR(limit)}</strong></span>
              </div>
              <div class="progress" style="height: 10px; border-radius: 5px;">
                <div class="progress-bar ${progressClass}" role="progressbar" style="width: ${percentage}%" aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
              <span class="small ${remaining < 0 ? 'text-danger fw-bold' : 'text-muted'}">
                ${remaining < 0 ? `Over by ${CampusCoin.formatPKR(Math.abs(remaining))}` : `Remaining: ${CampusCoin.formatPKR(remaining)}`}
              </span>
              <button class="btn btn-sm btn-outline-primary btn-edit-budget" data-category="${b.category}" data-limit="${limit}">
                <i class="bi bi-pencil me-1"></i> Edit
              </button>
            </div>
          </div>
        </div>
      `;
    }).join('');

    // Bind edit buttons
    document.querySelectorAll('.btn-edit-budget').forEach(btn => {
      btn.onclick = function() {
        const cat = this.getAttribute('data-category');
        const lim = this.getAttribute('data-limit');
        if (modalTitle) modalTitle.innerText = `Edit Budget: ${cat}`;
        if (categorySelect) categorySelect.value = cat;
        if (amountInput) amountInput.value = lim;
        if (budgetModal) budgetModal.show();
      };
    });
  }

  // Handle set new budget button
  const setBudgetBtn = document.getElementById('openNewBudgetModalBtn');
  if (setBudgetBtn) {
    setBudgetBtn.onclick = function() {
      if (modalTitle) modalTitle.innerText = 'Set Category Budget';
      if (budgetForm) budgetForm.reset();
      if (budgetModal) budgetModal.show();
    };
  }

  // Form submission
  if (budgetForm) {
    budgetForm.onsubmit = function(e) {
      e.preventDefault();
      const cat = categorySelect.value;
      const amt = parseFloat(amountInput.value);

      if (!cat || isNaN(amt) || amt <= 0) {
        CampusCoin.showToast('Please enter a valid budget amount', 'danger');
        return;
      }

      CampusDB.updateBudget(cat, amt);
      CampusCoin.showToast(`Budget for ${cat} updated to ${CampusCoin.formatPKR(amt)}`, 'success');
      if (budgetModal) budgetModal.hide();
      loadBudgets();
    };
  }

  loadBudgets();
  window.addEventListener('cc:currency-change', () => loadBudgets());
});
