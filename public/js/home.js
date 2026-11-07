/* ========== LENIS SMOOTH SCROLL ========== */
const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const isTouch = window.matchMedia('(pointer: coarse)').matches;

if (typeof Lenis !== 'undefined' && !reduced) {
  const lenis = new Lenis({
    duration: 1.1,
    easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    smoothWheel: true,
    smoothTouch: false,
  });
  function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
  requestAnimationFrame(raf);
  lenis.on('scroll', () => { if (window.ScrollTrigger) ScrollTrigger.update(); });
}

/* ========== GSAP REGISTER ========== */
if (window.gsap && window.ScrollTrigger) {
  gsap.registerPlugin(ScrollTrigger);
}

/* ========== RISOGRAPH CURSOR DOT TRAIL ========== */
(function cursorTrail() {
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
  let lastX = window.innerWidth / 2, lastY = window.innerHeight / 2;

  window.addEventListener('mousemove', e => {
    lastX = e.clientX; lastY = e.clientY;
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
      ctx.fillStyle = `hsla(${d.hue}, ${d.sat}%, ${d.lig}%, ${alpha})`;
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
})();

/* ========== HERO KINETIC TYPOGRAPHY ========== */
(function heroKinetic() {
  if (reduced) return;
  const letters = document.querySelectorAll('.hero__name span');
  if (!letters.length) return;

  const motions = [
    { y: -120, opacity: 0, rotate: -8 },
    { y: 120, opacity: 0, rotate: 6 },
    { x: -100, opacity: 0, rotate: -4 },
    { x: 100, opacity: 0, rotate: 5 },
    { scale: 0, opacity: 0, rotate: 180 },
    { y: -80, opacity: 0, rotate: 12 },
  ];

  letters.forEach((letter, i) => {
    const m = motions[i % motions.length];
    gsap.from(letter, {
      ...m,
      duration: 1.4,
      ease: 'expo.out',
      delay: 0.3 + i * 0.07,
      clearProps: 'transform',
    });
  });

  gsap.from('.hero__byline', {
    opacity: 0, y: 20,
    duration: 1.2,
    ease: 'power3.out',
    delay: 1.4,
  });

  gsap.from('.hero__meta-top span, .hero__meta-bottom span', {
    opacity: 0,
    duration: 1,
    ease: 'power2.out',
    stagger: 0.1,
    delay: 1.8,
  });
})();

/* ========== SPREAD CROSS-FADE ========== */
(function spreads() {
  const els = document.querySelectorAll('.spread');
  if (!els.length) return;

  const obs = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      const el = entry.target;
      if (entry.isIntersecting && entry.intersectionRatio > 0.15) {
        el.classList.add('is-in');
        el.classList.remove('is-past');
      } else if (!entry.isIntersecting && entry.boundingClientRect.top < 0) {
        el.classList.add('is-past');
      }
    });
  }, {
    threshold: [0, 0.15, 0.4, 0.7],
    rootMargin: '-5% 0px -5% 0px'
  });

  els.forEach(el => obs.observe(el));
})();

/* ========== REVEAL ========== */
(function reveals() {
  const els = document.querySelectorAll('.reveal');
  const obs = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-in');
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.18, rootMargin: '0px 0px -8% 0px' });
  els.forEach(el => obs.observe(el));
})();

/* ========== RUNNING HEAD PAGE NUMBER ========== */
(function pageNumber() {
  const numEl = document.getElementById('page-num');
  if (!numEl) return;
  const total = 220;
  function update() {
    const progress = Math.max(0, Math.min(1, window.scrollY / (document.body.scrollHeight - window.innerHeight)));
    const page = 8 + Math.floor(progress * (total - 8));
    numEl.textContent = String(page).padStart(3, '0');
  }
  window.addEventListener('scroll', update, { passive: true });
  update();
})();

/* ========== SPECIMEN DRAG + PINCH ========== */
(function specimenDrag() {
  const desk = document.getElementById('specimens-desk');
  if (!desk) return;
  const cards = desk.querySelectorAll('.specimen');

  cards.forEach(card => {
    let scale = 1;
    let dragX = 0, dragY = 0;
    let startX, startY;
    let isDragging = false;
    let activePointerId = null;
    let zIndex = 5;

    function applyTransform() {
      card.style.transform = `translate(${dragX}px, ${dragY}px) scale(${scale})`;
    }
    function bringFront() { zIndex += 1; card.style.zIndex = zIndex; }

    card.addEventListener('pointerdown', e => {
      if (e.pointerType === 'mouse' && e.button !== 0) return;
      if (activePointerId !== null && activePointerId !== e.pointerId) return;
      isDragging = true;
      activePointerId = e.pointerId;
      startX = e.clientX;
      startY = e.clientY;
      card.classList.add('is-dragging');
      bringFront();
      card.setPointerCapture(e.pointerId);
    });

    card.addEventListener('pointermove', e => {
      if (!isDragging || e.pointerId !== activePointerId) return;
      const dx = e.clientX - startX;
      const dy = e.clientY - startY;
      dragX += dx; dragY += dy;
      startX = e.clientX; startY = e.clientY;
      applyTransform();
    });

    function endDrag(e) {
      if (e.pointerId !== activePointerId) return;
      isDragging = false;
      activePointerId = null;
      card.classList.remove('is-dragging');
      try { card.releasePointerCapture(e.pointerId); } catch(_){}
    }
    card.addEventListener('pointerup', endDrag);
    card.addEventListener('pointercancel', endDrag);

    card.addEventListener('wheel', e => {
      e.preventDefault();
      const delta = -e.deltaY * 0.0015;
      scale = Math.max(0.55, Math.min(2.2, scale + delta));
      applyTransform();
    }, { passive: false });

    let pinchInitialDist = 0;
    let pinchInitialScale = 1;

    card.addEventListener('touchstart', e => {
      if (e.touches.length === 2) {
        e.preventDefault();
        const t1 = e.touches[0], t2 = e.touches[1];
        pinchInitialDist = Math.hypot(t2.clientX - t1.clientX, t2.clientY - t1.clientY);
        pinchInitialScale = scale;
        isDragging = false;
      }
    }, { passive: false });

    card.addEventListener('touchmove', e => {
      if (e.touches.length === 2) {
        e.preventDefault();
        const t1 = e.touches[0], t2 = e.touches[1];
        const dist = Math.hypot(t2.clientX - t1.clientX, t2.clientY - t1.clientY);
        scale = Math.max(0.55, Math.min(2.2, pinchInitialScale * (dist / Math.max(20, pinchInitialDist))));
        applyTransform();
      }
    }, { passive: false });

    const dl = card.querySelector('.specimen__download');
    if (dl) {
      dl.addEventListener('click', e => {
        e.stopPropagation();
        dl.textContent = '✓ REQUESTED';
        setTimeout(() => { dl.innerHTML = 'SRS.PDF'; }, 1800);
      });
    }
  });
})();

