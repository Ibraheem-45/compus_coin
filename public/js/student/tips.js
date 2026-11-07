/**
 * Campus Coin - Personalized Saving Tips Module
 */

document.addEventListener('DOMContentLoaded', function() {
  CampusCoin.init('saving-tips');

  let activeFilter = 'All';
  const tipsContainer = document.getElementById('savingTipsGrid');
  const filterButtons = document.querySelectorAll('.tips-filter-btn');
  const insightsContainer = document.getElementById('monthlyInsights');
  const refreshInsightsButton = document.getElementById('refreshInsightsBtn');

  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    })[character]);
  }

  function renderTips() {
    const tips = (Array.isArray(window.studentTipsData) ? window.studentTipsData : [])
      .filter(tip => !tip.dismissed);

    const filtered = activeFilter === 'All' 
      ? tips 
      : tips.filter(t => t.category.toLowerCase() === activeFilter.toLowerCase());

    if (!tipsContainer) return;

    if (!filtered.length) {
      const noMonthlyData = !window.studentHasMonthExpenses;
      tipsContainer.innerHTML = `
        <div class="col-12 text-center py-5 text-muted">
          <i class="bi ${noMonthlyData ? 'bi-receipt' : 'bi-check2-circle text-success'} fs-1 d-block mb-2 opacity-75"></i>
          <h5>${noMonthlyData ? 'No spending to analyze yet' : 'No active tips right now'}</h5>
          <p class="small">${noMonthlyData ? 'Add an expense transaction this month to get recommendations based on your spending.' : `No active tips in ${escapeHtml(activeFilter)}. Check back after your next transaction.`}</p>
        </div>
      `;
      return;
    }

    tipsContainer.innerHTML = filtered.map(tip => `
      <div class="col-md-6 col-lg-4 mb-4" id="tipCard_${tip.id}">
        <div class="card saving-tip-card h-100">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-light text-dark border">${escapeHtml(tip.category)}</span>
            <span class="tip-savings-badge">Save ${CampusCoin.formatPKR(tip.potentialSavings)}/mo</span>
          </div>
          <h5 class="fw-bold mb-2">${escapeHtml(tip.title)}</h5>
          <p class="text-muted small flex-grow-1 mb-3">${escapeHtml(tip.description)}</p>
          <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
            <button class="btn btn-sm ${tip.pinned ? 'btn-warning text-dark' : 'btn-outline-secondary'} pin-tip-btn" data-id="${tip.id}">
              <i class="bi bi-pin-angle${tip.pinned ? '-fill' : ''}"></i> ${tip.pinned ? 'Saved to Bookmarks' : 'Pin to Bookmarks'}
            </button>
            <button class="btn btn-sm btn-link text-muted text-decoration-none dismiss-tip-btn" data-id="${tip.id}">
              Dismiss
            </button>
          </div>
        </div>
      </div>
    `).join('');

    // Attach listeners
    tipsContainer.querySelectorAll('.pin-tip-btn').forEach(btn => {
      btn.onclick = async function() {
        const id = this.getAttribute('data-id');
        const tip = tips.find(item => String(item.id) === String(id));
        if (!tip) return;

        this.disabled = true;
        try {
          const result = await CampusCoin.toggleBookmark('tip', tip);
          tip.pinned = result.saved;
          this.className = `btn btn-sm ${tip.pinned ? 'btn-warning text-dark' : 'btn-outline-secondary'} pin-tip-btn`;
          this.innerHTML = `<i class="bi bi-pin-angle${tip.pinned ? '-fill' : ''}"></i> ${tip.pinned ? 'Saved to Bookmarks' : 'Pin to Bookmarks'}`;
          CampusCoin.showToast(tip.pinned ? 'Tip saved to Bookmarks!' : 'Tip removed from Bookmarks', 'info');
        } catch (error) {
          CampusCoin.showToast('Could not update this bookmark. Please try again.', 'danger');
        } finally {
          this.disabled = false;
        }
      };
    });

    tipsContainer.querySelectorAll('.dismiss-tip-btn').forEach(btn => {
      btn.onclick = async function() {
        const id = this.getAttribute('data-id');
        const tip = tips.find(item => String(item.id) === String(id));
        if (!tip) return;

        this.disabled = true;
        try {
          const tipId = String(id).replace(/^tip_/, '');
          const response = await fetch(`/saving-tips/${encodeURIComponent(tipId)}/dismiss`, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': window.csrfToken,
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          if (!response.ok) throw new Error('Could not dismiss tip');

          tip.dismissed = true;
          renderTips();
          CampusCoin.showToast('Tip dismissed', 'info');
        } catch (error) {
          CampusCoin.showToast('Could not dismiss this tip. Please try again.', 'danger');
          this.disabled = false;
        }
      };
    });
  }

  function renderInsights(insights) {
    if (!insightsContainer) return;

    if (!insights.length) {
      insightsContainer.innerHTML = '<div class="col-12 text-muted small">No expenses recorded this month yet. Add an expense to get a useful insight.</div>';
      return;
    }

    insightsContainer.innerHTML = insights.map(insight => `
      <div class="col-md-6 col-xl-4">
        <article class="card h-100 p-3 shadow-sm">
          <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
            <span class="badge bg-light text-dark border">${escapeHtml(insight.category)}</span>
            <span class="small text-muted">${insight.ai_generated ? 'AI generated' : 'Based on your records'}</span>
          </div>
          <h6 class="fw-bold">${escapeHtml(insight.pattern)}</h6>
          <p class="text-muted small mb-2">${escapeHtml(insight.explanation)}</p>
          <p class="small mb-3"><strong>Next step:</strong> ${escapeHtml(insight.suggestedAction)}</p>
          <button type="button" class="btn btn-sm ${insight.bookmarked ? 'btn-warning text-dark' : 'btn-outline-secondary'} align-self-start insight-bookmark-btn" data-id="${escapeHtml(insight.id)}">
            <i class="bi bi-bookmark${insight.bookmarked ? '-check-fill' : ''}"></i> ${insight.bookmarked ? 'Saved to bookmarks' : 'Save insight'}
          </button>
        </article>
      </div>
    `).join('');

    insightsContainer.querySelectorAll('.insight-bookmark-btn').forEach(button => {
      button.addEventListener('click', async () => {
        const insight = insights.find(item => item.id === button.dataset.id);
        if (!insight) return;

        button.disabled = true;
        try {
          const result = await CampusCoin.toggleBookmark('insight', insight);
          insight.bookmarked = result.saved;
          button.className = `btn btn-sm ${result.saved ? 'btn-warning text-dark' : 'btn-outline-secondary'} align-self-start insight-bookmark-btn`;
          button.innerHTML = result.saved
            ? '<i class="bi bi-bookmark-check-fill"></i> Saved to bookmarks'
            : '<i class="bi bi-bookmark"></i> Save insight';
          CampusCoin.showToast(result.saved ? 'Insight saved to Bookmarks.' : 'Insight removed from Bookmarks.', 'info');
        } catch {
          CampusCoin.showToast('Could not update this bookmark. Please try again.', 'danger');
        } finally {
          button.disabled = false;
        }
      });
    });
  }

  if (refreshInsightsButton) {
    refreshInsightsButton.addEventListener('click', async () => {
      refreshInsightsButton.disabled = true;
      refreshInsightsButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Checking records';

      try {
        const response = await fetch(window.studentInsightsUrl, {
          headers: { Accept: 'application/json' },
          cache: 'no-store',
        });

        if (!response.ok) throw new Error('Insight request failed');
        renderInsights(await response.json());
      } catch {
        insightsContainer.innerHTML = '<div class="col-12 text-danger small">Insights could not be refreshed. Please try again.</div>';
      } finally {
        refreshInsightsButton.disabled = false;
        refreshInsightsButton.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Refresh insights';
      }
    });
  }

  // Filter Buttons
  filterButtons.forEach(btn => {
    btn.onclick = function() {
      filterButtons.forEach(b => b.classList.remove('active', 'btn-primary'));
      filterButtons.forEach(b => b.classList.add('btn-outline-secondary'));
      this.classList.remove('btn-outline-secondary');
      this.classList.add('active', 'btn-primary');
      activeFilter = this.getAttribute('data-filter');
      renderTips();
    };
  });

  renderTips();
  window.addEventListener('cc:currency-change', () => renderTips());
});
