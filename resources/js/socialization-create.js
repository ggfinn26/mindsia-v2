window.__socCfg = window.__socCfg || {};

document.addEventListener("alpine:init", () => {
    window.Alpine.data("socForm", () => {
        const cfg = window.__socCfg;
        const branchRegionMap = cfg.branchRegionMap || {};
        const allInstitutions = cfg.allInstitutions || [];
        const defaultInstitutions = cfg.defaultInstitutions || allInstitutions;
        const isBranchLevel = cfg.isBranchLevel || false;

        // Sync initial branch selection from branchPicker's old() values
        const initBranches = (window.__branchPickerCfg?.selected || []).map(Number);

        return {
            selectedBranches: initBranches,
            selectedInstitution: cfg.oldInstitution ? String(cfg.oldInstitution) : "",
            isBranchLevel,

            get filteredInstitutions() {
                if (isBranchLevel) {
                    return defaultInstitutions;
                }
                if (this.selectedBranches.length === 0) {
                    return [];
                }
                const regionIds = new Set(this.selectedBranches.map((id) => branchRegionMap[id]).filter(Boolean));
                return allInstitutions.filter((i) => regionIds.has(i.region_id));
            },

            init() {
                window.addEventListener("branches-changed", (e) => {
                    this.selectedBranches = e.detail;
                    // Reset institution if no longer in filtered list
                    const ids = this.filteredInstitutions.map((i) => String(i.id));
                    if (this.selectedInstitution && !ids.includes(this.selectedInstitution)) {
                        this.selectedInstitution = "";
                    }
                });
            },
        };
    });
});
