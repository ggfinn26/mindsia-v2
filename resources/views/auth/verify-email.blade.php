<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Verifikasi Email - MINDSIA</title>
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
                    <h2 class="hidden md:block font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">Verifikasi <br><span class="text-[#F8FAFC]/90">Email</span></h2>
                    <p class="hidden md:block text-[16px] text-white/80 leading-relaxed font-medium">Satu langkah lagi untuk mengaktifkan akun Anda. Periksa kotak masuk untuk instruksi lebih lanjut.</p>
                    <h2 class="md:hidden font-jakarta font-extrabold text-[22px] tracking-tight text-white leading-tight">Verifikasi Email</h2>
                </div>
            </div>

            <div class="hidden md:block absolute bottom-10 left-10 text-[12px] font-bold text-white/40 uppercase tracking-widest font-jakarta">
                Security Verification
            </div>
        </div>

        <!-- Right Form -->
        <div class="absolute left-0 md:left-auto right-0 bottom-0 w-full md:w-[60%] lg:w-[55%] bg-white shadow-[-10px_0_30px_rgba(17,24,39,0.02)] z-30 flex flex-col h-[85vh] md:h-full">
            <div class="relative w-full h-full overflow-y-auto overflow-x-hidden p-6 md:p-16 lg:p-24 flex flex-col justify-start md:justify-center pt-20 md:pt-16">
                <!-- Back Button -->
                <a href="{{ url('/') }}"
                        class="absolute top-6 left-6 md:top-10 md:left-10 text-[#4B5563] hover:text-[#111827] transition-colors flex items-center gap-1.5 font-bold text-sm z-40">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span class="hidden md:inline">Kembali</span>
                </a>

                <div class="max-w-[400px] w-full mx-auto">

            <h1 class="font-jakarta font-extrabold text-[28px] md:text-[32px] text-[#111827] mb-2 tracking-tight">Masukkan Kode OTP</h1>
            <p class="text-[15px] text-[#4B5563] mb-8 font-medium">Kode 6 digit telah dikirim ke email Anda. Berlaku 15 menit.</p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-[#ECFDF5] border border-[#A7F3D0] rounded-xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#10B981]">check_circle</span>
                    <p class="text-[13px] text-[#047857] font-medium pt-0.5">Kode OTP baru telah dikirim ulang ke email Anda.</p>
                </div>
            @endif

            @if ($errors->has('otp'))
                <div class="mb-6 p-4 bg-[#FEF2F2] border border-[#FECACA] rounded-xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#EF4444] mt-0.5">error</span>
                    <p class="text-[13px] text-[#B91C1C] font-medium">{{ $errors->first('otp') }}</p>
                </div>
            @endif

            <form action="{{ route('verification.submit') }}" method="POST" class="mb-6">
                @csrf
                <div class="flex gap-2 justify-center mb-6" id="otp-boxes">
                    @for ($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            maxlength="1"
                            inputmode="numeric"
                            pattern="[0-9]"
                            class="w-12 h-14 text-center text-[22px] font-bold border-2 border-[#E5E7EB] rounded-xl focus:border-[#5586DB] focus:outline-none transition-colors"
                            data-otp-index="{{ $i }}"
                        >
                    @endfor
                </div>
                <input type="hidden" name="otp" id="otp-hidden">
                <button
                    type="submit"
                    id="otp-submit"
                    class="w-full bg-[#5586DB] text-white font-bold py-3.5 px-4 rounded-xl hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2"
                >
                    Verifikasi <span class="material-symbols-outlined text-[18px]">verified</span>
                </button>
            </form>

            <div class="space-y-3">
                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="w-full bg-white border border-[#E5E7EB] text-[#4B5563] font-bold py-3 px-4 rounded-xl hover:bg-[#F3F4F6] hover:text-[#111827] transition-all flex justify-center items-center gap-2 text-[14px]"
                    >
                        Kirim Ulang Kode <span class="material-symbols-outlined text-[16px]">forward_to_inbox</span>
                    </button>
                </form>

                @if (auth('web')->check() || auth('member')->check() || auth('applicant')->check())
                @php
                    $logoutRoute = route('logout');
                    if (auth('member')->check()) { $logoutRoute = route('member.logout'); }
                    elseif (auth('applicant')->check()) { $logoutRoute = route('applicant.logout'); }
                @endphp
                <form action="{{ $logoutRoute }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="w-full bg-white border border-[#E5E7EB] text-[#9CA3AF] font-medium py-3 px-4 rounded-xl hover:bg-[#F3F4F6] transition-all flex justify-center items-center gap-2 text-[13px]"
                    >
                        Masuk dengan akun lain
                    </button>
                </form>
                @endif
            </div>


        </div>
        </div>
    </div>
</body>
</html>
