/**
 * Campus Coin - Monthly Reports & Financial Analytics Module
 */

document.addEventListener('DOMContentLoaded', function() {
  CampusCoin.init('reports');

  let chartInstances = {};
  let reportData = window.reportData || {};
  const chartColors = ['#f59e0b', '#64748b', '#06b6d4', '#8b5cf6', '#3b82f6', '#10b981', '#ec4899'];

  function renderReports() {
    Object.values(chartInstances).forEach(chart => chart.destroy());
    chartInstances = {};
    renderMonthlySpendingChart();
    renderIncomeVsExpenseChart();
    renderDailySpendingChart();
    renderWeeklySpendingChart();
    renderCategoryBreakdownChart();
    renderHighlights();
    updateReportLabels();
  }

  window.addEventListener('cc:currency-change', () => {
    renderReports();
  });

  function renderMonthlySpendingChart() {
    const ctx = document.getElementById('reportMonthlySpending');
    if (!ctx) return;

    const incomeOnly = reportData.filters?.flow === 'income';

    chartInstances.monthly = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: reportData.labels || [],
        datasets: [{
          label: incomeOnly ? 'Total Income' : 'Total Expenses',
          data: incomeOnly ? reportData.monthly_income || [] : reportData.monthly_expense || [],
          backgroundColor: incomeOnly ? '#10b981' : '#0d9488',
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            callbacks: {
              label: (ctx) => `Expenses: ${CampusCoin.formatPKR(ctx.parsed.y)}`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { callback: (v) => 'PKR ' + (v/1000) + 'k' }
          }
        }
      }
    });
  }

  function renderIncomeVsExpenseChart() {
    const ctx = document.getElementById('reportIncomeVsExpense');
    if (!ctx) return;

    chartInstances.incomeExpense = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: reportData.labels || [],
        datasets: [
          {
            label: 'Income',
            data: reportData.monthly_income || [],
            backgroundColor: '#10b981',
            borderRadius: 6
          },
          {
            label: 'Expense',
            data: reportData.monthly_expense || [],
            backgroundColor: '#ef4444',
            borderRadius: 6
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            callbacks: {
              label: (ctx) => `${ctx.dataset.label}: ${CampusCoin.formatPKR(ctx.parsed.y)}`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { callback: (v) => 'PKR ' + (v/1000) + 'k' }
          }
        }
      }
    });
  }

  function renderDailySpendingChart() {
    const ctx = document.getElementById('reportDailySpending');
    if (!ctx) return;

    chartInstances.daily = new Chart(ctx, {
      type: 'line',
      data: {
        labels: reportData.daily_labels || [],
        datasets: [{
          label: reportData.filters?.flow === 'income' ? 'Daily Income' : 'Daily Outflow',
          data: reportData.daily_spend || [],
          borderColor: '#f59e0b',
          backgroundColor: 'rgba(245, 158, 11, 0.1)',
          fill: true,
          tension: 0.3,
          pointRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            callbacks: {
              label: (ctx) => `Spent: ${CampusCoin.formatPKR(ctx.parsed.y)}`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { callback: (v) => 'PKR ' + (v/1000) + 'k' }
          }
        }
      }
    });
  }

  function renderWeeklySpendingChart() {
    const ctx = document.getElementById('reportWeeklySpending');
    if (!ctx) return;

    chartInstances.weekly = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: reportData.weekly_labels || [],
        datasets: [{
          label: reportData.filters?.flow === 'income' ? 'Weekly Income' : 'Weekly Outflow',
          data: reportData.weekly_spend || [],
          backgroundColor: '#3b82f6',
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            callbacks: {
              label: (ctx) => `Week Total: ${CampusCoin.formatPKR(ctx.parsed.y)}`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { callback: (v) => 'PKR ' + (v/1000) + 'k' }
          }
        }
      }
    });
  }

  function renderCategoryBreakdownChart() {
    const ctx = document.getElementById('reportCategoryBreakdown');
    if (!ctx) return;

    chartInstances.category = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: reportData.category_labels || [],
        datasets: [{
          data: reportData.category_values || [],
          backgroundColor: chartColors,
          borderWidth: 2,
          borderColor: '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
          legend: { position: 'right' },
          tooltip: {
            callbacks: {
              label: (ctx) => ` ${ctx.label}: ${CampusCoin.formatPKR(ctx.parsed)}`
            }
          }
        }
      }
    });
  }

  function renderHighlights() {
    const list = document.getElementById('reportHighlights');
    if (!list) return;

    list.replaceChildren();
    const highlights = reportData.highlights || [];

    if (!highlights.length) {
      const empty = document.createElement('li');
      empty.className = 'list-group-item px-0 text-muted';
      empty.textContent = 'No transactions match these report filters.';
      list.appendChild(empty);
      return;
    }

    highlights.forEach((highlight, index) => {
      const item = document.createElement('li');
      item.className = 'list-group-item d-flex justify-content-between px-0';

      const name = document.createElement('span');
      const marker = document.createElement('i');
      marker.className = 'bi bi-circle-fill me-1';
      marker.style.color = chartColors[index % chartColors.length];
      name.append(marker, document.createTextNode(highlight.name));

      const total = document.createElement('strong');
      total.textContent = `${CampusCoin.formatPKR(highlight.amount)} (${highlight.share}%)`;
      item.append(name, total);
      list.appendChild(item);
    });
  }

  function updateReportLabels() {
    const rangeLabel = document.getElementById('reportRangeLabel');
    const dailyLabel = document.getElementById('reportDailyLabel');
    const takeawayHeading = document.getElementById('reportTakeawayHeading');

    if (rangeLabel) rangeLabel.textContent = reportData.month_label || 'Selected date range';
    if (dailyLabel) dailyLabel.textContent = `Current month · ${reportData.daily_label || ''}`;
    if (takeawayHeading) takeawayHeading.textContent = `Key Takeaways for ${reportData.month_label || 'selected period'}`;
  }

  function bindFilters() {
    const applyButton = document.getElementById('applyReportFiltersBtn');
    if (!applyButton) return;

    applyButton.onclick = async function() {
      const dateFrom = document.getElementById('reportDateFrom').value;
      const dateTo = document.getElementById('reportDateTo').value;
      const categoryId = document.getElementById('reportCategoryFilter').value;
      const flow = document.getElementById('reportFlowFilter').value;

      if (dateFrom && dateTo && dateFrom > dateTo) {
        CampusCoin.showToast('The start date must be on or before the end date.', 'danger');
        return;
      }

      const params = new URLSearchParams({ date_from: dateFrom, date_to: dateTo, flow });
      if (categoryId !== 'all') params.set('category_id', categoryId);

      this.disabled = true;
      this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Loading';

      try {
        const response = await fetch(`/reports/data?${params.toString()}`, {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) throw new Error('Could not load reports for those filters.');

        reportData = await response.json();
        window.reportData = reportData;
        renderReports();
        CampusCoin.showToast('Reports updated from your transactions.', 'success');
      } catch (error) {
        CampusCoin.showToast(error.message || 'Could not load report data.', 'danger');
      } finally {
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-funnel me-1"></i> Apply';
      }
    };
  }

  function bindExportButtons() {
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    const exportImageBtn = document.getElementById('exportImageBtn');

    if (exportPdfBtn) {
      exportPdfBtn.onclick = function() {
        window.print();
      };
    }

    if (exportImageBtn) {
      exportImageBtn.onclick = function() {
        const canvases = [...document.querySelectorAll('.app-content canvas')];
        const images = canvases.map(canvas => canvas.toDataURL('image/png'));
        if (images.length) {
          const link = document.createElement('a');
          link.download = 'CampusCoin_Report.png';
          link.href = images[0];
          link.click();
          CampusCoin.showToast('Report chart downloaded as PNG image', 'success');
        }
      };
    }
  }

  bindFilters();
  bindExportButtons();
  renderReports();
});
