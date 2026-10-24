// Minimal WYSIWYG: bold / italic / underline / lists / alignment via
// contenteditable + execCommand. No dependency. HTML is sanitized server-side
// (mews/purifier) on save, so only the display markup lives in the textarea.

function initRichEditor(root) {
    const editable = root.querySelector("[data-rich-content]");
    const textarea = root.querySelector("[data-rich-input]");
    if (!editable || !textarea) return;

    const buttons = Array.from(root.querySelectorAll("[data-cmd]"));

    const sync = () => {
        const html = editable.innerHTML.trim();
        // Treat an empty editor (just <br> or empty) as a blank value.
        textarea.value = editable.textContent.trim() === "" ? "" : html;
    };

    // Paint a button on/off. Active = filled with the theme primary colour so
    // the user can see "this text is bold / centered" at a glance.
    const paint = (btn, active) => {
        btn.style.backgroundColor = active ? "var(--dash-primary)" : "transparent";
        btn.style.color = active ? "#fff" : "var(--dash-text-body)";
        btn.dataset.active = active ? "1" : "0";
    };

    // Reflect the current caret/selection state onto every toolbar button.
    const refresh = () => {
        // Only react while the selection is inside this editor.
        const sel = document.getSelection();
        if (!sel || !sel.rangeCount || !editable.contains(sel.anchorNode)) return;
        buttons.forEach((btn) => {
            let active = false;
            try {
                active = document.queryCommandState(btn.dataset.cmd);
            } catch (_) {
                active = false;
            }
            paint(btn, active);
        });
    };

    buttons.forEach((btn) => {
        btn.addEventListener("mousedown", (e) => {
            e.preventDefault(); // keep the text selection in the editable
            editable.focus();
            try {
                document.execCommand("styleWithCSS", false, true);
            } catch (_) {
                /* older browsers ignore this */
            }
            document.execCommand(btn.dataset.cmd, false, null);
            sync();
            refresh();
        });
        // Hover only nudges inactive buttons; active ones keep their fill.
        btn.addEventListener("mouseover", () => {
            if (btn.dataset.active !== "1") btn.style.backgroundColor = "var(--dash-card-bg)";
        });
        btn.addEventListener("mouseout", () => {
            if (btn.dataset.active !== "1") btn.style.backgroundColor = "transparent";
        });
    });

    editable.addEventListener("input", () => {
        sync();
        refresh();
    });
    editable.addEventListener("keyup", refresh);
    editable.addEventListener("mouseup", refresh);
    editable.addEventListener("focus", refresh);
    editable.addEventListener("blur", sync);
    // Keep button states in sync as the caret moves around the editor.
    document.addEventListener("selectionchange", refresh);

    // Final safety net: sync right before the form submits.
    const form = root.closest("form");
    if (form) form.addEventListener("submit", sync);
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-rich-editor]").forEach(initRichEditor);
});
