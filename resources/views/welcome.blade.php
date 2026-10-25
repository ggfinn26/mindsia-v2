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


{{-- ═══ SISTEM 24 HESA (Symmetrical Corporate Grid) ═══ --}}
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
                <p class="text-[15px] text-ky-text/70 leading-relaxed">Lingkungan belajar yang nyaman seperti di rumah, untuk membangun kebiasaan berbahasa Inggris melalui interaksi sehari-hari.</p>
            </div>
            
            <!-- Card 2 -->
            <div class="bg-white p-10 shadow-sm border border-ky-border/50 hover:shadow-lg transition-all duration-300 rounded-2xl group flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-full bg-ky-primary/5 flex items-center justify-center text-[24px] font-black text-ky-primary mb-6 group-hover:scale-110 group-hover:bg-gold transition-all">02</div>
                <h3 class="font-sans text-[20px] font-bold mb-3 text-[#06122B]">English Cafe</h3>
                <p class="text-[15px] text-ky-text/70 leading-relaxed">Lingkungan belajar yang santai ala kafe, untuk melatih keberanian berbicara bahasa Inggris melalui obrolan dan interaksi sosial.</p>
            </div>
            
            <!-- Card 3 -->
            <div class="bg-[#06122B] p-10 shadow-lg transition-all duration-300 rounded-2xl group flex flex-col items-center text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-gold/10 to-transparent"></div>
                <div class="w-16 h-16 rounded-full bg-gold/10 flex items-center justify-center text-[24px] font-black text-gold mb-6 group-hover:scale-110 group-hover:bg-gold group-hover:text-[#06122B] transition-all relative z-10">03</div>
                <h3 class="font-sans text-[20px] font-bold mb-3 text-white relative z-10">English Point</h3>
                <p class="text-[15px] text-white/70 leading-relaxed relative z-10">Lingkungan belajar yang menjadi titik praktik bahasa Inggris untuk mengasah kemampuan berbicara melalui aktivitas yang singkat, fokus, dan interaktif.</p>
            </div>
        </div>
        
    </div>
</section>

{{-- ═══ BENEFITS (header sticky kiri, daftar 8 poin kanan; sengaja bukan grid kartu agar beda ritme dari HESA) ═══ --}}
@php
    $benefits = [
        ['Nationally Recognized Certificates', 'Sertifikat Berstandar Nasional dan Berlisensi', 'Anda akan memperoleh sertifikat sebagai bukti pencapaian dan hasil pembelajaran sesuai ketentuan program yang diikuti.'],
        ['Guaranteed Classes', 'Kelas Bergaransi tanpa biaya tambahan', 'Anda akan mendapatkan kelas pengulangan jika target kelas belum tercapai.'],
        ['Free Cross Branch Discussions', 'Pindah Kelas dan Diskusi Lintas Cabang tanpa biaya tambahan', 'Anda dapat berpindah kelas ke cabang MINDSIA lainnya tanpa biaya tambahan serta dapat mengikuti diskusi dan berlatih bahasa Inggris saat berkunjung ke cabang lain.'],
        ['24 Hours English Service Area (24 HESA)', 'Area layanan bahasa Inggris 24 jam', 'Anda bersama Civitas Mindsia diwajibkan untuk menggunakan bahasa Inggris selama 24 jam untuk membentuk kebiasaan berbahasa Inggris.'],
        ['Flexible Learning Schedule', 'Jadwal belajar yang fleksibel', 'Anda dapat menyesuaikan jadwal belajar dengan aktivitas dan kebutuhan masing-masing sesuai pilihan jadwal yang tersedia.'],
        ['Fun and Engaging Learning Methods', 'Metode belajar yang menyenangkan dan interaktif', 'Proses pembelajaran dirancang agar lebih menarik, interaktif, dan mendorong Anda untuk aktif menggunakan bahasa Inggris.'],
        ['Learning Assessment Report', 'Laporan hasil penilaian pembelajaran', 'Anda akan memperoleh laporan hasil penilaian untuk mengetahui perkembangan kemampuan, pencapaian, dan aspek yang masih perlu ditingkatkan.'],
        ['Young and Friendly Tutors', 'Tutor Muda dan Bersahabat', 'Tutor muda yang ramah, komunikatif, dan dekat dengan Anda sehingga suasana belajar lebih nyaman dan menyenangkan.'],
    ];
