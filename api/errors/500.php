<?php
http_response_code(500);
$basePath = '';
?><!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/frontend/navbar.php'; ?>
<?php include __DIR__ . '/../includes/frontend/mobile-nav.php'; ?>

<section class="section" style="min-height: 72vh; display:flex; align-items:center; justify-content:center; padding: 4rem 1rem;">
  <div class="container" style="max-width: 760px; text-align:center;">
    <div style="display:inline-flex; align-items:center; gap:.75rem; padding:.55rem 1rem; border-radius:999px; background:rgba(255,166,43,.12); color:#a85c00; font-weight:700; letter-spacing:.08em; text-transform:uppercase; font-size:.72rem; margin-bottom:1.5rem;">
      <span style="width:9px; height:9px; border-radius:50%; background:#f59e0b; display:inline-block;"></span>
      Server Error
    </div>
    <div style="font-size: clamp(4rem, 8vw, 7rem); line-height:1; font-weight:800; letter-spacing:-.06em; background:linear-gradient(135deg,#1d1f2e,#f59e0b); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:1rem;">500</div>
    <h1 style="font-size:clamp(2rem,4vw,3rem); margin:0 0 1rem; font-family:var(--font-heading, 'Poppins', sans-serif); color:#1b1d2a;">Something went wrong on our end.</h1>
    <p style="margin:0 auto 2rem; max-width:560px; color:#5f647c; font-size:1.05rem; line-height:1.7;">
      We’re working to fix this issue. Please refresh the page or return to the homepage and try again.
    </p>
    <div style="display:flex; justify-content:center; gap:1rem; flex-wrap:wrap;">
      <a href="/" class="btn btn-primary btn-lg">Back to Home</a>
      <button type="button" class="btn btn-ghost btn-lg" onclick="window.location.reload()">Refresh Page</button>
    </div>
  </div>
</section>

<script src="/js/utils.js"></script>
<script src="/js/api-client.js"></script>
<script src="/js/cart.js"></script>
<script src="/js/auth.js"></script>
<script src="/js/search.js"></script>
<script src="/js/site-footer.js"></script>
</body>
</html>
