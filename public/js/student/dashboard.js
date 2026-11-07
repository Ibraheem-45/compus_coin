/**
 * Campus Coin - Main Student Dashboard Module
 */

document.addEventListener('DOMContentLoaded', function() {
  CampusCoin.init('dashboard');

  const serverStats = window.studentDashboardStats || {};
  const serverData = window.studentDashboardData || {};
  const hasServerData = Object.keys(serverStats).length > 0 || Object.keys(serverData).length > 0;

  const user = hasServerData ? (window.currentUser || CampusDB.getUser()) : CampusDB.getUser();
  const transactions = hasServerData ? [] : CampusDB.getTransactions();
  const budgets = hasServerData && Array.isArray(serverData.budget_actual)
    ? serverData.budget_actual
    : CampusDB.getBudgets();
  const tips = hasServerData && Array.isArray(serverData.tips)
    ? serverData.tips
    : CampusDB.getTips();

  const greetingEl = document.getElementById('userGreeting');
  if (greetingEl) {
    const hour = new Date().getHours();
    let timeGreeting = 'Good afternoon';
    if (hour < 12) timeGreeting = 'Good morning';
    else if (hour >= 18) timeGreeting = 'Good evening';

    const firstName = user && user.name ? user.name.split(' ')[0] : 'Student';
    greetingEl.innerText = `${timeGreeting}, ${firstName}!`;
  }

  let totalIncome = Number(serverStats.total_income ?? 0);
  let totalExpenses = Number(serverStats.total_expenses ?? 0);
  let currentBalance = Number(serverStats.current_balance ?? 0);
  let savingsGoal = Number(serverStats.goal ?? user?.savingsGoal ?? user?.monthly_saving_goal ?? 20000);
  let categorySpending = {};

  if (!hasServerData) {
    transactions.forEach(t => {
      if (t.date && t.date.startsWith('2026-09')) {
        const amt = parseFloat(t.amount) || 0;
        if (t.type === 'income') {
          totalIncome += amt;
        } else if (t.type === 'expense') {
          totalExpenses += amt;
          categorySpending[t.category] = (categorySpending[t.category] || 0) + amt;
        }
      }
    });

    if (totalIncome === 0 && totalExpenses === 0) {
      totalIncome = 75000;
      totalExpenses = 34000;
    }

    currentBalance = totalIncome - totalExpenses;
  } else {
    const spendingMap = serverData.spending || {};
    Object.keys(spendingMap).forEach(key => {
      categorySpending[key] = Number(spendingMap[key]) || 0;
    });
    totalIncome = Number(serverStats.total_income ?? totalIncome);
    totalExpenses = Number(serverStats.total_expenses ?? totalExpenses);
    currentBalance = Number(serverStats.current_balance ?? (totalIncome - totalExpenses));
    savingsGoal = Number(serverStats.goal ?? savingsGoal);
  }

  const savingsPercent = savingsGoal > 0
    ? Math.max(0, Math.min(Math.round((currentBalance / savingsGoal) * 100), 100))
    : 0;

  const incomeEl = document.getElementById('kpiTotalIncome');
  const expensesEl = document.getElementById('kpiTotalExpenses');
  const balanceEl = document.getElementById('kpiCurrentBalance');
  const goalEl = document.getElementById('kpiSavingsGoal');
  const goalProgressEl = document.getElementById('goalProgressBar');
  const goalPercentText = document.getElementById('goalPercentText');

  if (incomeEl) incomeEl.innerText = CampusCoin.formatPKR(totalIncome);
  if (expensesEl) expensesEl.innerText = CampusCoin.formatPKR(totalExpenses);
  if (balanceEl) balanceEl.innerText = CampusCoin.formatPKR(currentBalance);
  if (goalEl) goalEl.innerText = CampusCoin.formatPKR(savingsGoal);
  if (goalProgressEl) goalProgressEl.style.width = savingsPercent + '%';
  if (goalPercentText) goalPercentText.innerText = `${savingsPercent}% achieved`;

  let topCategory = serverStats.top_category || 'No data yet';
  let topAmount = Number(serverStats.top_amount ?? 0);
  let maxSpend = 0;

  if (!hasServerData) {
    for (const [cat, amt] of Object.entries(categorySpending)) {
      if (amt > maxSpend) {
        maxSpend = amt;
        topCategory = cat;
        topAmount = amt;
      }
    }
  }

  if (!hasServerData && Object.keys(categorySpending).length === 0 && totalExpenses === 0) {
    topCategory = 'No data yet';
    topAmount = 0;
  }

  const topCatNameEl = document.getElementById('topCategoryName');
  const topCatAmountEl = document.getElementById('topCategoryAmount');
  if (topCatNameEl) topCatNameEl.innerText = topCategory;
  if (topCatAmountEl) topCatAmountEl.innerText = CampusCoin.formatPKR(topAmount);

  renderBudgetVsActualChart(budgets, categorySpending);
  renderSpendingBreakdownChart(categorySpending);
  renderSixMonthTrendChart(serverData.trend || []);
  renderDashboardTips(tips);

  window.addEventListener('cc:currency-change', () => {
    if (incomeEl) incomeEl.innerText = CampusCoin.formatPKR(totalIncome);
    if (expensesEl) expensesEl.innerText = CampusCoin.formatPKR(totalExpenses);
    if (balanceEl) balanceEl.innerText = CampusCoin.formatPKR(currentBalance);
    if (goalEl) goalEl.innerText = CampusCoin.formatPKR(savingsGoal);
    if (topCatAmountEl) topCatAmountEl.innerText = CampusCoin.formatPKR(topAmount);
    renderBudgetVsActualChart(budgets, categorySpending);
    renderSpendingBreakdownChart(categorySpending);
    renderSixMonthTrendChart(serverData.trend || []);
    renderDashboardTips(tips);
  });
});

