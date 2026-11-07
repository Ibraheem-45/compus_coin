<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Campus Coin — Smart Spending, Student Style</title>
<meta name="description" content="Campus Coin is a student-first budget and expense tracker for college and university students — log income and expenses, set budgets, get AI-powered insights, and build smarter money habits.">
<link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap'>
<link rel='stylesheet' href="{{asset('css/home.css')}}">
<script src="{{asset('js/home.js')}}" defer></script>
<script src='https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/lenis@1.3.13/dist/lenis.min.js'></script>
</head>
<body>

<canvas class="cursor-canvas" id="cursor-canvas"></canvas>

<header class="running-head">
  <span>CAMPUS COIN</span>
  <span>STUDENT FINANCE · MMXXVI</span>
  <span>P. <span id="page-num">008</span></span>
</header>

<!-- ============ NAVBAR ============ -->
<header class="home-navbar">
  <div class="home-nav-container">
    <a href="{{ url('/') }}" class="home-logo">
      <div class="home-logo-mark">+</div>
      <div class="home-logo-text">
        <strong>Campus<span>Coin</span></strong>
        <small>STUDENT FINANCE</small>
      </div>
    </a>

    <nav class="home-nav-links">
      <a href="#features">Features</a>
      <a href="#categories">Categories</a>
      <a href="#how-it-works">How It Works</a>
      <a href="#insights">Insights</a>
      <a href="#faq">FAQ</a>
    </nav>

    <div class="home-nav-actions">
      @auth
        @if(auth()->user()->role === 'admin')
          <a href="{{ route('admin.dashboard') }}" class="home-login-btn">Admin Panel</a>
        @else
          <a href="{{ route('dashboard') }}" class="home-login-btn">Dashboard</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="home-logout-form">
          @csrf
          <button type="submit" class="home-nav-cta">Sign Out <span>→</span></button>
        </form>
      @else
        <a href="{{ route('login') }}" class="home-login-btn">Login</a>
        <a href="{{ route('register') }}" class="home-nav-cta">Register <span>→</span></a>
      @endauth
    </div>

    <button class="home-menu-btn" type="button" aria-label="Open navigation">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<main>

<!-- ============ HERO ============ -->
<section class="hero" id="hero">
  <div class="hero__meta-top">
    <span>VOL. I / STUDENT FINANCE</span>
    <span>FREE FOR STUDENTS · NO BANK LINK REQUIRED</span>
    <span>MMXXVI</span>
  </div>

  <div class="hero__title">
    <div class="hero__name hero__name--line1">
      <span>C</span><span>A</span><span>M</span><span>P</span><span>U</span><span>S</span>
    </div>
    <div class="hero__name hero__name--line2">
      <span>C</span><span>O</span><span>I</span><span>N</span>
    </div>
    <div class="hero__byline">
      <span class="hero__rule"></span>
      <span>Track · Budget · Save · Understand</span>
      <span class="hero__rule"></span>
    </div>
  </div>

  <div class="hero__meta-bottom">
    <span>SMART SPENDING, STUDENT STYLE</span>
    <span class="hero__scroll-cue">↓ BEGIN READING</span>
    <span>SET IN CORMORANT &amp; INTER</span>
  </div>
</section>

<!-- ============ DIVIDER I ============ -->
<div class="divider">
  <span class="divider__roman">I</span>
  <span>— Preface —</span>
</div>

<!-- ============ MANIFESTO ============ -->
<section class="manifesto reveal">
  <div class="manifesto__header">
    <span class="eyebrow">A NOTE FROM THE STUDIO</span>
    <h2 class="manifesto__title">Student money, <em>finally</em> understood.</h2>
  </div>

  <div class="manifesto__body">
    <p>College and university students routinely receive money from multiple, irregular sources — monthly allowance from family, part-time or gig work, scholarships, and the occasional gift — yet rarely track where that money actually goes. Generic personal-finance apps are built for salaried adults with fixed pay cycles and bank integrations. They are too complex, subscription-gated, or simply irrelevant to a student's spending patterns: canteen food, hostel expenses, textbooks, transport, subscriptions, and social outings.</p>

    <p>Campus Coin exists to close that gap. It is a <em>lightweight, student-first</em> web application that makes it effortless to log income and expenses, view spending by category, and receive plain-language guidance on how to save more — without requiring a linked bank account or any financial expertise.</p>

    <p>Every transaction is stored in a central database, which powers category-wise breakdowns, month-over-month trend charts, and a personalized tips engine that reacts to the student's own habits. An optional AI assistant can categorize expenses automatically as they are entered and generate a friendly, actionable monthly insight summary — for example, flagging that food-delivery spending rose sharply, and suggesting a simple, achievable adjustment.</p>

    <p>What follows is a complete tour of the system: the dashboard, the categories, the budgets, the AI insights, and the security that holds it together. Built with modern front-end technologies and a robust database layer, Campus Coin is designed for individual students, hostel communities, and campus financial-literacy initiatives.</p>
  </div>

  <div class="manifesto__sig">
    Campus Coin<br>
    <span style="font-size: 0.85rem; opacity: 0.6; font-style: normal; font-family: var(--mono); letter-spacing: 0.2em; text-transform: uppercase; color: var(--ivory);">Student Finance Workspace · Version 1.0 · MMXXVI</span>
  </div>
</section>

