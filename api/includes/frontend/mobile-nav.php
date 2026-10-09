<nav class="mobile-nav" id="mobileNav" aria-label="Mobile navigation">
  <!-- Drawer Header -->
  <div class="mobile-nav-header">
    <a href="/" class="navbar-logo" onclick="closeMenu()">
      <img src="<?= $basePath ?>/images/logo.png" alt="Mithooos" style="height:40px;width:auto">
    </a>
    <button class="mobile-nav-close" onclick="closeMenu()" aria-label="Close menu">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 6 12 12M18 6 6 18"/></svg>
    </button>
  </div>

  <!-- Main Links -->
  <div class="mobile-nav-section">
    <a href="/" class="mobile-nav-link">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Home
    </a>
    <a href="<?= $basePath ?>/shop" class="mobile-nav-link">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      Shop All
    </a>
    <a href="<?= $basePath ?>/about" class="mobile-nav-link">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      About Us
    </a>
    <a href="<?= $basePath ?>/contact" class="mobile-nav-link">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.6 3.22a2 2 0 0 1 1.98-2.18H6a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21 16l.92.92z"/></svg>
      Contact
    </a>
    <a href="<?= $basePath ?>/blog" class="mobile-nav-link">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
      Blog
    </a>
  </div>

  <!-- Categories Section -->
  <div class="mobile-nav-divider-label">Categories</div>
  <div class="mobile-nav-section">
    <a href="<?= $basePath ?>/shop?cat=sindhi-ajrak" class="mobile-nav-link mobile-nav-link--sub">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M16 19a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M6 17V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10"/><path d="M6 9h12"/><path d="M6 13h12"/></svg>
      Ajrak
    </a>
    <a href="<?= $basePath ?>/shop?cat=sindhi-topi" class="mobile-nav-link mobile-nav-link--sub">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18h18"/><path d="M16 18v-8c0-2.2-1.8-4-4-4h0c-2.2 0-4 1.8-4 4v8"/></svg>
      Topi
    </a>
    <a href="<?= $basePath ?>/shop?cat=sindhi-kajoor" class="mobile-nav-link mobile-nav-link--sub">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22V12"/><path d="M12 12c-2.5 0-5-2-5-5a7 7 0 0 1 5 5z"/><path d="M12 12c2.5 0 5-2 5-5a7 7 0 0 0-5 5z"/><path d="M12 12c-3.5-3-6-4-6-7s2.5-4 6-1 6 2 6 7z"/></svg>
      Kajoor
    </a>
  </div>

  <!-- Account / Cart -->
  <div class="mobile-nav-divider-label">Account</div>
  <div class="mobile-nav-section">
    <a href="<?= $basePath ?>/account" class="mobile-nav-link">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      My Account
    </a>
    <a href="<?= $basePath ?>/cart" class="mobile-nav-link">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      Cart <span class="mobile-cart-badge" id="mobileCartBadge">0</span>
    </a>
  </div>
</nav>
<div class="overlay" id="navOverlay"></div>
