<!DOCTYPE html>
<html lang="en">
<?php include '../includes/frontend/head.php'; ?>
<body>

<!-- Minimal navbar -->
<?php include '../includes/frontend/navbar.php'; ?>


<?php include '../includes/frontend/mobile-nav.php'; ?>


<section class="auth-page">
  <div class="container">
    <div class="auth-card">

      <!-- Brand panel -->
      <div class="auth-brand">
        <div class="auth-brand-content">
          <img src="../images/logo.png" alt="Mithooos Logo" style="height:56px; width:auto; margin-bottom:1.5rem; display:block;">
          <h2 style="line-height:1.25">Welcome to<br>Mithooos</h2>
          <p>Join thousands of fashion lovers and discover premium styles delivered right to your door.</p>
          <div class="auth-brand-perks">
            <div class="auth-perk">
              <div class="auth-perk-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#FF6B35" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>
              <span>10% off your first order on sign-up</span>
            </div>
            <div class="auth-perk">
              <div class="auth-perk-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#FF6B35" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>
              <span>Free shipping on orders over PKR 5,000</span>
            </div>
            <div class="auth-perk">
              <div class="auth-perk-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#FF6B35" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>
              <span>Early access to new collections</span>
            </div>
            <div class="auth-perk">
              <div class="auth-perk-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#FF6B35" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>
              <span>Save wishlist and order history</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Form panel -->
      <div class="auth-form-panel">
        <!-- Tabs -->
        <div class="auth-tabs" role="tablist">
          <button class="auth-tab active" role="tab" id="loginTab" onclick="switchTab('login')">Sign In</button>
          <button class="auth-tab" role="tab" id="registerTab" onclick="switchTab('register')">Create Account</button>
        </div>

        <!-- LOGIN FORM -->
        <div id="loginForm">
          <h3 style="margin-bottom:var(--space-6);color:var(--color-ink)">Welcome back</h3>

          <!-- Social -->
          <button class="btn-social" onclick="showToast('Google login coming in Phase 6','info')">
            <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Continue with Google
          </button>

          <div class="social-divider"><span>or sign in with email</span></div>

          <form onsubmit="handleLogin(event)" novalidate>
            <div class="form-group">
              <label class="form-label" for="loginEmail">Email Address</label>
              <input type="email" id="loginEmail" class="form-input" placeholder="your@email.com" autocomplete="email" required>
              <div class="form-error" id="loginEmailErr">Please enter a valid email address.</div>
            </div>
            <div class="form-group">
              <label class="form-label" for="loginPassword">Password</label>
              <div class="input-wrap">
                <input type="password" id="loginPassword" class="form-input" placeholder="Enter your password" autocomplete="current-password" required>
                <span class="input-icon" onclick="togglePwd('loginPassword', this)">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </span>
              </div>
              <div class="form-error" id="loginPwdErr">Password is required.</div>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-6)">
              <label class="form-check"><input type="checkbox" id="rememberMe"><span>Remember me</span></label>
              <button type="button" class="btn-ghost btn-sm" style="color:var(--color-primary);padding:0" onclick="switchTab('forgot')">Forgot password?</button>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;height:50px;font-size:var(--text-base)">Sign In</button>
          </form>
        </div>

        <!-- REGISTER FORM -->
        <div id="registerForm" style="display:none">
          <h3 style="margin-bottom:var(--space-6);color:var(--color-ink)">Create your account</h3>

          <button class="btn-social" onclick="showToast('Google signup coming in Phase 6','info')">
            <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Sign up with Google
          </button>
          <div class="social-divider"><span>or register with email</span></div>

          <form onsubmit="handleRegister(event)" novalidate>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="regFirst">First Name</label>
                <input type="text" id="regFirst" class="form-input" placeholder="First name" required>
                <div class="form-error" id="regFirstErr">First name is required.</div>
              </div>
              <div class="form-group">
                <label class="form-label" for="regLast">Last Name</label>
                <input type="text" id="regLast" class="form-input" placeholder="Last name" required>
                <div class="form-error" id="regLastErr">Last name is required.</div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="regEmail">Email Address</label>
              <input type="email" id="regEmail" class="form-input" placeholder="your@email.com" autocomplete="email" required>
              <div class="form-error" id="regEmailErr">Please enter a valid email.</div>
            </div>
            <div class="form-group">
              <label class="form-label" for="regPassword">Password</label>
              <div class="input-wrap">
                <input type="password" id="regPassword" class="form-input" placeholder="Min. 8 characters" autocomplete="new-password" required oninput="checkStrength(this.value)">
                <span class="input-icon" onclick="togglePwd('regPassword', this)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
              </div>
              <div class="password-strength">
                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                <div class="strength-text" id="strengthText"></div>
              </div>
              <div class="form-error" id="regPwdErr">Password must be at least 8 characters.</div>
            </div>
            <div class="form-group">
              <label class="form-label" for="regConfirm">Confirm Password</label>
              <div class="input-wrap">
                <input type="password" id="regConfirm" class="form-input" placeholder="Repeat your password" autocomplete="new-password" required>
                <span class="input-icon" onclick="togglePwd('regConfirm', this)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
              </div>
              <div class="form-error" id="regConfirmErr">Passwords do not match.</div>
            </div>
            <div class="form-group">
              <label class="form-check">
                <input type="checkbox" id="termsCheck" required>
                <label for="termsCheck" style="font-size:var(--text-sm)">I agree to the <a href="terms-of-service.php">Terms of Service</a> and <a href="privacy-policy.php">Privacy Policy</a></label>
              </label>
              <div class="form-error" id="termsErr">You must agree to the terms.</div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;height:50px;font-size:var(--text-base)">Create Account</button>
          </form>
        </div>

        <!-- FORGOT PASSWORD -->
        <div id="forgotForm" style="display:none">
          <button type="button" onclick="switchTab('login')" style="background:none;border:none;color:var(--color-primary);font-size:var(--text-sm);cursor:pointer;margin-bottom:var(--space-5);display:flex;align-items:center;gap:var(--space-2)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Sign In
          </button>
          <h3 style="margin-bottom:var(--space-3)">Reset Password</h3>
          <p style="font-size:var(--text-sm);color:var(--color-muted);margin-bottom:var(--space-6)">Enter the email address linked to your account and we'll send you a reset link.</p>
          <form onsubmit="handleForgot(event)" novalidate>
            <div class="form-group">
              <label class="form-label" for="forgotEmail">Email Address</label>
              <input type="email" id="forgotEmail" class="form-input" placeholder="your@email.com" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;height:50px">Send Reset Link</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<script src="../js/utils.js"></script>
