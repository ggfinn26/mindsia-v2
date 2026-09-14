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
<body class="w-full flex flex-col md:flex-row bg-[#F8FAFC] text-[#111827] font-inter antialiased min-h-screen">

    <!-- Left Cover -->
    <div class="hidden md:flex md:w-[45%] lg:w-1/2 flex-col items-center justify-center text-center relative overflow-hidden bg-[#5586DB]">
        <div class="absolute inset-0 bg-gradient-to-br from-[#5586DB]/90 to-[#00AACC]/90"></div>

        <div class="relative z-10 max-w-md px-10 text-left">
            <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-20 w-auto mb-8 ml-10 filter brightness-0 invert opacity-90">
            <h2 class="font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">Student <br><span class="text-[#F8FAFC]/90">Portal</span></h2>
            <p class="text-[16px] text-white/80 leading-relaxed font-medium">Akses materi belajar, pantau progress akademik, dan kelola pendaftaran program MINDSIA Anda di sini.</p>
        </div>

        <div class="absolute bottom-10 left-10 text-[12px] font-bold text-white/50 uppercase tracking-widest font-jakarta">
            Student Access Only
        </div>
    </div>

    <!-- Right Form -->
    <div class="w-full md:w-[55%] lg:w-1/2 flex flex-col justify-center p-8 md:p-16 lg:p-24 overflow-y-auto bg-white relative shadow-[-10px_0_30px_rgba(17,24,39,0.02)]">
        <div class="max-w-[400px] w-full mx-auto">
            <div class="md:hidden mb-10">
                <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-14 w-auto">
            </div>


            <h1 class="font-jakarta font-extrabold text-[28px] md:text-[32px] text-[#111827] mb-2 tracking-tight">Selamat datang di MINDSIA</h1>
            <p class="text-[15px] text-[#4B5563] mb-10 font-medium">Silakan masuk untuk mengakses portal belajar Anda.</p>

            @if (session('error'))
                <div class="mb-6 p-3 bg-red-50 border border-red-200 rounded-xl text-[13px] text-red-600 font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('member.login.post') }}" method="POST" class="space-y-5">
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
                    @error('email')
                        <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-[13px] font-bold text-[#4B5563]">Password</label>
                        <a href="{{ route('member.password.request') }}" class="text-[12px] font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Lupa kata sandi?</a>
                    </div>
                    <div class="relative w-full">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[14px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm pr-10"
                            placeholder="••••••••"
                            required
                        >
                        <button type="button" onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.innerHTML = input.type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>';" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center pt-1 pb-4">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="h-4 w-4 text-[#5586DB] focus:ring-[#5586DB] border-[#E5E7EB] rounded"
                    >
                    <label for="remember" class="ml-2.5 block text-[13px] text-[#4B5563] font-medium">Ingat saya</label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#5586DB] text-white font-bold py-3.5 px-4 rounded-xl hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2"
                >
                    Masuk <span class="material-symbols-outlined text-[18px]">login</span>
                </button>
            </form>

            <div class="mt-12 text-center border-t border-[#E5E7EB] pt-8">
                <p class="text-[14px] text-[#4B5563]">
                    Belum punya akun?
                    <a href="{{ route('member.register') }}" class="font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Daftar sekarang</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
