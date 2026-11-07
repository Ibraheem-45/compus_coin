/**
 * Campus Coin - Core Application & Central State Management
 * Theme: NextGen BudgetBee
 * All data is managed using localStorage with rich student demo seed data in PKR.
 */

const CampusDB = (function() {
  const STORAGE_KEYS = {
    USER: 'cc_user',
    TRANSACTIONS: 'cc_transactions',
    BUDGETS: 'cc_budgets',
    CATEGORIES: 'cc_categories',
    TIPS: 'cc_tips',
    NOTIFICATIONS: 'cc_notifications',
    SETTINGS: 'cc_settings',
    ADMIN_METRICS: 'cc_admin_metrics'
  };

  // Realistic seed data
  const DEFAULT_USER = {
    name: "Ahmad Raza",
    email: "ahmad.raza@nu.edu.pk",
    role: "student",
    academicYear: "3rd Year (Junior)",
    baselineAllowance: 50000,
    savingsGoal: 20000,
    university: "FAST-NUCES",
    avatar: "AR",
    joinedDate: "Fall 2024"
  };

  const DEFAULT_CATEGORIES = {
    income: [
      { id: "inc_1", name: "Allowance", icon: "bi-cash-stack", color: "#10b981" },
      { id: "inc_2", name: "Part-time Job", icon: "bi-briefcase", color: "#3b82f6" },
      { id: "inc_3", name: "Scholarship", icon: "bi-mortarboard", color: "#8b5cf6" },
      { id: "inc_4", name: "Gift", icon: "bi-gift", color: "#ec4899" },
      { id: "inc_5", name: "Other Income", icon: "bi-wallet2", color: "#06b6d4" }
    ],
    expense: [
      { id: "exp_1", name: "Food", icon: "bi-cup-hot", color: "#f59e0b" },
      { id: "exp_2", name: "Transport", icon: "bi-bus-front", color: "#06b6d4" },
      { id: "exp_3", name: "Hostel/Rent", icon: "bi-building", color: "#64748b" },
      { id: "exp_4", name: "Academics", icon: "bi-book", color: "#3b82f6" },
      { id: "exp_5", name: "Subscriptions", icon: "bi-play-btn", color: "#ec4899" },
      { id: "exp_6", name: "Entertainment", icon: "bi-controller", color: "#8b5cf6" },
      { id: "exp_7", name: "Miscellaneous", icon: "bi-three-dots", color: "#6b7280" }
    ]
  };

  const DEFAULT_BUDGETS = [
    { category: "Food", limit: 20000, icon: "bi-cup-hot", color: "#f59e0b" },
    { category: "Transport", limit: 10000, icon: "bi-bus-front", color: "#06b6d4" },
    { category: "Hostel/Rent", limit: 15000, icon: "bi-building", color: "#64748b" },
    { category: "Academics", limit: 8000, icon: "bi-book", color: "#3b82f6" },
    { category: "Entertainment", limit: 6000, icon: "bi-controller", color: "#8b5cf6" },
    { category: "Subscriptions", limit: 3000, icon: "bi-play-btn", color: "#ec4899" },
    { category: "Miscellaneous", limit: 3000, icon: "bi-three-dots", color: "#6b7280" }
  ];

  const DEFAULT_TRANSACTIONS = [
    { id: 101, date: "2026-09-23", description: "Campus Cafe Lunch", category: "Food", type: "expense", amount: 850, recurring: false },
    { id: 102, date: "2026-09-22", description: "Metro Bus Monthly Pass", category: "Transport", type: "expense", amount: 2000, recurring: true, frequency: "Monthly" },
    { id: 103, date: "2026-09-21", description: "Web Dev Tutoring Stipend", category: "Part-time Job", type: "income", amount: 20000, recurring: false },
    { id: 104, date: "2026-09-20", description: "Photocopying & Course Packs", category: "Academics", type: "expense", amount: 1450, recurring: false },
    { id: 105, date: "2026-09-19", description: "Hostel Mess Advance", category: "Food", type: "expense", amount: 8500, recurring: true, frequency: "Monthly" },
    { id: 106, date: "2026-09-18", description: "Spotify & Cloud Storage", category: "Subscriptions", type: "expense", amount: 950, recurring: true, frequency: "Monthly" },
    { id: 107, date: "2026-09-16", description: "Cinema & Bowling Night", category: "Entertainment", type: "expense", amount: 3500, recurring: false },
    { id: 108, date: "2026-09-15", description: "Monthly Family Allowance", category: "Allowance", type: "income", amount: 50000, recurring: true, frequency: "Monthly" },
    { id: 109, date: "2026-09-14", description: "Subway Meal with Project Team", category: "Food", type: "expense", amount: 1800, recurring: false },
    { id: 110, date: "2026-09-12", description: "Careem Ride to Exam Center", category: "Transport", type: "expense", amount: 1200, recurring: false },
    { id: 111, date: "2026-09-10", description: "Hostel Room Rent Installment", category: "Hostel/Rent", type: "expense", amount: 15000, recurring: true, frequency: "Monthly" },
    { id: 112, date: "2026-09-08", description: "Gaming Lounge Tournament", category: "Entertainment", type: "expense", amount: 3000, recurring: false },
    { id: 113, date: "2026-09-06", description: "Data Structures Reference Book", category: "Academics", type: "expense", amount: 3200, recurring: false },
    { id: 114, date: "2026-09-05", description: "Birthday Gift from Uncle", category: "Gift", type: "income", amount: 5000, recurring: false },
    { id: 115, date: "2026-09-04", description: "Grocery & Midnight Study Snacks", category: "Food", type: "expense", amount: 3500, recurring: false },
    { id: 116, date: "2026-09-03", description: "Stationery & Notebooks", category: "Miscellaneous", type: "expense", amount: 800, recurring: false },
    { id: 117, date: "2026-09-02", description: "Campus Juice Bar & Tea", category: "Food", type: "expense", amount: 650, recurring: false },
    { id: 118, date: "2026-09-01", description: "Hostel Wi-Fi Contribution", category: "Subscriptions", type: "expense", amount: 1050, recurring: true, frequency: "Monthly" },
    { id: 119, date: "2026-08-28", description: "Previous Month Freelance Gig", category: "Part-time Job", type: "income", amount: 18000, recurring: false },
    { id: 120, date: "2026-08-25", description: "August Food Mart Grocery", category: "Food", type: "expense", amount: 4200, recurring: false },
    { id: 121, date: "2026-08-20", description: "Campus Shuttle Pass", category: "Transport", type: "expense", amount: 2500, recurring: false }
  ];

  const DEFAULT_TIPS = [
    {
      id: "tip_1",
      category: "Food",
      title: "Switch to Campus Mess Subscription",
      description: "Dining à la carte at the cafeteria costs roughly PKR 850/day. Enrolling in the weekly hostel mess meal plan saves over 25% on daily meals.",
      potentialSavings: 3000,
      pinned: true,
      dismissed: false
    },
    {
      id: "tip_2",
      category: "Transport",
      title: "Carpool or Use University Point Buses",
      description: "Taking private rideshare (Careem/Indrive) during rush hours eats into savings. Coordinating campus point routes cuts commute costs significantly.",
      potentialSavings: 2500,
      pinned: false,
      dismissed: false
    },
    {
      id: "tip_3",
      category: "Academics",
      title: "Use Digital Library Reserves",
      description: "Access semester textbooks via HEC digital library portal and borrow reserved copies before buying brand-new hardcover editions.",
      potentialSavings: 1800,
      pinned: true,
      dismissed: false
    },
    {
      id: "tip_4",
      category: "Subscriptions",
      title: "Activate Student Tier Discounts",
      description: "You are currently paying standard rates for streaming. Switching to verified Spotify & GitHub student discount cuts fees by 50%.",
      potentialSavings: 1200,
      pinned: false,
      dismissed: false
    },
    {
      id: "tip_5",
      category: "Entertainment",
      title: "Opt for Campus Society Movie Nights",
      description: "Enjoy weekly screenings hosted by the University Media Club rather than commercial cinema tickets and mall snacks.",
      potentialSavings: 2000,
      pinned: false,
      dismissed: false
    }
  ];

  const DEFAULT_NOTIFICATIONS = [
    { id: "notif_1", type: "warning", title: "Budget Limit Exceeded", message: "Your Entertainment budget has surpassed the PKR 6,000 threshold (PKR 6,500 spent).", time: "2 hours ago", read: false },
    { id: "notif_2", type: "warning", title: "Budget Near Limit", message: "Food spending has reached 92.5% of your PKR 20,000 monthly allowance.", time: "Yesterday", read: false },
    { id: "notif_3", type: "info", title: "New Monthly Spending Insight", message: "Ledger analysis has detected a 38% surge in midterm week cafe expenditures.", time: "2 days ago", read: false },
    { id: "notif_4", type: "tip", title: "Personalized Saving Tip", message: "Switch to university point buses to save up to PKR 2,500 this month.", time: "3 days ago", read: true },
    { id: "notif_5", type: "security", title: "Duplicate Transaction Detected", message: "A recurring charge of PKR 850 at Campus Cafe matched an earlier entry on Sep 23.", time: "4 days ago", read: true },
    { id: "notif_6", type: "success", title: "Savings Milestone Progress", message: "You have achieved 65% of your PKR 20,000 monthly savings goal.", time: "5 days ago", read: true }
  ];

  const DEFAULT_SETTINGS = {
    theme: "light",
    fontSize: "md",
    notifyBudget: true,
    notifyTips: true,
  };

  const DEFAULT_ADMIN = {
    totalStudents: 1480,
    activeUsers: 940,
    totalTransactions: 38450,
    mostUsedCategory: "Food",
    users: [
      { id: 1, name: "Ahmad Raza", email: "ahmad.raza@nu.edu.pk", campus: "FAST-NUCES Islamabad", year: "3rd Year", allowance: 50000, goal: 20000, status: "Active", transactionsCount: 21, joined: "Aug 2024" },
      { id: 2, name: "Fatima Noor", email: "fatima.noor@lums.edu.pk", campus: "LUMS Lahore", year: "2nd Year", allowance: 60000, goal: 25000, status: "Active", transactionsCount: 45, joined: "Sep 2024" },
      { id: 3, name: "Bilal Tariq", email: "bilal.t@nust.edu.pk", campus: "NUST Islamabad", year: "4th Year", allowance: 40000, goal: 15000, status: "Active", transactionsCount: 32, joined: "Jan 2024" },
      { id: 4, name: "Zainab Ali", email: "zainab.ali@iba.edu.pk", campus: "IBA Karachi", year: "1st Year", allowance: 45000, goal: 18000, status: "Active", transactionsCount: 18, joined: "Feb 2025" },
      { id: 5, name: "Hamza Khan", email: "hamza.k@giki.edu.pk", campus: "GIKI Topi", year: "3rd Year", allowance: 55000, goal: 22000, status: "Disabled", transactionsCount: 9, joined: "Oct 2024" }
    ],
    announcements: [
      { id: 1, title: "Semester Mess Dues Deadline", message: "Reminder: Hostel mess dues for October must be cleared before the 5th to avoid late fines.", priority: "High", date: "Sep 24, 2026", author: "Warden Office" },
      { id: 2, title: "HEC Digital Library Access Renewed", message: "All enrolled students can now download IEEE and Springer book chapters free through university WiFi.", priority: "Normal", date: "Sep 20, 2026", author: "Central Library" }
    ],
    tipTemplates: [
      { id: 1, title: "Canteen Meal Bundles", category: "Food", saving: "PKR 2,400/mo", text: "Buy weekly cafe meal coupons in bulk rather than individual daily snacks." },
      { id: 2, title: "Campus Shuttle Pass", category: "Transport", saving: "PKR 3,500/mo", text: "Subscribe to university point buses instead of daily Careem / InDrive rides." },
      { id: 3, title: "Senior Book Exchange", category: "Academics", saving: "PKR 4,000/sem", text: "Acquire curriculum textbooks from graduating seniors at 70% discount." },
      { id: 4, title: "Student Streaming Discount", category: "Subscriptions", saving: "PKR 800/mo", text: "Sign up using your university .edu.pk email for 50% discount on Spotify & YouTube." }
    ],
    flaggedTransactions: [
      { id: "FLG-101", student: "Ahmad Raza", email: "ahmad.raza@nu.edu.pk", desc: "Computer Peripherals & RAM", amount: 16500, category: "Academics", reason: "Unusually large single expense (> PKR 10,000 threshold)", date: "Sep 22, 2026", status: "Pending Review" },
      { id: "FLG-102", student: "Bilal Tariq", email: "bilal.t@nust.edu.pk", desc: "Campus Cafe Lunch", amount: 850, category: "Food", reason: "Possible duplicate charge (2 identical charges within 4 minutes)", date: "Sep 23, 2026", status: "Flagged Duplicate" }
    ]
  };

  function initStorage() {
    if (!localStorage.getItem(STORAGE_KEYS.USER)) {
      localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(DEFAULT_USER));
    }
    if (!localStorage.getItem(STORAGE_KEYS.CATEGORIES)) {
      localStorage.setItem(STORAGE_KEYS.CATEGORIES, JSON.stringify(DEFAULT_CATEGORIES));
    }
    if (!localStorage.getItem(STORAGE_KEYS.SETTINGS)) {
      localStorage.setItem(STORAGE_KEYS.SETTINGS, JSON.stringify(DEFAULT_SETTINGS));
    }
  }

  initStorage();

  return {
    // User
    getUser: function() {
      return JSON.parse(localStorage.getItem(STORAGE_KEYS.USER)) || DEFAULT_USER;
    },
    saveUser: function(userData) {
      localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(userData));
    },

    // Categories
    getCategories: function() {
      return JSON.parse(localStorage.getItem(STORAGE_KEYS.CATEGORIES)) || DEFAULT_CATEGORIES;
    },
    saveCategories: function(catData) {
      localStorage.setItem(STORAGE_KEYS.CATEGORIES, JSON.stringify(catData));
    },
    addCategory: function(type, categoryObj) {
      const cats = this.getCategories();
      if (!cats[type]) cats[type] = [];
      categoryObj.id = type + '_' + Date.now();
      cats[type].push(categoryObj);
      this.saveCategories(cats);
      return categoryObj;
    },
    deleteCategory: function(type, id) {
      const cats = this.getCategories();
      if (cats[type]) {
        cats[type] = cats[type].filter(c => c.id !== id);
        this.saveCategories(cats);
      }
    },

    // Transactions
    getTransactions: function() {
      const value = localStorage.getItem(STORAGE_KEYS.TRANSACTIONS);
      return value ? JSON.parse(value) : [];
    },
    saveTransactions: function(list) {
      localStorage.setItem(STORAGE_KEYS.TRANSACTIONS, JSON.stringify(list));
    },
    addTransaction: function(tx) {
      const list = this.getTransactions();
      tx.id = Date.now();
      list.unshift(tx);
      this.saveTransactions(list);
      return tx;
    },
    updateTransaction: function(id, updatedTx) {
      const list = this.getTransactions();
      const idx = list.findIndex(t => t.id == id);
      if (idx !== -1) {
        list[idx] = { ...list[idx], ...updatedTx };
        this.saveTransactions(list);
        return true;
      }
      return false;
    },
    deleteTransaction: function(id) {
      let list = this.getTransactions();
      list = list.filter(t => t.id != id);
      this.saveTransactions(list);
    },
    getTransactionById: function(id) {
      const list = this.getTransactions();
      return list.find(t => t.id == id);
    },

    // Budgets
    getBudgets: function() {
      const value = localStorage.getItem(STORAGE_KEYS.BUDGETS);
      return value ? JSON.parse(value) : [];
    },
    saveBudgets: function(budgets) {
      localStorage.setItem(STORAGE_KEYS.BUDGETS, JSON.stringify(budgets));
    },
    updateBudget: function(categoryName, newLimit) {
      const budgets = this.getBudgets();
      const b = budgets.find(x => x.category.toLowerCase() === categoryName.toLowerCase());
      if (b) {
        b.limit = parseFloat(newLimit);
      } else {
        budgets.push({
          category: categoryName,
          limit: parseFloat(newLimit),
          icon: "bi-tag",
          color: "#0d9488"
        });
      }
      this.saveBudgets(budgets);
    },

    // Tips
    getTips: function() {
      const value = localStorage.getItem(STORAGE_KEYS.TIPS);
      return value ? JSON.parse(value) : [];
    },
    saveTips: function(tips) {
      localStorage.setItem(STORAGE_KEYS.TIPS, JSON.stringify(tips));
    },
    togglePinTip: function(id) {
      const tips = this.getTips();
      const tip = tips.find(t => t.id == id);
      if (tip) {
        tip.pinned = !tip.pinned;
        this.saveTips(tips);
        return tip.pinned;
      }
      return false;
    },
    dismissTip: function(id) {
      const tips = this.getTips();
      const tip = tips.find(t => t.id == id);
      if (tip) {
        tip.dismissed = true;
        this.saveTips(tips);
      }
    },

    // Notifications
    getNotifications: function() {
      return JSON.parse(localStorage.getItem(STORAGE_KEYS.NOTIFICATIONS)) || DEFAULT_NOTIFICATIONS;
    },
    saveNotifications: function(notifs) {
      localStorage.setItem(STORAGE_KEYS.NOTIFICATIONS, JSON.stringify(notifs));
    },
    markNotificationRead: function(id) {
      const list = this.getNotifications();
      const n = list.find(x => x.id == id);
      if (n) {
        n.read = true;
        this.saveNotifications(list);
      }
    },
    markAllNotificationsRead: function() {
      const list = this.getNotifications();
      list.forEach(n => n.read = true);
      this.saveNotifications(list);
    },
    getUnreadNotificationsCount: function() {
      const list = this.getNotifications();
      return list.filter(n => !n.read).length;
    },

    // Settings
    getSettings: function() {
      return JSON.parse(localStorage.getItem(STORAGE_KEYS.SETTINGS)) || DEFAULT_SETTINGS;
    },
    saveSettings: function(settings) {
      localStorage.setItem(STORAGE_KEYS.SETTINGS, JSON.stringify(settings));
    },

    // Admin
    getAdminData: function() {
      const data = JSON.parse(localStorage.getItem(STORAGE_KEYS.ADMIN_METRICS)) || DEFAULT_ADMIN;
      if (!data.announcements) data.announcements = DEFAULT_ADMIN.announcements;
      if (!data.tipTemplates) data.tipTemplates = DEFAULT_ADMIN.tipTemplates;
      if (!data.flaggedTransactions) data.flaggedTransactions = DEFAULT_ADMIN.flaggedTransactions;
      if (!data.users || !data.users[0].campus) data.users = DEFAULT_ADMIN.users;
      return data;
    },
    saveAdminData: function(data) {
      localStorage.setItem(STORAGE_KEYS.ADMIN_METRICS, JSON.stringify(data));
    },

    // Reset All Demo Data
    resetAll: function() {
      localStorage.clear();
      initStorage();
    }
  };
})();

