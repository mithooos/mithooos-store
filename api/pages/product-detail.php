<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>

<!-- Navbar -->
<?php include __DIR__ . '/../includes/frontend/navbar.php'; ?>


<?php include __DIR__ . '/../includes/frontend/mobile-nav.php'; ?>


<div class="container">
  <!-- Breadcrumb -->
  <nav class="breadcrumb" style="padding-top:var(--space-6)" aria-label="Breadcrumb">
    <a href="/">Home</a><span class="breadcrumb-sep">/</span>
    <a href="/shop">Shop</a><span class="breadcrumb-sep">/</span>
    <a href="/shop" id="breadcrumbCat">Category</a><span class="breadcrumb-sep">/</span>
    <span id="breadcrumbName">Loading…</span>
  </nav>

  <div class="product-detail-grid">

    <!-- ── IMAGE GALLERY ── -->
    <div>
      <div class="gallery-main" id="galleryMain">
        <div class="img-placeholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#93C5FD" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
      </div>
      <div class="gallery-thumbs" id="galleryThumbs">
        <!-- Populated dynamically from the product's images array — see renderGallery() below -->
      </div>
    </div>

    <!-- ── PRODUCT INFO ── -->
    <div class="product-info-panel">
      <div>
        <h1 class="product-detail-title" id="pdTitle">Loading…</h1>
      </div>

      <div class="product-detail-rating">
        <span class="rating-stars-lg" id="pdStars"></span>
        <span style="font-weight:700;color:var(--color-ink)" id="pdRatingNum">—</span>
        <a href="#reviews" class="rating-link" id="pdReviewCount">0 reviews</a>
      </div>

      <div class="product-detail-price" id="pdPrice">
        <span class="price-current">—</span>
      </div>

      <div class="stock-status in-stock" id="pdStock">
        <div class="stock-dot"></div>
        Checking availability…
      </div>

      <!-- Variant Selector -->
      <div id="pdVariantsWrap" style="display:none;margin-bottom:var(--space-4)">
        <p class="variant-label" style="font-size:var(--text-sm);font-weight:600;margin-bottom:var(--space-2)">Select Option</p>
        <div class="variant-pills" id="pdVariants"></div>
      </div>

      <!-- Qty + Add to cart -->
      <div>
        <p class="variant-label" style="font-size:var(--text-sm);font-weight:600;margin-bottom:var(--space-2)">Quantity</p>
        <div style="display:flex;align-items:center;gap:var(--space-4);flex-wrap:wrap">
          <div class="qty-selector">
            <button class="qty-btn" id="qtyMinus" aria-label="Decrease quantity">−</button>
            <input class="qty-val" id="qtyVal" value="1" readonly aria-label="Quantity">
            <button class="qty-btn" id="qtyPlus" aria-label="Increase quantity">+</button>
          </div>
          <div class="add-to-cart-row" style="flex:1">
            <button class="btn btn-primary btn-add-cart" onclick="handleAddToCart()">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              Add to Cart
            </button>
            <button class="btn-wishlist-lg" id="wishlistBtn" aria-label="Add to wishlist">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Trust badges -->
      <div class="trust-badges">
        <div class="trust-badge"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Free shipping over PKR 5,000</div>
        <div class="trust-badge"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> 30-day free returns</div>
        <div class="trust-badge"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Secure checkout</div>
        <div class="trust-badge"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> 100% authentic</div>
      </div>

      <!-- Share -->
      <div>
        <p style="font-size:var(--text-sm);font-weight:600;color:var(--color-ink);margin-bottom:var(--space-3)">Share this product</p>
        <div class="share-row">
          <button class="share-btn" aria-label="Share on Instagram" title="Instagram"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg></button>
          <button class="share-btn" aria-label="Share on Facebook" title="Facebook"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></button>
          <button class="share-btn" aria-label="Copy link" title="Copy link" onclick="navigator.clipboard.writeText(location.href);showToast('Link copied!','success')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></button>
        </div>
      </div>

      <!-- Accordion details -->
      <div class="accordion">
        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            Product Description
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="accordion-body" id="pdDescription">Loading…</div>
        </div>
        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            Size & Fit
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="accordion-body">
            This shirt fits true to size. We recommend sizing up if you prefer a more relaxed fit.<br><br>
            <strong>Chest:</strong> XS=32", S=34", M=36", L=38", XL=40"<br>
            <strong>Length:</strong> XS=27", S=28", M=29", L=30", XL=31"
          </div>
        </div>
        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            Shipping & Returns
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="accordion-body">
            <strong>Free shipping</strong> on orders over PKR 5,000. Standard delivery 3–5 business days.<br><br>
            <strong>30-day returns:</strong> Items must be unworn, unwashed, with original tags attached. Free return shipping on all orders.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── REVIEWS SECTION ── -->
