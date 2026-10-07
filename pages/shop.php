<!DOCTYPE html>
<html lang="en">
<?php include '../includes/frontend/head.php'; ?>
<body>

<!-- Navbar (same component) -->
<?php include '../includes/frontend/navbar.php'; ?>


<?php include '../includes/frontend/mobile-nav.php'; ?>


<!-- Page Hero -->
<section class="page-hero">
  <div class="container page-hero-inner">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="../index.php">Home</a><span class="breadcrumb-sep">/</span>
      <span>Shop</span>
    </nav>
    <h1>All Products</h1>
    <p style="color:var(--color-body);margin-top:var(--space-2)" id="heroSubtitle">Discover our complete collection of premium clothing</p>
  </div>
</section>

<div class="container">
  <div class="shop-layout">

    <!-- ── SIDEBAR FILTERS ── -->
    <aside class="filter-sidebar" id="filterSidebar">
      <div class="filter-header">
        <span class="filter-title">Filters</span>
        <button class="filter-clear" id="clearAllFilters">Clear All</button>
      </div>

      <!-- Category -->
      <div class="filter-group" id="fg-category">
        <div class="filter-group-title">
          Category
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="filter-group-body" id="categoryFilterBody">
          <!-- Populated dynamically from GET /api/categories — see loadCategories() below -->
        </div>
      </div>

      <!-- Collection -->
      <div class="filter-group" id="fg-collection">
        <div class="filter-group-title">
          Collection
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="filter-group-body" id="collectionFilterBody">
          <!-- Populated dynamically from GET /api/collections -->
        </div>
      </div>

      <!-- Price Range -->
      <div class="filter-group" id="fg-price">
        <div class="filter-group-title">
          Price Range
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="filter-group-body">
          <div class="price-inputs">
            <input type="number" class="price-input" id="priceMin" placeholder="PKR 0" min="0" max="500000" value="0">
            <span class="price-sep">—</span>
            <input type="number" class="price-input" id="priceMax" placeholder="PKR 500,000" min="0" max="500000" value="500000">
          </div>
          <input type="range" class="range-slider" id="priceRange" min="0" max="500000" value="500000">
        </div>
      </div>

      <!-- Rating -->
      <div class="filter-group" id="fg-rating">
        <div class="filter-group-title">
          Minimum Rating
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="filter-group-body">
          <div class="rating-options">
            <div class="rating-option" data-rating="4"><span class="rating-stars" data-stars="4" aria-label="4 out of 5 stars"></span><span style="font-size:.75rem;color:var(--color-muted)"> & up</span></div>
            <div class="rating-option" data-rating="3"><span class="rating-stars" data-stars="3" aria-label="3 out of 5 stars"></span><span style="font-size:.75rem;color:var(--color-muted)"> & up</span></div>
            <div class="rating-option" data-rating="2"><span class="rating-stars" data-stars="2" aria-label="2 out of 5 stars"></span><span style="font-size:.75rem;color:var(--color-muted)"> & up</span></div>
          </div>
        </div>
      </div>

      <!-- On Sale -->
      <div class="filter-group" style="border:none;padding-bottom:0">
        <label class="filter-option">
          <input type="checkbox" id="onSaleFilter"><span class="filter-checkbox"></span>
          <span class="filter-option-label" style="font-weight:600"><svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3c1.5 3.5 5 4.5 5 9a5 5 0 1 1-10 0c0-2.5 1.6-4.4 3.2-6.2.3 2 1.3 3.1 2.3 3.7C13 7 12.6 5 12 3Z"/></svg> On Sale Only</span>
        </label>
      </div>
    </aside>

    <!-- ── PRODUCTS AREA ── -->
    <div>
      <!-- Active filter tags -->
      <div class="active-filters" id="activeFilters"></div>

      <!-- Toolbar -->
      <div class="products-toolbar">
        <div>
          <button class="btn btn-outline btn-sm filter-toggle-btn" id="filterToggle" style="gap:.5rem">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
            Filters
          </button>
          <p class="products-count" style="display:inline;margin-left:var(--space-3)">Showing <strong id="productCount">0</strong> of <strong id="productTotal">0</strong> products</p>
        </div>
        <div style="display:flex;align-items:center;gap:var(--space-4)">
          <select class="sort-select" id="sortSelect" aria-label="Sort products">
            <option value="newest">Newest First</option>
            <option value="popular">Most Popular</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
            <option value="rating">Top Rated</option>
          </select>
          <div class="view-toggle" role="group" aria-label="View mode">
            <button class="view-btn active" id="viewGrid" aria-label="Grid view" aria-pressed="true">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><rect x="0" y="0" width="6" height="6" rx="1"/><rect x="10" y="0" width="6" height="6" rx="1"/><rect x="0" y="10" width="6" height="6" rx="1"/><rect x="10" y="10" width="6" height="6" rx="1"/></svg>
            </button>
            <button class="view-btn" id="viewList" aria-label="List view" aria-pressed="false">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><rect x="0" y="0" width="16" height="4" rx="1"/><rect x="0" y="6" width="16" height="4" rx="1"/><rect x="0" y="12" width="16" height="4" rx="1"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="products-grid-3" id="productsGrid" data-view="grid">
        <!-- Populated dynamically from GET /api/products — see loadProducts() below -->
      </div><!-- /grid -->

      <!-- Empty state (no products match current filters) -->
      <div id="shopEmptyState" style="display:none;text-align:center;padding:var(--space-20) var(--space-4)">
        <div style="width:80px;height:80px;background:var(--color-surface-alt);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-6);color:var(--color-muted);">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
        <h3 style="font-size:var(--text-xl);color:var(--color-ink);margin-bottom:var(--space-3)">No products found</h3>
        <p style="color:var(--color-muted);margin-bottom:var(--space-8)">Try adjusting your filters or search terms.</p>
        <a href="shop.php" class="btn btn-outline">Clear Filters</a>
      </div>

      <!-- Pagination -->
      <nav class="pagination" aria-label="Product pages" id="paginationNav">
        <!-- Populated dynamically by renderPagination() based on the API's pagination metadata -->
      </nav>
    </div><!-- /products area -->

  </div><!-- /shop layout -->
