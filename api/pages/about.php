<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/frontend/navbar.php'; ?>


<?php include __DIR__ . '/../includes/frontend/mobile-nav.php'; ?>


<!-- HERO -->
<section class="about-hero">
  <div class="hero-bg-shapes" aria-hidden="true"><div class="hero-shape hero-shape-1"></div><div class="hero-shape hero-shape-2"></div></div>
  <div class="container" style="position:relative;z-index:1;padding-bottom:var(--space-10)">
    <div class="about-grid">
      <div>
        <span class="section-label" data-reveal>Our Story</span>
        <h1 data-reveal style="transition-delay:.1s">Crafted in Sindh.<br><span class="text-gradient">Worn by the World.</span></h1>
        <p style="font-size:var(--text-xl);color:var(--color-body);margin:var(--space-5) 0 var(--space-8);max-width:480px" data-reveal style="transition-delay:.2s">We started Mithooos to bridge the gap between traditional Sindhi heritage and modern living. From authentic Ajrak to hand-woven Kajoor baskets, we celebrate the artisans who keep our culture alive.</p>
        <a href="/shop" class="btn btn-primary btn-lg" data-reveal style="transition-delay:.3s">Explore Heritage</a>
      </div>
      <div class="about-visual" data-reveal style="transition-delay:.2s; border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-xl); align-self: center; aspect-ratio: auto;">
        <img src="/images/Mithoosabt.jpeg" alt="Mithooos About" style="width: 100%; height: auto; display: block;">
      </div>
    </div>
  </div>
</section>

<!-- STATS BAR -->
<div style="background:white;padding:var(--space-10) 0;border-bottom:1px solid var(--color-border-soft)">
  <div class="container">
    <div class="stats-grid" data-reveal>
      <div><div style="font-family:var(--font-heading);font-size:var(--text-4xl);font-weight:800;background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">12K+</div><div style="font-size:var(--text-sm);color:var(--color-muted);font-weight:600;margin-top:4px">Happy Customers</div></div>
      <div><div style="font-family:var(--font-heading);font-size:var(--text-4xl);font-weight:800;background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">500+</div><div style="font-size:var(--text-sm);color:var(--color-muted);font-weight:600;margin-top:4px">Unique Styles</div></div>
      <div><div style="font-family:var(--font-heading);font-size:var(--text-4xl);font-weight:800;background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">4.9 <svg class="rating-inline-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg></div><div style="font-size:var(--text-sm);color:var(--color-muted);font-weight:600;margin-top:4px">Avg. Rating</div></div>
      <div><div style="font-family:var(--font-heading);font-size:var(--text-4xl);font-weight:800;background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">40+</div><div style="font-size:var(--text-sm);color:var(--color-muted);font-weight:600;margin-top:4px">Countries Shipped</div></div>
    </div>
  </div>
</div>

<!-- VALUES -->
<section class="section">
  <div class="container">
    <div style="text-align:center;margin-bottom:var(--space-12)" data-reveal>
      <span class="section-label">What We Stand For</span>
      <h2>Our Core Values</h2>
    </div>
    <div class="value-cards">
      <div class="value-card" data-reveal>
        <div class="value-icon" style="background:#FFF0EB"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FF6B35" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div>
        <h4 style="margin-bottom:var(--space-3)">Artisan Crafted</h4>
        <p style="font-size:var(--text-sm);color:var(--color-body);line-height:1.7">We partner directly with families who have perfected the art of Ajrak block printing and Topi weaving over generations. Every piece tells their story.</p>
      </div>
      <div class="value-card" data-reveal style="transition-delay:.1s">
        <div class="value-icon" style="background:#D1FAE5"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
        <h4 style="margin-bottom:var(--space-3)">Cultural Integrity</h4>
        <p style="font-size:var(--text-sm);color:var(--color-body);line-height:1.7">We don't mass-produce. Authentic Ajrak involves a painstaking 21-stage process using natural dyes, taking weeks to complete. We refuse to shortcut our heritage.</p>
      </div>
      <div class="value-card" data-reveal style="transition-delay:.2s">
        <div class="value-icon" style="background:#DBEAFE"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
        <h4 style="margin-bottom:var(--space-3)">Global Community</h4>
        <p style="font-size:var(--text-sm);color:var(--color-body);line-height:1.7">Whether you're deeply rooted in Sindh or discovering its beauty for the first time, our community celebrates diversity, art, and timeless style.</p>
      </div>
    </div>
  </div>
</section>

