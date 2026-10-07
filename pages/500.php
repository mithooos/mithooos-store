<!DOCTYPE html>
<html lang="en">
<?php include '../includes/frontend/head.php'; ?>
<body>

<?php include '../includes/frontend/navbar.php'; ?>
<?php include '../includes/frontend/mobile-nav.php'; ?>

<main class="container" style="padding:var(--space-20) 0; text-align:center">
  <div style="max-width:500px; margin:0 auto">
    <div style="font-size:120px; font-weight:800; line-height:1; color:#DC2626; opacity:0.1; margin-bottom:-40px; position:relative; z-index:-1">500</div>
    <h1 style="font-size:var(--text-4xl); margin-bottom:var(--space-4)">Internal Server Error</h1>
    <p style="color:var(--color-muted); font-size:var(--text-lg); margin-bottom:var(--space-8)">
      Sorry, something went wrong on our end. We're actively working to fix the issue. Please try again in a few minutes.
    </p>
    <div style="display:flex; gap:var(--space-4); justify-content:center">
      <a href="../index.php" class="btn btn-primary">Return Home</a>
      <button onclick="location.reload()" class="btn btn-outline">Refresh Page</button>
    </div>
  </div>
</main>

<script src="../js/utils.js"></script>
<script src="../js/api-client.js"></script>
<script src="../js/cart.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/site-footer.js"></script>
</body>
</html>
