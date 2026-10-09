<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>

<!-- Minimal checkout nav -->
<?php include __DIR__ . '/../includes/frontend/navbar.php'; ?>


<?php include __DIR__ . '/../includes/frontend/mobile-nav.php'; ?>


<div class="container" id="checkoutWrap">
  <!-- Step indicator -->
  <div class="checkout-steps" style="padding-top:var(--space-8);max-width:500px;margin:0 auto var(--space-2)">
    <div class="ck-step done" id="stepInd1"><div class="ck-num">1</div><span>Shipping</span></div>
    <div class="ck-line done" id="line1"></div>
    <div class="ck-step active" id="stepInd2"><div class="ck-num">2</div><span>Payment</span></div>
    <div class="ck-line" id="line2"></div>
    <div class="ck-step" id="stepInd3"><div class="ck-num">3</div><span>Review</span></div>
  </div>

  <div class="checkout-layout">
    <!-- LEFT — forms -->
    <div>

      <!-- STEP 1: Shipping -->
      <div class="ck-panel" id="step1Panel">
        <div class="ck-panel-title">
          <div class="step-num">1</div>
          Shipping Address
        </div>
        <form id="shippingForm" novalidate>
          <div class="form-row" style="margin-bottom:var(--space-5)">
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="ckFirst">First Name *</label>
              <input type="text" id="ckFirst" class="form-input" placeholder="John" required>
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="ckLast">Last Name *</label>
              <input type="text" id="ckLast" class="form-input" placeholder="Doe" required>
            </div>
          </div>
          <div class="form-row" style="margin-bottom:var(--space-5)">
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="ckEmail">Email Address *</label>
              <input type="email" id="ckEmail" class="form-input" placeholder="john@example.com" required>
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="ckPhone">Phone Number *</label>
              <input type="tel" id="ckPhone" class="form-input" placeholder="+1 (555) 000-0000" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="ckStreet">Street Address *</label>
            <input type="text" id="ckStreet" class="form-input" placeholder="123 Main Street" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="ckApt">Apartment / Suite <span style="font-weight:400;color:var(--color-muted)">(optional)</span></label>
            <input type="text" id="ckApt" class="form-input" placeholder="Apt 4B">
          </div>
          <div class="form-row" style="margin-bottom:var(--space-5)">
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="ckCountry">Country *</label>
              <select id="ckCountry" class="form-select" required>
                <option value="">Select country…</option>
                <option value="PK" selected>Pakistan</option>
                <option value="GB">United Kingdom</option>
                <option value="CA">Canada</option>
                <option value="AU">Australia</option>
                <option value="US">United States</option>
                <option value="IN">India</option>
                <option value="DE">Germany</option>
                <option value="FR">France</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="ckState">State / Province *</label>
              <input type="text" id="ckState" class="form-input" placeholder="New York" required>
            </div>
          </div>
          <div class="form-row" style="margin-bottom:var(--space-6)">
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="ckCity">City *</label>
              <input type="text" id="ckCity" class="form-input" placeholder="New York" required>
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="ckZip">ZIP / Postal Code *</label>
              <input type="text" id="ckZip" class="form-input" placeholder="10001" required>
            </div>
          </div>

          <div style="border-top:1px solid var(--color-border-soft);padding-top:var(--space-6)">
            <p class="form-label" style="margin-bottom:var(--space-4)">Shipping Method</p>
            <div style="display:flex;flex-direction:column;gap:var(--space-3)">
              <label class="payment-option selected" for="shipStd" style="cursor:pointer">
                <input type="radio" id="shipStd" name="shipMethod" value="standard" checked>
                <div style="flex:1">
                  <p style="font-weight:700;font-size:var(--text-sm);color:var(--color-ink)">Standard Shipping</p>
                  <p style="font-size:var(--text-xs);color:var(--color-muted)">3–5 business days</p>
                </div>
                <span style="font-weight:700;color:var(--color-success)" id="shipStdPrice">FREE</span>
              </label>
              <label class="payment-option" for="shipExp" style="cursor:pointer">
                <input type="radio" id="shipExp" name="shipMethod" value="express">
                <div style="flex:1">
                  <p style="font-weight:700;font-size:var(--text-sm);color:var(--color-ink)">Express Shipping</p>
                  <p style="font-size:var(--text-xs);color:var(--color-muted)">1–2 business days</p>
                </div>
                <span style="font-weight:700;color:var(--color-ink)">PKR 500.00</span>
              </label>
              <label class="payment-option" for="shipOvn" style="cursor:pointer">
                <input type="radio" id="shipOvn" name="shipMethod" value="overnight">
                <div style="flex:1">
                  <p style="font-weight:700;font-size:var(--text-sm);color:var(--color-ink)">Overnight Delivery</p>
                  <p style="font-size:var(--text-xs);color:var(--color-muted)">Next business day</p>
                </div>
                <span style="font-weight:700;color:var(--color-ink)">PKR 1,000.00</span>
              </label>
            </div>
          </div>

          <button type="button" onclick="goStep2()" class="btn btn-primary btn-lg" style="width:100%;margin-top:var(--space-6)">
            Continue to Payment
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </form>
      </div>

      <!-- STEP 2: Payment -->
      <div class="ck-panel inactive" id="step2Panel">
        <div class="ck-panel-title"><div class="step-num">2</div>Payment Method</div>
        <div class="payment-methods">
          <label class="payment-option selected" for="payCod" style="cursor:pointer">
            <input type="radio" id="payCod" name="payMethod" value="cod" checked onchange="toggleEasypaisaFields()">
            <div style="flex:1"><p style="font-weight:700;font-size:var(--text-sm)">Cash on Delivery (COD)</p><p style="font-size:var(--text-xs);color:var(--color-muted)">Pay when you receive the order</p></div>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-ink)" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
          </label>
          <label class="payment-option" for="payEasypaisa" style="cursor:pointer">
            <input type="radio" id="payEasypaisa" name="payMethod" value="easypaisa" onchange="toggleEasypaisaFields()">
            <div style="flex:1"><p style="font-weight:700;font-size:var(--text-sm)">Easypaisa / Online Transfer</p><p style="font-size:var(--text-xs);color:var(--color-muted)">Upload a screenshot of your payment</p></div>
            <span style="font-weight:900;color:#00c853;font-size:.85rem">easypaisa</span>
          </label>
        </div>

        <!-- Easypaisa fields -->
        <div class="card-fields" id="easypaisaFields" style="display:none;background:#f9fafb;border-radius:8px;padding:16px;border:1px dashed #d1d5db;margin-top:16px">
          <div style="margin-bottom:12px">
            <p style="font-size:var(--text-sm);font-weight:600;margin-bottom:4px">Please send payment to:</p>
            <div style="background:white;padding:12px;border-radius:6px;border:1px solid #e5e7eb">
              <p style="font-size:var(--text-sm);color:var(--color-muted);margin-bottom:2px">Account Title:</p>
              <p style="font-size:1rem;font-weight:700;color:var(--color-ink)">Mithooos Official</p>
              <p style="font-size:var(--text-sm);color:var(--color-muted);margin-bottom:2px;margin-top:8px">Account Number:</p>
              <p style="font-size:1.1rem;font-weight:800;color:var(--color-primary);letter-spacing:1px">0300-1234567</p>
            </div>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Upload Payment Screenshot <span style="color:red">*</span></label>
            <input type="file" id="paymentScreenshot" accept="image/jpeg,image/png,image/webp" style="display:block;width:100%;font-size:14px;padding:8px;background:white;border:1px solid #e5e7eb;border-radius:6px">
            <p style="font-size:11px;color:var(--color-muted);margin-top:4px">Max size: 5MB (JPG, PNG, WebP)</p>
          </div>
        </div>

        <!-- Billing address toggle -->
        <div style="margin-top:var(--space-5);padding-top:var(--space-5);border-top:1px solid var(--color-border-soft)">
          <label class="form-check">
            <input type="checkbox" id="sameAsBilling" checked>
            <span style="font-size:var(--text-sm);font-weight:500">Billing address same as shipping</span>
          </label>
        </div>

        <div class="ck-actions" style="display:flex;gap:var(--space-4);margin-top:var(--space-6)">
          <button type="button" onclick="goStep1()" class="btn btn-outline" style="flex:1">← Back</button>
          <button type="button" onclick="goStep3()" class="btn btn-primary" style="flex:2;height:50px">Review Order →</button>
        </div>
      </div>

      <!-- STEP 3: Review -->
      <div class="ck-panel inactive" id="step3Panel">
        <div class="ck-panel-title"><div class="step-num">3</div>Review & Place Order</div>
        <div id="reviewContent">
          <div style="background:var(--color-surface);border-radius:var(--radius-lg);padding:var(--space-4) var(--space-5);margin-bottom:var(--space-4)">
            <p style="font-size:var(--text-xs);font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--color-muted);margin-bottom:var(--space-2)">Shipping to</p>
            <p style="font-size:var(--text-sm);color:var(--color-ink);font-weight:600" id="reviewAddress">John Doe — 123 Main St, New York, NY 10001, US</p>
            <button onclick="goStep1()" class="btn-ghost btn-sm" style="margin-top:var(--space-1);font-size:.7rem;color:var(--color-primary);padding:0;background:none;border:none;cursor:pointer">Edit</button>
          </div>
          <div style="background:var(--color-surface);border-radius:var(--radius-lg);padding:var(--space-4) var(--space-5);margin-bottom:var(--space-4)">
            <p style="font-size:var(--text-xs);font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--color-muted);margin-bottom:var(--space-2)">Payment</p>
            <p style="font-size:var(--text-sm);color:var(--color-ink);font-weight:600" id="reviewPayment">Visa ending in 3456</p>
            <button onclick="goStep2()" class="btn-ghost btn-sm" style="margin-top:var(--space-1);font-size:.7rem;color:var(--color-primary);padding:0;background:none;border:none;cursor:pointer">Edit</button>
          </div>
        </div>
        <label class="form-check" style="margin-bottom:var(--space-5)">
          <input type="checkbox" id="termsAgree" required>
          <span style="font-size:var(--text-sm)">I agree to the <a href="/terms-of-service" style="color:var(--color-primary)">Terms of Service</a> and <a href="/privacy-policy" style="color:var(--color-primary)">Privacy Policy</a></span>
        </label>
        <div class="ck-actions" style="display:flex;gap:var(--space-4)">
          <button type="button" onclick="goStep2()" class="btn btn-outline" style="flex:1">← Back</button>
          <button type="button" onclick="placeOrder()" class="btn btn-primary place-order-btn" id="placeOrderBtn" style="flex:2;height:52px;font-size:var(--text-base)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span id="placeOrderLabel">Place Order</span>
          </button>
        </div>
      </div>

    </div><!-- /left -->

    <!-- RIGHT — order summary -->
    <div>
      <div class="ck-order-summary">
        <h2 style="font-family:var(--font-heading);font-size:var(--text-lg);font-weight:700;margin-bottom:var(--space-5)">Your Order</h2>

        <div id="ckSummaryItems">
          <p style="text-align:center;color:var(--color-muted);font-size:var(--text-sm);padding:var(--space-4) 0">Loading your order…</p>
        </div>

        <div class="ck-divider"></div>
        <div class="ck-row"><span style="color:var(--color-body)">Subtotal</span><span style="font-weight:600" id="ckSubtotal">PKR 0.00</span></div>
        <div class="ck-row" id="ckDiscountRow" style="display:none"><span style="color:var(--color-success)">Discount</span><span style="font-weight:600;color:var(--color-success)" id="ckDiscount">−PKR 0.00</span></div>
        <div class="ck-row"><span style="color:var(--color-body)" id="ckShippingLabel">Shipping</span><span style="font-weight:600" id="ckShipping">FREE</span></div>
        <div class="ck-row"><span style="color:var(--color-body)">Tax (10%)</span><span style="font-weight:600" id="ckTax">PKR 0.00</span></div>
        <div class="ck-row total"><span>Total</span><span id="ckTotal">PKR 0.00</span></div>

        <!-- Trust signals -->
        <div style="display:flex;flex-direction:column;gap:var(--space-3);margin-top:var(--space-5);padding-top:var(--space-5);border-top:1px solid var(--color-border-soft)">
          <div style="display:flex;align-items:center;gap:var(--space-2);font-size:var(--text-xs);color:var(--color-muted)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            256-bit SSL encryption
          </div>
          <div style="display:flex;align-items:center;gap:var(--space-2);font-size:var(--text-xs);color:var(--color-muted)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            30-day free returns
          </div>
          <div style="display:flex;align-items:center;gap:var(--space-2);font-size:var(--text-xs);color:var(--color-muted)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            Estimated delivery: 3–5 days
          </div>
        </div>
      </div>
    </div>
  </div><!-- /checkout-layout -->
