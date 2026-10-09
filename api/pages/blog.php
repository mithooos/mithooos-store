<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>

<?php include __DIR__ . '/../includes/frontend/navbar.php'; ?>


<?php include __DIR__ . '/../includes/frontend/mobile-nav.php'; ?>


<section class="page-hero">
  <div class="container page-hero-inner">
    <nav class="breadcrumb"><a href="/">Home</a><span class="breadcrumb-sep">/</span><span>Style Blog</span></nav>
    <h1>Style Stories</h1>
    <p style="color:var(--color-body);margin-top:var(--space-2)">Fashion tips, trend guides, and inspiration for every wardrobe.</p>
  </div>
</section>

<div class="container" style="padding-bottom:var(--space-20)">

  <!-- Featured post -->
  <div class="blog-featured" data-reveal>
    <div class="blog-featured-img">
      <div style="text-align:center;padding:var(--space-8)">
        <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-4);animation:float 4s ease-in-out infinite">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <p style="color:rgba(255,255,255,.7);font-size:var(--text-sm)">Featured Article</p>
      </div>
    </div>
    <div class="blog-featured-body">
      <span class="blog-cat-tag" style="background:rgba(255,107,53,.2);color:#FF6B35;border:1px solid rgba(255,107,53,.3)">Style Guide</span>
      <h2 style="color:white;font-size:var(--text-3xl);line-height:1.3;margin:var(--space-3) 0">10 Summer Outfits That Will Turn Heads Everywhere You Go</h2>
      <p style="color:rgba(255,255,255,.65);font-size:var(--text-sm);line-height:1.7;margin-bottom:var(--space-5)">From beach-ready looks to evening elegance, we've curated the ultimate summer style guide to keep you looking effortlessly chic all season long.</p>
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-3)">
        <div style="display:flex;align-items:center;gap:var(--space-3)">
          <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#FF6B35,#FF3D7F);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.7rem">A</div>
          <div><p style="color:white;font-size:var(--text-xs);font-weight:600">Alex Rivera</p><p style="color:rgba(255,255,255,.4);font-size:.65rem">Apr 8, 2026 · 8 min read</p></div>
        </div>
        <a href="#" class="btn btn-primary btn-sm">Read Article</a>
      </div>
    </div>
  </div>

  <!-- Blog grid + sidebar -->
  <div class="blog-layout">
    <div class="blog-grid">

      <!-- Post 1 -->
      <article class="blog-card" data-reveal>
        <div class="blog-card-img"><div class="img-placeholder" style="width:100%;height:100%;background:linear-gradient(135deg,#FEF3C7,#FDE68A);display:flex;align-items:center;justify-content:center;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#92400E" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div></div>
        <div class="blog-card-body">
          <span class="blog-cat-tag">Trend Report</span>
          <a href="#" class="blog-card-title">The Return of Denim: How to Wear the Fabric of the Season</a>
          <p class="blog-card-excerpt">Denim is having its biggest comeback in years. From double-denim to contrast stitching, here's everything you need to know about rocking this endlessly versatile fabric in 2026.</p>
          <div class="blog-meta">
            <div class="blog-author-avatar img-placeholder" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE)"></div>
            <div><p class="blog-author-name">Jordan Lee</p><p class="blog-date">Apr 5, 2026</p></div>
            <span class="blog-read-time">5 min read</span>
          </div>
        </div>
      </article>

      <!-- Post 2 -->
      <article class="blog-card" data-reveal style="transition-delay:.08s">
        <div class="blog-card-img"><div class="img-placeholder" style="width:100%;height:100%;background:linear-gradient(135deg,#FCE7F3,#FBCFE8);display:flex;align-items:center;justify-content:center;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9D174D" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div></div>
        <div class="blog-card-body">
          <span class="blog-cat-tag">Sustainability</span>
          <a href="#" class="blog-card-title">Why Slow Fashion Is the Future (And How to Shop It)</a>
          <p class="blog-card-excerpt">Fast fashion's environmental cost is becoming impossible to ignore. We break down exactly how to build a sustainable wardrobe without sacrificing style or spending a fortune.</p>
          <div class="blog-meta">
            <div class="blog-author-avatar img-placeholder" style="background:linear-gradient(135deg,#D1FAE5,#A7F3D0)"></div>
            <div><p class="blog-author-name">Maya Patel</p><p class="blog-date">Apr 1, 2026</p></div>
            <span class="blog-read-time">7 min read</span>
          </div>
        </div>
      </article>

      <!-- Post 3 -->
      <article class="blog-card" data-reveal style="transition-delay:.16s">
        <div class="blog-card-img"><div class="img-placeholder" style="width:100%;height:100%;background:linear-gradient(135deg,#EDE9FE,#DDD6FE);display:flex;align-items:center;justify-content:center;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6D28D9" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div></div>
        <div class="blog-card-body">
          <span class="blog-cat-tag">Style Tips</span>
          <a href="#" class="blog-card-title">The 5-Piece Capsule Wardrobe That Works for Every Occasion</a>
          <p class="blog-card-excerpt">What if you only needed 5 versatile pieces to look great every single day? We've put together the ultimate minimalist wardrobe guide — mix, match, and conquer.</p>
          <div class="blog-meta">
            <div class="blog-author-avatar img-placeholder" style="background:linear-gradient(135deg,#EDE9FE,#DDD6FE)"></div>
            <div><p class="blog-author-name">Chris Wong</p><p class="blog-date">Mar 28, 2026</p></div>
            <span class="blog-read-time">6 min read</span>
          </div>
        </div>
      </article>

      <!-- Post 4 -->
      <article class="blog-card" data-reveal>
        <div class="blog-card-img"><div class="img-placeholder" style="width:100%;height:100%;background:linear-gradient(135deg,#D1FAE5,#A7F3D0);display:flex;align-items:center;justify-content:center;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#065F46" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div></div>
        <div class="blog-card-body">
          <span class="blog-cat-tag">Care Guide</span>
          <a href="#" class="blog-card-title">How to Make Your Linen Clothes Last for Years</a>
          <p class="blog-card-excerpt">Linen is one of the most durable natural fabrics — but only if you treat it right. From washing temperature to storage, here's the complete care guide for your linen wardrobe.</p>
          <div class="blog-meta">
            <div class="blog-author-avatar img-placeholder" style="background:linear-gradient(135deg,#FFF0EB,#FFE4F0)"></div>
            <div><p class="blog-author-name">Alex Rivera</p><p class="blog-date">Mar 20, 2026</p></div>
            <span class="blog-read-time">4 min read</span>
          </div>
        </div>
      </article>

    </div><!-- /blog grid -->

    <!-- SIDEBAR -->
    <aside>
      <!-- Search -->
      <div class="blog-sidebar-card">
        <p class="blog-sidebar-title">Search Articles</p>
        <label class="nav-search" style="max-width:100%">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="search" placeholder="Search blog…" style="width:100%">
        </label>
      </div>

      <!-- Recent Posts -->
      <div class="blog-sidebar-card">
        <p class="blog-sidebar-title">Recent Posts</p>
        <div class="sidebar-post"><div class="sidebar-post-thumb img-placeholder" style="background:linear-gradient(135deg,#FEF3C7,#FDE68A)"></div><div><a href="#" class="sidebar-post-title">The Return of Denim: How to Wear It This Season</a><p class="sidebar-post-date">Apr 5, 2026</p></div></div>
        <div class="sidebar-post"><div class="sidebar-post-thumb img-placeholder" style="background:linear-gradient(135deg,#FCE7F3,#FBCFE8)"></div><div><a href="#" class="sidebar-post-title">Why Slow Fashion Is the Future</a><p class="sidebar-post-date">Apr 1, 2026</p></div></div>
        <div class="sidebar-post"><div class="sidebar-post-thumb img-placeholder" style="background:linear-gradient(135deg,#EDE9FE,#DDD6FE)"></div><div><a href="#" class="sidebar-post-title">The 5-Piece Capsule Wardrobe Guide</a><p class="sidebar-post-date">Mar 28, 2026</p></div></div>
      </div>

      <!-- Categories -->
      <div class="blog-sidebar-card">
        <p class="blog-sidebar-title">Categories</p>
        <div class="cat-tag-list">
          <span class="cat-pill">Style Tips (12)</span>
          <span class="cat-pill">Trend Report (8)</span>
          <span class="cat-pill">Sustainability (5)</span>
          <span class="cat-pill">Care Guide (6)</span>
          <span class="cat-pill">Sindhi Ajrak (9)</span>
          <span class="cat-pill">Sindhi Topi (14)</span>
          <span class="cat-pill">Sindhi Gift Sets (4)</span>
        </div>
      </div>

      <!-- Newsletter mini -->
      <div class="blog-sidebar-card" style="background:linear-gradient(135deg,var(--color-ink),var(--color-ink-soft));color:white">
        <p class="blog-sidebar-title" style="color:white"><svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg> Style Newsletter</p>
        <p style="font-size:var(--text-xs);color:rgba(255,255,255,.6);margin-bottom:var(--space-4)">Get the latest articles and exclusive deals delivered weekly.</p>
        <input type="email" class="newsletter-input" placeholder="your@email.com" style="width:100%;margin-bottom:var(--space-3)">
        <button class="btn btn-primary" style="width:100%;height:40px;font-size:var(--text-sm)" onclick="showToast('Subscribed to Style Newsletter!','success')">Subscribe</button>
      </div>
    </aside>

  </div><!-- /blog layout -->

  <!-- Load more -->
  <div style="text-align:center;margin-top:var(--space-4)" data-reveal>
    <button class="btn btn-outline btn-lg" onclick="loadMore()">Load More Articles</button>
  </div>
