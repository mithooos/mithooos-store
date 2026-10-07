<!DOCTYPE html>
<html lang="en">
<?php include './includes/frontend/head.php'; ?>

<body>

<a href="#hero" class="skip-link">Skip to main content</a>


<!-- ════════════ NAVBAR ════════════ -->
<?php include './includes/frontend/navbar.php'; ?>


<?php include './includes/frontend/mobile-nav.php'; ?>


<!-- ════════════ HERO ════════════ -->
<section class="hero" id="hero" aria-label="Hero banner">
  <div class="hero-bg-shapes" aria-hidden="true">
    <div class="hero-shape hero-shape-1"></div>
    <div class="hero-shape hero-shape-2"></div>
    <div class="hero-shape hero-shape-3"></div>
  </div>
  <div class="container">
    <div class="hero-grid">
      <div class="hero-content">
        <div class="hero-eyebrow">
          <span aria-hidden="true"></span>
          THE MITHOOOS HERITAGE EDIT
        </div>
        <h1 class="hero-title">
          <span class="hero-title-line">Rooted in</span>
          <span class="hero-title-line text-gradient">Sindh.</span>
          <span class="hero-title-line">Made for Today.</span>
        </h1>
        <p class="hero-desc">
          Discover authentic Ajrak, traditional craftsmanship and timeless pieces thoughtfully curated for modern living.
        </p>
        <div class="hero-actions">
          <a href="pages/shop.php" class="btn btn-primary btn-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Shop the Collection
          </a>
          <a href="pages/shop.php?cat=sindhi-ajrak" class="btn btn-outline btn-lg">Explore Ajrak</a>
        </div>
        <div class="hero-signature"><span></span> Authentic Sindhi craft <span></span></div>
      </div>

      <div class="hero-image-wrapper">
        <div class="hero-image-card">
          <div class="hero-slides" aria-live="polite">
            <img class="hero-image hero-slide is-active" src="images/hero1.png" alt="Mithooos heritage campaign">
            <img class="hero-image hero-slide" src="images/hero2.png" alt="Mithooos heritage campaign" loading="lazy">
            <img class="hero-image hero-slide" src="images/hero3.png" alt="Mithooos heritage campaign" loading="lazy">
            <img class="hero-image hero-slide" src="images/hero4.png" alt="Mithooos heritage campaign" loading="lazy">
            <img class="hero-image hero-slide" src="images/hero5.png" alt="Mithooos heritage campaign" loading="lazy">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════ CATEGORIES ════════════ -->
<section class="section" aria-labelledby="categoriesHeading">
  <div class="container">
    <div class="section-header" data-reveal>
      <div>
        <span class="section-label">Browse by Category</span>
        <h2 id="categoriesHeading" class="section-title">Shop Your Style</h2>
      </div>
      <a href="pages/shop.php" class="btn btn-ghost">View All <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></a>
    </div>

    <div class="categories-grid" id="categoriesGrid" aria-live="polite">
      <a href="pages/shop.php?cat=sindhi-ajrak" class="category-card" data-reveal>
        <img src="images/hero1.png" class="category-card-image" alt="Sindhi Ajrak heritage collection" loading="lazy">
        <div class="category-card-overlay">
          <div>
            <div class="category-card-label">Sindhi Ajrak</div>
            <div class="category-card-count">Heritage textile</div>
          </div>
        </div>
      </a>
      <a href="pages/shop.php?cat=sindhi-topi" class="category-card" data-reveal style="transition-delay:.05s">
        <img src="images/hero2.png" class="category-card-image" alt="Sindhi Topi traditional collection" loading="lazy">
        <div class="category-card-overlay">
          <div>
            <div class="category-card-label">Sindhi Topi</div>
            <div class="category-card-count">Festive headwear</div>
          </div>
        </div>
      </a>
      <a href="pages/shop.php?cat=sindhi-kajoor" class="category-card" data-reveal style="transition-delay:.1s">
        <img src="images/hero3.png" class="category-card-image" alt="Sindhi Kajoor premium collection" loading="lazy">
        <div class="category-card-overlay">
          <div>
            <div class="category-card-label">Sindhi Kajoor</div>
            <div class="category-card-count">Premium gifting</div>
          </div>
        </div>
      </a>
      <a href="pages/shop.php?cat=sindhi-handicrafts" class="category-card" data-reveal style="transition-delay:.15s">
        <img src="images/hero4.png" class="category-card-image" alt="Sindhi handcrafted traditions collection" loading="lazy">
        <div class="category-card-overlay">
          <div>
            <div class="category-card-label">Sindhi Handicrafts</div>
            <div class="category-card-count">Crafted traditions</div>
          </div>
        </div>
      </a>
      <a href="pages/shop.php?cat=sindhi-gift-sets" class="category-card" data-reveal style="transition-delay:.2s">
        <img src="images/hero5.png" class="category-card-image" alt="Sindhi gift sets collection" loading="lazy">
        <div class="category-card-overlay">
          <div>
            <div class="category-card-label">Sindhi Gift Sets</div>
            <div class="category-card-count">Celebrate together</div>
          </div>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- ════════════ FEATURED PRODUCTS ════════════ -->
