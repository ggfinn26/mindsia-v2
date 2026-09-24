window.__branchPickerCfg = window.__branchPickerCfg || {};

document.addEventListener("alpine:init", () => {
    window.Alpine.data("branchPicker", () => {
        const cfg = window.__branchPickerCfg;
        const allIds = cfg.allIds || [];
        const allNames = cfg.allNames || [];
        return {
            selected: cfg.selected || [],
            search: "",
            get allSelected() {
                return allIds.length > 0 && allIds.every((id) => this.selected.includes(id));
            },
            get noResults() {
                const s = this.search.toLowerCase();
                return allNames.every((name) => !name.toLowerCase().includes(s));
            },
            toggleAll() {
                const s = this.search.toLowerCase();
                const visible = allIds.filter((_, i) => allNames[i].toLowerCase().includes(s));
                const allVisibleSelected = visible.every((id) => this.selected.includes(id));
                if (allVisibleSelected) {
                    this.selected = this.selected.filter((id) => !visible.includes(id));
                } else {
                    this.selected = [...new Set([...this.selected, ...visible])];
                }
            },
        };
    });
});
