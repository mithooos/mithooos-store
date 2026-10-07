/**
 * MITHOOOS — Search Autocomplete
 * Live search with dropdown, keyboard navigation, debounce
 */
'use strict';

class SearchAutocomplete {
  constructor(inputEl, opts = {}) {
    this.input      = typeof inputEl === 'string' ? document.querySelector(inputEl) : inputEl;
    this.opts       = { minChars: 2, maxResults: 8, debounce: 280, ...opts };
    this.dropdown   = null;
    this.timer      = null;
    this.activeIdx  = -1;
    this.results    = [];
    this.cache      = {};
    if (this.input) this.init();
  }

  init() {
    this.createDropdown();
    this.input.setAttribute('autocomplete', 'off');
    this.input.setAttribute('role', 'combobox');
    this.input.setAttribute('aria-expanded', 'false');
    this.input.setAttribute('aria-autocomplete', 'list');

    this.input.addEventListener('input',   () => this.onInput());
    this.input.addEventListener('keydown', (e) => this.onKeydown(e));
    this.input.addEventListener('focus',   () => { if (this.input.value.length >= this.opts.minChars) this.onInput(); });
    document.addEventListener('click', (e) => { if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) this.close(); });
  }

  createDropdown() {
    this.dropdown = document.createElement('div');
    this.dropdown.className = 'search-dropdown';
    this.dropdown.setAttribute('role', 'listbox');
    this.dropdown.style.cssText = `
      position:absolute; top:100%; left:0; right:0; z-index:1000;
      background:white; border-radius:12px; box-shadow:0 8px 32px rgba(26,26,46,.14);
      border:1px solid var(--color-border, #E8ECF0); overflow:hidden;
      display:none; margin-top:6px;
    `;
    const parent = this.input.closest('label, .nav-search, .search-wrap') || this.input.parentElement;
    parent.style.position = 'relative';
    parent.appendChild(this.dropdown);
  }

  onInput() {
    clearTimeout(this.timer);
    const q = this.input.value.trim();
    if (q.length < this.opts.minChars) { this.close(); return; }
    this.showLoading();
    this.timer = setTimeout(() => this.fetch(q), this.opts.debounce);
  }

  async fetch(q) {
    if (this.cache[q]) { this.render(this.cache[q], q); return; }
    try {
      const res = await API.products.search(q);
      const items = res.data || [];
      this.cache[q] = items;
      this.render(items, q);
    } catch {
      // Fallback to static demo data
      const demo = [
        { product_id: 1, product_name: 'Classic Linen Shirt', final_price: 34.99, slug: 'classic-linen-shirt' },
        { product_id: 2, product_name: 'Vintage Denim Jacket', final_price: 89.99, slug: 'vintage-denim-jacket' },
        { product_id: 3, product_name: 'Floral Wrap Dress', final_price: 54.99, slug: 'floral-wrap-dress' },
      ].filter(p => p.product_name.toLowerCase().includes(q.toLowerCase()));
      this.render(demo, q);
    }
  }

  highlight(text, q) {
    const re = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(re, '<mark style="background:rgba(255,107,53,.15);color:var(--color-primary,#FF6B35);border-radius:2px;padding:0 1px">$1</mark>');
  }

  render(items, q) {
    this.results = items;
    this.activeIdx = -1;
    const safeQ = escapeHtml(q);
    if (!items.length) {
      this.dropdown.innerHTML = `<div style="padding:1rem;font-size:.8rem;color:var(--color-muted,#9CA3AF);text-align:center">No results for "<strong>${safeQ}</strong>"</div>`;
    } else {
      this.dropdown.innerHTML = items.slice(0, this.opts.maxResults).map((item, i) => {
        const safeName  = escapeHtml(item.product_name);
        const safeSlug  = encodeURIComponent(item.slug || item.product_id || '');
        const safeImg   = item.image ? escapeHtml(item.image) : '';
        const safePrice = formatPrice(item.final_price || 0);
        return `
        <a href="${location.pathname.includes('/pages/') ? 'product.php' : 'pages/product.php'}?slug=${safeSlug}" class="search-result-item"
           role="option" data-idx="${i}"
           style="display:flex;align-items:center;gap:.75rem;padding:.625rem 1rem;text-decoration:none;color:inherit;transition:background .15s;border-bottom:1px solid #F9FAFB;">
          <div style="width:40px;height:48px;border-radius:8px;background:linear-gradient(135deg,#FFF0EB,#FFE4F0);flex-shrink:0;overflow:hidden">
            ${safeImg ? `<img src="${safeImg}" alt="" style="width:100%;height:100%;object-fit:cover" loading="lazy">` : ''}
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:.82rem;font-weight:600;color:#1A1A2E;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${this.highlight(safeName, safeQ)}</div>
          </div>
          <div style="font-size:.82rem;font-weight:700;color:#1A1A2E;flex-shrink:0">${safePrice}</div>
        </a>`;
      }).join('') +
        `<a href="shop.php?search=${encodeURIComponent(q)}"
            style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.625rem 1rem;font-size:.78rem;font-weight:600;color:var(--color-primary,#FF6B35);text-decoration:none;background:#FFF5F0">
           <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
           See all results for "${safeQ}"
         </a>`;
    }

    this.dropdown.querySelectorAll('.search-result-item').forEach(el => {
      el.addEventListener('mouseenter', () => this.setActive(parseInt(el.dataset.idx)));
      el.addEventListener('mouseleave', () => this.clearActive());
    });

    this.open();
  }

  showLoading() {
    this.dropdown.innerHTML = `<div style="padding:.875rem;display:flex;align-items:center;gap:.625rem;font-size:.78rem;color:var(--color-muted,#9CA3AF)"><div style="width:14px;height:14px;border:2px solid #E8ECF0;border-top-color:var(--color-primary,#FF6B35);border-radius:50%;animation:spin .7s linear infinite"></div>Searching…</div>`;
    this.open();
  }

  onKeydown(e) {
    if (!this.isOpen()) return;
    const items = this.dropdown.querySelectorAll('.search-result-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); this.setActive(Math.min(this.activeIdx + 1, items.length - 1)); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); this.setActive(Math.max(this.activeIdx - 1, -1)); }
    else if (e.key === 'Enter' && this.activeIdx >= 0) { e.preventDefault(); items[this.activeIdx]?.click(); }
    else if (e.key === 'Escape') this.close();
  }

  setActive(idx) {
    this.activeIdx = idx;
    this.dropdown.querySelectorAll('.search-result-item').forEach((el, i) => {
      el.style.background = i === idx ? 'var(--color-surface-alt, #FFF5F0)' : '';
    });
  }

  clearActive() { this.setActive(-1); }
  open()  { this.dropdown.style.display = 'block'; this.input.setAttribute('aria-expanded', 'true'); }
  close() { this.dropdown.style.display = 'none';  this.input.setAttribute('aria-expanded', 'false'); this.activeIdx = -1; }
  isOpen() { return this.dropdown.style.display !== 'none'; }
}

/* ── Auto-init all search inputs on page ── */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-search="autocomplete"]').forEach(inp => {
    new SearchAutocomplete(inp);
  });
  // Also init main navbar search
  const navSearch = document.querySelector('.nav-search input[type="search"]');
  if (navSearch) new SearchAutocomplete(navSearch);
});
