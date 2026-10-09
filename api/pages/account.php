
<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>

<?php include __DIR__ . '/../includes/frontend/navbar.php'; ?>


<?php include __DIR__ . '/../includes/frontend/mobile-nav.php'; ?>


<div class="page-hero" style="padding-bottom:var(--space-8)">
  <div class="container page-hero-inner">
    <nav class="breadcrumb"><a href="/">Home</a><span class="breadcrumb-sep">/</span><span>My Account</span></nav>
    <h1>My Account</h1>
  </div>
</div>

<div class="container">
  <div class="account-layout">

    <!-- SIDEBAR -->
    <aside class="account-sidebar">
      <div class="account-avatar" id="acctAvatar">•</div>
      <p class="account-name" id="acctName">Loading…</p>
      <p class="account-email" id="acctEmail"></p>

      <nav>
        <button class="account-nav-item active" onclick="switchTab('dashboard',this)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard
        </button>
        <button class="account-nav-item" onclick="switchTab('orders',this)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
          My Orders
        </button>
        <button class="account-nav-item" onclick="switchTab('wishlist',this)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          Wishlist <span id="acctWishlistCount" style="background:var(--color-primary);color:white;border-radius:10px;padding:1px 7px;font-size:.65rem;font-weight:800;margin-left:auto">0</span>
        </button>
        <button class="account-nav-item" onclick="switchTab('addresses',this)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Addresses
        </button>
        <div class="account-nav-sep"></div>
        <button class="account-nav-item" onclick="switchTab('settings',this)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82"/></svg>
          Settings
        </button>
        <a href="#" onclick="handleSignOut(event)" class="account-nav-item" style="color:var(--color-error)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Sign Out
        </a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <div>

      <!-- DASHBOARD TAB -->
      <div id="tab-dashboard" class="tab-content active">
        <div class="account-panel">
          <h2 class="account-panel-title" id="dashGreeting"><svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 11V5a2 2 0 1 1 4 0v5m0-4a2 2 0 1 1 4 0v4m0-2a2 2 0 1 1 4 0v5c0 5-3 8-8 8h-1c-3 0-5-1-7-4l-2-3a2 2 0 0 1 3-2l3 2V11a2 2 0 1 1 4 0Z"/></svg> Welcome back!</h2>
          <div class="stats-row">
            <div class="stat-box"><div class="stat-box-val" id="statTotalOrders">—</div><div class="stat-box-label">Total Orders</div></div>
            <div class="stat-box"><div class="stat-box-val" id="statTotalSpent">—</div><div class="stat-box-label">Total Spent</div></div>
          </div>

          <div class="account-section">
            <p class="account-section-title">Recent Orders</p>
            <div class="orders-table-wrap">
              <table class="orders-table">
                <thead><tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="dashRecentOrders">
                  <tr><td colspan="6" style="text-align:center;color:var(--color-muted);padding:var(--space-6)">Loading…</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- ORDERS TAB -->
      <div id="tab-orders" class="tab-content">
        <div class="account-panel">
          <h2 class="account-panel-title">My Orders</h2>
          <div style="display:flex;gap:var(--space-2);margin-bottom:var(--space-5);flex-wrap:wrap" id="orderStatusFilters">
            <button class="size-btn active" data-status="all">All</button>
            <button class="size-btn" data-status="processing">Processing</button>
            <button class="size-btn" data-status="delivered">Delivered</button>
            <button class="size-btn" data-status="cancelled">Cancelled</button>
          </div>
          <div class="orders-table-wrap">
            <table class="orders-table">
              <thead><tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody id="fullOrdersList">
                <tr><td colspan="6" style="text-align:center;color:var(--color-muted);padding:var(--space-6)">Loading…</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- WISHLIST TAB -->
      <div id="tab-wishlist" class="tab-content">
        <div class="account-panel">
          <h2 class="account-panel-title">My Wishlist <span style="font-size:var(--text-base);color:var(--color-muted);font-weight:400" id="wishlistTabCount">(0 items)</span></h2>
          <div class="wishlist-grid" id="wishlistGrid">
            <p style="color:var(--color-muted);font-size:var(--text-sm)">Loading…</p>
          </div>
        </div>
      </div>

      <!-- ADDRESSES TAB -->
      <div id="tab-addresses" class="tab-content">
        <div class="account-panel">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-6)">
            <h2 class="account-panel-title" style="margin-bottom:0">Saved Addresses</h2>
            <button class="btn btn-primary btn-sm" onclick="openAddressModal()"><svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg> Add New</button>
          </div>
          <div id="addressesEmpty" style="text-align:center;padding:var(--space-12) var(--space-4);color:var(--color-muted);display:none">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--color-border)" stroke-width="1.5" style="margin:0 auto var(--space-4)">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
            <p style="font-weight:700;color:var(--color-ink);margin-bottom:var(--space-2)">No saved addresses yet</p>
            <p style="font-size:var(--text-sm)">Add an address to speed up your next checkout.</p>
          </div>
          <div class="address-grid" id="addressGrid"></div>
        </div>
      </div>

      <!-- SETTINGS TAB -->
      <div id="tab-settings" class="tab-content">
        <div class="account-panel">
          <h2 class="account-panel-title">Account Settings</h2>

          <div class="account-section">
            <p class="account-section-title">Personal Information</p>
            <form id="profileForm" onsubmit="updateProfile(event)">
              <div class="form-row"><div class="form-group"><label class="form-label" for="sFirst">First Name</label><input type="text" id="sFirst" class="form-input" required></div><div class="form-group"><label class="form-label" for="sLast">Last Name</label><input type="text" id="sLast" class="form-input" required></div></div>
              <div class="form-row"><div class="form-group"><label class="form-label" for="sEmail">Email</label><input type="email" id="sEmail" class="form-input" disabled></div><div class="form-group"><label class="form-label" for="sPhone">Phone</label><input type="tel" id="sPhone" class="form-input"></div></div>
              <button type="submit" class="btn btn-primary" id="btnUpdateProfile">Save Changes</button>
            </form>
          </div>

          <div class="account-section">
            <p class="account-section-title">Change Password</p>
            <form id="passwordForm" onsubmit="changePassword(event)">
              <div class="form-row">
                <div class="form-group"><label class="form-label" for="sCurrentPassword">Current Password</label><input type="password" id="sCurrentPassword" class="form-input" required></div>
                <div class="form-group"><label class="form-label" for="sNewPassword">New Password</label><input type="password" id="sNewPassword" class="form-input" required minlength="8"></div>
              </div>
              <button type="submit" class="btn btn-outline" id="btnChangePassword">Update Password</button>
            </form>
          </div>

          <div class="account-section">
            <p class="account-section-title">Email Preferences</p>
            <div class="pref-row"><div><p style="font-weight:600;font-size:var(--text-sm)">Newsletter & Promotions</p><p style="font-size:var(--text-xs);color:var(--color-muted)">Exclusive deals and new arrivals</p></div><label class="toggle-switch"><input type="checkbox" id="prefNewsletter" onchange="updatePreferences()"><span class="toggle-slider"></span></label></div>
            <div class="pref-row"><div><p style="font-weight:600;font-size:var(--text-sm)">Order Notifications</p><p style="font-size:var(--text-xs);color:var(--color-muted)">Shipping and delivery updates</p></div><label class="toggle-switch"><input type="checkbox" id="prefOrderNotif" onchange="updatePreferences()"><span class="toggle-slider"></span></label></div>
            <div class="pref-row"><div><p style="font-weight:600;font-size:var(--text-sm)">Back in Stock Alerts</p><p style="font-size:var(--text-xs);color:var(--color-muted)">When wishlist items are restocked</p></div><label class="toggle-switch"><input type="checkbox" id="prefRestock" onchange="updatePreferences()"><span class="toggle-slider"></span></label></div>
          </div>

          <div class="account-section" style="margin-bottom:0">
            <p class="account-section-title" style="color:var(--color-error)">Danger Zone</p>
            <p style="font-size:var(--text-sm);color:var(--color-muted);margin-bottom:var(--space-4)">Permanently delete your account and all your data. This action cannot be undone.</p>
            <button class="btn btn-sm btn-danger" style="border-radius:var(--radius-full);padding:var(--space-2) var(--space-5);font-weight:700" onclick="deactivateAccount()">Delete My Account</button>
          </div>
        </div>
      </div>

    </div><!-- /main content -->
  </div><!-- /account layout -->