<section class="reviews-section container" id="reviews">
  <h2 style="margin-bottom:var(--space-8)">Customer Reviews</h2>

  <div class="reviews-summary">
    <div class="rating-big">
      <div class="rating-num" id="reviewsAvg">—</div>
      <div class="rating-stars-big" id="reviewsAvgStars"></div>
      <div class="rating-total" id="reviewsTotalLabel">0 reviews</div>
    </div>
    <div class="rating-bars" id="ratingBars">
      <!-- Populated dynamically from the fetched reviews — see renderReviewSummary() below -->
    </div>
  </div>

  <!-- Review cards -->
  <div id="reviewsList">
    <p style="color:var(--color-muted);font-size:var(--text-sm)">Loading reviews…</p>
  </div>

  <div style="text-align:center;margin-top:var(--space-8)">
    <button class="btn btn-outline" id="loadMoreReviewsBtn" style="display:none">Load More Reviews</button>
    <button class="btn btn-primary" style="margin-left:var(--space-3)" onclick="openReviewForm()">Write a Review</button>
  </div>

  <!-- Write-a-review form (hidden until opened) -->
  <div id="reviewFormWrap" class="hidden" style="max-width:600px;margin:var(--space-8) auto 0;padding:var(--space-6);background:var(--color-surface);border-radius:var(--radius-xl)">
    <h3 style="margin-bottom:var(--space-4)">Write a Review</h3>
    <div class="form-group">
      <label class="form-label">Your Rating</label>
      <div id="reviewStarInput" style="display:flex;gap:4px;font-size:1.5rem;cursor:pointer" data-rating="0">
        <span data-star="1"></span><span data-star="2"></span><span data-star="3"></span><span data-star="4"></span><span data-star="5"></span>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="reviewTitleInput">Title (optional)</label>
      <input type="text" id="reviewTitleInput" class="form-input" placeholder="Summarize your experience" maxlength="150">
    </div>
    <div class="form-group">
      <label class="form-label" for="reviewTextInput">Review</label>
      <textarea id="reviewTextInput" class="form-input" rows="4" placeholder="What did you like or dislike?"></textarea>
    </div>
    <div style="display:flex;gap:var(--space-3)">
      <button class="btn btn-primary" onclick="submitReview()">Submit Review</button>
      <button class="btn btn-outline" onclick="closeReviewForm()">Cancel</button>
    </div>
  </div>
</section>

<!-- Related products -->
<section class="related-section">
  <div class="container">
    <h2 style="margin-bottom:var(--space-8)">You Might Also Like</h2>
    <div class="grid-products" id="relatedProducts">
      <!-- Populated dynamically from GET /api/products?category_id=... — see loadRelatedProducts() below -->
    </div>
  </div>
</section>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<script src="/js/utils.js"></script>
<script src="/js/api-client.js"></script>
<script src="/js/cart.js"></script>
<script src="/js/auth.js"></script>
<script src="/js/search.js"></script>
<script>
'use strict';
const nb = document.getElementById('navbar');
window.addEventListener('scroll', () => nb.classList.toggle('scrolled', scrollY > 20), { passive: true });

