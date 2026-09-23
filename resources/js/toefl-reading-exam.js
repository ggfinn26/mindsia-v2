(function () {
    const el = document.getElementById("exam-config");
    if (!el) {
        return;
    }
    const startedAtRaw = el.dataset.startedAt;
    const cfg = {
        markStartedUrl: el.dataset.markStartedUrl,
        startedAt: startedAtRaw ? parseInt(startedAtRaw) : null,
        limitSeconds: parseInt(el.dataset.limitSeconds),
        csrfToken: el.dataset.csrf,
        answerUrl: el.dataset.answerUrl,
        totalGroups: parseInt(el.dataset.totalGroups),
    };

    const submitForm = document.getElementById("submit-form");
    const instrPanel = document.getElementById("instr-panel");
    const passagesPanel = document.getElementById("passages-panel");
    const phaseLbl = document.getElementById("phase-label");
    const timerEl = document.getElementById("timer");
    const submitModal = document.getElementById("submit-modal");

    /* ── Timer ── */
    let timerInterval = null;

    function startTimer(timestamp) {
        if (timerInterval) {
            return;
        }
        timerInterval = setInterval(function () {
            const elapsed = Math.floor(Date.now() / 1000) - timestamp;
            const remaining = Math.max(0, cfg.limitSeconds - elapsed);
            const m = String(Math.floor(remaining / 60)).padStart(2, "0");
            const s = String(remaining % 60).padStart(2, "0");
            timerEl.textContent = m + ":" + s;
            timerEl.classList.toggle("text-red-600", remaining <= 300);
            timerEl.classList.toggle("text-gray-800", remaining > 300);
            if (remaining <= 0) {
                submitForm.submit();
            }
        }, 1000);
    }

    function markStarted() {
        if (timerInterval) {
            return;
        }
        fetch(cfg.markStartedUrl, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": cfg.csrfToken, Accept: "application/json" },
        })
            .then((r) => r.json())
            .then((data) => {
                if (data.started_at) {
                    startTimer(data.started_at);
                }
            });
    }

    /* ── Autosave ── */
    document.addEventListener("change", function (e) {
        if (!e.target.classList.contains("autosave-radio")) {
            return;
        }
        const radio = e.target;
        radio
            .closest(".space-y-2")
            .querySelectorAll("label")
            .forEach((l) => {
                l.classList.remove("border-green-400", "bg-green-50");
                l.classList.add("border-gray-200");
            });
        radio.closest("label").classList.add("border-green-400", "bg-green-50");
        radio.closest("label").classList.remove("border-gray-200");
        fetch(cfg.answerUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": cfg.csrfToken },
            body: JSON.stringify({ question_id: radio.dataset.question, selected_option: radio.value }),
        });
    });

    /* ── Toast ── */
    function showToast(msg) {
        const existing = document.getElementById("reading-toast");
        if (existing) {
            existing.remove();
        }
        const t = document.createElement("div");
        t.id = "reading-toast";
        t.textContent = msg;
        t.style.cssText =
            "position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#ef4444;color:#fff;padding:10px 20px;border-radius:8px;font-size:0.875rem;font-weight:600;z-index:9998;box-shadow:0 4px 12px rgba(0,0,0,0.2);pointer-events:none;";
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
    }

    /* ── Navigation ── */
    let currentGroup = 0;

    function allAnswered(groupEl) {
        const names = new Set();
        groupEl.querySelectorAll('input[type="radio"]').forEach((r) => names.add(r.name));
        for (const name of names) {
            if (!groupEl.querySelector('input[name="' + name + '"]:checked')) {
                return false;
            }
        }
        return names.size > 0;
    }

    function showGroup(index) {
        document.querySelectorAll(".passage-group").forEach((g) => g.classList.add("hidden"));
        const g = document.getElementById("pg-" + index);
        if (!g) {
            return;
        }
        g.classList.remove("hidden");
        window.scrollTo({ top: 0, behavior: "smooth" });
        phaseLbl.textContent = "Bacaan " + (index + 1) + " dari " + cfg.totalGroups;
    }

    function goToPassages() {
        instrPanel.classList.add("hidden");
        passagesPanel.classList.remove("hidden");
        showGroup(0);
    }

    document.getElementById("start-btn").addEventListener("click", function () {
        markStarted();
        goToPassages();
    });

    document.querySelectorAll(".pg-next-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const groupEl = document.getElementById("pg-" + currentGroup);
            if (groupEl && !allAnswered(groupEl)) {
                showToast("Jawab semua soal pada bacaan ini sebelum lanjut.");
                return;
            }
            btn.disabled = true;
            btn.style.opacity = "0.5";
            currentGroup = parseInt(this.dataset.next);
            showGroup(currentGroup);
        });
    });

    /* ── Submit modal ── */
    document.getElementById("modal-cancel").addEventListener("click", () => submitModal.classList.add("hidden"));
    document.getElementById("modal-confirm").addEventListener("click", () => submitForm.submit());

    window.submitReading = function () {
        submitModal.classList.remove("hidden");
    };

    /* ── Exam overlay entry ── */
    let examStarted = false;
    function examStart() {
        if (examStarted) {
            return;
        }
        examStarted = true;
        if (cfg.startedAt) {
            startTimer(cfg.startedAt);
            goToPassages();
        } else {
            instrPanel.classList.remove("hidden");
            phaseLbl.textContent = "Baca petunjuk";
        }
    }

    window.addEventListener("exam-start", examStart);
    window._examStart = examStart;
})();