<!-- ============ DIVIDER II ============ -->
<div class="divider">
  <span class="divider__roman">II</span>
  <span>— Contents —</span>
</div>

<!-- ============ INDEX ============ -->
<section class="index reveal" id="contents">
  <div class="index__header">
    <span class="eyebrow">FIVE MODULES, ONE WORKSPACE</span>
    <h2 class="index__title">Contents</h2>
  </div>

  <ul class="index__list">
    <li class="index__entry">
      <span class="index__num">01 ——</span>
      <span class="index__name">Dashboard &amp; Logging</span>
      <span class="index__cat">Income · Expenses · Quick Add</span>
      <span class="index__page">P. 042</span>
    </li>
    <li class="index__entry">
      <span class="index__num">02 ——</span>
      <span class="index__name">Categories &amp; Budgets</span>
      <span class="index__cat">Manage · Set Caps · Track</span>
      <span class="index__page">P. 068</span>
    </li>
    <li class="index__entry">
      <span class="index__num">03 ——</span>
      <span class="index__name">Budgets &amp; Alerts</span>
      <span class="index__cat">Monthly Caps · Progress Bars</span>
      <span class="index__page">P. 094</span>
    </li>
    <li class="index__entry">
      <span class="index__num">04 ——</span>
      <span class="index__name">AI Insights &amp; Tips</span>
      <span class="index__cat">Categorization · Saving Advice</span>
      <span class="index__page">P. 122</span>
    </li>
    <li class="index__entry">
      <span class="index__num">05 ——</span>
      <span class="index__name">Security &amp; Admin</span>
      <span class="index__cat">Auth · Privacy · Oversight</span>
      <span class="index__page">P. 158</span>
    </li>
  </ul>
</section>

<!-- ============ DIVIDER III ============ -->
<div class="divider">
  <span class="divider__roman">III</span>
  <span>— Selected Modules —</span>
</div>

<!-- ============ SPREAD 01 — DASHBOARD ============ -->
<section class="spread spread--dashboard" data-spread="01" id="features">
  <div class="spread__inner">
    <div class="spread__head">
      <span>CAMPUS COIN / SRS v1.0</span>
      <span>CHAPTER 01 — DASHBOARD &amp; LOGGING</span>
      <span>P. 042—067</span>
    </div>

    <div class="spread__chapter-wrap">
      <div class="spread__chapter">01</div>
      <div class="spread__chapter-label">
        Dashboard &amp; Logging
        <small>Income · Expenses · Quick Add · Recurring</small>
      </div>
    </div>

    <h3 class="spread__title">Every rupee, logged in seconds.</h3>
    <p class="spread__sub">A personalized greeting, current-month balance, and quick-add buttons for income and expenses — designed so a student can record a transaction before the chai gets cold.</p>

    <div class="spread__meta">
      <div class="spread__meta-item">
        <span class="label">Module</span>
        <span class="value">Dashboard &amp; Logging</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Version</span>
        <span class="value">1.0</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Scope</span>
        <span class="value">Income, Expenses, Recurring</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Access</span>
        <span class="value">Registered Students</span>
      </div>
    </div>

    <div class="spread__layout">
      <div class="spread__col-text">
        <div class="spread__body">
          <p>The dashboard is the first thing a student sees after login: a personalized greeting, the current month's balance comparing income against expenses, and quick-add buttons for the most common transaction types. No digging, no setup, no bank link.</p>

          <p>Income entries cover <em>allowance, part-time job earnings, scholarships, gifts, and other income</em>. Expense entries cover <em>food, transport, hostel/rent, academics, subscriptions, entertainment, and miscellaneous</em>. Recurring entries — monthly allowances and subscription charges — can be set once and repeated automatically.</p>

          <div class="spread__pullquote">A transaction you can log in five seconds is a transaction that actually gets logged.</div>

          <p>Dynamic widgets sit alongside: "This Month's Top Category" and "Budget vs. Actual" update in real time as entries are added. Every edit and deletion retains the full history, so the record is never silently rewritten.</p>
        </div>
      </div>

      <div class="spread__col-visual">
        {{-- ✅ CAMPUS COIN BRANDED VISUAL — Dashboard Mockup --}}
        <div class="finance-mockup">
          <span class="finance-mockup__corner finance-mockup__corner--tl">— PL. 01.A</span>
          <span class="finance-mockup__corner finance-mockup__corner--tr">DASHBOARD</span>
          <span class="finance-mockup__corner finance-mockup__corner--bl">CAMPUS COIN</span>
          <span class="finance-mockup__corner finance-mockup__corner--br">MMXXVI</span>

          <div class="finance-mockup__header">
            <div>
              <span class="finance-mockup__label">AVAILABLE BALANCE</span>
              <strong class="finance-mockup__amount">Rs. 18,450</strong>
            </div>
            <div class="finance-mockup__icon">↗</div>
          </div>

          <div class="finance-mockup__chart">
            <div class="finance-mockup__chart-fill"></div>
            <svg viewBox="0 0 420 120" preserveAspectRatio="none" aria-hidden="true">
              <path d="M0 94 C35 90, 45 76, 70 80 S105 88, 125 65 S165 54, 185 61 S215 78, 235 52 S270 37, 290 45 S320 60, 340 33 S380 26, 420 12"/>
            </svg>
            <span class="finance-mockup__point" style="top: 78%; left: 16%;"></span>
            <span class="finance-mockup__point" style="top: 52%; left: 42%;"></span>
            <span class="finance-mockup__point" style="top: 38%; left: 68%;"></span>
            <span class="finance-mockup__point" style="top: 12%; left: 94%;"></span>
          </div>

          <div class="finance-mockup__months">
            <span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span>
          </div>

          <div class="finance-mockup__widgets">
            <div class="finance-mockup__widget">
              <span class="finance-mockup__widget-label">TOP CATEGORY</span>
              <strong>Food</strong>
              <span class="finance-mockup__widget-value">Rs. 3,240</span>
            </div>
            <div class="finance-mockup__widget">
              <span class="finance-mockup__widget-label">BUDGET USED</span>
              <strong>68%</strong>
              <div class="finance-mockup__bar"><div style="width: 68%;"></div></div>
            </div>
          </div>
        </div>

        <div class="spread__caption">
          <span>Pl. 01.A — Dashboard mockup, balance &amp; budget widgets</span>
          <span>Source: Campus Coin SRS v1.0</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ SPREAD 02 — CATEGORIES ============ -->
