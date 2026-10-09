/**
 * MITHOOOS — Admin Auth Guard
 * Include this as the FIRST script on every admin page.
 * Redirects to login if user is not an authenticated admin.
 */
(function adminAuthGuard() {
  'use strict';

  const PUBLIC_PAGES = ['login.html'];
  const currentPage  = location.pathname.split('/').pop() || 'index.html';

  if (PUBLIC_PAGES.includes(currentPage)) return;

  const inSubfolder = location.pathname.includes('/pages/');
  const LOGIN_URL = inSubfolder ? '../login.html' : 'login.html';

  function redirect() {
    window.location.replace(LOGIN_URL);
  }

  try {
    const user  = JSON.parse(localStorage.getItem('pp_user') || 'null');

    if (!user || user.user_type !== 'admin') {
      redirect();
      return;
    }

    // Inject admin user name into topbar if element exists
    document.addEventListener('DOMContentLoaded', () => {
      const nameEl = document.getElementById('adminUserName');
      if (nameEl) nameEl.textContent = user.first_name + ' ' + user.last_name;
    });

  } catch (e) {
    redirect();
  }
})();

// ── Logout helper ──
async function adminLogout() {
  const segments = window.location.pathname.split('/').filter(Boolean);
  const adminIndex = segments.indexOf('admin-panel');
  const basePath = adminIndex > 0 ? '/' + segments.slice(0, adminIndex).join('/') : '';
  await fetch(`${basePath}/backend/index.php?_url=/api/auth/logout`, { method: 'POST' }).catch(() => {});
  localStorage.removeItem('pp_user');
  sessionStorage.removeItem('pp_csrf_token');
  const inSubfolder = location.pathname.includes('/pages/');
  window.location.replace(inSubfolder ? '../login.html' : 'login.html');
}
