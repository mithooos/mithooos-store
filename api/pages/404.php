<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>

<?php include __DIR__ . '/../includes/frontend/navbar.php'; ?>
<?php include __DIR__ . '/../includes/frontend/mobile-nav.php'; ?>

<main class="container" style="padding:var(--space-20) 0; text-align:center">
  <div style="max-width:500px; margin:0 auto">
    <div style="font-size:120px; font-weight:800; line-height:1; color:var(--color-primary); opacity:0.1; margin-bottom:-40px; position:relative; z-index:-1">404</div>
    <h1 style="font-size:var(--text-4xl); margin-bottom:var(--space-4)">Page Not Found</h1>
    <p style="color:var(--color-muted); font-size:var(--text-lg); margin-bottom:var(--space-8)">
      Oops! The page you are looking for doesn't exist, has been removed, or is temporarily unavailable.
    </p>
    <div style="display:flex; gap:var(--space-4); justify-content:center">
      <a href="/" class="btn btn-primary">Go to Homepage</a>
      <a href="/shop" class="btn btn-outline">Browse Shop</a>
    </div>
  </div>
</main>

<script src="/js/utils.js"></script>
<script src="/js/api-client.js"></script>
<script src="/js/cart.js"></script>
<script src="/js/auth.js"></script>
<script src="/js/site-footer.js"></script>
</body>
</html>