<section class="spread spread--categories" data-spread="02" id="categories">
  <div class="spread__inner">
    <div class="spread__head">
      <span>CAMPUS COIN / SRS v1.0</span>
      <span>CHAPTER 02 — CATEGORIES &amp; BUDGETS</span>
      <span>P. 068—093</span>
    </div>

    <div class="spread__chapter-wrap">
      <div class="spread__chapter">02</div>
      <div class="spread__chapter-label">
        Categories &amp; Budgets
        <small>Income · Expenses · Manage Own Categories</small>
      </div>
    </div>

    <h3 class="spread__title">Categories built for student life, not salaried adulthood.</h3>
    <p class="spread__sub">Default income and expense categories cover the realities of campus spending — and every registered student can add, edit, or delete their own under "Manage Own Categories."</p>

    <div class="spread__meta">
      <div class="spread__meta-item">
        <span class="label">Income</span>
        <span class="value">Allowance, Job, Scholarship</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Expense</span>
        <span class="value">Food, Transport, Hostel</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Custom</span>
        <span class="value">Add · Edit · Delete</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Type</span>
        <span class="value">income / expense</span>
      </div>
    </div>

    <div class="spread__layout">
      {{-- ✅ CAMPUS COIN CATEGORY VISUAL — Category Grid --}}
      <div class="category-visual">
        <div class="category-visual__header">
          <span class="category-visual__eyebrow">DEFAULT CATEGORY SET</span>
          <span class="category-visual__count">12 CATEGORIES</span>
        </div>

        <div class="category-visual__section">
          <span class="category-visual__section-label">INCOME</span>
          <div class="category-visual__grid">
            <div class="category-visual__item">
              <span class="category-visual__num">01</span>
              <strong>Allowance</strong>
              <small>Monthly · Family</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">02</span>
              <strong>Part-time Job</strong>
              <small>Gig · Shift</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">03</span>
              <strong>Scholarship</strong>
              <small>Merit · Grant</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">04</span>
              <strong>Gift</strong>
              <small>Occasional</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">05</span>
              <strong>Other Income</strong>
              <small>Additional</small>
            </div>
          </div>
        </div>

        <div class="category-visual__section">
          <span class="category-visual__section-label">EXPENSE</span>
          <div class="category-visual__grid">
            <div class="category-visual__item">
              <span class="category-visual__num">01</span>
              <strong>Food</strong>
              <small>Canteen · Cafe</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">02</span>
              <strong>Transport</strong>
              <small>Commute</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">03</span>
              <strong>Hostel / Rent</strong>
              <small>Living</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">04</span>
              <strong>Academics</strong>
              <small>Books · Fees</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">05</span>
              <strong>Subscriptions</strong>
              <small>Streaming · Apps</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">06</span>
              <strong>Entertainment</strong>
              <small>Movies · Outings</small>
            </div>
            <div class="category-visual__item">
              <span class="category-visual__num">07</span>
              <strong>Miscellaneous</strong>
              <small>Other</small>
            </div>
          </div>
        </div>

        <div class="spread__caption">
          <span>Pl. 02.A — Default category set from SRS</span>
          <span>Income · Expense · Custom</span>
        </div>
      </div>

      <div class="spread__col-text">
        <div class="spread__body">
          <p>Income categories ship as <em>Allowance, Part-time Job, Scholarship, Gift, and Other Income</em>. Expense categories ship as <em>Food, Transport, Hostel/Rent, Academics (books, tuition, stationery), Subscriptions (streaming, apps), Entertainment (movies, outings), and Miscellaneous</em>.</p>

          <p>Registered users can create their personal categories under both types, and manage them under "Manage Own Categories" — add, edit, and delete without ever touching another student's data.</p>

          <div class="spread__pullquote">The category list is the spine of the whole system. Everything else — reports, budgets, insights — hangs from it.</div>

          <p>Each category carries a type flag (income or expense) and a default flag distinguishing system categories from user-created ones. This is what allows the personalized tips engine to compare a student's spending against their own historical averages, category by category.</p>
        </div>

        <div class="bottle-spec">
          <div class="bottle-spec__item">
            <span class="label">Default Income</span>
            <span class="value">5 categories</span>
          </div>
          <div class="bottle-spec__item">
            <span class="label">Default Expense</span>
            <span class="value">7 categories</span>
          </div>
          <div class="bottle-spec__item">
            <span class="label">Custom</span>
            <span class="value">Unlimited, per user</span>
          </div>
          <div class="bottle-spec__item">
            <span class="label">Scope</span>
            <span class="value">Private to account</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ SPREAD 03 — BUDGETS ============ -->
