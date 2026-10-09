<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../includes/frontend/head.php'; ?>
<body>

<div class="reset-card">
  <a href="/login" style="display:inline-flex;align-items:center;gap:var(--space-2);font-size:var(--text-sm);color:var(--color-primary);margin-bottom:var(--space-6);text-decoration:none">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Sign In
  </a>

  <!-- Validating token state -->
  <div id="loadingState" style="text-align:center;padding:var(--space-8) 0">
    <div class="spinner" style="width:28px;height:28px;border-color:var(--color-border);border-top-color:var(--color-primary);margin:0 auto var(--space-4)"></div>
    <p style="color:var(--color-muted);font-size:var(--text-sm)">Validating your reset link…</p>
  </div>

  <!-- Invalid token -->
  <div id="invalidState" style="display:none;text-align:center;padding:var(--space-4) 0">
    <div style="width:56px;height:56px;background:#FEF2F2;border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-5)">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    </div>
    <h2 style="margin-bottom:var(--space-2)">Link expired</h2>
    <p>This reset link is invalid or has expired. Reset links are valid for 1 hour.</p>
    <a href="/login" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:var(--space-4)">Request a new link</a>
  </div>

  <!-- Reset form -->
  <div id="formState" class="form-state hide">
    <div style="width:56px;height:56px;background:var(--color-surface-alt);border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;margin-bottom:var(--space-5)">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    </div>
    <h2>Set new password</h2>
    <p id="resetEmailLabel">Create a strong password for your account.</p>

    <form id="resetForm" novalidate>
      <div class="form-group">
        <label class="form-label" for="newPassword">New Password</label>
        <div class="input-wrap" style="position:relative">
          <input type="password" id="newPassword" class="form-input" placeholder="At least 8 characters"
                 oninput="checkStrength(this.value)" required autocomplete="new-password">
          <button type="button" class="input-icon" onclick="togglePwd('newPassword',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--color-muted)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
        <div style="display:flex;justify-content:space-between;margin-top:4px">
          <span style="font-size:.7rem;color:var(--color-muted)">Min. 8 characters</span>
          <span style="font-size:.7rem;font-weight:600" id="strengthText"></span>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" class="form-input" placeholder="Repeat your password"
               required autocomplete="new-password">
        <div class="form-error" id="confirmErr" style="display:none;font-size:.75rem;color:var(--color-error);margin-top:4px">Passwords do not match.</div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;height:50px;font-size:var(--text-base);margin-top:var(--space-2)" id="submitBtn">
        Set New Password
      </button>
    </form>
  </div>

  <!-- Success -->
  <div id="successState" class="success-state">
    <div style="width:64px;height:64px;background:#ECFDF5;border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-5)">
      <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h2 style="margin-bottom:var(--space-2)">Password updated!</h2>
    <p>Your password has been changed successfully. You can now sign in with your new password.</p>
    <a href="/login" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:var(--space-6)">Sign In Now</a>
  </div>
</div>

<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<script src="/js/utils.js"></script>
<script src="/js/api-client.js"></script>
<script>
'use strict';

const params = new URLSearchParams(location.search);
const TOKEN  = params.get('token') || '';

/* ── Validate token on load ── */
async function validateToken() {
  if (!TOKEN) {
    show('invalidState');
    return;
  }

  try {
    const res = await API.auth.validateResetToken(TOKEN);
    const emailLabel = document.getElementById('resetEmailLabel');
    if (emailLabel && res.data?.email) {
      emailLabel.textContent = 'Setting a new password for ' + res.data.email;
    }
    show('formState');
  } catch (err) {
    if (err.status === 0) {
      // Genuine network failure (backend unreachable) — not a rejected
      // token. Show the form anyway so local dev/testing isn't blocked.
      show('formState');
    } else {
      // Token was actually rejected by the server (invalid/expired/missing)
      show('invalidState');
    }
  }
}

/* ── Submit new password ── */
document.getElementById('resetForm')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const pwd     = document.getElementById('newPassword').value;
  const confirm = document.getElementById('confirmPassword').value;
  const errEl   = document.getElementById('confirmErr');
  const btn     = document.getElementById('submitBtn');

  errEl.style.display = 'none';
  document.getElementById('newPassword').classList.remove('error');

  if (pwd.length < 8) {
    document.getElementById('newPassword').classList.add('error');
    showToast('Password must be at least 8 characters.', 'error');
    return;
  }

  if (pwd !== confirm) {
    errEl.style.display = 'block';
    document.getElementById('confirmPassword').classList.add('error');
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<div class="spinner" style="width:16px;height:16px;border-color:rgba(255,255,255,.3);border-top-color:white;display:inline-block;margin-right:6px"></div> Updating…';

  try {
    const res = await API.auth.resetPassword(TOKEN, pwd, confirm);
    show('successState');
  } catch (err) {
    showToast(err.message || 'Something went wrong.', 'error');
    btn.disabled = false;
    btn.textContent = 'Set New Password';
  }
});

/* ── Password strength ── */
function checkStrength(val) {
  let score = 0;
  if (val.length >= 8) score++;
  if (val.length >= 12) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const levels = [
    { w:'0%',   c:'',                            label:'' },
    { w:'25%',  c:'var(--color-error)',           label:'Weak' },
    { w:'50%',  c:'var(--color-warning)',         label:'Fair' },
    { w:'75%',  c:'var(--color-info)',            label:'Good' },
    { w:'100%', c:'var(--color-success)',         label:'Strong' },
  ];
  const l = levels[Math.min(score, 4)];
  const fill = document.getElementById('strengthFill');
  const text = document.getElementById('strengthText');
  fill.style.width      = l.w;
  fill.style.background = l.c;
  text.textContent      = l.label;
  text.style.color      = l.c;
}

function togglePwd(id) {
  const inp = document.getElementById(id);
  inp.type = inp.type === 'password' ? 'text' : 'password';
}

function show(id) {
  ['loadingState','invalidState','formState','successState'].forEach(s => {
    const el = document.getElementById(s);
    if (el) el.style.display = s === id ? (s === 'formState' ? 'block' : 'block') : 'none';
  });
}

/* ── Init ── */
document.addEventListener('DOMContentLoaded', validateToken);
</script>
<script src="/js/site-footer.js"></script>
</body>
</html>
