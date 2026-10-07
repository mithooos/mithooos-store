<!DOCTYPE html>
<html lang="en">
<?php include '../includes/frontend/head.php'; ?>
<body>

<?php include '../includes/frontend/navbar.php'; ?>


<?php include '../includes/frontend/mobile-nav.php'; ?>


<section class="page-hero">
  <div class="container page-hero-inner">
    <nav class="breadcrumb"><a href="../index.php">Home</a><span class="breadcrumb-sep">/</span><span>Contact</span></nav>
    <h1>Get in Touch</h1>
    <p style="color:var(--color-body);margin-top:var(--space-2)">We'd love to hear from you. Our friendly team is always here to help.</p>
  </div>
</section>

<div class="container">
  <div class="contact-layout">

    <!-- INFO SIDE -->
    <div data-reveal>
      <h2 style="margin-bottom:var(--space-8)">Let's Talk</h2>

      <div class="contact-info-item">
        <div class="contact-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
        <div>
          <p style="font-weight:700;color:var(--color-ink);margin-bottom:4px">Email Us</p>
          <a href="mailto:hello@mithooos.com" style="color:var(--color-primary);font-size:var(--text-sm)">hello@mithooos.com</a>
          <p style="font-size:var(--text-xs);color:var(--color-muted);margin-top:2px">We reply within 24 hours</p>
        </div>
      </div>

      <div class="contact-info-item">
        <div class="contact-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.08 1.18 2 2 0 012.06 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.2 7.72a16 16 0 006.07 6.07l1.07-1.07a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z"/></svg></div>
        <div>
          <p style="font-weight:700;color:var(--color-ink);margin-bottom:4px">Call Us</p>
          <a href="tel:+15551234567" style="color:var(--color-primary);font-size:var(--text-sm)">+1 (555) 123-4567</a>
          <p style="font-size:var(--text-xs);color:var(--color-muted);margin-top:2px">Mon–Fri, 9 AM–6 PM EST</p>
        </div>
      </div>

      <div class="contact-info-item">
        <div class="contact-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
        <div>
          <p style="font-weight:700;color:var(--color-ink);margin-bottom:4px">Visit Us</p>
          <p style="font-size:var(--text-sm);color:var(--color-body)">123 Fashion Avenue, Suite 400<br>New York, NY 10001, USA</p>
          <p style="font-size:var(--text-xs);color:var(--color-muted);margin-top:2px">By appointment only</p>
        </div>
      </div>

      <div class="contact-info-item">
        <div class="contact-icon-box"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div>
          <p style="font-weight:700;color:var(--color-ink);margin-bottom:4px">Business Hours</p>
          <p style="font-size:var(--text-sm);color:var(--color-body)">Monday – Friday: 9 AM – 6 PM EST</p>
          <p style="font-size:var(--text-sm);color:var(--color-body)">Saturday: 10 AM – 4 PM EST</p>
          <p style="font-size:var(--text-xs);color:var(--color-muted)">Sunday: Closed</p>
        </div>
      </div>

      <!-- Map placeholder -->
      <div class="map-placeholder">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#93C5FD" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <p style="color:var(--color-muted);font-size:var(--text-sm)">123 Fashion Avenue, New York</p>
        <a href="https://maps.google.com" target="_blank" class="btn btn-outline btn-sm">Open in Maps</a>
      </div>
    </div>

    <!-- FORM SIDE -->
    <div data-reveal style="transition-delay:.15s">
      <div class="contact-form-card">
        <h2 class="contact-form-title">Send a Message</h2>
        <p class="contact-form-sub">Fill out the form and we'll get back to you as soon as possible.</p>

        <form id="contactForm" onsubmit="handleContact(event)" novalidate>
          <div class="form-row">
            <div class="form-group"><label class="form-label" for="ctName">Full Name *</label><input type="text" id="ctName" class="form-input" placeholder="Your full name" required></div>
            <div class="form-group"><label class="form-label" for="ctEmail">Email *</label><input type="email" id="ctEmail" class="form-input" placeholder="your@email.com" required></div>
          </div>
          <div class="form-group">
            <label class="form-label" for="ctSubject">Subject *</label>
            <select id="ctSubject" class="form-input" style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%236B7280' stroke-width='1.5' stroke-linecap='round' fill='none'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 14px center;padding-right:2.5rem" required>
              <option value="">Select a topic…</option>
              <option>General Inquiry</option>
              <option>Order Issue</option>
              <option>Product Question</option>
              <option>Returns & Exchanges</option>
              <option>Shipping Question</option>
              <option>Technical Support</option>
              <option>Partnership / Press</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="ctMsg">Message *</label>
            <textarea id="ctMsg" class="form-input" placeholder="Tell us how we can help you…" required style="height:140px;resize:vertical"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label" for="ctAttach">Attachment <span style="font-weight:400;color:var(--color-muted)">(optional, max 5MB)</span></label>
            <input type="file" id="ctAttach" class="form-input" accept=".jpg,.png,.pdf" style="padding:var(--space-3)">
          </div>
          <label class="form-check" style="margin-bottom:var(--space-5)">
            <input type="checkbox" id="ctConsent" required>
            <span style="font-size:var(--text-sm)">I agree that my data will be used to respond to my inquiry</span>
          </label>
          <button type="submit" class="btn btn-primary btn-lg" style="width:100%" id="ctSubmitBtn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            Send Message
          </button>
        </form>
      </div>

      <!-- FAQ -->
      <div style="margin-top:var(--space-8)">
        <h3 style="margin-bottom:var(--space-5)">Frequently Asked Questions</h3>
        <div id="faqList">
          <div class="faq-item"><div class="faq-q" onclick="toggleFaq(this)">How long does shipping take? <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></div><div class="faq-a">Standard shipping takes 3–5 business days within the US. International shipping takes 7–14 business days. Express (1–2 days) and overnight options are also available at checkout.</div></div>
          <div class="faq-item"><div class="faq-q" onclick="toggleFaq(this)">What is your return policy? <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></div><div class="faq-a">We offer free 30-day returns on all orders. Items must be unworn, unwashed, and in original condition with tags attached. Simply log in to your account and initiate a return from your order history.</div></div>
          <div class="faq-item"><div class="faq-q" onclick="toggleFaq(this)">How do I track my order? <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></div><div class="faq-a">Once your order ships, you'll receive an email with a tracking number and link. You can also view tracking from your account dashboard under "My Orders".</div></div>
          <div class="faq-item"><div class="faq-q" onclick="toggleFaq(this)">Do you ship internationally? <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></div><div class="faq-a">Yes! We ship to over 40 countries worldwide. International shipping rates and times are calculated at checkout based on your location.</div></div>
          <div class="faq-item" style="border-bottom:none"><div class="faq-q" onclick="toggleFaq(this)">Can I change or cancel my order? <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></div><div class="faq-a">Orders can be modified or cancelled within 2 hours of placement. After that, the order enters processing and cannot be changed. Contact us immediately if you need assistance.</div></div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>
