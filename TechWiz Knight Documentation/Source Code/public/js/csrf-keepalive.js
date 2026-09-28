/**
 * Keep CSRF tokens fresh and recover from expired sessions without a scary 419 page.
 */
(function () {
  function meta() {
    return document.querySelector('meta[name="csrf-token"]');
  }

  function readCookie(name) {
    var m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
    return m ? decodeURIComponent(m[1]) : '';
  }

  function currentToken() {
    var el = meta();
    return (el && el.content) || '';
  }

  function setToken(token) {
    if (!token) return;
    var el = meta();
    if (el) el.content = token;
    document.querySelectorAll('input[name="_token"]').forEach(function (input) {
      input.value = token;
    });
    document.querySelectorAll('[data-csrf]').forEach(function (node) {
      node.setAttribute('data-csrf', token);
    });
  }

  function xsrfHeader() {
    var fromCookie = readCookie('XSRF-TOKEN');
    return fromCookie || currentToken();
  }

  window.mlCsrf = {
    token: currentToken,
    refresh: function () {
      return fetch((document.body.getAttribute('data-csrf-url') || '/csrf-token'), {
        method: 'GET',
        credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      })
        .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
        .then(function (data) {
          if (data && data.token) setToken(data.token);
          return data && data.token;
        })
        .catch(function () { return currentToken(); });
    },
    headers: function () {
      var t = currentToken();
      var h = {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
      };
      if (t) h['X-CSRF-TOKEN'] = t;
      var xsrf = xsrfHeader();
      if (xsrf) h['X-XSRF-TOKEN'] = xsrf;
      return h;
    },
  };

  // Sync hidden _token fields right before any form POST
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || !form.tagName || form.tagName.toLowerCase() !== 'form') return;
    var method = (form.getAttribute('method') || 'get').toLowerCase();
    if (method === 'get') return;
    var token = currentToken() || xsrfHeader();
    if (!token) return;
    var input = form.querySelector('input[name="_token"]');
    if (input) input.value = currentToken() || token;
  }, true);

  // Soft-refresh token every 15 minutes while tab is open
  setInterval(function () {
    if (document.hidden) return;
    if (window.mlCsrf && window.mlCsrf.refresh) window.mlCsrf.refresh();
  }, 15 * 60 * 1000);

  // If a fetch gets 419, refresh once then tell caller via custom event
  var origFetch = window.fetch;
  if (typeof origFetch === 'function') {
    window.fetch = function () {
      var args = arguments;
      return origFetch.apply(this, args).then(function (res) {
        if (res.status !== 419) return res;
        return window.mlCsrf.refresh().then(function () {
          // One silent retry for same-origin POSTs that used CSRF headers
          try {
            var init = args[1] || {};
            var method = String(init.method || 'GET').toUpperCase();
            if (method === 'GET' || method === 'HEAD') {
              window.location.reload();
              return res;
            }
            var headers = new Headers(init.headers || {});
            var t = currentToken();
            if (t) headers.set('X-CSRF-TOKEN', t);
            var xsrf = xsrfHeader();
            if (xsrf) headers.set('X-XSRF-TOKEN', xsrf);
            init.headers = headers;
            init.credentials = init.credentials || 'same-origin';
            return origFetch.call(this, args[0], init);
          } catch (err) {
            window.location.reload();
            return res;
          }
        });
      });
    };
  }
})();