<section class="spread spread--budgets" data-spread="03" id="how-it-works">
  <div class="spread__inner">
    <div class="spread__head">
      <span>CAMPUS COIN / SRS v1.0</span>
      <span>CHAPTER 03 — BUDGETS &amp; ALERTS</span>
      <span>P. 094—121</span>
    </div>

    <div class="spread__chapter-wrap">
      <div class="spread__chapter">03</div>
      <div class="spread__chapter-label">
        Budgets &amp; Alerts
        <small>Monthly Caps · Progress Bars · Notifications</small>
      </div>
    </div>

    <h3 class="spread__title">A monthly cap per category, shown in real time.</h3>
    <p class="spread__sub">Set a budget per category — food at 30 USD, transport at 15, subscriptions at 10 — and watch consumption fill the bar. In-app notifications fire when a category nears or exceeds its cap.</p>

    <div class="spread__meta">
      <div class="spread__meta-item">
        <span class="label">Granularity</span>
        <span class="value">Per category, per month</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Display</span>
        <span class="value">Real-time progress bars</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Alert</span>
        <span class="value">Near / Over budget</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Storage</span>
        <span class="value">Budget entity, per user</span>
      </div>
    </div>

    <div class="spread__body">
      <p>A budget is a cap set by the student for a category in a given month. The system stores the limit, compares it against actual transactions, and renders consumption as a progress bar that updates in real time.</p>
    </div>

    <div class="poster-wall">
      <div class="poster-card">
        <span class="poster-card__num">N. 01 / FOOD</span>
        <div class="poster-card__type" style="font-size: 1.4rem;">FOOD</div>
        <div class="poster-card__type" style="font-size: 1.4rem;">30 USD</div>
        <div class="poster-card__bar"><div style="width: 32%;"></div></div>
        <div class="poster-card__footer"><span>32% USED</span><span>CAP</span></div>
      </div>
      <div class="poster-card">
        <span class="poster-card__num">N. 02 / TRANSPORT</span>
        <div class="poster-card__type" style="font-size: 1.4rem;">TRANS</div>
        <div class="poster-card__type" style="font-size: 1.4rem;">PORT</div>
        <div class="poster-card__bar"><div style="width: 48%;"></div></div>
        <div class="poster-card__footer"><span>48% USED</span><span>CAP</span></div>
      </div>
      <div class="poster-card">
        <span class="poster-card__num">N. 03 / ACADEMICS</span>
        <div class="poster-card__type" style="font-size: 1.4rem;">ACAD</div>
        <div class="poster-card__type" style="font-size: 1.4rem;">EMICS</div>
        <div class="poster-card__bar"><div style="width: 31%;"></div></div>
        <div class="poster-card__footer"><span>31% USED</span><span>CAP</span></div>
      </div>
      <div class="poster-card">
        <span class="poster-card__num">N. 04 / SUBS</span>
        <div class="poster-card__type" style="font-size: 1.4rem;">SUB</div>
        <div class="poster-card__type" style="font-size: 1.4rem;">SCRIPT</div>
        <div class="poster-card__bar"><div style="width: 78%;"></div></div>
        <div class="poster-card__footer"><span>78% USED</span><span>CAP</span></div>
      </div>
      <div class="poster-card">
        <span class="poster-card__num">N. 05 / HOSTEL</span>
        <div class="poster-card__type" style="font-size: 1.4rem;">HOS</div>
        <div class="poster-card__type" style="font-size: 1.4rem;">TEL</div>
        <div class="poster-card__bar poster-card__bar--over"><div style="width: 100%;"></div></div>
        <div class="poster-card__footer"><span>100% USED</span><span>CAP</span></div>
      </div>
      <div class="poster-card">
        <span class="poster-card__num">N. 06 / ENTMT</span>
        <div class="poster-card__type" style="font-size: 1.4rem;">ENTER</div>
        <div class="poster-card__type" style="font-size: 1.4rem;">TAIN</div>
        <div class="poster-card__bar"><div style="width: 55%;"></div></div>
        <div class="poster-card__footer"><span>55% USED</span><span>CAP</span></div>
      </div>
      <div class="poster-card">
        <span class="poster-card__num">N. 07 / MISC</span>
        <div class="poster-card__type" style="font-size: 1.4rem;">MIS</div>
        <div class="poster-card__type" style="font-size: 1.4rem;">CELL</div>
        <div class="poster-card__bar"><div style="width: 22%;"></div></div>
        <div class="poster-card__footer"><span>22% USED</span><span>CAP</span></div>
      </div>
      <div class="poster-card poster-card--alert">
        <span class="poster-card__num">N. 08 / ALERT</span>
        <div class="poster-card__type" style="font-size: 1.4rem;">OVER</div>
        <div class="poster-card__type" style="font-size: 1.4rem;">BUDGET</div>
        <div class="poster-card__bar poster-card__bar--over"><div style="width: 100%;"></div></div>
        <div class="poster-card__footer"><span>NOTIFY</span><span>NOW</span></div>
      </div>
    </div>

    <div class="spread__pullquote">A budget you can see filling up is a budget you actually respect. A number buried three menus deep is a number you ignore.</div>

    <div class="spread__caption">
      <span>Pl. 03.A–H — Selected budget categories from the default set</span>
      <span>Real-time progress · In-app alerts</span>
    </div>
  </div>
