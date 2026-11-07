<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Campus Coin</title>
    <meta name="description" content="Sign in to your Campus Coin account and continue managing your student finances.">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap'>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/auth.js') }}" defer></script>
</head>
<body class="register-page">

<canvas class="cursor-canvas" id="cursor-canvas"></canvas>

<header class="running-head">
    <span>CAMPUS COIN</span>
    <span>SIGN IN · MMXXVI</span>
    <span>P. <span id="page-num">001</span></span>
</header>

<main class="register-container">

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
                    WELCOME BACK
                </div>
                <h1>Spend smart.<br><span>Save better.</span></h1>
                <p>Keep your spending organized, track your savings, and stay in control of your student finances.</p>
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
                    <div><small>Food</small><strong>Rs. 3,240</strong></div>
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

    <section class="register-form-section">
        <div class="register-form-wrapper">

            <div class="mobile-brand">
                <a href="{{ url('/') }}" class="brand">
                    <div class="brand-icon">CC</div>
                    <span>Campus<span>Coin</span></span>
                </a>
            </div>

            <div class="form-heading">
                <p class="form-eyebrow">WELCOME BACK</p>
                <h2>Sign in to your account</h2>
                <p>Continue managing your money with Campus Coin.</p>
            </div>

            <x-auth-session-status class="login-status" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="campus-register-form" novalidate>
                @csrf

                {{-- Email --}}
                <div class="form-group @error('email') has-error @enderror">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus autocomplete="username" placeholder="you@example.com">
                    </div>
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div class="form-group @error('password') has-error @enderror">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        </span>
                        <input id="password" type="password" name="password" autocomplete="current-password" placeholder="Enter your password">
                        <button type="button" class="password-toggle" data-target="password" aria-label="Show password">Show</button>
                    </div>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                {{-- Remember --}}
                <label class="remember-row">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>

                {{-- Submit --}}
                <button type="submit" class="register-button">
                    <span>Sign In to Campus Coin</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                </button>

                <p class="login-link">
                    Don't have an account?
                    <a href="{{ route('register') }}">Create one</a>
                </p>
            </form>

            <p class="form-footer">
                Your personal budgeting space, designed for students.
            </p>
        </div>
    </section>
</main>
</body>
</html>