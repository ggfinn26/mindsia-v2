(function () {
    const el = document.getElementById("exam-config");
    if (!el) {
        return;
    }
    const startedAtRaw = el.dataset.startedAt;
    const cfg = {
        startedAt: startedAtRaw ? parseInt(startedAtRaw) : null,
        limitSeconds: parseInt(el.dataset.limitSeconds),
        csrfToken: el.dataset.csrf,
        answerUrl: el.dataset.answerUrl,
        total: parseInt(el.dataset.total),
    };

    const submitForm = document.getElementById("submit-form");
    const progressBar = document.getElementById("progress-bar");
    const counter = document.getElementById("q-counter");
    const nextBtn = document.getElementById("next-btn");
    const submitBtn = document.getElementById("submit-btn");
    const instrPanel = document.getElementById("instr-panel");
    const questionsPanel = document.getElementById("questions-panel");
    const phaseLbl = document.getElementById("phase-label");
    const timerEl = document.getElementById("timer");

    /* ── Timer (starts from cfg.startedAt on page load) ── */
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

    // Guest sessions always have structure_started_at set on page load
    if (cfg.startedAt) {
        startTimer(cfg.startedAt);
    }

    /* ── Toast ── */
    function showToast(msg) {
        const existing = document.getElementById("struct-toast");
        if (existing) {
            existing.remove();
        }
        const t = document.createElement("div");
        t.id = "struct-toast";
        t.textContent = msg;
        t.style.cssText =
            "position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#ef4444;color:#fff;padding:10px 20px;border-radius:8px;font-size:0.875rem;font-weight:600;z-index:9998;box-shadow:0 4px 12px rgba(0,0,0,0.2);pointer-events:none;";
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
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
                l.classList.remove("border-purple-400", "bg-purple-50");
                l.classList.add("border-gray-200");
            });
        radio.closest("label").classList.add("border-purple-400", "bg-purple-50");
        radio.closest("label").classList.remove("border-gray-200");
        fetch(cfg.answerUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": cfg.csrfToken },
            body: JSON.stringify({ question_id: radio.dataset.question, selected_option: radio.value }),
        });
    });

    /* ── Navigation ── */
    let current = 0;

    function showQuestion(index) {
        document.querySelectorAll(".structure-question").forEach((el) => el.classList.add("hidden"));
        const el = document.getElementById("q-" + index);
        if (!el) {
            return;
        }
        el.classList.remove("hidden");
        el.scrollIntoView({ behavior: "smooth", block: "start" });

        progressBar.style.width = Math.round(((index + 1) / cfg.total) * 100) + "%";
        counter.textContent = index + 1 + " / " + cfg.total;
        phaseLbl.textContent = "Soal " + (index + 1) + " dari " + cfg.total;

        nextBtn.style.display = index === cfg.total - 1 ? "none" : "";
        submitBtn.style.display = index === cfg.total - 1 ? "" : "none";
    }

    /* ── Helpers ── */
    function goToQuestions() {
        instrPanel.classList.add("hidden");
        questionsPanel.classList.remove("hidden");
        showQuestion(0);
    }

    document.getElementById("start-btn").addEventListener("click", function () {
        // No markStarted call — guest timer is already running from cfg.startedAt
        if (!cfg.startedAt) {
            // Fallback: use current time as reference (should not happen in practice)
            startTimer(Math.floor(Date.now() / 1000));
        }
        goToQuestions();
    });

    nextBtn.addEventListener("click", function () {
        const el = document.getElementById("q-" + current);
        if (!el.querySelector('input[type="radio"]:checked')) {
            showToast("Pilih jawaban terlebih dahulu sebelum lanjut.");
            return;
        }
        current++;
        showQuestion(current);
    });

    const submitModal = document.getElementById("submit-modal");
    const modalCancel = document.getElementById("modal-cancel");
    const modalConfirm = document.getElementById("modal-confirm");

    modalCancel.addEventListener("click", () => submitModal.classList.add("hidden"));
    modalConfirm.addEventListener("click", () => submitForm.submit());

    window.submitStructure = function () {
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
            // refresh after questions began — skip instructions
            goToQuestions();
        } else {
            instrPanel.classList.remove("hidden");
            phaseLbl.textContent = "Baca petunjuk";
        }
    }

    window.addEventListener("exam-start", examStart);
    window._examStart = examStart;

    // If already started (page refresh after questions began), skip instructions
    if (cfg.startedAt) {
        instrPanel.classList.add("hidden");
        questionsPanel.classList.remove("hidden");
        phaseLbl.textContent = "Soal 1 dari " + cfg.total;
        showQuestion(0);
    }
})();