</div>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<script src="../js/utils.js"></script>
<script src="../js/api-client.js"></script>
<script src="../js/cart.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/search.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════
   SHOP — Wired to the real backend
   (GET /api/products, GET /api/categories)
   ══════════════════════════════════════════ */

/* ── Navbar scroll ── */
const nb = document.getElementById('navbar');
if (nb) window.addEventListener('scroll', () => nb.classList.toggle('scrolled', scrollY > 20), { passive: true });

/* ── Filter/sort/pagination state ──
  Categories and price are fetched from the real product API. */
let state = {
  categoryId: null,   // resolved from the checked category checkbox's slug
  collectionId: null, // resolved from the checked collection checkbox's id
  minPrice: 0,
  maxPrice: 500000,
  minRating: 0,        // applied client-side, since the API doesn't support it
  onSale: false,
  sort: 'newest',
  page: 1,
  search: null,
};

let categorySlugToId = {};   // populated by loadCategories()
let collectionNameToId = {}; // populated by loadCollections()
let lastFetchedRows = [];    // last raw API rows, before client-side rating filter

/* ── Load categories into the sidebar + build slug -> id lookup ── */
async function loadCategories() {
  const body = document.getElementById('categoryFilterBody');
  try {
    const res = await API.categories.list();
    const cats = res.data || [];
    // Only show top-level categories (no parent) in this simple sidebar —
    // matches what the original hardcoded markup showed.
    const topLevel = cats.filter(c => !c.parent_category_id);

    topLevel.forEach(c => { categorySlugToId[c.slug] = c.category_id; });

    if (body) {
      body.innerHTML = topLevel.map(c => `
        <label class="filter-option">
          <input type="checkbox" name="cat" value="${escapeHtml(c.slug)}">
          <span class="filter-checkbox"></span>
          <span class="filter-option-label">${escapeHtml(c.category_name)}</span>
        </label>`).join('');

      // Wire the newly-created checkboxes
      body.querySelectorAll('input[name="cat"]').forEach(cb => {
        cb.addEventListener('change', () => {
          const checked = Array.from(body.querySelectorAll('input[name="cat"]:checked')).map(c => c.value);
          // Backend only supports a single category_id filter — use the first checked
          state.categoryId = checked.length ? categorySlugToId[checked[0]] : null;
          state.page = 1;
          loadProducts();
        });
      });
    }

    // Apply ?cat= from the URL now that we have the slug -> id map
    const params = new URLSearchParams(location.search);
    const cat = params.get('cat');
    if (cat && categorySlugToId[cat]) {
      const cb = body?.querySelector(`input[name="cat"][value="${cat}"]`);
      if (cb) cb.checked = true;
      state.categoryId = categorySlugToId[cat];
      const hero = document.querySelector('.page-hero h1');
      if (hero) hero.textContent = cat.charAt(0).toUpperCase() + cat.slice(1) + "'s Collection";
    }
  } catch (err) {
    if (body) body.innerHTML = `<p style="font-size:var(--text-xs);color:var(--color-muted)">Could not load categories.</p>`;
  }
}

