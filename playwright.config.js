import { defineConfig, devices } from "@playwright/test";

export default defineConfig({
    testDir: "./e2e",
    globalSetup: "./e2e/global-setup.js",
    fullyParallel: false,
    retries: 0,
    workers: 1,
    reporter: [["html", { outputFolder: "e2e-report" }], ["list"]],
    use: {
        baseURL: process.env.APP_URL ?? "http://localhost:8000",
        trace: "on-first-retry",
        screenshot: "only-on-failure",
        headless: true,
    },
    projects: [{ name: "chromium", use: { ...devices["Desktop Chrome"] } }],
});
