document.addEventListener("alpine:init", () => {
    window.Alpine.data("galleryCoverflow", (config) => {
        // config: number (legacy count, no filter) or { count } or { cats: [catIdPerSlide] }
        const cfg = typeof config === "number" ? { count: config } : config || {};
        const cats = cfg.cats || null;
        const total = cats ? cats.length : cfg.count || 0;

        return {
            activeCat: null, // null = "Semua"
            pos: 0, // active position within the visible list

            // slide indexes visible under the current category filter
            get visible() {
                if (!cats || this.activeCat === null) {
                    return Array.from({ length: total }, (_, i) => i);
                }
                return cats.map((c, i) => (String(c) === String(this.activeCat) ? i : -1)).filter((i) => i >= 0);
            },
            get count() {
                return this.visible.length;
            },
            get activeSlide() {
                return this.visible[this.pos];
            },

            setCat(cat) {
                this.activeCat = cat;
                this.pos = 0;
            },
            next() {
                if (this.count) this.pos = (this.pos + 1) % this.count;
            },
            prev() {
                if (this.count) this.pos = (this.pos - 1 + this.count) % this.count;
            },
            go(slideIndex) {
                const p = this.visible.indexOf(slideIndex);
                if (p >= 0) this.pos = p;
            },

            // shortest signed distance of a slide from center, within the visible list
            offsetOf(slideIndex) {
                const p = this.visible.indexOf(slideIndex);
                if (p < 0) return null;
                let d = p - this.pos;
                const n = this.count;
                if (d > n / 2) d -= n;
                if (d < -n / 2) d += n;
                return d;
            },

            slideStyle(slideIndex) {
                const o = this.offsetOf(slideIndex);
                if (o === null || Math.abs(o) > 2) {
                    return `transform: translateX(-50%) scale(0.4); opacity: 0; pointer-events: none; z-index: 0;`;
                }
                const abs = Math.abs(o);
                const translate = o * 62;
                const scale = 1 - abs * 0.16;
                const rotate = o * -6;
                const opacity = abs === 0 ? 1 : 0.55;
                const z = 10 - abs;
                return `transform: translateX(calc(-50% + ${translate}%)) scale(${scale}) rotateY(${rotate}deg); opacity: ${opacity}; z-index: ${z};`;
            },
        };
    });
});
