'use strict';

/* ── Toast ── */
function showToast(msg, type = 'info') {
  const tc = document.getElementById('toastContainer') || (() => {
    const el = document.createElement('div'); el.id = 'toastContainer'; el.className = 'toast-container';
    document.body.appendChild(el); return el;
  })();
  const t = document.createElement('div'); t.className = `toast ${type}`;
  const icons = {
    success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>',
    error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg>',
    info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg>',
    warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><path d="m12 3 10 18H2L12 3Z"/><path d="M12 9v5m0 3h.01"/></svg>'
  };
  t.innerHTML = `<span style="display:flex;flex-shrink:0">${icons[type] || icons.info}</span><span>${msg}</span>`;
  tc.appendChild(t);
  setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateX(40px)'; t.style.transition = 'all .25s'; setTimeout(() => t.remove(), 250); }, 3500);
}

/* ── Modal helpers ── */
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('open');
});

/* ── Confirm delete ── */
async function confirmDelete(msg = 'Delete this item?') {
  return new Promise(resolve => {
    if (confirm(msg)) resolve(true);
    else resolve(false);
  });
}

/* ── API client ── */
const AdminAPI = {
  async fetchCsrfToken(basePath) {
    try {
      const res  = await fetch(`${basePath}/backend/index.php?_url=/api/csrf-token`, { credentials: 'same-origin' });
      const data = await res.json();
      if (data?.data?.token) {
        sessionStorage.setItem('pp_csrf_token', data.data.token);
        return data.data.token;
      }
    } catch {}
    return null;
  },
  async getCsrfToken(basePath) {
    return sessionStorage.getItem('pp_csrf_token') || await this.fetchCsrfToken(basePath);
  },
  async request(method, endpoint, data) {
    const segments = window.location.pathname.split('/').filter(Boolean);
    const adminIndex = segments.indexOf('admin-panel');
    const basePath = adminIndex > 0 ? '/' + segments.slice(0, adminIndex).join('/') : '';
    
    const opts = {
      method,
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
    };
    if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method.toUpperCase())) {
      const csrf = await this.getCsrfToken(basePath);
      if (csrf) opts.headers['X-CSRF-Token'] = csrf;
    }
    
    if (data && method !== 'GET') opts.body = JSON.stringify(data);
    const [path, query = ''] = endpoint.split('?');
    const route = `/admin${path}`;
    const queryString = query ? `&${query}` : '';
    
    let res = await fetch(`${basePath}/backend/index.php?_url=${encodeURIComponent(route)}${queryString}`, opts);
    
    // On CSRF failure, retry once
    if (res.status === 403) {
        const json = await res.json().catch(() => ({}));
        if (json.message?.includes('CSRF')) {
            sessionStorage.removeItem('pp_csrf_token');
            const retryCsrf = await this.fetchCsrfToken(basePath);
            if (retryCsrf) opts.headers['X-CSRF-Token'] = retryCsrf;
            if (data && method !== 'GET') opts.body = JSON.stringify(data);
            res = await fetch(`${basePath}/backend/index.php?_url=${encodeURIComponent(route)}${queryString}`, opts);
        } else {
            throw new Error(json.message || 'Forbidden');
        }
    }
    
    const json = await res.json().catch(() => ({}));
    if (!res.ok || !json.success) {
      let details = '';
      if (json.errors) {
        if (Array.isArray(json.errors)) {
          details = ` ${json.errors.join(' ')}`;
        } else if (typeof json.errors === 'object') {
          details = ` ${Object.values(json.errors).join(' ')}`;
        }
      }
      throw new Error((json.message || `Request failed (${res.status}).`) + details);
    }
    return json;
  },
  get: (ep) => AdminAPI.request('GET', ep),
  post: (ep, d) => AdminAPI.request('POST', ep, d),
  put: (ep, d) => AdminAPI.request('PUT', ep, d),
  del: (ep) => AdminAPI.request('DELETE', ep),
};

/* ── Highlight active nav item ── */
document.addEventListener('DOMContentLoaded', () => {
  const path = location.pathname.split('/').pop();
  document.querySelectorAll('.nav-item').forEach(a => {
    const href = a.getAttribute('href') || '';
    if (href.includes(path) && path) a.classList.add('active');
  });
});

/* ── Format helpers ── */
const fmt = {
  currency: n => 'PKR ' + Number(n).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
  date: s => new Date(s).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }),
  num: n => Number(n).toLocaleString(),
};

/* ── Image drag-and-drop handler ── */
function handleImageDrop(e) {
  e.preventDefault();
  document.getElementById('uploadZone').style.borderColor = '';
  const dt    = e.dataTransfer;
  const files = dt.files;
  if (!files.length) return;
  const input = document.getElementById('imgInput');
  // DataTransfer is read-only so we simulate via previewImages directly
  previewImages({ files });
  // Store files for upload
  if (input && window.DataTransfer) {
    try {
      const transfer = new DataTransfer();
      [...files].forEach(f => transfer.items.add(f));
      input.files = transfer.files;
    } catch(e) { /* Firefox fallback */ }
  }
}

/* ── Safe escape ── */
function escapeHtml(s) {
  return String(s || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function isTruthy(value) {
  return value === true || value === 1 || value === '1' || value === 'true';
}
