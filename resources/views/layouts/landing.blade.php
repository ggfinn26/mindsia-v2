<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>MINDSIA - Lembaga Kursus Bahasa Inggris</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="description" content="Lembaga kursus bahasa Inggris dengan asrama dan metode intensif.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" rel="stylesheet"></noscript>
</head>

<body class="font-sans antialiased overflow-x-hidden bg-ky-surface text-ky-text">

{{-- FAB WhatsApp --}}
<a href="https://wa.me/6289518494953" target="_blank" class="fixed bottom-7 right-7 z-50 w-14 h-14 rounded-full bg-[#25D366] text-white flex items-center justify-center shadow-lg no-underline transition-transform duration-300 hover:scale-110 group" title="Hubungi via WhatsApp" aria-label="Hubungi via WhatsApp">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
</a>

{{-- Nav --}}
<nav id="lp-nav" x-data="{ mobileMenuOpen: false }" class="fixed top-0 w-full z-40 bg-white/90 backdrop-blur-md border-b border-ky-border/50 transition-all duration-300 shadow-sm" aria-label="Navigasi utama">
    <div class="max-w-[1100px] mx-auto px-6 flex items-center justify-between h-[68px]">
        <a href="{{ url('/') }}" class="hover:opacity-80 transition-opacity" title="MINDSIA">
            <!-- Normal logo (not inverted) -->
            <img src="/img/logo/mindsia-logo.webp" alt="MINDSIA" class="h-12 w-auto object-contain drop-shadow-sm">
        </a>
        <div class="hidden md:flex gap-4 items-center">
            <a href="#konsep" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary transition-colors">Konsep</a>
            <a href="#program" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary transition-colors">Program</a>
            <a href="#cabang" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary transition-colors">Cabang</a>
            <a href="#testimoni" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary transition-colors">Testimoni</a>
            <a href="/karir" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary transition-colors">Karir</a>
        </div>
        @if(!request()->routeIs('company.landing'))
        <div class="hidden md:flex items-center gap-3">
            @if(request()->is('karir*') || request()->is('applicant*'))
                <a href="{{ route('applicant.login') }}" class="text-[14px] font-semibold bg-gray-100 text-gray-700 py-2 px-5 rounded-lg no-underline transition-all duration-200 hover:bg-gray-200 shadow-sm border border-gray-200">Login Pelamar</a>
                <a href="{{ route('applicant.register') }}" class="text-[14px] font-semibold bg-ky-primary text-white py-2 px-5 rounded-lg no-underline transition-all duration-200 hover:bg-ky-primary-hover shadow-sm">Daftar Pelamar</a>
            @else
                <a href="{{ route('member.login', [], false) }}" class="text-[14px] font-semibold bg-gray-100 text-gray-700 py-2 px-5 rounded-lg no-underline transition-all duration-200 hover:bg-gray-200 shadow-sm border border-gray-200">Login</a>
                <a href="/member/register" class="text-[14px] font-semibold bg-ky-primary text-white py-2 px-5 rounded-lg no-underline transition-all duration-200 hover:bg-ky-primary-hover shadow-sm">Daftar</a>
            @endif
        </div>
        @endif
        <button id="nav-ham" @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden flex flex-col gap-1.5 bg-transparent border-none p-1.5" aria-label="Menu">
            <span class="block w-6 h-0.5 bg-ky-text rounded-full"></span>
            <span class="block w-6 h-0.5 bg-ky-text rounded-full"></span>
            <span class="block w-6 h-0.5 bg-ky-text rounded-full"></span>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-white border-t border-gray-100 shadow-lg absolute w-full left-0 top-[68px]">
        <div class="flex flex-col px-6 py-4 gap-4">
            <a href="#konsep" @click="mobileMenuOpen = false" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary">Konsep</a>
            <a href="#program" @click="mobileMenuOpen = false" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary">Program</a>
            <a href="#cabang" @click="mobileMenuOpen = false" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary">Cabang</a>
            <a href="#testimoni" @click="mobileMenuOpen = false" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary">Testimoni</a>
            <a href="/karir" @click="mobileMenuOpen = false" class="text-[14px] font-medium text-ky-text/80 hover:text-ky-primary">Karir</a>
            @if(!request()->routeIs('company.landing'))
            <div class="h-[1px] bg-gray-100 w-full my-1"></div>
            @if(request()->is('karir*') || request()->is('applicant*'))
                <a href="{{ route('applicant.login') }}" class="text-[14px] font-semibold bg-gray-100 text-gray-700 py-2 px-5 rounded-lg text-center shadow-sm border border-gray-200">Login Pelamar</a>
                <a href="{{ route('applicant.register') }}" class="text-[14px] font-semibold bg-ky-primary text-white py-2 px-4 rounded-lg text-center shadow-sm">Daftar Pelamar</a>
            @else
                <a href="{{ route('member.login', [], false) }}" class="text-[14px] font-semibold bg-gray-100 text-gray-700 py-2 px-5 rounded-lg text-center shadow-sm border border-gray-200">Login Member</a>
                <a href="/member/register" class="text-[14px] font-semibold bg-ky-primary text-white py-2 px-4 rounded-lg text-center shadow-sm">Daftar</a>
            @endif
            @endif
        </div>
    </div>