@endphp
<section class="py-24 px-6 bg-[#06122B]" id="benefits" aria-labelledby="benefits-title">
    <div class="max-w-[1200px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-28">
                <div class="w-12 h-1 bg-gold mb-6"></div>
                <h2 id="benefits-title" class="font-sans font-black text-[36px] md:text-[42px] tracking-tight text-white mb-4 leading-tight">Mengapa Harus MINDSIA?</h2>
                <p class="text-[16px] text-white/75 leading-relaxed">8 alasan mengapa ribuan alumni memilih belajar di MINDSIA</p>
            </div>
        </div>

        <ol class="lg:col-span-8 border-t border-white/15">
            @foreach($benefits as $i => [$english, $headline, $description])
            <li class="grid grid-cols-[3rem_1fr] sm:grid-cols-[4.5rem_1fr] gap-x-4 sm:gap-x-6 py-8 border-b border-white/15">
                <span class="font-sans font-black text-[28px] sm:text-[36px] leading-none text-gold" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <div>
                    <p class="text-[13px] font-semibold text-gold/90 mb-1">{{ $english }}</p>
                    <h3 class="font-sans font-bold text-[20px] sm:text-[22px] text-white leading-snug mb-2">{{ $headline }}</h3>
                    <p class="text-[15px] text-white/75 leading-relaxed">{{ $description }}</p>
                </div>
            </li>
            @endforeach
        </ol>
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
                <div class="h-64 w-full overflow-hidden relative bg-ky-surface">
                    <img src="{{ $lp->image_url }}" alt="Program {{ $lp->program_name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-[#06122B]/10 group-hover:bg-transparent transition-colors"></div>
                    @if($lp->is_featured)
                    <div class="absolute top-4 right-4 bg-gold text-[#06122B] text-[10px] font-black uppercase tracking-wider py-1.5 px-3 rounded-full shadow-md">
                        Rekomendasi
                    </div>
                    @endif
                </div>
                
                <div class="p-8 flex-grow flex flex-col">
                    <div class="text-[11px] font-black text-ky-primary uppercase tracking-widest mb-2">{{ $lp->level_label ?? 'UMUM' }}</div>
                    <h3 class="font-sans font-black text-[22px] text-[#06122B] mb-3 leading-tight">{{ $lp->program_name }}</h3>
                    @if ($lp->program_description)
                        <div x-data="{ open: false }" class="mb-6 flex-grow">
                            <div class="rich-content text-[14px] text-ky-text/70 overflow-hidden transition-all" :class="open || 'max-h-16'">{!! clean($lp->program_description) !!}</div>
                            <button type="button" @click="open = !open" class="mt-1.5 inline-flex items-center gap-1 text-[13px] font-bold text-ky-primary">
                                <span class="material-symbols-outlined text-[16px]" x-text="open ? 'expand_less' : 'expand_more'"></span>
                                <span x-text="open ? 'Ringkas' : 'Selengkapnya'"></span>
                            </button>
                        </div>
                    @else
                        <p class="text-[14px] text-ky-text/70 mb-6 flex-grow">Program intensif dengan metode pengajaran berstandar internasional.</p>
                    @endif
                    
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
@include('landing.partials.gallery-carousel', [
    'items'     => $galleries,
    'sectionId' => 'gallery',
    'bg'        => 'bg-ky-surface',
    'eyebrow'   => 'Galeri',
    'title'     => 'Mindsivers',
    'subtitle'  => 'Momen dan Dokumentasi kegiatan MINDSIA.',
])