</div>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<div class="account-modal-overlay" id="addressModal">
  <div class="modal" style="max-width:620px;background:var(--color-bg);border-radius:var(--radius-xl);padding:var(--space-6);box-shadow:var(--shadow-xl)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-5)">
      <h2 style="font-size:var(--text-xl);margin:0" id="addressModalTitle">Add Address</h2>
      <button class="modal-close" type="button" onclick="closeAddressModal()" aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
    </div>
    <form id="addressForm" onsubmit="saveAddress(event)">
      <input type="hidden" id="addressId">
      <div class="form-row"><div class="form-group"><label class="form-label" for="addressName">Full Name *</label><input class="form-input" id="addressName" required></div><div class="form-group"><label class="form-label" for="addressPhone">Phone *</label><input class="form-input" id="addressPhone" required></div></div>
      <div class="form-row"><div class="form-group"><label class="form-label" for="addressCountry">Country *</label><select class="form-input" id="addressCountry" required><option value="PK">Pakistan</option><option value="US">United States</option><option value="GB">United Kingdom</option><option value="AE">United Arab Emirates</option></select></div><div class="form-group"><label class="form-label" for="addressState">State / Province *</label><input class="form-input" id="addressState" required></div></div>
      <div class="form-row"><div class="form-group"><label class="form-label" for="addressCity">City *</label><input class="form-input" id="addressCity" required></div><div class="form-group"><label class="form-label" for="addressPostal">Postal Code *</label><input class="form-input" id="addressPostal" required></div></div>
      <div class="form-group"><label class="form-label" for="addressStreet">Street Address *</label><input class="form-input" id="addressStreet" required></div>
      <div class="form-group"><label class="form-label" for="addressApartment">Apartment / Suite</label><input class="form-input" id="addressApartment"></div>
      <label class="form-check"><input type="checkbox" id="addressDefault"><span>Make this my default address</span></label>
      <div style="display:flex;justify-content:flex-end;gap:var(--space-3);margin-top:var(--space-6)"><button type="button" class="btn btn-outline" onclick="closeAddressModal()">Cancel</button><button type="submit" class="btn btn-primary">Save Address</button></div>
    </form>
  </div>
