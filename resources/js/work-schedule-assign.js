document.addEventListener("alpine:init", () => {
    Alpine.data("workScheduleAssign", () => ({
        open: false,
        type: "",
        typeLabel: "",
        options: [
            { value: "role", label: "Role" },
            { value: "position", label: "Posisi" },
            { value: "employee", label: "Pegawai" },
        ],

        pick(value, label) {
            this.type = value;
            this.typeLabel = label;
            this.open = false;
        },

        selectAll() {
            this.$el.querySelectorAll('input[name="assignable_id[]"]').forEach((i) => (i.checked = true));
        },
    }));
});
