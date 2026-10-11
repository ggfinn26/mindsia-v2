// Global config store — avoids injecting JSON into HTML attributes (XSS-safe)
window.__selectCfg = window.__selectCfg || {};
let _selectId = 0;

document.addEventListener("alpine:init", () => {
    window.Alpine.data("customSelect", (id) => {
        const cfg = window.__selectCfg[id] || { value: "", label: "", placeholder: "Pilih...", options: [] };
        const native = cfg._native || null;
        return {
            open: false,
            search: "",
            value: cfg.value,
            label: cfg.label,
            options: cfg.options,
            placeholder: cfg.placeholder,
            get filtered() {
                if (!this.search) return this.options;
                const s = this.search.toLowerCase();
                return this.options.filter((o) => o.label.toLowerCase().includes(s));
            },
            init() {
                if (native) {
                    const sync = () => {
                        const val = native.value;
                        if (this.value !== val) {
                            this.value = val;
                            const opt = this.options.find((o) => o.value == val);
                            this.label = opt ? opt.label : (val ? val : "");
                        }
                    };
                    native.addEventListener("change", sync);
                    native.addEventListener("input", sync);
                    setInterval(sync, 150);
                }
            },
            pick(opt) {
                if (opt.disabled) return;
                this.value = opt.value;
                this.label = opt.label;
                this.open = false;
                this.search = "";
                if (native) {
                    native.value = opt.value;
                    native.dispatchEvent(new Event("change", { bubbles: true }));
                }
            },
        };
    });
});

document.addEventListener("alpine:initialized", () => {
    document
        .querySelectorAll("select:not([data-enhanced]):not([data-no-enhance]):not([data-with-semua])")
        .forEach(enhanceSelect);

    new MutationObserver((muts) => {
        muts.forEach((m) =>
            m.addedNodes.forEach((node) => {
                if (node.nodeType !== 1) return;
                if (
                    node.tagName === "SELECT" &&
                    !node.dataset.enhanced &&
                    !node.dataset.noEnhance &&
                    !node.dataset.withSemua
                ) {
                    enhanceSelect(node);
                }
                node.querySelectorAll?.(
                    "select:not([data-enhanced]):not([data-no-enhance]):not([data-with-semua])",
                ).forEach(enhanceSelect);
            }),
        );
    }).observe(document.body, { childList: true, subtree: true });
});

window.enhanceSelect = enhanceSelect;
function enhanceSelect(sel) {
    if (sel.dataset.enhanced || sel.dataset.noEnhance) return;
    sel.dataset.enhanced = "1";

    const options = Array.from(sel.options)
        .filter((o) => o.value !== "")
        .map((o) => ({ value: o.value, label: o.text.trim(), disabled: o.disabled }));

    const emptyOpt = Array.from(sel.options).find((o) => o.value === "");
    const placeholder = emptyOpt?.text.trim() || "Pilih...";
    const selectedOpt = sel.options[sel.selectedIndex];
    const hasValue = selectedOpt && selectedOpt.value !== "";

    const id = "sel_" + _selectId++;
    window.__selectCfg[id] = {
        value: hasValue ? selectedOpt.value : "",
        label: hasValue ? selectedOpt.text.trim() : "",
        placeholder,
        options,
        _native: sel,
    };

    Object.assign(sel.style, {
        position: "absolute",
        width: "1px",
        height: "1px",
        opacity: "0",
        overflow: "hidden",
        pointerEvents: "none",
    });
    sel.removeAttribute("required");
    sel.setAttribute("tabindex", "-1");

    const wrapper = document.createElement("div");
    wrapper.className = "relative";
    wrapper.setAttribute("x-data", `customSelect('${id}')`);
    wrapper.setAttribute("@click.away", "open = false");

    // Transfer x-show from native select so visibility bindings work on the wrapper
    const xShow = sel.getAttribute("x-show");
    if (xShow) {
        wrapper.setAttribute("x-show", xShow);
        sel.removeAttribute("x-show");
    }

    // All content is static template — no user data injected into HTML
    wrapper.innerHTML = `
        <div @click="open = !open"
             class="w-full px-3.5 py-2.5 rounded-xl text-[13px] shadow-sm flex justify-between items-center cursor-pointer transition-all min-h-[44px]"
             :style="open
                 ? 'background-color:var(--dash-input-bg);border:1px solid var(--dash-primary);box-shadow:0 0 0 2px color-mix(in srgb,var(--dash-primary) 20%,transparent);'
                 : 'background-color:var(--dash-input-bg);border:1px solid var(--dash-input-border);'">
            <span class="truncate block pr-4 text-[13px]"
                  :style="value ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-faint);'"
                  x-text="label || placeholder"></span>
            <span class="material-symbols-outlined text-[16px] transition-transform duration-200"
                  style="color: var(--dash-text-muted);"
                  :class="open ? 'rotate-180' : ''">expand_more</span>
        </div>
        <div x-show="open" x-transition x-cloak
             class="absolute z-50 w-full mt-2 rounded-xl shadow-xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="p-2 border-b rounded-t-xl" style="background-color: var(--dash-primary-bg); border-color: var(--dash-card-border);">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                    <input type="text" x-model="search" placeholder="Cari..." @click.stop
                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading);"
                           class="w-full pr-3 py-2 rounded-lg text-[13px] focus:outline-none transition-all border">
                </div>
            </div>
            <ul class="max-h-48 overflow-y-auto py-1 rounded-b-xl">
                <li x-show="filtered.length === 0"
                    class="px-4 py-3 text-[12px] text-center"
                    style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                <template x-for="opt in filtered" :key="opt.value">
                    <li @click="pick(opt)"
                        class="px-4 py-2 text-[12px] transition-colors"
                        :class="opt.disabled ? 'cursor-not-allowed italic' : 'cursor-pointer'"
                        :style="opt.disabled
                            ? 'color: var(--dash-text-faint); background-color: var(--dash-primary-bg);'
                            : (value == opt.value
                                ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary-text); font-weight: 600;'
                                : 'color: var(--dash-text-body);')"
                        @mouseover="if(!opt.disabled && value != opt.value) $el.style.backgroundColor='var(--dash-primary)'; $el.style.color='#ffffff';"
                        @mouseout="if(!opt.disabled && value != opt.value) $el.style.backgroundColor=''; $el.style.color='var(--dash-text-body)';"
                        x-text="opt.label"></li>
                </template>
            </ul>
        </div>
    `;

    sel.parentNode.insertBefore(wrapper, sel);
    window.Alpine.initTree(wrapper);
}