// Chart 1: Budget vs Actual
function renderBudgetVsActualChart(budgets, spendingMap) {
  const ctx = document.getElementById('budgetVsActualChart');
  if (!ctx || typeof Chart === 'undefined') return;

  const budgetEntries = Array.isArray(budgets) ? budgets : [];
  const spendingEntries = Object.entries(spendingMap || {}).filter(([, value]) => Number(value) > 0);

  const chartCategories = spendingEntries.length
    ? spendingEntries
        .sort((a, b) => Number(b[1]) - Number(a[1]))
        .slice(0, 5)
        .map(([category]) => category)
    : budgetEntries.length
      ? budgetEntries.map(item => item.category || 'Other').slice(0, 5)
      : [];

  if (!chartCategories.length) {
    const container = ctx.parentElement;
    if (container) {
      container.innerHTML = '<div class="d-flex align-items-center justify-content-center text-muted small fw-semibold h-100 border rounded bg-light"><span><i class="bi bi-info-circle me-2"></i>No spending data yet</span></div>';
    }
    return;
  }

  const budgetLimits = chartCategories.map(cat => {
    const match = budgetEntries.find(item => String(item.category || 'Other').toLowerCase() === String(cat).toLowerCase());
    return match ? Number(match.limit || 0) : 0;
  });

  const actuals = chartCategories.map(cat => Number(spendingMap?.[cat] || 0));

  const existing1 = Chart.getChart(ctx);
  if (existing1) existing1.destroy();

  const existing2 = Chart.getChart(ctx);
  if (existing2) existing2.destroy();

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartCategories,
      datasets: [
        {
          label: 'Budget Limit',
          data: budgetLimits,
          backgroundColor: 'rgba(201, 162, 39, 0.25)',
          borderColor: 'rgba(201, 162, 39, 0.5)',
          borderWidth: 1,
          borderRadius: 6,
          barThickness: 14
        },
        {
          label: 'Actual Spent',
          data: actuals,
          backgroundColor: '#C9A227',
          borderRadius: 6,
          barThickness: 14
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: { boxWidth: 12, color: '#e2e8f0', font: { family: 'Inter', weight: 600 } }
        },
        tooltip: {
          backgroundColor: '#05140A',
          borderColor: '#C9A227',
          borderWidth: 1,
          titleColor: '#C9A227',
          bodyColor: '#F5F5DC',
          callbacks: {
            label: function(context) {
              return `${context.dataset.label}: ${CampusCoin.formatPKR(context.parsed.y)}`;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(201, 162, 39, 0.08)' },
          ticks: {
            color: '#94a3b8',
            font: { family: 'JetBrains Mono', size: 10 },
            callback: function(value) { return 'PKR ' + (value >= 1000 ? (value/1000) + 'k' : value); }
          }
        },
        x: {
          grid: { display: false },
          ticks: { color: '#94a3b8', font: { family: 'Inter', size: 11 } }
        }
      }
    }
  });
}

