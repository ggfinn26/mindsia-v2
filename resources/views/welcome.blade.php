@extends('layouts.landing')

@section('content')

    <style>
        html { scroll-behavior: smooth; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        
        /* Sembunyikan navbar di awal khusus halaman ini */
        #lp-nav {
            transform: translateY(-100%);
            opacity: 0;
            transition: transform 0.4s ease-in-out, opacity 0.4s ease-in-out;
        }
        #lp-nav.scrolled-down {
            transform: translateY(0);
            opacity: 1;
        }
    </style>

    <!-- ==================== FULL SCREEN BLUE ==================== -->
    <section id="hero" class="relative w-full min-h-screen flex flex-col items-center justify-center text-center overflow-hidden z-20 font-inter">

        <!-- Background Gradient and Pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#5586DB] to-[#00AACC]"></div>
        <div class="absolute inset-0 animated-pattern pointer-events-none"></div>

        <!-- Main Content -->
        <div class="relative z-10 w-full max-w-6xl px-6 flex flex-col items-center mt-12 md:mt-0">

            <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo"
                 class="h-16 md:h-28 w-auto mb-4 md:mb-6 filter brightness-0 invert drop-shadow-lg">
            
            <h1 class="font-jakarta font-extrabold text-[36px] md:text-[64px] tracking-tight text-white mb-4 md:mb-6 leading-[1.1] drop-shadow-md text-center">
                Selamat Datang di <br><span class="text-white/90">MINDSIA</span>
            </h1>
            
            <p class="text-[16px] md:text-[20px] text-white/85 leading-relaxed font-medium mb-8 md:mb-12 max-w-2xl mx-auto text-center">
                Pusat pembelajaran bahasa Inggris terpadu dan pengembangan sumber daya manusia profesional.
            </p>
            
            <!-- Call To Actions -->
            <div class="flex flex-col md:flex-row justify-center items-center gap-4 w-full">
                
                <!-- 1. Jelajahi Program -->
                <a href="#konsep"
                   class="w-full md:w-auto bg-white text-[#5586DB] font-bold text-[16px] py-3 px-8 rounded-xl hover:bg-[#F8FAFC] transition shadow-xl hover:shadow-2xl transform hover:-translate-y-1 flex justify-center items-center text-center">
                    Jelajahi Program
                </a>
                
                <!-- 2. Portal Member -->
                <a href="{{ route('member.landing') }}"
                   class="w-full md:w-auto bg-transparent border-2 border-white/80 text-white font-bold text-[16px] py-3 px-8 rounded-xl hover:bg-white/10 transition hover:border-white flex justify-center items-center text-center">
                    Portal Member
                </a>

                <!-- 3. Lihat Lowongan -->
                <a href="{{ route('careers.index') }}"
                   class="w-full md:w-auto bg-transparent border-2 border-white/80 text-white font-bold text-[16px] py-3 px-8 rounded-xl hover:bg-white/10 transition hover:border-white flex justify-center items-center text-center">
                    Lihat Lowongan
                </a>

                <!-- 4. Company Profile -->
                <a href="{{ route('company.landing') }}"
                   class="w-full md:w-auto bg-transparent border-2 border-white/80 text-white font-bold text-[16px] py-3 px-8 rounded-xl hover:bg-white/10 transition hover:border-white flex justify-center items-center text-center">
                    Company Profile
                </a>

            </div>
        </div>

        <!-- Footer Note & Scroll Indicator -->
        <a href="#konsep" class="absolute bottom-10 flex flex-col items-center text-white/50 hover:text-white transition-colors cursor-pointer group">
            <span class="text-[12px] font-bold uppercase tracking-widest font-jakarta mb-2 group-hover:scale-105 transition-transform">Scroll Down</span>
            <span class="material-symbols-outlined animate-bounce">keyboard_arrow_down</span>
        </a>
    </section>