/* ══════════════════════════════════════════
   PRODUCT DETAIL — Wired to the real backend
   (GET /api/products/:id, GET/POST /api/reviews, POST /api/wishlist)
   ══════════════════════════════════════════ */

const params = new URLSearchParams(location.search);
const productId = params.get('id');

let currentProduct = null;
let selectedVariantId = null;
let reviewsPage = 1;
let reviewsHasMore = false;
let isWishlisted = false;
let selectedStarRating = 0;

if (!productId) {
  // No product specified — nothing to show
  document.querySelector('.product-detail-grid').innerHTML =
    `<div style="grid-column:1/-1;text-align:center;padding:var(--space-20) var(--space-4);">
      <div style="width:80px;height:80px;background:var(--color-surface-alt);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-6);color:var(--color-muted);">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </div>
      <h3 style="font-size:var(--text-xl);margin-bottom:var(--space-3)">Product Not Found</h3>
      <p style="color:var(--color-muted);margin-bottom:var(--space-8)">The product you are looking for does not exist or has been removed.</p>
      <a href="/shop" class="btn btn-primary">Return to Shop</a>
    </div>`;
} else {
  loadProduct();
}

async function loadProduct() {
  try {
    const res = await API.products.get(productId);
    currentProduct = res.data;
    renderProduct(currentProduct);
    await checkWishlistStatus();
    loadReviews(1);
    loadRelatedProducts();
  } catch (err) {
    document.querySelector('.product-detail-grid').innerHTML =
      `<div style="grid-column:1/-1;text-align:center;padding:var(--space-20) var(--space-4);">
        <div style="width:80px;height:80px;background:var(--color-surface-alt);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-6);color:var(--color-error);">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <h3 style="font-size:var(--text-xl);margin-bottom:var(--space-3)">${err.status === 404 ? 'Product Not Found' : 'Error Loading Product'}</h3>
        <p style="color:var(--color-muted);margin-bottom:var(--space-8)">${err.status === 404 ? 'The product you are looking for could not be found.' : 'There was a problem loading this product. Please try again later.'}</p>
        <a href="/shop" class="btn btn-primary">Return to Shop</a>
      </div>`;
  }
}

