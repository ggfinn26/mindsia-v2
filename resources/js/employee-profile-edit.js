document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById("signature-pad");
    if (!canvas) return;

    const ctx = canvas.getContext("2d");
    let drawing = false;

    const isDark = () => document.documentElement.classList.contains("mode-dark");
    const bgColor = () => (isDark() ? "#1e293b" : "#ffffff");
    const strokeColor = () => (isDark() ? "#f1f5f9" : "#000000");

    function fillBg() {
        ctx.fillStyle = bgColor();
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    }

    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
    fillBg();

    new MutationObserver(() => fillBg()).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"],
    });

    function getPos(e) {
        const r = canvas.getBoundingClientRect();
        const clientX = e.clientX ?? e.touches?.[0]?.clientX;
        const clientY = e.clientY ?? e.touches?.[0]?.clientY;
        return { x: clientX - r.left, y: clientY - r.top };
    }

    function startDraw(e) {
        drawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        if (e.cancelable) e.preventDefault();
    }

    function stopDraw() {
        drawing = false;
        ctx.beginPath();
    }

    function draw(e) {
        if (!drawing) return;
        const pos = getPos(e);
        ctx.lineWidth = 2.5;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";
        ctx.strokeStyle = strokeColor();
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        if (e.cancelable) e.preventDefault();
    }

    canvas.addEventListener("mousedown", startDraw, { passive: false });
    canvas.addEventListener("mouseup", stopDraw);
    canvas.addEventListener("mousemove", draw, { passive: false });
    canvas.addEventListener("mouseleave", stopDraw);
    canvas.addEventListener("touchstart", startDraw, { passive: false });
    canvas.addEventListener("touchend", stopDraw);
    canvas.addEventListener("touchmove", draw, { passive: false });

    document.getElementById("clear-signature")?.addEventListener("click", () => {
        fillBg();
    });

    document.querySelector("form")?.addEventListener("submit", () => {
        const blank = document.createElement("canvas");
        blank.width = canvas.width;
        blank.height = canvas.height;
        const bCtx = blank.getContext("2d");
        bCtx.fillStyle = bgColor();
        bCtx.fillRect(0, 0, blank.width, blank.height);

        if (canvas.toDataURL() !== blank.toDataURL()) {
            const field = document.getElementById("signature_base64");
            if (field) field.value = canvas.toDataURL("image/png");
        }
    });
});