</div>
<script src="/js/utils.js"></script>
<script src="/js/api-client.js"></script>
<script src="/js/cart.js"></script>
<script src="/js/auth.js"></script>
<script src="/js/search.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════
   ACCOUNT — Wired to the real backend
   (GET /api/auth/me, GET /api/orders, GET /api/wishlist)
   ══════════════════════════════════════════ */

// This page requires a logged-in user for everything it shows.
if (!API.auth.isLoggedIn()) {
  window.location.href = `/login?redirect=${encodeURIComponent('/account')}`;
}

window.addEventListener('scroll', () => document.getElementById('navbar')?.classList.toggle('scrolled', scrollY > 20), { passive: true });

function switchTab(tab, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
  document.querySelectorAll('.account-nav-item').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
}

function handleSignOut(e) {
  e.preventDefault();
  API.auth.logout();
  showToast('Signed out', 'info');
  setTimeout(() => window.location.href = "/login", 500);
}

const ORDER_STATUS_LABELS = {
  pending: 'Processing', processing: 'Processing', shipped: 'Shipped',
  delivered: 'Delivered', cancelled: 'Cancelled', refunded: 'Refunded',
};
function statusClass(status) {
  if (status === 'delivered') return 'delivered';
  if (status === 'cancelled' || status === 'refunded') return 'cancelled';
  return 'processing';
}
function fmtDate(iso) {
  return new Date(iso).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

let allOrders = [];

/* ── Load user profile into sidebar + settings form ── */
async function loadProfile() {
  try {
    const res = await API.auth.me();
    const u = res.data;
    document.getElementById('acctAvatar').textContent = (u.first_name || '?').charAt(0).toUpperCase();
    document.getElementById('acctName').textContent = `${u.first_name} ${u.last_name}`;
    document.getElementById('acctEmail').textContent = u.email;
    document.getElementById('dashGreeting').innerHTML = `<svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 11V5a2 2 0 1 1 4 0v5m0-4a2 2 0 1 1 4 0v4m0-2a2 2 0 1 1 4 0v5c0 5-3 8-8 8h-1c-3 0-5-1-7-4l-2-3a2 2 0 0 1 3-2l3 2V11a2 2 0 1 1 4 0Z"/></svg> Welcome back, ${escapeHtml(u.first_name)}!`;

    const sFirst = document.getElementById('sFirst');
    const sLast  = document.getElementById('sLast');
    const sEmail = document.getElementById('sEmail');
    const sPhone = document.getElementById('sPhone');
    if (sFirst) sFirst.value = u.first_name || '';
    if (sLast)  sLast.value  = u.last_name || '';
    if (sEmail) sEmail.value = u.email || '';
    if (sPhone) sPhone.value = u.phone || '';

    const prefNews = document.getElementById('prefNewsletter');
    const prefOrder = document.getElementById('prefOrderNotif');
    const prefRestock = document.getElementById('prefRestock');
    if (prefNews) prefNews.checked = !!u.prefs_newsletter;
    if (prefOrder) prefOrder.checked = u.prefs_order_notifications !== false; // default true if undefined
    if (prefRestock) prefRestock.checked = !!u.prefs_restock_alerts;
  } catch (err) {
    // Token expired/invalid — bounce to login
    API.auth.logout();
    window.location.href = `/login?redirect=${encodeURIComponent('/account')}`;
  }
}

/* ── Load orders (used by both the dashboard summary and the full Orders tab) ── */
async function loadOrders() {
  try {
    const res = await API.orders.list();
    allOrders = res.data || [];
    renderDashboard();
    renderOrdersTab('all');
  } catch (err) {
    document.getElementById('dashRecentOrders').innerHTML =
      `<tr><td colspan="6" style="text-align:center;color:var(--color-error);padding:var(--space-6)">Could not load orders.</td></tr>`;
    document.getElementById('fullOrdersList').innerHTML =
      `<tr><td colspan="6" style="text-align:center;color:var(--color-error);padding:var(--space-6)">Could not load orders.</td></tr>`;
  }
}

function renderDashboard() {
  const totalOrders = allOrders.length;
  const totalSpent = allOrders.reduce((sum, o) => sum + parseFloat(o.total_amount || 0), 0);
  document.getElementById('statTotalOrders').textContent = totalOrders;
  document.getElementById('statTotalSpent').textContent = formatPrice(totalSpent);

  const recent = allOrders.slice(0, 3);
  const tbody = document.getElementById('dashRecentOrders');
  tbody.innerHTML = recent.length
    ? recent.map(orderRow).join('')
    : `<tr><td colspan="6" style="text-align:center;color:var(--color-muted);padding:var(--space-6)">No orders yet. <a href="/shop" style="color:var(--color-primary);font-weight:600">Start shopping →</a></td></tr>`;
}

function orderRow(o) {
  let label = ORDER_STATUS_LABELS[o.order_status] || o.order_status;
  if (o.payment_method === 'easypaisa' && o.payment_status === 'pending') {
      label = 'Pending Verification';
  }
  return `<tr>
    <td><strong>${escapeHtml(o.order_number)}</strong></td>
    <td>${fmtDate(o.created_at)}</td>
    <td>${o.item_count}</td>
    <td><strong>${formatPrice(o.total_amount)}</strong></td>
    <td><span class="order-status ${statusClass(o.order_status)}">${label}</span></td>
    <td><a href="/order-tracking?order=${encodeURIComponent(o.order_number)}" class="btn-ghost btn-sm" style="font-size:.75rem">View</a></td>
  </tr>`;
}

function renderOrdersTab(filter) {
  const rows = filter === 'all' ? allOrders : allOrders.filter(o => statusClass(o.order_status) === filter || o.order_status === filter);
  const tbody = document.getElementById('fullOrdersList');
  tbody.innerHTML = rows.length
    ? rows.map(orderRow).join('')
    : `<tr><td colspan="6" style="text-align:center;color:var(--color-muted);padding:var(--space-6)">No orders in this category.</td></tr>`;
}

let savedAddresses = [];
function openAddressModal(address = null) {
  document.getElementById('addressModalTitle').textContent = address ? 'Edit Address' : 'Add Address';
  document.getElementById('addressId').value = address?.address_id || '';
  document.getElementById('addressName').value = address?.full_name || '';
  document.getElementById('addressPhone').value = address?.phone || '';
  document.getElementById('addressCountry').value = address?.country || 'PK';
  document.getElementById('addressState').value = address?.state || '';
  document.getElementById('addressCity').value = address?.city || '';
  document.getElementById('addressPostal').value = address?.postal_code || '';
  document.getElementById('addressStreet').value = address?.street_address || '';
  document.getElementById('addressApartment').value = address?.apartment_suite || '';
  document.getElementById('addressDefault').checked = !!address?.is_default;
  document.getElementById('addressModal').classList.add('open');
}
function closeAddressModal() { document.getElementById('addressModal').classList.remove('open'); }
function addressText(address) { return [address.street_address, address.apartment_suite, [address.city, address.state, address.postal_code].filter(Boolean).join(', '), address.country].filter(Boolean).join('<br>'); }
function renderAddresses() {
  const grid = document.getElementById('addressGrid');
  const empty = document.getElementById('addressesEmpty');
  empty.style.display = savedAddresses.length ? 'none' : 'block';
  grid.innerHTML = savedAddresses.map(address => `<article class="address-card ${address.is_default ? 'default' : ''}"><div style="display:flex;justify-content:space-between;gap:var(--space-3)"><div><strong>${escapeHtml(address.full_name)}</strong>${address.is_default ? '<span class="badge badge-new" style="margin-left:.5rem">Default</span>' : ''}</div><svg class="ui-icon" style="color:var(--color-primary)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div><p style="font-size:var(--text-sm);margin-top:var(--space-3)">${escapeHtml(address.phone)}</p><p style="font-size:var(--text-sm);color:var(--color-muted);line-height:1.6">${addressText(address)}</p><div class="address-actions"><button class="btn btn-outline btn-sm" onclick="editAddress(${address.address_id})">Edit</button><button class="btn btn-danger btn-sm" onclick="deleteAddress(${address.address_id})">Delete</button></div></article>`).join('');
}
function editAddress(id) { const address = savedAddresses.find(item => Number(item.address_id) === Number(id)); if (address) openAddressModal(address); }
async function loadAddresses() { try { const res = await API.addresses.list(); savedAddresses = res.data || []; renderAddresses(); } catch (err) { showToast(err.message || 'Could not load addresses.', 'error'); } }
async function saveAddress(event) {
  event.preventDefault();
  const value = id => document.getElementById(id).value.trim();
  const data = { full_name: value('addressName'), phone: value('addressPhone'), country: document.getElementById('addressCountry').value, state: value('addressState'), city: value('addressCity'), postal_code: value('addressPostal'), street_address: value('addressStreet'), apartment_suite: value('addressApartment'), is_default: document.getElementById('addressDefault').checked };
  try { const id = document.getElementById('addressId').value; if (id) await API.addresses.update(id, data); else await API.addresses.create(data); closeAddressModal(); showToast('Address saved.', 'success'); await loadAddresses(); } catch (err) { showToast(err.message || 'Could not save address.', 'error'); }
}
async function deleteAddress(id) { if (!confirm('Delete this saved address?')) return; try { await API.addresses.remove(id); showToast('Address deleted.', 'success'); await loadAddresses(); } catch (err) { showToast(err.message || 'Could not delete address.', 'error'); } }

document.getElementById('orderStatusFilters')?.addEventListener('click', (e) => {
  const btn = e.target.closest('.size-btn');
  if (!btn) return;
  document.querySelectorAll('#orderStatusFilters .size-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderOrdersTab(btn.dataset.status);
});

/* ── Load wishlist ── */
async function loadWishlist() {
  const grid = document.getElementById('wishlistGrid');
  try {
    const res = await API.wishlist.list();
    const items = res.data || [];

    document.getElementById('acctWishlistCount').textContent = items.length;
    document.getElementById('wishlistTabCount').textContent = `(${items.length} item${items.length !== 1 ? 's' : ''})`;

    if (!items.length) {
      grid.innerHTML = `<p style="color:var(--color-muted);font-size:var(--text-sm)">Your wishlist is empty. <a href="/shop" style="color:var(--color-primary);font-weight:600">Browse products →</a></p>`;
      return;
    }

    grid.innerHTML = items.map(item => {
      const name  = escapeHtml(item.product_name || '');
      const image = item.image ? escapeHtml(item.image) : '';
      // Safe to embed inside a single-quoted onclick(...) JS string argument —
      // same escaping approach as buildProductCard() in utils.js.
      const nameForJs = (item.product_name || '')
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'")
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
      return `
      <div class="product-card" data-wishlist-id="${item.wishlist_id}" data-product-id="${item.product_id}">
        <div class="product-card-img">
          ${image
            ? `<img src="${image}" alt="${name}" style="width:100%;aspect-ratio:3/4;object-fit:cover">`
            : `<div class="img-placeholder" style="width:100%;aspect-ratio:3/4;background:linear-gradient(135deg,#FFF0EB,#FFE4F0);display:flex;align-items:center;justify-content:center;"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFB89A" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>`}
          <button class="btn-wishlist active" aria-label="Remove from wishlist" onclick="removeWishlistItem(event, ${item.wishlist_id})">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </button>
        </div>
        <div class="product-card-body">
          <a href="/product?id=${item.product_id}" class="product-name">${name}</a>
          <div class="product-price" style="margin-top:var(--space-2)"><span class="product-price-current">${formatPrice(item.final_price)}</span></div>
          <button onclick="moveWishlistItemToCart(event, ${item.product_id}, '${nameForJs}', ${parseFloat(item.final_price)})" class="btn btn-primary btn-sm" style="width:100%;margin-top:var(--space-3)">Move to Cart</button>
        </div>
      </div>`;
    }).join('');
  } catch (err) {
    grid.innerHTML = `<p style="color:var(--color-error);font-size:var(--text-sm)">Could not load your wishlist.</p>`;
  }
}

async function removeWishlistItem(e, wishlistId) {
  e.preventDefault();
  try {
    await API.wishlist.remove(wishlistId);
    showToast('Removed from wishlist', 'info');
    await loadWishlist();
  } catch (err) {
    showToast(err.message || 'Could not remove item.', 'error');
  }
}

async function moveWishlistItemToCart(e, productId, name, price) {
  e.preventDefault();
  try {
    await Cart.add({ product_id: productId, product_name: name, final_price: price });
    // "Move" = add to cart, then remove from wishlist
    const card = e.target.closest('[data-wishlist-id]');
    const wishlistId = card?.dataset.wishlistId;
    if (wishlistId) await API.wishlist.remove(parseInt(wishlistId, 10));
    await loadWishlist();
  } catch (err) {
    showToast(err.message || 'Could not move item to cart.', 'error');
  }
}

/* ── Account Settings Logic ── */
async function updateProfile(e) {
  e.preventDefault();
  const btn = document.getElementById('btnUpdateProfile');
  btn.textContent = 'Saving...'; btn.disabled = true;
  try {
    const first = document.getElementById('sFirst').value;
    const last = document.getElementById('sLast').value;
    const phone = document.getElementById('sPhone').value;
    await API.auth.updateProfile(first, last, phone);
    
    // Update local user context
    const u = JSON.parse(localStorage.getItem('pp_user'));
    u.first_name = first; u.last_name = last;
    localStorage.setItem('pp_user', JSON.stringify(u));
    document.getElementById('acctName').textContent = `${first} ${last}`;
    document.getElementById('dashGreeting').innerHTML = `<svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 11V5a2 2 0 1 1 4 0v5m0-4a2 2 0 1 1 4 0v4m0-2a2 2 0 1 1 4 0v5c0 5-3 8-8 8h-1c-3 0-5-1-7-4l-2-3a2 2 0 0 1 3-2l3 2V11a2 2 0 1 1 4 0Z"/></svg> Welcome back, ${escapeHtml(first)}!`;
    
    showToast('Profile updated successfully.', 'success');
  } catch (err) {
    showToast(err.message || 'Could not update profile.', 'error');
  } finally {
    btn.textContent = 'Save Changes'; btn.disabled = false;
  }
}

async function changePassword(e) {
  e.preventDefault();
  const btn = document.getElementById('btnChangePassword');
  btn.textContent = 'Updating...'; btn.disabled = true;
  try {
    const curr = document.getElementById('sCurrentPassword').value;
    const next = document.getElementById('sNewPassword').value;
    await API.auth.changePassword(curr, next);
    showToast('Password updated successfully.', 'success');
    e.target.reset();
  } catch (err) {
    showToast(err.message || 'Could not update password.', 'error');
  } finally {
    btn.textContent = 'Update Password'; btn.disabled = false;
  }
}

async function updatePreferences() {
  try {
    const news = document.getElementById('prefNewsletter').checked;
    const order = document.getElementById('prefOrderNotif').checked;
    const restock = document.getElementById('prefRestock').checked;
    await API.auth.updatePreferences(news, order, restock);
    showToast('Preferences updated.', 'success');
  } catch (err) {
    showToast(err.message || 'Could not update preferences.', 'error');
  }
}

async function deactivateAccount() {
  if (!confirm('Are you sure you want to delete your account? This cannot be undone.')) return;
  try {
    await API.auth.deleteAccount();
    API.auth.logout();
    window.location.href = '/';
  } catch (err) {
    showToast(err.message || 'Could not delete account.', 'error');
  }
}

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
  loadProfile();
  loadOrders();
  loadWishlist();
  loadAddresses();

  // Deep-link support: /account#orders, #wishlist, etc.
  const hash = location.hash.replace('#', '');
  if (['dashboard', 'orders', 'wishlist', 'addresses', 'settings'].includes(hash)) {
    const btn = document.querySelector(`.account-nav-item[onclick*="'${hash}'"]`);
    switchTab(hash, btn);
  }
});
</script>
<script src="/js/site-footer.js"></script>
</body>
</html>