// Chart 2: Spending Breakdown Doughnut
function renderSpendingBreakdownChart(spendingMap) {
  const ctx = document.getElementById('spendingBreakdownChart');
  if (!ctx || typeof Chart === 'undefined') return;

  const entries = Object.entries(spendingMap || {}).filter(([, value]) => Number(value) > 0);

  if (!entries.length) {
    const container = ctx.parentElement;
    if (container) {
      container.innerHTML = '<div class="d-flex align-items-center justify-content-center text-muted small fw-semibold h-100 border rounded bg-light"><span><i class="bi bi-info-circle me-2"></i>No expense data yet</span></div>';
    }
    return;
  }

  const labels = entries.map(([label]) => label);
  const data = entries.map(([, value]) => Number(value));
  const colors = ['#C9A227', '#10b981', '#38bdf8', '#f59e0b', '#8FBC8F', '#a855f7', '#ef4444', '#ec4899'];

  const existing2 = Chart.getChart(ctx);
  if (existing2) existing2.destroy();

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: colors.slice(0, labels.length),
        borderWidth: 2,
        borderColor: '#05140A',
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '72%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { 
            boxWidth: 10, 
            color: '#cbd5e1',
            font: { family: 'Inter', size: 11, weight: 500 } 
          }
        },
        tooltip: {
          backgroundColor: 'rgba(5, 20, 10, 0.95)',
          borderColor: 'rgba(201, 162, 39, 0.3)',
          borderWidth: 1,
          titleColor: '#C9A227',
          bodyColor: '#F5F5DC',
          padding: 10,
          callbacks: {
            label: function(context) {
              return ` ${context.label}: ${CampusCoin.formatPKR(context.parsed)}`;
            }
          }
        }
      }
    }
  });
}

// Chart 3: Six-Month Trend
function renderSixMonthTrendChart(trendData = []) {
  const ctx = document.getElementById('sixMonthTrendChart');
  if (!ctx || typeof Chart === 'undefined') return;

  let months = [];
  let incomeTrend = [];
  let expenseTrend = [];

  if (Array.isArray(trendData) && trendData.length) {
    months = trendData.map(item => item.label || item.month || item.name || '');
    incomeTrend = trendData.map(item => Number(item.income || 0));
    expenseTrend = trendData.map(item => Number(item.expense || 0));
  } else {
    for (let i = 5; i >= 0; i--) {
      const monthDate = new Date();
      monthDate.setMonth(monthDate.getMonth() - i);
      months.push(monthDate.toLocaleString('en-US', { month: 'short', year: 'numeric' }));
      incomeTrend.push(0);
      expenseTrend.push(0);
    }
  }

  const existing2 = Chart.getChart(ctx);
  if (existing2) existing2.destroy();

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: months,
      datasets: [
        {
          label: 'Total Income',
          data: incomeTrend,
          borderColor: '#C9A227',
          backgroundColor: 'rgba(201, 162, 39, 0.15)',
          borderWidth: 2.5,
          fill: true,
          tension: 0.35,
          pointRadius: 4,
          pointBackgroundColor: '#C9A227',
          pointBorderColor: '#05140A',
          pointHoverRadius: 6
        },
        {
          label: 'Total Expenses',
          data: expenseTrend,
          borderColor: '#ef4444',
          backgroundColor: 'rgba(239, 68, 68, 0.08)',
          borderWidth: 2,
          fill: true,
          tension: 0.35,
          pointRadius: 4,
          pointBackgroundColor: '#ef4444',
          pointBorderColor: '#05140A',
          pointHoverRadius: 6
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: { 
            boxWidth: 12, 
            color: '#cbd5e1',
            font: { family: 'Inter', weight: 600 } 
          }
        },
        tooltip: {
          backgroundColor: 'rgba(5, 20, 10, 0.95)',
          borderColor: 'rgba(201, 162, 39, 0.3)',
          borderWidth: 1,
          titleColor: '#C9A227',
          bodyColor: '#F5F5DC',
          padding: 10,
          callbacks: {
            label: function(context) {
              return `${context.dataset.label}: ${CampusCoin.formatPKR(context.parsed.y)}`;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: false,
          grid: { color: 'rgba(201, 162, 39, 0.08)' },
          ticks: {
            color: '#94a3b8',
            callback: function(val) { return 'PKR ' + (val/1000) + 'k'; }
          }
        },
        x: {
          grid: { display: false },
          ticks: { color: '#94a3b8' }
        }
      }
    }
  });
}