<section class="section section-alt" aria-labelledby="productsHeading">
  <div class="container">
    <div class="section-header" data-reveal>
      <div>
        <span class="section-label">Curated For You</span>
        <h2 id="productsHeading" class="section-title">Featured Products</h2>
      </div>
      <!-- Tabs -->
      <div class="tabs" role="tablist">
        <button class="tab-btn active" role="tab" aria-selected="true" data-tab="all">All</button>
        <button class="tab-btn" role="tab" aria-selected="false" data-tab="new">New In</button>
        <button class="tab-btn" role="tab" aria-selected="false" data-tab="trending">Trending</button>
        <button class="tab-btn" role="tab" aria-selected="false" data-tab="sale">Sale</button>
      </div>
    </div>

    <div class="grid-products" id="productsGrid">
      <!-- Populated dynamically from GET /api/products — see loadFeaturedProducts() below -->
    </div>

    <div style="text-align:center;margin-top:var(--space-12)" data-reveal>
      <a href="pages/shop.php" class="btn btn-outline btn-lg">
        Load More Products
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>
  </div>
</section>

<!-- ════════════ PROMO / COUNTDOWN ════════════ -->
<section class="promo-section">
  <div class="container">
    <div class="promo-card">
      <div class="promo-card-text" data-reveal="left">
        <span class="badge badge-sale" style="font-size:var(--text-sm);padding:var(--space-2) var(--space-4);margin-bottom:var(--space-4);"><svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3c1.5 3.5 5 4.5 5 9a5 5 0 1 1-10 0c0-2.5 1.6-4.4 3.2-6.2.3 2 1.3 3.1 2.3 3.7C13 7 12.6 5 12 3Z"/></svg> Limited Time Offer</span>
        <h2>Summer Sale<br><span class="text-gradient">Up to 60% Off</span></h2>
        <p>Don't miss out on our biggest sale of the year. Hundreds of styles reduced across all categories.</p>
        <a href="pages/shop.php?filter=sale" class="btn btn-primary btn-lg">Shop the Sale</a>
      </div>
      <div style="text-align:center" data-reveal="right">
        <p style="font-size:var(--text-sm);color:var(--color-muted);letter-spacing:2px;text-transform:uppercase;margin-bottom:var(--space-4);">Sale Ends In</p>
        <div class="countdown" id="countdown">
          <div class="countdown-item"><span class="countdown-num" id="cdDays">02</span><div class="countdown-label">Days</div></div>
          <span class="countdown-sep">:</span>
          <div class="countdown-item"><span class="countdown-num" id="cdHours">14</span><div class="countdown-label">Hours</div></div>
          <span class="countdown-sep">:</span>
          <div class="countdown-item"><span class="countdown-num" id="cdMins">38</span><div class="countdown-label">Mins</div></div>
          <span class="countdown-sep">:</span>
          <div class="countdown-item"><span class="countdown-num" id="cdSecs">00</span><div class="countdown-label">Secs</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════ TESTIMONIALS ════════════ -->
<section class="section" aria-labelledby="reviewsHeading">
  <div class="container">
    <div style="text-align:center;max-width:520px;margin:0 auto var(--space-12)" data-reveal>
      <span class="section-label">What They're Saying</span>
      <h2 id="reviewsHeading">Loved by Thousands</h2>
    </div>
    <div class="testimonials-grid" id="testimonialsGrid" aria-live="polite">
      <!-- Populated dynamically via JS -->
    </div>
  </div>
</section>

<!-- ════════════ NEWSLETTER ════════════ -->
<section class="newsletter-section" aria-labelledby="newsletterHeading">
  <div class="container">
    <div class="newsletter-inner" data-reveal>
      <span class="section-label" style="color:rgba(255,255,255,.6)">Stay in the Loop</span>
      <h2 id="newsletterHeading">Get 10% Off Your First Order</h2>
      <p>Subscribe to our newsletter for exclusive deals, new arrivals, and style inspiration delivered straight to your inbox.</p>
      <form class="newsletter-form" onsubmit="handleNewsletterSubmit(event)" aria-label="Newsletter signup">
        <input type="email" class="newsletter-input" placeholder="Enter your email address…" required aria-label="Email address">
        <button type="submit" class="btn btn-primary">Subscribe</button>
      </form>
      <p style="font-size:var(--text-xs);color:rgba(255,255,255,.4);margin-top:var(--space-4)">No spam, unsubscribe anytime. We respect your privacy.</p>
    </div>
  </div>