/**
 * Shared CampusCoin Utility Functions
 */
const CampusCoin = {
  // Format PKR currency
  formatPKR: function(amount) {
    if (window.CampusCurrency) {
      return window.CampusCurrency.format(amount);
    }
    if (amount === undefined || amount === null || isNaN(amount)) return "PKR 0";
    return "PKR " + Number(amount).toLocaleString('en-PK', {
      maximumFractionDigits: 0
    });
  },

  // Format Date (e.g. 23 Sep 2026)
  formatDate: function(dateStr) {
    if (!dateStr) return "";
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    const date = new Date(dateStr);
    return isNaN(date.getTime()) ? dateStr : date.toLocaleDateString('en-GB', options);
  },

  // Display Toast Notification
  showToast: function(message, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
      container.style.zIndex = '1100';
      document.body.appendChild(container);
    }

    const toastId = 'toast_' + Date.now();
    const icons = {
      success: 'bi-check-circle-fill text-success',
      danger: 'bi-exclamation-octagon-fill text-danger',
      warning: 'bi-exclamation-triangle-fill text-warning',
      info: 'bi-info-circle-fill text-primary'
    };

    const toastHTML = `
      <div id="${toastId}" class="toast align-items-center custom-toast border-0 mb-2 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex p-2">
          <div class="toast-body d-flex align-items-center gap-2">
            <i class="bi ${icons[type] || icons.info} fs-5"></i>
            <div>${message}</div>
          </div>
          <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHTML);
    const toastEl = document.getElementById(toastId);
    if (typeof bootstrap !== 'undefined') {
      const bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
      bsToast.show();
      toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    } else {
      setTimeout(() => toastEl.remove(), 4000);
    }
  },

  toggleBookmark: async function(type, item) {
    const response = await fetch('/bookmarks/toggle', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': window.csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ type, item })
    });

    if (!response.ok) {
      throw new Error('Could not update bookmark');
    }

    return response.json();
  },

  // Permanent Dark Jungle & Gold Theme
  initTheme: function() {
    document.documentElement.setAttribute('data-bs-theme', 'dark');
    localStorage.setItem('cc_theme', 'dark');
  },

  // Initialize Font Size Accessibility
  initFontSize: function() {
    const settings = CampusDB.getSettings();
    const size = settings.fontSize || 'md';
    document.body.classList.remove('font-sm', 'font-md', 'font-lg');
    document.body.classList.add('font-' + size);
  },

  // Render Consistent Sidebar across all Student Portal pages
  renderSidebar: function(activePage) {
    const sidebarEl = document.getElementById('appSidebar');
    if (!sidebarEl) return;

    const user = window.currentUser || CampusDB.getUser();
    const userName = user.name || 'Student';
    const userAcademicYear = user.academic_year || user.academicYear || 'Student';
    const userInitials = userName.trim().split(/\s+/)
      .filter(Boolean)
      .map(part => part.charAt(0).toUpperCase())
      .slice(0, 2)
      .join('') || 'ST';
    const escapeHtml = value => String(value).replace(/[&<>"']/g, character => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    })[character]);
    const unreadCount = Number.isInteger(window.serverUnreadNotificationsCount)
      ? window.serverUnreadNotificationsCount
      : CampusDB.getUnreadNotificationsCount();

    const isSubdir = window.location.pathname.toLowerCase().includes('student-portal') ||
                     window.location.pathname.toLowerCase().includes('student%20portal') ||
                     window.location.pathname.toLowerCase().includes('student portal');
    const pathPrefix = isSubdir ? '' : 'student-portal/';
    const pageHref = page => `/${page}`;
    const brandHref = pageHref('dashboard');
    const logoutRoute = window.logoutRoute || '/logout';
    const csrfToken = window.csrfToken || '';

    const menuItems = [
      { id: 'dashboard', label: 'Dashboard', icon: 'bi-grid-1x2', href: pageHref('dashboard') },
      { id: 'transactions', label: 'Transactions', icon: 'bi-arrow-left-right', href: pageHref('transactions') },
      { id: 'add-transaction', label: 'Add Transaction', icon: 'bi-plus-circle', href: pageHref('add-transaction') },
      { id: 'budgets', label: 'Budgets', icon: 'bi-pie-chart', href: pageHref('budgets') },
      { id: 'reports', label: 'Reports', icon: 'bi-file-earmark-bar-graph', href: pageHref('reports') },
      { id: 'saving-tips', label: 'Saving Tips', icon: 'bi-lightbulb', href: pageHref('saving-tips') },
      { id: 'categories', label: 'Categories', icon: 'bi-tags', href: pageHref('categories') },
      { id: 'bookmarks', label: 'Bookmarks', icon: 'bi-bookmark-star', href: pageHref('bookmarks') },
      { id: 'notifications', label: 'Notifications', icon: 'bi-bell', href: pageHref('notifications'), count: unreadCount },
      { id: 'profile', label: 'Student Profile', icon: 'bi-person-circle', href: pageHref('profile') },
      { id: 'settings', label: 'Settings', icon: 'bi-gear', href: pageHref('settings') }
    ];

    const linksHtml = menuItems.map(item => {
      const isActive = item.id === activePage ? 'active' : '';
      let badgeHtml = '';
      if (item.badge) {
        badgeHtml = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle sidebar-badge">${item.badge}</span>`;
      } else if (item.count > 0) {
        badgeHtml = `<span class="badge bg-danger sidebar-badge">${item.count}</span>`;
      }
      return `
        <li class="sidebar-item">
          <a href="${item.href}" class="sidebar-link ${isActive}">
            <i class="bi ${item.icon}"></i>
            <span>${item.label}</span>
            ${badgeHtml}
          </a>
        </li>
      `;
    }).join('');

    sidebarEl.innerHTML = `
      <div class="sidebar-header">
        <a href="${brandHref}" class="sidebar-brand">
          <div class="brand-icon-box">
            <i class="bi bi-coin"></i>
          </div>
          <div class="brand-logo-text">
            Campus<span class="brand-highlight">Coin</span>
          </div>
        </a>
        <button class="btn btn-sm btn-link d-lg-none text-white p-0" id="closeSidebarBtn" title="Close Menu">
          <i class="bi bi-x-lg fs-5"></i>
        </button>
      </div>

      <ul class="sidebar-menu">
        <li class="sidebar-label">Main Menu</li>
        ${linksHtml}
      </ul>

      <div class="sidebar-footer">
        <div class="user-profile-summary">
          <div class="user-avatar-circle user-avatar-sm">${userInitials}</div>
          <div class="overflow-hidden flex-grow-1">
            <div class="user-name text-truncate">${escapeHtml(userName)}</div>
            <div class="user-sub">${escapeHtml(userAcademicYear)}</div>
          </div>
        </div>

        <form method="POST" action="${logoutRoute}" class="mt-3 w-100">
          <input type="hidden" name="_token" value="${csrfToken}">
          <button type="submit" class="btn btn-logout w-100">
            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
          </button>
        </form>
      </div>
    `;

    // Setup mobile drawer close button listener
    const closeBtn = document.getElementById('closeSidebarBtn');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (closeBtn) {
      closeBtn.onclick = function() {
        sidebarEl.classList.remove('show-mobile');
        if (backdrop) backdrop.classList.remove('show');
      };
    }

    // Auto-close drawer on mobile when any sidebar navigation link is clicked
    const links = sidebarEl.querySelectorAll('.sidebar-link');
    links.forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth < 992) {
          sidebarEl.classList.remove('show-mobile');
          const bd = document.getElementById('sidebarBackdrop');
          if (bd) bd.classList.remove('show');
        }
      });
    });
  },

  // Setup Mobile Nav Toggles
  setupMobileNav: function() {
    const toggleBtn = document.getElementById('mobileNavToggle');
    const sidebar = document.getElementById('appSidebar');
    let backdrop = document.getElementById('sidebarBackdrop');

    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.id = 'sidebarBackdrop';
      backdrop.className = 'sidebar-backdrop';
      document.body.appendChild(backdrop);
    }

    if (toggleBtn && sidebar) {
      toggleBtn.onclick = function() {
        sidebar.classList.toggle('show-mobile');
        backdrop.classList.toggle('show');
      };
    }

    if (backdrop && sidebar) {
      backdrop.onclick = function() {
        sidebar.classList.remove('show-mobile');
        backdrop.classList.remove('show');
      };
    }
  },

  // Initialize Global App Behavior on DOM Ready
  init: function(activePage) {
    this.initTheme();
    this.initFontSize();
    this.renderSidebar(activePage);
    this.setupMobileNav();
  }
};

// Auto-run common initialization when DOM loads
document.addEventListener('DOMContentLoaded', function() {
  CampusCoin.initTheme();
  CampusCoin.initFontSize();
});
