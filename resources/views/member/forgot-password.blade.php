<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Student Portal - MINDSIA</title>
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
                    <h2 class="hidden md:block font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">Student <br><span class="text-[#F8FAFC]/90">Portal</span></h2>
                    <p class="hidden md:block text-[16px] text-white/80 leading-relaxed font-medium">Akses materi belajar, pantau progress akademik, dan kelola pendaftaran program MINDSIA Anda di sini.</p>
                    <h2 class="md:hidden font-jakarta font-extrabold text-[22px] tracking-tight text-white leading-tight">Student Portal</h2>
                </div>
            </div>

            <div class="hidden md:block absolute bottom-10 left-10 text-[12px] font-bold text-white/40 uppercase tracking-widest font-jakarta">
                Student Access Only
            </div>
        </div>

        <!-- Right Form -->
        <div class="absolute left-0 md:left-auto right-0 bottom-0 w-full md:w-[60%] lg:w-[55%] bg-white shadow-[-10px_0_30px_rgba(17,24,39,0.02)] z-30 flex flex-col h-[85vh] md:h-full">
            <div class="relative w-full h-full overflow-y-auto overflow-x-hidden p-6 md:p-16 lg:p-24 flex flex-col justify-start md:justify-center pt-20 md:pt-16">
                <!-- Back Button -->
                <a href="{{ route('member.landing') }}?transition=back"
                        class="absolute top-6 left-6 md:top-10 md:left-10 text-[#4B5563] hover:text-[#111827] transition-colors flex items-center gap-1.5 font-bold text-sm z-40">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span class="hidden md:inline">Kembali</span>
                </a>

                <div class="max-w-[400px] w-full mx-auto">

            <h1 class="font-jakarta font-extrabold text-[28px] md:text-[32px] text-[#111827] mb-2 tracking-tight">Lupa Password</h1>
            <p class="text-[15px] text-[#4B5563] mb-10 font-medium">Masukkan email Anda untuk menerima tautan reset password.</p>

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-[13px] text-green-700 font-medium flex gap-3 items-start">
                    <span class="material-symbols-outlined text-[18px] text-green-600">check_circle</span>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-[13px] text-red-700 font-medium flex gap-3 items-start">
                    <span class="material-symbols-outlined text-[18px] text-red-600">error</span>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('member.password.email') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block mb-1.5 text-[13px] font-bold text-[#4B5563]">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[14px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm"
                        placeholder="member@mindsia.com"
                        required
                        autofocus
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#5586DB] text-white font-bold py-3.5 px-4 rounded-xl hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2"
                >
                    Kirim Tautan Reset <span class="material-symbols-outlined text-[18px]">send</span>
                </button>
            </form>

            <div class="mt-12 text-center border-t border-[#E5E7EB] pt-8">
                <p class="text-[14px] text-[#4B5563]">
                    Ingat password Anda?
                    <a href="{{ route('member.landing', ['view' => 'login', 'transition' => 'back']) }}" class="font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Masuk di sini</a>
                </p>
            </div>
            
            </div>
        </div>
    </div>
</body>
</html>
