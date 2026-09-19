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
<body class="w-full h-screen overflow-hidden bg-[#F8FAFC] text-[#111827] font-inter antialiased">
    <div class="relative w-full h-full overflow-hidden">
        <!-- Left Cover -->
        <div class="absolute left-0 top-0 w-full flex flex-col items-center justify-center text-center overflow-hidden z-20 h-[15vh] md:h-full md:w-[40%] lg:w-[45%] bg-[#5586DB]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#5586DB]/90 to-[#00AACC]/90"></div>
            <div class="absolute inset-0 animated-pattern pointer-events-none"></div>

            <div class="relative z-10 w-full flex flex-col items-center md:items-center">
                <div class="max-w-md px-10 text-left w-full">
                    <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-10 md:h-20 w-auto mb-2 md:mb-8 filter brightness-0 invert opacity-90 drop-shadow-md">
                    <h2 class="hidden md:block font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">Applicant <br><span class="text-[#F8FAFC]/90">Portal</span></h2>
                    <p class="hidden md:block text-[16px] text-white/80 leading-relaxed font-medium">Buat akun untuk melamar lowongan pekerjaan, mengelola profil, dan memantau status lamaran Anda.</p>
                    <h2 class="md:hidden font-jakarta font-extrabold text-[22px] tracking-tight text-white leading-tight">Applicant Portal</h2>
                </div>
            </div>

            <div class="hidden md:block absolute bottom-10 left-10 text-[12px] font-bold text-white/40 uppercase tracking-widest font-jakarta">
                Applicant Access Only
            </div>
        </div>

        <!-- Right Form -->
        <div class="absolute left-0 md:left-auto right-0 bottom-0 w-full md:w-[60%] lg:w-[55%] bg-white shadow-[-10px_0_30px_rgba(17,24,39,0.02)] z-30 flex flex-col h-[85vh] md:h-full">
            <div class="relative w-full h-full overflow-y-auto overflow-x-hidden p-6 md:p-16 lg:p-24 flex flex-col justify-start md:justify-center pt-20 md:pt-16">
                <!-- Back Button -->
                <a href="{{ route('careers.index') }}"
                        class="absolute top-6 left-6 md:top-10 md:left-10 text-[#4B5563] hover:text-[#111827] transition-colors flex items-center gap-1.5 font-bold text-sm z-40">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span class="hidden md:inline">Kembali</span>
                </a>

                <div class="max-w-[520px] w-full mx-auto py-6">


            <h1 class="font-jakarta font-extrabold text-[28px] md:text-[32px] text-[#111827] mb-2 tracking-tight">Pendaftaran Pelamar</h1>
            <p class="text-[15px] text-[#4B5563] mb-8 font-medium">Lengkapi form pendaftaran berikut.</p>

            <form action="{{ route('applicant.register.post') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="full_name" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Nama Lengkap <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border @error('full_name') border-red-300 @else border-[#E5E7EB] @enderror rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20 transition-all shadow-sm" required>
                        @error('full_name')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="whatsapp_number" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            No. WhatsApp <span class="text-red-600">*</span>
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3.5 text-[13px] text-[#4B5563] bg-[#E5E7EB] border border-[#E5E7EB] rounded-l-xl font-bold">+62</span>
                            <input type="text" id="whatsapp_number" name="whatsapp_number"
                                   value="{{ old('whatsapp_number') }}" placeholder="8xxxxxxxxxx"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-l-0 @error('whatsapp_number') border-red-300 @else border-[#E5E7EB] @enderror rounded-r-xl text-[13px] focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20 transition-all shadow-sm" required>
                        </div>
                        @error('whatsapp_number')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Email <span class="text-red-600">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border @error('email') border-red-300 @else border-[#E5E7EB] @enderror rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20 transition-all shadow-sm" required>
                        @error('email')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="gender" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Jenis Kelamin
                        </label>
                        <select id="gender" name="gender"
                                class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border @error('gender') border-red-300 @else border-[#E5E7EB] @enderror rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20 transition-all shadow-sm">
                            <option value="">Pilih...</option>
                            <option value="Laki-laki" {{ old('gender') == 'Laki-laki' || old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' || old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="birth_date" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Tanggal Lahir
                        </label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border @error('birth_date') border-red-300 @else border-[#E5E7EB] @enderror rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20 transition-all shadow-sm">
                        @error('birth_date')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="city" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Kota Domisili
                        </label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border @error('city') border-red-300 @else border-[#E5E7EB] @enderror rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20 transition-all shadow-sm">
                        @error('city')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                        Alamat Lengkap
                    </label>
                    <textarea id="address" name="address" rows="2"
                              class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border @error('address') border-red-300 @else border-[#E5E7EB] @enderror rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20 transition-all shadow-sm">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Password <span class="text-red-600">*</span>
                        </label>
                        <div class="relative w-full">
                            <input type="password" id="password" name="password"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border @error('password') border-red-300 @else border-[#E5E7EB] @enderror rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20 transition-all shadow-sm pr-10" required>
                            <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                        </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Konfirmasi Password <span class="text-red-600">*</span>
                        </label>
                        <div class="relative w-full">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border @error('password_confirmation') border-red-300 @else border-[#E5E7EB] @enderror rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20 transition-all shadow-sm pr-10" required>
                            <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                        </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center pt-2">
                    <input type="checkbox" id="agree_terms" name="agree_terms" class="h-4 w-4 text-[#5586DB] focus:ring-[#5586DB] border-[#E5E7EB] rounded" required>
                    <label for="agree_terms" class="ml-2 block text-[13px] text-[#4B5563] font-medium">
                        Saya setuju dengan <a href="#" class="text-[#5586DB] hover:text-[#00AACC] font-bold">syarat dan ketentuan</a>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full bg-[#5586DB] text-white font-bold py-3.5 px-4 rounded-xl hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2">
                        Daftar Sekarang <span class="material-symbols-outlined text-[18px]">person_add</span>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center border-t border-[#E5E7EB] pt-6 pb-4">
                <p class="text-[14px] text-[#4B5563]">
                    Sudah punya akun pelamar?
                    <a href="{{ route('applicant.login') }}" class="font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Masuk di sini</a>
                </p>
            </div>
            </div>
        </div>
    </div>
</body>
</html>