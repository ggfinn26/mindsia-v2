(function () {
    const el = document.getElementById("exam-config");
    if (!el) {
        return;
    }
    const cfg = {
        markStartedUrl: el.dataset.markStartedUrl,
        answerUrl: el.dataset.answerUrl,
        csrfToken: el.dataset.csrf,
        limitSeconds: parseInt(el.dataset.limitSeconds),
        urlInstructionA: el.dataset.urlInstructionA,
        urlEndingA: el.dataset.urlEndingA,
        urlInstructionB: el.dataset.urlInstructionB,
        urlEndingB: el.dataset.urlEndingB,
        urlInstructionC: el.dataset.urlInstructionC,
        urlEndingC: el.dataset.urlEndingC,
        partACount: parseInt(el.dataset.partACount),
        partBCount: parseInt(el.dataset.partBCount),
        partCCount: parseInt(el.dataset.partCCount),
    };

    /* ── Toast ── */
    function showToast(msg) {
        const existing = document.getElementById("exam-toast");
        if (existing) {
            existing.remove();
        }
        const el = document.createElement("div");
        el.id = "exam-toast";
        el.textContent = msg;
        el.style.cssText =
            "position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#ef4444;color:#fff;padding:10px 20px;border-radius:8px;font-size:0.875rem;font-weight:600;z-index:9998;box-shadow:0 4px 12px rgba(0,0,0,0.2);pointer-events:none;";
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    /* ── Timer ── */
    let timerInterval = null;

    const submitForm = document.getElementById("submit-form");
    const submitBtn = document.getElementById("submit-btn");
    const timerEl = document.getElementById("timer");

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
            if (remaining <= 300) {
                timerEl.classList.remove("text-gray-800");
                timerEl.classList.add("text-red-600");
            }
            if (remaining <= 0) {
                submitForm.submit();
            }
        }, 1000);
    }

    function markListeningStarted() {
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
        const accent = radio.classList.contains("text-amber-600")
            ? ["border-amber-400", "bg-amber-50"]
            : radio.classList.contains("text-teal-600")
              ? ["border-teal-400", "bg-teal-50"]
              : ["border-indigo-400", "bg-indigo-50"];
        radio
            .closest(".space-y-2")
            .querySelectorAll("label")
            .forEach((l) => {
                l.className = l.className.replace(/border-(indigo|amber|teal)-400|bg-(indigo|amber|teal)-50/g, "");
                l.classList.add("border-gray-200");
            });
        radio.closest("label").classList.remove("border-gray-200");
        radio.closest("label").classList.add(...accent);
        fetch(cfg.answerUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": cfg.csrfToken },
            body: JSON.stringify({ question_id: radio.dataset.question, selected_option: radio.value }),
        });
    });

    /* ── Audio engine ── */
    const nowPlaying = document.getElementById("now-playing");
    const progressEl = document.getElementById("audio-progress");
    const spinner = document.getElementById("audio-spinner");
    const statusEl = document.getElementById("audio-status");
    const phaseLbl = document.getElementById("phase-label");

    let activeAudio = null;

    function playAudio(url, label, onEnded) {
        if (activeAudio) {
            activeAudio.onended = null;
            activeAudio.pause();
            activeAudio = null;
        }

        nowPlaying.textContent = label;
        progressEl.style.width = "0%";
        spinner.classList.remove("hidden");
        statusEl.textContent = "Memuat...";

        const a = new Audio(url);
        activeAudio = a;

        a.addEventListener("timeupdate", function () {
            if (a.duration > 0) {
                progressEl.style.width = (a.currentTime / a.duration) * 100 + "%";
            }
        });

        a.onended = function () {
            statusEl.textContent = "Selesai";
            progressEl.style.width = "100%";
            if (onEnded) {
                onEnded();
            }
        };

        a.play()
            .then(() => {
                spinner.classList.add("hidden");
                statusEl.textContent = "Sedang diputar";
            })
            .catch((err) => {
                spinner.classList.add("hidden");
                statusEl.textContent = err.name + ": " + err.message;
            });
    }

    /* ── UI helpers ── */
    const instrPanel = document.getElementById("instruction-panel");
    const instrA = document.getElementById("instr-a");
    const instrB = document.getElementById("instr-b");
    const instrC = document.getElementById("instr-c");
    const partAEl = document.getElementById("part-a-container");
    const partBEl = document.getElementById("part-b-container");
    const partCEl = document.getElementById("part-c-container");

    function showOnly(...elements) {
        [instrPanel, instrA, instrB, instrC, partAEl, partBEl, partCEl].forEach((el) => {
            if (el) {
                el.classList.add("hidden");
            }
        });
        elements.forEach((el) => {
            if (el) {
                el.classList.remove("hidden");
            }
        });
    }

    /* ── Part A ── */
    let partAIndex = 0;
    const partAProgBar = document.getElementById("part-a-progress-bar");
    const partACounter = document.getElementById("part-a-counter");
    const partANextBtn = document.getElementById("part-a-next");

    function showPartAQuestion(index) {
        document.querySelectorAll(".part-a-question").forEach((el) => el.classList.add("hidden"));
        const el = document.getElementById("part-a-q-" + index);
        if (!el) {
            return;
        }
        el.classList.remove("hidden");
        el.scrollIntoView({ behavior: "smooth", block: "start" });

        const pct = Math.round(((index + 1) / cfg.partACount) * 100);
        partAProgBar.style.width = pct + "%";
        partACounter.textContent = index + 1 + " / " + cfg.partACount;
        phaseLbl.textContent = "Part A — Soal " + (index + 1) + " dari " + cfg.partACount;
        partANextBtn.textContent = index < cfg.partACount - 1 ? "Soal Berikutnya →" : "Selesai Part A →";

        const audioUrl = el.dataset.audio;
        if (audioUrl) {
            playAudio(audioUrl, "Soal " + (index + 1), null);
        } else {
            nowPlaying.textContent = "Soal " + (index + 1);
            statusEl.textContent = "(tanpa audio)";
            spinner.classList.add("hidden");
        }
    }

    document.getElementById("start-part-a-btn").addEventListener("click", function () {
        markListeningStarted();
        showOnly(partAEl);
        partAIndex = 0;
        showPartAQuestion(0);
    });

    partANextBtn.addEventListener("click", function () {
        // Guard: require answer before advancing
        const currentEl = document.getElementById("part-a-q-" + partAIndex);
        if (currentEl && !currentEl.querySelector('input[type="radio"]:checked')) {
            showToast("Pilih jawaban terlebih dahulu sebelum lanjut.");
            return;
        }

        partAIndex++;
        if (partAIndex < cfg.partACount) {
            showPartAQuestion(partAIndex);
        } else {
            lockBtn(partANextBtn);
            phaseLbl.textContent = "Akhir Part A";
            playAudio(cfg.urlEndingA, "Ending Part A", function () {
                showOnly(instrPanel, instrB);
                phaseLbl.textContent = "Petunjuk Part B";
                playAudio(cfg.urlInstructionB, "Petunjuk Part B", null);
            });
        }
    });

    /* ── One-shot button lock ── */
    function lockBtn(btn) {
        btn.disabled = true;
        btn.style.opacity = "0.5";
        btn.style.cursor = "not-allowed";
    }

    /* ── Answer guard helper ── */
    function allAnswered(containerEl) {
        const names = new Set();
        containerEl.querySelectorAll('input[type="radio"]').forEach((r) => names.add(r.name));
        for (const name of names) {
            if (!containerEl.querySelector('input[name="' + name + '"]:checked')) {
                return false;
            }
        }
        return names.size > 0;
    }

    /* ── Part B ── */
    let bIndex = 0;

    function showBGroup(index) {
        document.querySelectorAll(".part-b-group").forEach((el) => el.classList.add("hidden"));
        const el = document.getElementById("part-b-group-" + index);
        if (!el) {
            return;
        }
        el.classList.remove("hidden");
        el.scrollIntoView({ behavior: "smooth", block: "start" });
        phaseLbl.textContent = "Part B — Percakapan " + (index + 1) + " dari " + cfg.partBCount;
        playAudio(el.dataset.audio, el.dataset.label, null);
    }

    document.getElementById("start-part-b-btn").addEventListener("click", function () {
        showOnly(partBEl);
        bIndex = 0;
        showBGroup(0);
    });

    document.querySelectorAll(".b-next-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const currentGroup = document.getElementById("part-b-group-" + bIndex);
            if (currentGroup && !allAnswered(currentGroup)) {
                showToast("Jawab semua soal dalam percakapan ini sebelum lanjut.");
                return;
            }
            lockBtn(this);
            bIndex = parseInt(this.dataset.next);
            showBGroup(bIndex);
        });
    });

    const bFinishBtn = document.getElementById("b-finish-btn");
    if (bFinishBtn) {
        bFinishBtn.addEventListener("click", function () {
            const currentGroup = document.getElementById("part-b-group-" + bIndex);
            if (currentGroup && !allAnswered(currentGroup)) {
                showToast("Jawab semua soal dalam percakapan ini sebelum lanjut.");
                return;
            }
            lockBtn(this);
            phaseLbl.textContent = "Akhir Part B";
            playAudio(cfg.urlEndingB, "Ending Part B", function () {
                showOnly(instrPanel, instrC);
                phaseLbl.textContent = "Petunjuk Part C";
                playAudio(cfg.urlInstructionC, "Petunjuk Part C", null);
            });
        });
    }

    /* ── Part C ── */
    let cIndex = 0;

    function showCGroup(index) {
        document.querySelectorAll(".part-c-group").forEach((el) => el.classList.add("hidden"));
        const el = document.getElementById("part-c-group-" + index);
        if (!el) {
            return;
        }
        el.classList.remove("hidden");
        el.scrollIntoView({ behavior: "smooth", block: "start" });
        phaseLbl.textContent = "Part C — Ceramah " + (index + 1) + " dari " + cfg.partCCount;
        playAudio(el.dataset.audio, el.dataset.label, null);
    }

    document.getElementById("start-part-c-btn").addEventListener("click", function () {
        showOnly(partCEl);
        cIndex = 0;
        showCGroup(0);
    });

    document.querySelectorAll(".c-next-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const currentGroup = document.getElementById("part-c-group-" + cIndex);
            if (currentGroup && !allAnswered(currentGroup)) {
                showToast("Jawab semua soal dalam ceramah ini sebelum lanjut.");
                return;
            }
            lockBtn(this);
            cIndex = parseInt(this.dataset.next);
            showCGroup(cIndex);
        });
    });

    const cFinishBtn = document.getElementById("c-finish-btn");
    if (cFinishBtn) {
        cFinishBtn.addEventListener("click", function () {
            const currentGroup = document.getElementById("part-c-group-" + cIndex);
            if (currentGroup && !allAnswered(currentGroup)) {
                showToast("Jawab semua soal dalam ceramah ini sebelum lanjut.");
                return;
            }
            lockBtn(this);
            phaseLbl.textContent = "Akhir Listening";
            playAudio(cfg.urlEndingC, "Ending Listening Section", function () {
                nowPlaying.textContent = "Listening selesai";
                statusEl.textContent = "";
                spinner.classList.add("hidden");
                phaseLbl.textContent = "Selesai — silakan submit";
                submitBtn.disabled = false;
            });
        });
    }

    /* ── Entry (guard double-fire from exam-security overlay) ── */
    let examStarted = false;
    function examStart() {
        if (examStarted) {
            return;
        }
        examStarted = true;
        showOnly(instrPanel, instrA);
        phaseLbl.textContent = "Petunjuk Part A";
        playAudio(cfg.urlInstructionA, "Petunjuk Part A", null);
    }

    window.addEventListener("exam-start", examStart);
    window._examStart = examStart;
})();