<script src="../js/utils.js"></script>
<script src="../js/api-client.js"></script>
<script>
const io = new IntersectionObserver(e=>e.forEach(x=>{if(x.isIntersecting){x.target.classList.add('visible');io.unobserve(x.target);}}),{threshold:.1});
document.querySelectorAll('[data-reveal]').forEach(el=>io.observe(el));

function toggleFaq(el) {
  const ans = el.nextElementSibling;
  const icon = el.querySelector('svg');
  const isOpen = ans.classList.contains('open');
  document.querySelectorAll('.faq-a').forEach(a=>a.classList.remove('open'));
  document.querySelectorAll('.faq-q svg').forEach(s=>s.style.transform='');
  if(!isOpen){ ans.classList.add('open'); icon.style.transform='rotate(180deg)'; }
}

async function handleContact(e) {
  e.preventDefault();
  const btn = document.getElementById('ctSubmitBtn');
  if(!document.getElementById('ctConsent').checked){ showToast('Please agree to data usage','error'); return; }

  const name = document.getElementById('ctName').value.trim();
  const email = document.getElementById('ctEmail').value.trim();
  const subject = document.getElementById('ctSubject').value;
  const message = document.getElementById('ctMsg').value.trim();
  if (!name || !email || !subject || !message) { showToast('Please fill in all required fields','error'); return; }

  const attachment = document.getElementById('ctAttach').files[0];
  if (attachment) {
    // Attachments aren't supported by the backend yet (POST /api/contact
    // only accepts the text fields) — be upfront about it rather than
    // silently drop the file while implying it was sent.
    showToast('Note: attachments aren\'t supported yet — sending your message without it.', 'info');
  }

  const originalHTML = btn.innerHTML;
  btn.innerHTML = '<div class="spinner"></div> Sending…';
  btn.disabled = true;

  try {
    await API.contact.send(name, email, subject, message);
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Message Sent!';
    btn.style.background = 'linear-gradient(135deg,#10B981,#059669)';
    showToast('Message sent! We\'ll get back to you within 24 hours.','success');
    setTimeout(()=>{ e.target.reset(); btn.innerHTML=originalHTML; btn.disabled=false; btn.style.background=''; }, 3000);
  } catch (err) {
    showToast(err.message || 'Could not send your message. Please try again.', 'error');
    btn.innerHTML = originalHTML;
    btn.disabled = false;
  }
}
</script>
<script src="../js/site-footer.js"></script>
</body>
</html>
