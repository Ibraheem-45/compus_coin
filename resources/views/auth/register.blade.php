<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Campus Coin</title>
    <meta name="description" content="Create your Campus Coin account and start building better money habits.">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap'>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/auth.js') }}" defer></script>
</head>
<body class="register-page">

<canvas class="cursor-canvas" id="cursor-canvas"></canvas>

<header class="running-head">
    <span>CAMPUS COIN</span>
    <span>CREATE ACCOUNT · MMXXVI</span>
    <span>P. <span id="page-num">001</span></span>
</header>

<main class="register-container">

    {{-- LEFT SHOWCASE --}}
    <section class="register-showcase">
        <div class="showcase-grid"></div>
        <div class="glow glow-green"></div>
        <div class="glow glow-purple"></div>

        <div class="showcase-inner">
            <a href="{{ url('/') }}" class="modern-brand">
                <div class="modern-brand-mark">+</div>
                <div>
                    <strong>Campus<span>Coin</span></strong>
                    <small>STUDENT FINANCE</small>
                </div>
            </a>

            <div class="hero-copy">
                <div class="hero-pill">
                    <span class="live-dot"></span>
                    BUILT FOR STUDENTS
                </div>
                <h1>Spend smart.<br><span>Save better.</span></h1>
                <p>Track your spending, manage your allowance, and build better money habits — all in one place.</p>
            </div>

            <div class="finance-preview">
                <div class="preview-main-card">
                    <div class="preview-top">
                        <div>
                            <span>AVAILABLE BALANCE</span>
                            <strong>Rs. 18,450</strong>
                        </div>
                        <div class="balance-icon">↗</div>
                    </div>
                    <div class="mini-chart">
                        <div class="chart-fill"></div>
                        <svg viewBox="0 0 420 120" preserveAspectRatio="none" aria-hidden="true">
                            <path d="M0 94 C35 90, 45 76, 70 80 S105 88, 125 65 S165 54, 185 61 S215 78, 235 52 S270 37, 290 45 S320 60, 340 33 S380 26, 420 12"/>
                        </svg>
                        <span class="graph-point point-one"></span>
                        <span class="graph-point point-two"></span>
                        <span class="graph-point point-three"></span>
                        <span class="graph-point point-four"></span>
                    </div>
                    <div class="chart-months">
                        <span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span>
                    </div>
                </div>

                <div class="floating-card spending-card">
                    <div class="floating-icon food-icon">🍔</div>
                    <div>
                        <small>Food</small>
                        <strong>Rs. 3,240</strong>
                    </div>
                    <span class="percentage">32%</span>
                </div>

                <div class="floating-card saving-card">
                    <div class="saving-header">
                        <span>Saving goal</span>
                        <strong>68%</strong>
                    </div>
                    <div class="progress-track">
                        <div class="progress-value"></div>
                    </div>
                    <small>Rs. 3,400 of Rs. 5,000</small>
                </div>
            </div>

            <div class="hero-bottom">
                <div class="bottom-item">
                    <span class="bottom-check">✓</span>
                    <div><strong>Track</strong><small>Every rupee</small></div>
                </div>
                <div class="bottom-line"></div>
                <div class="bottom-item">
                    <span class="bottom-check">✓</span>
                    <div><strong>Save</strong><small>With a goal</small></div>
                </div>
                <div class="bottom-line"></div>
                <div class="bottom-item">
                    <span class="bottom-check">✓</span>
                    <div><strong>Understand</strong><small>Your habits</small></div>
                </div>
            </div>
        </div>
    </section>

    {{-- RIGHT REGISTER FORM --}}
    <section class="register-form-section">
        <div class="register-form-wrapper">

            <div class="mobile-brand">
                <a href="{{ url('/') }}" class="brand">
                    <div class="brand-icon">CC</div>
                    <span>Campus<span>Coin</span></span>
                </a>
            </div>

            <div class="form-heading">
                <p class="form-eyebrow">GET STARTED</p>
                <h2>Create your account</h2>
                <p>Start building better money habits today.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="campus-register-form" novalidate>
                @csrf

                {{-- Full Name --}}
                <div class="form-group @error('name') has-error @enderror">
                    <label for="name">Full Name</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" autofocus autocomplete="name" placeholder="Enter your full name">
                    </div>
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div class="form-group @error('email') has-error @enderror">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="you@example.com">
                    </div>
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- Academic Year --}}
                <div class="form-group @error('academic_year') has-error @enderror">
                    <label for="academic_year">Academic Year</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        </span>
                        <input id="academic_year" type="text" name="academic_year" value="{{ old('academic_year') ?: '3rd Year (Junior)' }}" autocomplete="off" placeholder="e.g. 2nd Year">
                    </div>
                    @error('academic_year')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- University --}}
                <div class="form-group @error('university') has-error @enderror">
                    <label for="university">University / Institution</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 5l9 5.5-9 5.5-9-5.5Zm0 0V18l9 5 9-5v-7.5"/></svg>
                        </span>
                        <input id="university" type="text" name="university" value="{{ old('university') ?: 'FAST-NUCES' }}" autocomplete="organization" placeholder="e.g. FAST-NUCES">
                    </div>
                    @error('university')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- Financial Info --}}
                <div class="form-row">
                    <div class="form-group @error('monthly_allowance_baseline') has-error @enderror">
                        <label for="monthly_allowance_baseline">Monthly Allowance</label>
                        <div class="input-wrapper">
                            <span class="currency-symbol">Rs.</span>
                            <input id="monthly_allowance_baseline" type="number" name="monthly_allowance_baseline" value="{{ old('monthly_allowance_baseline') }}" min="0" step="0.01" placeholder="25,000">
                        </div>
                        @error('monthly_allowance_baseline')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group @error('monthly_saving_goal') has-error @enderror">
                        <label for="monthly_saving_goal">Monthly Saving Goal</label>
                        <div class="input-wrapper">
                            <span class="currency-symbol">Rs.</span>
                            <input id="monthly_saving_goal" type="number" name="monthly_saving_goal" value="{{ old('monthly_saving_goal') }}" min="0" step="0.01" placeholder="5,000">
                        </div>
                        @error('monthly_saving_goal')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-group @error('password') has-error @enderror">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        </span>
                        <input id="password" type="password" name="password" autocomplete="new-password" placeholder="Create a strong password">
                        <button type="button" class="password-toggle" data-target="password" aria-label="Show password">Show</button>
                    </div>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- Confirm Password --}}
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        </span>
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Repeat your password">
                        <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Show password">Show</button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="register-button">
                    <span>Create Campus Coin Account</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                </button>

                <p class="login-link">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in</a>
                </p>
            </form>

            <p class="form-footer">
                By creating an account, you agree to use Campus Coin responsibly for personal budgeting.
            </p>
        </div>
    </section>
</main>
</body>
</html>