{{-- ═══ KONSEP HESA (Symmetrical Corporate Grid) ═══ --}}
<section class="py-24 px-6 bg-ky-surface" id="konsep" aria-label="Sistem 24 HESA">
    <div class="max-w-[1200px] mx-auto">
        
        <!-- Section Header -->
        <div class="text-center max-w-[700px] mx-auto mb-16">
            <div class="w-12 h-1 bg-gold mb-6 mx-auto"></div>
            <h2 class="font-sans font-black text-[36px] md:text-[42px] tracking-tight text-[#06122B] mb-6">Sistem 24 HESA</h2>
            <p class="text-[16px] text-ky-text/70 leading-relaxed">
                Mindsia mengedepankan 24 Hours English Service Area. Seluruh civitas diwajibkan berbahasa Inggris secara intens selama 24 jam agar penguasaan bahasa melekat dalam kebiasaan sehari-hari.
            </p>
        </div>
        
        <!-- Symmetrical 3-Column Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-white p-10 shadow-sm border border-ky-border/50 hover:shadow-lg transition-all duration-300 rounded-2xl group flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-full bg-ky-primary/5 flex items-center justify-center text-[24px] font-black text-ky-primary mb-6 group-hover:scale-110 group-hover:bg-gold transition-all">01</div>
                <h3 class="font-sans text-[20px] font-bold mb-3 text-[#06122B]">English House</h3>
                <p class="text-[15px] text-ky-text/70 leading-relaxed">Meningkatkan kepercayaan diri dalam mempraktikkan percakapan bahasa Inggris secara nyata di lingkungan asrama.</p>
            </div>
            
            <!-- Card 2 -->
            <div class="bg-white p-10 shadow-sm border border-ky-border/50 hover:shadow-lg transition-all duration-300 rounded-2xl group flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-full bg-ky-primary/5 flex items-center justify-center text-[24px] font-black text-ky-primary mb-6 group-hover:scale-110 group-hover:bg-gold transition-all">02</div>
                <h3 class="font-sans text-[20px] font-bold mb-3 text-[#06122B]">English Cafe</h3>
                <p class="text-[15px] text-ky-text/70 leading-relaxed">Meningkatkan kemampuan memahami bahasa Inggris dalam berbagai konteks obrolan sehari-hari dengan santai.</p>
            </div>
            
            <!-- Card 3 -->
            <div class="bg-[#06122B] p-10 shadow-lg transition-all duration-300 rounded-2xl group flex flex-col items-center text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-gold/10 to-transparent"></div>
                <div class="w-16 h-16 rounded-full bg-gold/10 flex items-center justify-center text-[24px] font-black text-gold mb-6 group-hover:scale-110 group-hover:bg-gold group-hover:text-[#06122B] transition-all relative z-10">03</div>
                <h3 class="font-sans text-[20px] font-bold mb-3 text-white relative z-10">English Point</h3>
                <p class="text-[15px] text-white/70 leading-relaxed relative z-10">Mengembangkan kemampuan berfikir dalam bahasa Inggris melalui interaksi yang intens dan sumber daya mandiri.</p>
            </div>
        </div>
        
    </div>
</section>

