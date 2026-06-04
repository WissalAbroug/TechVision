/* main.js — PathFinder AI */

document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initAvatarMenu();
  initMobileNav();
  initSidebar();
  initToasts();
  initProgressBars();
  initScoreRings();
  initCounters();
  initDeleteConfirm();
  initChipInputs();
  initDynamicRows();
  animateOnScroll();
  initBackgroundParticles();
});

/* ── Theme Switcher ────────────────────────────────────── */
function initTheme() {
  const t = localStorage.getItem('theme') || 'dark';
  document.body.className = 'theme-' + t;
  document.documentElement.style.background = t === 'dark' ? '#06060c' : '#f1f5f9';
  updateThemeIcon(t);
}

function toggleTheme() {
  const current = localStorage.getItem('theme') || 'dark';
  const next = current === 'dark' ? 'light' : 'dark';
  localStorage.setItem('theme', next);
  document.body.className = 'theme-' + next;
  document.documentElement.style.background = next === 'dark' ? '#06060c' : '#f1f5f9';
  updateThemeIcon(next);
}

function updateThemeIcon(theme) {
  const btn = document.getElementById('themeToggle');
  if (btn) {
    btn.innerHTML = theme === 'dark' ? '☀️' : '🌙';
  }
}

/* ── Avatar Dropdown ───────────────────────────────────── */
function initAvatarMenu() {
  const avatars = document.querySelectorAll('.avatar');
  avatars.forEach(avatar => {
    avatar.addEventListener('click', (e) => {
      e.stopPropagation();
      const menu = avatar.querySelector('.avatar-menu');
      if (menu) menu.classList.toggle('open');
    });
  });
  document.addEventListener('click', () => {
    document.querySelectorAll('.avatar-menu.open').forEach(m => m.classList.remove('open'));
  });
}

/* ── Mobile Nav ────────────────────────────────────────── */
function initMobileNav() {
  const hamburger = document.getElementById('hamburger');
  const mobileNav = document.getElementById('mobileNav');
  if (!hamburger || !mobileNav) return;
  hamburger.addEventListener('click', () => {
    mobileNav.classList.toggle('open');
    hamburger.classList.toggle('active');
  });
  mobileNav.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => mobileNav.classList.remove('open'));
  });
}

/* ── Sidebar Toggle ────────────────────────────────────── */
function initSidebar() {
  const toggle  = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (!toggle || !sidebar) return;
  const close = () => { sidebar.classList.remove('open'); if(overlay) overlay.classList.remove('open'); };
  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('open');
  });
  if (overlay) overlay.addEventListener('click', close);
}

/* ── Flash Toasts ──────────────────────────────────────── */
function initToasts() {
  const toasts = document.querySelectorAll('.toast');
  toasts.forEach((toast, i) => {
    toast.style.animationDelay = (i * 0.1) + 's';
    setTimeout(() => {
      toast.style.animation = 'fadeIn .3s ease reverse both';
      setTimeout(() => toast.remove(), 400);
    }, 4000 + i * 200);
  });
}

/* ── Progress Bars ─────────────────────────────────────── */
function initProgressBars() {
  const bars = document.querySelectorAll('.progress-fill[data-width]');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.width = entry.target.dataset.width + '%';
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });
  bars.forEach(bar => observer.observe(bar));
}

/* ── SVG Score Rings ────────────────────────────────────── */
function initScoreRings() {
  document.querySelectorAll('.score-ring[data-score]').forEach(ring => {
    const score = parseFloat(ring.dataset.score);
    const circle = ring.querySelector('.score-ring-circle');
    if (!circle) return;
    const circumference = 251.2;
    const offset = circumference - (score / 100) * circumference;
    setTimeout(() => { circle.style.strokeDashoffset = offset; }, 200);
    // Color
    if (score >= 70) circle.style.stroke = 'var(--success)';
    else if (score >= 40) circle.style.stroke = 'var(--warning)';
    else circle.style.stroke = 'var(--danger)';
  });
}

/* ── Count-up Numbers ───────────────────────────────────── */
function initCounters() {
  const counters = document.querySelectorAll('[data-count]');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el  = entry.target;
        const end = parseFloat(el.dataset.count);
        const dur = 1500;
        const step = 16;
        let start = 0;
        const timer = setInterval(() => {
          start += end / (dur / step);
          if (start >= end) { el.textContent = Number.isInteger(end) ? end : end.toFixed(1); clearInterval(timer); }
          else { el.textContent = Number.isInteger(end) ? Math.floor(start) : start.toFixed(1); }
        }, step);
        observer.unobserve(el);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(el => observer.observe(el));
}

/* ── Delete Confirmation ────────────────────────────────── */
function initDeleteConfirm() {
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
      const msg = this.dataset.confirm || 'Confirmer ?';
      if (!confirm(msg)) e.preventDefault();
    });
  });
  document.querySelectorAll('form[data-confirm-form]').forEach(form => {
    form.addEventListener('submit', function(e) {
      const msg = this.dataset.confirmForm || 'Confirmer ?';
      if (!confirm(msg)) e.preventDefault();
    });
  });
}

