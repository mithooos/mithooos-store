<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>

<!-- Navbar -->
<?php include __DIR__ . '/../includes/frontend/navbar.php'; ?>
<?php include __DIR__ . '/../includes/frontend/mobile-nav.php'; ?>

<style>
/* ── ORDER TRACKING LUXURY STYLES ── */
.tracking-page-wrap {
  min-height: 85vh;
  padding: var(--space-8) 0 var(--space-20);
  background: linear-gradient(180deg, #FDFBF7 0%, #F5EFE6 100%);
}

.tracking-card-container {
  max-width: 880px;
  margin: 0 auto;
  padding: 0 var(--space-4);
}

/* Breadcrumb */
.trk-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: var(--text-xs);
  color: var(--color-muted);
  margin-bottom: var(--space-6);
  font-weight: 500;
}
.trk-breadcrumb a {
  color: var(--color-muted);
  text-decoration: none;
  transition: color 0.2s;
}
.trk-breadcrumb a:hover {
  color: var(--color-primary);
}
.trk-breadcrumb-sep {
  color: var(--color-border);
}

/* ── SEARCH CARD ── */
.trk-search-card {
  background: white;
  border-radius: var(--radius-2xl);
  padding: var(--space-8) var(--space-6);
  border: 1px solid var(--color-border-soft);
  box-shadow: 0 10px 30px rgba(24, 35, 51, 0.04);
  text-align: center;
  position: relative;
  overflow: hidden;
  margin-bottom: var(--space-8);
}
.trk-search-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-accent) 100%);
}
.trk-icon-badge {
  width: 58px;
  height: 58px;
  background: var(--color-surface);
  border: 1px solid var(--color-border-soft);
  border-radius: var(--radius-full);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: var(--space-4);
  color: var(--color-primary);
  box-shadow: 0 4px 14px rgba(122, 31, 43, 0.08);
}
.trk-search-title {
  font-family: var(--font-heading);
  font-size: var(--text-3xl);
  font-weight: 800;
  color: var(--color-ink);
  letter-spacing: -0.5px;
  margin-bottom: var(--space-2);
}
.trk-search-desc {
  color: var(--color-muted);
  font-size: var(--text-sm);
  max-width: 480px;
  margin: 0 auto var(--space-6);
  line-height: 1.5;
}

/* Modern Pill Search Input */
.trk-input-pill {
  display: flex;
  align-items: center;
  max-width: 540px;
  margin: 0 auto;
  background: var(--color-surface);
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-full);
  padding: 4px 5px 4px 18px;
  transition: all 0.25s ease;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
}
.trk-input-pill:focus-within {
  border-color: var(--color-primary);
  background: white;
  box-shadow: 0 0 0 4px rgba(122, 31, 43, 0.1), inset 0 1px 3px rgba(0, 0, 0, 0.02);
}
.trk-input-field {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  font-family: var(--font-heading);
  font-size: 15px;
  font-weight: 700;
  color: var(--color-ink);
  letter-spacing: 0.5px;
  text-transform: uppercase;
}
.trk-input-field::placeholder {
  text-transform: none;
  font-family: var(--font-body);
  font-weight: 400;
  color: var(--color-muted);
  font-size: var(--text-sm);
  letter-spacing: normal;
}
.trk-submit-btn {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: var(--radius-full);
  padding: 10px 24px;
  font-size: var(--text-sm);
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
  height: 44px;
}
.trk-submit-btn:hover {
  background: var(--color-primary-dark);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(122, 31, 43, 0.28);
}
.trk-submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* ── TRACKING RESULT CARD ── */
.tracking-result-panel {
  display: none;
  animation: trkFadeUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.tracking-result-panel.show {
  display: block;
}
@keyframes trkFadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.trk-main-card {
  background: white;
  border-radius: var(--radius-2xl);
  border: 1px solid var(--color-border-soft);
  box-shadow: 0 12px 36px rgba(24, 35, 51, 0.06);
  overflow: hidden;
  margin-bottom: var(--space-8);
}

/* Hero Header Bar */
.trk-header-bar {
  background: linear-gradient(135deg, #7A1F2B 0%, #52121B 100%);
  color: white;
  padding: var(--space-6) var(--space-8);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: var(--space-4);
}
.trk-order-subtext {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 4px;
}
.trk-order-id-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
}
.trk-order-num {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: 800;
  letter-spacing: 0.5px;
  color: white;
}
.trk-copy-chip {
  background: rgba(255, 255, 255, 0.16);
  border: 1px solid rgba(255, 255, 255, 0.25);
  color: white;
  padding: 4px 10px;
  border-radius: var(--radius-full);
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s;
}
.trk-copy-chip:hover {
  background: rgba(255, 255, 255, 0.28);
}

/* Status Pill */
.trk-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 18px;
  border-radius: var(--radius-full);
  font-size: var(--text-sm);
  font-weight: 800;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}
