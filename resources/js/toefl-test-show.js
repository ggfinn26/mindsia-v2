document.addEventListener("alpine:init", () => {
    Alpine.data("toeflTestShow", () => ({
        activeTab: "listening",
        showQuestionModal: false,
        questionMode: "create",
        questionSection: "listening",
        questionType: "multiple_choice",
        questionId: null,
        showPublishModal: false,
        showUnpublishModal: false,
        showPassageModal: false,
        passageMode: "create",
        passageId: null,
        passageSection: "listening",

        get questionPlaceholder() {
            if (this.questionType === "written_expression") {
                return "Contoh: According to studies, body language [A]expresses[/A] a speaker's [B]emotions[/B] and [C]attitudes[/C] more than [D]his words[/D]";
            }
            if (this.questionSection === "structure") {
                return "Contoh: Halley's Comet had its first documented sighting in 240 B.C. and _____ it has been seen 29 times.";
            }
            return "Teks pertanyaan";
        },

        openQuestionModal(section, mode = "create", q = null) {
            this.questionSection = section;
            this.questionMode = mode;
            this.questionId = q ? q.id : null;
            if (mode === "edit" && q) {
                this.questionType = q.question_type || "multiple_choice";
                document.getElementById("q-section").value = q.section;
                document.getElementById("q-question-type").value = q.question_type || "multiple_choice";
                document.getElementById("q-display-order").value = q.display_order;
                document.getElementById("q-passage-id").value = q.passage_id ?? "";
                document.getElementById("q-question-text").value = q.question_text;
                document.getElementById("q-option-a").value = q.option_a ?? "";
                document.getElementById("q-option-b").value = q.option_b ?? "";
                document.getElementById("q-option-c").value = q.option_c ?? "";
                document.getElementById("q-option-d").value = q.option_d ?? "";
                document.getElementById("q-correct-option").value = q.correct_option;
                document.getElementById("q-difficulty").value = q.difficulty;
                document.getElementById("q-explanation").value = q.explanation ?? "";
            } else {
                document.getElementById("question-form").reset();
                this.questionType = "multiple_choice";
                document.getElementById("q-section").value = section;
                document.getElementById("q-question-type").value = "multiple_choice";
            }
            this.showQuestionModal = true;
        },

        openPassageModal(section, mode = "create", p = null) {
            this.passageSection = section;
            this.passageMode = mode;
            this.passageId = p ? p.id : null;
            if (mode === "edit" && p) {
                document.getElementById("p-section").value = p.section;
                document.getElementById("p-title").value = p.title;
                const bodyEl = document.getElementById("p-body-text");
                if (bodyEl) {
                    bodyEl.value = p.body_text ?? "";
                    bodyEl.dispatchEvent(new Event("input"));
                }
                document.getElementById("p-display-order").value = p.display_order;
                const audioEl = document.getElementById("p-audio-media-id");
                if (audioEl) audioEl.value = p.audio_media_id ?? "";
            } else {
                document.getElementById("passage-form").reset();
                const bodyEl = document.getElementById("p-body-text");
                if (bodyEl) bodyEl.dispatchEvent(new Event("input"));
                document.getElementById("p-section").value = section;
            }
            this.showPassageModal = true;
        },
    }));
});
