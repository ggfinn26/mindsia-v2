window.programRegistration = function (config) {
    return {
        branchProvinces: [],
        branches: [],
        indoProvinces: [],
        indoCities: [],

        needsBranch: config.needsBranch ?? true,
        needsBirth: config.needsBirth ?? true,

        branchProvinceId: config.memberProvinceId || "",
        branchId: config.memberBranchId || "",
        birthProvinceId: config.memberBirthProvinceId || "",
        birthPlace: config.memberBirthPlace || "",
        referredByCode: config.referredByCode || "",
        referredByName: config.referredByName || "",
        paymentPlanId: config.paymentPlanId || "",
        educationLevel: config.educationLevel || "",
        classOrFaculty: config.classOrFaculty || "",

        branchProvinceSearch: "",
        branchSearch: "",
        birthProvinceSearch: "",
        birthPlaceSearch: "",
        employeeSearch: "",

        showBranchProvinceDropdown: false,
        showBranchDropdown: false,
        showBirthProvinceDropdown: false,
        showBirthPlaceDropdown: false,
        showEmployeeDropdown: false,
        showEducationDropdown: false,

        educationOptions: [
            { group: "Sekolah", items: ["SD", "SMP", "SMA", "SMK"] },
            { group: "Perguruan Tinggi", items: ["D1", "D2", "D3", "D4", "S1", "S2", "S3"] },
            { group: "Lainnya", items: ["Umum"] },
        ],

        get selectedEducationLabel() {
            return this.educationLevel || "Pilih Jenjang...";
        },

        get classOrFacultyLabel() {
            return ["SD", "SMP", "SMA", "SMK"].includes(this.educationLevel) ? "Kelas" : "Jurusan / Fakultas";
        },

        get classOrFacultyPlaceholder() {
            return ["SD", "SMP", "SMA", "SMK"].includes(this.educationLevel)
                ? "Contoh: Kelas 10 IPA"
                : "Contoh: Teknik Informatika";
        },

        selectEducationLevel(level) {
            this.educationLevel = level;
            this.classOrFaculty = "";
            this.showEducationDropdown = false;
        },

        employees: [],
        allEmployees: [],
        searchingEmployee: false,

        get filteredBranchProvinces() {
            if (!this.branchProvinceSearch) return this.branchProvinces;
            const q = this.branchProvinceSearch.toLowerCase();
            return this.branchProvinces.filter((p) => p.name.toLowerCase().includes(q));
        },
        get filteredBranches() {
            if (!this.branchSearch) return this.branches;
            const q = this.branchSearch.toLowerCase();
            return this.branches.filter((b) => b.branch_name.toLowerCase().includes(q));
        },
        get filteredBirthProvinces() {
            if (!this.birthProvinceSearch) return this.indoProvinces;
            const q = this.birthProvinceSearch.toLowerCase();
            return this.indoProvinces.filter((p) => p.nama.toLowerCase().includes(q));
        },
        get filteredBirthPlaces() {
            if (!this.birthPlaceSearch) return this.indoCities;
            const q = this.birthPlaceSearch.toLowerCase();
            return this.indoCities.filter((c) => c.nama.toLowerCase().includes(q));
        },

        get selectedBranchProvinceName() {
            const p = this.branchProvinces.find((p) => p.id == this.branchProvinceId);
            return p ? p.name : "Pilih Provinsi";
        },
        get selectedBranchName() {
            const b = this.branches.find((b) => b.id == this.branchId);
            return b ? b.branch_name : "Pilih Cabang";
        },
        get selectedBirthProvinceName() {
            const p = this.indoProvinces.find((p) => p.id == this.birthProvinceId);
            return p ? p.nama : "Pilih Provinsi";
        },
        get selectedBirthPlaceName() {
            return this.birthPlace || "Pilih Kota/Kabupaten";
        },

        loadAllEmployees() {
            if (this.employees.length > 0) return;
            this.searchingEmployee = true;
            fetch("/api/member/employees/search")
                .then((res) => res.json())
                .then((data) => {
                    this.employees = data;
                    this.allEmployees = data;
                    this.searchingEmployee = false;
                })
                .catch(() => {
                    this.searchingEmployee = false;
                });
        },

        searchEmployee() {
            const q = this.employeeSearch.toLowerCase();
            if (!q) {
                this.employees = this.allEmployees;
                return;
            }
            this.employees = this.allEmployees.filter(
                (e) => e.full_name.toLowerCase().includes(q) || e.employee_code.toLowerCase().includes(q),
            );
        },

        selectEmployee(code, name) {
            this.referredByCode = code;
            this.referredByName = name;
            this.showEmployeeDropdown = false;
        },

        clearEmployee() {
            this.referredByCode = "";
            this.referredByName = "";
            this.employeeSearch = "";
            this.employees = [];
        },

        async init() {
            if (this.needsBranch) {
                fetch("/api/public/provinces")
                    .then((res) => res.json())
                    .then((data) => {
                        this.branchProvinces = data;
                    })
                    .catch((err) => console.error("Failed to fetch branch provinces:", err));

                if (this.branchProvinceId) {
                    this.fetchBranches();
                }
            }

            if (this.needsBirth) {
                fetch("/api/public/indonesia-provinces")
                    .then((res) => res.json())
                    .then((data) => {
                        this.indoProvinces = data;
                    })
                    .catch((err) => console.error("Failed to fetch indo provinces:", err));

                if (this.birthProvinceId) {
                    this.fetchCities();
                }
            }
        },

        selectBranchProvince(id) {
            this.branchProvinceId = id;
            this.showBranchProvinceDropdown = false;
            this.branchProvinceSearch = "";
            this.fetchBranches();
        },
        selectBranch(id) {
            this.branchId = id;
            this.showBranchDropdown = false;
            this.branchSearch = "";
        },
        selectBirthProvince(id) {
            this.birthProvinceId = id;
            this.showBirthProvinceDropdown = false;
            this.birthProvinceSearch = "";
            this.fetchCities();
        },
        selectBirthPlace(nama) {
            this.birthPlace = nama;
            this.showBirthPlaceDropdown = false;
            this.birthPlaceSearch = "";
        },

        async fetchBranches() {
            this.branches = [];
            if (!this.branchProvinceId) {
                this.branchId = "";
                return;
            }
            try {
                const res = await fetch(`/api/public/branches?province_id=${this.branchProvinceId}`);
                this.branches = await res.json();
                if (!this.branches.find((b) => b.id == this.branchId)) {
                    this.branchId = "";
                }
            } catch (err) {
                console.error("Failed to fetch branches:", err);
            }
        },

        async fetchCities() {
            this.indoCities = [];
            if (!this.birthProvinceId) {
                this.birthPlace = "";
                return;
            }
            try {
                const res = await fetch(`/api/public/indonesia-cities?province_id=${this.birthProvinceId}`);
                this.indoCities = await res.json();
                if (!this.indoCities.find((c) => c.nama == this.birthPlace)) {
                    this.birthPlace = "";
                }
            } catch (err) {
                console.error("Failed to fetch cities:", err);
            }
        },
    };
};
