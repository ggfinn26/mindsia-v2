window.memberRegisterPortal = function () {
    const cfg = window.__memberRegisterConfig ?? {};
    const provinces = cfg.provinces ?? [];

    function getVal(id) {
        const el = document.getElementById(id);
        return el ? el.value.trim() : "";
    }

    const PASSWORD_RE = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

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
        form: { province_id: "", institution_id: "" },
        institutionSearch: "",
        showInstitutionDropdown: false,

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
            this.checkValidities();
        },

        getSelectedInstitutionName() {
            const id = getVal("institution_id") || this.form.institution_id;
            if (!id) return "Pilih Institusi...";
            const inst = this.institutions.find((i) => i.id == id);
            return inst ? inst.institution_name : "Pilih Institusi...";
        },

        init() {
            this.$watch("step", () => window.scrollTo({ top: 0, behavior: "smooth" }));
            this.$nextTick(() => this.checkValidities());
        },

        getVal,

        async fetchInstitutions() {
            this.institutions = [];
            this.form.institution_id = "";
            this.institutionSearch = "";
            const el = document.getElementById("institution_id");
            if (el) el.value = "";
            this.checkValidities();

            const provinceId = document.getElementById("province_id").value;
            if (!provinceId) return;

            try {
                const response = await fetch(`/api/regions/${provinceId}/institutions`);
                this.institutions = await response.json();
            } catch (e) {
                console.error("Failed to fetch institutions:", e);
            }
        },

        checkValidities() {
            this.step1Valid = !!(
                getVal("full_name") &&
                getVal("gender") &&
                getVal("birthdate") &&
                getVal("whatsapp_number").length >= 9 &&
                getVal("email") &&
                getVal("address") &&
                getVal("province_id")
            );
            this.step2Valid = !!getVal("institution_id");
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
            if (id === "password") return !PASSWORD_RE.test(getVal(id));
            if (id === "password_confirmation") return getVal("password") !== getVal(id) || !getVal(id);
            return !getVal(id);
        },

        inputClass(stepNum, id) {
            return this.hasError(stepNum, id)
                ? "border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 text-red-700"
                : "bg-[#F8FAFC] border-[#E5E7EB] focus:border-[#5586DB] focus:ring-[#5586DB]/20 text-[#111827]";
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
