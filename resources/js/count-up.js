/**
 * Animasi angka 0 sampai target untuk elemen [data-count-to].
 * Markup berisi angka akhir sebagai fallback (tanpa JS / reduced motion).
 * Opsional: data-count-suffix="+" untuk akhiran.
 */
const DURATION_MS = 1600;
const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

function animate(el) {
    const target = Number(el.dataset.countTo);
    const suffix = el.dataset.countSuffix ?? "";
    const format = (n) => Math.round(n).toLocaleString("id-ID") + suffix;
    const start = performance.now();

    const tick = (now) => {
        const progress = Math.min((now - start) / DURATION_MS, 1);
        el.textContent = format(target * easeOutCubic(progress));
        if (progress < 1) {
            requestAnimationFrame(tick);
        }
    };
    requestAnimationFrame(tick);
}

document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll("[data-count-to]");
    if (
        !counters.length ||
        window.matchMedia("(prefers-reduced-motion: reduce)").matches
    ) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    observer.unobserve(entry.target);
                    animate(entry.target);
                }
            });
        },
        { threshold: 0.4 },
    );

    counters.forEach((el) => {
        el.textContent = "0" + (el.dataset.countSuffix ?? "");
        observer.observe(el);
    });
});
