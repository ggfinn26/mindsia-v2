document.addEventListener("alpine:init", () => {
    Alpine.data("tutorDropdown", (tutors, initialId) => ({
        tutors,
        search: "",
        open: false,
        selectedId: initialId,
        get filtered() {
            if (!this.search) return this.tutors;
            const q = this.search.toLowerCase();
            return this.tutors.filter((t) => t.name.toLowerCase().includes(q));
        },
        get selectedLabel() {
            const t = this.tutors.find((t) => t.id == this.selectedId);
            return t ? t.name + " (" + t.class_count + " kelas)" : "";
        },
        select(t) {
            this.selectedId = t.id;
            this.open = false;
            this.search = "";
        },
    }));
});
