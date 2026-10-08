import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { bunny } from "laravel-vite-plugin/fonts";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/toefl-listening-exam.js",
                "resources/js/toefl-structure-exam.js",
                "resources/js/toefl-reading-exam.js",
                "resources/js/toefl-guest-structure-exam.js",
                "resources/js/toefl-guest-reading-exam.js",
                "resources/js/toefl-guest-listening-exam.js",
                "resources/js/classroom-show.js",
                "resources/js/classroom-edit.js",
                "resources/js/employee-profile-edit.js",
            ],
            refresh: true,
            fonts: [
                bunny("Instrument Sans", {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