function renderProduct(p) {
  document.title = `${p.product_name} — Mithooos`;

  // SEO / social sharing — update to reflect the real product now that we
  // have it. (Note: search engines and most link-preview bots that don't
  // execute JavaScript will still see the generic placeholder tags in the
  // initial HTML. A fully crawler-proof solution needs server-side
  // rendering or prerendering for this route — out of scope for a static
  // frontend fix, but worth knowing if organic product-page SEO matters.)
  const shortDesc = (p.short_description || p.description || '').slice(0, 160);
  const ogImage = (p.images && p.images[0]?.image_url) || 'https://mithooos.com/images/og-image.png';
  const pageUrl = `https://mithooos.com/product-detail?id=${p.product_id}`;
  document.getElementById('metaDescription')?.setAttribute('content', shortDesc);
  document.getElementById('ogTitle')?.setAttribute('content', `${p.product_name} — Mithooos`);
  document.getElementById('ogDescription')?.setAttribute('content', shortDesc);
  document.getElementById('ogImage')?.setAttribute('content', ogImage);
  document.getElementById('ogUrl')?.setAttribute('content', pageUrl);
  document.getElementById('twitterTitle')?.setAttribute('content', `${p.product_name} — Mithooos`);
  document.getElementById('twitterDescription')?.setAttribute('content', shortDesc);
  document.getElementById('twitterImage')?.setAttribute('content', ogImage);

  document.getElementById('pdTitle').textContent = p.product_name;

  document.getElementById('breadcrumbName').textContent = p.product_name;
  const catLink = document.getElementById('breadcrumbCat');
  if (p.category_name) {
    catLink.textContent = p.category_name;
    catLink.href = `/shop?cat=${encodeURIComponent((p.category_name || '').toLowerCase())}`;
  } else {
    catLink.style.display = 'none';
  }

  // Rating
  const rating = parseFloat(p.rating) || 0;
  const reviewCount = parseInt(p.review_count, 10) || 0;
  document.getElementById('pdStars').innerHTML = starString(rating);
  document.getElementById('pdRatingNum').textContent = rating > 0 ? rating.toFixed(1) : '—';
  document.getElementById('pdReviewCount').textContent = `${reviewCount} review${reviewCount !== 1 ? 's' : ''}`;

  // Price
  const finalPrice = parseFloat(p.final_price ?? p.price);
  const origPrice = parseFloat(p.price);
  const hasDiscount = parseFloat(p.discount_percentage) > 0;

  const now = new Date();
  const hasOnlineDiscount = p.online_discount_enabled 
    && (!p.online_discount_start || new Date(p.online_discount_start) <= now)
    && (!p.online_discount_end || new Date(p.online_discount_end) >= now);

  const priceEl = document.getElementById('pdPrice');
  priceEl.innerHTML = `<span class="price-current">${formatPrice(finalPrice)}</span>` +
    (hasDiscount ? `
      <span class="price-original">${formatPrice(origPrice)}</span>
      <span class="price-save">Save ${formatPrice(origPrice - finalPrice)} (${p.discount_percentage}% off)</span>` : '') +
    (hasOnlineDiscount ? `<div style="margin-top:0.5rem"><span class="badge" style="background:#8B5CF6;color:white;font-size:0.75rem;padding:0.3rem 0.6rem;display:inline-block;">${escapeHtml(p.online_discount_label || 'Online Discount')}</span></div>` : '');

  // Stock
  const stockEl = document.getElementById('pdStock');
  const stock = parseInt(p.stock_quantity, 10) || 0;
  const addBtn = document.querySelector('.btn-add-cart');
  if (stock <= 0) {
    stockEl.className = 'stock-status out-stock';
    stockEl.innerHTML = `<div class="stock-dot"></div> Out of Stock`;
    if (addBtn) { addBtn.disabled = true; addBtn.style.opacity = '.5'; addBtn.style.cursor = 'not-allowed'; }
  } else if (stock <= 10) {
    stockEl.className = 'stock-status in-stock';
    stockEl.innerHTML = `<div class="stock-dot"></div> In Stock — Only ${stock} left`;
  } else {
    stockEl.className = 'stock-status in-stock';
    stockEl.innerHTML = `<div class="stock-dot"></div> In Stock`;
  }

  // Variants
  const variantsWrap = document.getElementById('pdVariantsWrap');
  const variantsContainer = document.getElementById('pdVariants');
  if (p.variants && p.variants.length > 0) {
    variantsWrap.style.display = 'block';
    variantsContainer.innerHTML = p.variants.map(v => 
      `<button class="variant-pill" data-id="${v.variant_id}" data-price="${v.price ?? p.final_price ?? p.price}" data-stock="${v.stock_quantity}">
        ${escapeHtml(v.variant_name)}
      </button>`
    ).join('');
    
    const pills = variantsContainer.querySelectorAll('.variant-pill');
    pills.forEach(pill => {
      pill.addEventListener('click', () => {
        pills.forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        selectedVariantId = parseInt(pill.dataset.id, 10);
        
        const vPrice = parseFloat(pill.dataset.price);
        const vStock = parseInt(pill.dataset.stock, 10);
        
        const priceEl = document.getElementById('pdPrice');
        priceEl.innerHTML = `<span class="price-current">${formatPrice(vPrice)}</span>`;
        
        const stockEl = document.getElementById('pdStock');
        const addBtn = document.querySelector('.btn-add-cart');
        if (vStock <= 0) {
          stockEl.className = 'stock-status out-stock';
          stockEl.innerHTML = `<div class="stock-dot"></div> Out of Stock`;
          if (addBtn) { addBtn.disabled = true; addBtn.style.opacity = '.5'; addBtn.style.cursor = 'not-allowed'; }
        } else if (vStock <= 10) {
          stockEl.className = 'stock-status in-stock';
          stockEl.innerHTML = `<div class="stock-dot"></div> In Stock — Only ${vStock} left`;
          if (addBtn) { addBtn.disabled = false; addBtn.style.opacity = '1'; addBtn.style.cursor = 'pointer'; }
        } else {
          stockEl.className = 'stock-status in-stock';
          stockEl.innerHTML = `<div class="stock-dot"></div> In Stock`;
          if (addBtn) { addBtn.disabled = false; addBtn.style.opacity = '1'; addBtn.style.cursor = 'pointer'; }
        }
      });
    });
    
    const firstAvailable = Array.from(pills).find(p => parseInt(p.dataset.stock, 10) > 0) || pills[0];
    if (firstAvailable) firstAvailable.click();
    
  } else {
    variantsWrap.style.display = 'none';
  }

  // Description
  document.getElementById('pdDescription').textContent = p.description || 'No description available.';

  renderGallery(p.images || []);
}

