<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Employee Portal - MINDSIA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" rel="stylesheet"></noscript>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
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
            <h2 class="font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">Portal <br><span class="text-[#F8FAFC]/90">Karyawan</span></h2>
            <p class="text-[16px] text-white/80 leading-relaxed font-medium">Sistem Informasi Manajemen Terpadu untuk operasional, akademik, dan sumber daya manusia MINDSIA.</p>
        </div>
        
        <div class="absolute bottom-10 left-10 text-[12px] font-bold text-white/50 uppercase tracking-widest font-jakarta">
            Internal Access Only
        </div>
    </div>

    <!-- Right Form -->
    <div class="w-full md:w-[55%] lg:w-1/2 flex flex-col justify-center p-8 md:p-16 lg:p-24 overflow-y-auto bg-white relative shadow-[-10px_0_30px_rgba(17,24,39,0.02)]">
        <div class="max-w-[400px] w-full mx-auto">
            <div class="md:hidden mb-10">
                <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-14 w-auto">
            </div>

            <h1 class="font-jakarta font-extrabold text-[28px] md:text-[32px] text-[#111827] mb-2 tracking-tight">Reset Password</h1>
            <p class="text-[15px] text-[#4B5563] mb-10 font-medium">Buat kata sandi baru untuk akun Anda.</p>

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

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5" x-data="resetPasswordForm()" @submit="submit($event)">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ request()->query('email') ?? old('email') }}">

                <div>
                    <label for="password" class="block mb-1.5 text-[13px] font-bold text-[#4B5563]">Password Baru</label>
                    <div class="relative w-full">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            x-model="password"
                            @input="checkPassword"
                            class="w-full px-4 py-3 bg-[#F8FAFC] border rounded-xl text-[14px] focus:outline-none focus:ring-2 transition-all shadow-sm pr-10"
                            :class="passwordError ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-[#E5E7EB] focus:border-[#5586DB] focus:ring-[#5586DB]/20'"
                            placeholder="••••••••"
                            required
                            autofocus
                        >
                        <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                        </button>
                    </div>
                    <p x-show="passwordError" x-cloak class="mt-1.5 text-[11px] text-red-600 font-medium" x-text="passwordErrorMsg"></p>
                    <p x-show="!passwordError" class="mt-1.5 text-[11px] text-gray-500">Minimal 8 karakter.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block mb-1.5 text-[13px] font-bold text-[#4B5563]">Konfirmasi Password</label>
                    <div class="relative w-full">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            x-model="passwordConfirmation"
                            @input="checkPassword"
                            class="w-full px-4 py-3 bg-[#F8FAFC] border rounded-xl text-[14px] focus:outline-none focus:ring-2 transition-all shadow-sm pr-10"
                            :class="confirmError ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-[#E5E7EB] focus:border-[#5586DB] focus:ring-[#5586DB]/20'"
                            placeholder="••••••••"
                            required
                        >
                        <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                        </button>
                    </div>
                    <p x-show="confirmError" x-cloak class="mt-1.5 text-[11px] text-red-600 font-medium">Password tidak cocok.</p>
                </div>

                <button
                    type="submit"
                    :disabled="!isValid"
                    class="w-full text-white font-bold py-3.5 px-4 rounded-xl transition-all flex justify-center items-center gap-2 mt-2"
                    :class="isValid ? 'bg-[#5586DB] hover:bg-[#00AACC] shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)]' : 'bg-gray-300 cursor-not-allowed'"
                >
                    Simpan Password Baru <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                </button>
            </form>

            <div class="mt-12 text-center border-t border-[#E5E7EB] pt-8">
                <p class="text-[14px] text-[#4B5563]">
                    Batal mereset?
                    <a href="{{ route('login') }}" class="font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Masuk kembali</a>
                </p>
            </div>
            
        </div>
    </div>

    <script>
        function resetPasswordForm() {
            return {
                password: '',
                passwordConfirmation: '',
                passwordError: false,
                passwordErrorMsg: '',
                confirmError: false,
                isValid: false,

                checkPassword() {
                    if (this.password.length > 0 && this.password.length < 8) {
                        this.passwordError = true;
                        this.passwordErrorMsg = 'Password minimal 8 karakter.';
                    } else {
                        this.passwordError = false;
                        this.passwordErrorMsg = '';
                    }

                    if (this.passwordConfirmation.length > 0 && this.password !== this.passwordConfirmation) {
                        this.confirmError = true;
                    } else {
                        this.confirmError = false;
                    }

                    this.isValid = this.password.length > 0 && 
                                   this.passwordConfirmation.length > 0 && 
                                   !this.passwordError && 
                                   !this.confirmError;
                },

                submit(e) {
                    this.checkPassword();
                    if (!this.isValid) {
                        e.preventDefault();
                    }
                }
            }
        }
    </script>
</body>
</html>
