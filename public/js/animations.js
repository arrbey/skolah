/**
 * ═══════════════════════════════════════════════════════════════
 * Scroll Reveal — Vanilla JS with IntersectionObserver
 * No dependencies. Just add class "reveal" to any element.
 * ═══════════════════════════════════════════════════════════════
 */
(function () {
    'use strict';

    // ── Scroll Reveal ────────────────────────────────────────
    var selectors = '.reveal, .reveal-left, .reveal-right, .reveal-scale';

    function initReveal() {
        var elements = document.querySelectorAll(selectors);
        if (!elements.length) return;

        if (!('IntersectionObserver' in window)) {
            // Fallback: show everything immediately
            elements.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -40px 0px'
        });

        elements.forEach(function (el) {
            observer.observe(el);
        });
    }

    // ── Scroll Progress Bar ──────────────────────────────────
    function initScrollProgress() {
        var bar = document.getElementById('scroll-progress');
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'scroll-progress';
            document.body.appendChild(bar);
        }

        window.addEventListener('scroll', function () {
            var scrollTop = window.scrollY || document.documentElement.scrollTop;
            var docHeight = document.documentElement.scrollHeight - window.innerHeight;
            var progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
            bar.style.width = progress + '%';
        }, { passive: true });
    }

    // ── Init on DOM ready ────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initReveal();
            initScrollProgress();
        });
    } else {
        initReveal();
        initScrollProgress();
    }
})();
