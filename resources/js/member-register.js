window.memberRegisterPortal = function () {
    const cfg = window.__memberRegisterConfig ?? {};
    const provinces = cfg.provinces ?? [];
    const oldData = cfg.old ?? {};
    const errorStep = cfg.errorStep ?? 0;

    const MAX_BIRTHDATE = (() => {
        const d = new Date();
        d.setFullYear(d.getFullYear() - 6);
        return d.toISOString().split("T")[0];
    })();

    function getVal(id) {
        const el = document.getElementById(id);
        return el ? el.value.trim() : "";
    }

    const PASSWORD_RE = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    const EDUCATION_TO_JENJANG = {
        SD: "SD",
        SMP: "SMP,MTS",
        SMA: "SMA,MA",
        SMK: "SMK",
        D1: "PERGURUAN TINGGI",
        D2: "PERGURUAN TINGGI",
        D3: "PERGURUAN TINGGI",
        D4: "PERGURUAN TINGGI",
        S1: "PERGURUAN TINGGI",
        S2: "PERGURUAN TINGGI",
        S3: "PERGURUAN TINGGI",
    };
    const PARENT_FIELDS = [
        "father_name",
        "mother_name",
        "father_occupation",
        "mother_occupation",
        "father_whatsapp",
        "mother_whatsapp",
    ];

    return {
        step: 1,
        step1Valid: false,
        step2Valid: false,
        step3Valid: false,
        showErrors1: false,
        showErrors2: false,
        showErrors3: false,
        _tick: 0,
        _uniquenessErrors: {},
        regPassword: "",
        regPasswordConfirm: "",
        provinces,
        indoProvinces: [],
        indoCities: [],
        institutions: [],
        branches: [],
        form: {
            province_id: "",
            institution_id: "",
            branch_id: "",
            birthdate: oldData.birthdate ?? "",
            birth_place_province_id: oldData.birth_place_province_id ?? "",
            birth_place: oldData.birth_place ?? "",
            education_level: oldData.education_level ?? "",
            class_or_faculty: oldData.class_or_faculty ?? "",
        },
        institutionSearch: "",
        branchSearch: "",
        indoProvinceSearch: "",
        indoCitySearch: "",
        provinceSearch: "",
        showInstitutionDropdown: false,
        showBranchDropdown: false,
        showIndoProvinceDropdown: false,
        showIndoCityDropdown: false,
        showProvinceDropdown: false,
        showCustomInstitution: false,
        showEducationDropdown: false,

        educationOptions: [
            { group: "Sekolah", items: ["SD", "SMP", "SMA", "SMK"] },
            { group: "Perguruan Tinggi", items: ["D1", "D2", "D3", "D4", "S1", "S2", "S3"] },
            { group: "Lainnya", items: ["Umum"] },
        ],

        get selectedEducationLabel() {
            return this.form.education_level || "Pilih Jenjang...";
        },

        get classOrFacultyLabel() {
            return ["SD", "SMP", "SMA", "SMK"].includes(this.form.education_level) ? "Kelas" : "Jurusan / Fakultas";
        },

        get classOrFacultyPlaceholder() {
            return ["SD", "SMP", "SMA", "SMK"].includes(this.form.education_level)
                ? "Contoh: Kelas 10 IPA"
                : "Contoh: Teknik Informatika";
        },

        selectEducationLevel(level) {
            this.form.education_level = level;
            this.form.class_or_faculty = "";
            this.showEducationDropdown = false;

            if (level === "Umum") {
                // Umum = tidak sekolah/kuliah — skip institution picker
                this.form.institution_id = "";
                this.institutions = [];
                this.showCustomInstitution = true;
                const custom = document.getElementById("institution_name_custom");
                if (custom && !custom.value) custom.value = "Tidak Kuliah/Sekolah";
                this.checkValidities();
            } else {
                this.showCustomInstitution = false;
                const custom = document.getElementById("institution_name_custom");
                if (custom) custom.value = "";
                this.fetchInstitutions();
            }
        },

        get filteredInstitutions() {
            if (this.institutionSearch.trim() === "") return this.institutions.slice(0, 100);
            const q = this.institutionSearch.toLowerCase();
            return this.institutions.filter((i) => i.institution_name.toLowerCase().includes(q)).slice(0, 100);
        },

        get filteredBranches() {
            if (this.branchSearch.trim() === "") return this.branches;
            const q = this.branchSearch.toLowerCase();
            return this.branches.filter((b) => b.branch_name.toLowerCase().includes(q));
        },

        get filteredProvinces() {
            if (this.provinceSearch.trim() === "") return this.provinces;
            const q = this.provinceSearch.toLowerCase();
            return this.provinces.filter((p) => p.name.toLowerCase().includes(q));
        },

        get filteredIndoProvinces() {
            if (this.indoProvinceSearch.trim() === "") return this.indoProvinces;
            const q = this.indoProvinceSearch.toLowerCase();
            return this.indoProvinces.filter((p) => p.nama.toLowerCase().includes(q));
        },

        get filteredIndoCities() {
            if (this.indoCitySearch.trim() === "") return this.indoCities;
            const q = this.indoCitySearch.toLowerCase();
            return this.indoCities.filter((c) => c.nama.toLowerCase().includes(q));
        },

        async fetchIndonesianProvinces() {
            try {
                const response = await fetch("/api/public/indonesia-provinces");
                const data = await response.json();
                this.indoProvinces = Array.isArray(data) ? data : [];
            } catch (e) {
                console.error("Failed to fetch indo provinces:", e);
            }
        },

        async fetchIndonesianCities() {
            this.indoCities = [];
            this.form.birth_place = "";
            this.checkValidities();

            if (!this.form.birth_place_province_id) return;

            try {
                const response = await fetch(
                    `/api/public/indonesia-cities?province_id=${this.form.birth_place_province_id}`,
                );
                const data = await response.json();
                this.indoCities = Array.isArray(data) ? data : [];
            } catch (e) {
                console.error("Failed to fetch indo cities:", e);
            }
        },

        selectBranch(id) {
            this.form.branch_id = id;
            this.showBranchDropdown = false;
            this.branchSearch = "";
            this.checkValidities();
        },

        getSelectedBranchName() {
            if (!this.form.branch_id) return "Pilih Cabang MINDSIA...";
            const b = this.branches.find((b) => b.id == this.form.branch_id);
            return b ? b.branch_name : "Pilih Cabang MINDSIA...";
        },

        selectInstitution(id) {
            this.form.institution_id = id;
            const el = document.getElementById("institution_id");
            if (el) el.value = id;
            this.showInstitutionDropdown = false;
            const inst = this.institutions.find((i) => i.id == id);
            this.showCustomInstitution = inst?.institution_name === "Institusi tidak ditemukan";
            if (!this.showCustomInstitution) {
                const custom = document.getElementById("institution_name_custom");
                if (custom) custom.value = "";
            }
            this.checkValidities();
        },

        getSelectedInstitutionName() {
            const id = getVal("institution_id") || this.form.institution_id;
            if (!id) return "Pilih Institusi...";
            const inst = this.institutions.find((i) => i.id == id);
            return inst ? inst.institution_name : "Pilih Institusi...";
        },

        selectProvince(id) {
            this.form.province_id = id;
            this.showProvinceDropdown = false;
            this.provinceSearch = "";
            const el = document.getElementById("province_id");
            if (el) el.value = id;
            this.fetchBranches();
            this.fetchInstitutions();
            this.checkValidities();
        },

        getSelectedProvinceName() {
            if (!this.form.province_id) return "Pilih Provinsi...";
            const p = this.provinces.find((p) => p.id == this.form.province_id);
            return p ? p.name : "Pilih Provinsi...";
        },

        selectIndoProvince(id) {
            this.form.birth_place_province_id = id;
            this.showIndoProvinceDropdown = false;
            this.indoProvinceSearch = "";
            const el = document.getElementById("birth_place_province_id");
            if (el) el.value = id;
            this.fetchIndonesianCities();
            this.checkValidities();
        },

        getSelectedIndoProvinceName() {
            if (!this.form.birth_place_province_id) return "Pilih Provinsi...";
            const p = this.indoProvinces.find((p) => p.id == this.form.birth_place_province_id);
            return p ? p.nama : "Pilih Provinsi...";
        },

        selectIndoCity(nama) {
            this.form.birth_place = nama;
            this.showIndoCityDropdown = false;
            this.indoCitySearch = "";
            const el = document.getElementById("birth_place");
            if (el) el.value = nama;
            this.checkValidities();
        },

        getSelectedIndoCityName() {
            if (!this.form.birth_place) return "Pilih Kota/Kabupaten...";
            return this.form.birth_place;
        },

        async init() {
            this.$watch("step", () => window.scrollTo({ top: 0, behavior: "smooth" }));

            await this.fetchIndonesianProvinces();

            if (oldData.birth_place_province_id) {
                this.form.birth_place_province_id = oldData.birth_place_province_id;
                await this.fetchIndonesianCities();
                if (oldData.birth_place) {
                    this.form.birth_place = oldData.birth_place;
                }
            }

            // Restore state after server validation error
            if (oldData.province_id) {
                this.form.province_id = oldData.province_id;
                const sel = document.getElementById("province_id");
                if (sel) sel.value = oldData.province_id;
                await Promise.all([this.fetchBranches(), this.fetchInstitutions()]);
                if (oldData.branch_id) {
                    this.form.branch_id = oldData.branch_id;
                }
                if (oldData.institution_id) {
                    this.form.institution_id = oldData.institution_id;
                    const el = document.getElementById("institution_id");
                    if (el) el.value = oldData.institution_id;
                    const inst = this.institutions.find((i) => i.id == oldData.institution_id);
                    this.showCustomInstitution = inst?.institution_name === "Institusi tidak ditemukan";
                }
            }

            if (errorStep > 0) {
                this.step = errorStep;
                if (errorStep === 1) this.showErrors1 = true;
                if (errorStep === 2) this.showErrors2 = true;
                if (errorStep === 3) this.showErrors3 = true;
            }

            this.$nextTick(() => this.checkValidities());
        },

        getVal,

        async fetchBranches() {
            this.branches = [];
            this.form.branch_id = "";
            this.branchSearch = "";
            this.checkValidities();

            const provinceId = document.getElementById("province_id")?.value;
            if (!provinceId) return;

            try {
                const response = await fetch(`/api/public/branches?province_id=${provinceId}`);
                const data = await response.json();
                this.branches = Array.isArray(data) ? data : [];
            } catch (e) {
                console.error("Failed to fetch branches:", e);
            }
        },

        async fetchInstitutions() {
            this.institutions = [];
            this.form.institution_id = "";
            this.institutionSearch = "";
            this.showCustomInstitution = false;
            const el = document.getElementById("institution_id");
            if (el) el.value = "";
            this.checkValidities();

            const provinceId = document.getElementById("province_id")?.value;
            if (!provinceId) return;

            try {
                const jenjang = EDUCATION_TO_JENJANG[this.form.education_level] ?? "";
                const params = new URLSearchParams({ province_id: provinceId });
                if (jenjang) params.set("jenjang", jenjang);
                const response = await fetch(`/api/public/institutions?${params}`);
                const data = await response.json();
                this.institutions = Array.isArray(data) ? data : [];
            } catch (e) {
                console.error("Failed to fetch institutions:", e);
            }
        },

        checkValidities() {
            this._tick++;
            const bdValid = this.form.birthdate && this.form.birthdate <= MAX_BIRTHDATE;
            this.step1Valid = !!(
                getVal("full_name") &&
                getVal("gender") &&
                bdValid &&
                this.form.birth_place_province_id &&
                this.form.birth_place &&
                getVal("whatsapp_number").length >= 9 &&
                getVal("email") &&
                getVal("address")
            );
            const parentsValid = PARENT_FIELDS.every((f) => getVal(f) !== "");
            const institutionValid = this.showCustomInstitution
                ? !!getVal("institution_name_custom")
                : !!getVal("institution_id");
            this.step2Valid = !!(getVal("province_id") && this.form.branch_id && institutionValid && parentsValid);
            this.step3Valid = !!(
                this.regPassword &&
                this.regPasswordConfirm &&
                this.regPassword === this.regPasswordConfirm &&
                PASSWORD_RE.test(this.regPassword)
            );
        },

        async nextStep(current) {
            this.checkValidities();
            if (current === 1) {
                if (!this.step1Valid) {
                    this.showErrors1 = true;
                    return;
                }
                const email = getVal("email");
                const wa = getVal("whatsapp_number");
                try {
                    const params = new URLSearchParams({ email, whatsapp: wa });
                    const res = await fetch(`/api/public/member/check-uniqueness?${params}`);
                    const data = await res.json();
                    if (data.errors && Object.keys(data.errors).length > 0) {
                        this._uniquenessErrors = data.errors;
                        this.showErrors1 = true;
                        this._tick++;
                        return;
                    }
                } catch (e) {
                    // network error — allow to proceed, server will catch
                }
                this._uniquenessErrors = {};
                this.step = 2;
            } else if (current === 2) {
                if (this.step2Valid) this.step = 3;
                else this.showErrors2 = true;
            }
        },

        hasError(stepNum, id) {
            void this._tick;
            if (stepNum === 1 && !this.showErrors1) return false;
            if (stepNum === 2 && !this.showErrors2) return false;
            if (stepNum === 3 && !this.showErrors3) return false;
            if (this._uniquenessErrors[id]) return true;
            if (id === "branch_id") return !this.form.branch_id;
            if (id === "birth_place_province_id") return !this.form.birth_place_province_id;
            if (id === "birth_place") return !this.form.birth_place;
            if (id === "province_id") return !this.form.province_id;
            if (id === "whatsapp_number") {
                const wa = getVal(id);
                return !wa || wa.length < 9;
            }
            if (id === "birthdate") {
                const bd = this.form.birthdate;
                return !bd || bd > MAX_BIRTHDATE;
            }
            if (id === "password") return !PASSWORD_RE.test(this.regPassword);
            if (id === "password_confirmation")
                return this.regPassword !== this.regPasswordConfirm || !this.regPasswordConfirm;
            return !getVal(id);
        },

        inputClass(stepNum, id) {
            return this.hasError(stepNum, id)
                ? "border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 text-red-700"
                : "bg-[#F8FAFC] border-[#E5E7EB] focus:border-[#5586DB] focus:ring-[#5586DB]/20 text-[#111827]";
        },

        whatsappErrorText() {
            if (this._uniquenessErrors["whatsapp_number"]) return this._uniquenessErrors["whatsapp_number"];
            const wa = getVal("whatsapp_number");
            if (!wa) return "Bagian ini wajib diisi.";
            return "Minimal 9 digit.";
        },

        emailErrorText() {
            if (this._uniquenessErrors["email"]) return this._uniquenessErrors["email"];
            return "Bagian ini wajib diisi.";
        },

        birthdateErrorText() {
            const bd = this.form.birthdate;
            if (!bd) return "Bagian ini wajib diisi.";
            if (bd > MAX_BIRTHDATE) return "Usia minimal 6 tahun (usia masuk SD).";
            return "";
        },

        passwordErrorText() {
            if (!this.regPassword) return "Bagian ini wajib diisi.";
            if (!PASSWORD_RE.test(this.regPassword))
                return "Password minimal 8 karakter, mengandung huruf besar, angka, & simbol.";
            return "";
        },

        passwordConfirmErrorText() {
            if (!this.regPasswordConfirm) return "Bagian ini wajib diisi.";
            if (this.regPassword !== this.regPasswordConfirm) return "Password tidak cocok.";
            return "";
        },
    };
};