</div><!-- /checkoutWrap -->

<!-- ORDER CONFIRMATION -->
<div id="confirmStep" class="container" style="padding:var(--space-8) 0 var(--space-16)">
  <div class="confirm-card">
    <div class="confirm-icon">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h1 style="font-size:var(--text-3xl);margin-bottom:var(--space-3)"><svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg> Order Placed!</h1>
    <p style="color:var(--color-body);font-size:var(--text-lg);margin-bottom:var(--space-6)">Thank you for shopping with Mithooos. Your order has been confirmed.</p>

    <div class="confirm-order-num">
      <p style="font-size:var(--text-xs);font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--color-muted);margin-bottom:var(--space-1)">Order Number</p>
      <p style="font-family:var(--font-heading);font-size:var(--text-2xl);font-weight:800" id="confirmOrderNum">—</p>
    </div>

    <div style="background:var(--color-surface);border-radius:var(--radius-xl);padding:var(--space-5);text-align:left;margin-bottom:var(--space-6)">
      <div style="display:flex;justify-content:space-between;margin-bottom:var(--space-3);font-size:var(--text-sm)"><span style="color:var(--color-muted)">Items</span><span style="font-weight:600" id="confirmItemCount">—</span></div>
      <div style="display:flex;justify-content:space-between;margin-bottom:var(--space-3);font-size:var(--text-sm)"><span style="color:var(--color-muted)">Total Paid</span><span style="font-weight:700;color:var(--color-ink)" id="confirmTotal">—</span></div>
      <div style="display:flex;justify-content:space-between;margin-bottom:var(--space-3);font-size:var(--text-sm)"><span style="color:var(--color-muted)">Estimated Delivery</span><span style="font-weight:600" id="confirmDelivery">—</span></div>
      <div style="display:flex;justify-content:space-between;font-size:var(--text-sm)"><span style="color:var(--color-muted)">Status</span><span class="order-status processing" id="confirmStatus">Processing</span></div>
    </div>

    <p style="font-size:var(--text-sm);color:var(--color-muted);margin-bottom:var(--space-6)">A confirmation email has been sent to <strong id="confirmEmail">—</strong></p>

    <div style="display:flex;gap:var(--space-4);flex-wrap:wrap;justify-content:center">
      <a href="/account" class="btn btn-outline">View My Orders</a>
      <a href="/shop" class="btn btn-primary">Continue Shopping</a>
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
let currentStep = 1;

