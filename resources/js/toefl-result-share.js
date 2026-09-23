(function () {
    if (typeof html2canvas === "undefined") {
        return;
    }

    const btn = document.getElementById("share-btn");
    const label = document.getElementById("share-btn-text");
    const shareText = btn ? btn.dataset.shareText : "";

    let capturedDataUrl = null;

    async function doCapture() {
        const card = document.getElementById("result-card");
        const logo = card.querySelector('img[alt="MINDSIA"]');
        if (logo) {
            logo.style.filter = "brightness(0) invert(1)";
            logo.style.opacity = "0.9";
        }
        const canvas = await html2canvas(card, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: "#ffffff",
            logging: false,
            imageTimeout: 10000,
        });
        if (logo) {
            logo.style.filter = "";
            logo.style.opacity = "";
        }
        return canvas.toDataURL("image/png");
    }

    window.addEventListener("load", async function () {
        try {
            capturedDataUrl = await doCapture();
        } catch (_) {}
    });

    window.handleShare = async function (btn) {
        btn.disabled = true;
        label.textContent = "Menyiapkan…";

        if (!capturedDataUrl) {
            try {
                capturedDataUrl = await doCapture();
            } catch (e) {
                label.textContent = "Error: " + e.message.slice(0, 40);
                btn.disabled = false;
                return;
            }
        }

        // Download
        const a = document.createElement("a");
        a.href = capturedDataUrl;
        a.download = "hasil-toefl-mindsia.png";
        a.click();

        // Native share (mobile)
        try {
            const res = await fetch(capturedDataUrl);
            const blob = await res.blob();
            const file = new File([blob], "hasil-toefl-mindsia.png", { type: "image/png" });
            if (navigator.canShare && navigator.canShare({ files: [file] })) {
                await navigator.share({ files: [file], title: "Hasil TOEFL MINDSIA", text: shareText });
            }
        } catch (err) {
            if (err.name !== "AbortError") {
                console.error(err);
            }
        }

        label.textContent = "Bagikan Hasil";
        btn.disabled = false;
    };
})();
