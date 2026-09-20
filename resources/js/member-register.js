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
        provinces,
        institutions: [],
        form: { province_id: "", institution_id: "", birthdate: oldData.birthdate ?? "" },
        institutionSearch: "",
        showInstitutionDropdown: false,
        showCustomInstitution: false,

        get filteredInstitutions() {
            if (this.institutionSearch.trim() === "") return this.institutions.slice(0, 100);
            const q = this.institutionSearch.toLowerCase();
            return this.institutions.filter((i) => i.institution_name.toLowerCase().includes(q)).slice(0, 100);
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

        async init() {
            this.$watch("step", () => window.scrollTo({ top: 0, behavior: "smooth" }));

            // Restore state after server validation error
            if (oldData.province_id) {
                this.form.province_id = oldData.province_id;
                const sel = document.getElementById("province_id");
                if (sel) sel.value = oldData.province_id;
                await this.fetchInstitutions();
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

        async fetchInstitutions() {
            this.institutions = [];
            this.form.institution_id = "";
            this.institutionSearch = "";
            this.showCustomInstitution = false;
            const el = document.getElementById("institution_id");
            if (el) el.value = "";
            this.checkValidities();

            const provinceId = document.getElementById("province_id").value;
            if (!provinceId) return;

            try {
                const response = await fetch(`/api/public/institutions?province_id=${provinceId}`);
                const data = await response.json();
                this.institutions = Array.isArray(data) ? data : [];
            } catch (e) {
                console.error("Failed to fetch institutions:", e);
            }
        },

        checkValidities() {
            const bdValid = this.form.birthdate && this.form.birthdate <= MAX_BIRTHDATE;
            this.step1Valid = !!(
                getVal("full_name") &&
                getVal("gender") &&
                bdValid &&
                getVal("whatsapp_number").length >= 9 &&
                getVal("email") &&
                getVal("address") &&
                getVal("province_id")
            );
            const parentsValid = PARENT_FIELDS.every((f) => getVal(f) !== "");
            this.step2Valid = !!(getVal("institution_id") && parentsValid);
            const pass = getVal("password");
            const conf = getVal("password_confirmation");
            this.step3Valid = !!(pass && conf && pass === conf && PASSWORD_RE.test(pass));
        },

        nextStep(current) {
            this.checkValidities();
            if (current === 1) {
                if (this.step1Valid) this.step = 2;
                else this.showErrors1 = true;
            } else if (current === 2) {
                if (this.step2Valid) this.step = 3;
                else this.showErrors2 = true;
            }
        },

        hasError(stepNum, id) {
            if (stepNum === 1 && !this.showErrors1) return false;
            if (stepNum === 2 && !this.showErrors2) return false;
            if (stepNum === 3 && !this.showErrors3) return false;
            if (id === "whatsapp_number") return getVal(id).length < 9;
            if (id === "birthdate") {
                const bd = this.form.birthdate;
                return !bd || bd > MAX_BIRTHDATE;
            }
            if (id === "password") return !PASSWORD_RE.test(getVal(id));
            if (id === "password_confirmation") return getVal("password") !== getVal(id) || !getVal(id);
            return !getVal(id);
        },

        inputClass(stepNum, id) {
            return this.hasError(stepNum, id)
                ? "border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 text-red-700"
                : "bg-[#F8FAFC] border-[#E5E7EB] focus:border-[#5586DB] focus:ring-[#5586DB]/20 text-[#111827]";
        },

        birthdateErrorText() {
            const bd = this.form.birthdate;
            if (!bd) return "Bagian ini wajib diisi.";
            if (bd > MAX_BIRTHDATE) return "Usia minimal 6 tahun (usia masuk SD).";
            return "";
        },

        passwordErrorText() {
            const pass = getVal("password");
            if (!pass) return "Bagian ini wajib diisi.";
            if (!PASSWORD_RE.test(pass)) return "Password minimal 8 karakter, mengandung huruf besar, angka, & simbol.";
            return "";
        },

        passwordConfirmErrorText() {
            const conf = getVal("password_confirmation");
            if (!conf) return "Bagian ini wajib diisi.";
            if (getVal("password") !== conf) return "Password tidak cocok.";
            return "";
        },
    };
};
