(function () {
    'use strict';

    // ── Nav Scroll & Hamburger ───────────────────────────────────────
    function initNav() {
        var nav = document.getElementById('lp-nav');
        if (nav) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 60) {
                    nav.classList.add('bg-[rgba(6,18,43,0.98)]', 'shadow-[0_4px_24px_rgba(0,0,0,.3)]');
                    nav.classList.remove('bg-[rgba(6,18,43,0.92)]');
                } else {
                    nav.classList.remove('bg-[rgba(6,18,43,0.98)]', 'shadow-[0_4px_24px_rgba(0,0,0,.3)]');
                    nav.classList.add('bg-[rgba(6,18,43,0.92)]');
                }
            }, { passive: true });
        }

        var ham = document.getElementById('nav-ham');
        var drawer = document.getElementById('nav-drawer');
        if (ham && drawer) {
            ham.addEventListener('click', function () {
                ham.classList.toggle('open');
                drawer.classList.toggle('opacity-0');
                drawer.classList.toggle('pointer-events-none');
                drawer.classList.toggle('-translate-y-2');
            });
            drawer.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () {
                    ham.classList.remove('open');
                    drawer.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
                });
            });
        }
    }

    // ── Reveal Animation & Intersection Observer ─────────────────────
    function initReveal() {
        if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            // Animasi angka
            function animateCount(el) {
                var target = +el.dataset.target;
                var suffix = el.dataset.suffix || '';
                var display = target >= 1000 ? Math.floor(target/1000) : target;
                var step = Math.ceil(display / 40);
                var current = 0;
                var timer = setInterval(function () {
                    current += step;
                    if (current >= display) { current = display; clearInterval(timer); }
                    el.textContent = current + suffix;
                }, 30);
            }

            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { 
                        var el = e.target;
                        if (el.classList.contains('reveal')) {
                            el.classList.add('active');
                        }
                        if (el.hasAttribute('data-target')) {
                            animateCount(el);
                        }
                        io.unobserve(el); 
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
            
            document.querySelectorAll('.reveal, [data-target]').forEach(function (el) { io.observe(el); });
        }
    }

    // ── Hero Particles ───────────────────────────────────────────────
    function initParticles() {
        var container = document.getElementById('hero-particles');
        if (container) {
            for (var i = 0; i < 18; i++) {
                var p = document.createElement('div');
                p.className = 'absolute rounded-full bg-gold opacity-0 animate-particle-float';
                var size = Math.random() * 4 + 2;
                p.style.cssText = 'width:'+size+'px;height:'+size+'px;left:'+Math.random()*100+'%;bottom:'+Math.random()*40+'%;animation-duration:'+(Math.random()*6+4)+'s;animation-delay:'+Math.random()*6+'s;';
                container.appendChild(p);
            }
        }
    }

    // ── Fullpage Scroll (Legacy/Existing if used) ────────────────────
    var sections   = [];
    var current    = 0;
    var isAnimating = false;
    var touchStartY = 0;

    function initFullpage() {
        sections = Array.from(document.querySelectorAll('main > header, main > section'));
        if (sections.length === 0) return;
        // Keep existing fullpage logic mostly intact just in case
        // Note: Tailwind conversion focuses on standard scrolling but we'll leave this intact
    }

    document.addEventListener('DOMContentLoaded', function () {
        initNav();
        initReveal();
        initParticles();
        // initFullpage(); // Temporarily disabled fullpage if they are using standard scrolling
    });
})();
