/**
 * Campus Coin - Custom Categories Management Module
 */

document.addEventListener('DOMContentLoaded', function() {
  CampusCoin.init('categories');

  const incomeGrid = document.getElementById('incomeCategoriesGrid');
  const expenseGrid = document.getElementById('expenseCategoriesGrid');
  const modalEl = document.getElementById('categoryModal');
  const form = document.getElementById('categoryForm');
  const modalTitle = document.getElementById('categoryModalTitle');
  const typeSelect = document.getElementById('categoryType');
  const nameInput = document.getElementById('categoryName');
  const iconSelect = document.getElementById('categoryIcon');
  const colorInput = document.getElementById('categoryColor');

  const catModal = modalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(modalEl) : null;

  function renderCategories() {
    const cats = CampusDB.getCategories();

    // Render Income
    if (incomeGrid) {
      incomeGrid.innerHTML = cats.income.map(c => `
        <div class="col-md-6 col-lg-4 mb-3">
          <div class="card p-3 d-flex flex-row align-items-center justify-content-between shadow-sm">
            <div class="d-flex align-items-center gap-3">
              <div class="category-avatar" style="background-color: ${c.color}20; color: ${c.color};">
                <i class="bi ${c.icon || 'bi-tag'}"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">${c.name}</h6>
                <small class="text-muted">Income Source</small>
              </div>
            </div>
            <button class="btn btn-sm btn-outline-danger btn-delete-cat" data-type="income" data-id="${c.id}" data-name="${c.name}">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      `).join('');
    }

    // Render Expense
    if (expenseGrid) {
      expenseGrid.innerHTML = cats.expense.map(c => `
        <div class="col-md-6 col-lg-4 mb-3">
          <div class="card p-3 d-flex flex-row align-items-center justify-content-between shadow-sm">
            <div class="d-flex align-items-center gap-3">
              <div class="category-avatar" style="background-color: ${c.color}20; color: ${c.color};">
                <i class="bi ${c.icon || 'bi-tag'}"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">${c.name}</h6>
                <small class="text-muted">Expense Category</small>
              </div>
            </div>
            <button class="btn btn-sm btn-outline-danger btn-delete-cat" data-type="expense" data-id="${c.id}" data-name="${c.name}">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      `).join('');
    }

    // Delete handlers
    document.querySelectorAll('.btn-delete-cat').forEach(btn => {
      btn.onclick = function() {
        const type = this.getAttribute('data-type');
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        if (confirm(`Are you sure you want to delete category "${name}"?`)) {
          CampusDB.deleteCategory(type, id);
          CampusCoin.showToast(`Category "${name}" removed`, 'info');
          renderCategories();
        }
      };
    });
  }

  // Add Category Button
  const addBtn = document.getElementById('openAddCategoryModalBtn');
  if (addBtn) {
    addBtn.onclick = function() {
      if (modalTitle) modalTitle.innerText = 'Add New Category';
      if (form) form.reset();
      if (catModal) catModal.show();
    };
  }

  // Handle Form Submit
  if (form) {
    form.onsubmit = function(e) {
      e.preventDefault();
      const type = typeSelect.value;
      const name = nameInput.value.trim();
      const icon = iconSelect.value;
      const color = colorInput.value;

      if (!name) {
        CampusCoin.showToast('Please provide a category name', 'danger');
        return;
      }

      CampusDB.addCategory(type, { name, icon, color });
      CampusCoin.showToast(`Category "${name}" created!`, 'success');
      if (catModal) catModal.hide();
      renderCategories();
    };
  }

  renderCategories();
});