</div>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>
<script src="/js/utils.js"></script>
<script src="/js/api-client.js"></script>
<script src="/js/cart.js"></script>
<script src="/js/auth.js"></script>
<script>
const io = new IntersectionObserver(e=>e.forEach(x=>{if(x.isIntersecting){x.target.classList.add('visible');io.unobserve(x.target);}}),{threshold:.1});

let currentPage = 1;
const LIMIT = 10;
const blogGrid = document.querySelector('.blog-grid');
const featuredBlock = document.querySelector('.blog-featured');
const sidebarRecent = document.querySelector('.blog-sidebar-card:nth-of-type(2)');

async function loadBlogPosts(page = 1) {
  try {
    const res = await fetch(API_BASE + `/blog?limit=${LIMIT}&offset=${(page-1)*LIMIT}`);
    const data = await res.json();
    if (data.success) {
      renderPosts(data.data.rows, page);
      
      if (page === 1) {
        document.querySelectorAll('[data-reveal]').forEach(el => io.observe(el));
      }
    }
  } catch (err) {
    console.error(err);
    showToast('Failed to load blog posts', 'error');
  }
}

function getAvatar(first, last) {
  const letters = (first?first.charAt(0):'') + (last?last.charAt(0):'');
  return `<div class="blog-author-avatar img-placeholder" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);display:flex;align-items:center;justify-content:center;color:var(--color-primary);font-weight:bold">${letters.toUpperCase()}</div>`;
}