/* ── Fetch products from the real API and render ── */
async function loadProducts() {
  const grid = document.getElementById('productsGrid');
  if (!grid) return;

  showSkeletons('productsGrid', 6);
  document.getElementById('shopEmptyState').style.display = 'none';

  const params = { page: state.page, limit: 12, sort: state.sort };
  if (state.categoryId) params.category_id = state.categoryId;
  if (state.collectionId) params.collection_id = state.collectionId;
  if (state.minPrice > 0)   params.min_price = state.minPrice;
  if (state.maxPrice < 500000) params.max_price = state.maxPrice;
  if (state.onSale)     params.is_sale = 1;
  if (state.search)     params.search = state.search;

  try {
    const res = await API.products.list(params);
    let rows = res.data || [];
    lastFetchedRows = rows;

    // Rating filter is applied client-side (within this page of results only)
    // since Product::list() has no min_rating parameter.
    if (state.minRating > 0) {
      rows = rows.filter(p => (parseFloat(p.rating) || 0) >= state.minRating);
    }

    renderProducts(rows, res.pagination);
    updateActiveFilterTags();
  } catch (err) {
    grid.innerHTML = '';
    showToast(err.message || 'Could not load products. Please try again.', 'error');
    document.getElementById('shopEmptyState').style.display = 'block';
  }
}

function renderProducts(rows, pagination) {
  const grid = document.getElementById('productsGrid');
  const empty = document.getElementById('shopEmptyState');

  if (!rows.length) {
    grid.innerHTML = '';
    empty.style.display = 'block';
  } else {
    empty.style.display = 'none';
    grid.innerHTML = rows.map(buildProductCard).join('');
  }

  const countEl = document.getElementById('productCount');
  const totalEl = document.getElementById('productTotal');
  if (countEl) countEl.textContent = rows.length;
  if (totalEl && pagination) totalEl.textContent = pagination.total;

  if (pagination) renderPagination(pagination);
}

