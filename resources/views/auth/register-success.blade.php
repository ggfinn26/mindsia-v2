<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Akun Berhasil Dibuat - MINDSIA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" rel="stylesheet"></noscript>
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="w-full min-h-screen bg-[#F8FAFC] text-[#111827] font-inter antialiased flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-14 w-auto mx-auto">
        </div>

        <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm p-8 md:p-10 text-center">
            <div class="w-16 h-16 bg-[#5586DB]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-[32px] text-[#5586DB]">mark_email_read</span>
            </div>

            <h1 class="font-jakarta font-extrabold text-[24px] text-[#111827] mb-3 tracking-tight">Akun Berhasil Dibuat</h1>
            <p class="text-[14px] text-[#4B5563] font-medium mb-6 leading-relaxed">
                Kami telah mengirimkan link verifikasi ke email Anda. Silakan cek inbox untuk mengaktifkan akun.
            </p>

            <div class="bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl p-4 mb-8 text-left">
                <p class="text-[12px] font-bold text-[#4B5563] uppercase tracking-widest mb-3">Langkah berikutnya</p>
                <ol class="space-y-2">
                    <li class="flex items-start gap-3 text-[13px] text-[#4B5563]">
                        <span class="w-5 h-5 rounded-full bg-[#5586DB] text-white text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">1</span>
                        Buka email Anda
                    </li>
                    <li class="flex items-start gap-3 text-[13px] text-[#4B5563]">
                        <span class="w-5 h-5 rounded-full bg-[#5586DB] text-white text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">2</span>
                        Klik link verifikasi dalam email
                    </li>
                    <li class="flex items-start gap-3 text-[13px] text-[#4B5563]">
                        <span class="w-5 h-5 rounded-full bg-[#5586DB] text-white text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">3</span>
                        Kembali ke halaman login untuk masuk
                    </li>
                </ol>
            </div>

            <p class="text-[12px] text-[#9CA3AF] mb-6">
                Tidak menerima email? Periksa folder spam atau hubungi tim support.
            </p>

            <a href="{{ route('login') }}"
               class="w-full bg-[#5586DB] text-white font-bold py-3 px-4 rounded-xl hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2">
                Ke Halaman Login <span class="material-symbols-outlined text-[16px]">login</span>
            </a>
        </div>
    </div>
</body>
</html>