</section>

<!-- ============ SPREAD 04 — AI INSIGHTS ============ -->
<section class="spread spread--ai" data-spread="04" id="insights">
  <div class="spread__inner">
    <div class="spread__head">
      <span>CAMPUS COIN / SRS v1.0</span>
      <span>CHAPTER 04 — AI INSIGHTS &amp; TIPS</span>
      <span>P. 122—157</span>
    </div>

    <div class="spread__chapter-wrap">
      <div class="spread__chapter">04</div>
      <div class="spread__chapter-label">
        AI Insights &amp; Tips
        <small>Categorization · Monthly Summaries · Saving Engine</small>
      </div>
    </div>

    <h3 class="spread__title">An assistant that reads your month back to you in plain language.</h3>
    <p class="spread__sub">Optional AI categorizes expenses as you type — "Campus Cafe" becomes Food — and generates a short monthly narrative: what rose, what fell, and one simple adjustment to try next month.</p>

    <div class="spread__meta">
      <div class="spread__meta-item">
        <span class="label">Categorization</span>
        <span class="value">AI/ML suggestion, manual override</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Learning</span>
        <span class="value">From student corrections</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Summary</span>
        <span class="value">Plain-language, monthly</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Nature</span>
        <span class="value">Advisory, not financial advice</span>
      </div>
    </div>

    {{-- ✅ CAMPUS COIN AI INSIGHT VISUAL --}}
    <div class="specimen-stage">
      <span class="specimen-stage__corner specimen-stage__corner--tl">— PLATE 04.A —</span>
      <span class="specimen-stage__corner specimen-stage__corner--br">INSIGHT · MONTHLY</span>

      <div class="ai-insight">
        <div class="ai-insight__header">
          <span class="ai-insight__eyebrow">MONTHLY INSIGHT · SEPTEMBER 2026</span>
          <span class="ai-insight__badge">+12.4%</span>
        </div>

        <div class="ai-insight__message">
          <div class="ai-insight__avatar">Ai</div>
          <div>
            <strong class="ai-insight__title">Your spending snapshot</strong>
            <p class="ai-insight__text">Your food delivery spending rose 40% this month. Consider a weekly cap of Rs. 1,500 to keep it under control.</p>
          </div>
        </div>

        <div class="ai-insight__bars">
          <div class="ai-insight__bar-row">
            <span>Food</span>
            <div class="ai-insight__bar"><div style="width: 78%;"></div></div>
            <b>78%</b>
          </div>
          <div class="ai-insight__bar-row">
            <span>Transport</span>
            <div class="ai-insight__bar"><div style="width: 48%;"></div></div>
            <b>48%</b>
          </div>
          <div class="ai-insight__bar-row">
            <span>Academics</span>
            <div class="ai-insight__bar"><div style="width: 31%;"></div></div>
            <b>31%</b>
          </div>
        </div>
      </div>
    </div>

    <div class="spread__body" style="max-width: 760px;">
      <p>The optional AI assistant does two things. First, it <em>suggests a category</em> as the student types a transaction description — for example, "Campus Cafe" becomes Food. It learns from the student's corrections over time, and any suggestion can be manually overridden. Batch suggestions are also offered when importing a CSV history.</p>

      <p>Second, it <em>generates a monthly insight</em>: a short, plain-language narrative highlighting notable patterns. It flags categories with above-average growth compared to the student's own trend — for example, "Food delivery spending rose 40% this month" — and pairs each flag with a simple, actionable suggestion such as a weekly cap or a lower-cost alternative. Every insight is stored, so past months can be reviewed.</p>

      <p>The personalized saving tips engine is separate but complementary: it compares current spending against historical averages and set budget goals, ranks tips by potential savings impact, and surfaces the top few on the dashboard. Tips can be dismissed or pinned.</p>

      <p><em>Important:</em> all AI-generated categorization and insight is advisory. It is presented as a suggestion the student can review or override — never as certified financial advice.</p>
    </div>

    <div class="type-features">
      <div class="type-features__item">
        <span class="num">2</span>
        <span class="label">AI Functions</span>
      </div>
      <div class="type-features__item">
        <span class="num">1</span>
        <span class="label">Adjustment Suggested</span>
      </div>
      <div class="type-features__item">
        <span class="num">∞</span>
        <span class="label">History Retained</span>
      </div>
      <div class="type-features__item">
        <span class="num">0</span>
        <span class="label">Bank Links Required</span>
      </div>
    </div>
  </div>
</section>

