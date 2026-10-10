/**
 * MITHOOOS — Centralized API Client
 * Handles all communication with the PHP backend
 */
'use strict';

const API = (() => {
  const BASE        = location.pathname.includes('/mithooos/')
    ? '/mithooos/backend'
    : '/backend';
  const TOKEN_KEY   = 'pp_token';
  const SESSION_KEY = 'pp_session_id';
  const CSRF_KEY    = 'pp_csrf_token';

  /* ── Session ID (for guest cart) ── */
  function getSessionId() {
    let sid = localStorage.getItem(SESSION_KEY);
    if (!sid) {
      sid = 'sess_' + Date.now() + '_' + Math.random().toString(36).slice(2, 10);
      localStorage.setItem(SESSION_KEY, sid);
    }
    return sid;
  }

  /* ── Fetch a fresh CSRF token from server ── */
  async function fetchCsrfToken() {
    try {
      const res  = await fetch(`${BASE}/index.php?_url=/api/csrf-token`, { method: 'GET', credentials: 'same-origin' });
      const data = await res.json();
      if (data?.data?.token) {
        sessionStorage.setItem(CSRF_KEY, data.data.token);
        return data.data.token;
      }
    } catch (e) { /* fallback: no CSRF — JWT already protects auth endpoints */ }
    return null;
  }

  /* ── Get cached CSRF token (or fetch new one) ── */
  async function getCsrfToken() {
    return sessionStorage.getItem(CSRF_KEY) || await fetchCsrfToken();
  }

  /* ── Base headers ── */
  async function headers(method = 'GET') {
    const h = {
      'Content-Type': 'application/json',
      'X-Session-ID': getSessionId(),
    };

    const user = auth.getUser();
    if (user && user.token) {
      h['Authorization'] = `Bearer ${user.token}`;
    } else {
      const token = localStorage.getItem(TOKEN_KEY);
      if (token) h['Authorization'] = `Bearer ${token}`;
    }

    // Attach CSRF token for state-changing requests
    if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method.toUpperCase())) {
      const csrf = await getCsrfToken();
      if (csrf) h['X-CSRF-Token'] = csrf;
    }
    return h;
  }

  /* ── Core request ── */
  async function request(method, endpoint, data = null, params = {}) {
    let url = `${BASE}/index.php?_url=${endpoint}`;
    if (Object.keys(params).length) {
      url += '&' + new URLSearchParams(params).toString();
    }
    try {
      const opts = { method, headers: await headers(method), credentials: 'same-origin' };
      if (data && method !== 'GET') opts.body = JSON.stringify(data);

      const res  = await fetch(url, opts);

      // Handle auth expiry
      if (res.status === 401) {
        localStorage.removeItem(TOKEN_KEY);
        window.dispatchEvent(new CustomEvent('pp:auth:expired'));
      }

      // On CSRF failure refresh token and retry once
      if (res.status === 403) {
        const json = await res.json().catch(() => ({}));
        if (json.message?.includes('CSRF')) {
          sessionStorage.removeItem(CSRF_KEY);
          // One automatic retry with fresh token
          const retryOpts = { method, headers: await headers(method), credentials: 'same-origin' };
          if (data && method !== 'GET') retryOpts.body = JSON.stringify(data);
          const retry = await fetch(url, retryOpts);
          const retryJson = await retry.json();
          if (!retryJson.success && retry.status >= 400) throw new APIError(retryJson.message || 'Request failed', retry.status);
          return retryJson;
        }
        throw new APIError(json.message || 'Forbidden', 403);
      }

      const json = await res.json();
      if (!json.success && res.status >= 400) {
        let details = '';
        if (json.errors) {
          if (Array.isArray(json.errors)) {
            details = ` ${json.errors.join(' ')}`;
          } else if (typeof json.errors === 'object') {
            details = ` ${Object.values(json.errors).join(' ')}`;
          }
        }
        throw new APIError((json.message || 'Request failed') + details, res.status, json.errors);
      }
      return json;
    } catch (err) {
      if (err instanceof APIError) throw err;
      throw new APIError('Network error. Is your local server running?', 0);
    }
  }

  /* ── Auth ── */
  const auth = {
    async register(firstName, lastName, email, password) {
      return request('POST', '/api/auth/register', { first_name: firstName, last_name: lastName, email, password });
    },
    async login(email, password) {
      const res = await request('POST', '/api/auth/login', { email, password });
      if (res.data?.user_id) {
        localStorage.setItem('pp_user', JSON.stringify(res.data));
        window.dispatchEvent(new CustomEvent('pp:auth:login', { detail: res.data }));
      }
      return res;
    },
    async logout() {
      await request('POST', '/api/auth/logout').catch(() => {});
      localStorage.removeItem('pp_user');
      sessionStorage.removeItem(CSRF_KEY);
      window.dispatchEvent(new CustomEvent('pp:auth:logout'));
    },
    getUser() {
      try { return JSON.parse(localStorage.getItem('pp_user')); } catch (e) { return null; }
    },
    isLoggedIn() { return !!localStorage.getItem('pp_user'); },
    async me() { return request('GET', '/api/auth/me'); },
    async validateResetToken(token) {
      return request('GET', '/api/auth/reset-password', null, { token });
    },
    async resetPassword(token, password, passwordConfirm) {
      return request('POST', '/api/auth/reset-password', { token, password, password_confirm: passwordConfirm });
    },
    async forgotPassword(email) {
      return request('POST', '/api/auth/forgot-password', { email });
    },
    async updateProfile(first_name, last_name, phone) {
      return request('PUT', '/api/auth/me', { first_name, last_name, phone });
    },
    async changePassword(current_password, new_password) {
      return request('PUT', '/api/auth/change-password', { current_password, new_password });
    },
    async updatePreferences(newsletter, order_notifications, restock_alerts) {
      return request('PUT', '/api/auth/preferences', { newsletter, order_notifications, restock_alerts });
    },
    async deleteAccount() {
      return request('DELETE', '/api/auth/me');
    },
  };

  /* ── Products ── */
  const products = {
    list: (params = {}) => request('GET', '/api/products', null, params),
    get:  (id)          => request('GET', `/api/products/${id}`),
    search: (q)         => request('GET', '/api/search', null, { q }),
    featured:   ()      => request('GET', '/api/products', null, { is_featured: 1, limit: 8 }),
    newArrivals: ()     => request('GET', '/api/products', null, { is_new: 1, limit: 8 }),
  };

  /* ── Cart ── */
  const cart = {
    get: (coupon, shippingMethod) => {
        let params = {};
        if (coupon) params.coupon = coupon;
        if (shippingMethod) params.shipping_method = shippingMethod;
        return request('GET', '/api/cart', null, params);
    },
    add: (productId, qty = 1, variantId = null) =>
      request('POST', '/api/cart', { product_id: productId, quantity: qty, variant_id: variantId }),
    update: (cartId, qty) => request('PUT',    `/api/cart/${cartId}`, { quantity: qty }),
    remove: (cartId)      => request('DELETE',  `/api/cart/${cartId}`),
    clear:  ()            => request('DELETE',  '/api/cart'),
    async sync() {
      const data  = await this.get();
      const count = data.data?.items?.reduce((n, i) => n + i.quantity, 0) || 0;
      document.querySelectorAll('[data-cart-count], #cartCount').forEach(el => el.textContent = count);
      window.dispatchEvent(new CustomEvent('pp:cart:updated', { detail: data.data }));
      return data;
    },
  };

  /* ── Orders ── */
  const orders = {
    list:  (params) => request('GET',  '/api/orders', null, params),
    get:   (id)     => request('GET',  `/api/orders/${id}`),
    place: (payload)=> request('POST', '/api/orders', payload),
  };

  /* ── Reviews ── */
  const reviews = {
    list:   (productId, params = {}) => request('GET',  '/api/reviews', null, { product_id: productId, ...params }),
    submit: (data)                   => request('POST', '/api/reviews', data),
  };

  /* ── Wishlist ── */
  const wishlist = {
    list:   ()    => request('GET',    '/api/wishlist'),
    toggle: (pid) => request('POST',   '/api/wishlist', { product_id: pid }),
    remove: (id)  => request('DELETE', `/api/wishlist/${id}`),
  };

  /* ── Newsletter ── */
  const newsletter = {
    subscribe: (email, firstName) => request('POST', '/api/newsletter', { email, first_name: firstName }),
  };

  /* ── Contact form ── */
  const contact = {
    send: (name, email, subject, message) => request('POST', '/api/contact', { name, email, subject, message }),
  };

  /* ── Categories ── */
  const categories = {
    list: () => request('GET', '/api/categories'),
  };

  /* ── Saved addresses ── */
  const addresses = {
    list: () => request('GET', '/api/addresses'),
    create: data => request('POST', '/api/addresses', data),
    update: (id, data) => request('PUT', `/api/addresses/${id}`, data),
    remove: id => request('DELETE', `/api/addresses/${id}`),
  };

  /* ── Collections ── */
  const collections = {
    list: () => request('GET', '/api/collections'),
  };

  return { auth, products, cart, orders, reviews, wishlist, newsletter, contact, categories, collections, addresses, getSessionId, request, BASE };
})();

window.API_BASE = API.BASE + '/index.php?_url=/api';

/* ── Custom error class ── */
class APIError extends Error {
  constructor(message, status = 400, errors = null) {
    super(message);
    this.name   = 'APIError';
    this.status = status;
    this.errors = errors;
  }
}

document.addEventListener('DOMContentLoaded', async () => {
  try {
    await API.cart.sync();
  } catch (e) {
    // Backend not available — use localStorage fallback
    const local = JSON.parse(localStorage.getItem('pp_cart') || '[]');
    const count = local.reduce((n, i) => n + (i.qty || 1), 0);
    document.querySelectorAll('[data-cart-count], #cartCount').forEach(el => el.textContent = count);
  }
});