</section>

<!-- ════════════ FOOTER ════════════ -->
<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="index.php" aria-label="Mithooos Home">
          <img src="images/logo.png" alt="Mithooos logo" style="height:56px;width:auto;display:block;filter:drop-shadow(0 2px 4px rgba(0,0,0,.2))">
        </a>
        <p>Premium fashion for the modern individual. We believe great style shouldn't break the bank — quality and affordability together.</p>
        <div class="social-links" style="margin-top:var(--space-6); display:flex; gap:1.25rem;">
          <a href="#" class="social-link" aria-label="Instagram">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
          </a>
          <a href="#" class="social-link" aria-label="Facebook">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          </a>
          <a href="#" class="social-link" aria-label="Twitter/X">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
          </a>
          <a href="#" class="social-link" aria-label="Pinterest">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C6.48 2 2 6.48 2 12c0 4.24 2.65 7.86 6.39 9.29-.09-.78-.17-1.98.04-2.83.18-.77 1.22-5.17 1.22-5.17s-.31-.62-.31-1.55c0-1.45.84-2.54 1.89-2.54.89 0 1.32.67 1.32 1.48 0 .9-.57 2.25-.87 3.5-.25 1.04.52 1.89 1.54 1.89 1.85 0 3.28-1.95 3.28-4.77 0-2.49-1.79-4.24-4.34-4.24-2.96 0-4.69 2.22-4.69 4.51 0 .89.34 1.85.77 2.37.08.1.09.19.07.29-.08.32-.25 1.04-.28 1.18-.04.19-.15.23-.34.14-1.25-.58-2.03-2.42-2.03-3.89 0-3.15 2.29-6.05 6.61-6.05 3.47 0 6.16 2.47 6.16 5.77 0 3.44-2.17 6.21-5.18 6.21-1.01 0-1.97-.53-2.29-1.15l-.62 2.33c-.23.87-.84 1.96-1.25 2.62.94.29 1.94.45 2.97.45 5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
          </a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Shop</h4>
        <ul>
          <li><a href="pages/shop.php?cat=sindhi-ajrak">Sindhi Ajrak</a></li>
          <li><a href="pages/shop.php?cat=sindhi-topi">Sindhi Topi</a></li>
          <li><a href="pages/shop.php?cat=sindhi-kajoor">Sindhi Kajoor</a></li>
          <li><a href="pages/shop.php?cat=sindhi-handicrafts">Sindhi Handicrafts</a></li>
          <li><a href="pages/shop.php?filter=new">New Arrivals</a></li>
          <li><a href="pages/shop.php?filter=sale">Sale</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Help</h4>
        <ul>
          <li><a href="pages/account.php">My Account</a></li>
          <li><a href="pages/order-tracking.php">Order Tracking</a></li>
          <li><a href="#">Size Guide</a></li>
          <li><a href="#">Returns & Exchanges</a></li>
          <li><a href="#">Shipping Info</a></li>
          <li><a href="pages/contact.php">Contact Us</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Company</h4>
        <ul>
          <li><a href="pages/about.php">About Us</a></li>
          <li><a href="pages/blog.php">Blog</a></li>
          <li><a href="#">Sustainability</a></li>
          <li><a href="#">Careers</a></li>
          <li><a href="#">Affiliates</a></li>
        </ul>
        <div style="margin-top:var(--space-6)">
          <h4 style="margin-bottom:var(--space-3)">Payment Methods</h4>
          <div style="display:flex;gap:var(--space-3);flex-wrap:wrap">
            <span style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:var(--radius-sm);padding:6px 12px;font-size:.75rem;color:rgba(255,255,255,.8);font-weight:500;">Cash on Delivery</span>
            <span style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:var(--radius-sm);padding:6px 12px;font-size:.75rem;color:rgba(255,255,255,.8);font-weight:500;">Easypaisa</span>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 Mithooos. All rights reserved.</p>
      <div style="display:flex;gap:var(--space-6);font-size:var(--text-sm)">
        <a href="pages/privacy-policy.php" class="footer-legal-link">Privacy Policy</a>
        <a href="pages/terms-of-service.php" class="footer-legal-link">Terms of Service</a>
        <a href="pages/cookie-policy.php" class="footer-legal-link">Cookie Policy</a>
      </div>
    </div>
  </div>
