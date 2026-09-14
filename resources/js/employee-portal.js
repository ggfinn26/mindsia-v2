window.employeePortal = function (serverView) {
    const cfg = window.__employeePortalConfig ?? {};
    const routes = cfg.routes ?? {};

    function getVal(id) {
        const el = document.getElementById(id);
        return el ? el.value.trim() : "";
    }

    const PASSWORD_RE = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    return {
        view: serverView,
        step1Valid: false,
        step2Valid: false,
        showErrors1: false,
        showErrors2: false,
        verifyError: "",
        verifyLoading: false,
        registerErrors: {},
        registerLoading: false,
        loginError: "",
        loginLoading: false,
        regPassword: "",
        regPasswordConfirm: "",

        init() {
            this.$nextTick(() => this.checkValidities());
        },

        async _post(url, body) {
            return fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content,
                    Accept: "application/json",
                },
                body: JSON.stringify(body),
            });
        },

        async verifyCode() {
            this.verifyError = "";
            if (!this.step1Valid) {
                this.showErrors1 = true;
                return;
            }
            this.verifyLoading = true;
            try {
                const res = await this._post(routes.registerVerify, { employee_code: getVal("employee_code") });
                if (res.ok) {
                    this.view = "register2";
                } else {
                    const data = await res.json();
                    this.verifyError = data.errors?.employee_code?.[0] ?? "Terjadi kesalahan, coba lagi.";
                }
            } catch {
                this.verifyError = "Tidak dapat terhubung ke server.";
            } finally {
                this.verifyLoading = false;
            }
        },

        async submitRegister() {
            this.registerErrors = {};
            if (!this.step2Valid) {
                this.showErrors2 = true;
                return;
            }
            this.registerLoading = true;
            try {
                const res = await this._post(routes.register, {
                    name: getVal("name"),
                    email: getVal("reg_email"),
                    password: this.regPassword,
                    password_confirmation: this.regPasswordConfirm,
                });
                if (res.ok) {
                    const data = await res.json();
                    window.location = data.redirect ?? routes.registerSuccess;
                } else {
                    const data = await res.json();
                    this.registerErrors = data.errors ?? {};
                }
            } catch {
                this.registerErrors = { form: ["Tidak dapat terhubung ke server."] };
            } finally {
                this.registerLoading = false;
            }
        },

        async submitLogin() {
            this.loginError = "";
            this.loginLoading = true;
            try {
                const res = await this._post(routes.loginPost, {
                    email: getVal("email"),
                    password: getVal("password"),
                    remember: document.getElementById("remember")?.checked ?? false,
                });
                if (res.ok) {
                    const data = await res.json();
                    window.location = data.redirect ?? routes.dashboard;
                } else {
                    const data = await res.json();
                    this.loginError = data.errors?.email?.[0] ?? "Email atau password salah.";
                }
            } catch {
                this.loginError = "Tidak dapat terhubung ke server.";
            } finally {
                this.loginLoading = false;
            }
        },

        getVal,

        checkValidities() {
            this.step1Valid = !!getVal("employee_code");
            this.step2Valid = !!(
                getVal("name") &&
                getVal("reg_email") &&
                this.regPassword &&
                this.regPasswordConfirm &&
                this.regPassword === this.regPasswordConfirm &&
                PASSWORD_RE.test(this.regPassword)
            );
        },

        hasError(step, id) {
            if (step === 1 && !this.showErrors1) return false;
            if (step === 2 && !this.showErrors2) return false;
            if (id === "reg_password") return !PASSWORD_RE.test(this.regPassword);
            if (id === "password_confirmation")
                return this.regPassword !== this.regPasswordConfirm || !this.regPasswordConfirm;
            return !getVal(id);
        },

        inputClass(step, id) {
            return this.hasError(step, id)
                ? "border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 text-red-700"
                : "bg-[#F8FAFC] border-[#E5E7EB] focus:border-[#5586DB] focus:ring-[#5586DB]/20 text-[#111827]";
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
