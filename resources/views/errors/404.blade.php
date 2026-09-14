<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>404 — Page Didn't Show Up | MINDSIA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#F8FAFC] text-[#111827] font-inter antialiased flex items-center justify-center p-6">

    <div class="w-full max-w-xl">

        <!-- "Test paper" card -->
        <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden">

            <!-- Header strip -->
            <div class="bg-[#5586DB] px-8 py-5 flex items-center justify-between">
                <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-8 w-auto filter brightness-0 invert opacity-90">
                <span class="font-jakarta font-extrabold text-white/80 text-[13px] uppercase tracking-widest">Error Report</span>
            </div>

            <!-- Body -->
            <div class="px-8 py-8">

                <!-- "Score" badge -->
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-[#9CA3AF] mb-1">HTTP Status</p>
                        <p class="font-jakarta font-extrabold text-[56px] leading-none text-[#5586DB]">404</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-[#9CA3AF] mb-1">Score</p>
                        <p class="font-jakarta font-extrabold text-[56px] leading-none text-red-400">0 / 10</p>
                    </div>
                </div>

                <div class="border-t border-dashed border-[#E5E7EB] pt-6 mb-6">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-[#9CA3AF] mb-3">Teacher's Note</p>
                    <h1 class="font-jakarta font-extrabold text-[22px] text-[#111827] mb-3 leading-snug">
                        "This page did not attend class."
                    </h1>
                    <p class="text-[14px] text-[#4B5563] leading-relaxed">
                        We searched our entire vocabulary — past tense, present perfect, even irregular verbs — and still couldn't find this URL. It might have skipped the lesson on <em>existing</em>.
                    </p>
                </div>

                <!-- Vocab entry -->
                <div class="bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl px-5 py-4 mb-8">
                    <p class="text-[13px] text-[#111827]">
                        <span class="font-bold">404</span>
                        <span class="text-[#9CA3AF] text-[12px] mx-1">/fɔːr.oʊ.fɔːr/</span>
                        <span class="italic text-[#9CA3AF] text-[12px]">noun</span>
                    </p>
                    <p class="text-[13px] text-[#4B5563] mt-1">
                        <span class="font-bold">1.</span> A page that was supposed to be here but chose the path of truancy instead.
                        <span class="italic text-[#9CA3AF]">Syn: missing, absent, AWOL.</span>
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="javascript:history.back()"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-white border border-[#E5E7EB] rounded-xl text-[14px] font-bold text-[#4B5563] hover:border-[#5586DB] hover:text-[#5586DB] transition-all">
                        ← Kembali
                    </a>
                    <a href="/"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#5586DB] rounded-xl text-[14px] font-bold text-white hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)]">
                        Ke Beranda
                    </a>
                </div>
            </div>

        </div>

        <p class="text-center text-[12px] text-[#9CA3AF] mt-5 font-medium">
            "The page you seek does not exist. Unlike your future in English — that one's bright." ✦
        </p>

    </div>

</body>
</html>