{{-- ═══ TESTIMONI ═══ --}}
<section class="py-24 px-6 bg-white overflow-hidden" id="testimoni" aria-label="Testimoni">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-14">
            <p class="font-sans font-bold text-sm text-ky-primary tracking-widest uppercase mb-3">Testimoni</p>
            <h2 class="font-sans font-black text-[36px] md:text-[42px] tracking-tight text-[#06122B] mb-3">Kata Mereka</h2>
            <p class="text-[16px] text-ky-text/70 max-w-xl mx-auto">Pengalaman belajar dari alumni dan siswa aktif kami.</p>
        </div>

        @if($testimonials->isEmpty())
        <div class="p-12 text-center border-2 border-dashed border-ky-border/60 rounded-2xl max-w-md mx-auto">
            <span class="material-symbols-outlined text-[48px] text-ky-text/40 block mb-2">format_quote</span>
            <p class="text-sm font-semibold text-ky-text/60">Belum ada testimoni.</p>
        </div>
        @else
        <div x-data="galleryCoverflow({{ $testimonials->count() }})">

            <div class="relative h-[540px] md:h-[700px]" style="perspective: 1600px;">
                @foreach($testimonials as $index => $testi)
                <div class="absolute left-1/2 top-0 w-[340px] h-[480px] md:w-[470px] md:h-[640px] transition-all duration-500 ease-out cursor-pointer"
                     style="transform-style: preserve-3d;"
                     :style="slideStyle({{ $index }})"
                     @click="go({{ $index }})">

                    @if($testi->type === 'image' || (is_object($testi->type) && $testi->type->value === 'image'))
                        <div class="w-full h-full rounded-3xl overflow-hidden shadow-[0_25px_50px_-12px_rgba(6,18,43,0.35)] bg-white">
                            <img src="{{ $testi->image_url }}" alt="Testimoni {{ $testi->name }}" class="w-full h-full object-cover" loading="lazy">
                        </div>
                    @else
                        <div class="w-full h-full rounded-3xl bg-white border border-ky-border/40 shadow-[0_25px_50px_-12px_rgba(6,18,43,0.35)] p-8 md:p-9 flex flex-col text-left relative">
                            <span class="material-symbols-outlined absolute top-6 right-6 text-[56px] text-ky-primary/5 select-none pointer-events-none">format_quote</span>
                            <p class="text-[15px] md:text-[17px] text-[#06122B] leading-relaxed italic relative z-10 flex-1 overflow-hidden">
                                "{{ $testi->quote }}"
                            </p>
                            <div class="flex items-center gap-3 pt-5 mt-4 border-t border-ky-border/40">
                                <div class="w-12 h-12 rounded-full bg-ky-primary/10 flex items-center justify-center text-ky-primary font-bold text-[18px] shrink-0">
                                    {{ substr($testi->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-[#06122B] text-[15px]">{{ $testi->name }}</div>
                                    @if($testi->program || $testi->city)
                                    <div class="text-[13px] text-ky-text/60 font-semibold">
                                        {{ $testi->program }}{{ $testi->program && $testi->city ? ' - ' : '' }}{{ $testi->city }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            @if($testimonials->count() > 1)
            <div class="flex justify-center items-center gap-4 mt-10">
                <button type="button" @click="prev()" aria-label="Sebelumnya"
                        class="w-12 h-12 rounded-full border border-ky-border flex items-center justify-center text-[#06122B] hover:bg-[#06122B] hover:text-white hover:border-[#06122B] transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <button type="button" @click="next()" aria-label="Berikutnya"
                        class="w-12 h-12 rounded-full border border-ky-border flex items-center justify-center text-[#06122B] hover:bg-[#06122B] hover:text-white hover:border-[#06122B] transition-colors">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
            @endif
        </div>
        @endif
    </div>
</section>

{{-- === CTA CORPORATE === --}}
<section class="py-32 px-6 relative text-center overflow-hidden" id="daftar">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop" alt="Kampus Mindsia" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-[#06122B]/85 backdrop-blur-[2px]"></div>
    </div>

    <div class="relative z-10 max-w-[700px] mx-auto">
        <h2 class="font-sans font-black text-[36px] md:text-[48px] tracking-tight text-white mb-6">Mulai Perjalanan Akademik Anda</h2>
        <p class="text-[16px] md:text-[18px] text-white/70 leading-relaxed mb-10">
            Pendaftaran siswa baru telah dibuka. Tingkatkan daya saing global Anda bersama fasilitas pendidikan bahasa Inggris terbaik.
        </p>
        <a href="/member/register" class="inline-flex items-center gap-2 bg-gold text-[#06122B] font-bold text-[16px] py-4 px-10 rounded-xl transition-all hover:-translate-y-1 shadow-lg hover:shadow-xl">
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