</footer>

<!-- Toast container -->
<div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>


<script src="js/utils.js"></script>
<script src="js/api-client.js"></script>
<script src="js/cart.js"></script>
<script src="js/auth.js"></script>
<script src="js/search.js"></script>
<script>
  // ── Hero campaign slider ──
  const heroSlides = Array.from(document.querySelectorAll('.hero-slide'));
  if (heroSlides.length > 1) {
    let heroOrder = heroSlides.map((_, index) => index).sort(() => Math.random() - 0.5);
    let heroPosition = 0;
    let activeHeroIndex = heroOrder[heroPosition];
    heroSlides.forEach((slide, index) => slide.classList.toggle('is-active', index === activeHeroIndex));

    const showNextHero = () => {
      heroSlides[activeHeroIndex].classList.remove('is-active');
      heroPosition += 1;
      if (heroPosition >= heroOrder.length) {
        heroOrder = heroSlides.map((_, index) => index).sort(() => Math.random() - 0.5);
        heroPosition = 0;
      }
      activeHeroIndex = heroOrder[heroPosition];
      heroSlides[activeHeroIndex].classList.add('is-active');
    };

    window.setInterval(showNextHero, 4000);
  }

  // ── Categories — managed from admin CRUD ──
  const categoryGrid = document.getElementById('categoriesGrid');
  const categoryTextFallbacks = ['Heritage textile', 'Festive headwear', 'Premium gifting', 'Crafted traditions', 'Celebrate together'];
  const escapeCategory = value => String(value || '').replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char]));

  async function loadHomepageCategories() {
    if (!categoryGrid || typeof API === 'undefined' || !API.categories) return;
    try {
      const response = await API.categories.list();
      const rows = (response.data || []).slice(0, 5);
      if (!rows.length) return;
      const cards = Array.from(categoryGrid.querySelectorAll('.category-card'));
      rows.forEach((category, index) => {
        const card = cards[index];
        if (!card) return;
        card.href = `pages/shop.php?cat=${encodeURIComponent(category.slug)}`;
        const image = card.querySelector('img');
        const label = card.querySelector('.category-card-label');
        const count = card.querySelector('.category-card-count');
        if (image) {
          if (category.cover_image_url) {
            image.src = category.cover_image_url;
          }
          image.hidden = false;
          image.alt = `${category.category_name} collection`;
        }
        if (label) label.textContent = category.category_name || '';
        if (count) count.textContent = category.description || categoryTextFallbacks[index % categoryTextFallbacks.length];
      });
    } catch (error) {
      // Keep the server-rendered fallback cards visible if the API is unavailable.
      console.error('Error loading categories:', error);
    }
  }
  loadHomepageCategories();

  // ── Instantiate search autocomplete on nav input ──
  document.addEventListener('DOMContentLoaded', () => {
    const navInput = document.getElementById('navSearch');
    if (navInput && typeof SearchAutocomplete !== 'undefined') {
      new SearchAutocomplete(navInput, { minChars: 2, maxResults: 8, debounce: 280 });
    }
  });
  // ── Inline bootstrap for Phase 1 ──

  // Navbar scroll effect
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
  }, { passive: true });



  // ── Featured products — loaded from the real backend ──
  let wishlistedIds = new Set();

  async function loadFeaturedProducts(tab = 'all') {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    showSkeletons('productsGrid', 4);

    try {
      const p = new URLSearchParams({ limit: 4, sort: 'newest' });
      if (tab === 'new') p.set('is_new', '1');
      if (tab === 'sale') p.set('is_sale', '1');
      if (tab === 'trending') p.set('sort', 'popular');

      const res = await API.products.list(p.toString());
      const products = res.data || [];
      if (!products.length) {
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:var(--space-10);color:var(--color-muted)">No products found for this category.</div>`;
        return;
      }
      grid.innerHTML = products.map(buildProductCard).join('');
      applyWishlistState();
    } catch (err) {
      grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:var(--space-10);color:var(--color-error)">Could not load products. Please try again.</div>`;
    }
  }

  // ── Testimonials ──
  async function loadFeaturedTestimonials() {
    const grid = document.getElementById('testimonialsGrid');
    if (!grid) return;
    try {
      const res = await fetch(API_BASE + '/reviews?limit=3');
      const data = await res.json();
      const reviews = data.data || [];
      if (!reviews.length) return;
      
      grid.innerHTML = reviews.map((r, i) => `
        <div class="testimonial-card" data-reveal style="transition-delay:${i * 0.1}s">
          <div class="quote-icon">"</div>
          <p class="testimonial-quote">${escapeCategory(r.review_text || '')}</p>
          <div class="testimonial-meta" style="margin-top:var(--space-4)">
            <div class="testimonial-avatar img-placeholder" style="width:48px;height:48px;border-radius:50%;flex-shrink:0;"></div>
            <div>
              <div class="testimonial-name">${escapeCategory(r.first_name)} ${escapeCategory((r.last_name||'').charAt(0))}.</div>
              <div class="testimonial-role">${r.is_verified_purchase ? 'Verified Customer' : 'Customer'}</div>
              <div class="inline-rating" aria-label="${r.rating} out of 5 stars">
                ${'<svg viewBox="0 0 24 24" fill="currentColor"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>'.repeat(r.rating)}
              </div>
            </div>
          </div>
        </div>
      `).join('');
      initScrollReveal(); // re-init for newly added elements
    } catch (err) {
      console.error(err);
    }
  }

  // Mark cards whose product is already in the signed-in user's wishlist
  async function refreshWishlistState() {
    if (!API.auth.isLoggedIn()) {
      wishlistedIds = new Set();
      document.querySelectorAll('#wishlistCount').forEach(el => el.textContent = '0');
      return;
    }
    try {
      const res = await API.wishlist.list();
      wishlistedIds = new Set((res.data || []).map(i => i.product_id));
      document.querySelectorAll('#wishlistCount').forEach(el => el.textContent = wishlistedIds.size);
    } catch { /* non-fatal — leave badge at its current value */ }
  }

  function applyWishlistState() {
    document.querySelectorAll('#productsGrid .btn-wishlist').forEach(btn => {
      const card = btn.closest('[data-product-id]');
      const pid = card ? parseInt(card.dataset.productId, 10) : null;
      btn.classList.toggle('active', pid !== null && wishlistedIds.has(pid));
    });
  }

  // Product tabs — now actually filter, via the server
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('active'); b.setAttribute('aria-selected','false'); });
      btn.classList.add('active'); btn.setAttribute('aria-selected','true');
      loadFeaturedProducts(btn.dataset.tab);
    });
  });

  // Countdown timer — connected to backend promo_end_date
  async function setCountdown() {
    let endTs = 0;
    try {
        const res = await fetch(API_BASE + '/settings');
        const data = await res.json();
        if (data && data.success && data.data && data.data.promo_end_date) {
            endTs = new Date(data.data.promo_end_date).getTime();
        }
    } catch (e) {
        console.error('Failed to fetch promo end date', e);
    }
    
    // Fallback if no valid date from server
    if (!endTs || endTs < Date.now()) {
      endTs = Date.now() + (2 * 24 * 60 * 60 + 14 * 60 * 60) * 1000;
    }

    function tick() {
      const diff = endTs - Date.now();
      if (diff <= 0) {
        ['cdDays','cdHours','cdMins','cdSecs'].forEach(id => {
          const el = document.getElementById(id);
          if (el) el.textContent = '00';
        });
        return;
      }
      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const s = Math.floor((diff % 60000) / 1000);
      const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = String(val).padStart(2, '0'); };
      set('cdDays', d); set('cdHours', h); set('cdMins', m); set('cdSecs', s);
    }
    tick();
    setInterval(tick, 1000);
  }
  setCountdown();

  // Scroll reveal
  const revealEls = document.querySelectorAll('[data-reveal]');
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => { if(e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
  }, { threshold: .12 });
  revealEls.forEach(el => io.observe(el));

  // Toast: uses the shared showToast() from js/utils.js (loaded above) —
  // no page-specific copy needed.

  // ── Wishlist toggle ──
  // Uses the shared toggleWishlist(event, productId) from js/utils.js —
  // already wired onto each card's button by buildProductCard(), no
  // page-specific copy needed here.

  // Init: load featured products + wishlist state together
  document.addEventListener('DOMContentLoaded', async () => {
    await refreshWishlistState();
    loadFeaturedProducts('all');
    loadFeaturedTestimonials();
  });

  // Newsletter
  async function handleNewsletterSubmit(e) {
    e.preventDefault();
    const input = e.target.querySelector('input');
    const btn = e.target.querySelector('button, [type="submit"]');
    const email = input.value.trim();
    if (btn) btn.disabled = true;
    try {
      await API.newsletter.subscribe(email);
      showToast('Subscribed! 10% off code sent to your email.', 'success');
      input.value = '';
    } catch (err) {
      showToast(err.message || 'Could not subscribe. Please try again.', 'error');
    } finally {
      if (btn) btn.disabled = false;
    }
  }
</script>
</body>
</html>

