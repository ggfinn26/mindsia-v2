<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Portal - MINDSIA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" rel="stylesheet"></noscript>
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
@php
    $allowedViews = ['landing', 'login', 'register1', 'register2'];
    $initialView = 'landing';
    if (in_array(request()->query('view'), $allowedViews, true)) {
        $initialView = request()->query('view');
    }
    if (in_array(old('form_type'), $allowedViews, true)) {
        $initialView = old('form_type');
    }
@endphp
<body class="w-full h-screen overflow-hidden bg-[#F8FAFC] text-[#111827] font-inter antialiased"
      x-data="employeePortal('{{ $initialView }}')" @input="checkValidities()" @change="checkValidities()">

    <div class="relative w-full h-full overflow-hidden">

        <!-- ==================== LEFT COVER ==================== -->
        <div class="absolute left-0 top-0 w-full flex flex-col items-center justify-center text-center overflow-hidden transition-all duration-700 ease-in-out z-20 {{ $initialView === 'landing' ? 'h-full md:w-full' : 'h-[15vh] md:h-full md:w-[40%] lg:w-[45%]' }}"
             :class="{ 'h-full md:w-full': view === 'landing', 'h-[15vh] md:h-full md:w-[40%] lg:w-[45%]': view !== 'landing' }">

            <div class="absolute inset-0 bg-gradient-to-br from-[#5586DB] to-[#00AACC]"></div>
            <div class="absolute inset-0 animated-pattern pointer-events-none"></div>

            <!-- Landing Content -->
            <div class="relative z-10 w-full max-w-3xl px-6 flex flex-col items-center transition-all duration-500"
                 x-show="view === 'landing'"
                 x-transition:enter="transition ease-out duration-700 delay-300"
                 x-transition:enter-start="opacity-0 translate-y-12"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 {!! $initialView !== 'landing' ? 'style="display: none;"' : '' !!}>

                <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo"
                     class="h-16 md:h-28 w-auto mb-4 md:mb-6 filter brightness-0 invert drop-shadow-lg">
                <h2 class="font-jakarta font-extrabold text-[36px] md:text-[64px] tracking-tight text-white mb-4 md:mb-6 leading-[1.1] drop-shadow-md">
                    Portal <br><span class="text-white/80">Karyawan</span>
                </h2>
                <p class="text-[16px] md:text-[20px] text-white/85 leading-relaxed font-medium mb-6 md:mb-12 max-w-2xl mx-auto">
                    Sistem Informasi Manajemen Terpadu untuk operasional, akademik, dan sumber daya manusia MINDSIA.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 md:gap-5 w-full sm:w-auto">
                    <button @click="view = 'login'"
                            class="w-full sm:w-auto bg-white text-[#5586DB] font-bold text-lg py-3 md:py-3.5 px-12 rounded-xl hover:bg-[#F8FAFC] transition shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                        Login
                    </button>
                    <button @click="view = 'register1'"
                            class="w-full sm:w-auto bg-transparent border-2 border-white/80 text-white font-bold text-lg py-3 md:py-3.5 px-12 rounded-xl hover:bg-white/10 transition hover:border-white">
                        Daftar
                    </button>
                </div>
            </div>

            <!-- Split Content (shown when form is active) -->
            <div class="relative z-10 w-full flex flex-col items-center md:items-center transition-all duration-500"
                 x-show="view !== 'landing'"
                 x-transition:enter="transition ease-out duration-700 delay-500"
                 x-transition:enter-start="opacity-0 -translate-y-4 md:translate-y-0 md:-translate-x-12"
                 x-transition:enter-end="opacity-100 translate-y-0 md:translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 {!! $initialView === 'landing' ? 'style="display: none;"' : '' !!}>

                <div class="max-w-md px-10 text-left w-full">
                    <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo"
                         class="h-10 md:h-20 w-auto mb-2 md:mb-8 filter brightness-0 invert opacity-90 cursor-pointer hover:opacity-80 transition drop-shadow-md"
                         @click="view = 'landing'" title="Kembali ke awal">
                    <h2 class="hidden md:block font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">
                        Portal <br><span class="text-[#F8FAFC]/90">Karyawan</span>
                    </h2>
                    <p class="hidden md:block text-[16px] text-white/80 leading-relaxed font-medium">
                        Sistem Informasi Manajemen Terpadu untuk operasional, akademik, dan sumber daya manusia MINDSIA.
                    </p>
                    <h2 class="md:hidden font-jakarta font-extrabold text-[22px] tracking-tight text-white leading-tight">
                        Portal Karyawan
                    </h2>
                </div>
            </div>

            <div class="hidden md:block absolute bottom-10 left-10 text-[12px] font-bold text-white/40 uppercase tracking-widest font-jakarta"
                 x-show="view !== 'landing'" {!! $initialView === 'landing' ? 'style="display: none;"' : '' !!}>
                Internal Access Only
            </div>
        </div>

        <!-- ==================== RIGHT FORM ==================== -->
        <div class="absolute left-0 md:left-auto right-0 bottom-0 w-full md:w-[60%] lg:w-[55%] bg-white shadow-[-10px_0_30px_rgba(17,24,39,0.02)] transition-transform duration-700 ease-in-out z-30 flex flex-col h-[85vh] md:h-full translate-x-0 md:translate-y-0 {{ $initialView === 'landing' ? 'translate-y-full md:translate-x-full' : 'translate-y-0 md:translate-x-0' }}"
             :class="{ 'translate-y-full md:translate-x-full': view === 'landing', 'translate-y-0 md:translate-x-0': view !== 'landing' }">

            <div class="relative w-full h-full overflow-y-auto overflow-x-hidden p-4 md:p-16 lg:p-24 flex flex-col justify-start md:justify-center pt-20 md:pt-16">

                <!-- Back Button -->
                <button @click="view = 'landing'"
                        class="absolute top-6 left-6 md:top-10 md:left-10 text-[#4B5563] hover:text-[#111827] transition-colors flex items-center gap-1.5 font-bold text-sm z-40">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span class="hidden md:inline">Kembali</span>
                </button>

                <!-- ===================== LOGIN FORM ===================== -->
                <div class="max-w-[400px] w-full mx-auto"
                     x-show="view === 'login'"
                     x-transition:enter="transition ease-out duration-500 delay-300"
                     x-transition:enter-start="opacity-0 translate-y-8"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     {!! $initialView !== 'login' ? 'style="display: none;"' : '' !!}>


                    <h1 class="font-jakarta font-extrabold text-[24px] md:text-[28px] text-[#111827] mb-2 tracking-tight">Selamat datang di MINDSIA</h1>
                    <p class="text-[14px] text-[#4B5563] mb-6 font-medium">Silakan masuk untuk mengakses sistem perusahaan.</p>

                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-[13px] text-red-600 font-medium">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form @submit.prevent="submitLogin()" class="space-y-5">
                        <div>
                            <label for="email" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Email</label>
                            <input type="email" id="email" name="email"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                   :class="loginError ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-[#E5E7EB] focus:border-[#5586DB] focus:ring-[#5586DB]/20'"
                                   placeholder="karyawan@mindsia.com" required autofocus>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label for="password" class="block text-[12px] font-bold text-[#4B5563]">Password</label>
                                <a href="{{ route('password.request') }}" class="text-[12px] font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Lupa kata sandi?</a>
                            </div>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                       class="pr-12 w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm"
                                       required>
                                <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-[#4B5563] hover:text-[#5586DB] transition-colors focus:outline-none pwd-toggle">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </button>
                            </div>
                        </div>

                        <div
                            x-show="loginError"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            class="flex items-start gap-2 rounded-lg bg-red-50 border border-red-200 px-3 py-2.5"
                        >
                            <svg class="h-4 w-4 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-[12px] font-medium text-red-700" x-text="loginError"></p>
                        </div>

                        <div class="flex items-center pt-1 pb-4">
                            <input type="checkbox" id="remember" name="remember"
                                   class="h-4 w-4 text-[#5586DB] focus:ring-[#5586DB] border-[#E5E7EB] rounded">
                            <label for="remember" class="ml-2.5 block text-[12px] text-[#4B5563] font-medium">Ingat saya</label>
                        </div>

                        <button type="submit"
                                :disabled="loginLoading"
                                class="w-full bg-[#5586DB] text-white font-bold py-2.5 px-4 rounded-xl hover:bg-[#00AACC] disabled:opacity-60 transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2">
                            <span x-show="!loginLoading" class="flex justify-center items-center gap-2">Masuk <span class="material-symbols-outlined text-[18px]">login</span></span>
                            <span x-show="loginLoading">Memeriksa...</span>
                        </button>
                    </form>

                    <div class="mt-8 text-center border-t border-[#E5E7EB] pt-6 pb-4">
                        <p class="text-[13px] text-[#4B5563]">
                            Karyawan baru?
                            <a href="#" @click.prevent="view = 'register1'" class="font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Aktivasi akun</a>
                        </p>
                    </div>
                </div>

                <!-- ===================== REGISTER STEP 1 ===================== -->
                <div class="max-w-[400px] w-full mx-auto"
                     x-show="view === 'register1'"
                     x-transition:enter="transition ease-out duration-500 delay-300"
                     x-transition:enter-start="opacity-0 translate-y-8"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     {!! $initialView !== 'register1' ? 'style="display: none;"' : '' !!}>


                    <h1 class="font-jakarta font-extrabold text-[24px] md:text-[28px] text-[#111827] mb-2 tracking-tight">Verifikasi Data</h1>
                    <p class="text-[14px] text-[#4B5563] mb-6 font-medium">Masukkan kode karyawan untuk memulai aktivasi akun.</p>

                    <form @submit.prevent="verifyCode()" class="space-y-5">
                        <div>
                            <label for="employee_code" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Kode Karyawan</label>
                            <input type="text" id="employee_code" name="employee_code"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                   :class="verifyError ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 text-red-700' : inputClass(1, 'employee_code')"
                                   placeholder="Masukkan kode karyawan" required>
                            <div
                                x-show="verifyError"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                class="mt-2 flex items-start gap-2 rounded-lg bg-red-50 border border-red-200 px-3 py-2.5"
                            >
                                <svg class="h-4 w-4 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-[12px] font-medium text-red-700" x-text="verifyError"></p>
                            </div>
                        </div>

                        <button type="submit"
                                :disabled="verifyLoading"
                                class="w-full bg-[#5586DB] text-white font-bold py-2.5 px-4 rounded-xl hover:bg-[#00AACC] disabled:opacity-60 transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2">
                            <span x-show="!verifyLoading" class="flex justify-center items-center gap-2">Lanjut <span class="material-symbols-outlined text-[18px]">arrow_forward</span></span>
                            <span x-show="verifyLoading">Memeriksa...</span>
                        </button>
                    </form>

                    <div class="mt-8 text-center border-t border-[#E5E7EB] pt-6 pb-4">
                        <p class="text-[13px] text-[#4B5563]">
                            Sudah punya akun?
                            <a href="#" @click.prevent="view = 'login'" class="font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Masuk di sini</a>
                        </p>
                    </div>
                </div>

                <!-- ===================== REGISTER STEP 2 ===================== -->
                <div class="max-w-[400px] w-full mx-auto"
                     x-show="view === 'register2'"
                     x-transition:enter="transition ease-out duration-500 delay-300"
                     x-transition:enter-start="opacity-0 translate-y-8"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     {!! $initialView !== 'register2' ? 'style="display: none;"' : '' !!}>

                    <h1 class="font-jakarta font-extrabold text-[24px] md:text-[28px] text-[#111827] mb-2 tracking-tight">Buat Akun</h1>
                    <p class="text-[14px] text-[#4B5563] mb-6 font-medium">Lengkapi data akun Anda untuk masuk ke sistem.</p>

                    <form @submit.prevent="submitRegister()" class="space-y-5">
                        <div>
                            <label for="name" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Nama Lengkap</label>
                            <input type="text" id="name" name="name"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                   :class="(registerErrors.name || hasError(2, 'name')) ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : inputClass(2, 'name')" required>
                            <p x-show="hasError(2, 'name')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            <p x-show="registerErrors.name" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="registerErrors.name?.[0]"></p>
                        </div>

                        <div>
                            <label for="reg_email" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Email</label>
                            <input type="email" id="reg_email" name="email"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                   :class="(registerErrors.email || hasError(2, 'reg_email')) ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : inputClass(2, 'reg_email')" required>
                            <p x-show="hasError(2, 'reg_email')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            <p x-show="registerErrors.email" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="registerErrors.email?.[0]"></p>
                        </div>

                        <div>
                            <label for="reg_password" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Password</label>
                            <div class="relative">
                                <input type="password" id="reg_password" name="password"
                                       x-model="regPassword"
                                       class="pr-12 w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                       :class="(registerErrors.password || hasError(2, 'reg_password')) ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : inputClass(2, 'reg_password')" required>
                                <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-[#4B5563] hover:text-[#5586DB] transition-colors focus:outline-none pwd-toggle">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </button>
                            </div>
                            <p x-show="hasError(2, 'reg_password')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="passwordErrorText()"></p>
                            <p x-show="registerErrors.password && !hasError(2, 'reg_password')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="registerErrors.password?.[0]"></p>
                            <p class="mt-1.5 text-[11px] text-gray-500">Minimal 8 karakter, mengandung huruf besar, angka, dan simbol.</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       x-model="regPasswordConfirm"
                                       class="pr-12 w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                       :class="hasError(2, 'password_confirmation') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : inputClass(2, 'password_confirmation')" required>
                                <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-[#4B5563] hover:text-[#5586DB] transition-colors focus:outline-none pwd-toggle">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </button>
                            </div>
                            <p x-show="hasError(2, 'password_confirmation')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="passwordConfirmErrorText()"></p>
                        </div>

                        <div x-show="registerErrors.form" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="flex items-start gap-2 rounded-lg bg-red-50 border border-red-200 px-3 py-2.5">
                            <svg class="h-4 w-4 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-[12px] font-medium text-red-700" x-text="registerErrors.form?.[0]"></p>
                        </div>

                        <button type="submit"
                                :disabled="registerLoading"
                                class="w-full bg-[#5586DB] text-white font-bold py-2.5 px-4 rounded-xl hover:bg-[#00AACC] disabled:opacity-60 transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2">
                            <span x-show="!registerLoading" class="flex justify-center items-center gap-2">Simpan Akun <span class="material-symbols-outlined text-[18px]">person_add</span></span>
                            <span x-show="registerLoading">Menyimpan...</span>
                        </button>
                    </form>

                    <div class="mt-4">
                        <button type="button" @click="view = 'register1'"
                                class="w-full bg-white border border-[#E5E7EB] text-[#4B5563] font-bold py-2.5 px-4 rounded-xl hover:bg-[#F8FAFC] hover:text-[#111827] transition-all flex justify-center items-center shadow-sm">
                            Kembali ke Langkah 1
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        window.__employeePortalConfig = {
            routes: {
                registerVerify: '{{ route('register.verify') }}',
                register: '{{ route('register') }}',
                registerSuccess: '{{ route('register.success') }}',
                loginPost: '{{ route('login.post') }}',
                dashboard: '{{ route('dashboard') }}',
            }
        };
        // employeePortal() is defined in resources/js/employee-portal.js
    </script>
</body>
</html>
