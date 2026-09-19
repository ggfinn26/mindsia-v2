<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Pendaftaran Sukses - MINDSIA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" rel="stylesheet"></noscript>
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="w-full h-screen overflow-hidden bg-[#F8FAFC] text-[#111827] font-inter antialiased">
    <div class="relative w-full h-full overflow-hidden">
        <!-- Left Cover -->
        <div class="absolute left-0 top-0 w-full flex flex-col items-center justify-center text-center overflow-hidden z-20 h-[15vh] md:h-full md:w-[40%] lg:w-[45%] bg-[#5586DB]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#5586DB]/90 to-[#00AACC]/90"></div>
            <div class="absolute inset-0 animated-pattern pointer-events-none"></div>

            <div class="relative z-10 w-full flex flex-col items-center md:items-center">
                <div class="max-w-md px-10 text-left w-full">
                    <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-10 md:h-20 w-auto mb-2 md:mb-8 filter brightness-0 invert opacity-90 drop-shadow-md">
                    <h2 class="hidden md:block font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">Pendaftaran <br><span class="text-[#F8FAFC]/90">Sukses</span></h2>
                    <p class="hidden md:block text-[16px] text-white/80 leading-relaxed font-medium">Selamat bergabung! Akun Anda telah berhasil diverifikasi dan siap digunakan.</p>
                    <h2 class="md:hidden font-jakarta font-extrabold text-[22px] tracking-tight text-white leading-tight">Pendaftaran Sukses</h2>
                </div>
            </div>

            <div class="hidden md:block absolute bottom-10 left-10 text-[12px] font-bold text-white/40 uppercase tracking-widest font-jakarta">
                Access Granted
            </div>
        </div>

        <!-- Right Form -->
        <div class="absolute left-0 md:left-auto right-0 bottom-0 w-full md:w-[60%] lg:w-[55%] bg-white shadow-[-10px_0_30px_rgba(17,24,39,0.02)] z-30 flex flex-col h-[85vh] md:h-full">
            <div class="relative w-full h-full overflow-y-auto overflow-x-hidden p-6 md:p-16 lg:p-24 flex flex-col justify-start md:justify-center pt-20 md:pt-16">
                <!-- Back Button -->
                <a href="{{ url('/') }}"
                        class="absolute top-6 left-6 md:top-10 md:left-10 text-[#4B5563] hover:text-[#111827] transition-colors flex items-center gap-1.5 font-bold text-sm z-40">
                    <span class="material-symbols-outlined text-[16px]">home</span>
                    <span class="hidden md:inline">Beranda</span>
                </a>

                <div class="max-w-[400px] w-full mx-auto">

                    <div class="w-16 h-16 bg-[#5586DB]/10 rounded-full flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-[32px] text-[#5586DB]">verified_user</span>
                    </div>

                    <h1 class="font-jakarta font-extrabold text-[28px] md:text-[32px] text-[#111827] mb-2 tracking-tight">Sukses!</h1>
                    <p class="text-[15px] text-[#4B5563] mb-8 font-medium">Akun Anda telah berhasil diverifikasi dan aktif.</p>

                    <div class="bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl p-5 mb-8 shadow-sm">
                        <p class="text-[13px] font-bold text-[#111827] mb-3">Apa selanjutnya?</p>
                        <ol class="space-y-3">
                            <li class="flex items-center gap-3 text-[13px] text-[#4B5563] font-medium">
                                <div class="w-6 h-6 rounded-full bg-white border border-[#E5E7EB] flex items-center justify-center text-[#5586DB] font-bold text-[11px] shadow-sm">1</div>
                                Buka dashboard akun Anda
                            </li>
                            <li class="flex items-center gap-3 text-[13px] text-[#4B5563] font-medium">
                                <div class="w-6 h-6 rounded-full bg-white border border-[#E5E7EB] flex items-center justify-center text-[#5586DB] font-bold text-[11px] shadow-sm">2</div>
                                Lengkapi informasi profil Anda
                            </li>
                            <li class="flex items-center gap-3 text-[13px] text-[#4B5563] font-medium">
                                <div class="w-6 h-6 rounded-full bg-white border border-[#E5E7EB] flex items-center justify-center text-[#5586DB] font-bold text-[11px] shadow-sm">3</div>
                                Nikmati semua fitur MINDSIA
                            </li>
                        </ol>
                    </div>

                    <div class="space-y-4">
                        @php
                            $dashboardRoute = route('login');
                            $btnText = "Masuk Sekarang";
                            $icon = "login";
                            if (auth('web')->check()) {
                                $dashboardRoute = route('dashboard');
                                $btnText = "Buka Dashboard";
                                $icon = "arrow_forward";
                            } elseif (auth('member')->check()) {
                                $dashboardRoute = route('member.dashboard');
                                $btnText = "Buka Dashboard";
                                $icon = "arrow_forward";
                            } elseif (auth('applicant')->check()) {
                                $dashboardRoute = route('applicant.dashboard');
                                $btnText = "Buka Dashboard";
                                $icon = "arrow_forward";
                            }
                        @endphp

                        <a href="{{ $dashboardRoute }}" class="w-full bg-[#5586DB] text-white font-bold py-3.5 px-4 rounded-xl hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2">
                            {{ $btnText }} <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
