const fmtRp = (n) => "Rp " + new Intl.NumberFormat("id-ID").format(Math.round(n));

function addMonths(date, n) {
    const d = new Date(date);
    d.setMonth(d.getMonth() + n);
    return d;
}

function addYears(date, n) {
    const d = new Date(date);
    d.setFullYear(d.getFullYear() + n);
    return d;
}

function toInputDate(d) {
    return d.toISOString().slice(0, 10);
}

function autoCalcTerminCount() {
    const startVal = document.querySelector('[name="start_date"]')?.value;
    const endVal = document.querySelector('[name="end_date"]')?.value;
    const rentPeriod = document.querySelector('[name="rent_period"]')?.value;
    const terminInput = document.querySelector('[name="termin_count"]');

    if (!startVal || !endVal || !rentPeriod || !terminInput) return;

    const start = new Date(startVal);
    const end = new Date(endVal);
    if (end <= start) return;

    let count;
    if (rentPeriod === "monthly") {
        count = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
    } else if (rentPeriod === "yearly") {
        count = end.getFullYear() - start.getFullYear();
    } else {
        count = 1;
    }

    terminInput.value = Math.max(1, count);
    terminInput.dispatchEvent(new Event("input"));
}

function buildRows(rentAmount, downPayment, terminCount, rentPeriod, startDate) {
    const sisa = rentAmount - downPayment;
    const amtPerTermin = sisa / terminCount;
    const base = new Date(startDate);
    const rows = [];

    for (let i = 0; i < terminCount; i++) {
        let due;
        if (rentPeriod === "monthly") due = addMonths(base, i);
        else if (rentPeriod === "yearly") due = addYears(base, i);
        else due = new Date(base);

        rows.push({ no: i + 1, dueDate: toInputDate(due), amount: amtPerTermin });
    }
    return rows;
}

// Safe: all interpolated values are integers or ISO date strings (YYYY-MM-DD) — no user text.
function renderRows(rows) {
    return rows
        .map(
            (r, i) => `<tr class="hover:bg-slate-50/70">
            <td class="px-4 py-2 font-semibold text-slate-900 w-16">${r.no}</td>
            <td class="px-4 py-2">
                <input type="date" name="termins[${i}][due_date]" value="${r.dueDate}"
                    required
                    class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </td>
            <td class="px-4 py-2">
                <div class="flex rounded-md shadow-sm">
                    <span class="inline-flex items-center rounded-l-md border border-r-0 border-slate-300 bg-slate-50 px-2 text-xs font-bold text-slate-500">Rp</span>
                    <input type="number" name="termins[${i}][amount]" value="${Math.round(r.amount)}"
                        required min="0" step="1"
                        class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </td>
        </tr>`,
        )
        .join("");
}

function updatePreview() {
    const rentAmount = parseInt(document.querySelector('[name="rent_amount"]')?.value) || 0;
    const downPayment = parseInt(document.querySelector('[name="down_payment"]')?.value) || 0;
    const terminCount = parseInt(document.querySelector('[name="termin_count"]')?.value) || 0;
    const rentPeriod = document.querySelector('[name="rent_period"]')?.value;
    const startDateVal = document.querySelector('[name="start_date"]')?.value;

    const preview = document.getElementById("termin-preview");
    if (!preview) return;

    if (!rentAmount || !terminCount || !rentPeriod || !startDateVal) {
        preview.classList.add("hidden");
        return;
    }

    const rows = buildRows(rentAmount, downPayment, terminCount, rentPeriod, startDateVal);
    document.getElementById("preview-rows").innerHTML = renderRows(rows);
    document.getElementById("preview-sisa").textContent = fmtRp(rentAmount - downPayment);
    preview.classList.remove("hidden");
}

document.addEventListener("DOMContentLoaded", () => {
    ["rent_amount", "down_payment", "termin_count", "rent_period", "start_date"].forEach((name) => {
        document.querySelectorAll(`[name="${name}"]`).forEach((el) => {
            el.addEventListener("input", updatePreview);
            el.addEventListener("change", updatePreview);
        });
    });

    ["start_date", "end_date", "rent_period"].forEach((name) => {
        document.querySelector(`[name="${name}"]`)?.addEventListener("change", autoCalcTerminCount);
    });
});
