<!DOCTYPE html>
<html lang="en">
<?php include '../includes/frontend/head.php'; ?>
<body>

<!-- ── NAVBAR ── -->
<?php include '../includes/frontend/navbar.php'; ?>


<?php include '../includes/frontend/mobile-nav.php'; ?>


<!-- Mobile nav -->
<?php include '../includes/frontend/mobile-nav.php'; ?>


<!-- ── MAIN ── -->
<main class="wishlist-section">
  <div class="container">

    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom: var(--space-6);">
      <a href="../index.php">Home</a>
      <span class="breadcrumb-sep">/</span>
      <span>Wishlist</span>
    </nav>

    <div class="wishlist-header">
      <h1 class="wishlist-title">My Wishlist</h1>
      <p class="wishlist-subtitle" id="wishlistSubtitle">Loading your saved items…</p>
    </div>

    <!-- Action bar -->
    <div class="wishlist-actions-bar" id="wishlistActionsBar" style="display:none">
      <p class="wishlist-count-label"><span id="wishlistItemCount">0</span> saved items</p>
      <div style="display:flex; gap: var(--space-3);">
        <button class="btn btn-outline btn-sm" onclick="clearWishlist()">Clear All</button>
        <button class="btn btn-primary btn-sm" onclick="addAllToCart()">Add All to Cart</button>
      </div>
    </div>

    <!-- Skeleton loaders (shown while loading) -->
    <div class="wishlist-grid" id="skeletonGrid">
      <div class="skeleton-card skeleton">
        <div class="skeleton-img skeleton"></div>
        <div class="skeleton-body">
          <div class="skeleton-line skeleton"></div>
          <div class="skeleton-line short skeleton"></div>
          <div class="skeleton-line price skeleton"></div>
        </div>
      </div>
      <div class="skeleton-card skeleton">
        <div class="skeleton-img skeleton"></div>
        <div class="skeleton-body">
          <div class="skeleton-line skeleton"></div>
          <div class="skeleton-line short skeleton"></div>
          <div class="skeleton-line price skeleton"></div>
        </div>
      </div>
      <div class="skeleton-card skeleton">
        <div class="skeleton-img skeleton"></div>
        <div class="skeleton-body">
          <div class="skeleton-line skeleton"></div>
          <div class="skeleton-line short skeleton"></div>
          <div class="skeleton-line price skeleton"></div>
        </div>
      </div>
      <div class="skeleton-card skeleton">
        <div class="skeleton-img skeleton"></div>
        <div class="skeleton-body">
          <div class="skeleton-line skeleton"></div>
          <div class="skeleton-line short skeleton"></div>
          <div class="skeleton-line price skeleton"></div>
        </div>
      </div>
    </div>

    <!-- Wishlist product grid -->
    <div class="wishlist-grid" id="wishlistGrid" style="display:none"></div>

    <!-- Empty state -->
    <div class="wishlist-empty" id="wishlistEmpty">
      <div class="wishlist-empty-icon">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </div>
      <h3>Your wishlist is empty</h3>
      <p>Save items you love while you browse — they'll all appear here.</p>
      <a href="shop.php" class="btn btn-primary btn-lg">Start Shopping</a>
    </div>

  </div>
</main>

<!-- Toast container -->
<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<!-- ── SCRIPTS ── -->
<script src="../js/utils.js"></script>
<script src="../js/api-client.js"></script>
<script src="../js/cart.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/search.js"></script>

<script>
'use strict';

/* ══════════════════════════════════════════
   WISHLIST — Wired to the real backend
   (GET/DELETE /api/wishlist — the same server-side wishlist used by
   account.php and product-detail.php; previously this page kept its
   own separate localStorage-only list that never synced with either)
   ══════════════════════════════════════════ */

if (!API.auth.isLoggedIn()) {
  window.location.href = `login.php?redirect=${encodeURIComponent('wishlist.php')}`;
}

let wishlistItems = [];

function updateCountLabel() {
  const count = wishlistItems.length;
  document.getElementById('wishlistItemCount').textContent = count;
  document.getElementById('wishlistSubtitle').textContent = count === 0
    ? 'No saved items yet'
    : count + ' saved item' + (count === 1 ? '' : 's');
  document.querySelectorAll('#wishlistCount').forEach(el => el.textContent = count);
}

function checkEmpty() {
  const empty = document.getElementById('wishlistEmpty');
  const bar   = document.getElementById('wishlistActionsBar');
  if (wishlistItems.length === 0) {
    empty.classList.add('show');
    bar.style.display = 'none';
  } else {
    empty.classList.remove('show');
    bar.style.display = 'flex';
  }
  updateCountLabel();
}

async function removeFromWishlist(wishlistId, productId) {
  try {
    await API.wishlist.remove(wishlistId);
  } catch (err) {
    showToast(err.message || 'Could not remove item.', 'error');
    return;
  }
  wishlistItems = wishlistItems.filter(i => i.wishlist_id !== wishlistId);
  const card = document.getElementById('wl-card-' + productId);
  if (card) {
    card.style.transition = 'all .3s ease';
    card.style.opacity = '0';
    card.style.transform = 'scale(.92)';
    setTimeout(() => { card.remove(); checkEmpty(); }, 300);
  } else {
    checkEmpty();
  }
  showToast('Removed from wishlist', 'info');
}

