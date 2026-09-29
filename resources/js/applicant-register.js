window.applicantCitySelector = function () {
    return {
        provinces: [],
        cities: [],
        provinceId: "",
        city: "",
        showProvinceDropdown: false,
        showCityDropdown: false,
        provinceSearch: "",
        citySearch: "",

        get filteredProvinces() {
            if (!this.provinceSearch.trim()) return this.provinces;
            const q = this.provinceSearch.toLowerCase();
            return this.provinces.filter((p) => p.nama.toLowerCase().includes(q));
        },

        get filteredCities() {
            if (!this.citySearch.trim()) return this.cities;
            const q = this.citySearch.toLowerCase();
            return this.cities.filter((c) => c.nama.toLowerCase().includes(q));
        },

        async init() {
            const config = window.__applicantRegisterConfig ?? {};
            this.provinceId = config.old?.province_id ?? "";
            this.city = config.old?.city ?? "";

            try {
                const res = await fetch("/api/public/indonesia-provinces");
                const data = await res.json();
                this.provinces = Array.isArray(data) ? data : [];
            } catch (e) {
                console.error("Failed to fetch provinces:", e);
            }

            if (this.provinceId) {
                await this.fetchCities();
            }
        },

        async fetchCities() {
            this.cities = [];
            this.city = "";
            if (!this.provinceId) return;
            try {
                const res = await fetch(
                    `/api/public/indonesia-cities?province_id=${encodeURIComponent(this.provinceId)}`,
                );
                const data = await res.json();
                this.cities = Array.isArray(data) ? data : [];
            } catch (e) {
                console.error("Failed to fetch cities:", e);
            }
        },

        selectProvince(id) {
            this.provinceId = id;
            this.showProvinceDropdown = false;
            this.provinceSearch = "";
            this.fetchCities();
        },

        selectCity(nama) {
            this.city = nama;
            this.showCityDropdown = false;
            this.citySearch = "";
        },

        getSelectedProvinceName() {
            if (!this.provinceId) return "Pilih Provinsi...";
            const p = this.provinces.find((p) => p.id == this.provinceId);
            return p ? p.nama : "Pilih Provinsi...";
        },
    };
};

(function () {
    const ERROR_CLASS = "border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-200";
    const OK_CLASS = "bg-[#F8FAFC] border-[#E5E7EB] focus:border-[#5586DB] focus:ring-[#5586DB]/20";

    function showFieldError(inputEl, errorEl, message) {
        inputEl.classList.remove(...OK_CLASS.split(" "));
        inputEl.classList.add(...ERROR_CLASS.split(" "));
        errorEl.textContent = message;
        errorEl.classList.remove("hidden");
    }

    function clearFieldError(inputEl, errorEl) {
        inputEl.classList.remove(...ERROR_CLASS.split(" "));
        inputEl.classList.add(...OK_CLASS.split(" "));
        errorEl.textContent = "";
        errorEl.classList.add("hidden");
    }

    document.addEventListener("DOMContentLoaded", function () {
        const emailInput = document.getElementById("email");
        const waInput = document.getElementById("whatsapp_number");

        if (!emailInput || !waInput) return;

        const emailError = document.getElementById("email-uniqueness-error");
        const waError = document.getElementById("wa-uniqueness-error");

        async function checkUniqueness(field, value) {
            if (!value) return;
            try {
                const params = new URLSearchParams({ [field]: value });
                const res = await fetch(`/api/public/applicant/check-uniqueness?${params}`);
                const data = await res.json();

                if (field === "email") {
                    if (data.errors?.email) {
                        showFieldError(emailInput, emailError, data.errors.email);
                    } else {
                        clearFieldError(emailInput, emailError);
                    }
                }

                if (field === "whatsapp") {
                    if (data.errors?.whatsapp_number) {
                        showFieldError(waInput, waError, data.errors.whatsapp_number);
                    } else {
                        clearFieldError(waInput, waError);
                    }
                }
            } catch {
                // network error — server will catch on submit
            }
        }

        emailInput.addEventListener("blur", () => checkUniqueness("email", emailInput.value.trim()));

        waInput.addEventListener("blur", () => checkUniqueness("whatsapp", waInput.value.trim()));
    });
})();
