let _confirmAction = null;

window.openConfirmModal = function (title, message, action) {
    _confirmAction = action;
    document.getElementById("confirm-title").textContent = title;
    document.getElementById("confirm-message").textContent = message;
    document.getElementById("confirm-modal").classList.remove("hidden");
};

window.closeConfirmModal = function () {
    document.getElementById("confirm-modal").classList.add("hidden");
    _confirmAction = null;
};

window.submitConfirmAction = function () {
    const action = _confirmAction;
    closeConfirmModal();
    if (action) action();
};

document.addEventListener("alpine:init", () => {
    Alpine.data("memberDropdown", (members) => ({
        members,
        search: "",
        open: false,
        selectedId: "",
        get filtered() {
            if (!this.search) return this.members;
            const q = this.search.toLowerCase();
            return this.members.filter((m) => m.label.toLowerCase().includes(q));
        },
        get selectedLabel() {
            const m = this.members.find((m) => m.id == this.selectedId);
            return m ? m.label : "";
        },
        select(m) {
            this.selectedId = m.id;
            this.open = false;
            this.search = "";
        },
    }));
});