{{-- ═══ PROGRAM (Modern Asymmetric) ═══ --}}
<section class="py-24 px-6 bg-white" id="program" aria-label="Program Pilihan">
    <div class="max-w-[1200px] mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <h2 class="font-sans font-black text-[36px] md:text-[42px] tracking-tight text-[#06122B] mb-4">Program Pilihan</h2>
                <p class="text-[16px] text-ky-text/70 max-w-[500px]">Pilih jalur pendidikan yang paling sesuai dengan target kompetensi Anda.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($landingPrograms as $index => $lp)
            <div class="group bg-white rounded-2xl shadow-[0_10px_30px_-15px_rgba(0,0,0,0.1)] hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.15)] flex flex-col relative overflow-hidden transition-all duration-500 border border-ky-border/50">
                <!-- Image Header -->
                <div class="h-48 w-full overflow-hidden relative bg-ky-surface">
                    <img src="{{ $lp->image_url }}" alt="Program {{ $lp->program_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-[#06122B]/10 group-hover:bg-transparent transition-colors"></div>
                    @if($lp->is_featured)
                    <div class="absolute top-4 right-4 bg-gold text-[#06122B] text-[10px] font-black uppercase tracking-wider py-1.5 px-3 rounded-full shadow-md">
                        Rekomendasi
                    </div>
                    @endif
                </div>
                
                <div class="p-8 flex-grow flex flex-col">
                    <div class="text-[11px] font-black text-ky-primary uppercase tracking-widest mb-2">{{ $lp->level_label ?? 'UMUM' }}</div>
                    <h3 class="font-sans font-black text-[22px] text-[#06122B] mb-3 leading-tight">{{ $lp->code }}</h3>
                    <p class="text-[14px] text-ky-text/70 mb-6 flex-grow">{{ $lp->tagline ?? 'Program intensif dengan metode pengajaran berstandar internasional.' }}</p>
                    
                    <!-- Promo Box -->
                    <div class="mb-8 bg-ky-surface p-4 rounded-xl border border-ky-border/50">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="material-symbols-outlined text-[16px] text-gold">workspace_premium</span>
                            <div class="text-[11px] font-black text-[#06122B] uppercase tracking-wider">Limited Time Offer</div>
                        </div>
                        <div class="text-[12px] text-ky-text/70 leading-snug">Take your English skills to the next level with our special program offer.</div>
                    </div>
                    
                    <a href="/register?program={{ $lp->id }}" class="flex items-center justify-center gap-2 w-full bg-[#06122B] text-white py-3.5 rounded-xl text-[13px] font-bold group-hover:bg-gold group-hover:text-[#06122B] transition-all shadow-md group-hover:shadow-lg">
                        Pilih Program
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
{{-- ═══ MINDSIVERS GALLERY ═══ --}}
<section class="py-24 px-6 bg-ky-surface" id="gallery" aria-label="Galeri Mindsivers">
    <div class="max-w-[1200px] mx-auto">
        <div class="mb-16 border-l-[4px] border-gold pl-6">
            <h2 class="font-sans font-black text-[36px] md:text-[42px] tracking-tight text-[#06122B] mb-2">Mindsivers</h2>
            <p class="text-[16px] text-ky-text/70">Momen dan dokumentasi kegiatan Mindsivers.</p>
        </div>
        
        @if($galleries->isNotEmpty())
        <div class="flex flex-col max-w-[1100px] mx-auto">
            @foreach($galleries as $index => $gallery)
            <div class="scroll-reveal opacity-0 translate-y-16 transition-all duration-1000 ease-out flex flex-col md:flex-row {{ $index % 2 == 1 ? 'md:flex-row-reverse' : '' }} gap-10 md:gap-16 items-center mb-32 last:mb-0">
                
                <div class="w-full md:w-7/12 rounded-3xl overflow-hidden shadow-lg group">
                    <img src="{{ $gallery->image }}" alt="{{ $gallery->title }}" class="w-full h-auto object-contain group-hover:scale-105 transition-transform duration-1000">
                </div>
                
                <div class="w-full md:w-5/12 px-2 text-center md:text-left">
                    <h3 class="font-sans font-black text-3xl md:text-4xl text-[#06122B] tracking-tight mb-4">{{ $gallery->title }}</h3>
                    <p class="text-xl text-ky-text/75 leading-relaxed">{{ $gallery->caption ?? '' }}</p>
                </div>

            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ═══ TESTIMONI ═══ --}}
<section class="py-24 px-6 bg-white overflow-hidden" id="testimoni" aria-label="Testimoni">
    <div class="max-w-[800px] mx-auto text-center">
        <h2 class="font-sans font-black text-[36px] md:text-[42px] tracking-tight text-[#06122B] mb-4">Kata Mereka</h2>
        <p class="text-[16px] text-ky-text/70 mb-16">Pengalaman belajar dari alumni dan siswa aktif kami.</p>
        
        <!-- Testimonial Carousel -->
        <div class="relative w-full h-[650px] md:h-[800px]" id="testimonial-carousel">
            @foreach($testimonials as $index => $testi)
                <div class="testi-slide absolute inset-0 w-full h-full flex justify-center items-center opacity-0 pointer-events-none transition-all duration-[2s] ease-[cubic-bezier(0.25,1,0.5,1)]" 
                     data-index="{{ $index }}"
                     style="transform: translateY(80px);">
                    
                    @if($testi->type === 'image')
                        <!-- Image/Flyer Testimonial -->
                        <img src="{{ $testi->image_url }}" alt="Testimoni" class="max-w-[90vw] max-h-[600px] md:max-h-[750px] w-auto h-auto object-contain rounded-2xl shadow-[0_15px_40px_-15px_rgba(0,0,0,0.2)] bg-white">
                    @else
                        <!-- Text Testimonial -->
                        <div class="max-w-[700px] w-full bg-white p-10 rounded-2xl shadow-[0_15px_40px_-15px_rgba(0,0,0,0.15)] relative text-left mx-auto">
                            <span class="material-symbols-outlined absolute top-8 right-8 text-[60px] text-ky-primary/5">format_quote</span>
                            <p class="text-[16px] md:text-[20px] text-[#06122B] leading-relaxed italic mb-10 relative z-10">
                                "{{ $testi->quote }}"
                            </p>
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-full bg-ky-primary/10 flex items-center justify-center text-ky-primary font-bold text-[20px]">
                                    {{ substr($testi->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-[#06122B] text-[16px]">{{ $testi->name }}</div>
                                    <div class="text-[13px] text-ky-text/60 font-semibold">{{ $testi->program }} • {{ $testi->city }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    /* Custom Animation Classes for Carousel */
    .testi-slide.active {
        opacity: 1 !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
        z-index: 10;
    }
    
    .testi-slide.exit-right {
        opacity: 0 !important;
        transform: translateX(150px) !important;
        z-index: 5;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.remove('opacity-0', 'translate-y-16');
                entry.target.classList.add('opacity-100', 'translate-y-0');
            } else {
                entry.target.classList.add('opacity-0', 'translate-y-16');
                entry.target.classList.remove('opacity-100', 'translate-y-0');
            }
        });
    }, { threshold: 0.15, rootMargin: "0px 0px -50px 0px" });
    
    document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
});
</script>