/* ========== FILMSTRIP SCRUB ========== */
(function filmstrip() {
  const strip = document.getElementById('filmstrip');
  const section = document.getElementById('palette');
  const bar = document.getElementById('palette-bar');
  const current = document.getElementById('palette-current');
  const frameName = document.getElementById('palette-frame-name');
  const label = document.querySelector('.palette__progress-label:not(.palette__progress-label--right)');

  if (!strip || !section) return;

  const frames = section.querySelectorAll('.filmframe');
  const frameNames = ['Jungle Deep', 'Jungle Primary', 'Jungle Secondary', 'Moss', 'Gold Dark', 'Gold', 'Gold Light', 'Ivory', 'Burnt Umber', 'Slate'];

  function getTotalScroll() {
    return Math.max(0, strip.scrollWidth - window.innerWidth + 200);
  }

  let progress = 0;
  let targetProgress = 0;

  function setStrip() {
    const total = getTotalScroll();
    const x = -progress * total;
    strip.style.transform = `translateX(${x}px)`;
    if (bar) bar.style.width = (progress * 100) + '%';

    const idx = Math.min(frames.length - 1, Math.floor(progress * frames.length));
    if (current) current.textContent = String(idx + 1).padStart(2, '0');
    if (frameName) frameName.textContent = frameNames[idx] ? frameNames[idx].toUpperCase() : '';
    if (label) label.textContent = `FRAME ${String(idx + 1).padStart(2, '0')} — ${(frameNames[idx] || '').toUpperCase()}`;
  }

  if (window.ScrollTrigger && window.gsap && !reduced) {
    ScrollTrigger.create({
      trigger: section,
      start: 'top top',
      end: () => '+=' + (window.innerHeight * 2.5),
      pin: true,
      scrub: 1,
      onUpdate: self => { targetProgress = self.progress; },
      onLeaveBack: () => { targetProgress = 0; }
    });
  } else {
    section.style.padding = '4rem 0';
    strip.style.overflowX = 'auto';
  }

  function loop() {
    progress += (targetProgress - progress) * 0.09;
    setStrip();
    requestAnimationFrame(loop);
  }
  loop();

  window.addEventListener('resize', setStrip);
})();

/* ========== TALK VIDEO CLICK ========== */
document.querySelectorAll('.talk__video').forEach(vid => {
  const play = vid.querySelector('.talk__play');
  const poster = vid.querySelector('.talk__poster');
  if (!play) return;
  play.addEventListener('click', e => {
    e.preventDefault();
    play.style.transform = 'translate(-50%, -50%) scale(0.85)';
    play.style.background = 'var(--gold)';
    if (poster) poster.style.filter = 'brightness(0.45) contrast(1.1) saturate(0.6)';
    if (!vid.querySelector('.talk__now-playing')) {
      const overlay = document.createElement('div');
      overlay.className = 'talk__now-playing';
      overlay.style.cssText = 'position:absolute;bottom:1rem;left:1rem;font-family:var(--mono);font-size:10px;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);background:rgba(5,20,10,0.7);padding:0.4rem 0.7rem;backdrop-filter:blur(4px);';
      overlay.textContent = '▶ NOW PLAYING';
      vid.appendChild(overlay);
    }
  });
});

/* ========== INDEX ENTRY CLICK — SCROLL TO SPREAD ========== */
document.querySelectorAll('.index__entry').forEach((entry, i) => {
  entry.addEventListener('click', () => {
    const spreads = document.querySelectorAll('.spread');
    const target = spreads[i];
    if (target) {
      window.scrollTo({ top: target.offsetTop - 40, behavior: 'smooth' });
    }
  });
});

/* ========== MOBILE MENU ========== */
(function mobileMenu() {
  const menuButton = document.querySelector('.home-menu-btn');
  const navLinks = document.querySelector('.home-nav-links');
  const navActions = document.querySelector('.home-nav-actions');
  if (!menuButton) return;

  menuButton.addEventListener('click', function () {
    navLinks.classList.toggle('home-mobile-open');
    navActions.classList.toggle('home-mobile-open');
    menuButton.classList.toggle('home-menu-active');
  });
})();