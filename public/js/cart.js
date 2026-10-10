/**
 * MITHOOOS — Cart Module
 * Handles all cart UI interactions and sync with API
 */
'use strict';

const Cart = (() => {
  const STORAGE_KEY = 'pp_cart';
  const COUPON_KEY  = 'pp_cart_coupon';

  /* ── Local state ── */
  let state = {
    items: JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'),
    coupon: localStorage.getItem(COUPON_KEY) || null,
    discount: 0,
  };

  /* ── Persist to localStorage ── */
  function save() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state.items));
    if (state.coupon) localStorage.setItem(COUPON_KEY, state.coupon);
    else localStorage.removeItem(COUPON_KEY);
  }

  /* ── Count total items ── */
  function count() {
    return state.items.reduce((n, i) => n + i.qty, 0);
  }

  /* ── Calculate totals ── */
  function totals() {
    const subtotal = state.items.reduce((s, i) => s + i.price * i.qty, 0);
    const discount = state.discount || 0;
    const shipping = (subtotal - discount) >= 50 ? 0 : 5.99;
    const tax = parseFloat(((subtotal - discount) * 0.10).toFixed(2));
    const total = parseFloat((subtotal - discount + shipping + tax).toFixed(2));
    return { subtotal, discount, shipping, tax, total, count: count() };
  }

  /* ── Sync state from server ── */
  function syncStateFromServer(data) {
    if (!data) return;
    const items = data.items || [];
    state.items = items.map(i => ({
      _key: `${i.product_id}`,
      id: i.cart_id,
      cart_id: i.cart_id,
      product_id: i.product_id,
      name: i.product_name,
      price: parseFloat(i.final_price ?? i.price),
      image: i.image || null,
      qty: i.quantity,
    }));
    state.discount = data.discount || 0;
    save();
  }

  /* ── Add item ── */
  async function add(product, qty = 1) {
    // Try API first
    try {
      const res = await API.cart.add(product.product_id || product.id, qty);
      if (res.success && res.data && res.data.items) {
          syncStateFromServer(res.data);
      }
    } catch (err) {
      if (err.message && err.message.includes('out of stock')) {
          if (typeof showToast === 'function') showToast('Item is out of stock.', 'error');
          return;
      }
      // Fallback: local storage
      const key = `${product.product_id || product.id}`;
      const existing = state.items.find(i => i._key === key);
      if (existing) {
        existing.qty = Math.min(existing.qty + qty, 10);
      } else {
        state.items.push({
          _key: key,
          id: product.product_id || product.id,
          product_id: product.product_id || product.id,
          name: product.product_name || product.name,
          price: parseFloat(product.final_price || product.price),
          image: product.image || null,
          qty,
        });
      }
      save();
    }
    syncUI();
    showAddedFeedback(product.product_name || product.name);
  }

  /* ── Clear entire cart (asks for confirmation) ── */
  async function clearAll() {
    if (!confirm('Remove all items from your cart?')) return;
    try {
      await API.cart.clear();
    } catch (e) {
      // Fallback: local storage
    }
    state.items   = [];
    state.coupon  = null;
    state.discount = 0;
    save();
    if (typeof showToast === 'function') showToast('Cart cleared', 'info');
    if (typeof renderCartPage === 'function') await renderCartPage();
    else syncUI();
  }

  /* ── Remove item ── */
  async function remove(cartId, localKey = null) {
    try {
      const res = await API.cart.remove(cartId);
      if (res.success && res.data && res.data.items) {
          syncStateFromServer(res.data);
      }
    } catch (e) {
      state.items = state.items.filter(i => i._key !== localKey && i.cart_id !== cartId);
      save();
    }
    syncUI();
  }

  /* ── Update quantity ── */
  async function update(cartId, qty, localKey = null) {
    if (qty <= 0) { remove(cartId, localKey); return; }
    try {
      const res = await API.cart.update(cartId, qty);
      if (res.success && res.data && res.data.items) {
          syncStateFromServer(res.data);
      }
    } catch (err) {
      if (err.message && err.message.includes('out of stock')) {
          if (typeof showToast === 'function') showToast('Not enough stock available.', 'warning');
      } else {
          const item = state.items.find(i => i.cart_id === cartId || i._key === localKey);
          if (item) item.qty = qty;
          save();
      }
    }
    syncUI();
  }


  async function applyCoupon(code) {
    if (!code || !code.trim()) {
      return { success: false, message: 'Please enter a coupon code.' };
    }
    const normalized = code.trim().toUpperCase();
    try {
      const res = await API.cart.get(normalized);
      if (res.success && res.data?.discount > 0) {
        state.coupon   = normalized;
        state.discount = res.data.discount;
        return { success: true, discount: state.discount };
      }
      state.coupon   = null;
      state.discount = 0;
      return { success: false, message: 'Invalid or expired coupon code.' };
    } catch (e) {
      return { success: false, message: 'Could not validate coupon. Please try again.' };
    }
  }

  /* ── Sync UI across all cart indicators ── */
  function syncUI() {
    const c = count();
    document.querySelectorAll('[data-cart-count], #cartCount, .nav-badge').forEach(el => {
      el.textContent = c;
    });
    window.dispatchEvent(new CustomEvent('pp:cart:changed', { detail: { count: c, totals: totals() } }));
  }

  /* ── Toast feedback ── */
  function showAddedFeedback(name) {
    if (typeof showToast === 'function') {
      showToast(`"${name}" added to cart`, 'success');
    }
    // Animate cart icon
    document.querySelectorAll('.nav-icon-btn[aria-label="Shopping Cart"], .nav-icon-btn[aria-label="Cart"]').forEach(btn => {
      btn.style.transform = 'scale(1.3)';
      btn.style.transition = 'transform .2s';
      setTimeout(() => { btn.style.transform = ''; }, 300);
    });
  }

  /* ── Render cart page ── */
  async function renderCartPage() {
    const container = document.getElementById('cartItemsList');
    if (!container) return;

    let items = [];
    try {
      const res = await API.cart.get(state.coupon || null);
      items = res.data?.items || [];
      // Keep local state (used by totals()) aligned with what the server returned,
      // so the summary panel matches what's actually rendered.
      state.items = items.map(i => ({
        _key: `${i.product_id}`,
        id: i.cart_id,
        cart_id: i.cart_id,
        product_id: i.product_id,
        name: i.product_name,
        price: parseFloat(i.final_price ?? i.price),
        image: i.image || null,
        qty: i.quantity,
      }));
      state.discount = res.data?.discount || state.discount || 0;
      save();
    } catch (e) {
      items = state.items;
    }

    if (!items.length) {
      document.getElementById('cartEmptyState')?.classList.remove('hidden');
      document.getElementById('cartContent')?.classList.add('hidden');
      container.innerHTML = '';
      updateCartSummary();
      return;
    }
    document.getElementById('cartEmptyState')?.classList.add('hidden');
    document.getElementById('cartContent')?.classList.remove('hidden');

    container.innerHTML = items.map(item => {
      const name  = escapeHtml(item.product_name || item.name || '');
      const image = item.image ? escapeHtml(item.image) : '';
      return `
      <div class="cart-item" id="cart-row-${item.cart_id || item.id}" data-key="${item._key || ''}">
        <div class="cart-item-img">
          ${image
            ? `<img src="${image}" alt="${name}" loading="lazy">`
            : `<img src="/images/logo.png" alt="Mithooos Placeholder" style="object-fit:contain; padding:8px; width:100%; height:100%; background:var(--color-surface);" loading="lazy">`}
        </div>
        <div class="cart-item-info">
          <div class="cart-item-top-row">
            <p class="cart-item-name">${name}</p>
            <button class="cart-item-remove" onclick="Cart.removeFromPage('${item.cart_id || item.id}', '${item._key || ''}')"
              aria-label="Remove item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
              </svg>
            </button>
          </div>
          <div class="qty-selector">
            <button class="qty-btn" onclick="Cart.changeQty('${item.cart_id || item.id}', '${item._key || ''}', -1)"
              aria-label="Decrease quantity">−</button>
            <input class="qty-val" value="${item.quantity || item.qty}" readonly id="qty-${item.cart_id || item.id}">
            <button class="qty-btn" onclick="Cart.changeQty('${item.cart_id || item.id}', '${item._key || ''}', 1)"
              aria-label="Increase quantity">+</button>
          </div>
          <p class="cart-item-price" id="item-total-${item.cart_id || item.id}"
             data-unit="${(item.final_price || item.price)}">
            ${formatPrice((item.final_price || item.price) * (item.quantity || item.qty))}
          </p>
        </div>
      </div>`;
    }).join('');

    updateCartSummary();
  }

  let _qtys = {};
  function changeQty(id, key, delta) {
    const inp = document.getElementById(`qty-${id}`);
    if (!inp) return;
    const current = parseInt(inp.value) || 1;
    const newQty = Math.max(1, Math.min(10, current + delta));
    inp.value = newQty;
    _qtys[id] = newQty;
    // Debounce API call
    clearTimeout(_qtys[`_t_${id}`]);
    _qtys[`_t_${id}`] = setTimeout(() => update(id, newQty, key), 500);

    const priceEl = document.getElementById(`item-total-${id}`);
    if (priceEl) {
      // data-unit is on the price element itself — set in the cart template
      const unitPrice = parseFloat(priceEl.dataset.unit || 0);
      if (unitPrice > 0) priceEl.textContent = formatPrice(unitPrice * newQty);
    }
    updateCartSummary();
  }

  function removeFromPage(id, key) {
    const row = document.getElementById(`cart-row-${id}`);
    if (row) {
      row.style.transition = 'all .3s ease';
      row.style.opacity = '0';
      row.style.transform = 'translateX(20px)';
      setTimeout(() => { row.remove(); updateCartSummary(); }, 300);
    }
    remove(id, key);
    if (typeof showToast === 'function') showToast('Item removed from cart', 'info');
  }

  function updateCartSummary() {
    const t = totals();
    const els = {
      '#summarySubtotal': formatPrice(t.subtotal),
      '#summaryShipping': t.shipping === 0 ? 'Free' : formatPrice(t.shipping),
      '#summaryTax': formatPrice(t.tax),
      '#summaryTotal': formatPrice(t.total),
      '#summaryDiscount': t.discount > 0 ? '−' + formatPrice(t.discount) : '−PKR 0.00',
    };
    Object.entries(els).forEach(([sel, val]) => {
      const el = document.querySelector(sel);
      if (el) el.textContent = val;
    });

    const itemCountLabel = document.getElementById('itemCountLabel');
    if (itemCountLabel) itemCountLabel.textContent = `${t.count} Item${t.count !== 1 ? 's' : ''}`;

    const subtitle = document.getElementById('cartSubtitle');
    if (subtitle) {
      subtitle.innerHTML = t.count
        ? `You have <strong>${t.count} item${t.count !== 1 ? 's' : ''}</strong> in your cart`
        : 'Your cart is <strong>empty</strong>';
    }

    syncUI();
  }

  return { add, remove, update, clearAll, applyCoupon, syncUI, totals, count, renderCartPage, changeQty, removeFromPage, state };
})();

// Sync the cart badge as soon as the page has a DOM to update — previously
// this only happened reactively after add/remove/update, so every page
// showed a stale "0" badge on first load regardless of actual cart contents.
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => Cart.syncUI());
} else {
  Cart.syncUI();
}
