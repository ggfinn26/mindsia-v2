<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Applicant Portal - MINDSIA</title>
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
        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay grayscale">
        <div class="absolute inset-0 bg-gradient-to-br from-[#5586DB]/90 to-[#00AACC]/90 backdrop-blur-[2px]"></div>
        <div class="absolute inset-0 animated-pattern pointer-events-none"></div>
        
        <div class="relative z-10 max-w-md px-10 text-left">
            <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-20 w-auto mb-8 filter brightness-0 invert opacity-90">
            <h2 class="font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">Applicant <br><span class="text-[#F8FAFC]/90">Portal</span></h2>
            <p class="text-[16px] text-white/80 leading-relaxed font-medium">Jelajahi karir dan kembangkan potensi Anda bersama MINDSIA.</p>
        </div>
        
        <div class="absolute bottom-10 left-10 text-[12px] font-bold text-white/50 uppercase tracking-widest font-jakarta">
            Applicant Access Only
        </div>
    </div>

    <!-- Right Form -->
    <div class="w-full md:w-[55%] lg:w-1/2 flex flex-col justify-center p-8 md:p-16 lg:p-24 overflow-y-auto bg-white relative shadow-[-10px_0_30px_rgba(17,24,39,0.02)]">
        <!-- Back Button -->
        <a href="{{ route('applicant.login') }}" class="absolute top-6 left-6 md:top-10 md:left-10 text-[#4B5563] hover:text-[#111827] transition-colors flex items-center gap-1.5 font-bold text-sm z-40">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span class="hidden md:inline">Kembali ke Login</span>
        </a>

        <div class="max-w-[400px] w-full mx-auto pt-10 md:pt-0">
            <div class="md:hidden mb-10">
                <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-14 w-auto">
            </div>

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

            <form action="{{ route('applicant.password.email') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block mb-1.5 text-[13px] font-bold text-[#4B5563]">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[14px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm"
                        placeholder="pelamar@email.com"
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
        </div>
    </div>
</body>
</html>