.trk-status-pill.confirmed { background: #D1FAE5; color: #065F46; }
.trk-status-pill.processing { background: #DBEAFE; color: #1E40AF; }
.trk-status-pill.shipped { background: #EDE9FE; color: #5B21B6; }
.trk-status-pill.delivered { background: #DCFCE7; color: #15803D; }
.trk-status-pill.pending { background: #FEF3C7; color: #92400E; }
.trk-status-pill.cancelled { background: #FEE2E2; color: #991B1B; }

/* ── 4-TILE METRICS ROW ── */
.trk-metrics-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border-soft);
}
@media (max-width: 768px) {
  .trk-metrics-grid { grid-template-columns: repeat(2, 1fr); }
}
.trk-metric-box {
  padding: var(--space-5) var(--space-6);
  border-right: 1px solid var(--color-border-soft);
}
.trk-metric-box:last-child {
  border-right: none;
}
@media (max-width: 768px) {
  .trk-metric-box:nth-child(2) { border-right: none; }
  .trk-metric-box:nth-child(1), .trk-metric-box:nth-child(2) { border-bottom: 1px solid var(--color-border-soft); }
}
.trk-metric-label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: var(--color-muted);
  margin-bottom: 4px;
}
.trk-metric-val {
  font-family: var(--font-heading);
  font-size: 15px;
  font-weight: 800;
  color: var(--color-ink);
}

/* ── VISUAL STEPPER ── */
.trk-stepper-section {
  padding: var(--space-8);
  border-bottom: 1px solid var(--color-border-soft);
}
.trk-section-title {
  font-family: var(--font-heading);
  font-size: var(--text-base);
  font-weight: 800;
  color: var(--color-ink);
  text-transform: uppercase;
  letter-spacing: 0.8px;
  margin-bottom: var(--space-6);
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Stepper Track */
.trk-stepper-track {
  display: flex;
  justify-content: space-between;
  position: relative;
  margin: var(--space-4) 0 var(--space-2);
}
.trk-stepper-track::before {
  content: '';
  position: absolute;
  top: 20px;
  left: 36px;
  right: 36px;
  height: 4px;
  background: var(--color-border-soft);
  z-index: 1;
  border-radius: 2px;
}
.trk-track-progress {
  position: absolute;
  top: 20px;
  left: 36px;
  height: 4px;
  background: linear-gradient(90deg, #10B981, #7A1F2B);
  z-index: 1;
  border-radius: 2px;
  transition: width 0.4s ease;
}

.trk-milestone {
  position: relative;
  z-index: 2;
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}
.trk-node-circle {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: white;
  border: 3px solid var(--color-border);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-muted);
  transition: all 0.3s ease;
  margin-bottom: var(--space-3);
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.trk-milestone.done .trk-node-circle {
  background: #10B981;
  border-color: #10B981;
  color: white;
}
.trk-milestone.active .trk-node-circle {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: white;
  box-shadow: 0 0 0 5px rgba(122, 31, 43, 0.18);
  animation: trkPulseRing 2s infinite;
}
@keyframes trkPulseRing {
  0% { box-shadow: 0 0 0 0 rgba(122, 31, 43, 0.4); }
  70% { box-shadow: 0 0 0 8px rgba(122, 31, 43, 0); }
  100% { box-shadow: 0 0 0 0 rgba(122, 31, 43, 0); }
}

.trk-node-title {
  font-family: var(--font-heading);
  font-size: 13px;
  font-weight: 700;
  color: var(--color-ink);
  margin-bottom: 2px;
}
.trk-milestone.done .trk-node-title { color: #065F46; }
.trk-milestone.active .trk-node-title { color: var(--color-primary); }
.trk-milestone.pending .trk-node-title { color: var(--color-muted); }

.trk-node-desc {
  font-size: 11px;
  color: var(--color-muted);
  max-width: 140px;
  line-height: 1.35;
}
.trk-node-time {
  font-size: 11px;
  font-weight: 700;
  color: var(--color-primary);
  margin-top: 4px;
}

@media (max-width: 640px) {
  .trk-stepper-track {
    flex-direction: column;
    gap: var(--space-5);
  }
  .trk-stepper-track::before {
    top: 20px;
    bottom: 20px;
    left: 20px;
    right: auto;
    width: 4px;
    height: auto;
  }
  .trk-track-progress {
    top: 20px;
    left: 20px;
    width: 4px;
    height: 0;
  }
  .trk-milestone {
    flex-direction: row;
    align-items: flex-start;
    text-align: left;
    gap: var(--space-4);
  }
  .trk-node-circle {
    margin-bottom: 0;
    flex-shrink: 0;
  }
  .trk-node-desc {
    max-width: none;
  }
}

/* ── DETAILS 2-COLUMN GRID ── */
.trk-columns-grid {
  display: grid;
  grid-template-columns: 1.25fr 0.75fr;
}
@media (max-width: 820px) {
  .trk-columns-grid { grid-template-columns: 1fr; }
}
.trk-items-col {
  padding: var(--space-8);
  border-right: 1px solid var(--color-border-soft);
}
@media (max-width: 820px) {
  .trk-items-col { border-right: none; border-bottom: 1px solid var(--color-border-soft); }
}
.trk-meta-col {
  padding: var(--space-8);
  background: #FCFBF9;
}

/* Items List */
.trk-item-card {
  display: flex;
  align-items: center;
  gap: var(--space-4);
  padding: var(--space-3) 0;
  border-bottom: 1px solid var(--color-border-soft);
}
.trk-item-card:last-child {
  border-bottom: none;
}
.trk-item-pic {
  width: 58px;
  height: 58px;
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--color-surface);
  border: 1px solid var(--color-border-soft);
  flex-shrink: 0;
}
.trk-item-pic img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.trk-item-details {
  flex: 1;
  min-width: 0;
}
.trk-item-name {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-ink);
  line-height: 1.3;
  margin-bottom: 2px;
}
.trk-item-meta {
  font-size: 12px;
  color: var(--color-muted);
  display: flex;
  align-items: center;
  gap: 6px;
}
.trk-item-cost {
  font-size: 14px;
  font-weight: 800;
  color: var(--color-ink);
  text-align: right;
  white-space: nowrap;
}

/* Info Cards in Right Column */
.trk-info-group {
  margin-bottom: var(--space-5);
}
.trk-info-lbl {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: var(--color-muted);
  margin-bottom: var(--space-2);
  display: flex;
  align-items: center;
  gap: 5px;
}
.trk-info-content-card {
  background: white;
  border-radius: var(--radius-lg);
  padding: var(--space-4);
  border: 1px solid var(--color-border-soft);
  font-size: var(--text-sm);
  color: var(--color-ink);
  line-height: 1.5;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

/* ── NOT FOUND CARD ── */
.tracking-not-found-panel {
  display: none;
  background: white;
  border-radius: var(--radius-2xl);
  padding: var(--space-12) var(--space-6);
  border: 1px solid var(--color-border-soft);
  box-shadow: 0 8px 30px rgba(24, 35, 51, 0.05);
  text-align: center;
}
.tracking-not-found-panel.show {
  display: block;
}
</style>

<main class="tracking-page-wrap">
  <div class="tracking-card-container">

    <!-- Breadcrumb -->
    <nav class="trk-breadcrumb" aria-label="Breadcrumb">
      <a href="/">Home</a><span class="trk-breadcrumb-sep">/</span>
      <a href="/account">My Account</a><span class="trk-breadcrumb-sep">/</span>
      <span style="color:var(--color-ink);font-weight:600">Track Order</span>
    </nav>

    <!-- Search Panel -->
    <div class="trk-search-card">
      <div class="trk-icon-badge">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
      </div>
      <h1 class="trk-search-title">Track Your Order</h1>
      <p class="trk-search-desc">Enter your order number to view real-time delivery status, carrier info, and shipment progress.</p>
      
      <div class="trk-input-pill">
        <input type="text" id="orderInput" class="trk-input-field" placeholder="e.g. ORD-20261012-11CAB5" autocomplete="off" aria-label="Order number">
        <button class="trk-submit-btn" onclick="lookupOrder()" id="trackBtn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          Track Order
        </button>
      </div>
    </div>

    <!-- Result Card -->
    <div class="tracking-result-panel" id="trackingResult">
      <div class="trk-main-card">
        
        <!-- Header Bar -->
        <div class="trk-header-bar" id="resultHeader"></div>

        <!-- 4-Tile Metrics Grid -->
        <div class="trk-metrics-grid" id="metricsGrid"></div>

        <!-- Stepper / Progress Timeline -->
        <div class="trk-stepper-section">
          <div class="trk-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2.5">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Shipment Progress
          </div>
          <div class="trk-stepper-track" id="trackingStepper"></div>
        </div>

        <!-- 2 Columns: Items & Shipping -->
        <div class="trk-columns-grid">
          
          <!-- Left: Items -->
          <div class="trk-items-col">
            <div class="trk-section-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2.5">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
              </svg>
              Items in This Order
            </div>
            <div id="trackingItemsList"></div>
          </div>

          <!-- Right: Shipping & Payment Details -->
          <div class="trk-meta-col">
            <div class="trk-section-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2.5">
                <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
              </svg>
              Delivery & Payment
            </div>

            <div class="trk-info-group">
              <div class="trk-info-lbl">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Shipping Address
              </div>
              <div class="trk-info-content-card" id="trkShippingAddress">—</div>
            </div>

            <div class="trk-info-group">
              <div class="trk-info-lbl">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Payment Details
              </div>
              <div class="trk-info-content-card" id="trkPaymentInfo">—</div>
            </div>

            <div style="margin-top:var(--space-6);display:flex;gap:var(--space-3);flex-wrap:wrap">
              <a href="/shop" class="btn btn-outline btn-sm" style="flex:1;justify-content:center">Continue Shopping</a>
              <button onclick="window.print()" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;border:1px solid var(--color-border)">Print Receipt</button>
            </div>
          </div>

        </div>

      </div>
    </div>

    <!-- Not found card -->
    <div class="tracking-not-found-panel" id="trackingNotFound">
      <div style="width:64px;height:64px;background:var(--color-surface);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-4);color:var(--color-muted)">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </div>
      <h3 style="font-family:var(--font-heading);font-size:var(--text-xl);font-weight:700;color:var(--color-ink);margin-bottom:var(--space-2)">Order Not Found</h3>
      <p style="color:var(--color-muted);font-size:var(--text-sm);max-width:380px;margin:0 auto var(--space-6);line-height:1.5">
        We couldn't locate an order with that number. Please ensure the order number is typed correctly (e.g. <code>ORD-20261012-XXXXXX</code>) or reach out to us.
      </p>
      <div style="display:flex;gap:var(--space-3);justify-content:center;flex-wrap:wrap">
        <a href="/contact" class="btn btn-outline">Contact Support</a>
        <a href="/shop" class="btn btn-primary">Browse Shop</a>
      </div>
    </div>

  </div>
</main>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<script src="/js/utils.js"></script>
<script>
'use strict';

const STATUS_ICONS = {
  confirmed: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>',
  processing: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>',
  shipped: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
  delivered: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
  pending: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'
};

function copyOrderNumber(num) {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(num).then(() => {
      const chip = document.getElementById('copyChip');
      if (chip) chip.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Copied!';
      setTimeout(() => {
        if (chip) chip.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy';
      }, 2000);
    });
  }
}

function lookupOrder() {
  const raw = document.getElementById('orderInput').value.trim().toUpperCase();
  if (!raw) return;
  const clean = raw.replace(/^#/, '').replace(/\s+/g, '-');
  const id = clean.startsWith('ORD-') ? clean : 'ORD-' + clean;

  const result = document.getElementById('trackingResult');
  const notFound = document.getElementById('trackingNotFound');
  const btn = document.getElementById('trackBtn');

  btn.disabled = true;
  btn.innerHTML = '<div class="spinner"></div> Tracking…';

  const token = localStorage.getItem('pp_token');
  const basePath = window.location.pathname.includes('/mithooos') ? '/mithooos' : '';
  
  fetch(`${basePath}/backend/index.php?_url=/api/orders/` + encodeURIComponent(id), {
    headers: token ? { 'Authorization': 'Bearer ' + token } : {}
  })
  .then(r => r.json())
  .then(res => {
    btn.disabled = false;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg> Track Order';

    if (res.success && res.data) {
      renderOrder(normalizeApiOrder(res.data));
    } else {
      throw new Error('Not found');
    }
  })
  .catch(() => {
    btn.disabled = false;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg> Track Order';
    result.classList.remove('show');
    notFound.classList.add('show');
    notFound.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
}

function normalizeApiOrder(raw) {
  const items = (raw.items || []).map(item => ({
    name: item.product_name || 'Product',
    variant: item.product_sku ? `SKU: ${item.product_sku}` : (item.sku ? `SKU: ${item.sku}` : ''),
    qty: Number(item.quantity || 1),
    price: Number(item.price_at_purchase || item.price || item.item_total || 0),
    image: item.image || (item.product_id ? `/backend/api/image.php?type=product&id=${item.product_id}` : '/images/logo.png')
  }));

  let stepStatus = (raw.order_status || 'pending').toLowerCase();
  if (raw.payment_method === 'easypaisa' && raw.payment_status === 'pending') {
    stepStatus = 'pending_verification';
  }

  // Parse delivery estimate or compute 3–5 business days
  let estDate = raw.estimated_delivery;
  if (!estDate && raw.created_at) {
    const dStart = new Date(raw.created_at); dStart.setDate(dStart.getDate() + 3);
    const dEnd = new Date(raw.created_at); dEnd.setDate(dEnd.getDate() + 5);
    const fmt = d => d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    estDate = `${fmt(dStart)} – ${fmt(dEnd)}`;
  } else if (!estDate) {
    estDate = '3–5 Business Days';
  }

  // Parse shipping address
  let shipObj = null;
  if (typeof raw.shipping_address === 'string') {
    try { shipObj = JSON.parse(raw.shipping_address); } catch (_) {}
  } else if (raw.shipping_address) {
    shipObj = raw.shipping_address;
  }

  // Build milestone steps
  const isDoneConfirmed = ['confirmed', 'processing', 'shipped', 'delivered'].includes(stepStatus);
  const isDoneProcessing = ['processing', 'shipped', 'delivered'].includes(stepStatus);
  const isDoneShipped = ['shipped', 'delivered'].includes(stepStatus);
  const isDoneDelivered = stepStatus === 'delivered';

  const orderTimeStr = raw.created_at ? formatDate(raw.created_at) : 'Just now';

  const steps = [
    {
      key: 'ordered',
      label: 'Order Placed',
      desc: 'Order received and logged.',
      time: orderTimeStr,
      done: true,
      active: false
    },
    {
      key: 'confirmed',
      label: 'Confirmed',
      desc: raw.payment_method === 'cod' ? 'Cash on Delivery confirmed.' : 'Payment confirmed.',
      time: isDoneConfirmed ? orderTimeStr : '',
      done: isDoneConfirmed,
      active: stepStatus === 'confirmed'
    },
    {
      key: 'processing',
      label: 'Preparing',
      desc: 'Items carefully inspected & packed.',
      time: '',
      done: isDoneProcessing,
      active: stepStatus === 'processing'
    },
    {
      key: 'shipped',
      label: 'On the Way',
      desc: raw.tracking_number ? `Tracking #${raw.tracking_number}` : 'Handed to courier for delivery.',
      time: '',
      done: isDoneShipped,
      active: stepStatus === 'shipped'
    },
    {
      key: 'delivered',
      label: 'Delivered',
      desc: isDoneDelivered ? 'Delivered successfully.' : 'Will be delivered to your door.',
      time: isDoneDelivered ? 'Delivered' : '',
      done: isDoneDelivered,
      active: stepStatus === 'delivered'
    }
  ];

  return {
    id: raw.order_number || raw.id || 'N/A',
    date: raw.created_at || new Date().toISOString(),
    status: stepStatus,
    total: Number(raw.total_amount || 0),
    estimatedDelivery: estDate,
    carrier: raw.shipping_method === 'standard' ? 'Standard Shipping (3–5 Days)' : (raw.shipping_method || 'Standard'),
    payment_method: raw.payment_method || 'cod',
    payment_status: raw.payment_status || 'pending',
    shipping_address: shipObj,
    customer_name: shipObj ? `${shipObj.first_name || ''} ${shipObj.last_name || ''}`.trim() : (raw.first_name ? `${raw.first_name} ${raw.last_name}` : 'Customer'),
    customer_email: shipObj?.email || raw.email || '',
    customer_phone: shipObj?.phone || raw.phone || '',
    items,
    steps
  };
}

function renderOrder(order) {
  document.getElementById('trackingNotFound').classList.remove('show');

  // Status mapping
  let statusKey = order.status;
  let statusText = 'Confirmed';
  if (statusKey === 'pending_verification') { statusText = 'Pending Verification'; statusKey = 'pending'; }
  else if (statusKey === 'confirmed') statusText = 'Order Confirmed';
  else if (statusKey === 'processing') statusText = 'Being Prepared';
  else if (statusKey === 'shipped') statusText = 'On the Way';
  else if (statusKey === 'delivered') statusText = 'Delivered';
  else if (statusKey === 'cancelled') statusText = 'Cancelled';
  else statusText = statusKey.charAt(0).toUpperCase() + statusKey.slice(1);

  const statusIcon = STATUS_ICONS[statusKey] || STATUS_ICONS.confirmed;

  // 1. Render Header Bar
  document.getElementById('resultHeader').innerHTML = `
    <div>
      <div class="trk-order-subtext">Verified Order</div>
      <div class="trk-order-id-wrap">
        <span class="trk-order-num">#${escapeHtml(order.id)}</span>
        <button class="trk-copy-chip" id="copyChip" onclick="copyOrderNumber('${escapeHtml(order.id)}')">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          Copy
        </button>
      </div>
    </div>
    <div>
      <span class="trk-status-pill ${statusKey}">
        ${statusIcon}
        ${statusText}
      </span>
    </div>
  `;

  // 2. Render Metrics Row
  document.getElementById('metricsGrid').innerHTML = `
    <div class="trk-metric-box">
      <div class="trk-metric-label">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Placed On
      </div>
      <div class="trk-metric-val">${formatDate(order.date)}</div>
    </div>
    <div class="trk-metric-box">
      <div class="trk-metric-label">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Est. Delivery
      </div>
      <div class="trk-metric-val" style="color:var(--color-primary)">${order.estimatedDelivery}</div>
    </div>
    <div class="trk-metric-box">
      <div class="trk-metric-label">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        Total Paid
      </div>
      <div class="trk-metric-val">${formatPrice(order.total)}</div>
    </div>
    <div class="trk-metric-box">
      <div class="trk-metric-label">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        Shipping
      </div>
      <div class="trk-metric-val">${escapeHtml(order.carrier)}</div>
    </div>
  `;

  // 3. Render Visual Stepper
  const doneCount = order.steps.filter(s => s.done).length;
  const progressPercent = Math.max(0, Math.min(100, ((doneCount - 1) / (order.steps.length - 1)) * 100));

  const stepperHtml = `
    <div class="trk-track-progress" style="width: calc(${progressPercent}% * 0.9 + 20px)"></div>
    ${order.steps.map(step => {
      const cls = step.done ? 'done' : (step.active ? 'active' : 'pending');
      const nodeIcon = step.done
        ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>'
        : (step.active
          ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>'
          : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>');

      return `
        <div class="trk-milestone ${cls}">
          <div class="trk-node-circle">${nodeIcon}</div>
          <div>
            <div class="trk-node-title">${escapeHtml(step.label)}</div>
            <div class="trk-node-desc">${escapeHtml(step.desc)}</div>
            ${step.time ? `<div class="trk-node-time">${escapeHtml(step.time)}</div>` : ''}
          </div>
        </div>
      `;
    }).join('')}
  `;
  document.getElementById('trackingStepper').innerHTML = stepperHtml;

  // 4. Render Items List
  document.getElementById('trackingItemsList').innerHTML = order.items.map(item => `
    <div class="trk-item-card">
      <div class="trk-item-pic">
        <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" onerror="this.src='/images/logo.png'">
      </div>
      <div class="trk-item-details">
        <div class="trk-item-name">${escapeHtml(item.name)}</div>
        <div class="trk-item-meta">
          ${item.variant ? `<span>${escapeHtml(item.variant)}</span> · ` : ''}
          <span style="font-weight:600">Qty: ${item.qty}</span>
        </div>
      </div>
      <div class="trk-item-cost">${formatPrice(item.price * item.qty)}</div>
    </div>
  `).join('');

  // 5. Render Shipping Address Card
  if (order.shipping_address) {
    const s = order.shipping_address;
    document.getElementById('trkShippingAddress').innerHTML = `
      <div style="font-weight:700;color:var(--color-ink);margin-bottom:2px">${escapeHtml(order.customer_name)}</div>
      <div style="color:var(--color-body)">${escapeHtml(s.street || '')} ${s.apt ? escapeHtml(s.apt) : ''}</div>
      <div style="color:var(--color-body)">${escapeHtml(s.city || '')}, ${escapeHtml(s.state || '')} ${escapeHtml(s.zip || '')}</div>
      ${s.phone ? `<div style="color:var(--color-muted);font-size:12px;margin-top:4px"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:2px"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>${escapeHtml(s.phone)}</div>` : ''}
    `;
  } else {
    document.getElementById('trkShippingAddress').innerHTML = `
      <div style="font-weight:600">${escapeHtml(order.customer_name)}</div>
      <div style="color:var(--color-muted);font-size:12px">Address details verified on invoice</div>
    `;
  }

  // 6. Render Payment Info Card
  const isCod = order.payment_method === 'cod';
  document.getElementById('trkPaymentInfo').innerHTML = `
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
      <span style="font-weight:700;color:var(--color-ink)">${isCod ? 'Cash on Delivery (COD)' : 'Online / Easypaisa'}</span>
      <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;background:${order.payment_status === 'completed' ? '#D1FAE5' : '#FEF3C7'};color:${order.payment_status === 'completed' ? '#065F46' : '#92400E'}">
        ${order.payment_status === 'completed' ? 'Paid' : 'Pending'}
      </span>
    </div>
    <div style="font-size:12px;color:var(--color-muted)">
      ${isCod ? 'Pay cash upon package handover.' : 'Payment proof verified by Mithooos.'}
    </div>
  `;

  // Show Result Card and scroll smoothly
  document.getElementById('trackingResult').classList.add('show');
  document.getElementById('trackingResult').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Enter Key triggers search
document.getElementById('orderInput')?.addEventListener('keydown', e => {
  if (e.key === 'Enter') lookupOrder();
});

// Auto-lookup on page load if ?order= or ?id= is present in URL
document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const orderId = params.get('order') || params.get('id');
  if (orderId) {
    document.getElementById('orderInput').value = orderId;
    lookupOrder();
  }
});
</script>

<?php include __DIR__ . '/../includes/frontend/footer.php'; ?>
</body>
</html>
