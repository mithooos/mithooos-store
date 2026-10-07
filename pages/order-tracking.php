<!DOCTYPE html>
<html lang="en">
<?php include '../includes/frontend/head.php'; ?>
<body>

<!-- Navbar -->
<?php include '../includes/frontend/navbar.php'; ?>


<?php include '../includes/frontend/mobile-nav.php'; ?>


<main class="tracking-section">
  <div class="container">

    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom:var(--space-6)">
      <a href="../index.php">Home</a><span class="breadcrumb-sep">/</span>
      <a href="account.php">My Account</a><span class="breadcrumb-sep">/</span>
      <span>Track Order</span>
    </nav>

    <!-- Search panel -->
    <div class="tracking-search-panel">
      <div style="width:56px;height:56px;background:var(--color-surface-alt);border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-5)">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
      </div>
      <h2>Track Your Order</h2>
      <p>Enter your order number and the email address used at checkout to see your delivery status.</p>
      <div class="tracking-input-row">
        <input type="text" id="orderInput" class="form-input" placeholder="Order number (e.g. ORD-0042)"
               style="flex:1" autocomplete="off" aria-label="Order number">
        <button class="btn btn-primary" onclick="lookupOrder()" id="trackBtn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          Track
        </button>
      </div>
      <p style="font-size:var(--text-xs);color:var(--color-muted);margin-top:var(--space-4)">
        Demo: try <strong>ORD-0042</strong>, <strong>ORD-0041</strong>, or <strong>ORD-0040</strong>
      </p>
    </div>

    <!-- Result card -->
    <div class="tracking-result" id="trackingResult">

      <!-- Header meta -->
      <div class="tracking-result-header" id="resultHeader"></div>

      <!-- Timeline -->
      <div class="tracking-timeline">
        <p class="timeline-title">Delivery Progress</p>
        <div class="timeline" id="trackingTimeline"></div>
      </div>

      <!-- Items -->
      <div class="tracking-items">
        <p class="tracking-items-title">Items in This Order</p>
        <div id="trackingItemsList"></div>
      </div>

    </div>

    <!-- Not found -->
    <div class="tracking-not-found" id="trackingNotFound">
      <div class="sk-empty__icon" style="margin:0 auto var(--space-5)">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </div>
      <h3 style="font-size:var(--text-xl);color:var(--color-ink);margin-bottom:var(--space-2)">Order not found</h3>
      <p style="color:var(--color-muted);font-size:var(--text-sm);max-width:340px;margin:0 auto var(--space-8)">
        We couldn't find that order number. Double-check it and try again, or
        <a href="contact.php" style="color:var(--color-primary)">contact support</a>.
      </p>
      <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
    </div>

  </div>
</main>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<script src="../js/utils.js"></script>
<script>
'use strict';

/* ── Demo order data (replace with API.orders.get(id) when DB is live) ── */
const DEMO_ORDERS = {
  'ORD-0042': {
    id: 'ORD-0042', date: '2026-04-07', status: 'shipped',
    customer: 'Sarah Mitchell', email: 'sarah@example.com',
    total: 129.99, estimatedDelivery: '2026-04-13',
    carrier: 'FedEx', trackingNumber: '794644792798',
    steps: [
      { key:'ordered',    label:'Order Placed',     desc:'Your order was received and confirmed.',         time:'Apr 7, 10:23 AM', done:true },
      { key:'confirmed',  label:'Payment Confirmed', desc:'Payment processed successfully.',               time:'Apr 7, 10:25 AM', done:true },
      { key:'processing', label:'Being Prepared',   desc:'Your items are being packed.',                  time:'Apr 8, 2:14 PM',  done:true },
      { key:'shipped',    label:'Shipped',           desc:'Package picked up by FedEx. Tracking: 7946447927.', time:'Apr 9, 9:42 AM', done:false, active:true },
      { key:'delivered',  label:'Delivered',         desc:'Estimated delivery by Apr 13.',                time:'Est. Apr 13',     done:false },
    ],
    items: [
      { name:'Classic Linen Shirt', variant:'Size M · White', qty:1, price:34.99 },
      { name:'Slim Chino Trousers', variant:'Size 32 · Navy',  qty:2, price:44.99 },
    ],
  },
  'ORD-0041': {
    id: 'ORD-0041', date: '2026-04-06', status: 'confirmed',
    customer: 'James Thompson', email: 'james@example.com',
    total: 64.99, estimatedDelivery: '2026-04-14',
    carrier: 'UPS', trackingNumber: '1Z999AA10123456784',
    steps: [
      { key:'ordered',    label:'Order Placed',     desc:'Your order was received and confirmed.',  time:'Apr 6, 3:11 PM', done:true },
      { key:'confirmed',  label:'Payment Confirmed', desc:'Payment processed successfully.',        time:'Apr 6, 3:13 PM', done:true },
      { key:'processing', label:'Being Prepared',   desc:'Your items are being packed.',           time:'',               done:false, active:true },
      { key:'shipped',    label:'Shipped',           desc:'We will email you when this ships.',    time:'',               done:false },
      { key:'delivered',  label:'Delivered',         desc:'',                                      time:'',               done:false },
    ],
    items: [
      { name:'Vintage Denim Jacket', variant:'Size L · Blue Wash', qty:1, price:64.99 },
    ],
  },
  'ORD-0040': {
    id: 'ORD-0040', date: '2026-04-05', status: 'pending',
    customer: 'Emma Roberts', email: 'emma@example.com',
    total: 219.50, estimatedDelivery: '2026-04-15',
    carrier: '—', trackingNumber: '—',
    steps: [
      { key:'ordered',    label:'Order Placed',     desc:'Your order was received.',               time:'Apr 5, 11:47 AM', done:true },
      { key:'confirmed',  label:'Payment Confirmed', desc:'Awaiting payment confirmation.',        time:'',               done:false, active:true },
      { key:'processing', label:'Being Prepared',   desc:'',  time:'', done:false },
      { key:'shipped',    label:'Shipped',           desc:'',  time:'', done:false },
      { key:'delivered',  label:'Delivered',         desc:'',  time:'', done:false },
    ],
    items: [
      { name:'Floral Wrap Dress',    variant:'Size S · Pink', qty:2, price:54.99 },
      { name:'Ribbed Knit Sweater',  variant:'Size M · Cream', qty:1, price:39.99 },
      { name:'Classic Linen Shirt',  variant:'Size XS · White', qty:2, price:34.99 },
    ],
  },
};