function starString(rating) {
  const rounded = Math.round(rating);
  return Array.from({ length: 5 }, (_, index) => `<svg width="18" height="18" viewBox="0 0 24 24" fill="${index < rounded ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`).join('');
}

/* ── Gallery ── */
function renderGallery(images) {
  const main = document.getElementById('galleryMain');
  const thumbsEl = document.getElementById('galleryThumbs');

  if (!images.length) {
    main.innerHTML = placeholderImg(80);
    thumbsEl.innerHTML = '';
    return;
  }

  main.innerHTML = `<img src="${escapeHtml(resolveImageUrl(images[0].image_url))}" alt="${escapeHtml(currentProduct.product_name)}" onerror="handleImageError(this)" style="width:100%;height:100%;object-fit:cover"><div class="img-fallback" style="display:none;width:100%;height:100%">${placeholderImg(80)}</div>`;

  thumbsEl.innerHTML = images.map((img, i) => `
    <div class="gallery-thumb ${i === 0 ? 'active' : ''}" data-idx="${i}">
      <img src="${escapeHtml(resolveImageUrl(img.image_url))}" alt="" loading="lazy" onerror="handleImageError(this)" style="width:100%;height:100%;object-fit:cover">
    </div>`).join('');

  thumbsEl.querySelectorAll('.gallery-thumb').forEach(t => {
    t.addEventListener('click', () => setMainImg(parseInt(t.dataset.idx, 10), images));
  });
}

function setMainImg(idx, images) {
  images = images || currentProduct.images || [];
  document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
  document.querySelector(`.gallery-thumb[data-idx="${idx}"]`)?.classList.add('active');
  if (images[idx]) {
    document.getElementById('galleryMain').innerHTML =
      `<img src="${escapeHtml(resolveImageUrl(images[idx].image_url))}" alt="${escapeHtml(currentProduct.product_name)}" onerror="handleImageError(this)" style="width:100%;height:100%;object-fit:cover"><div class="img-fallback" style="display:none;width:100%;height:100%">${placeholderImg(80)}</div>`;
  }
}

function placeholderImg(size) {
  return `<div class="img-placeholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#FFF0EB,#FFE4F0);">
    <svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="#FFB89A" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
  </div>`;
}

/* ── Quantity ── */
let qty = 1;
document.getElementById('qtyMinus').addEventListener('click', () => { if (qty > 1) { qty--; document.getElementById('qtyVal').value = qty; } });
document.getElementById('qtyPlus').addEventListener('click', () => { if (qty < 10) { qty++; document.getElementById('qtyVal').value = qty; } });