<!-- ============ SPREAD 05 — SECURITY ============ -->
<section class="spread spread--security" data-spread="05">
  <div class="spread__inner">
    <div class="spread__head">
      <span>CAMPUS COIN / SRS v1.0</span>
      <span>CHAPTER 05 — SECURITY &amp; ADMIN</span>
      <span>P. 158—197</span>
    </div>

    <div class="spread__chapter-wrap">
      <div class="spread__chapter">05</div>
      <div class="spread__chapter-label">
        Security &amp; Admin
        <small>Authentication · Privacy · Admin Control Panel</small>
      </div>
    </div>

    <h3 class="spread__title">Your records, your account, nobody else's.</h3>
    <p class="spread__sub">Student registration and login, separate admin access, secure sessions, password recovery — and an admin control panel for categories, users, and system-wide statistics.</p>

    <div class="spread__meta">
      <div class="spread__meta-item">
        <span class="label">Auth</span>
        <span class="value">Student + Admin, separate</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Sessions</span>
        <span class="value">Secure, per account</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Recovery</span>
        <span class="value">Email / tokenized link</span>
      </div>
      <div class="spread__meta-item">
        <span class="label">Privacy</span>
        <span class="value">Only owner sees own history</span>
      </div>
    </div>

    <div class="spread__layout">
      <div class="spread__col-visual">
        {{-- ✅ CAMPUS COIN SECURITY VISUAL — Access Ledger --}}
        <div class="security-visual">
          <div class="security-visual__header">
            <span class="security-visual__label">ACCESS MODEL</span>
            <span class="security-visual__badge">SECURE</span>
          </div>

          <div class="security-visual__row">
            <span class="security-visual__icon">🔒</span>
            <div>
              <strong>Student Login</strong>
              <small>Email + password · own ledger only</small>
            </div>
          </div>

          <div class="security-visual__row">
            <span class="security-visual__icon">🔑</span>
            <div>
              <strong>Admin Access</strong>
              <small>Separate login · categories &amp; users</small>
            </div>
          </div>

          <div class="security-visual__row">
            <span class="security-visual__icon">↺</span>
            <div>
              <strong>Password Recovery</strong>
              <small>Email / tokenized link</small>
            </div>
          </div>

          <div class="security-visual__row">
            <span class="security-visual__icon">✓</span>
            <div>
              <strong>Private Ledger</strong>
              <small>Only owner sees own history</small>
            </div>
          </div>

          <div class="security-visual__footer">
            <span>CAMPUS COIN</span>
            <span>P. 158</span>
          </div>
        </div>
        <div class="spread__caption">
          <span>Pl. 05.A — Access model &amp; admin oversight</span>
          <span>Private per student · Oversight for admin</span>
        </div>
      </div>

      <div class="spread__col-text">
        <div class="spread__body">
          <p>The security model is straightforward: <em>authentication is the first gate</em>. Students register and log in; administrators have a separate, direct-access login. Sessions are managed securely, and password recovery runs through email verification or a tokenized link.</p>

          <p>Each user profile carries editable fields — name, academic year, monthly allowance baseline, and savings goal — and optionally allows bulk import of historical transactions from a CSV file.</p>

          <div class="spread__pullquote">Only registered users can access their own transaction history. A student's ledger is theirs alone; the admin oversees the system, not the student.</div>

          <p>The admin control panel manages default income and expense categories, system-wide announcement and tip templates, and user accounts — view, disable, or reset. It also surfaces usage statistics: active users, total transactions logged, and most-used categories.</p>

          <p>On the non-functional side, the SRS commits to: safe use, accessibility, user-friendliness for first-time users, reliable operability, high performance when rendering charts and AI insights, scalability, adequate security, 24/7 availability, and compatibility with the latest browsers and devices.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ DIVIDER IV ============ -->
<div class="divider">
  <span class="divider__roman">IV</span>
  <span>— Categories, Loose on the Desk —</span>
</div>

<!-- ============ CATEGORY SPECIMENS — DRAGGABLE ============ -->
<div class="section-head reveal">
  <div class="section-head__num">— IV —</div>
  <h2 class="section-head__title">Categories, <em>loose on the desk.</em></h2>
  <p class="section-head__sub">The default income and expense categories from the Campus Coin SRS. Drag to rearrange. Pinch — or scroll while holding — to scale.</p>
</div>