<!-- TIMELINE -->
<section class="section section-alt">
  <div class="container">
    <div class="about-grid">
      <div data-reveal>
        <span class="section-label">Our Journey</span>
        <h2 style="margin-bottom:var(--space-10)">How We Got Here</h2>
        <div class="milestone-line">
          <div class="milestone"><div class="milestone-dot"><span class="milestone-year">22</span></div><div><p class="milestone-title">Roots in Sindh</p><p class="milestone-text">Started by connecting directly with master artisans in rural Sindh to bring authentic Ajrak to the digital space.</p></div></div>
          <div class="milestone"><div class="milestone-dot"><span class="milestone-year">23</span></div><div><p class="milestone-title">First 1,000 Customers</p><p class="milestone-text">Our hand-blocked Ajrak shawls went viral — we reached 1,000 orders and proved the global demand for authentic heritage.</p></div></div>
          <div class="milestone"><div class="milestone-dot"><span class="milestone-year">24</span></div><div><p class="milestone-title">Fair Trade Pledge</p><p class="milestone-text">Committed to ethical sourcing. We ensured 100% of our products support the direct livelihood of local weaving families.</p></div></div>
          <div class="milestone"><div class="milestone-dot"><span class="milestone-year">25</span></div><div><p class="milestone-title">Expanding the Vision</p><p class="milestone-text">Launched our premium Sindhi Topi and Kajoor handicraft lines to provide a complete cultural offering.</p></div></div>
          <div class="milestone" style="margin-bottom:0"><div class="milestone-dot" style="background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));border:none"><span style="color:white;font-size:.65rem;font-weight:800">26</span></div><div><p class="milestone-title">Global Reach</p><p class="milestone-text">12,000+ happy customers across 40+ countries. The fragrance of Sindh is reaching the world.</p></div></div>
        </div>
      </div>
      <div data-reveal style="transition-delay:.2s">
        <div style="background:linear-gradient(135deg,#FFF5F0,#FFF0F9);border-radius:var(--radius-2xl);padding:var(--space-8);text-align:center">
          <p style="font-family:var(--font-heading);font-size:var(--text-3xl);font-weight:800;color:var(--color-ink);line-height:1.4;margin-bottom:var(--space-5)">"We don't just sell clothes. We sell confidence."</p>
          <div style="display:flex;align-items:center;justify-content:center;gap:var(--space-3)">
            <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:var(--text-lg)">A</div>
            <div style="text-align:left"><p style="font-weight:700;color:var(--color-ink)">Alex Rivera</p><p style="font-size:var(--text-xs);color:var(--color-muted)">Co-Founder & Creative Director</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TEAM -->
<section class="section">
  <div class="container">
    <div style="text-align:center;margin-bottom:var(--space-12)" data-reveal>
      <span class="section-label">The People Behind It</span>
      <h2>Meet the Team</h2>
    </div>
    <div class="team-grid">
      <div class="team-card" data-reveal>
        <div class="team-avatar img-placeholder" style="background:linear-gradient(135deg,#FFF0EB,#FFE4F0)"></div>
        <p class="team-name">Master Ustad Ali</p>
        <p class="team-role">Head Artisan (Ajrak)</p>
      </div>
      <div class="team-card" data-reveal style="transition-delay:.08s">
        <div class="team-avatar img-placeholder" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE)"></div>
        <p class="team-name">Zahra</p>
        <p class="team-role">Creative Director</p>
      </div>
      <div class="team-card" data-reveal style="transition-delay:.16s">
        <div class="team-avatar img-placeholder" style="background:linear-gradient(135deg,#ECFCCB,#FEF08A)"></div>
        <p class="team-name">Faizan</p>
        <p class="team-role">Operations & Global Shipping</p>
      </div>
      <div class="team-card" data-reveal style="transition-delay:.24s">
        <div class="team-avatar img-placeholder" style="background:linear-gradient(135deg,#F3E8FF,#E9D5FF)"></div>
        <p class="team-name">Khatoon Begum</p>
        <p class="team-role">Lead Weaver (Topi)</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="newsletter-section">
  <div class="container">
    <div class="newsletter-inner" data-reveal>
      <h2>Ready to Find Your Style?</h2>
      <p>Browse our latest collection and discover pieces you'll reach for again and again.</p>
      <a href="/shop" class="btn btn-primary btn-lg">Shop Now →</a>
    </div>
  </div>
</section>

<script>
const io = new IntersectionObserver(e => e.forEach(x => { if(x.isIntersecting){x.target.classList.add('visible');io.unobserve(x.target);} }),{threshold:.1});
document.querySelectorAll('[data-reveal]').forEach(el => io.observe(el));
document.getElementById('navbar').classList.add('scrolled');
</script>
<script src="/js/utils.js"></script>
<script src="/js/api-client.js"></script>
<script src="/js/cart.js"></script>
<script src="/js/auth.js"></script>
<script src="/js/search.js"></script>
<script src="/js/site-footer.js"></script>
</body>
</html>
