/* Shared storefront newsletter and footer. */
(function () {
  'use strict';

  if (document.querySelector('.site-footer-shell')) return;

  if (typeof window.handleNewsletterSubmit !== 'function') {
    window.handleNewsletterSubmit = async function (event) {
      event.preventDefault();
      const form = event.currentTarget;
      const email = form.querySelector('input[type="email"]')?.value.trim();
      if (!email) return;
      const button = form.querySelector('button');
      if (button) { button.disabled = true; button.textContent = 'Subscribing...'; }
      try {
        if (window.API?.newsletter?.subscribe) await API.newsletter.subscribe(email);
        form.reset();
        alert('Thanks for subscribing to Mithooos.');
      } catch (error) {
        alert(error.message || 'Subscription could not be completed.');
      } finally {
        if (button) { button.disabled = false; button.textContent = 'Subscribe'; }
      }
    };
  }

  const shell = document.createElement('div');
  shell.className = 'site-footer-shell';
  shell.innerHTML = `
    <section class="newsletter-section" aria-labelledby="pageNewsletterHeading">
      <div class="container">
        <div class="newsletter-inner">
          <span class="section-label" style="color:rgba(255,255,255,.6)">Stay in the Loop</span>
          <h2 id="pageNewsletterHeading">Get 10% Off Your First Order</h2>
          <p>Subscribe to our newsletter for exclusive deals, new arrivals, and style inspiration delivered straight to your inbox.</p>
          <form class="newsletter-form" onsubmit="handleNewsletterSubmit(event)" aria-label="Newsletter signup">
            <input type="email" class="newsletter-input" placeholder="Enter your email address..." required aria-label="Email address">
            <button type="submit" class="btn btn-primary">Subscribe</button>
          </form>
          <p style="font-size:var(--text-xs);color:rgba(255,255,255,.4);margin-top:var(--space-4)">No spam, unsubscribe anytime. We respect your privacy.</p>
        </div>
      </div>
    </section>
    <footer class="footer" role="contentinfo">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand">
            <a href="/" aria-label="Mithooos home"><img src="/images/logo.png" alt="Mithooos" style="height:64px;width:auto;object-fit:contain"></a>
            <p>Premium fashion for the modern individual. Quality and affordability together, rooted in Sindhi heritage.</p>
            <div class="social-links" style="margin-top:var(--space-5)">
              <a href="https://www.instagram.com/mithooos.pk/" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg></a>
              <a href="https://www.facebook.com/people/Mithooos/61593170878179/" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Facebook"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
              <a href="#" class="social-link" aria-label="Twitter/X"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg></a>
            </div>
          </div>
          <div class="footer-col"><h4>Shop</h4><ul>
            <li><a href="/shop?cat=sindhi-ajrak">Sindhi Ajrak</a></li>
            <li><a href="/shop?cat=sindhi-topi">Sindhi Topi</a></li>
            <li><a href="/shop?cat=sindhi-kajoor">Sindhi Kajoor</a></li>
            <li><a href="/shop?cat=sindhi-handicrafts">Sindhi Handicrafts</a></li>
            <li><a href="/shop?filter=new">New Arrivals</a></li>
            <li><a href="/shop?filter=sale">Sale</a></li>
          </ul></div>
          <div class="footer-col"><h4>Help</h4><ul>
            <li><a href="/account">My Account</a></li>
            <li><a href="/order-tracking">Order Tracking</a></li>
            <li><a href="/contact">Contact Us</a></li>
            <li><a href="/checkout">Checkout</a></li>
          </ul></div>
          <div class="footer-col"><h4>Company</h4><ul>
            <li><a href="/about">About Us</a></li>
            <li><a href="/blog">Blog</a></li>
            <li><a href="/contact">Support</a></li>
          </ul><div style="margin-top:var(--space-6)"><h4 style="margin-bottom:var(--space-3)">Payment</h4><div style="display:flex;gap:var(--space-2);flex-wrap:wrap"><span style="background:rgba(255,255,255,.08);border-radius:var(--radius-sm);padding:4px 8px;font-size:.7rem;color:rgba(255,255,255,.6)">Easypaisa</span><span style="background:rgba(255,255,255,.08);border-radius:var(--radius-sm);padding:4px 8px;font-size:.7rem;color:rgba(255,255,255,.6)">Cash on Delivery</span></div></div></div>
        </div>
        <div class="footer-bottom"><p>© 2026 Mithooos. All rights reserved.</p><div style="display:flex;gap:var(--space-6);font-size:var(--text-sm)"><a href="/privacy-policy">Privacy Policy</a><a href="/terms-of-service">Terms of Service</a><a href="/cookie-policy">Cookie Policy</a></div></div>
      </div>
    </footer>`;

  if (document.querySelector('.newsletter-section')) {
    shell.querySelector('.newsletter-section')?.remove();
  }
  document.body.appendChild(shell);
})();
