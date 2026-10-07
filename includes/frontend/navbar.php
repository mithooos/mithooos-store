<nav class="navbar scrolled" id="navbar">
  <div class="container navbar-inner">
    <a href="<?= $basePath ?>/index.php" class="navbar-logo">
      <img src="<?= $basePath ?>/images/logo.png" alt="Mithooos logo" style="height:40px;width:auto;display:block;">
    </a>
<?php $currentScript = basename($_SERVER['SCRIPT_NAME']); ?>
    <ul class="navbar-nav">
      <li><a href="<?= $basePath ?>/index.php" class="nav-link <?= ($currentScript == 'index.php') ? 'active' : '' ?>">Home</a></li>
      <li><a href="<?= $basePath ?>/pages/shop.php" class="nav-link <?= ($currentScript == 'shop.php') ? 'active' : '' ?>">Shop</a></li>
      <li><a href="<?= $basePath ?>/pages/about.php" class="nav-link <?= ($currentScript == 'about.php') ? 'active' : '' ?>">About</a></li>
      <li><a href="<?= $basePath ?>/pages/contact.php" class="nav-link <?= ($currentScript == 'contact.php') ? 'active' : '' ?>">Contact</a></li>
    </ul>
    <div class="navbar-actions" style="margin-left:auto">
      <div class="nav-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="search" placeholder="Search..." aria-label="Search products">
      </div>
      <a href="<?= $basePath ?>/pages/account.php" class="nav-icon-btn" aria-label="Account">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </a>
      <a href="<?= $basePath ?>/pages/cart.php" class="nav-icon-btn" aria-label="Cart">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="nav-badge" id="cartCountBadge">0</span>
      </a>
      <button class="nav-icon-btn menu-toggle" onclick="openMenu()" aria-label="Open menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
  </div>
</nav>