</nav>

@yield('content')

{{-- Footer --}}
<footer class="bg-ky-card border-t border-ky-border mt-20" aria-label="Footer">
    <div class="max-w-[1100px] mx-auto px-6 pt-14 pb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 mb-12">
            <div class="md:col-span-1">
                <img src="/img/logo/mindsia-logo.webp" alt="MINDSIA" class="h-8 w-auto object-contain mb-4">
                <p class="text-[14px] text-ky-text/70 leading-relaxed">Lembaga kursus bahasa Inggris terpadu dengan fasilitas asrama wajib bahasa Inggris.</p>
            </div>
            <div>
                <div class="text-[12px] font-semibold text-ky-text mb-4">Kantor Pusat</div>
                <p class="text-[14px] text-ky-text/70 leading-relaxed">Eastern Hills Regency Blok G No.6,<br>Cipadung, Cibiru,<br>Kota Bandung, Jawa Barat</p>
            </div>
            <div>
                <div class="text-[12px] font-semibold text-ky-text mb-4">Navigasi</div>
                <ul class="list-none flex flex-col gap-3 m-0 p-0">
                    <li><a href="#konsep" class="text-[14px] text-ky-text/70 hover:text-ky-primary transition-colors">Konsep HESA</a></li>
                    <li><a href="#program" class="text-[14px] text-ky-text/70 hover:text-ky-primary transition-colors">Program</a></li>
                    <li><a href="#cabang" class="text-[14px] text-ky-text/70 hover:text-ky-primary transition-colors">Cabang</a></li>
                    <li><a href="/karir" class="text-[14px] text-ky-text/70 hover:text-ky-primary transition-colors">Karir</a></li>
                </ul>
            </div>
            <div>
                <div class="text-[12px] font-semibold text-ky-text mb-4">Hubungi Kami</div>
                <div class="flex flex-col gap-3">
                    <a href="https://wa.me/6289518494953" class="text-[14px] text-ky-text/70 hover:text-ky-primary transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">chat</span> WhatsApp
                    </a>
                    <a href="https://instagram.com/mindsiaofficial" class="text-[14px] text-ky-text/70 hover:text-ky-primary transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">photo_camera</span> Instagram
                    </a>
                    <a href="mailto:mindsiaofficial@gmail.com" class="text-[14px] text-ky-text/70 hover:text-ky-primary transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">mail</span> Email
                    </a>
                </div>
            </div>
        </div>
        <div class="border-t border-ky-border pt-6 text-center">
            <p class="text-[13px] text-ky-text/50">© {{ date('Y') }} MINDSIA English Course</p>
        </div>
    </div>
</footer>


    @stack("scripts")
</body>
</html>