async function addToCartFromWishlist(item) {
  try {
    await Cart.add(
      { product_id: item.product_id, product_name: item.product_name, final_price: item.final_price },
      1
    );
  } catch (err) {
    showToast(err.message || 'Could not add to cart.', 'error');
  }
}

async function addAllToCart() {
  if (!wishlistItems.length) return;
  let count = 0;
  for (const item of wishlistItems) {
    try {
      await Cart.add(
        { product_id: item.product_id, product_name: item.product_name, final_price: item.final_price },
        1
      );
      count++;
    } catch { /* skip failures, continue with the rest */ }
  }
  if (count > 0) showToast(count + ' item' + (count !== 1 ? 's' : '') + ' added to cart', 'success');
}

async function clearWishlist() {
  if (!confirm('Remove all items from your wishlist?')) return;
  try {
    await Promise.allSettled(wishlistItems.map(item => API.wishlist.remove(item.wishlist_id)));
  } catch { /* individual failures are tolerated — re-render from the server after */ }
  wishlistItems = [];
  document.getElementById('wishlistGrid').innerHTML = '';
  checkEmpty();
  showToast('Wishlist cleared', 'info');
}

/* ── Render a single product card (createElement/textContent throughout —
   no innerHTML with untrusted data, so no XSS risk by construction) ── */
function buildCard(item) {
  const article = document.createElement('article');
  article.className = 'product-card wishlist-product-card';
  article.id = 'wl-card-' + item.product_id;
  article.dataset.productId = item.product_id;

  const imgWrap = document.createElement('div');
  imgWrap.className = 'product-card-img';

  if (item.image) {
    const img = document.createElement('img');
    img.src = item.image;
    img.alt = item.product_name;
    img.loading = 'lazy';
    img.width = 400; img.height = 533;
    imgWrap.appendChild(img);
  } else {
    const ph = document.createElement('div');
    ph.className = 'img-placeholder';
    ph.style.cssText = 'width:100%;height:100%;aspect-ratio:3/4;display:flex;align-items:center;justify-content:center;';
    ph.innerHTML = '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
    imgWrap.appendChild(ph);
  }

  const removeBtn = document.createElement('button');
  removeBtn.className = 'wishlist-remove';
  removeBtn.setAttribute('aria-label', 'Remove from wishlist');
  removeBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>';
  removeBtn.addEventListener('click', () => removeFromWishlist(item.wishlist_id, item.product_id));

  const actions = document.createElement('div');
  actions.className = 'product-card-actions';
  const cartBtn = document.createElement('button');
  cartBtn.className = 'btn btn-primary btn-sm';
  cartBtn.style.flex = '1';
  cartBtn.textContent = 'Add to Cart';
  cartBtn.addEventListener('click', () => addToCartFromWishlist(item));
  const viewLink = document.createElement('a');
  viewLink.href = 'product.php?id=' + item.product_id;
  viewLink.className = 'btn btn-sm';
  viewLink.style.cssText = 'background:rgba(255,255,255,.2);color:white;border:1px solid rgba(255,255,255,.3);';
  viewLink.textContent = 'View';

  actions.appendChild(cartBtn);
  actions.appendChild(viewLink);
  imgWrap.appendChild(removeBtn);
  imgWrap.appendChild(actions);

  const body = document.createElement('div');
  body.className = 'product-card-body';

  const name = document.createElement('a');
  name.className = 'product-name';
  name.href = 'product.php?id=' + item.product_id;
  name.textContent = item.product_name || 'Product';

  const priceRow = document.createElement('div');
  priceRow.className = 'product-price';
  const current = document.createElement('span');
  current.className = 'product-price-current';
  current.textContent = formatPrice(item.final_price);
  priceRow.appendChild(current);

  body.appendChild(name);
  body.appendChild(priceRow);

  article.appendChild(imgWrap);
  article.appendChild(body);
  return article;
}

/* ── Main render ── */
async function renderWishlist() {
  const skeleton = document.getElementById('skeletonGrid');
  const grid     = document.getElementById('wishlistGrid');
  const subtitle = document.getElementById('wishlistSubtitle');

  try {
    const res = await API.wishlist.list();
    wishlistItems = res.data || [];
  } catch (err) {
    skeleton.style.display = 'none';
    subtitle.textContent = 'Could not load your wishlist.';
    showToast(err.message || 'Could not load your wishlist.', 'error');
    checkEmpty();
    return;
  }

  skeleton.style.display = 'none';
  grid.style.display = 'grid';

  if (wishlistItems.length === 0) {
    subtitle.textContent = 'No saved items yet';
    checkEmpty();
    return;
  }

  grid.innerHTML = '';
  wishlistItems.forEach(item => grid.appendChild(buildCard(item)));
  checkEmpty();
}

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
  navOverlay.addEventListener('click', () => {
    mobileNav.classList.remove('open');
    navOverlay.classList.remove('active');
    menuToggle.setAttribute('aria-expanded', false);
  });

  // Navbar scroll
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
  }, { passive: true });

  renderWishlist();
});
</script>

<script src="../js/site-footer.js"></script>
</body>
</html>
