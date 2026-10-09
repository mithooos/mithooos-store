<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>

<!-- Navbar -->
<?php include __DIR__ . '/../includes/frontend/navbar.php'; ?>


<?php include __DIR__ . '/../includes/frontend/mobile-nav.php'; ?>


<div class="container cart-section">
  <!-- Breadcrumb -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="/">Home</a><span class="breadcrumb-sep">/</span>
    <span>Shopping Cart</span>
  </nav>

  <h1 class="cart-section-title" style="margin-top:var(--space-4)">Shopping Cart</h1>
  <p class="cart-section-sub" id="cartSubtitle">Loading your cart…</p>

  <!-- Progress steps -->
  <div class="progress-steps" aria-label="Checkout progress">
    <div class="progress-step active"><div class="step-circle">1</div><span>Cart</span></div>
    <div class="step-line"></div>
    <div class="progress-step"><div class="step-circle">2</div><span>Shipping</span></div>
    <div class="step-line"></div>
    <div class="progress-step"><div class="step-circle">3</div><span>Payment</span></div>
    <div class="step-line"></div>
    <div class="progress-step"><div class="step-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m7 12 3 3 7-7"/></svg></div><span>Confirm</span></div>
  </div>

  <!-- Standalone empty state — shown only when the cart truly has no items -->
  <div id="cartEmptyState" class="hidden" style="text-align:center;padding:var(--space-20) var(--space-4)">
    <div style="width:80px;height:80px;background:var(--color-surface-alt);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-6);color:var(--color-muted);">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
    </div>
    <h3 style="font-size:var(--text-xl);color:var(--color-ink);margin-bottom:var(--space-3)">Your cart is empty</h3>
    <p style="color:var(--color-muted);margin-bottom:var(--space-8)">Looks like you haven't added anything to your cart yet.</p>
    <a href="/shop" class="btn btn-primary btn-lg">Start Shopping</a>
  </div>

  <div class="cart-layout" id="cartContent">

    <!-- Cart items -->
    <div>
      <div class="cart-items-panel">
        <div class="cart-panel-header">
          <span class="cart-panel-title" id="itemCountLabel">0 Items</span>
          <button onclick="Cart.clearAll()" style="font-size:var(--text-xs);color:var(--color-error);background:none;border:none;cursor:pointer;font-weight:600">Clear Cart</button>
        </div>

        <div id="cartItemsList"></div>

        <!-- Continue shopping -->
        <div style="padding-top:var(--space-5);border-top:1px solid var(--color-border-soft);margin-top:var(--space-2)">
          <a href="/shop" class="btn btn-ghost" style="font-size:var(--text-sm)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Continue Shopping
          </a>
        </div>
      </div>
    </div>

    <!-- Order summary -->
    <div>
      <div class="order-summary">
        <h2 class="summary-title">Order Summary</h2>

        <!-- Coupon -->
        <div class="coupon-form">
          <input type="text" class="coupon-input" id="couponInput" placeholder="Coupon code…" aria-label="Coupon code">
          <button class="btn-coupon" onclick="handleApplyCoupon()">Apply</button>
        </div>
        <div id="couponMsg" style="font-size:var(--text-xs);margin-bottom:var(--space-4);display:none"></div>

        <div class="summary-row"><span class="summary-label">Subtotal</span><span class="summary-val" id="summarySubtotal">PKR 0.00</span></div>
        <div class="summary-row"><span class="summary-label" style="color:var(--color-success)">Discount</span><span class="summary-val green" id="summaryDiscount">−PKR 0.00</span></div>
        <div class="summary-row"><span class="summary-label">Shipping</span><span class="summary-val" id="summaryShipping">Free</span></div>
        <div class="summary-row"><span class="summary-label">Estimated Tax (10%)</span><span class="summary-val" id="summaryTax">PKR 0.00</span></div>
        <div class="summary-row total">
          <span>Total</span>
          <span id="summaryTotal">PKR 0.00</span>
        </div>

        <a href="/checkout" class="btn btn-primary btn-lg" style="width:100%;justify-content:center">
          Proceed to Checkout
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>

        <div style="display:flex;align-items:center;justify-content:center;gap:var(--space-3);margin-top:var(--space-4)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <span style="font-size:var(--text-xs);color:var(--color-muted)">Secure 256-bit SSL encryption</span>
        </div>

        <!-- Payment icons -->
        <div style="display:flex;justify-content:center;gap:var(--space-2);margin-top:var(--space-4)">
          <span style="background:var(--color-surface);border-radius:var(--radius-sm);padding:4px 10px;font-size:.7rem;font-weight:700;color:var(--color-muted)">VISA</span>
          <span style="background:var(--color-surface);border-radius:var(--radius-sm);padding:4px 10px;font-size:.7rem;font-weight:700;color:var(--color-muted)">MC</span>
          <span style="background:var(--color-surface);border-radius:var(--radius-sm);padding:4px 10px;font-size:.7rem;font-weight:700;color:var(--color-muted)">PayPal</span>
          <span style="background:var(--color-surface);border-radius:var(--radius-sm);padding:4px 10px;font-size:.7rem;font-weight:700;color:var(--color-muted)">Stripe</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<script src="/js/utils.js"></script>
<script src="/js/api-client.js"></script>
<script src="/js/cart.js"></script>
<script src="/js/auth.js"></script>
<script src="/js/search.js"></script>
<script>
'use strict';
window.addEventListener('scroll', () => document.getElementById('navbar').classList.toggle('scrolled', scrollY > 20), { passive: true });

/* ── Apply coupon — thin wrapper around Cart.applyCoupon(), which validates
   server-side against the real coupons table (see backend/includes/Cart.php) ── */
async function handleApplyCoupon() {
  const input = document.getElementById('couponInput');
  const msg   = document.getElementById('couponMsg');
  const code  = input.value.trim();

  msg.style.display = 'block';
  const result = await Cart.applyCoupon(code);

  if (result.success) {
    msg.innerHTML  = `<svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg> Coupon applied! −${formatPrice(result.discount)} discount added.`;
    msg.style.color  = 'var(--color-success)';
    if (typeof showToast === 'function') showToast('Coupon applied', 'success');
  } else {
    msg.textContent = result.message.startsWith('Please')
      ? result.message
      : result.message;
    msg.style.color = 'var(--color-error)';
  }

  await Cart.renderCartPage();
}

/* ── Load real cart data from the backend on page load ── */
document.addEventListener('DOMContentLoaded', () => {
  Cart.renderCartPage();
});
</script>
<script src="/js/site-footer.js"></script>
</body>
</html>
