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

  /* ── Free shipping threshold (fetched from API, cached per session) ── */
  let _freeShipThreshold = 5000;
  (async () => {
    try {
      const res = await fetch('/backend/index.php?_url=/api/settings', { credentials: 'same-origin' });
      const data = await res.json();
      if (data?.data?.free_shipping_threshold) {
        _freeShipThreshold = parseFloat(data.data.free_shipping_threshold) || 5000;
      }
    } catch (_) {}
  })();

  /* ── Calculate totals ── */
  function totals() {
    const subtotal = state.items.reduce((s, i) => s + i.price * i.qty, 0);
    const discount = state.discount || 0;
    const threshold = _freeShipThreshold;
    const shipping = (subtotal - discount) >= threshold ? 0 : 250;
    const tax = parseFloat(((subtotal - discount) * 0.10).toFixed(2));
    const total = parseFloat((subtotal - discount + shipping + tax).toFixed(2));
    return { subtotal, discount, shipping, tax, total, count: count() };
  }

  /* ── Sync state from server ── */
  function syncStateFromServer(data) {
    if (!data) return;
    const items = data.items || [];
    state.items = items.map(i => ({
      _key: `${i.product_id}_${i.variant_id || 0}`,
      id: i.cart_id,
      cart_id: i.cart_id,
      product_id: i.product_id,
      variant_id: i.variant_id || null,
      variant_name: i.variant_name || null,
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
    const productId = product.product_id || product.id;
    const variantId = product.variant_id || null;
    const name = product.product_name || product.name || 'Item';

    try {
      const res = await API.cart.add(productId, qty, variantId);
      if (res && res.success && res.data && res.data.items) {
        syncStateFromServer(res.data);
        syncUI();
        showAddedFeedback(name);
        return { success: true, data: res.data };
      } else {
        throw new Error(res?.message || 'Could not add item to cart.');
      }
    } catch (err) {
      throw err;
    }
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
    document.querySelectorAll('[data-cart-count], #cartCount, #cartCountBadge, #mobileCartBadge, .nav-badge, .mobile-cart-badge').forEach(el => {
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

  function renderRows(items) {
    const container = document.getElementById('cartItemsList');
    if (!container) return;
    if (!items || !items.length) {
      document.getElementById('cartEmptyState')?.classList.remove('hidden');
      document.getElementById('cartContent')?.classList.add('hidden');
      container.innerHTML = '';
      return;
    }
    document.getElementById('cartEmptyState')?.classList.add('hidden');
    document.getElementById('cartContent')?.classList.remove('hidden');

    container.innerHTML = items.map(item => {
      const name = escapeHtml(item.product_name || item.name || '');
      const variantText = item.variant_name ? ` <span class="cart-item-variant" style="font-size:var(--text-xs);color:var(--color-muted);font-weight:normal">(${escapeHtml(item.variant_name)})</span>` : '';
      const image = item.image ? escapeHtml(item.image) : '';
      const unitPrice = parseFloat(item.final_price ?? item.price ?? 0);
      const qty = parseInt(item.quantity ?? item.qty ?? 1, 10);
      const id = item.cart_id || item.id || '';
      return `
      <div class="cart-item" id="cart-row-${id}" data-key="${item._key || ''}">
        <div class="cart-item-img">
          ${image
            ? `<img src="${image}" alt="${name}" loading="lazy">`
            : `<img src="/images/logo.png" alt="Mithooos Placeholder" style="object-fit:contain; padding:8px; width:100%; height:100%; background:var(--color-surface);" loading="lazy">`}
        </div>
        <div class="cart-item-info">
          <div class="cart-item-top-row">
            <p class="cart-item-name">${name}${variantText}</p>
            <button class="cart-item-remove" onclick="Cart.removeFromPage('${id}', '${item._key || ''}')"
              aria-label="Remove item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
              </svg>
            </button>
          </div>
          <div class="qty-selector">
            <button class="qty-btn" onclick="Cart.changeQty('${id}', '${item._key || ''}', -1)"
              aria-label="Decrease quantity">−</button>
            <input class="qty-val" value="${qty}" readonly id="qty-${id}">
            <button class="qty-btn" onclick="Cart.changeQty('${id}', '${item._key || ''}', 1)"
              aria-label="Increase quantity">+</button>
          </div>
          <p class="cart-item-price" id="item-total-${id}"
             data-unit="${unitPrice}">
            ${formatPrice(unitPrice * qty)}
          </p>
        </div>
      </div>`;
    }).join('');
  }

  /* ── Render cart page (Instant optimistic render + background sync) ── */
  async function renderCartPage() {
    const container = document.getElementById('cartItemsList');
    if (!container) return;

    // 1. Instant optimistic render from existing local state (0ms delay)
    if (state.items && state.items.length) {
      renderRows(state.items);
      updateCartSummary();
    } else {
      document.getElementById('cartEmptyState')?.classList.remove('hidden');
      document.getElementById('cartContent')?.classList.add('hidden');
    }

    // 2. Fetch fresh cart from server in background and sync
    try {
      const res = await API.cart.get(state.coupon || null);
      if (res && res.success && res.data) {
        const items = res.data.items || [];
        state.items = items.map(i => ({
          _key: `${i.product_id}_${i.variant_id || 0}`,
          id: i.cart_id,
          cart_id: i.cart_id,
          product_id: i.product_id,
          variant_id: i.variant_id || null,
          variant_name: i.variant_name || null,
          name: i.product_name,
          price: parseFloat(i.final_price ?? i.price),
          image: i.image || null,
          qty: i.quantity,
        }));
        state.discount = res.data.discount || 0;
        save();
        renderRows(items);
        updateCartSummary();
      } else if (!state.items.length) {
        renderRows([]);
        updateCartSummary();
      }
    } catch (e) {
      if (!state.items.length) {
        renderRows([]);
        updateCartSummary();
      }
    }
  }

  const MAX_QTY = 10;
  let _qtys = {};
  function changeQty(id, key, delta) {
    const inp = document.getElementById(`qty-${id}`);
    if (!inp) return;
    const current = parseInt(inp.value) || 1;
    if (delta > 0 && current >= MAX_QTY) {
      if (typeof showToast === 'function') showToast(`Maximum ${MAX_QTY} items per product.`, 'info');
      return;
    }
    const newQty = Math.max(1, Math.min(MAX_QTY, current + delta));
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