{{-- ═══ CTA CORPORATE ═══ --}}
<section class="py-32 px-6 relative text-center overflow-hidden" id="daftar">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <!-- Nanti gambar asli dipasang di src img ini -->
        <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop" alt="Kampus Mindsia" class="w-full h-full object-cover">
        <!-- Overlay Gelap agar teks tetap terbaca jelas -->
        <div class="absolute inset-0 bg-[#06122B]/85 backdrop-blur-[2px]"></div>
    </div>

    <div class="relative z-10 max-w-[700px] mx-auto">
        <h2 class="font-sans font-black text-[36px] md:text-[48px] tracking-tight text-white mb-6">Mulai Perjalanan Akademik Anda</h2>
        <p class="text-[16px] md:text-[18px] text-white/70 leading-relaxed mb-10">
            Pendaftaran siswa baru telah dibuka. Tingkatkan daya saing global Anda bersama fasilitas pendidikan bahasa Inggris terbaik.
        </p>
        <a href="/member/register" class="inline-flex items-center gap-2 bg-gold text-[#06122B] font-bold text-[16px] py-4 px-10 rounded-none transition-transform hover:-translate-y-1">
            Mendaftar Sekarang
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Navbar visibility logic
    const nav = document.getElementById('lp-nav');
    if (nav) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > window.innerHeight * 0.3) {
                nav.classList.add('scrolled-down');
            } else {
                nav.classList.remove('scrolled-down');
            }
        });
    }

    // Smooth scroll for all anchor links (e.g. Jelajahi Program, Scroll Down)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Testimonial Carousel
    const slides = document.querySelectorAll('.testi-slide');
    const dots = document.querySelectorAll('.testi-dot');
    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('opacity-100', 'pointer-events-auto');
            slide.classList.add('opacity-0', 'pointer-events-none');
            slide.style.transform = 'translateY(80px)';
            
            if (i === index) {
                slide.classList.add('opacity-100', 'pointer-events-auto');
                slide.classList.remove('opacity-0', 'pointer-events-none');
                slide.style.transform = 'translateY(0)';
            }
        });
        
        dots.forEach((dot, i) => {
            dot.classList.toggle('w-8', i === index);
            dot.classList.toggle('bg-[#06122B]', i === index);
            dot.classList.toggle('w-2', i !== index);
            dot.classList.toggle('bg-ky-text/30', i !== index);
        });
    }

    if (slides.length > 0) {
        showSlide(0);
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 5000);
        
        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                currentSlide = i;
                showSlide(currentSlide);
            });
        });
    }

    // Existing Intersection Observer for Scroll Reveal
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.remove('opacity-0', 'translate-y-16');
                entry.target.classList.add('opacity-100', 'translate-y-0');
            } else {
                entry.target.classList.add('opacity-0', 'translate-y-16');
                entry.target.classList.remove('opacity-100', 'translate-y-0');
            }
        });
    }, { threshold: 0.15, rootMargin: "0px 0px -50px 0px" });
    
    document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
});
</script>
@endpush
