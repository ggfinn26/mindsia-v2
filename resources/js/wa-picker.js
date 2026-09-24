let _waTemplatesCache = null;

async function loadWaTemplates() {
    if (_waTemplatesCache !== null) return _waTemplatesCache;
    try {
        const res = await fetch("/wa-templates/json", {
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
        });
        _waTemplatesCache = res.ok ? await res.json() : [];
    } catch {
        _waTemplatesCache = [];
    }
    return _waTemplatesCache;
}

document.addEventListener("alpine:init", () => {
    window.Alpine.data("waPicker", (lead) => ({
        open: false,
        templates: [],

        async init() {
            this.templates = await loadWaTemplates();
        },

        fillTemplate(body) {
            return body
                .replace(/\{nama\}/gi, lead.nama)
                .replace(/\{wa\}/gi, lead.wa)
                .replace(/\{institusi\}/gi, lead.institusi)
                .replace(/\{sosialisasi\}/gi, lead.sosialisasi)
                .replace(/\{status\}/gi, lead.status);
        },

        send(template) {
            this.open = false;
            const base = `https://wa.me/${lead.wa}`;
            if (!template) {
                window.open(base, "_blank", "noopener");
                return;
            }
            const text = this.fillTemplate(template.body);
            window.open(`${base}?text=${encodeURIComponent(text)}`, "_blank", "noopener");
        },
    }));
});
