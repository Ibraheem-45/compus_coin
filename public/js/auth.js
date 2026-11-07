/* =========================================================
   CAMPUS COIN — MASTER AUTH SCRIPT
   Handles: cursor trail, page number, submit disable,
            password toggle
   ========================================================= */

(function () {
    'use strict';

    const isTouch = window.matchMedia('(pointer: coarse)').matches;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ========== CURSOR DOT TRAIL ========== */
    function initCursorTrail() {
        if (isTouch || reduced) return;

        const canvas = document.getElementById('cursor-canvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let dpr = Math.min(window.devicePixelRatio || 1, 2);

        function resize() {
            canvas.width = window.innerWidth * dpr;
            canvas.height = window.innerHeight * dpr;
            canvas.style.width = window.innerWidth + 'px';
            canvas.style.height = window.innerHeight + 'px';
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }
        resize();
        window.addEventListener('resize', resize);

        const dots = [];
        let lastX = window.innerWidth / 2;
        let lastY = window.innerHeight / 2;

        window.addEventListener('mousemove', function (e) {
            lastX = e.clientX;
            lastY = e.clientY;

            const count = 2 + Math.floor(Math.random() * 2);
            for (let i = 0; i < count; i++) {
                dots.push({
                    x: e.clientX + (Math.random() - 0.5) * 6,
                    y: e.clientY + (Math.random() - 0.5) * 6,
                    r: 0.8 + Math.random() * 2.2,
                    life: 1,
                    decay: 0.012 + Math.random() * 0.022,
                    hue: 42 + Math.random() * 12,
                    sat: 55 + Math.random() * 20,
                    lig: 50 + Math.random() * 15,
                });
            }
            if (dots.length > 600) dots.splice(0, dots.length - 600);
        });

        function tick() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = dots.length - 1; i >= 0; i--) {
                const d = dots[i];
                d.life -= d.decay;
                if (d.life <= 0) { dots.splice(i, 1); continue; }

                const alpha = d.life * 0.55;
                ctx.fillStyle = 'hsla(' + d.hue + ', ' + d.sat + '%, ' + d.lig + '%, ' + alpha + ')';
                ctx.beginPath();
                ctx.arc(d.x, d.y, d.r * Math.max(0.2, d.life), 0, Math.PI * 2);
                ctx.fill();
            }

            ctx.fillStyle = 'rgba(201, 162, 39, 0.85)';
            ctx.beginPath();
            ctx.arc(lastX, lastY, 2, 0, Math.PI * 2);
            ctx.fill();

            requestAnimationFrame(tick);
        }
        tick();
    }

    /* ========== RUNNING HEAD PAGE NUMBER ========== */
    function initPageNumber() {
        const numEl = document.getElementById('page-num');
        if (!numEl) return;

        function update() {
            const scrollable = document.body.scrollHeight - window.innerHeight;
            const progress = scrollable > 0
                ? Math.max(0, Math.min(1, window.scrollY / scrollable))
                : 0;
            const page = 1 + Math.floor(progress * 12);
            numEl.textContent = String(page).padStart(3, '0');
        }

        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        update();
    }

    /* ========== SUBMIT BUTTON DISABLE ========== */
    function initSubmitDisable() {
        document.querySelectorAll('.campus-register-form').forEach(function (form) {
            form.addEventListener('submit', function () {
                const submitButton = this.querySelector('button[type="submit"]');
                if (!submitButton || submitButton.disabled) return;

                submitButton.disabled = true;
                submitButton.setAttribute('aria-busy', 'true');

                const label = submitButton.querySelector('span');
                if (label && !submitButton.dataset.originalText) {
                    submitButton.dataset.originalText = label.textContent.trim();
                    label.textContent = 'Please wait...';
                }
            });
        });
    }

    /* ========== PASSWORD TOGGLE ========== */
    function initPasswordToggle() {
        document.querySelectorAll('.password-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(this.dataset.target);
                if (!input) return;

                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                this.textContent = isHidden ? 'Hide' : 'Show';
                this.setAttribute(
                    'aria-label',
                    isHidden ? 'Hide password' : 'Show password'
                );
            });
        });
    }

    /* ========== INIT ========== */
    function init() {
        initCursorTrail();
        initPageNumber();
        initSubmitDisable();
        initPasswordToggle();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();