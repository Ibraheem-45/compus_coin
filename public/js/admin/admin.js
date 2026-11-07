document.addEventListener('DOMContentLoaded', function() {
  let adminData = CampusDB.getAdminData();

  CampusCoin.initTheme();
  CampusCoin.initFontSize();
  CampusCoin.setupMobileNav();

  const adminSidebar = document.getElementById('appSidebar');
  const closeSidebarBtn = document.getElementById('closeSidebarBtn');

  function closeMobileSidebar() {
    if (adminSidebar) adminSidebar.classList.remove('show-mobile');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (backdrop) backdrop.classList.remove('show');
  }

  if (closeSidebarBtn) {
    closeSidebarBtn.onclick = closeMobileSidebar;
  }

  document.querySelectorAll('#appSidebar .sidebar-link').forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth < 992) {
        closeMobileSidebar();
      }

      document.querySelectorAll('#appSidebar .sidebar-link').forEach(l => l.classList.remove('active'));
      this.classList.add('active');
    });
  });

  function updateKPIs() {
    adminData = CampusDB.getAdminData();

    const totalStudentsEl = document.getElementById('adminTotalStudents');
    const activeUsersEl = document.getElementById('adminActiveUsers');
    const totalTxEl = document.getElementById('adminTotalTx');
    const topCategoryEl = document.getElementById('adminTopCategory');
    const totalVolumeEl = document.getElementById('adminTotalVolume');

    if (totalStudentsEl) totalStudentsEl.innerText = adminData.totalStudents.toLocaleString();
    if (activeUsersEl) activeUsersEl.innerText = adminData.activeUsers.toLocaleString();
    if (totalTxEl) totalTxEl.innerText = adminData.totalTransactions.toLocaleString();
    if (topCategoryEl) topCategoryEl.innerText = adminData.mostUsedCategory || 'Food';
    if (totalVolumeEl) totalVolumeEl.innerText = 'PKR 4,820,000';

    const userBadge = document.getElementById('sidebarUserCount');
    const catBadge = document.getElementById('sidebarCatCount');
    const auditBadge = document.getElementById('sidebarAuditCount');

    if (userBadge) userBadge.innerText = window.adminSidebarStudentCount ?? 0;

    if (catBadge) catBadge.innerText = window.adminSidebarCategoryCount ?? 0;

    if (auditBadge) {
      const pendingFlags = adminData.flaggedTransactions.filter(f =>
        f.status.includes('Pending') || f.status.includes('Flagged')
      ).length;

      auditBadge.innerText = pendingFlags + ' Flagged';
      auditBadge.className = pendingFlags > 0
        ? 'badge bg-warning text-dark sidebar-badge'
        : 'badge bg-success-subtle text-success sidebar-badge';
    }
  }

  let growthChartInstance = null;
  let categoryChartInstance = null;
  let forecastChartInstance = null;

  function renderAdminCharts() {
    if (typeof Chart === 'undefined') return;

    const growthCtx = document.getElementById('adminUserGrowthChart');

    if (growthCtx) {
      if (growthChartInstance) growthChartInstance.destroy();

      growthChartInstance = new Chart(growthCtx, {
        type: 'line',
        data: {
          labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep 2026'],
          datasets: [{
            label: 'Active Students',
            data: [620, 710, 780, 830, 890, 940],
            borderColor: '#0d9488',
            backgroundColor: 'rgba(13, 148, 136, 0.12)',
            fill: true,
            tension: 0.35,
            borderWidth: 2.5,
            pointBackgroundColor: '#0d9488',
            pointRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              grid: {
                color: 'rgba(0,0,0,0.05)'
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    }

    const catCtx = document.getElementById('adminCategoryUsageChart');

    if (catCtx) {
      if (categoryChartInstance) categoryChartInstance.destroy();

      categoryChartInstance = new Chart(catCtx, {
        type: 'bar',
        data: {
          labels: ['Food', 'Transport', 'Academics', 'Entertainment', 'Rent', 'Subscriptions'],
          datasets: [{
            label: 'Monthly Volume (PKR)',
            data: [185000, 112000, 78000, 64000, 32000, 19500],
            backgroundColor: [
              '#0d9488',
              '#0284c7',
              '#8b5cf6',
              '#f59e0b',
              '#ef4444',
              '#10b981'
            ],
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              grid: {
                color: 'rgba(0,0,0,0.05)'
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    }

    const forecastCtx = document.getElementById('adminForecastChart');

    if (forecastCtx) {
      if (forecastChartInstance) forecastChartInstance.destroy();

      forecastChartInstance = new Chart(forecastCtx, {
        type: 'line',
        data: {
          labels: ['Jun', 'Jul', 'Aug', 'Sep (Actual)', 'Oct (Projected)', 'Nov (Projected)'],
          datasets: [
            {
              label: 'Actual Volume',
              data: [310000, 345000, 420000, 482000, null, null],
              borderColor: '#0d9488',
              backgroundColor: '#0d9488',
              tension: 0.3,
              borderWidth: 2.5
            },
            {
              label: 'Trend Forecast',
              data: [null, null, null, 482000, 525000, 560000],
              borderColor: '#f59e0b',
              borderDash: [6, 6],
              backgroundColor: 'rgba(245, 158, 11, 0.08)',
              fill: true,
              tension: 0.3,
              borderWidth: 2.5,
              pointStyle: 'rectRot'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          },
          scales: {
            y: {
              grid: {
                color: 'rgba(0,0,0,0.05)'
              },
              ticks: {
                callback: v => 'PKR ' + (v / 1000) + 'k'
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    }
  }

  function renderUsersTable() {
    adminData = CampusDB.getAdminData();

    const tbody = document.getElementById('adminUsersTableBody');

    if (!tbody) return;

    const searchTerm = (document.getElementById('userSearchInput')?.value || '').toLowerCase().trim();
    const statusFilter = document.getElementById('userStatusFilter')?.value || 'all';
    const yearFilter = document.getElementById('userYearFilter')?.value || 'all';

    const filtered = adminData.users.filter(u => {
      const matchSearch =
        u.name.toLowerCase().includes(searchTerm) ||
        u.email.toLowerCase().includes(searchTerm) ||
        (u.campus && u.campus.toLowerCase().includes(searchTerm));

      const matchStatus = statusFilter === 'all' || u.status === statusFilter;
      const matchYear = yearFilter === 'all' || (u.year && u.year.includes(yearFilter));

      return matchSearch && matchStatus && matchYear;
    });

    if (!filtered.length) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No student accounts matched your search criteria.</td></tr>`;
      return;
    }

    tbody.innerHTML = filtered.map(u => {
      const initials = u.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .substring(0, 2)
        .toUpperCase();

      return `
        <tr>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="user-avatar-circle user-avatar-sm" style="background: linear-gradient(135deg, #0d9488 0%, #2563eb 100%); width:32px; height:32px; font-size:0.75rem;">
                ${initials}
              </div>
              <div>
                <div class="fw-bold">${u.name}</div>
                <small class="text-muted">${u.campus || 'FAST-NUCES'}</small>
              </div>
            </div>
          </td>
          <td><span class="text-muted small">${u.email}</span></td>
          <td><span class="badge bg-light text-dark border">${u.year || '3rd Year'}</span></td>
          <td><strong class="text-main">${CampusCoin.formatPKR(u.allowance || 50000)}</strong></td>
          <td>
            <span class="badge ${u.status === 'Active' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle'} border">
              ${u.status === 'Active' ? '● Active' : '● Disabled'}
            </span>
          </td>
          <td><span class="badge bg-secondary-subtle text-secondary">${u.transactionsCount || 0} entries</span></td>
          <td>
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-secondary btn-admin-view" data-id="${u.id}" title="View Details">
                <i class="bi bi-eye"></i> View
              </button>
              <button class="btn ${u.status === 'Active' ? 'btn-outline-warning' : 'btn-outline-success'} btn-admin-toggle" data-id="${u.id}" title="${u.status === 'Active' ? 'Disable Account' : 'Enable Account'}">
                <i class="bi ${u.status === 'Active' ? 'bi-slash-circle' : 'bi-check2-circle'}"></i> ${u.status === 'Active' ? 'Disable' : 'Enable'}
              </button>
              <button class="btn btn-outline-danger btn-admin-reset" data-email="${u.email}" title="Dispatch Password Reset">
                <i class="bi bi-key"></i> Reset
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');

    tbody.querySelectorAll('.btn-admin-view').forEach(btn => {
      btn.onclick = function() {
        const id = parseInt(this.getAttribute('data-id'));
        const u = adminData.users.find(x => x.id === id);

        if (!u) return;

        document.getElementById('modalStudentName').innerText = u.name;
        document.getElementById('modalStudentEmail').innerText = u.email;
        document.getElementById('modalStudentCampus').innerText = u.campus || 'FAST-NUCES Islamabad';
        document.getElementById('modalStudentYear').innerText = u.year || '3rd Year (Junior)';
        document.getElementById('modalStudentAllowance').innerText = CampusCoin.formatPKR(u.allowance || 50000);
        document.getElementById('modalStudentGoal').innerText = CampusCoin.formatPKR(u.goal || 20000);
        document.getElementById('modalStudentJoined').innerText = u.joined || 'August 2024';
        document.getElementById('modalStudentStatus').className = `badge ${u.status === 'Active' ? 'bg-success' : 'bg-danger'}`;
        document.getElementById('modalStudentStatus').innerText = u.status;

        const viewModal = new bootstrap.Modal(document.getElementById('viewStudentModal'));
        viewModal.show();
      };
    });

    tbody.querySelectorAll('.btn-admin-toggle').forEach(btn => {
      btn.onclick = function() {
        const id = parseInt(this.getAttribute('data-id'));
        const u = adminData.users.find(x => x.id === id);

        if (!u) return;

        u.status = u.status === 'Active' ? 'Disabled' : 'Active';

        CampusDB.saveAdminData(adminData);

        CampusCoin.showToast(
          `Account for ${u.name} is now ${u.status}.`,
          u.status === 'Active' ? 'success' : 'warning'
        );

        renderUsersTable();
        updateKPIs();
      };
    });

    tbody.querySelectorAll('.btn-admin-reset').forEach(btn => {
      btn.onclick = function() {
        const email = this.getAttribute('data-email');

        document.getElementById('resetTargetEmail').value = email;

        const resetModal = new bootstrap.Modal(
          document.getElementById('resetPasswordModal')
        );

        resetModal.show();
      };
    });
  }

  async function renderAnnouncements() {
    const container = document.getElementById('announcementsFeed');

    if (!container) return;

    container.innerHTML = '<div class="p-3 text-muted text-center">Loading campus announcements...</div>';

    try {
      const response = await fetch('/admin/announcements/list', {
        headers: { 'Accept': 'application/json' }
      });

      if (!response.ok) throw new Error('Could not load announcements');

      const announcements = await response.json();
      if (!announcements.length) {
        container.innerHTML = '<div class="p-3 text-muted text-center">No active campus announcements. Post one above.</div>';
        return;
      }

      container.innerHTML = announcements.map(a => `
      <div class="card p-3 mb-2 shadow-sm border">
        <div class="d-flex justify-content-between align-items-start mb-1 flex-wrap gap-2">
          <div>
            <span class="badge ${a.priority === 'Normal' ? 'bg-primary' : 'bg-danger'} me-2">${escapeHtml(a.priority)} Priority</span>
            <strong class="text-main">${escapeHtml(a.title)}</strong>
          </div>
          <div class="d-flex align-items-center gap-2">
            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>${escapeHtml(a.date)}</small>
            <button class="btn btn-sm btn-link text-danger p-0 ms-2 btn-del-ann" data-id="${a.id}" title="Remove Notice" aria-label="Remove ${escapeHtml(a.title)}">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
        <p class="mb-1 text-muted small">${escapeHtml(a.message)}</p>
        <small class="text-secondary"><i class="bi bi-person-badge me-1"></i>Issued by: ${escapeHtml(a.author)}</small>
      </div>
    `).join('');

      container.querySelectorAll('.btn-del-ann').forEach(btn => {
        btn.onclick = async function() {
          this.disabled = true;
          try {
            const response = await fetch(`/admin/announcements/${encodeURIComponent(this.dataset.id)}`, {
              method: 'DELETE',
              headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.csrfToken }
            });

            if (!response.ok) throw new Error('Could not remove announcement');

            CampusCoin.showToast('Announcement removed.', 'info');
            renderAnnouncements();
          } catch (error) {
            this.disabled = false;
            CampusCoin.showToast('Could not remove the announcement.', 'danger');
          }
        };
      });
    } catch (error) {
      container.innerHTML = '<div class="p-3 text-danger text-center">Could not load database announcements.</div>';
      CampusCoin.showToast('Could not load campus announcements.', 'danger');
    }
  }

  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    })[character]);
  }

  async function renderTipTemplates() {
    const tbody = document.getElementById('tipTemplatesTbody');

    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Loading templates...</td></tr>';

    try {
      const response = await fetch('/admin/tip-templates', {
        headers: { 'Accept': 'application/json' }
      });

      if (!response.ok) throw new Error('Unable to load templates');

      const templates = await response.json();
      tbody.innerHTML = templates.map(t => `
      <tr>
        <td class="fw-bold">${escapeHtml(t.title)}</td>
        <td><span class="badge bg-light text-dark border">${escapeHtml(t.category)}</span></td>
        <td class="text-success fw-bold">${CampusCoin.formatPKR(t.saving)}</td>
        <td class="text-muted small tip-template-advice" title="${escapeHtml(t.text)}">${escapeHtml(t.text)}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary btn-edit-tip me-1" data-id="${t.id}" title="Edit Template"><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-outline-danger btn-del-tip" data-id="${t.id}" title="Delete Template">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
    `).join('');

      tbody.querySelectorAll('.btn-edit-tip').forEach(btn => {
        btn.onclick = async function() {
          const template = templates.find(item => String(item.id) === this.dataset.id);
          if (!template) return;

          document.getElementById('tipTemplateId').value = template.id;
          document.getElementById('tipTitleInput').value = template.title;
          document.getElementById('tipCategorySelect').value = template.category;
          document.getElementById('tipSavingInput').value = template.saving;
          document.getElementById('tipTextInput').value = template.text;
          document.getElementById('tipTemplateModalTitle').innerText = 'Edit Saving Tip Template';
          document.getElementById('saveTipTemplateBtn').innerText = 'Save Changes';
          bootstrap.Modal.getOrCreateInstance(document.getElementById('addTipTemplateModal')).show();
        };
      });

    tbody.querySelectorAll('.btn-del-tip').forEach(btn => {
        btn.onclick = async function() {
          this.disabled = true;
          try {
            const response = await fetch(`/admin/tip-templates/${encodeURIComponent(this.dataset.id)}`, {
              method: 'DELETE',
              headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.csrfToken }
            });

            if (!response.ok) throw new Error('Unable to delete template');

            CampusCoin.showToast('Tip template deleted.', 'info');
            renderTipTemplates();
          } catch (error) {
            this.disabled = false;
            CampusCoin.showToast('Could not delete the tip template.', 'danger');
          }
        };
      });
    } catch (error) {
      tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Could not load database templates.</td></tr>';
      CampusCoin.showToast('Could not load tip templates from the database.', 'danger');
    }
  }

  function renderAuditFlags() {
    adminData = CampusDB.getAdminData();

    const tbody = document.getElementById('auditTableBody');

    if (!tbody) return;

    tbody.innerHTML = (adminData.flaggedTransactions || []).map(f => `
      <tr>
        <td><strong class="font-monospace">${f.id}</strong></td>
        <td>
          <div class="fw-bold">${f.student}</div>
          <small class="text-muted">${f.email}</small>
        </td>
        <td>
          <div>${f.desc}</div>
          <span class="badge bg-light text-muted border">${f.category}</span>
        </td>
        <td><strong class="text-danger">${CampusCoin.formatPKR(f.amount)}</strong></td>
        <td>
          <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> ${f.reason}
          </span>
        </td>
        <td><small class="text-muted">${f.date}</small></td>
        <td>
          <span class="badge ${f.status === 'Resolved' ? 'bg-success' : 'bg-warning text-dark'}">
            ${f.status}
          </span>
        </td>
        <td>
          <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-success btn-audit-resolve" data-id="${f.id}" title="Mark as Verified">
              <i class="bi bi-check-lg"></i> Verify
            </button>
            <button class="btn btn-outline-secondary btn-audit-dismiss" data-id="${f.id}" title="Dismiss Flag">
              Dismiss
            </button>
          </div>
        </td>
      </tr>
    `).join('');

    tbody.querySelectorAll('.btn-audit-resolve').forEach(btn => {
      btn.onclick = function() {
        const id = this.getAttribute('data-id');
        const item = adminData.flaggedTransactions.find(x => x.id === id);

        if (item) {
          item.status = 'Resolved';

          CampusDB.saveAdminData(adminData);

          CampusCoin.showToast(`Transaction ${id} verified and approved.`, 'success');

          renderAuditFlags();
          updateKPIs();
        }
      };
    });

    tbody.querySelectorAll('.btn-audit-dismiss').forEach(btn => {
      btn.onclick = function() {
        const id = this.getAttribute('data-id');

        adminData.flaggedTransactions = adminData.flaggedTransactions.filter(x => x.id !== id);

        CampusDB.saveAdminData(adminData);

        CampusCoin.showToast(`Audit flag ${id} dismissed.`, 'info');

        renderAuditFlags();
        updateKPIs();
      };
    });
  }

  const searchInput = document.getElementById('userSearchInput');
  const statusFilter = document.getElementById('userStatusFilter');
  const yearFilter = document.getElementById('userYearFilter');

  if (searchInput) searchInput.addEventListener('input', renderUsersTable);
  if (statusFilter) statusFilter.addEventListener('change', renderUsersTable);
  if (yearFilter) yearFilter.addEventListener('change', renderUsersTable);

  const addStudentForm = document.getElementById('addStudentForm');

  if (addStudentForm) {
    addStudentForm.onsubmit = function(e) {
      e.preventDefault();

      const name = document.getElementById('newStudentName').value.trim();
      const email = document.getElementById('newStudentEmail').value.trim();
      const campus = document.getElementById('newStudentCampus').value.trim();
      const year = document.getElementById('newStudentYear').value;
      const allowance = parseFloat(document.getElementById('newStudentAllowance').value) || 50000;
      const goal = parseFloat(document.getElementById('newStudentGoal').value) || 20000;

      if (!name || !email) {
        CampusCoin.showToast('Please fill all required student fields.', 'danger');
        return;
      }

      adminData = CampusDB.getAdminData();

      const newId = (adminData.users.reduce((max, u) => Math.max(max, u.id), 0) || 0) + 1;

      adminData.users.unshift({
        id: newId,
        name: name,
        email: email,
        campus: campus || 'FAST-NUCES Islamabad',
        year: year,
        allowance: allowance,
        goal: goal,
        status: 'Active',
        transactionsCount: 0,
        joined: new Date().toLocaleDateString('en-US', {
          month: 'short',
          year: 'numeric'
        })
      });

      adminData.totalStudents += 1;
      adminData.activeUsers += 1;

      CampusDB.saveAdminData(adminData);

      CampusCoin.showToast(`Student "${name}" registered successfully!`, 'success');

      bootstrap.Modal.getInstance(
        document.getElementById('addStudentModal')
      )?.hide();

      addStudentForm.reset();

      renderUsersTable();
      updateKPIs();
    };
  }

  const postAnnouncementBtn = document.getElementById('postAnnouncementBtn');

  if (postAnnouncementBtn) {
    postAnnouncementBtn.onclick = async function() {
      const title = document.getElementById('annTitleInput').value.trim();
      const msg = document.getElementById('annMessageInput').value.trim();
      const priority = document.getElementById('annPrioritySelect').value;
      const author = document.getElementById('annAuthorInput').value.trim() || 'Central Administration';

      if (!title || !msg) {
        CampusCoin.showToast('Please enter both announcement title and message.', 'danger');
        return;
      }

      this.disabled = true;
      try {
        const response = await fetch('/admin/announcements', {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
          },
          body: JSON.stringify({ title, message: msg, priority, office: author })
        });

        if (!response.ok) {
          const result = await response.json();
          throw new Error(Object.values(result.errors || {}).flat()[0] || 'Could not post this announcement.');
        }

        CampusCoin.showToast('Campus broadcast advisory posted successfully!', 'success');
        document.getElementById('annTitleInput').value = '';
        document.getElementById('annMessageInput').value = '';
        renderAnnouncements();
      } catch (error) {
        CampusCoin.showToast(error.message || 'Could not post the announcement.', 'danger');
      } finally {
        this.disabled = false;
      }
    };
  }

  const addTipTemplateForm = document.getElementById('addTipTemplateForm');

  if (addTipTemplateForm) {
    document.getElementById('newTipTemplateBtn')?.addEventListener('click', function() {
      addTipTemplateForm.reset();
      document.getElementById('tipTemplateId').value = '';
      document.getElementById('tipTemplateModalTitle').innerText = 'New Saving Tip Template';
      document.getElementById('saveTipTemplateBtn').innerText = 'Save Template';
      document.getElementById('tipTemplateError').innerText = '';
    });

    addTipTemplateForm.onsubmit = async function(e) {
      e.preventDefault();

      const title = document.getElementById('tipTitleInput').value.trim();
      const category = document.getElementById('tipCategorySelect').value;
      const saving = Number(document.getElementById('tipSavingInput').value);
      const text = document.getElementById('tipTextInput').value.trim();
      const tipId = document.getElementById('tipTemplateId').value;
      const errorEl = document.getElementById('tipTemplateError');
      const saveBtn = document.getElementById('saveTipTemplateBtn');

      if (!title || !text || !Number.isFinite(saving) || saving < 0) {
        errorEl.innerText = 'Enter a headline, advice, and a valid estimated savings amount.';
        return;
      }

      saveBtn.disabled = true;
      errorEl.innerText = '';
      try {
        const response = await fetch(tipId ? `/admin/tip-templates/${encodeURIComponent(tipId)}` : '/admin/tip-templates', {
          method: tipId ? 'PUT' : 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
          },
          body: JSON.stringify({ title, category, impact_score: saving, content: text })
        });

        if (!response.ok) {
          const result = await response.json();
          throw new Error(Object.values(result.errors || {}).flat()[0] || 'Could not save this template.');
        }

        CampusCoin.showToast(tipId ? 'Tip template updated.' : 'Tip template created.', 'success');
        bootstrap.Modal.getInstance(document.getElementById('addTipTemplateModal'))?.hide();
        addTipTemplateForm.reset();
        document.getElementById('tipTemplateId').value = '';
        renderTipTemplates();
      } catch (error) {
        errorEl.innerText = error.message || 'Could not save this template. Please try again.';
      } finally {
        saveBtn.disabled = false;
      }
    };
  }

  const confirmPasswordResetBtn = document.getElementById('confirmPasswordResetBtn');

  if (confirmPasswordResetBtn) {
    confirmPasswordResetBtn.onclick = function() {
      const email = document.getElementById('resetTargetEmail').value;

      CampusCoin.showToast(
        `Temporary security reset token sent to ${email}`,
        'warning'
      );

      bootstrap.Modal.getInstance(
        document.getElementById('resetPasswordModal')
      )?.hide();
    };
  }

  const exportBtn = document.getElementById('exportDbSnapshotBtn');

  if (exportBtn) {
    exportBtn.onclick = function() {
      const snapshot = {
        exportedAt: new Date().toISOString(),
        adminMetrics: CampusDB.getAdminData(),
        categories: CampusDB.getCategories(),
        systemStats: {
          uptime: '99.98%',
          databaseCluster: 'FAST-NUCES Primary JSON Node',
          version: '1.0'
        }
      };

      const blob = new Blob(
        [JSON.stringify(snapshot, null, 2)],
        { type: 'application/json' }
      );

      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');

      a.href = url;
      a.download = `CampusCoin_AdminSnapshot_${Date.now()}.json`;

      a.click();

      URL.revokeObjectURL(url);

      CampusCoin.showToast(
        'Database snapshot downloaded successfully!',
        'success'
      );
    };
  }

  updateKPIs();
  renderAdminCharts();
  renderUsersTable();
  renderAnnouncements();
  renderTipTemplates();
  renderAuditFlags();

  window.addEventListener('cc:currency-change', () => {
    updateKPIs();
    renderUsersTable();
    renderTipTemplates();
    renderAuditFlags();
  });
});