// Checkout requires a logged-in user (the backend's POST /api/orders
// endpoint calls $auth->require()). Bounce early with a clear message
// rather than letting someone fill out three steps and then fail.
if (!API.auth.isLoggedIn()) {
  window.location.href = `/login?redirect=${encodeURIComponent('/checkout')}`;
}

// ── Load real cart contents into the order summary sidebar ──
let checkoutTotals = null;

async function loadOrderSummary() {
  try {
    const shipMethod = document.querySelector('input[name="shipMethod"]:checked')?.value || 'standard';
    const res = await API.cart.get(Cart.state.coupon || null, shipMethod);
    const items = res.data?.items || [];
    checkoutTotals = res.data;

    if (!items.length) {
      // Nothing to check out — send the shopper back to their cart
      window.location.href = "/cart";
      return;
    }

    const itemsEl = document.getElementById('ckSummaryItems');
    itemsEl.innerHTML = items.map(i => {
      const name  = escapeHtml(i.product_name || '');
      const image = i.image ? escapeHtml(i.image) : '';
      return `
        <div class="ck-summary-item">
          <div class="ck-item-thumb">
            ${image
              ? `<img src="${image}" alt="${name}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">`
              : `<div class="img-placeholder" style="width:100%;height:100%;background:linear-gradient(135deg,#EFF6FF,#DBEAFE)"></div>`}
            <div class="qty-pill">${parseInt(i.quantity, 10)}</div>
          </div>
          <span class="ck-item-name">${name}</span>
          <span class="ck-item-price">${formatPrice(parseFloat(i.final_price) * parseInt(i.quantity, 10))}</span>
        </div>`;
    }).join('');

    renderTotals(checkoutTotals);
  } catch (err) {
    showToast(err.message || 'Could not load your cart.', 'error');
    setTimeout(() => window.location.href = "/cart", 1500);
  }
}

