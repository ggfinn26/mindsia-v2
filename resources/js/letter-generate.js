window.letterGeneratorForm = function (config) {
    return {
        isSubmitting: false,

        templates: config.templates || [],
        branches: config.branches || [],
        employees: config.employees || [],

        templateVars: [],
        values: {},

        selectedTemplateId: config.old.template_id || "",
        selectedBranchId: config.old.branch_id || "",
        selectedEmployeeId: config.old.employee_id || "",
        selectedSignerId: config.old.signer_id || "",

        searchTemplate: "",
        searchBranch: "",
        searchEmployee: "",
        searchSigner: "",
        showTemplateDD: false,
        showBranchDD: false,
        showEmployeeDD: false,
        showSignerDD: false,

        // ── Filtered lists ──────────────────────────────────────────────────
        get filteredTemplates() {
            const q = this.searchTemplate.toLowerCase();
            return q ? this.templates.filter((t) => t.name.toLowerCase().includes(q)) : this.templates;
        },
        get filteredBranches() {
            const q = this.searchBranch.toLowerCase();
            return q ? this.branches.filter((b) => b.name.toLowerCase().includes(q)) : this.branches;
        },
        get filteredEmployees() {
            const q = this.searchEmployee.toLowerCase();
            return q ? this.employees.filter((e) => e.name.toLowerCase().includes(q)) : this.employees;
        },
        get filteredSigners() {
            const q = this.searchSigner.toLowerCase();
            return q ? this.employees.filter((e) => e.name.toLowerCase().includes(q)) : this.employees;
        },

        // ── Display names ───────────────────────────────────────────────────
        get selectedTemplateName() {
            const t = this.templates.find((x) => x.id == this.selectedTemplateId);
            return t ? t.name : "— Pilih template —";
        },
        get selectedBranchName() {
            const b = this.branches.find((x) => x.id == this.selectedBranchId);
            return b ? b.name : "— Pilih cabang —";
        },
        get selectedEmployeeName() {
            const e = this.employees.find((x) => x.id == this.selectedEmployeeId);
            return e ? e.name : "— Pilih pegawai —";
        },
        get selectedSignerName() {
            const e = this.employees.find((x) => x.id == this.selectedSignerId);
            return e ? e.name : "— Bawaan dari pengaturan sistem —";
        },

        // ── Var type helpers ─────────────────────────────────────────────────
        hasType(type) {
            return this.templateVars.some((v) => v.type === type);
        },
        varsByType(...types) {
            return this.templateVars.filter((v) => types.includes(v.type));
        },

        get showBranch() {
            return this.hasType("branch");
        },
        get showEmployee() {
            return this.hasType("employee");
        },
        get showSigner() {
            return this.hasType("signer");
        },
        get autoVars() {
            return this.varsByType("auto");
        },
        get dateVars() {
            return this.varsByType("date");
        },
        get textVars() {
            return this.varsByType("text");
        },
        get derivedVars() {
            return this.varsByType("employee_derived", "signer_derived");
        },
        get repeatableVars() {
            return this.varsByType("repeatable");
        },

        // ── Template selection ───────────────────────────────────────────────
        async selectTemplate(id) {
            this.selectedTemplateId = id;
            this.showTemplateDD = false;
            this.searchTemplate = "";
            this.templateVars = [];
            this.values = { ...(config.old.manual_vars || {}) };
            if (!id) return;
            try {
                const res = await fetch(`${config.templateVarsUrl}?template_id=${id}`);
                const data = await res.json();
                this.templateVars = data.vars || [];
                // Default tanggal_surat = today
                this.templateVars
                    .filter((v) => v.type === "date")
                    .forEach((v) => {
                        if (!this.values[v.key]) {
                            this.values[v.key] = new Date().toISOString().split("T")[0];
                        }
                    });
                // Re-apply existing selections
                if (this.selectedBranchId) this._applyBranch(this.selectedBranchId);
                if (this.selectedEmployeeId) this._applyEmployee(this.selectedEmployeeId);
                if (this.selectedSignerId) this._applySigner(this.selectedSignerId);
            } catch (err) {
                console.warn("Gagal load template vars:", err);
            }
        },

        // ── Branch selection ─────────────────────────────────────────────────
        selectBranch(id) {
            this.selectedBranchId = id;
            this.showBranchDD = false;
            this.searchBranch = "";
            this._applyBranch(id);
        },
        _applyBranch(id) {
            const b = this.branches.find((x) => x.id == id);
            if (b && this.templateVars.some((v) => v.key === "kota_cabang")) {
                this.values["kota_cabang"] = b.name;
            }
        },

        // ── Employee selection ───────────────────────────────────────────────
        selectEmployee(id) {
            this.selectedEmployeeId = id;
            this.showEmployeeDD = false;
            this.searchEmployee = "";
            this._applyEmployee(id);
        },
        _applyEmployee(id) {
            const emp = this.employees.find((x) => x.id == id);
            if (!emp) return;
            const map = {
                nama_pegawai: emp.name,
                jabatan_pegawai: emp.jabatan_pegawai,
                divisi_pegawai: emp.divisi_pegawai,
                agama_pegawai: emp.agama_pegawai,
                alamat_pegawai: emp.alamat_pegawai,
                email_pegawai: emp.email_pegawai,
                wa_pegawai: emp.wa_pegawai,
                nomor_identitas_pegawai: emp.nomor_identitas_pegawai,
                tempat_tanggal_lahir_pegawai: emp.tempat_tanggal_lahir_pegawai,
                laki_laki_atau_perempuan: emp.laki_laki_atau_perempuan,
                tanggal_mulai_kontrak: emp.tanggal_mulai_kontrak,
                tanggal_berakhir_kontrak: emp.tanggal_berakhir_kontrak,
            };
            this.templateVars
                .filter((v) => v.type === "employee" || v.type === "employee_derived")
                .forEach((v) => {
                    this.values[v.key] = map[v.key] ?? "";
                });
        },

        // ── Signer selection ─────────────────────────────────────────────────
        selectSigner(id) {
            this.selectedSignerId = id;
            this.showSignerDD = false;
            this.searchSigner = "";
            this._applySigner(id);
        },
        _applySigner(id) {
            const emp = this.employees.find((x) => x.id == id);
            if (!emp) return;
            const map = {
                nama_penandatangan: emp.name,
                jabatan_penandatangan: emp.jabatan_pegawai,
                email_penandatangan: emp.email_pegawai,
            };
            this.templateVars
                .filter((v) => v.type === "signer" || v.type === "signer_derived")
                .forEach((v) => {
                    this.values[v.key] = map[v.key] ?? "";
                });
        },

        // ── Repeatable rows ──────────────────────────────────────────────────
        getRows(key) {
            const v = this.values[key];
            if (Array.isArray(v)) return v;
            return [typeof v === "string" ? v : ""];
        },
        addRow(key) {
            this.values[key] = [...this.getRows(key), ""];
        },
        removeRow(key, i) {
            const rows = this.getRows(key);
            rows.splice(i, 1);
            this.values[key] = rows.length ? rows : [""];
        },
        updateRow(key, i, val) {
            const rows = this.getRows(key);
            rows[i] = val;
            this.values[key] = rows;
        },

        init() {
            this.values = { ...(config.old.manual_vars || {}) };
            if (this.selectedTemplateId) this.selectTemplate(this.selectedTemplateId);
        },
    };
};
