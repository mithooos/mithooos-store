/**
 * MITHOOOS — Utility Functions
 */
'use strict';

/* Global close function for mobile nav (used via onclick in HTML) */
function closeMenu() {
  const mobileNav = document.getElementById('mobileNav');
  const overlay   = document.getElementById('navOverlay');
  const toggle    = document.getElementById('menuToggle');
  if (mobileNav) { mobileNav.classList.remove('open'); }
  if (overlay)   { overlay.classList.remove('active'); }
  if (toggle)    { toggle.setAttribute('aria-expanded', 'false'); }
  document.body.style.overflow = '';
}


/* ── Format currency ── */
function formatPrice(n) {
  return 'PKR ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/* ── Format date ── */
function formatDate(str) {
  if (!str || str === '—') return '—';
  const d = new Date(str);
  if (isNaN(d.getTime())) return '—';
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

/* ── Truncate text ── */
function truncate(str, max = 80) {
  return str.length > max ? str.slice(0, max).trim() + '…' : str;
}

/* ── Debounce ── */
function debounce(fn, ms = 300) {
  let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

/* ── Slugify ── */
function slugify(str) {
  return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
}

/* ── Toast notifications ── */
function showToast(message, type = 'info', duration = 3500) {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    container.setAttribute('aria-live', 'polite');
    container.setAttribute('aria-atomic', 'true');
    document.body.appendChild(container);
  }
  const icons = {
    success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>',
    error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg>',
    info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg>',
    warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><path d="m12 3 10 18H2L12 3Z"/><path d="M12 9v5m0 3h.01"/></svg>'
  };
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.setAttribute('role', 'alert');
  toast.innerHTML = `<span style="display:flex;flex-shrink:0">${icons[type] || icons.info}</span><span>${message}</span>`;
  container.appendChild(toast);
  const dismiss = () => {
    toast.style.cssText += 'opacity:0;transform:translateX(40px);transition:all .3s ease';
    setTimeout(() => toast.remove(), 320);
  };
  const tid = setTimeout(dismiss, duration);
  toast.addEventListener('click', () => { clearTimeout(tid); dismiss(); });
}

/* ── Scroll reveal observer ── */
function initScrollReveal(selector = '[data-reveal]') {
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
    });
  }, { threshold: 0.1 });
  document.querySelectorAll(selector).forEach(el => io.observe(el));
}

/* ── Skeleton loaders ── */
function showSkeletons(containerId, count = 4, type = 'product') {
  const el = document.getElementById(containerId);
  if (!el) return;
  const productSkeleton = () => `
    <div class="product-card" style="pointer-events:none">
      <div class="product-card-img"><div class="skeleton" style="width:100%;aspect-ratio:3/4"></div></div>
      <div class="product-card-body" style="gap:.5rem">
        <div class="skeleton" style="height:12px;width:60%;border-radius:4px"></div>
        <div class="skeleton" style="height:16px;width:85%;border-radius:4px"></div>
        <div class="skeleton" style="height:12px;width:45%;border-radius:4px"></div>
        <div class="skeleton" style="height:20px;width:50%;border-radius:4px;margin-top:.5rem"></div>
      </div>
    </div>`;
  el.innerHTML = Array.from({ length: count }, productSkeleton).join('');
}