<section class="specimens">
  <div class="specimens__desk" id="specimens-desk">
    <span class="specimens__hint">↑ <span>DRAG</span> · PINCH TO SCALE · ↓</span>

    <div class="specimen" style="left: 5%; top: 12%;" data-id="01">
      <div class="specimen__paper">
        <div class="specimen__head"><span>NO. 01 / INCOME</span><span>ALLOWANCE</span></div>
        <div class="specimen__aa">A<em>l</em></div>
        <div class="specimen__alphabet">MONTHLY · FAMILY<br>RECURRING · BASELINE<br>REGULAR · PRIMARY</div>
        <div class="specimen__footer"><span>CAMPUS COIN · 2026</span><span class="specimen__download">SRS.PDF</span></div>
      </div>
    </div>

    <div class="specimen" style="left: 35%; top: 8%;" data-id="02">
      <div class="specimen__paper">
        <div class="specimen__head"><span>NO. 02 / INCOME</span><span>PART-TIME JOB</span></div>
        <div class="specimen__aa">J<em>o</em></div>
        <div class="specimen__alphabet">GIG · SHIFT · FREELANCE<br>IRREGULAR · VARIABLE<br>STUDENT · EARNED</div>
        <div class="specimen__footer"><span>CAMPUS COIN · 2026</span><span class="specimen__download">SRS.PDF</span></div>
      </div>
    </div>

    <div class="specimen" style="left: 64%; top: 18%;" data-id="03">
      <div class="specimen__paper">
        <div class="specimen__head"><span>NO. 03 / INCOME</span><span>SCHOLARSHIP</span></div>
        <div class="specimen__aa">S<em>c</em></div>
        <div class="specimen__alphabet">MERIT · NEED · GRANT<br>INSTITUTIONAL · TERM<br>AWARD · ACADEMIC</div>
        <div class="specimen__footer"><span>CAMPUS COIN · 2026</span><span class="specimen__download">SRS.PDF</span></div>
      </div>
    </div>

    <div class="specimen" style="left: 18%; top: 48%;" data-id="04">
      <div class="specimen__paper">
        <div class="specimen__head"><span>NO. 04 / EXPENSE</span><span>FOOD</span></div>
        <div class="specimen__aa">F<em>o</em></div>
        <div class="specimen__alphabet">CANTEEN · CAFE · DELIVERY<br>SNACKS · MEALS · CHAI<br>TOP CATEGORY · DAILY</div>
        <div class="specimen__footer"><span>CAMPUS COIN · 2026</span><span class="specimen__download">SRS.PDF</span></div>
      </div>
    </div>

    <div class="specimen" style="left: 50%; top: 52%;" data-id="05">
      <div class="specimen__paper">
        <div class="specimen__head"><span>NO. 05 / EXPENSE</span><span>ACADEMICS</span></div>
        <div class="specimen__aa">A<em>c</em></div>
        <div class="specimen__alphabet">BOOKS · TUITION · FEES<br>STATIONERY · PRINTING<br>SEMESTER · ESSENTIAL</div>
        <div class="specimen__footer"><span>CAMPUS COIN · 2026</span><span class="specimen__download">SRS.PDF</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ DIVIDER V ============ -->
<div class="divider">
  <span class="divider__roman">V</span>
  <span>— Lectures &amp; Talks —</span>
</div>

<!-- ============ LECTURES ============ -->
<div class="section-head reveal">
  <div class="section-head__num">— V —</div>
  <h2 class="section-head__title">Lectures &amp; <em>Talks.</em></h2>
  <p class="section-head__sub">Three appearances from the Campus Coin team on student finance, budgeting, and the case for plain-language money tools.</p>
</div>

<section class="lectures">
  <div class="lectures__grid">

    {{-- ✅ CAMPUS COIN BRANDED TALK CARDS — No random images --}}
    <div class="talk reveal">
      <div class="talk__video talk__video--finance" data-video="campus-finance-2026">
        <div class="talk__visual">
          <div class="talk__visual-icon">↗</div>
          <div class="talk__visual-lines">
            <span></span><span></span><span></span><span></span>
          </div>
        </div>
        <span class="talk__corner">CAMPUS · 2026</span>
        <button class="talk__play" aria-label="Play"></button>
        <span class="talk__duration">42:10</span>
      </div>
      <div class="talk__meta"><span>STUDENT FINANCE SUMMIT</span><span>14 MAR 2026</span></div>
      <h3 class="talk__title">On logging money <em>before</em> the chai gets cold.</h3>
      <p class="talk__blurb">Forty-two minutes on why the five-second transaction entry is the single most important design decision in a student finance app — and what breaks when it isn't.</p>
    </div>

    <div class="talk reveal reveal--delay-1">
      <div class="talk__video talk__video--budget" data-video="campus-budgets-2026">
        <div class="talk__visual">
          <div class="talk__visual-icon">%</div>
          <div class="talk__visual-bars">
            <span style="width: 32%;"></span>
            <span style="width: 48%;"></span>
            <span style="width: 78%;"></span>
            <span style="width: 100%;"></span>
          </div>
        </div>
        <span class="talk__corner">HOSTEL · 2026</span>
        <button class="talk__play" aria-label="Play"></button>
        <span class="talk__duration">36:55</span>
      </div>
      <div class="talk__meta"><span>HOSTEL COMMUNITY TALK</span><span>06 MAY 2026</span></div>
      <h3 class="talk__title">A budget you can <em>see</em> is a budget you keep.</h3>
      <p class="talk__blurb">A practical walkthrough of per-category monthly caps, real-time progress bars, and in-app alerts — with three months of before-and-after data from a hostel pilot.</p>
    </div>

    <div class="talk reveal reveal--delay-2">
      <div class="talk__video talk__video--ai" data-video="campus-ai-2026">
        <div class="talk__visual">
          <div class="talk__visual-icon">Ai</div>
          <div class="talk__visual-message">
            <span>Food delivery rose 40%</span>
            <span class="talk__visual-arrow">→ Weekly cap: Rs. 1,500</span>
          </div>
        </div>
        <span class="talk__corner">LITERACY · 2026</span>
        <button class="talk__play" aria-label="Play"></button>
        <span class="talk__duration">31:22</span>
      </div>
      <div class="talk__meta"><span>CAMPUS LITERACY INITIATIVE</span><span>13 APR 2026</span></div>
      <h3 class="talk__title">AI as an <em>assistant</em>, not an oracle.</h3>
      <p class="talk__blurb">Thirty-one minutes on why AI categorization and monthly insights must remain advisory — overridable, transparent, and never mistaken for certified financial advice.</p>
    </div>

  </div>
</section>

<!-- ============ DIVIDER VI ============ -->
<div class="divider">
  <span class="divider__roman">VI</span>
  <span>— FAQ —</span>
</div>