/* ── Add to cart ── */
async function handleAddToCart() {
  if (!currentProduct) return;
  if ((parseInt(currentProduct.stock_quantity, 10) || 0) <= 0 && (!currentProduct.variants || currentProduct.variants.length === 0)) {
    showToast('This item is currently out of stock.', 'error');
    return;
  }
  
  if (currentProduct.variants && currentProduct.variants.length > 0 && !selectedVariantId) {
    showToast('Please select an option.', 'error');
    return;
  }
  
  const btn = document.querySelector('.btn-add-cart');
  btn.disabled = true;
  try {
    const payload = Object.assign({}, currentProduct, { variant_id: selectedVariantId });
    await Cart.add(payload, qty);
  } catch (err) {
    showToast(err.message || 'Could not add to cart.', 'error');
  } finally {
    btn.disabled = false;
  }
}

/* ── Wishlist ── */
async function checkWishlistStatus() {
  if (!API.auth.isLoggedIn()) return;
  try {
    const res = await API.wishlist.list();
    isWishlisted = (res.data || []).some(item => item.product_id === currentProduct.product_id);
    updateWishlistBtn();
  } catch { /* non-fatal */ }
}

function updateWishlistBtn() {
  const btn = document.getElementById('wishlistBtn');
  btn.classList.toggle('active', isWishlisted);
}

document.getElementById('wishlistBtn').addEventListener('click', async function () {
  if (!API.auth.isLoggedIn()) {
    showToast('Please log in to save items to your wishlist', 'info');
    setTimeout(() => window.location.href = `/login?redirect=${encodeURIComponent(location.pathname + location.search)}`, 1200);
    return;
  }
  try {
    const res = await API.wishlist.toggle(currentProduct.product_id);
    isWishlisted = res.data.added;
    updateWishlistBtn();
    showToast(isWishlisted ? 'Added to wishlist' : 'Removed from wishlist', 'info');
  } catch (err) {
    showToast(err.message || 'Could not update wishlist.', 'error');
  }
});

/* ── Reviews ── */
async function loadReviews(page) {
  try {
    const res = await API.reviews.list(productId, { page, limit: 5 });
    const rows = res.data || [];
    reviewsHasMore = res.pagination?.has_more || false;
    reviewsPage = page;

    const list = document.getElementById('reviewsList');
    if (page === 1) list.innerHTML = '';
    if (!rows.length && page === 1) {
      list.innerHTML = `<p style="color:var(--color-muted);font-size:var(--text-sm)">No reviews yet. Be the first to review this product!</p>`;
    } else {
      list.insertAdjacentHTML('beforeend', rows.map(reviewCard).join(''));
    }

    document.getElementById('loadMoreReviewsBtn').style.display = reviewsHasMore ? '' : 'none';
    if (page === 1) renderReviewSummary(currentProduct);
  } catch (err) {
    document.getElementById('reviewsList').innerHTML =
      `<p style="color:var(--color-error);font-size:var(--text-sm)">Could not load reviews.</p>`;
  }
}

