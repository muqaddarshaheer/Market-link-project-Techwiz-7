(function () {
    'use strict';

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.content;
        const input = document.querySelector('input[name="_token"]');
        return input ? input.value : '';
    }

    function toast(message, kind) {
        const stack = document.getElementById('toastStack');
        if (!stack || !message) return;
        const el = document.createElement('div');
        el.className = 'ml-toast' + (kind === 'error' ? ' is-error' : ' is-ok');
        el.setAttribute('role', 'status');
        el.textContent = message;
        stack.appendChild(el);
        requestAnimationFrame(function () { el.classList.add('is-in'); });
        setTimeout(function () {
            el.classList.remove('is-in');
            setTimeout(function () { el.remove(); }, 220);
        }, 2800);
    }

    function setCartBadge(count) {
        const n = Math.max(0, parseInt(count, 10) || 0);
        document.querySelectorAll('[data-cart-badge]').forEach(function (badge) {
            badge.textContent = String(n);
            badge.hidden = n < 1;
            badge.classList.toggle('d-none', n < 1);
        });
        document.querySelectorAll('[data-cart-link]').forEach(function (link) {
            link.setAttribute('data-count', String(n));
        });
    }

    document.addEventListener('submit', function (event) {
        const form = event.target.closest('form[data-ajax-cart]');
        if (!form) return;
        event.preventDefault();
        const btn = event.submitter || form.querySelector('[type="submit"]');
        if (btn) btn.disabled = true;
        const action = form.getAttribute('action');
        const methodInput = form.querySelector('input[name="_method"]');
        const method = (methodInput ? methodInput.value : form.method || 'POST').toUpperCase();
        const body = typeof FormData === 'function' && event.submitter
            ? new FormData(form, event.submitter)
            : new FormData(form);
        if (!body.has('_token')) body.append('_token', csrfToken());
        fetch(action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: body,
            credentials: 'same-origin',
        }).then(function (res) {
            return res.json().then(function (data) {
                if (!res.ok || data.ok === false) throw new Error(data.message || 'Something went wrong.');
                return data;
            });
        }).then(function (data) {
            toast(data.message || 'Updated', 'ok');
            if (typeof data.count !== 'undefined') setCartBadge(data.count);
            if (/\/cart|\/guest\/cart/.test(window.location.pathname)) {
                window.location.reload();
            }
        }).catch(function (err) {
            toast(err.message || 'Could not update cart', 'error');
        }).finally(function () {
            if (btn) btn.disabled = false;
        });
    });

    function bindLiveFilters() {
        const form = document.querySelector('form[data-live-filter]');
        const results = document.getElementById('productResults');
        if (!form || !results) return;

        let timer = null;
        let seq = 0;

        async function refresh() {
            const id = ++seq;
            const params = new URLSearchParams(new FormData(form));
            params.set('partial', '1');
            results.classList.add('is-loading');
            try {
                const res = await fetch(form.action + '?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    credentials: 'same-origin',
                });
                const html = await res.text();
                if (id !== seq) return;
                results.innerHTML = html;
                params.delete('partial');
                const clean = params.toString();
                history.replaceState({}, '', form.action + (clean ? '?' + clean : ''));
                revealShopCards();
            } catch (e) {
                toast('Could not refresh products', 'error');
            } finally {
                if (id === seq) results.classList.remove('is-loading');
            }
        }

        function schedule() {
            clearTimeout(timer);
            timer = setTimeout(refresh, 280);
        }

        form.querySelectorAll('[data-filter-live]').forEach(function (el) {
            el.addEventListener('change', schedule);
            if (el.matches('input[type="text"], input[type="search"], input:not([type]), input[type="number"]')) {
                el.addEventListener('input', schedule);
            }
        });

        form.addEventListener('submit', function (e) {
            if (window.matchMedia('(min-width: 992px)').matches) {
                e.preventDefault();
                refresh();
            }
        });

        results.addEventListener('click', function (e) {
            const pageLink = e.target.closest('.product-pagination a');
            if (!pageLink) return;
            e.preventDefault();
            const url = new URL(pageLink.href, window.location.origin);
            url.searchParams.set('partial', '1');
            results.classList.add('is-loading');
            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin',
            }).then(function (res) { return res.text(); }).then(function (html) {
                results.innerHTML = html;
                url.searchParams.delete('partial');
                history.replaceState({}, '', url.pathname + url.search);
                revealShopCards();
            }).finally(function () {
                results.classList.remove('is-loading');
            });
        });
    }

    function revealShopCards() {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const nodes = document.querySelectorAll('.shop-reveal');
        if (reduce || !('IntersectionObserver' in window)) {
            nodes.forEach(function (n) { n.classList.add('is-visible'); });
            return;
        }
        const io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            });
        }, { threshold: 0.08, rootMargin: '40px 0px' });
        nodes.forEach(function (n) { io.observe(n); });
    }

    function bindFilterDrawer() {
        const toggle = document.getElementById('shopFilterToggle');
        const panel = document.getElementById('productFilters');
        if (!toggle || !panel) return;
        toggle.addEventListener('click', function () {
            const open = panel.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            document.body.classList.toggle('shop-filters-open', open);
        });
    }

    function bindCheckoutGuard() {
        document.querySelectorAll('form[data-checkout-guard]').forEach(function (form) {
            form.addEventListener('submit', function () {
                const btn = form.querySelector('[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Placing…';
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindLiveFilters();
        bindFilterDrawer();
        bindCheckoutGuard();
        revealShopCards();
    });

    window.mlToast = toast;
    window.mlSetCartBadge = setCartBadge;
})();
