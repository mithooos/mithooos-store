/**
 * MITHOOOS — Auth Module
 * Handles login, register, session management, UI state
 */
'use strict';

const Auth = (() => {

  /* ── Update navbar based on auth state ── */
  function syncNavbar() {
    const user = API.auth.getUser();
    const accountLinks = document.querySelectorAll('[data-auth-account]');
    const loginLinks   = document.querySelectorAll('[data-auth-login]');
    const greetings    = document.querySelectorAll('[data-auth-greeting]');

    if (user) {
      accountLinks.forEach(el => el.style.display = '');
      loginLinks.forEach(el => el.style.display = 'none');
      greetings.forEach(el => el.textContent = `Hi, ${user.first_name}!`);
    } else {
      accountLinks.forEach(el => el.style.display = 'none');
      loginLinks.forEach(el => el.style.display = '');
    }
  }

  /* ── Password strength ── */
  function checkStrength(password) {
    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    const levels = [
      { label: '', color: '', width: '0%' },
      { label: 'Weak', color: 'var(--color-error)', width: '25%' },
      { label: 'Fair', color: 'var(--color-warning)', width: '50%' },
      { label: 'Good', color: 'var(--color-info)', width: '75%' },
      { label: 'Strong', color: 'var(--color-success)', width: '100%' },
    ];
    return levels[Math.min(score, 4)];
  }

  /* ── Field validation ── */
  function validateField(input, rule) {
    const err = input.parentElement?.querySelector('.form-error') ||
                input.closest('.form-group')?.querySelector('.form-error');
    let valid = true;
    let msg = '';

    if (rule === 'required' && !input.value.trim()) {
      valid = false; msg = `${input.placeholder || 'This field'} is required.`;
    } else if (rule === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
      valid = false; msg = 'Please enter a valid email address.';
    } else if (rule === 'min8' && input.value.length < 8) {
      valid = false; msg = 'Must be at least 8 characters.';
    }

    input.classList.toggle('error', !valid);
    if (err) { err.textContent = msg; err.classList.toggle('show', !valid); }
    return valid;
  }

  /* ── Login form handler ── */
  async function handleLoginForm(formEl, opts = {}) {
    const email = formEl.querySelector('#loginEmail, [name=email]');
    const pwd   = formEl.querySelector('#loginPassword, [name=password]');
    const btn   = formEl.querySelector('[type=submit], .btn-login');

    let valid = true;
    if (email && !validateField(email, 'email')) valid = false;
    if (pwd  && !validateField(pwd, 'required'))  valid = false;
    if (!valid) return;

    btn && (btn.disabled = true) && (btn.innerHTML = '<div class="spinner" style="border-color:rgba(255,255,255,.3);border-top-color:white"></div> Signing in…');

    try {
      const res = await API.auth.login(email.value.trim(), pwd.value);
      if (typeof showToast === 'function') showToast(`Welcome back, ${res.data.first_name}!`, 'success');
      syncNavbar();
      setTimeout(() => {
        const redirect = new URLSearchParams(location.search).get('redirect') || opts.redirect || '/account';
        window.location.href = redirect;
      }, 800);
    } catch (err) {
      if (typeof showToast === 'function') showToast(err.message, 'error');
      btn && (btn.disabled = false) && (btn.innerHTML = 'Sign In');
    }
  }

  /* ── Register form handler ── */
  async function handleRegisterForm(formEl, opts = {}) {
    const firstName = formEl.querySelector('#regFirst, [name=first_name]');
    const lastName  = formEl.querySelector('#regLast,  [name=last_name]');
    const email     = formEl.querySelector('#regEmail, [name=email]');
    const pwd       = formEl.querySelector('#regPassword, [name=password]');

    const confirm   = formEl.querySelector('#regConfirm, [name=confirm_password]');
    const terms     = formEl.querySelector('#termsCheck');
    const btn       = formEl.querySelector('[type=submit], .btn-register');

    let valid = true;
    if (firstName && !validateField(firstName, 'required')) valid = false;
    if (lastName  && !validateField(lastName,  'required')) valid = false;
    if (email     && !validateField(email,     'email'))    valid = false;
    if (pwd       && !validateField(pwd,       'min8'))     valid = false;
    if (confirm && confirm.value !== pwd?.value) {
      if (typeof showToast === 'function') showToast('Passwords do not match.', 'error');
      valid = false;
    }
    if (terms && !terms.checked) {
      if (typeof showToast === 'function') showToast('Please accept the terms and conditions.', 'error');
      valid = false;
    }
    if (!valid) return;

    btn && (btn.disabled = true) && (btn.innerHTML = '<div class="spinner" style="border-color:rgba(255,255,255,.3);border-top-color:white"></div> Creating account…');

    try {
      await API.auth.register(firstName?.value.trim(), lastName?.value.trim(), email?.value.trim(), pwd?.value);
      if (typeof showToast === 'function') showToast('Account created! Check your email to verify.', 'success');
      setTimeout(() => opts.onSuccess?.() || location.reload(), 1500);
    } catch (err) {
      if (typeof showToast === 'function') showToast(err.message, 'error');
      btn && (btn.disabled = false) && (btn.innerHTML = 'Create Account');
    }
  }

  /* ── Guard: redirect to login if not authenticated ── */
  function requireAuth(redirectTo = null) {
    if (!API.auth.isLoggedIn()) {
      const current = encodeURIComponent(location.pathname + location.search);
      window.location.href = `/login?redirect=${redirectTo || current}`;
      return false;
    }
    return true;
  }

  /* ── Init ── */
  document.addEventListener('DOMContentLoaded', syncNavbar);
  window.addEventListener('pp:auth:login', syncNavbar);
  window.addEventListener('pp:auth:logout', () => { syncNavbar(); window.location.href = '/'; });
  
  window.addEventListener('pp:auth:expired', () => {
    if (typeof showToast === 'function') showToast('Session expired. Please sign in again.', 'info');
    syncNavbar();
  });

  return { syncNavbar, checkStrength, validateField, handleLoginForm, handleRegisterForm, requireAuth };
})();