const STATUS_BADGE = {
  pending:    { text:'Pending',    bg:'#FEF3C7', color:'#92400E' },
  pending_verification: { text:'Pending Verification', bg:'#FEF3C7', color:'#92400E' },
  confirmed:  { text:'Confirmed',  bg:'#D1FAE5', color:'#065F46' },
  processing: { text:'Processing', bg:'#DBEAFE', color:'#1E40AF' },
  shipped:    { text:'Shipped',    bg:'#EDE9FE', color:'#5B21B6' },
  delivered:  { text:'Delivered',  bg:'#D1FAE5', color:'#065F46' },
  cancelled:  { text:'Cancelled',  bg:'#FEE2E2', color:'#991B1B' },
};

const STEP_ICONS = {
  ordered:    '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
  confirmed:  '<polyline points="20 6 9 17 4 12"/>',
  processing: '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>',
  shipped:    '<path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
  delivered:  '<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
};

function lookupOrder() {
  const raw = document.getElementById('orderInput').value.trim().toUpperCase();
  const id  = raw.startsWith('ORD-') ? raw : 'ORD-' + raw;

  const result   = document.getElementById('trackingResult');
  const notFound = document.getElementById('trackingNotFound');
  const btn      = document.getElementById('trackBtn');

  // Loading state
  btn.disabled = true;
  btn.innerHTML = '<div class="spinner"></div> Searching…';

  // Simulate API call delay
  setTimeout(() => {
    btn.disabled = false;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg> Track';

    // Try real API first
    const token = localStorage.getItem('pp_token');
    const basePath = window.location.pathname.includes('/mithooos') ? '/mithooos' : '';
    fetch(`${basePath}/backend/index.php?_url=/api/orders/` + encodeURIComponent(id), {
      headers: token ? { 'Authorization': 'Bearer ' + token } : {}
    })
    .then(r => r.json())
    .then(res => {
      if (res.success && res.data) {
        renderOrder(normalizeApiOrder(res.data));
      } else {
        throw new Error('not found');
      }
    })
    .catch(() => {
      // Fallback to demo data
      const order = DEMO_ORDERS[id];
      if (order) {
        renderOrder(order);
      } else {
        result.classList.remove('show');
        notFound.classList.add('show');
      }
    });
  }, 600);
}

function normalizeApiOrder(data) {
  const raw = data || {};
  const items = (raw.items || []).map(item => ({
    name: item.product_name || 'Product',
    variant: item.product_sku ? `SKU ${item.product_sku}` : 'Standard item',
    qty: Number(item.quantity || 0),
    price: Number(item.price_at_purchase || item.item_total || 0),
  }));

  const customer = [raw.first_name, raw.last_name].filter(Boolean).join(' ') || 'Customer';
  let stepStatus = raw.order_status || 'pending';
  let isPendingVerification = (raw.payment_method === 'easypaisa' && raw.payment_status === 'pending');
  if (isPendingVerification) {
      stepStatus = 'pending_verification';
  }
  
  const steps = [
    { key: 'ordered', label: 'Order Placed', desc: 'Your order was received and confirmed.', time: raw.created_at ? formatDate(raw.created_at) : '', done: true },
    { key: 'confirmed', label: 'Payment Confirmed', desc: 'Payment processed successfully.', time: '', done: ['confirmed','processing','shipped','delivered'].includes(stepStatus) },
    { key: 'processing', label: 'Being Prepared', desc: 'Your items are being packed.', time: '', done: ['processing','shipped','delivered'].includes(stepStatus), active: stepStatus === 'processing' },
    { key: 'shipped', label: 'Shipped', desc: raw.tracking_number ? `Tracking number: ${raw.tracking_number}` : 'Your order has been shipped.', time: '', done: ['shipped','delivered'].includes(stepStatus), active: stepStatus === 'shipped' },
    { key: 'delivered', label: 'Delivered', desc: stepStatus === 'delivered' ? 'Order delivered successfully.' : 'Estimated delivery in progress.', time: '', done: stepStatus === 'delivered', active: stepStatus === 'delivered' },
  ];

  return {
    id: raw.order_number || raw.id || 'N/A',
    date: raw.created_at || raw.date || new Date().toISOString(),
    status: stepStatus,
    customer,
    email: raw.email || '',
    total: Number(raw.total_amount || raw.total || 0),
    estimatedDelivery: raw.estimated_delivery || '',
    carrier: raw.shipping_method || '—',
    trackingNumber: raw.tracking_number || '—',
    steps,
    items: items.length ? items : [{ name: 'Order items', variant: 'Details available in account', qty: 1, price: Number(raw.total_amount || 0) }],
  };
}

