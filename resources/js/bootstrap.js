window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

// Ensure axios will use the XSRF cookie/header naming Laravel expects
window.axios.defaults.withCredentials = true;
window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

// Helper to read token safely (no crashes if meta/cookie missing)
function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  if (meta && meta.content) return meta.content;
  const m = document.cookie && document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
  return m ? decodeURIComponent(m[1]) : '';
}

// Attach CSRF only on mutating requests; avoid touching GET/HEAD
window.axios.interceptors.request.use((config) => {
  const method = (config.method || 'get').toLowerCase();
  if (method !== 'get' && method !== 'head') {
    const t = getCsrfToken();
    if (t) {
      (config.headers ||= {});
      config.headers['X-CSRF-TOKEN'] = t;
      config.headers['X-XSRF-TOKEN'] = t;
    }
  }
  return config;
});


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Real-time broadcasting (Laravel Echo + Pusher) is not active by default.
 * To enable: set BROADCAST_DRIVER=pusher in .env, fill PUSHER_* credentials,
 * then uncomment the block below and run `npm install laravel-echo pusher-js`.
 *
 * import Echo from 'laravel-echo';
 * window.Pusher = require('pusher-js');
 * window.Echo = new Echo({
 *     broadcaster: 'pusher',
 *     key: import.meta.env.VITE_PUSHER_APP_KEY,
 *     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
 *     forceTLS: true,
 * });
 */
