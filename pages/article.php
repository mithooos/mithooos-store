<!DOCTYPE html>
<html lang="en">
<?php include '../includes/frontend/head.php'; ?>
<body>

<?php include '../includes/frontend/navbar.php'; ?>
<?php include '../includes/frontend/mobile-nav.php'; ?>

<main class="container" style="max-width:800px; padding-top:var(--space-12); padding-bottom:var(--space-20)">
  
  <nav class="breadcrumb" style="margin-bottom:var(--space-6)">
    <a href="../index.php">Home</a><span class="breadcrumb-sep">/</span>
    <a href="blog.php">Style Blog</a><span class="breadcrumb-sep">/</span>
    <span id="breadcrumbTitle">Loading...</span>
  </nav>

  <div id="articleContent">
    <div style="text-align:center; padding:var(--space-12)">
      <div class="spinner"></div>
      <p style="margin-top:var(--space-4); color:var(--color-muted)">Loading article...</p>
    </div>
  </div>

  <div id="articleError" style="display:none; text-align:center; padding:var(--space-12)">
    <h2 style="color:var(--color-error); margin-bottom:var(--space-4)">Article Not Found</h2>
    <p style="color:var(--color-muted); margin-bottom:var(--space-6)">The article you are looking for does not exist or has been removed.</p>
    <a href="blog.php" class="btn btn-primary">Back to Blog</a>
  </div>

</main>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<script src="../js/utils.js"></script>
<script src="../js/api-client.js"></script>
<script src="../js/cart.js"></script>
<script src="../js/auth.js"></script>
<script>
async function loadArticle() {
  const params = new URLSearchParams(window.location.search);
  const slug = params.get('slug') || params.get('id');
  
  if (!slug) {
      document.getElementById('articleContent').style.display = 'none';
      document.getElementById('articleError').style.display = 'block';
      return;
  }

  try {
    const res = await fetch(API_BASE + '/blog/' + encodeURIComponent(slug));
    const data = await res.json();
    
    if (data.success && data.data) {
      const p = data.data;
      document.title = p.title + ' - Mithooos Blog';
      document.getElementById('breadcrumbTitle').textContent = escapeHtml(p.title);
      
      const letters = ((p.first_name||'').charAt(0) + (p.last_name||'').charAt(0)).toUpperCase() || 'A';
      
      let html = `
        <div style="text-align:center; margin-bottom:var(--space-8)">
          <span class="blog-cat-tag" style="margin:0 auto var(--space-4)">${escapeHtml(p.category || 'News')}</span>
          <h1 style="font-size:3rem; line-height:1.2; margin-bottom:var(--space-6)">${escapeHtml(p.title)}</h1>
          
          <div style="display:flex; align-items:center; justify-content:center; gap:var(--space-3)">
            <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);display:flex;align-items:center;justify-content:center;color:var(--color-primary);font-weight:bold">${letters}</div>
            <div style="text-align:left">
              <p style="font-weight:600; font-size:var(--text-sm)">${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)}</p>
              <p style="color:var(--color-muted); font-size:var(--text-xs)">${formatDate(p.published_at || p.created_at)} · ${Math.max(1, Math.ceil((p.content?.length || 0)/1000))} min read</p>
            </div>
          </div>
        </div>
      `;
      
      if (p.featured_image_url) {
          html += `
            <div style="width:100%; height:400px; border-radius:var(--radius-xl); overflow:hidden; margin-bottom:var(--space-10)">
              <img src="${p.featured_image_url}" style="width:100%; height:100%; object-fit:cover">
            </div>
          `;
      }
      
      html += `
        <div class="prose" style="font-size:1.1rem; line-height:1.8; color:var(--color-ink)">
          ${p.content}
        </div>
        
        <div style="margin-top:var(--space-12); padding-top:var(--space-8); border-top:1px solid var(--color-border-soft); display:flex; justify-content:space-between; align-items:center">
          <div>
            <p style="font-weight:600; margin-bottom:var(--space-2)">Share this article</p>
            <div style="display:flex; gap:var(--space-2)">
              <button class="btn btn-outline btn-sm" onclick="navigator.clipboard.writeText(window.location.href); showToast('Link copied to clipboard', 'success')">Copy Link</button>
            </div>
          </div>
          <a href="blog.php" class="btn btn-primary">Back to Blog</a>
        </div>
      `;
      
      document.getElementById('articleContent').innerHTML = html;
    } else {
      throw new Error('Not found');
    }
  } catch (err) {
    document.getElementById('articleContent').style.display = 'none';
    document.getElementById('articleError').style.display = 'block';
  }
}

document.addEventListener('DOMContentLoaded', loadArticle);
</script>

<script src="../js/site-footer.js"></script>
</body>
</html>
