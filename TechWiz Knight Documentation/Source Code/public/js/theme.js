/**
 * MarketLink theme — light / dark
 * Persists to localStorage (ml-theme) and syncs icons + theme-color.
 */
(function () {
    'use strict';

    var KEY = 'ml-theme';

    function current() {
        return document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    }

    function apply(theme, persist) {
        var next = theme === 'dark' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', next);
        document.documentElement.style.colorScheme = next;
        if (persist !== false) {
            try { localStorage.setItem(KEY, next); } catch (e) {}
        }
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            meta.setAttribute('content', next === 'dark' ? '#101612' : '#1f6b45');
        }
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            var icon = btn.querySelector('i');
            if (icon) {
                icon.className = next === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
            }
            btn.setAttribute('aria-label', next === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
            btn.setAttribute('title', next === 'dark' ? 'Light mode' : 'Dark mode');
            btn.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false');
        });
        try {
            window.dispatchEvent(new CustomEvent('ml-theme-change', { detail: { theme: next } }));
        } catch (e) {}
    }

    function boot() {
        var saved = null;
        try { saved = localStorage.getItem(KEY); } catch (e) {}
        if (saved !== 'dark' && saved !== 'light') {
            saved = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
        }
        apply(saved, false);
    }

    window.mlTheme = function (force) {
        if (force === 'dark' || force === 'light') {
            apply(force, true);
            return;
        }
        apply(current() === 'dark' ? 'light' : 'dark', true);
    };

    window.mlThemeApply = apply;
    boot();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { apply(current(), false); });
    } else {
        apply(current(), false);
    }
})();
