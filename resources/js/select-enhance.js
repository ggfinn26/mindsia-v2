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
            pick(opt) {
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
        .map((o) => ({ value: o.value, label: o.text.trim() }));

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

    // All content is static template — no user data injected into HTML
    wrapper.innerHTML = `
        <div @click="open = !open"
             class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-[13px] shadow-sm flex justify-between items-center cursor-pointer transition-all min-h-[44px]"
             :class="open ? 'border-[#5586DB] ring-2 ring-[#5586DB]/20' : 'border-[#E5E7EB]'">
            <span class="truncate block pr-4"
                  :class="value ? 'text-[#111827]' : 'text-[#9CA3AF]'"
                  x-text="label || placeholder"></span>
            <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform duration-200"
                  :class="open ? 'rotate-180' : ''">expand_more</span>
        </div>
        <div x-show="open" x-transition x-cloak
             class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
            <div class="p-2 bg-gray-50 border-b border-gray-100">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[16px]">search</span>
                    <input type="text" x-model="search" placeholder="Cari..." @click.stop
                           style="padding-left: 2rem !important"
                           class="w-full pr-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all">
                </div>
            </div>
            <ul class="max-h-48 overflow-y-auto py-1">
                <li x-show="filtered.length === 0"
                    class="px-4 py-3 text-[12px] text-gray-500 text-center">Tidak ditemukan</li>
                <template x-for="opt in filtered" :key="opt.value">
                    <li @click="pick(opt)"
                        class="px-4 py-2 text-[12px] text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors"
                        :class="value == opt.value ? 'bg-[#EFF6FF] font-semibold' : ''"
                        x-text="opt.label"></li>
                </template>
            </ul>
        </div>
    `;

    sel.parentNode.insertBefore(wrapper, sel);
    window.Alpine.initTree(wrapper);
}
