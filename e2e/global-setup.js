import { chromium } from "@playwright/test";

export default async function globalSetup() {
    const browser = await chromium.launch();
    const page = await browser.newPage();

    await page.goto("http://localhost:8000/employee/login");
    await page.fill('input[name="email"]', "developer@mindsia.id");
    await page.fill('input[name="password"]', "Password1@");
    await page.click('button[type="submit"]');
    await page.waitForURL("**/dashboard**");

    await page.context().storageState({ path: "e2e/.auth.json" });
    await browser.close();
}
