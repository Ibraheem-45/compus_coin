/**
 * Campus Coin - Universal Multi-Currency Engine (PKR / USD)
 * Designed for International Commercial SaaS & Multi-Campus Deployments
 */

(function () {
  'use strict';

  const EXCHANGE_RATE = 280; // 1 USD = 280 PKR

  const CurrencyEngine = {
    rate: EXCHANGE_RATE,

    getCurrency: function () {
      return localStorage.getItem('cc_currency') || 'PKR';
    },

    setCurrency: function (currency) {
      if (currency !== 'PKR' && currency !== 'USD') currency = 'PKR';
      localStorage.setItem('cc_currency', currency);
      this.updateUI();
      this.convertAllDOM();
      window.dispatchEvent(new CustomEvent('cc:currency-change', { detail: { currency, rate: this.rate } }));
    },

    format: function (amountInPKR, showDecimals) {
      if (amountInPKR === undefined || amountInPKR === null || isNaN(amountInPKR)) {
        return this.getCurrency() === 'USD' ? '$0' : 'PKR 0';
      }

      const num = Number(amountInPKR);
      const currency = this.getCurrency();

      if (currency === 'USD') {
        const usdVal = num / this.rate;
        const decimals = showDecimals !== undefined ? (showDecimals ? 2 : 0) : (usdVal >= 100 || Number.isInteger(usdVal) ? 0 : 2);
        return '$' + usdVal.toLocaleString('en-US', {
          minimumFractionDigits: decimals,
          maximumFractionDigits: decimals
        });
      }

      return 'PKR ' + Math.round(num).toLocaleString('en-PK');
    },

    convertAllDOM: function () {
      const currency = this.getCurrency();

      // 1. Elements explicitly tagged with data-pkr
      document.querySelectorAll('[data-pkr]').forEach(el => {
        const rawPKR = parseFloat(el.getAttribute('data-pkr'));
        if (!isNaN(rawPKR)) {
          el.innerText = this.format(rawPKR);
        }
      });

      // 2. Safe-to-Spend display
      const safeSpendEl = document.getElementById('safeSpendDisplay');
      if (safeSpendEl && safeSpendEl.hasAttribute('data-raw-pkr')) {
        const raw = parseFloat(safeSpendEl.getAttribute('data-raw-pkr'));
        safeSpendEl.innerText = this.format(raw);
      }

      // 3. Roommate Split display
      if (typeof window.calculateSplit === 'function') {
        window.calculateSplit();
      }

      // 4. Update Fast-Log Preset Chips
      document.querySelectorAll('.preset-chip').forEach(btn => {
        const pkr = parseFloat(btn.getAttribute('data-preset-pkr'));
        const label = btn.getAttribute('data-preset-label');
        if (!isNaN(pkr) && label) {
          btn.innerHTML = `${label} (${this.format(pkr)})`;
        }
      });

      // 5. Update table cells or elements containing PKR
      document.querySelectorAll('.kpi-amount, .currency-val').forEach(el => {
        if (!el.hasAttribute('data-pkr')) {
          const text = el.innerText.trim();
          const match = text.match(/(?:PKR|\$)\s*([\d,]+(?:\.\d+)?)/i);
          if (match) {
            const raw = parseFloat(match[1].replace(/,/g, ''));
            if (!isNaN(raw)) {
              el.setAttribute('data-pkr', raw);
              el.innerText = this.format(raw);
            }
          }
        }
      });
    },

    updateUI: function () {
      const currency = this.getCurrency();
      const currentFlag = currency === 'USD' ? '🇺🇸' : '🇵🇰';
      const currentLabel = currency === 'USD' ? 'USD ($)' : 'PKR (₨)';

      // Update Switcher Button label in Topbar
      const btn = document.getElementById('currencySwitcherBtn');
      if (btn) {
        btn.innerHTML = `<span class="currency-flag me-1">${currentFlag}</span> <span class="fw-bold currency-code">${currency}</span>`;
      }

      // Update dropdown active status
      document.querySelectorAll('.currency-option').forEach(item => {
        const itemCurr = item.getAttribute('data-currency');
        if (itemCurr === currency) {
          item.classList.add('active');
        } else {
          item.classList.remove('active');
        }
      });
    },

    init: function () {
      // Ensure dark theme is permanently locked
      document.documentElement.setAttribute('data-bs-theme', 'dark');
      localStorage.setItem('cc_theme', 'dark');

      this.updateUI();
      this.convertAllDOM();

      // Hook up dropdown items
      document.addEventListener('click', (e) => {
        const option = e.target.closest('.currency-option');
        if (option) {
          e.preventDefault();
          const selected = option.getAttribute('data-currency');
          if (selected) {
            this.setCurrency(selected);
          }
        }
      });

      // Monkey-patch or enhance CampusCoin.formatPKR
      if (window.CampusCoin) {
        window.CampusCoin.formatPKR = (amount) => this.format(amount);
        window.CampusCoin.formatMoney = (amount) => this.format(amount);
      }
    }
  };

  window.CampusCurrency = CurrencyEngine;

  // Auto initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => CurrencyEngine.init());
  } else {
    CurrencyEngine.init();
  }
})();