/* ── Chip/Tag Input ─────────────────────────────────────── */
function initChipInputs() {
  document.querySelectorAll('.chip-input-container').forEach(container => {
    const hidden = container.querySelector('input[type=hidden]');
    const input  = container.querySelector('.chip-text-input');
    const chips  = container.querySelector('.chips-container');
    if (!hidden || !input || !chips) return;

    let tags = hidden.value ? hidden.value.split(',').filter(Boolean) : [];

    const render = () => {
      chips.querySelectorAll('.chip').forEach(c => c.remove());
      tags.forEach(tag => {
        const chip = document.createElement('span');
        chip.className = 'chip';
        chip.innerHTML = `${tag} <span class="chip-remove" data-tag="${tag}">✕</span>`;
        chip.querySelector('.chip-remove').addEventListener('click', () => {
          tags = tags.filter(t => t !== tag);
          hidden.value = tags.join(',');
          render();
        });
        chips.insertBefore(chip, input);
      });
      hidden.value = tags.join(',');
    };

    input.addEventListener('keydown', e => {
      if ((e.key === 'Enter' || e.key === ',') && input.value.trim()) {
        e.preventDefault();
        const val = input.value.trim().replace(/,$/, '');
        if (val && !tags.includes(val)) { tags.push(val); render(); }
        input.value = '';
      }
      if (e.key === 'Backspace' && !input.value && tags.length) {
        tags.pop(); render();
      }
    });

    chips.addEventListener('click', () => input.focus());
    render();
  });
}

/* ── Dynamic Rows (CV form) ─────────────────────────────── */
function initDynamicRows() {
  document.querySelectorAll('[data-add-row]').forEach(btn => {
    btn.addEventListener('click', () => {
      const containerId = btn.dataset.addRow;
      const template    = document.getElementById(containerId + '_template');
      const container   = document.getElementById(containerId);
      if (!template || !container) return;
      const clone = template.content.cloneNode(true);
      container.appendChild(clone);
      // Remove row button
      container.querySelectorAll('[data-remove-row]').forEach(rb => {
        rb.onclick = () => rb.closest('.dynamic-row').remove();
      });
    });
  });
  // Init existing remove buttons
  document.querySelectorAll('[data-remove-row]').forEach(rb => {
    rb.onclick = () => rb.closest('.dynamic-row').remove();
  });
}

/* ── Scroll Animations ──────────────────────────────────── */
function animateOnScroll() {
  const els = document.querySelectorAll('.animate-on-scroll');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in-up');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });
  els.forEach(el => observer.observe(el));
}

/* ── Language Toggle ────────────────────────────────────── */
function switchLang(lang) {
  const url = new URL(window.location.href);
  url.searchParams.set('lang', lang);
  window.location.href = url.toString();
}

/* ── Utility ────────────────────────────────────────────── */
function getScoreClass(score) {
  if (score >= 70) return 'score-high';
  if (score >= 40) return 'score-medium';
  return 'score-low';
}

/* ── Interactive Cyberpunk Particle Background ────────── */
function initBackgroundParticles() {
  const canvas = document.getElementById('bg-neural-canvas');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  let animationFrameId;
  let width = (canvas.width = window.innerWidth);
  let height = (canvas.height = window.innerHeight);

  const particles = [];
  const particleCount = Math.min(80, Math.floor((width * height) / 18000));
  const connectionDistance = 115;
  const mouse = { x: null, y: null, radius: 150 };

  // Generate particles
  for (let i = 0; i < particleCount; i++) {
    particles.push({
      x: Math.random() * width,
      y: Math.random() * height,
      vx: (Math.random() - 0.5) * 0.45,
      vy: (Math.random() - 0.5) * 0.45,
      radius: Math.random() * 2 + 1.2,
      colorIndex: Math.floor(Math.random() * 2) // 0 or 1 for dual tone
    });
  }

  // Mouse interactivity
  window.addEventListener('mousemove', (e) => {
    mouse.x = e.clientX;
    mouse.y = e.clientY;
  });

  window.addEventListener('mouseleave', () => {
    mouse.x = null;
    mouse.y = null;
  });

  window.addEventListener('resize', () => {
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;
  });

  function draw() {
    ctx.clearRect(0, 0, width, height);
    
    // Dynamic theme colors
    const isLight = document.body.classList.contains('theme-light');
    const color1 = isLight ? 'rgba(37, 99, 235, 0.4)' : 'rgba(0, 240, 255, 0.4)';  // Cyan / Blue
    const color2 = isLight ? 'rgba(124, 58, 237, 0.4)' : 'rgba(167, 139, 250, 0.4)'; // Violet / Purple
    const lineColor = isLight ? 'rgba(37, 99, 235, 0.04)' : 'rgba(0, 240, 255, 0.05)';

    // Update and draw particles
    particles.forEach((p) => {
      // Move
      p.x += p.vx;
      p.y += p.vy;

      // Bounce
      if (p.x < 0 || p.x > width) p.vx *= -1;
      if (p.y < 0 || p.y > height) p.vy *= -1;

      // Gravitational pull towards mouse
      if (mouse.x !== null && mouse.y !== null) {
        const dx = mouse.x - p.x;
        const dy = mouse.y - p.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < mouse.radius) {
          const force = (mouse.radius - dist) / mouse.radius;
          p.x += (dx / dist) * force * 0.4;
          p.y += (dy / dist) * force * 0.4;
        }
      }

      // Render
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
      ctx.fillStyle = p.colorIndex === 0 ? color1 : color2;
      ctx.fill();
    });

    // Draw connection lines
    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const p1 = particles[i];
        const p2 = particles[j];
        const dx = p1.x - p2.x;
        const dy = p1.y - p2.y;
        const dist = Math.sqrt(dx * dx + dy * dy);

        if (dist < connectionDistance) {
          const alpha = (1 - dist / connectionDistance) * 0.85;
          ctx.beginPath();
          ctx.moveTo(p1.x, p1.y);
          ctx.lineTo(p2.x, p2.y);
          ctx.strokeStyle = lineColor.replace(/[^,]+(?=\))/, alpha);
          ctx.lineWidth = 0.65;
          ctx.stroke();
        }
      }
    }

    animationFrameId = requestAnimationFrame(draw);
  }

  draw();
}
