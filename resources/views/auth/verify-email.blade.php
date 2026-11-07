<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | Campus Coin</title>
    <meta name="description" content="Verify your email to activate your Campus Coin account.">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap'>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/auth.js') }}" defer></script>
</head>
<body class="register-page">

<canvas class="cursor-canvas" id="cursor-canvas"></canvas>

<header class="running-head">
    <span>CAMPUS COIN</span>
    <span>SECURE YOUR ACCOUNT · MMXXVI</span>
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
                    SECURE YOUR ACCOUNT
                </div>
                <h1>One step<br><span>to get started.</span></h1>
                <p>Verify your email address to keep your Campus Coin account secure and start managing your finances.</p>
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
                    <div><strong>Secure</strong><small>Your account</small></div>
                </div>
                <div class="bottom-line"></div>
                <div class="bottom-item">
                    <span class="bottom-check">✓</span>
                    <div><strong>Verify</strong><small>Your email</small></div>
                </div>
                <div class="bottom-line"></div>
                <div class="bottom-item">
                    <span class="bottom-check">✓</span>
                    <div><strong>Start</strong><small>Managing money</small></div>
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
                <p class="form-eyebrow">EMAIL VERIFICATION</p>
                <h2>Verify your email</h2>
                <p>Check your inbox to activate your Campus Coin account.</p>
            </div>

            <div class="verification-message">
                <div class="verification-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4z"/><path d="m4 7 8 6 8-6"/></svg>
                </div>
                <div>
                    <strong>Check your inbox</strong>
                    <p>Thanks for signing up! We sent a verification link to the email address you provided.</p>
                </div>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="verification-success">
                    <span class="success-icon">✓</span>
                    <span>A new verification link has been sent to your email address.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="verification-actions">
                @csrf
                <button type="submit" class="register-button">
                    <span>Resend Verification Email</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="verification-logout">
                @csrf
                <button type="submit">Log Out</button>
            </form>

            <p class="form-footer">
                Your personal budgeting space, designed for students.
            </p>
        </div>
    </section>
</main>
</body>
</html>