/* ── Build product card HTML ── */
function buildProductCard(p) {
  const finalPrice = parseFloat(p.final_price || p.price) || 0;
  const origPrice  = parseFloat(p.price) || 0;
  const hasDiscount = p.discount_percentage > 0;
  const stars = renderStars(p.rating || 0);

  const now = new Date();
  const hasOnlineDiscount = p.online_discount_enabled 
    && (!p.online_discount_start || new Date(p.online_discount_start) <= now)
    && (!p.online_discount_end || new Date(p.online_discount_end) >= now);

  // Escape once, reuse everywhere this data is rendered — product_name values
  // are admin-entered and were previously interpolated unescaped here.
  const name  = escapeHtml(p.product_name || '');
  const image = p.image ? escapeHtml(resolveImageUrl(p.image)) : '';
  // Safe to embed inside a single-quoted onclick(...) JS string argument:
  // escape backslash first, then single quote, and neutralize HTML-special
  // chars so it can't break out of the surrounding double-quoted attribute.
  const nameForJs = (p.product_name || '')
    .replace(/\\/g, '\\\\')
    .replace(/'/g, "\\'")
    .replace(/"/g, '&quot;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

  return `
    <article class="product-card" data-product-id="${p.product_id}">
      <div class="product-card-img">
        ${image
          ? `<img src="${image}" alt="${name}" loading="lazy" onerror="handleImageError(this)" style="width:100%;height:100%;object-fit:cover"><div class="img-placeholder img-fallback" style="display:none;width:100%;height:100%;align-items:center;justify-content:center;background:linear-gradient(135deg,#FFF0EB,#FFE4F0)"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#FFB89A" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>`
          : `<div class="img-placeholder" style="width:100%;aspect-ratio:3/4;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#FFF0EB,#FFE4F0)">
               <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#FFB89A" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
             </div>`}
        <div class="product-card-badges">
          ${p.is_new ? '<span class="badge badge-new">New</span>' : ''}
          ${p.is_sale && !p.is_new ? '<span class="badge badge-sale">Sale</span>' : ''}
          ${hasDiscount ? `<span class="badge badge-pct">-${p.discount_percentage}%</span>` : (hasOnlineDiscount ? `<span class="badge" style="background:#8B5CF6;color:white">%Off</span>` : '')}
        </div>
        <button class="btn-wishlist ${p.in_wishlist ? 'active' : ''}"
          onclick="toggleWishlist(event, ${p.product_id})"
          aria-label="${p.in_wishlist ? 'Remove from' : 'Add to'} wishlist">
          <svg width="16" height="16" viewBox="0 0 24 24"
            fill="${p.in_wishlist ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
        </button>
        <div class="product-card-actions">
          <button class="btn btn-primary btn-sm" style="flex:1"
            onclick="addToCartFromCard(event, ${p.product_id}, '${nameForJs}', ${finalPrice})">
            Add to Cart
          </button>
          <a href="/product?id=${p.product_id}"
            class="btn btn-sm" style="background:rgba(255,255,255,.2);color:white;border:1px solid rgba(255,255,255,.3)">
            View
          </a>
        </div>
      </div>
      <div class="product-card-body">
        <a href="/product?id=${p.product_id}" class="product-name">${name}</a>
        <div class="product-rating">
          <span style="color:#F59E0B;letter-spacing:1px;font-size:.8rem">${stars}</span>
          <span class="product-rating-count">(${p.review_count || 0})</span>
        </div>
        <div class="product-price">
          <span class="product-price-current ${hasDiscount ? 'product-price-sale' : ''}">${formatPrice(finalPrice)}</span>
          ${hasDiscount ? `<span class="product-price-original">${formatPrice(origPrice)}</span>` : ''}
        </div>
      </div>
    </article>`;
}

function resolveImageUrl(value) {
  if (!value) return '';
  const raw = String(value);
  const uploadIndex = raw.indexOf('/uploads/');
  if (uploadIndex >= 0) return `${location.origin}${raw.slice(uploadIndex)}`;
  return raw;
}

function handleImageError(image) {
  image.onerror = null;
  image.style.display = 'none';
  const fallback = image.parentElement.querySelector('.img-fallback');
  if (fallback) fallback.style.display = 'flex';
}

function renderStars(rating, size = 14) {
  const rounded = Math.max(0, Math.min(5, Math.round(Number(rating) || 0)));
  return Array.from({ length: 5 }, (_, index) => `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="${index < rounded ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>`).join('');
}

/* ── Quick add to cart from any card ── */
async function addToCartFromCard(e, productId, name, price) {
  e.preventDefault();
  const btn = e.target;
  btn.disabled = true;
  btn.innerHTML = '<div class="spinner" style="width:14px;height:14px;border-width:2px;border-color:rgba(255,255,255,.3);border-top-color:white"></div>';
  try {
    // Cart.add() has its own local-storage fallback and shows its own success
    // toast (showAddedFeedback) — it does not normally throw. This catch only
    // guards against an unexpected failure so we never silently lie about success.
    await Cart.add({ product_id: productId, product_name: name, final_price: price });
  } catch (err) {
    showToast(err?.message || 'Could not add item to cart. Please try again.', 'error');
  }
  setTimeout(() => {
    btn.disabled = false;
    btn.innerHTML = 'Add to Cart';
  }, 800);
}

/* ── Wishlist toggle ──
 * Shared by every page that renders product cards via buildProductCard()
 * (homepage, shop, related products) plus anywhere else that reuses this
 * same markup/onclick pattern. */
async function toggleWishlist(e, productId) {
  e.preventDefault();
  const btn = e.currentTarget;

  if (typeof API !== 'undefined' && !API.auth.isLoggedIn()) {
    showToast('Please log in to save items to your wishlist', 'info');
    setTimeout(() => {
      const loginPath = location.pathname.includes('/pages/') ? '/login' : '/login';
      window.location.href = `${loginPath}?redirect=${encodeURIComponent(location.pathname + location.search)}`;
    }, 1200);
    return;
  }

  try {
    const res = await API.wishlist.toggle(productId);
    btn.classList.toggle('active', res.data?.added);
    const svg = btn.querySelector('svg');
    if (svg) svg.setAttribute('fill', res.data?.added ? 'currentColor' : 'none');
    showToast(res.data?.added ? 'Added to wishlist' : 'Removed from wishlist', 'info');

    // Keep any navbar wishlist-count badge on this page in sync
    const badges = document.querySelectorAll('#wishlistCount');
    if (badges.length) {
      try {
        const listRes = await API.wishlist.list();
        badges.forEach(el => el.textContent = (listRes.data || []).length);
      } catch (e) { /* non-fatal — badge just won't update this time */ }
    }
  } catch (err) {
    // Genuine failure — don't claim success (previously this silently
    // flipped the button state and showed a "success" toast either way).
    showToast(err?.message || 'Could not update wishlist. Please try again.', 'error');
  }
}

/* ── Countdown timer ── */
function startCountdown(endDate, ids = { days: 'cdDays', hours: 'cdHours', mins: 'cdMins', secs: 'cdSecs' }) {
  function tick() {
    const diff = new Date(endDate) - Date.now();
    if (diff <= 0) return;
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    const pad = n => String(n).padStart(2, '0');
    document.getElementById(ids.days)?.textContent  && (document.getElementById(ids.days).textContent  = pad(d));
    document.getElementById(ids.hours)?.textContent && (document.getElementById(ids.hours).textContent = pad(h));
    document.getElementById(ids.mins)?.textContent  && (document.getElementById(ids.mins).textContent  = pad(m));
    document.getElementById(ids.secs)?.textContent  && (document.getElementById(ids.secs).textContent  = pad(s));
  }
  tick();
  return setInterval(tick, 1000);
}

/* ── Init all utils on DOM ready ── */
document.addEventListener('DOMContentLoaded', () => {
  initScrollReveal();
  // Sticky navbar
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', scrollY > 20), { passive: true });
  }
  // Mobile menu
  const menuToggle = document.getElementById('menuToggle');
  const mobileNav  = document.getElementById('mobileNav');
  const overlay    = document.getElementById('navOverlay');
  if (menuToggle && mobileNav) {
    menuToggle.addEventListener('click', () => {
      const open = mobileNav.classList.toggle('open');
      overlay?.classList.toggle('active', open);
      menuToggle.setAttribute('aria-expanded', open);
      document.body.style.overflow = open ? 'hidden' : '';
    });
    overlay?.addEventListener('click', () => {
      mobileNav.classList.remove('open');
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    });
    // Auto-close on link click
    mobileNav.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        mobileNav.classList.remove('open');
        overlay?.classList.remove('active');
        document.body.style.overflow = '';
      });
    });
  }
});

/* ── XSS-safe HTML escaping ── */
function escapeHtml(str) {
  if (str == null) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

/* ── Mobile Menu ── */
function openMenu() {
  const nav = document.getElementById('mobileNav');
  const overlay = document.getElementById('navOverlay');
  if (nav) nav.classList.add('open');
  if (overlay) overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeMenu() {
  const nav = document.getElementById('mobileNav');
  const overlay = document.getElementById('navOverlay');
  if (nav) nav.classList.remove('open');
  if (overlay) overlay.classList.remove('active');
  document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('navOverlay');
  if (overlay) {
    overlay.addEventListener('click', closeMenu);
  }
});