function getCardImg(url, cat) {
  if (url) return `<div class="blog-card-img"><img src="${url}" style="width:100%;height:100%;object-fit:cover"></div>`;
  return `<div class="blog-card-img"><div class="img-placeholder" style="width:100%;height:100%;background:linear-gradient(135deg,#FEF3C7,#FDE68A);display:flex;align-items:center;justify-content:center;"><span style="color:#92400E">${cat || 'Blog'}</span></div></div>`;
}

function renderPosts(posts, page) {
  if (!posts || posts.length === 0) {
      if (page === 1) {
          blogGrid.innerHTML = '<p style="color:var(--color-muted)">No posts published yet.</p>';
          featuredBlock.style.display = 'none';
      }
      return;
  }
  
  if (page === 1 && posts.length > 0) {
      const f = posts[0];
      featuredBlock.innerHTML = `
        <div class="blog-featured-img">
          ${f.featured_image_url ? `<img src="${f.featured_image_url}" style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0">` : ''}
          <div style="position:relative;z-index:2;text-align:center;padding:var(--space-8)">
            <p style="color:rgba(255,255,255,.9);font-size:var(--text-sm);text-shadow:0 1px 3px rgba(0,0,0,.5)">Featured Article</p>
          </div>
        </div>
        <div class="blog-featured-body">
          <span class="blog-cat-tag" style="background:rgba(255,107,53,.2);color:#FF6B35;border:1px solid rgba(255,107,53,.3)">${f.category || 'News'}</span>
          <h2 style="color:white;font-size:var(--text-3xl);line-height:1.3;margin:var(--space-3) 0">${escapeHtml(f.title)}</h2>
          <p style="color:rgba(255,255,255,.65);font-size:var(--text-sm);line-height:1.7;margin-bottom:var(--space-5)">${escapeHtml(f.excerpt || '')}</p>
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-3)">
            <div style="display:flex;align-items:center;gap:var(--space-3)">
              <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#FF6B35,#FF3D7F);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.7rem">${(f.first_name || 'A').charAt(0)}</div>
              <div><p style="color:white;font-size:var(--text-xs);font-weight:600">${escapeHtml(f.first_name)} ${escapeHtml(f.last_name)}</p><p style="color:rgba(255,255,255,.4);font-size:.65rem">${formatDate(f.published_at || f.created_at)}</p></div>
            </div>
            <a href="/article?slug=${f.slug}" class="btn btn-primary btn-sm">Read Article</a>
          </div>
        </div>
      `;
      
      blogGrid.innerHTML = '';
      
      // Update sidebar
      let sideHtml = '<p class="blog-sidebar-title">Recent Posts</p>';
      posts.slice(0, 3).forEach(p => {
          sideHtml += `<div class="sidebar-post"><div class="sidebar-post-thumb img-placeholder" style="background:linear-gradient(135deg,#FEF3C7,#FDE68A)"></div><div><a href="/article?slug=${p.slug}" class="sidebar-post-title">${escapeHtml(p.title)}</a><p class="sidebar-post-date">${formatDate(p.published_at || p.created_at)}</p></div></div>`;
      });
      sidebarRecent.innerHTML = sideHtml;
  }
  
  const startIdx = page === 1 ? 1 : 0;
  for (let i = startIdx; i < posts.length; i++) {
      const p = posts[i];
      blogGrid.insertAdjacentHTML('beforeend', `
        <article class="blog-card" data-reveal style="transition-delay:${(i%4)*0.08}s">
          ${getCardImg(p.featured_image_url, p.category)}
          <div class="blog-card-body">
            <span class="blog-cat-tag">${escapeHtml(p.category || 'News')}</span>
            <a href="/article?slug=${p.slug}" class="blog-card-title">${escapeHtml(p.title)}</a>
            <p class="blog-card-excerpt">${escapeHtml(p.excerpt || '')}</p>
            <div class="blog-meta">
              ${getAvatar(p.first_name, p.last_name)}
              <div><p class="blog-author-name">${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)}</p><p class="blog-date">${formatDate(p.published_at || p.created_at)}</p></div>
              <span class="blog-read-time">${Math.max(1, Math.ceil((p.content?.length || 0)/1000))} min read</span>
            </div>
          </div>
        </article>
      `);
  }
}

document.addEventListener('DOMContentLoaded', () => loadBlogPosts(1));

function loadMore() {
    currentPage++;
    loadBlogPosts(currentPage);
}
</script>
<script src="/js/site-footer.js"></script>
</body>
</html>
