document.addEventListener("DOMContentLoaded", function () {
    var root = document.getElementById("calendar");
    if (!root) return;

    var config = JSON.parse(root.dataset.config || "{}");
    var events = config.events || [];
    var holidays = config.holidays || [];
    var holidayMap = config.holidayMap || {};

    var MONTHS = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];
    var DAYS_SHORT = ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"];
    var DAYS_FULL = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

    var byDate = {};
    events.forEach(function (e) {
        if (!byDate[e.date]) byDate[e.date] = [];
        byDate[e.date].push(e);
    });

    var today = new Date();
    var cur = { y: today.getFullYear(), m: today.getMonth() };

    function pad(n) {
        return n < 10 ? "0" + n : "" + n;
    }

    function dateKey(y, m, d) {
        return y + "-" + pad(m + 1) + "-" + pad(d);
    }

    function esc(s) {
        return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
            return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
        });
    }

    function render() {
        var y = cur.y;
        var m = cur.m;

        document.getElementById("cal-title").textContent = MONTHS[m] + " " + y;

        var firstDow = new Date(y, m, 1).getDay();
        var startOff = (firstDow + 6) % 7;
        var daysInMon = new Date(y, m + 1, 0).getDate();
        var daysInPrev = new Date(y, m, 0).getDate();
        var todayKey = dateKey(today.getFullYear(), today.getMonth(), today.getDate());

        var cells = [];
        for (var i = startOff - 1; i >= 0; i--) {
            cells.push({ d: daysInPrev - i, own: false, key: null });
        }
        for (var d = 1; d <= daysInMon; d++) {
            cells.push({ d: d, own: true, key: dateKey(y, m, d) });
        }
        while (cells.length < 42) {
            cells.push({ d: cells.length - startOff - daysInMon + 1, own: false, key: null });
        }

        var html = '<div class="grid grid-cols-7 border-b border-gray-100">';
        DAYS_SHORT.forEach(function (day) {
            html +=
                '<div class="text-center text-xs font-bold text-gray-400 uppercase tracking-wide py-2">' +
                day +
                "</div>";
        });
        html += "</div>";

        html += '<div class="grid grid-cols-7">';
        cells.forEach(function (cell, idx) {
            var isHoliday = cell.key && holidays.indexOf(cell.key) !== -1;
            var evs = cell.key ? byDate[cell.key] || [] : [];
            var isToday = cell.key === todayKey;
            var clickable = cell.own && (evs.length > 0 || isHoliday);

            var borders = (idx + 1) % 7 === 0 ? "border-b border-gray-100" : "border-r border-b border-gray-100";
            var bg = !cell.own ? "bg-gray-50/60" : isHoliday ? "bg-red-50" : "";

            html +=
                '<div class="' +
                borders +
                " " +
                bg +
                " min-h-[76px] p-1.5" +
                (clickable ? " cursor-pointer hover:bg-gray-50 transition-colors" : "") +
                '"' +
                (cell.key && cell.own ? ' data-date="' + cell.key + '"' : "") +
                ">";

            if (isToday) {
                html +=
                    '<div class="w-6 h-6 rounded-full bg-[#5586DB] text-white text-xs font-bold flex items-center justify-center mb-1 mx-auto leading-none">' +
                    cell.d +
                    "</div>";
            } else {
                var nc = !cell.own ? "text-gray-300" : isHoliday ? "text-red-400 font-medium" : "text-gray-600";
                html += '<div class="text-xs ' + nc + ' text-center mb-1 leading-6">' + cell.d + "</div>";
            }

            if (cell.own) {
                evs.slice(0, 2).forEach(function (e) {
                    html +=
                        '<div class="truncate rounded px-1 mb-0.5 leading-[1.35rem] text-white font-semibold" style="font-size:0.6rem;background:' +
                        esc(e.color) +
                        '">' +
                        esc(e.title) +
                        "</div>";
                });
                if (evs.length > 2) {
                    html +=
                        '<div class="text-gray-400 px-1 leading-none" style="font-size:0.6rem">+' +
                        (evs.length - 2) +
                        " lagi</div>";
                }
                if (isHoliday && evs.length === 0) {
                    html +=
                        '<div class="text-red-400 font-medium px-0.5 leading-[1.35rem] truncate" style="font-size:0.6rem">' +
                        esc(holidayMap[cell.key] || "Libur") +
                        "</div>";
                }
            }

            html += "</div>";
        });
        html += "</div>";

        root.innerHTML = html;

        root.querySelectorAll("[data-date]").forEach(function (el) {
            el.addEventListener("click", function () {
                openModal(el.getAttribute("data-date"));
            });
        });
    }

    function openModal(key) {
        var evs = byDate[key] || [];
        var isHol = holidays.indexOf(key) !== -1;
        var holName = holidayMap[key];
        if (evs.length === 0 && !isHol) return;

        var parts = key.split("-");
        var dt = new Date(+parts[0], +parts[1] - 1, +parts[2]);
        var label = DAYS_FULL[dt.getDay()] + ", " + dt.getDate() + " " + MONTHS[dt.getMonth()] + " " + dt.getFullYear();

        var body = "";

        if (isHol && holName) {
            body +=
                '<div class="flex items-center gap-2 px-3 py-2 bg-red-50 rounded-lg mb-3">' +
                '<span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>' +
                '<span class="text-sm text-red-600 font-medium">' +
                esc(holName) +
                "</span></div>";
        }

        evs.forEach(function (e, i) {
            var p = e.props || {};
            var border = i < evs.length - 1 ? "border-b border-gray-100" : "";
            body +=
                '<div class="flex gap-3 py-3 ' +
                border +
                '">' +
                '<div class="w-0.5 rounded-full flex-shrink-0 self-stretch" style="background:' +
                esc(e.color) +
                '"></div>' +
                '<div class="flex-1 min-w-0">' +
                '<div class="font-semibold text-gray-800 text-sm leading-snug">' +
                esc(e.title) +
                "</div>" +
                (p.time ? '<div class="text-xs text-gray-400 mt-1">' + esc(p.time) + "</div>" : "") +
                (p.tutor ? '<div class="text-xs text-gray-500 mt-0.5">' + esc(p.tutor) + "</div>" : "") +
                (p.material && p.material !== "-"
                    ? '<div class="text-xs text-gray-400 mt-0.5 truncate">' + esc(p.material) + "</div>"
                    : "") +
                "</div></div>";
        });

        var modal = document.createElement("dialog");
        modal.className =
            "p-0 rounded-2xl shadow-xl border-0 overflow-hidden w-full max-w-xs m-auto backdrop:bg-gray-900/40";
        modal.innerHTML =
            '<div class="p-5">' +
            '<div class="flex items-center justify-between mb-4">' +
            '<span class="text-sm font-bold text-gray-900 font-jakarta">' +
            esc(label) +
            "</span>" +
            '<button type="button" class="close-btn w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">' +
            '<span class="material-symbols-outlined text-gray-500" style="font-size:13px">close</span>' +
            "</button></div>" +
            "<div>" +
            body +
            "</div>" +
            "</div>";

        document.body.appendChild(modal);
        modal.showModal();
        modal.querySelector(".close-btn").addEventListener("click", function () {
            modal.close();
        });
        modal.addEventListener("click", function (e) {
            if (e.target === modal) modal.close();
        });
        modal.addEventListener("close", function () {
            setTimeout(function () {
                modal.remove();
            }, 150);
        });
    }

    document.getElementById("cal-prev").addEventListener("click", function () {
        cur.m--;
        if (cur.m < 0) {
            cur.m = 11;
            cur.y--;
        }
        render();
    });
    document.getElementById("cal-next").addEventListener("click", function () {
        cur.m++;
        if (cur.m > 11) {
            cur.m = 0;
            cur.y++;
        }
        render();
    });
    document.getElementById("cal-today").addEventListener("click", function () {
        cur.y = today.getFullYear();
        cur.m = today.getMonth();
        render();
    });

    render();
});