function renderTotals(t) {
  document.getElementById('ckSubtotal').textContent = formatPrice(t.subtotal);
  document.getElementById('ckTax').textContent = formatPrice(t.tax);
  const shipping = parseFloat(t.shipping);
  document.getElementById('ckShipping').textContent = shipping === 0 ? 'FREE' : formatPrice(shipping);
  const stdPriceEl = document.getElementById('shipStdPrice');
  if (stdPriceEl) {
    stdPriceEl.textContent = shipping === 0 ? 'FREE' : formatPrice(shipping);
    stdPriceEl.style.color = shipping === 0 ? 'var(--color-success)' : 'var(--color-ink)';
  }
  document.getElementById('ckTotal').textContent = formatPrice(t.total);
  const discount = parseFloat(t.discount) || 0;
  const discountRow = document.getElementById('ckDiscountRow');
  if (discount > 0) {
    discountRow.style.display = 'flex';
    document.getElementById('ckDiscount').textContent = '−' + formatPrice(discount);
  } else {
    discountRow.style.display = 'none';
  }
  const label = document.getElementById('placeOrderLabel');
  if (label) label.textContent = `Place Order · ${formatPrice(t.total)}`;
}

function setStep(n) {
  currentStep = n;
  [1,2,3].forEach(i => {
    const panel = document.getElementById('step'+i+'Panel');
    panel.classList.toggle('inactive', i !== n);
    const ind = document.getElementById('stepInd'+i);
    ind.classList.remove('active','done');
    if(i < n) ind.classList.add('done');
    else if(i === n) ind.classList.add('active');
    if(i < n && document.getElementById('line'+i)) document.getElementById('line'+i).classList.add('done');
    else if(document.getElementById('line'+i)) document.getElementById('line'+i).classList.remove('done');
  });
  window.scrollTo({top:0,behavior:'smooth'});
}

