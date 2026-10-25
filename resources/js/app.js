import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import "./attendance-camera";
import "./work-schedule-assign";
import "./employee-portal";
import "./member-register";
import "./applicant-register";
import "./select-enhance";
import "./select-enhance-with-semua-option";
import "./branch-picker";
import "./socialization-create";
import "./wa-picker";
import "./gallery-coverflow";
import "./count-up";
window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.store("dashboard", { customizing: false });

window.dashTheme = function () {
    const THEMES = ["blue", "cyan", "gold"];
    const saved = localStorage.getItem("dash_theme") ?? "blue";
    const savedMode = localStorage.getItem("dash_mode") ?? "light";

    function apply(theme, mode) {
        const html = document.documentElement;
        THEMES.forEach((t) => html.classList.remove("theme-" + t));
        html.classList.remove("mode-light", "mode-dark");
        html.classList.add("theme-" + theme, "mode-" + mode);
    }

    apply(saved, savedMode);

    return {
        theme: saved,
        mode: savedMode,
        setTheme(t) {
            this.theme = t;
            localStorage.setItem("dash_theme", t);
            apply(t, this.mode);
        },
        toggleMode() {
            this.mode = this.mode === "light" ? "dark" : "light";
            localStorage.setItem("dash_mode", this.mode);
            apply(this.theme, this.mode);
        },
    };
};
Alpine.start();
import "./password-toggle";
import "./otp-input";
import "./redirect-countdown";
import "./resend-timer";
import "./program-registration";
import "./letter-generate";
import "./passage-preview";
import "./toefl-test-show";
import "./member-class-calendar";
import "./rent-contract-create";
import "./rich-editor";

import Sortable from "sortablejs";
window.Sortable = Sortable;

import ApexCharts from "apexcharts";
window.ApexCharts = ApexCharts;
