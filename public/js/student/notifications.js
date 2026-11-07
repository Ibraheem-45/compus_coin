/**
 * Campus Coin - Notifications Module
 */

document.addEventListener('DOMContentLoaded', function() {
  CampusCoin.init('notifications');

  const container = document.getElementById('notificationsList');
  const markAllBtn = document.getElementById('markAllReadBtn');
  let notifications = Array.isArray(window.studentNotifications) ? window.studentNotifications : [];

  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    })[character]);
  }

  function updateUnreadCount() {
    window.serverUnreadNotificationsCount = notifications.filter(notification => !notification.read).length;
    CampusCoin.renderSidebar('notifications');
    document.querySelector('.badge-dot')?.classList.toggle('d-none', window.serverUnreadNotificationsCount === 0);
  }

  function renderNotifications() {
    if (!container) return;

    if (!notifications.length) {
      container.innerHTML = '<div class="text-center py-5 text-muted">No notifications yet. Budget alerts and campus notices will appear here.</div>';
      if (markAllBtn) markAllBtn.disabled = true;
      return;
    }

    const typeIcons = {
      warning: { icon: 'bi-exclamation-triangle-fill text-warning', bg: 'bg-warning-subtle' },
      info: { icon: 'bi-info-circle-fill text-primary', bg: 'bg-primary-subtle' },
      tip: { icon: 'bi-lightbulb-fill text-amber', bg: 'bg-amber-subtle' },
      security: { icon: 'bi-shield-exclamation text-danger', bg: 'bg-danger-subtle' },
      success: { icon: 'bi-check-circle-fill text-success', bg: 'bg-success-subtle' }
    };

    container.innerHTML = notifications.map(n => {
      const style = typeIcons[n.type] || typeIcons.info;
      return `
        <div class="card mb-3 p-3 ${!n.read ? 'border-primary shadow-sm bg-primary-subtle bg-opacity-10' : ''}" id="notifItem_${n.id}">
          <div class="d-flex align-items-start gap-3">
            <div class="p-2 rounded ${style.bg} fs-4">
              <i class="bi ${style.icon}"></i>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 fw-bold">${escapeHtml(n.title)}</h6>
                <small class="text-muted">${escapeHtml(n.time)}</small>
              </div>
              <p class="mb-2 text-muted small">${escapeHtml(n.message)}</p>
              <div class="d-flex justify-content-end">
                ${!n.read ? `
                  <button class="btn btn-sm btn-link text-primary p-0 text-decoration-none mark-single-read-btn" data-id="${escapeHtml(n.id)}">
                    <i class="bi bi-check2"></i> Mark as read
                  </button>
                ` : '<span class="small text-muted"><i class="bi bi-check2-all text-muted"></i> Read</span>'}
              </div>
            </div>
          </div>
        </div>
      `;
    }).join('');

    // Attach single mark as read
    if (markAllBtn) {
      markAllBtn.disabled = notifications.every(notification => notification.read);
    }

    container.querySelectorAll('.mark-single-read-btn').forEach(btn => {
      btn.onclick = async function() {
        const notification = notifications.find(item => String(item.id) === this.dataset.id);
        if (!notification) return;

        this.disabled = true;
        try {
          const response = await fetch(`/notifications/${encodeURIComponent(notification.id)}/read`, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': window.csrfToken,
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          if (!response.ok) throw new Error('Could not mark notification as read');

          notification.read = true;
          updateUnreadCount();
          renderNotifications();
        } catch (error) {
          this.disabled = false;
          CampusCoin.showToast('Could not update this notification. Please try again.', 'danger');
        }
      };
    });
  }

  if (markAllBtn) {
    markAllBtn.onclick = async function() {
      this.disabled = true;
      try {
        const response = await fetch('/notifications/read-all', {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) throw new Error('Could not mark notifications as read');

        notifications = notifications.map(notification => ({ ...notification, read: true }));
        updateUnreadCount();
        CampusCoin.showToast('All notifications marked as read', 'success');
        renderNotifications();
      } catch (error) {
        this.disabled = false;
        CampusCoin.showToast('Could not update notifications. Please try again.', 'danger');
      }
    };
  }

  renderNotifications();
});