function reviewCard(r) {
  const name = escapeHtml(`${r.first_name} ${(r.last_name || '').charAt(0)}.`);
  const initial = escapeHtml((r.first_name || '?').charAt(0).toUpperCase());
  const date = new Date(r.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  const title = r.title ? escapeHtml(r.title) : '';
  const text = escapeHtml(r.review_text || '');
  return `
    <div class="review-card">
      <div class="review-header">
        <div class="reviewer-meta">
          <div class="reviewer-avatar">${initial}</div>
          <div>
            <div class="reviewer-name">${name}</div>
            <div class="reviewer-date">${date}</div>
          </div>
        </div>
        <div>
          <div class="review-rating">${starString(parseInt(r.rating, 10))}</div>
          ${parseInt(r.is_verified_purchase, 10) ? `<span class="verified-badge"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Verified Purchase</span>` : ''}
        </div>
      </div>
      ${title ? `<div class="review-title">${title}</div>` : ''}
      <div class="review-text">${text}</div>
    </div>`;
}

function renderReviewSummary(p) {
  const rating = parseFloat(p.rating) || 0;
  const count = parseInt(p.review_count, 10) || 0;
  document.getElementById('reviewsAvg').textContent = rating > 0 ? rating.toFixed(1) : '—';
  document.getElementById('reviewsAvgStars').innerHTML = starString(rating);
  document.getElementById('reviewsTotalLabel').textContent = `${count} review${count !== 1 ? 's' : ''}`;
  // Per-star breakdown isn't returned by the API (Product::find() only gives
  // an aggregate average + count), so the bar chart isn't shown rather than
  // fabricating percentages.
  document.getElementById('ratingBars').innerHTML = count
    ? `<p style="font-size:var(--text-xs);color:var(--color-muted)">Rating breakdown by star isn't available yet.</p>`
    : '';
}

document.getElementById('loadMoreReviewsBtn').addEventListener('click', () => loadReviews(reviewsPage + 1));

/* ── Write a review ── */
function openReviewForm() {
  if (!API.auth.isLoggedIn()) {
    showToast('Please log in to write a review', 'info');
    setTimeout(() => window.location.href = `/login?redirect=${encodeURIComponent(location.pathname + location.search)}`, 1200);
    return;
  }
  document.getElementById('reviewFormWrap').classList.remove('hidden');
  renderStarInput();
  document.getElementById('reviewFormWrap').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function closeReviewForm() {
  document.getElementById('reviewFormWrap').classList.add('hidden');
  selectedStarRating = 0;
  renderStarInput();
  document.getElementById('reviewTitleInput').value = '';
  document.getElementById('reviewTextInput').value = '';
}

function renderStarInput() {
  document.querySelectorAll('#reviewStarInput span').forEach(s => {
    s.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="${parseInt(s.dataset.star, 10) <= selectedStarRating ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
  });
}

document.querySelectorAll('#reviewStarInput span').forEach(s => {
  s.addEventListener('click', () => {
    selectedStarRating = parseInt(s.dataset.star, 10);
    renderStarInput();
  });
});

async function submitReview() {
  if (selectedStarRating < 1) {
    showToast('Please select a star rating', 'error');
    return;
  }
  try {
    await API.reviews.submit({
      product_id: currentProduct.product_id,
      rating: selectedStarRating,
      title: document.getElementById('reviewTitleInput').value.trim() || undefined,
      review_text: document.getElementById('reviewTextInput').value.trim() || undefined,
    });
    showToast('Thanks! Your review has been submitted for approval.', 'success');
    closeReviewForm();
    // Re-fetch the product to pick up the updated aggregate rating
    const res = await API.products.get(productId);
    currentProduct = res.data;
    loadReviews(1);
  } catch (err) {
    showToast(err.message || 'Could not submit review.', 'error');
  }
}

/* ── Related products ── */
async function loadRelatedProducts() {
  const grid = document.getElementById('relatedProducts');
  if (!currentProduct?.category_id) { grid.innerHTML = ''; return; }
  try {
    const res = await API.products.list({ category_id: currentProduct.category_id, limit: 5 });
    const related = (res.data || []).filter(p => p.product_id !== currentProduct.product_id).slice(0, 4);
    grid.innerHTML = related.length
      ? related.map(buildProductCard).join('')
      : `<p style="color:var(--color-muted);grid-column:1/-1">No related products found.</p>`;
  } catch {
    grid.innerHTML = '';
  }
}

/* ── Accordion ── */
function toggleAccordion(trigger) {
  const body = trigger.nextElementSibling;
  const isOpen = body.classList.contains('open');
  document.querySelectorAll('.accordion-body.open').forEach(b => { b.classList.remove('open'); b.previousElementSibling.classList.remove('open'); });
  if (!isOpen) { body.classList.add('open'); trigger.classList.add('open'); }
}
</script>
<script src="/js/site-footer.js"></script>
</body>
</html>