// Render 3 Personalized Saving Tips
function renderDashboardTips(tips) {
  const container = document.getElementById('dashboardTipsContainer');
  if (!container) return;

  const activeTips = tips.filter(t => !t.dismissed).slice(0, 3);
  if (!activeTips.length) {
    container.innerHTML = '<div class="col-12 text-center text-muted py-3">No spending data yet. Add your first transaction to unlock personalized tips.</div>';
    return;
  }

  container.innerHTML = activeTips.map(tip => `
    <div class="col-md-4 mb-3" id="tipCard_${tip.id}">
      <div class="saving-tip-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="badge bg-light text-dark border">${tip.category}</span>
          <span class="tip-savings-badge">Save ${CampusCoin.formatPKR(tip.potentialSavings)}/mo</span>
        </div>
        <h6 class="fw-bold mb-2">${tip.title}</h6>
        <p class="text-muted small flex-grow-1">${tip.description}</p>
        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
          <button class="btn btn-sm ${tip.pinned ? 'btn-warning text-dark' : 'btn-outline-secondary'} pin-tip-btn" data-id="${tip.id}">
            <i class="bi bi-pin-angle${tip.pinned ? '-fill' : ''}"></i> ${tip.pinned ? 'Pinned' : 'Pin'}
          </button>
          <button class="btn btn-sm btn-link text-muted text-decoration-none dismiss-tip-btn" data-id="${tip.id}">
            Dismiss
          </button>
        </div>
      </div>
    </div>
  `).join('');

  // Pin & Dismiss listeners
  container.querySelectorAll('.pin-tip-btn').forEach(btn => {
    btn.onclick = function() {
      const id = this.getAttribute('data-id');
      const isPinned = CampusDB.togglePinTip(id);
      this.className = `btn btn-sm ${isPinned ? 'btn-warning text-dark' : 'btn-outline-secondary'} pin-tip-btn`;
      this.innerHTML = `<i class="bi bi-pin-angle${isPinned ? '-fill' : ''}"></i> ${isPinned ? 'Pinned' : 'Pin'}`;
      CampusCoin.showToast(isPinned ? 'Tip saved to bookmarks' : 'Tip unpinned', 'info');
    };
  });

  container.querySelectorAll('.dismiss-tip-btn').forEach(btn => {
    btn.onclick = function() {
      const id = this.getAttribute('data-id');
      CampusDB.dismissTip(id);
      const card = document.getElementById('tipCard_' + id);
      if (card) {
        card.style.opacity = '0';
        setTimeout(() => card.remove(), 250);
      }
      CampusCoin.showToast('Tip dismissed', 'info');
    };
  });
}