/* ── Pagination ── */
function renderPagination(pagination) {
  const nav = document.getElementById('paginationNav');
  if (!nav) return;
  const { current_page, last_page } = pagination;
  if (last_page <= 1) { nav.innerHTML = ''; return; }

  const pageBtn = (n, label = n, extra = '') =>
    `<button class="page-btn ${n === current_page ? 'active' : ''}" ${n === current_page ? 'aria-current="page"' : ''} data-page="${n}" ${extra}>${label}</button>`;

  let html = '';
  html += `<button class="page-btn" aria-label="Previous" data-page="${current_page - 1}" ${current_page <= 1 ? 'disabled' : ''}>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
  </button>`;

  // Simple windowed pagination: first, current-1..current+1, last
  const pages = new Set([1, last_page, current_page - 1, current_page, current_page + 1]);
  const sorted = Array.from(pages).filter(p => p >= 1 && p <= last_page).sort((a, b) => a - b);

  let prev = 0;
  sorted.forEach(p => {
    if (prev && p - prev > 1) html += `<span style="color:var(--color-muted);padding:0 .5rem">…</span>`;
    html += pageBtn(p);
    prev = p;
  });

  html += `<button class="page-btn" aria-label="Next" data-page="${current_page + 1}" ${current_page >= last_page ? 'disabled' : ''}>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
  </button>`;

  nav.innerHTML = html;

  nav.querySelectorAll('.page-btn:not([disabled])').forEach(btn => {
    btn.addEventListener('click', () => {
      const page = parseInt(btn.dataset.page, 10);
      if (!page || page < 1) return;
      state.page = page;
      loadProducts();
      document.getElementById('productsGrid')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });
}

/* ── Active filter tags ── */
function updateActiveFilterTags() {
  const container = document.getElementById('activeFilters');
  if (!container) return;
  container.innerHTML = '';

  const addTag = (label, removeFn) => {
    const tag = document.createElement('div');
    tag.className = 'active-filter-tag';
    const span = document.createElement('span');
    span.textContent = label;
    const btn = document.createElement('button');
    btn.textContent = '×';
    btn.setAttribute('aria-label', 'Remove ' + label);
    btn.addEventListener('click', () => { removeFn(); state.page = 1; loadProducts(); });
    tag.appendChild(span);
    tag.appendChild(btn);
    container.appendChild(tag);
  };

  if (state.categoryId) {
    const slug = Object.keys(categorySlugToId).find(s => categorySlugToId[s] === state.categoryId);
    const cb = slug && document.querySelector(`input[name="cat"][value="${slug}"]`);
    const label = cb ? cb.closest('.filter-option').querySelector('.filter-option-label').textContent : 'Category';
    addTag(label, () => {
      state.categoryId = null;
      document.querySelectorAll('input[name="cat"]').forEach(c => c.checked = false);
    });
  }
  if (state.collectionId) {
    const name = Object.keys(collectionNameToId).find(n => collectionNameToId[n] === state.collectionId);
    const cb = name && document.querySelector(`input[name="col"][value="${name}"]`);
    const label = cb ? cb.closest('.filter-option').querySelector('.filter-option-label').textContent : 'Collection';
    addTag(label, () => {
      state.collectionId = null;
      document.querySelectorAll('input[name="col"]').forEach(c => c.checked = false);
    });
  }
  if (state.minRating > 0) {
    addTag(state.minRating + ' star rating & up', () => {
      state.minRating = 0;
      document.querySelectorAll('.rating-option.active').forEach(o => o.classList.remove('active'));
    });
  }
  if (state.onSale) {
    addTag('On Sale', () => {
      state.onSale = false;
      const el = document.getElementById('onSaleFilter');
      if (el) el.checked = false;
    });
  }
  if (state.maxPrice < 500000) {
    addTag('Under ' + formatPrice(state.maxPrice), () => {
      state.maxPrice = 500000;
      const r = document.getElementById('priceRange'); if (r) r.value = 500000;
      const m = document.getElementById('priceMax');   if (m) m.value = 500000;
    });
  }
}

/* ── Wire: filter group collapse ── */
document.querySelectorAll('.filter-group-title').forEach(t => {
  t.addEventListener('click', () => t.closest('.filter-group').classList.toggle('collapsed'));
});

/* ── Wire: price range ── */
const priceRange = document.getElementById('priceRange');
const priceMaxEl = document.getElementById('priceMax');
const priceMinEl = document.getElementById('priceMin');
function updatePriceRangeFill() {
  if (!priceRange) return;
  const min = parseFloat(priceRange.min) || 0;
  const max = parseFloat(priceRange.max) || 1;
  const value = parseFloat(priceRange.value) || min;
  priceRange.style.setProperty('--range-progress', `${((value - min) / (max - min)) * 100}%`);
}
updatePriceRangeFill();

priceRange?.addEventListener('change', () => {
  state.maxPrice = parseInt(priceRange.value);
  if (priceMaxEl) priceMaxEl.value = priceRange.value;
  state.page = 1;
  loadProducts();
});
priceRange?.addEventListener('input', () => {
  if (priceMaxEl) priceMaxEl.value = priceRange.value;
  updatePriceRangeFill();
});

priceMaxEl?.addEventListener('change', () => {
  state.maxPrice = parseInt(priceMaxEl.value) || 500000;
  if (priceRange) priceRange.value = state.maxPrice;
  state.page = 1;
  loadProducts();
});

priceMinEl?.addEventListener('change', () => {
  state.minPrice = parseInt(priceMinEl.value) || 0;
  state.page = 1;
  loadProducts();
});

/* ── Wire: rating (client-side filter within the current page) ── */
document.querySelectorAll('.rating-option').forEach(opt => {
  opt.addEventListener('click', () => {
    const wasActive = opt.classList.contains('active');
    document.querySelectorAll('.rating-option').forEach(o => o.classList.remove('active'));
    if (!wasActive) { opt.classList.add('active'); state.minRating = parseInt(opt.dataset.rating) || 0; }
    else { state.minRating = 0; }
    // Re-filter the already-fetched rows rather than re-fetching, since
    // rating isn't a server-side filter
    const filtered = state.minRating > 0
      ? lastFetchedRows.filter(p => (parseFloat(p.rating) || 0) >= state.minRating)
      : lastFetchedRows;
    renderProducts(filtered, null);
    updateActiveFilterTags();
  });
});

/* ── Wire: on sale toggle ── */
document.getElementById('onSaleFilter')?.addEventListener('change', function () {
  state.onSale = this.checked;
  state.page = 1;
  loadProducts();
});

/* ── Wire: clear all ── */
document.getElementById('clearAllFilters')?.addEventListener('click', () => {
  state = { categoryId: null, collectionId: null, minPrice: 0, maxPrice: 500000, minRating: 0, onSale: false, sort: state.sort, page: 1 };
  document.querySelectorAll('.rating-option.active').forEach(o => o.classList.remove('active'));
  document.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = false);
  if (priceRange)  priceRange.value  = 500000;
  if (priceMaxEl)  priceMaxEl.value  = 500000;
  if (priceMinEl)  priceMinEl.value  = 0;
  updatePriceRangeFill();
  loadProducts();
});

/* ── Wire: sort ── */
document.getElementById('sortSelect')?.addEventListener('change', function () {
  state.sort = this.value;
  state.page = 1;
  loadProducts();
});

/* ── Wire: view toggle (grid / list) ── */
document.getElementById('viewGrid')?.addEventListener('click', () => {
  const grid = document.getElementById('productsGrid');
  grid.style.gridTemplateColumns = '';
  grid.dataset.view = 'grid';
  document.getElementById('viewGrid').classList.add('active');
  document.getElementById('viewList')?.classList.remove('active');
});

document.getElementById('viewList')?.addEventListener('click', () => {
  const grid = document.getElementById('productsGrid');
  grid.style.gridTemplateColumns = '1fr';
  grid.dataset.view = 'list';
  document.getElementById('viewList').classList.add('active');
  document.getElementById('viewGrid')?.classList.remove('active');
});

/* ── Wire: mobile filter drawer ── */
document.getElementById('filterToggle')?.addEventListener('click', () => {
  document.getElementById('filterSidebar').classList.toggle('open');
});

/* ── Handle URL params + initial load ── */
document.addEventListener('DOMContentLoaded', async () => {
  document.querySelectorAll('.rating-stars[data-stars]').forEach(el => { el.innerHTML = renderStars(el.dataset.stars, 14); });
  const params = new URLSearchParams(location.search);

  const filter = params.get('filter');
  const search = params.get('search');
  const sort   = params.get('sort');

  if (filter === 'sale') {
    state.onSale = true;
    const cb = document.getElementById('onSaleFilter');
    if (cb) cb.checked = true;
  }
  if (filter === 'new') {
    state.sort = 'newest';
    const sel = document.getElementById('sortSelect');
    if (sel) sel.value = 'newest';
  }
  if (sort) {
    state.sort = sort;
    const sel = document.getElementById('sortSelect');
    if (sel) sel.value = sort;
  }
  if (search) {
    state.search = search;
    const hero = document.querySelector('.page-hero h1');
    if (hero) hero.textContent = `Results for "${search}"`;
  }

  await loadCategories(); // must resolve before loadProducts() so ?cat= applies
  await loadCollections();
  await loadProducts();
});

async function loadCollections() {
  const body = document.getElementById('collectionFilterBody');
  try {
    const res = await API.collections.list();
    const cols = res.data || [];

    cols.forEach(c => { collectionNameToId[c.collection_name] = c.collection_id; });

    if (body) {
      body.innerHTML = cols.map(c => `
        <label class="filter-option">
          <input type="checkbox" name="col" value="${escapeHtml(c.collection_name)}">
          <span class="filter-checkbox"></span>
          <span class="filter-option-label">${escapeHtml(c.collection_name)}</span>
        </label>`).join('');

      // Wire the newly-created checkboxes
      body.querySelectorAll('input[name="col"]').forEach(cb => {
        cb.addEventListener('change', () => {
          const checked = Array.from(body.querySelectorAll('input[name="col"]:checked')).map(c => c.value);
          // Backend only supports a single collection_id filter
          state.collectionId = checked.length ? collectionNameToId[checked[0]] : null;
          state.page = 1;
          loadProducts();
        });
      });
    }
  } catch (err) {
    if (body) body.innerHTML = `<p style="font-size:var(--text-xs);color:var(--color-muted)">Could not load collections.</p>`;
  }
}
</script>
<script src="../js/site-footer.js"></script>
</body>
</html>
