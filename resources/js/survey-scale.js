document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".scale-question").forEach((group) => {
        const inputs = group.querySelectorAll('input[type="radio"]');
        inputs.forEach((input) => {
            input.addEventListener("change", () => {
                inputs.forEach((i) => {
                    i.parentElement.classList.remove("bg-[#215aac]", "text-white", "border-[#215aac]");
                    i.nextElementSibling?.classList.remove("text-white");
                });
                if (input.checked) {
                    input.parentElement.classList.add("bg-[#215aac]", "text-white", "border-[#215aac]");
                    input.nextElementSibling?.classList.add("text-white");
                }
            });
        });
    });
});
