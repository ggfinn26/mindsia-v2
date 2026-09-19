const boxes = document.querySelectorAll("[data-otp-index]");
const hidden = document.getElementById("otp-hidden");

if (boxes.length && hidden) {
    boxes.forEach((box, i) => {
        box.addEventListener("input", () => {
            box.value = box.value.replace(/\D/g, "").slice(-1);
            if (box.value && i < 5) boxes[i + 1].focus();
            hidden.value = [...boxes].map((b) => b.value).join("");
        });
        box.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && !box.value && i > 0) boxes[i - 1].focus();
        });
        box.addEventListener("paste", (e) => {
            e.preventDefault();
            const digits = e.clipboardData.getData("text").replace(/\D/g, "").slice(0, 6);
            digits.split("").forEach((d, j) => {
                if (boxes[j]) boxes[j].value = d;
            });
            hidden.value = digits.padEnd(6, "").slice(0, 6);
            if (boxes[Math.min(digits.length, 5)]) boxes[Math.min(digits.length, 5)].focus();
        });
    });
}
