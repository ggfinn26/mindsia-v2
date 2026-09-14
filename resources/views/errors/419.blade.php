<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>419 — Session Expired | MINDSIA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#F8FAFC] text-[#111827] font-inter antialiased flex items-center justify-center p-6">

    <div class="w-full max-w-xl">

        <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-[#5586DB] px-8 py-5 flex items-center justify-between">
                <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-8 w-auto filter brightness-0 invert opacity-90">
                <span class="font-jakarta font-extrabold text-white/80 text-[13px] uppercase tracking-widest">Error Report</span>
            </div>

            <div class="px-8 py-8">

                <div class="flex items-start justify-between mb-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-[#9CA3AF] mb-1">HTTP Status</p>
                        <p class="font-jakarta font-extrabold text-[56px] leading-none text-[#5586DB]">419</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-[#9CA3AF] mb-1">Time Left</p>
                        <p class="font-jakarta font-extrabold text-[24px] leading-none text-red-400 mt-2">00:00</p>
                        <p class="text-[11px] text-[#9CA3AF]">Pencils down.</p>
                    </div>
                </div>

                <div class="border-t border-dashed border-[#E5E7EB] pt-6 mb-6">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-[#9CA3AF] mb-3">Teacher's Note</p>
                    <h1 class="font-jakarta font-extrabold text-[22px] text-[#111827] mb-3 leading-snug">
                        "Time's up. Your session has expired."
                    </h1>
                    <p class="text-[14px] text-[#4B5563] leading-relaxed">
                        You left the page open too long — like staring at a blank TOEFL essay until the proctor calls time. Refresh and try again; the vocabulary hasn't changed.
                    </p>
                </div>

                <div class="bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl px-5 py-4 mb-8">
                    <p class="text-[13px] text-[#111827]">
                        <span class="font-bold">expired</span>
                        <span class="text-[#9CA3AF] text-[12px] mx-1">/ɪkˈspaɪərd/</span>
                        <span class="italic text-[#9CA3AF] text-[12px]">verb, past tense</span>
                    </p>
                    <p class="text-[13px] text-[#4B5563] mt-1">
                        <span class="font-bold">1.</span> No longer valid. Like using "thou" in a modern essay — technically a word, practically a problem.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="javascript:location.reload()"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-white border border-[#E5E7EB] rounded-xl text-[14px] font-bold text-[#4B5563] hover:border-[#5586DB] hover:text-[#5586DB] transition-all">
                        ↺ Refresh
                    </a>
                    <a href="/"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#5586DB] rounded-xl text-[14px] font-bold text-white hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)]">
                        Ke Beranda
                    </a>
                </div>
            </div>

        </div>

        <p class="text-center text-[12px] text-[#9CA3AF] mt-5 font-medium">
            "A session expiring is just the universe saying: review your flashcards." ✦
        </p>

    </div>

</body>
</html>
