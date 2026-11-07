/**
 * Campus Coin - Transactions Management Module
 */

document.addEventListener('DOMContentLoaded', function() {
  CampusCoin.init('transactions');

  let currentTransactions = CampusDB.getTransactions();
  let filteredTransactions = [...currentTransactions];
  let currentPage = 1;
  const itemsPerPage = 8;
  let txToDeleteId = null;

  // Cache DOM Elements
  const searchInput = document.getElementById('searchTxInput');
  const typeFilter = document.getElementById('typeFilter');
  const categoryFilter = document.getElementById('categoryFilter');
  const sortSelect = document.getElementById('sortSelect');
  const tableBody = document.getElementById('transactionsTableBody');
  const mobileContainer = document.getElementById('mobileTransactionsList');
  const paginationContainer = document.getElementById('paginationControls');
  const countBadge = document.getElementById('txCountBadge');
  const deleteModalEl = document.getElementById('deleteConfirmModal');
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

  const deleteModal = deleteModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(deleteModalEl) : null;

  // Filter & Search Logic
  function applyFilters() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const selectedType = typeFilter ? typeFilter.value : 'all';
    const selectedCategory = categoryFilter ? categoryFilter.value : 'all';
    const sortVal = sortSelect ? sortSelect.value : 'date-desc';

    filteredTransactions = currentTransactions.filter(tx => {
      const matchSearch = tx.description.toLowerCase().includes(searchTerm) || 
                          tx.category.toLowerCase().includes(searchTerm) || 
                          tx.amount.toString().includes(searchTerm);
      const matchType = selectedType === 'all' || tx.type === selectedType;
      const matchCat = selectedCategory === 'all' || tx.category === selectedCategory;

      return matchSearch && matchType && matchCat;
    });

    // Sorting
    filteredTransactions.sort((a, b) => {
      if (sortVal === 'date-desc') return new Date(b.date) - new Date(a.date);
      if (sortVal === 'date-asc') return new Date(a.date) - new Date(b.date);
      if (sortVal === 'amount-desc') return b.amount - a.amount;
      if (sortVal === 'amount-asc') return a.amount - b.amount;
      return 0;
    });

    currentPage = 1;
    renderTransactions();
  }

  // Render Table & Mobile Cards
  function renderTransactions() {
    if (countBadge) {
      countBadge.innerText = `${filteredTransactions.length} Total`;
    }

    const totalPages = Math.ceil(filteredTransactions.length / itemsPerPage) || 1;
    const startIndex = (currentPage - 1) * itemsPerPage;
    const pageItems = filteredTransactions.slice(startIndex, startIndex + itemsPerPage);

    if (pageItems.length === 0) {
      const emptyHtml = `
        <tr>
          <td colspan="6" class="text-center py-5 text-muted">
            <i class="bi bi-wallet2 fs-1 d-block mb-2 text-secondary opacity-50"></i>
            <h6>No transactions found</h6>
            <p class="small">Try adjusting your search criteria or add a new transaction.</p>
          </td>
        </tr>
      `;
      if (tableBody) tableBody.innerHTML = emptyHtml;
      if (mobileContainer) mobileContainer.innerHTML = '<div class="text-center py-4 text-muted">No transactions found.</div>';
      if (paginationContainer) paginationContainer.innerHTML = '';
      return;
    }

    // Category Icon Helper
    const catIcons = {
      'Food': 'bi-cup-hot text-warning',
      'Transport': 'bi-bus-front text-info',
      'Hostel/Rent': 'bi-building text-secondary',
      'Academics': 'bi-book text-primary',
      'Subscriptions': 'bi-play-btn text-danger',
      'Entertainment': 'bi-controller text-purple',
      'Allowance': 'bi-cash-stack text-success',
      'Part-time Job': 'bi-briefcase text-primary',
      'Scholarship': 'bi-mortarboard text-info',
      'Gift': 'bi-gift text-pink',
      'Miscellaneous': 'bi-three-dots text-secondary'
    };

    // Desktop Table HTML
    if (tableBody) {
      tableBody.innerHTML = pageItems.map(tx => {
        const isIncome = tx.type === 'income';
        const iconClass = catIcons[tx.category] || 'bi-tag text-secondary';
        return `
          <tr>
            <td class="text-nowrap fw-medium text-muted">${CampusCoin.formatDate(tx.date)}</td>
            <td>
              <div class="fw-bold text-main">${tx.description}</div>
              ${tx.recurring ? `<span class="badge bg-light text-muted border" style="font-size: 0.7rem;"><i class="bi bi-arrow-repeat me-1"></i>${tx.frequency || 'Recurring'}</span>` : ''}
            </td>
            <td>
              <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                <i class="bi ${iconClass}"></i> ${tx.category}
              </span>
            </td>
            <td>
              <span class="badge ${isIncome ? 'badge-income' : 'badge-expense'} badge-student">
                ${isIncome ? 'Income' : 'Expense'}
              </span>
            </td>
            <td class="fw-bold ${isIncome ? 'text-success' : 'text-danger'} text-nowrap">
              ${isIncome ? '+' : '-'}${CampusCoin.formatPKR(tx.amount)}
            </td>
            <td>
              <div class="btn-group btn-group-sm">
                <a href="/edit-transaction?id=${tx.id}" class="btn btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <button class="btn btn-outline-danger btn-delete-tx" data-id="${tx.id}" data-desc="${tx.description}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    // Mobile Responsive Cards
    if (mobileContainer) {
      mobileContainer.innerHTML = pageItems.map(tx => {
        const isIncome = tx.type === 'income';
        const iconClass = catIcons[tx.category] || 'bi-tag text-secondary';
        return `
          <div class="card mb-3 p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div class="d-flex align-items-center gap-2">
                <div class="p-2 rounded bg-light border">
                  <i class="bi ${iconClass} fs-5"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">${tx.description}</h6>
                  <span class="small text-muted">${CampusCoin.formatDate(tx.date)} • ${tx.category}</span>
                </div>
              </div>
              <span class="fw-bold fs-6 ${isIncome ? 'text-success' : 'text-danger'}">
                ${isIncome ? '+' : '-'}${CampusCoin.formatPKR(tx.amount)}
              </span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
              <span class="badge ${isIncome ? 'badge-income' : 'badge-expense'} badge-student">${isIncome ? 'Income' : 'Expense'}</span>
              <div class="btn-group btn-group-sm">
                <a href="/edit-transaction?id=${tx.id}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <button class="btn btn-sm btn-outline-danger btn-delete-tx" data-id="${tx.id}" data-desc="${tx.description}">Delete</button>
              </div>
            </div>
          </div>
        `;
      }).join('');
    }

    // Bind Delete Buttons
    document.querySelectorAll('.btn-delete-tx').forEach(btn => {
      btn.onclick = function() {
        txToDeleteId = this.getAttribute('data-id');
        const desc = this.getAttribute('data-desc');
        const targetDescEl = document.getElementById('deleteItemDesc');
        if (targetDescEl) targetDescEl.innerText = desc;
        if (deleteModal) deleteModal.show();
      };
    });

    renderPagination(totalPages);
  }

  // Pagination Renderer
  function renderPagination(totalPages) {
    if (!paginationContainer) return;
    if (totalPages <= 1) {
      paginationContainer.innerHTML = '';
      return;
    }

    let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
    </li>`;

    for (let i = 1; i <= totalPages; i++) {
      html += `<li class="page-item ${currentPage === i ? 'active' : ''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>`;
    }

    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
    </li>`;

    paginationContainer.innerHTML = html;

    paginationContainer.querySelectorAll('.page-link').forEach(link => {
      link.onclick = function(e) {
        e.preventDefault();
        const page = parseInt(this.getAttribute('data-page'));
        if (page >= 1 && page <= totalPages && page !== currentPage) {
          currentPage = page;
          renderTransactions();
          window.scrollTo({ top: 100, behavior: 'smooth' });
        }
      };
    });
  }

  // Handle Delete Confirmation
  if (confirmDeleteBtn) {
    confirmDeleteBtn.onclick = function() {
      if (txToDeleteId) {
        CampusDB.deleteTransaction(txToDeleteId);
        currentTransactions = CampusDB.getTransactions();
        CampusCoin.showToast('Transaction removed successfully', 'danger');
        if (deleteModal) deleteModal.hide();
        applyFilters();
      }
    };
  }

  // Event Listeners for Filters
  if (searchInput) searchInput.addEventListener('input', applyFilters);
  if (typeFilter) typeFilter.addEventListener('change', applyFilters);
  if (categoryFilter) categoryFilter.addEventListener('change', applyFilters);
  if (sortSelect) sortSelect.addEventListener('change', applyFilters);

  // Initial display
  applyFilters();
});