// Shipping method selection highlight
document.querySelectorAll('input[name="shipMethod"]').forEach(r => {
  r.addEventListener('change', () => {
    document.querySelectorAll('input[name="shipMethod"]').forEach(x => x.closest('.payment-option').classList.remove('selected'));
    r.closest('.payment-option').classList.add('selected');
    loadOrderSummary();
  });
});
document.querySelectorAll('input[name="payMethod"]').forEach(r => {
  r.addEventListener('change', () => {
    document.querySelectorAll('input[name="payMethod"]').forEach(x => x.closest('.payment-option').classList.remove('selected'));
    r.closest('.payment-option').classList.add('selected');
  });
});

let shippingAddress = null;

function goStep2() {
  const first = document.getElementById('ckFirst').value.trim();
  const last = document.getElementById('ckLast').value.trim();
  const email = document.getElementById('ckEmail').value.trim();
  const phone = document.getElementById('ckPhone').value.trim();
  const street = document.getElementById('ckStreet').value.trim();
  const apt = document.getElementById('ckApt').value.trim();
  const city = document.getElementById('ckCity').value.trim();
  const state = document.getElementById('ckState').value.trim();
  const zip = document.getElementById('ckZip').value.trim();
  const country = document.getElementById('ckCountry').value;

  if (!first || !last || !email || !phone || !street || !city || !state || !zip || !country) {
    showToast('Please fill in all required fields', 'error');
    return;
  }

  shippingAddress = { first_name: first, last_name: last, email, phone, street, apt, city, state, zip, country };

  document.getElementById('reviewAddress').textContent = `${first} ${last} — ${street}, ${city}, ${state} ${zip}, ${country}`;
  document.getElementById('confirmEmail').textContent = email;
  setStep(2);
}
function goStep1() { setStep(1); }
let selectedPayMethod = 'cod';
let idempotencyKey = crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).substring(2);
function goStep3() {
  const payMethod = document.querySelector('input[name="payMethod"]:checked')?.value;
  if(payMethod === 'easypaisa') {
    const fileInput = document.getElementById('paymentScreenshot');
    if(!fileInput.files.length) { showToast('Please upload your payment screenshot','error'); return; }
  }
  selectedPayMethod = payMethod;
  document.getElementById('reviewPayment').textContent = payMethod === 'easypaisa' ? 'Easypaisa / Online Transfer' : 'Cash on Delivery (COD)';
  setStep(3);
}
async function placeOrder() {
  if(!document.getElementById('termsAgree').checked) { showToast('Please agree to the terms and conditions','error'); return; }
  if(!shippingAddress) { showToast('Please complete the shipping step first.','error'); goStep1(); return; }

  const btn = document.getElementById('placeOrderBtn');
  const originalHTML = btn.innerHTML;
  btn.innerHTML = '<div class="spinner"></div> Processing…';
  btn.disabled = true;

  const sameAsBilling = document.getElementById('sameAsBilling')?.checked ?? true;
  const shipMethod = document.querySelector('input[name="shipMethod"]:checked')?.value || 'standard';

  let paymentProofUrl = null;
  if (selectedPayMethod === 'easypaisa') {
    const fileInput = document.getElementById('paymentScreenshot');
    if (!fileInput.files.length) { showToast('Payment screenshot missing.', 'error'); return; }
    
    const formData = new FormData();
    formData.append('screenshot', fileInput.files[0]);
    
    try {
      const basePath = window.location.pathname.includes('/mithooos') ? '/mithooos' : '';
      const uploadRes = await fetch(`${basePath}/backend/index.php?_url=/api/upload-payment`, {
        method: 'POST',
        headers: {
          'X-CSRF-Token': sessionStorage.getItem('pp_csrf_token') || ''
        },
        body: formData
      }).then(r => r.json());
      if (uploadRes.success) {
        paymentProofUrl = uploadRes.data.url;
      } else {
        throw new Error(uploadRes.message || 'Upload failed');
      }
    } catch (e) {
      showToast(e.message || 'Failed to upload screenshot.', 'error');
      btn.disabled = false;
      btn.innerHTML = originalHTML;
      return;
    }
  }

  try {
    const placeRes = await API.orders.place({
      shipping_address: shippingAddress,
      billing_same: sameAsBilling,
      billing_address: shippingAddress,
      payment_method: selectedPayMethod,
      payment_proof_url: paymentProofUrl,
      shipping_method: shipMethod,
      coupon: Cart.state.coupon || undefined,
      idempotency_key: idempotencyKey,
    });

    const { order_id, order_number } = placeRes.data;

    // Fetch the full order back so the confirmation screen reflects exactly
    // what was actually charged and saved, not a client-side estimate.
    let order = null;
    try {
      const orderRes = await API.orders.get(order_id);
      order = orderRes.data;
    } catch { /* non-fatal — we still have order_number to show */ }

    document.getElementById('checkoutWrap').style.display = 'none';
    document.getElementById('confirmStep').style.display = 'block';
    document.getElementById('confirmOrderNum').textContent = order_number;

    if (order) {
      document.getElementById('confirmItemCount').textContent =
        `${order.items.length} item${order.items.length !== 1 ? 's' : ''}`;
      document.getElementById('confirmTotal').textContent = formatPrice(order.total_amount);
      
      let displayStatus = order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1);
      if (order.payment_method === 'easypaisa' && order.payment_status === 'pending') {
          displayStatus = 'Pending Verification';
      }
      document.getElementById('confirmStatus').textContent = displayStatus;
      
      document.getElementById('confirmEmail').textContent = order.email || shippingAddress.email;
    } else {
      document.getElementById('confirmTotal').textContent = checkoutTotals ? formatPrice(checkoutTotals.total) : '—';
    }

    // Estimated delivery: 3-5 business days from now (standard shipping only —
    // the backend doesn't yet vary this by shipping_method; see note above)
    const deliveryStart = new Date(); deliveryStart.setDate(deliveryStart.getDate() + 3);
    const deliveryEnd = new Date(); deliveryEnd.setDate(deliveryEnd.getDate() + 5);
    const fmt = d => d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    document.getElementById('confirmDelivery').textContent = `${fmt(deliveryStart)}–${fmt(deliveryEnd)}`;

    window.scrollTo({top:0,behavior:'smooth'});
  } catch (err) {
    showToast(err.message || 'Could not place your order. Please try again.', 'error');
    btn.disabled = false;
    btn.innerHTML = originalHTML;
  }
}
function toggleEasypaisaFields() {
  const val = document.querySelector('input[name="payMethod"]:checked')?.value;
  document.getElementById('easypaisaFields').style.display = val === 'easypaisa' ? 'block' : 'none';
}
// Start at step 1 fully active, load real cart data into the summary
setStep(1);
loadOrderSummary();
</script>
<script src="/js/site-footer.js"></script>
</body>
</html>
