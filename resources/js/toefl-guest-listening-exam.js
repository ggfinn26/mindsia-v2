(function () {
    const el = document.getElementById("exam-config");
    if (!el) {
        return;
    }

    const cfg = {
        startedAt: el.dataset.startedAt ? parseInt(el.dataset.startedAt) : null,
        limitSeconds: parseInt(el.dataset.limitSeconds),
        csrfToken: el.dataset.csrf,
        answerUrl: el.dataset.answerUrl,
        playlist: JSON.parse(el.dataset.playlist || "[]"),
    };

    const submitForm = document.getElementById("submit-form");
    const submitBtn = document.getElementById("submit-btn");
    const nowPlaying = document.getElementById("now-playing");
    const progressEl = document.getElementById("audio-progress");
    const spinner = document.getElementById("audio-spinner");
    const statusEl = document.getElementById("audio-status");

    /* ── Timer ── */
    if (cfg.startedAt) {
        (function tick() {
            const elapsed = Math.floor(Date.now() / 1000) - cfg.startedAt;
            const remaining = Math.max(0, cfg.limitSeconds - elapsed);
            const m = String(Math.floor(remaining / 60)).padStart(2, "0");
            const s = String(remaining % 60).padStart(2, "0");
            const timerEl = document.getElementById("timer");
            timerEl.textContent = m + ":" + s;
            if (remaining <= 300) {
                timerEl.classList.replace("text-gray-800", "text-red-600");
            }
            if (remaining <= 0) {
                submitForm.submit();
                return;
            }
            setTimeout(tick, 1000);
        })();
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
                l.classList.remove("border-indigo-400", "bg-indigo-50");
                l.classList.add("border-gray-200");
            });
        radio.closest("label").classList.add("border-indigo-400", "bg-indigo-50");
        radio.closest("label").classList.remove("border-gray-200");
        fetch(cfg.answerUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": cfg.csrfToken },
            body: JSON.stringify({ question_id: radio.dataset.question, selected_option: radio.value }),
        });
    });

    /* ── Playlist audio ── */
    let currentIndex = 0;
    let activeAudio = null;

    function unlockQuestion(qId) {
        const el = document.getElementById("q-" + qId);
        if (el) {
            el.classList.remove("opacity-40", "pointer-events-none");
            el.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    }

    function playNext() {
        if (currentIndex >= cfg.playlist.length) {
            nowPlaying.textContent = "Semua audio selesai";
            statusEl.textContent = "";
            spinner.classList.add("hidden");
            submitBtn.disabled = false;
            document.querySelectorAll("[data-question-id]").forEach((el) => {
                el.classList.remove("opacity-40", "pointer-events-none");
            });
            return;
        }

        const item = cfg.playlist[currentIndex];
        nowPlaying.textContent = item.label;
        progressEl.style.width = "0%";
        spinner.classList.remove("hidden");
        statusEl.textContent = "Memuat...";

        if (activeAudio) {
            activeAudio.onended = null;
            activeAudio.pause();
        }
        const a = new Audio(item.url);
        activeAudio = a;

        a.addEventListener("timeupdate", function () {
            if (a.duration > 0) {
                progressEl.style.width = (a.currentTime / a.duration) * 100 + "%";
            }
        });

        a.onended = function () {
            if (item.type === "question" && item.question_id) {
                unlockQuestion(item.question_id);
            }
            if (item.type === "passage") {
                document.querySelectorAll("[data-question-id]").forEach((el) => {
                    el.classList.remove("opacity-40", "pointer-events-none");
                });
            }
            currentIndex++;
            playNext();
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

    /* ── Entry (guard double-fire) ── */
    let examStarted = false;
    function examStart() {
        if (examStarted) {
            return;
        }
        examStarted = true;
        playNext();
    }

    window.addEventListener("exam-start", examStart);
    window._examStart = examStart;
})();