<!-- ============ FAQ ============ -->
<section class="faq-section reveal" id="faq">
  <div class="section-head" style="padding-top: 0;">
    <div class="section-head__num">— VI —</div>
    <h2 class="section-head__title">Simple answers to <em>common questions.</em></h2>
  </div>

  <details class="faq-item">
    <summary>What is Campus Coin? <span>+</span></summary>
    <p>Campus Coin is a student-focused finance management web application for tracking income and expenses, managing budgets, setting savings goals, and understanding spending habits. It is built specifically for college and university students — not for salaried adults with fixed pay cycles.</p>
  </details>

  <details class="faq-item">
    <summary>Does Campus Coin connect to my bank? <span>+</span></summary>
    <p>No. Campus Coin does not require real banking integration or payment processing. All income and expense data is manually entered or imported via a simple CSV file. The application does not perform bank-account verification or handle real monetary transactions.</p>
  </details>

  <details class="faq-item">
    <summary>Can I create my own categories? <span>+</span></summary>
    <p>Yes. Registered students can create personal categories under both income and expense types, and manage them under "Manage Own Categories" — add, edit, and delete — in addition to the default categories provided by the system.</p>
  </details>

  <details class="faq-item">
    <summary>Is the AI categorization mandatory? <span>+</span></summary>
    <p>No. AI categorization and monthly insights are entirely optional. When enabled, they suggest a category as you type and generate a plain-language monthly summary. Every suggestion can be manually overridden. AI output is advisory — it is not a substitute for professional financial advice.</p>
  </details>

  <details class="faq-item">
    <summary>Who can see my transaction history? <span>+</span></summary>
    <p>Only you. Authentication is the first gate, and only registered users can access their own transaction history. Administrators oversee the system — default categories, user accounts, and usage statistics — but do not read individual student ledgers.</p>
  </details>
</section>

<!-- ============ CTA / CONTACT ============ -->
<section class="contact reveal" id="cta">
  <p class="contact__pre">— Start your first transaction in under a minute —</p>
  <h2 class="contact__name">Campus <em>Coin</em></h2>

  @guest
    <a href="{{ route('register') }}" class="contact__email">Create your free account →</a>
  @else
    @if(auth()->user()->role === 'admin')
      <a href="{{ route('admin.dashboard') }}" class="contact__email">Open the admin panel →</a>
    @else
      <a href="{{ route('dashboard') }}" class="contact__email">Open your dashboard →</a>
    @endif
  @endguest

  <div class="contact__grid">
    <div class="contact__col">
      <span class="label">Product</span>
      <span class="value">
        Campus Coin<br>
        Student Finance Workspace<br>
        Version 1.0<br>
        Theme: NextGen BudgetBee
      </span>
    </div>
    <div class="contact__col">
      <span class="label">Built For</span>
      <span class="value">
        Individual students<br>
        Hostel communities<br>
        Campus financial-literacy<br>
        initiatives
      </span>
    </div>
    <div class="contact__col">
      <span class="label">Category</span>
      <span class="value">
        End-to-End Web Solutions<br>
        SRS v1.0<br>
        © Aptech Limited
      </span>
    </div>
  </div>

  <div class="colophon">
    <p>Campus Coin is a full-stack web application designed for college and university students. It does not integrate with real banking systems, does not perform actual bank-account verification or real payment processing, and does not handle real monetary transactions. All AI-generated categorization and insight is advisory in nature and is presented as a suggestion the student can review or override — never as certified financial advice.</p>

    <p>Every transaction is stored in a central database, which powers category-wise breakdowns, month-over-month trend charts, and a personalized tips engine that reacts to the student's own habits.</p>

    <div class="colophon__sig">— END · CAMPUS COIN · SMART SPENDING, STUDENT STYLE · MMXXVI —</div>
  </div>
</section>

</main>

<!-- ============ FOOTER ============ -->
<footer class="home-footer">
  <div class="home-footer-container">
    <div class="home-footer-brand">
      <a href="{{ url('/') }}" class="home-logo">
        <div class="home-logo-mark">+</div>
        <div class="home-logo-text">
          <strong>Campus<span>Coin</span></strong>
          <small>STUDENT FINANCE</small>
        </div>
      </a>

      <p>Smart spending, student style.</p>

      <span class="home-footer-description">
        A student-focused workspace for understanding, organizing, and managing personal finances — built for college and university students.
      </span>
    </div>

    <div class="home-footer-links">
      <div>
        <strong>Product</strong>
        <a href="#features">Features</a>
        <a href="#categories">Categories</a>
        <a href="#how-it-works">Budgets</a>
        <a href="#insights">AI Insights</a>
      </div>

      <div>
        <strong>Account</strong>
        @auth
          @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
          @else
            <a href="{{ route('dashboard') }}">Dashboard</a>
          @endif
        @else
          <a href="{{ route('login') }}">Log In</a>
          <a href="{{ route('register') }}">Register</a>
        @endauth
      </div>

      <div>
        <strong>Company</strong>
        <a href="#faq">FAQ</a>
        <a href="#contents">Contents</a>
        <a href="#cta">Get Started</a>
      </div>
    </div>
  </div>

  <div class="home-footer-bottom">
    <span>© {{ date('Y') }} Campus Coin. All rights reserved.</span>
    <span>Built for students.</span>
  </div>
</footer>

</body>
</html>