function renderOrder(order) {
  document.getElementById('trackingNotFound').classList.remove('show');

  // Header
  const badge = STATUS_BADGE[order.status] || STATUS_BADGE.pending;
  document.getElementById('resultHeader').innerHTML = `
    <div>
      <div class="trk-meta-label">Order</div>
      <div class="trk-meta-value">#${escapeHtml(order.id)}</div>
    </div>
    <div>
      <div class="trk-meta-label">Date Placed</div>
      <div class="trk-meta-value">${formatDate(order.date)}</div>
    </div>
    <div>
      <div class="trk-meta-label">Status</div>
      <div class="trk-meta-value">
        <span style="background:${badge.bg};color:${badge.color};padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:700">
          ${badge.text}
        </span>
      </div>
    </div>
    <div>
      <div class="trk-meta-label">Total</div>
      <div class="trk-meta-value">${formatPrice(order.total)}</div>
    </div>
    <div>
      <div class="trk-meta-label">Est. Delivery</div>
      <div class="trk-meta-value">${formatDate(order.estimatedDelivery)}</div>
    </div>
    <div>
      <div class="trk-meta-label">Carrier</div>
      <div class="trk-meta-value">${escapeHtml(order.carrier)}</div>
    </div>`;

  // Timeline
  document.getElementById('trackingTimeline').innerHTML = order.steps.map(step => {
    const cls = step.done ? 'is-done' : step.active ? 'is-active' : 'is-pending';
    const icon = STEP_ICONS[step.key] || STEP_ICONS.ordered;
    return `
    <div class="timeline-step ${cls}">
      <div class="timeline-dot">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          ${step.done ? '<polyline points="20 6 9 17 4 12"/>' : icon}
        </svg>
      </div>
      <div class="timeline-body">
        <p class="timeline-step-title">${escapeHtml(step.label)}</p>
        ${step.desc ? `<p class="timeline-step-desc">${escapeHtml(step.desc)}</p>` : ''}
        ${step.time ? `<p class="timeline-step-time">${escapeHtml(step.time)}</p>` : ''}
      </div>
    </div>`;
  }).join('');

  // Items
  document.getElementById('trackingItemsList').innerHTML = order.items.map(item => `
    <div class="tracking-item">
      <div class="tracking-item-img">
        <div style="width:100%;height:100%;background:linear-gradient(135deg,#FFF0EB,#FFE4F0);display:flex;align-items:center;justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-muted)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
      </div>
      <div style="flex:1">
        <p class="tracking-item-name">${escapeHtml(item.name)}</p>
        <p class="tracking-item-meta">${escapeHtml(item.variant)} &nbsp;·&nbsp; Qty: ${item.qty}</p>
      </div>
      <p class="tracking-item-price">${formatPrice(parseFloat(item.price) * item.qty)}</p>
    </div>`).join('');

  document.getElementById('trackingResult').classList.add('show');
  document.getElementById('trackingResult').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ── Helpers: escapeHtml and formatDate now come from js/utils.js ── */

/* ── Enter key submits ── */
document.getElementById('orderInput')?.addEventListener('keydown', e => {
  if (e.key === 'Enter') lookupOrder();
});

/* ── Sync cart count ── */
document.addEventListener('DOMContentLoaded', () => {
  const items = JSON.parse(localStorage.getItem('pp_cart') || '[]');
  const count = items.reduce((n, i) => n + (i.qty || 1), 0);
  document.querySelectorAll('#cartCount').forEach(el => el.textContent = count);

  // Auto-lookup if order param in URL
  const params = new URLSearchParams(location.search);
  const orderId = params.get('order') || params.get('id');
  if (orderId) {
    document.getElementById('orderInput').value = orderId;
    lookupOrder();
  }
});
</script>
<script src="../js/site-footer.js"></script>
</body>
</html>