<script src="../js/api-client.js"></script>
<script src="../js/auth.js"></script>
<script>
'use strict';
function switchTab(tab) {
  document.getElementById('loginForm').style.display    = tab==='login'    ? 'block' : 'none';
  document.getElementById('registerForm').style.display = tab==='register' ? 'block' : 'none';
  document.getElementById('forgotForm').style.display   = tab==='forgot'   ? 'block' : 'none';
  document.getElementById('loginTab').classList.toggle('active', tab==='login');
  document.getElementById('registerTab').classList.toggle('active', tab==='register');
}

function togglePwd(id, icon) {
  const inp = document.getElementById(id);
  inp.type = inp.type === 'password' ? 'text' : 'password';
  icon.style.color = inp.type === 'text' ? 'var(--color-primary)' : 'var(--color-muted)';
}

function checkStrength(val) {
  const fill = document.getElementById('strengthFill');
  const text = document.getElementById('strengthText');
  let score = 0;
  if(val.length >= 8) score++;
  if(/[A-Z]/.test(val)) score++;
  if(/[0-9]/.test(val)) score++;
  if(/[^A-Za-z0-9]/.test(val)) score++;
  const levels = [
    {w:'0%', c:'var(--color-error)', label:''},
    {w:'25%', c:'var(--color-error)', label:'Weak'},
    {w:'50%', c:'var(--color-warning)', label:'Fair'},
    {w:'75%', c:'#3B82F6', label:'Good'},
    {w:'100%', c:'var(--color-success)', label:'Strong'},
  ];
  const l = levels[score];
  fill.style.width = l.w;
  fill.style.background = l.c;
  text.textContent = l.label;
  text.style.color = l.c;
}

function validateEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
function setErr(id, show) {
  document.getElementById(id).classList.toggle('show', show);
  const inp = document.getElementById(id.replace('Err',''));
  if(inp) inp.classList.toggle('error', show);
  return show;
}

async function handleLogin(e) {
  e.preventDefault();
  const email = document.getElementById('loginEmail').value;
  const pwd   = document.getElementById('loginPassword').value;
  let err = false;
  if(!validateEmail(email)) err = setErr('loginEmailErr', true); else setErr('loginEmailErr', false);
  if(!pwd) err = setErr('loginPwdErr', true); else setErr('loginPwdErr', false);
  if(err) return;

  const btn = e.target.querySelector('button[type="submit"]');
  const originalLabel = btn.textContent;
  btn.disabled = true;
  btn.innerHTML = '<div class="spinner" style="border-color:rgba(255,255,255,.3);border-top-color:white;width:16px;height:16px;display:inline-block;margin-right:6px"></div> Signing in…';

  try {
    const res = await API.auth.login(email, pwd);
    showToast(`Welcome back, ${res.data.first_name}!`, 'success');
    setTimeout(() => {
      const redirect = new URLSearchParams(location.search).get('redirect') || 'account.php';
      window.location.href = redirect;
    }, 800);
  } catch (loginErr) {
    showToast(loginErr.message || 'Sign in failed. Please try again.', 'error');
    btn.disabled = false;
    btn.textContent = originalLabel;
  }
}

async function handleRegister(e) {
  e.preventDefault();
  const first   = document.getElementById('regFirst').value.trim();
  const last    = document.getElementById('regLast').value.trim();
  const email   = document.getElementById('regEmail').value.trim();
  const pwd     = document.getElementById('regPassword').value;
  const confirm = document.getElementById('regConfirm').value;
  const terms   = document.getElementById('termsCheck').checked;
  let err = false;
  if(!first) err = setErr('regFirstErr',true); else setErr('regFirstErr',false);
  if(!last)  err = setErr('regLastErr',true);  else setErr('regLastErr',false);
  if(!validateEmail(email)) err = setErr('regEmailErr',true); else setErr('regEmailErr',false);
  if(pwd.length < 8) err = setErr('regPwdErr',true); else setErr('regPwdErr',false);
  if(pwd !== confirm) err = setErr('regConfirmErr',true); else setErr('regConfirmErr',false);
  if(!terms) err = setErr('termsErr',true); else setErr('termsErr',false);
  if(err) return;

  const btn = e.target.querySelector('button[type="submit"]');
  const originalLabel = btn.textContent;
  btn.disabled = true;
  btn.innerHTML = '<div class="spinner" style="border-color:rgba(255,255,255,.3);border-top-color:white;width:16px;height:16px;display:inline-block;margin-right:6px"></div> Creating account…';

  try {
    await API.auth.register(first, last, email, pwd);
    showToast(`Account created! Welcome, ${first}! Check your email for verification.`, 'success');
    setTimeout(() => switchTab('login'), 2000);
  } catch (regErr) {
    showToast(regErr.message || 'Registration failed. Please try again.', 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = originalLabel;
  }
}

async function handleForgot(e) {
  e.preventDefault();
  const email = document.getElementById('forgotEmail').value.trim();
  if (!validateEmail(email)) { showToast('Enter a valid email address', 'error'); return; }

  const btn = e.target.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.innerHTML = '<div class="spinner" style="border-color:rgba(255,255,255,.3);border-top-color:white;width:16px;height:16px;display:inline-block;margin-right:6px"></div> Sending…';

  try {
    const res = await API.auth.forgotPassword(email);

    showToast('Reset link sent! Check your inbox.', 'success');

    // Local dev hint — show token in console if backend returns it
    if (res.data?._dev_token) {
      console.info('[LOCAL DEV] Password reset token:', res.data._dev_token);
      console.info('[LOCAL DEV] Reset URL: ../pages/reset-password.php?token=' + res.data._dev_token);
    }

    setTimeout(() => switchTab('login'), 2500);
  } catch (err) {
    showToast(err.message || 'Could not connect. Is your local server running?', 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Send Reset Link';
  }
}
</script>
<script src="../js/site-footer.js"></script>
</body>
</html>
