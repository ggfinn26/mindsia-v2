@extends('layouts.landing')

@section('title', 'Tentang MINDSIA — Company Profile')
@section('description', 'Kenali lebih dalam visi, misi, filosofi, dan perjalanan MINDSIA English Academy sejak 2019.')

@section('content')

{{-- HERO --}}
<section class="relative bg-navy pt-28 md:pt-32 pb-16 md:pb-20 border-b border-white/5" id="hero">
    <div class="max-w-[1100px] mx-auto px-6">
        <div class="max-w-[800px] mb-20">
            
            <h1 class="font-sans font-black text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-white leading-[1.1] mb-6 tracking-tight">
                Interactive Learning.<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-gold to-yellow-200">Real Confidence.</span>
            </h1>
            <p class="text-xl text-white/70 leading-relaxed max-w-[650px] mb-10">
                Lembaga pengembangan kemampuan berbahasa Inggris sejak 2019 yang mengedepankan konsep belajar <i>fun</i>, interaktif, dan fleksibel untuk pengaplikasian di kehidupan nyata.
            </p>

        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 pt-12 border-t border-white/10">
            <div>
                <div class="text-xs font-semibold text-white/40 tracking-widest uppercase mb-2">Berdiri Sejak</div>
                <div class="text-4xl font-bold text-white">2019</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-white/40 tracking-widest uppercase mb-2">Target Cabang (2025)</div>
                <div class="text-4xl font-bold text-white">24</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-white/40 tracking-widest uppercase mb-2">Sistem Pembelajaran</div>
                <div class="text-4xl font-bold text-gold">24 HESA</div>
            </div>
            <div class="flex items-center md:justify-end">
                <a href="{{ url('/') }}#program" class="group inline-flex items-center gap-3 text-white font-semibold hover:text-gold transition-colors">
                    Lihat Program
                    <span class="w-10 h-10 shrink-0 rounded-full border border-white/20 flex items-center justify-center group-hover:border-gold transition-colors">
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- TENTANG --}}
<section class="py-16 md:py-24 px-6 bg-ky-surface" id="tentang">
    <div class="max-w-[1100px] mx-auto">
        <div class="grid md:grid-cols-12 gap-16 items-start">
            <div class="md:col-span-4 sticky top-24">
                <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Tentang Kami</h2>
                <h3 class="font-sans font-bold text-3xl md:text-4xl text-[#06122B] tracking-tight leading-tight">Membangun Ekosistem Praktik.</h3>
                <div class="mt-10 hidden md:block">
                    <div id="commit-grid-container" class="w-full h-[320px] rounded-2xl overflow-hidden bg-navy shadow-xl border border-gold/20 relative"></div>
                </div>
            </div>
            <div class="md:col-span-8">
                <div class="prose prose-lg text-ky-text/75 leading-relaxed max-w-none">
                    <p class="text-2xl font-medium text-[#06122B] leading-relaxed mb-10">
                        MINDSIA English Academy lahir dari kebutuhan nyata: meruntuhkan metode hafalan pasif dan membangun ekosistem praktik berbahasa yang hidup.
                    </p>
                    <div class="grid sm:grid-cols-2 gap-10 mb-10">
                        <div>
                            <h4 class="font-bold text-[#06122B] mb-2">Perkembangan Pesat</h4>
                            <p class="text-base">Berdiri sejak 2019, pada tahun 2025 kami menargetkan 24 cabang yang tersebar di Jawa Barat, Banten, dan Jawa Tengah.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#06122B] mb-2">Sistem 24 HESA</h4>
                            <p class="text-base">24 Hours English Service Area memadukan English House, English Cafe, dan English Point untuk membiasakan siswa berbahasa penuh waktu.</p>
                        </div>
                    </div>
                    <div class="p-8 bg-white border border-ky-border/50 rounded-2xl shadow-sm">
                        <h4 class="font-bold text-xs tracking-widest uppercase text-ky-primary mb-4">Pilar Belajar MINDSIA</h4>
                        <div class="flex flex-wrap gap-2.5">
                            <span class="px-3.5 py-1.5 bg-ky-surface border border-ky-border/50 text-[#06122B] text-sm font-medium rounded-lg">Fun Learning</span>
                            <span class="px-3.5 py-1.5 bg-ky-surface border border-ky-border/50 text-[#06122B] text-sm font-medium rounded-lg">Interactive</span>
                            <span class="px-3.5 py-1.5 bg-ky-surface border border-ky-border/50 text-[#06122B] text-sm font-medium rounded-lg">Flexible</span>
                            <span class="px-3.5 py-1.5 bg-ky-surface border border-ky-border/50 text-[#06122B] text-sm font-medium rounded-lg">Relevant</span>
                            <span class="px-3.5 py-1.5 bg-ky-surface border border-ky-border/50 text-[#06122B] text-sm font-medium rounded-lg">Confident</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VISI & MISI --}}
<section class="py-16 md:py-24 px-6 bg-white border-t border-ky-border/50" id="visi-misi">
    <div class="max-w-[1100px] mx-auto">
        <div class="mb-24 text-center max-w-[850px] mx-auto">
            <span class="material-symbols-outlined text-ky-primary/20 text-5xl mb-6">visibility</span>
            <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-4">Visi Perusahaan</h2>
            <p class="font-sans font-bold text-2xl md:text-3xl lg:text-4xl text-[#06122B] leading-snug">
                "Menghadirkan wadah pembelajaran Bahasa Inggris yang membentuk kebiasaan positif dan kepercayaan diri, untuk melahirkan generasi Indonesia yang cerdas, bermimpi besar, dan berprestasi di tingkat global."
            </p>
        </div>
        
        <div class="border-t border-ky-border/50 pt-20">
            <div class="grid md:grid-cols-12 gap-12">
                <div class="md:col-span-4">
                    <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Misi Perusahaan</h2>
                    <h3 class="font-sans font-bold text-3xl text-[#06122B] tracking-tight">Langkah Konkret Kami</h3>
                </div>
                <div class="md:col-span-8 grid sm:grid-cols-2 gap-8">
                    <div class="p-8 bg-ky-surface border border-ky-border/50 rounded-2xl">
                        <div class="text-5xl font-black text-ky-primary/15 mb-6">01</div>
                        <h4 class="font-bold text-xl text-[#06122B] mb-3">Ekspansi Jaringan</h4>
                        <p class="text-ky-text/75 leading-relaxed text-lg">Mewujudkan <strong>100 cabang</strong> MINDSIA di seluruh Indonesia pada tahun 2030.</p>
                    </div>
                    <div class="p-8 bg-ky-surface border border-ky-border/50 rounded-2xl">
                        <div class="text-5xl font-black text-ky-primary/15 mb-6">02</div>
                        <h4 class="font-bold text-xl text-[#06122B] mb-3">Penciptaan Dampak</h4>
                        <p class="text-ky-text/75 leading-relaxed text-lg">Mewujudkan <strong>100.000 member</strong> pada tahun 2030 yang mampu berbahasa Inggris dengan fasih.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FILOSOFI LOGO --}}
<section class="py-16 md:py-24 px-6 bg-ky-surface border-t border-ky-border/50" id="filosofi">
    <div class="max-w-[1100px] mx-auto">
        <div class="grid md:grid-cols-12 gap-16 items-center">
            <div class="md:col-span-5 flex justify-center p-12 bg-white rounded-[2rem] border border-ky-border/50 shadow-sm">
                <img src="/img/logo/mindsia-logo.png" alt="Logo MINDSIA" class="w-full max-w-[260px] opacity-90 mix-blend-multiply">
            </div>
            <div class="md:col-span-7">
                <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Identitas Visual</h2>
                <h3 class="font-sans font-bold text-3xl md:text-4xl text-[#06122B] tracking-tight mb-12">Filosofi Logo MINDSIA</h3>
                
                <div class="space-y-8">
                    <div class="flex gap-5">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-ky-primary/10 flex items-center justify-center text-ky-primary mt-1">
                            <span class="material-symbols-outlined text-[24px]">text_fields</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#06122B] text-lg mb-1">Huruf M</h4>
                            <p class="text-ky-text/75 leading-relaxed">Singkatan dari Mindsia, menjadi lambang identitas utama meskipun berdiri sendiri sebagai ikon.</p>
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-ky-primary/10 flex items-center justify-center text-ky-primary mt-1">
                            <span class="material-symbols-outlined text-[24px]">flutter_dash</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#06122B] text-lg mb-1">Kupu-Kupu</h4>
                            <p class="text-ky-text/75 leading-relaxed">Melambangkan metamorfosa: Motivating Intelligence, Nurturing Dreams, Shaping Indonesian Achievers.</p>
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-ky-primary/10 flex items-center justify-center text-ky-primary mt-1">
                            <span class="material-symbols-outlined text-[24px]">star</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#06122B] text-lg mb-1">Bintang 4 Sisi</h4>
                            <p class="text-ky-text/75 leading-relaxed">Kecemerlangan pikiran ke segala penjuru. Membentuk pemikiran yang mulia, tercerahkan, berani, dan bijaksana.</p>
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-ky-primary/10 flex items-center justify-center text-ky-primary mt-1">
                            <span class="material-symbols-outlined text-[24px]">chat</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#06122B] text-lg mb-1">Chat Box</h4>
                            <p class="text-ky-text/75 leading-relaxed">Simbol diskusi interaktif. Menggambarkan sistem pembelajaran yang komunikatif dan percaya diri.</p>
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-ky-primary/10 flex items-center justify-center text-ky-primary mt-1">
                            <span class="material-symbols-outlined text-[24px]">local_florist</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#06122B] text-lg mb-1">Tumbuhan Berbunga</h4>
                            <p class="text-ky-text/75 leading-relaxed">Melambangkan pertumbuhan, perkembangan, dan harapan bagi generasi muda untuk mencapai potensi puncaknya.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- KODE ETIK --}}
<section class="py-16 md:py-24 px-6 bg-white border-t border-ky-border/50" id="kode-etik">
    <div class="max-w-[1100px] mx-auto text-center">
        <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Nilai Inti</h2>
        <h3 class="font-sans font-bold text-3xl md:text-4xl text-[#06122B] tracking-tight mb-16">Kode Etik Perusahaan</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-12 max-w-[900px] mx-auto">
            @foreach(['Religiusitas', 'Profesional', 'Integritas & Transparansi', 'Kreatif', 'Inovatif', 'Mengadaptasi Perubahan', 'Kolaboratif', 'Inisiatif', 'Berbagi dan Peduli'] as $index => $etik)
            <div class="flex flex-col items-center">
                <span class="text-4xl font-black text-ky-primary/15 mb-3">0{{ $index + 1 }}</span>
                <h4 class="font-bold text-[#06122B] text-lg">{{ $etik }}</h4>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- STRUKTUR KEPEMIMPINAN --}}
<section class="py-16 md:py-24 px-6 bg-ky-surface border-t border-ky-border/50" id="struktur">
    <div class="max-w-[1100px] mx-auto">
        <div class="mb-16 text-center">
            <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Manajemen</h2>
            <h3 class="font-sans font-bold text-3xl md:text-4xl text-[#06122B] tracking-tight">Struktur Kepemimpinan</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @forelse($leaders as $leader)
            <div class="bg-white p-6 rounded-2xl border border-ky-border/50 text-center shadow-sm hover:border-ky-primary/30 transition-colors">
                <div class="w-14 h-14 mx-auto rounded-full bg-ky-primary/5 flex items-center justify-center mb-4 overflow-hidden border border-ky-primary/10">
                    @if($leader->photo_url)
                        <img src="{{ $leader->photo_url }}" alt="{{ $leader->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-[24px] text-ky-primary">person</span>
                    @endif
                </div>
                <h4 class="text-[#06122B] font-bold text-sm leading-snug mb-1">{{ $leader->name }}</h4>
                <div class="font-bold text-ky-primary/70 text-[11px] uppercase tracking-widest">{{ $leader->title }}</div>
            </div>
            @empty
            <div class="col-span-full text-center text-ky-text/50 py-8">Belum ada data struktur kepemimpinan.</div>
            @endforelse
        </div>
    </div>
</section>

{{-- PETA WILAYAH --}}
<section class="py-16 md:py-24 px-6 bg-navy" id="wilayah">
    <div class="max-w-[1100px] mx-auto">
        <div class="grid md:grid-cols-12 gap-12 items-end mb-16">
            <div class="md:col-span-8">
                <h2 class="font-sans font-black text-sm text-gold tracking-widest uppercase mb-3">Jaringan Kami</h2>
                <h3 class="font-sans font-bold text-3xl md:text-4xl text-white tracking-tight leading-tight">Tersebar di {{ $totalCabang }} Titik Cabang.</h3>
            </div>
            <div class="md:col-span-4 md:text-right text-white/60 text-lg">
                Hadir di Regional JABALNUS (Jawa, Bali, dan Nusa).
            </div>
        </div>
        
        <div class="bg-[#0A1628] rounded-3xl border border-white/10 p-2 md:p-8 mb-16 shadow-2xl">
            <svg id="indonesia-map" class="w-full" style="height: 480px;"></svg>
        </div>
        
        <div id="branch-list" class="grid grid-cols-1 md:grid-cols-3 gap-x-12 gap-y-8"></div>
    </div>
</section>

{{-- GALERI --}}
<section class="py-16 md:py-24 px-6 bg-white" id="galeri">
    <div class="max-w-[1100px] mx-auto">
        <div class="mb-16 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Dokumentasi</h2>
                <h3 class="font-sans font-bold text-3xl md:text-4xl text-[#06122B] tracking-tight">Galeri MINDSIA</h3>
            </div>
        </div>
        
        @if($photos->isNotEmpty())
        <div class="flex flex-col max-w-[1100px] mx-auto">
            @foreach($photos as $index => $photo)
            <div class="scroll-reveal opacity-0 translate-y-16 transition-all duration-1000 ease-out flex flex-col md:flex-row {{ $index % 2 == 1 ? 'md:flex-row-reverse' : '' }} gap-10 md:gap-16 items-center mb-32 last:mb-0">
                
                <div class="w-full md:w-7/12 rounded-3xl overflow-hidden bg-ky-surface/80 border border-ky-border/40 p-4 shadow-sm group aspect-[4/3]">
                    <img src="{{ $photo->photo_url }}" alt="{{ $photo->title }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-1000">
                </div>
                
                <div class="w-full md:w-5/12 px-2 text-center md:text-left">
                    <h4 class="font-sans font-black text-3xl md:text-4xl text-[#06122B] tracking-tight mb-4">{{ $photo->title }}</h4>
                    <p class="text-xl text-ky-text/75 leading-relaxed">{{ $photo->caption }}</p>
                </div>

            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 border border-dashed border-ky-border/80 text-ky-text/50 text-center rounded-2xl">
            Belum ada foto galeri.
        </div>
        @endif
        
        @if($photos->isEmpty())
        <div class="p-12 border border-dashed border-ky-border/80 text-ky-text/50 text-center rounded-2xl">
            Belum ada foto galeri.
        </div>
        @endif
    </div>
</section>

{{-- OUR HEADQUARTER --}}
<section class="py-16 md:py-24 px-6 bg-white border-t border-ky-border/50" id="headquarter">
    <div class="max-w-[1100px] mx-auto">
        <div class="grid md:grid-cols-12 gap-12 items-center">
            <div class="md:col-span-5">
                <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Location</h2>
                <h3 class="font-sans font-bold text-3xl md:text-4xl text-[#06122B] tracking-tight mb-6">Our Headquarter</h3>
                <p class="text-ky-text/75 text-lg leading-relaxed mb-10">
                    Kunjungi kantor pusat kami untuk berdiskusi langsung mengenai program unggulan, peluang kemitraan, maupun informasi karir secara komprehensif.
                </p>
                <div class="flex items-start gap-5 p-6 bg-ky-surface border border-ky-border/50 rounded-2xl">
                    <div class="w-12 h-12 shrink-0 rounded-full bg-ky-primary/10 flex items-center justify-center text-ky-primary mt-0.5">
                        <span class="material-symbols-outlined text-[24px]">location_on</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#06122B] text-lg mb-2">MINDSIA Bandung</h4>
                        <p class="text-ky-text/75 leading-relaxed text-sm">
                            Eastern Hills Regency Blk. G No.6, Cipadung,<br>
                            Kec. Cibiru, Kota Bandung,<br>
                            Jawa Barat 40614
                        </p>
                    </div>
                </div>
            </div>
            <div class="md:col-span-7">
                <div class="w-full h-[450px] rounded-[2rem] overflow-hidden border border-ky-border/50 shadow-sm bg-gray-100">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d729.7438572271869!2d107.71931406687877!3d-6.925367717518738!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68dd35656d3575%3A0x6ceefa4593eacbca!2sMINDSIA%20BANDUNG%20I.%20CIPADUNG!5e0!3m2!1sid!2sid!4v1789320988600!5m2!1sid!2sid" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- LEVEL UP PROJECT & SUBDOMAIN --}}
<section class="py-32 px-6 bg-ky-surface border-t border-ky-border/50 text-center" id="levelup">
    <div class="max-w-[800px] mx-auto">
        <div class="w-16 h-16 mx-auto bg-ky-primary/10 text-ky-primary rounded-2xl flex items-center justify-center mb-8">
            <span class="material-symbols-outlined text-[32px]">rocket_launch</span>
        </div>
        <h2 class="font-sans font-bold text-4xl md:text-5xl text-[#06122B] mb-6 tracking-tight">MINDSIA Level Up Project</h2>
        <p class="text-ky-text/75 text-lg leading-relaxed mb-12">
            Peningkatan mutu fasilitas secara berkelanjutan. Ekspansi cabang baru mengusung rancangan fungsional, arsitektur matang, serta ekosistem wajib bahasa Inggris yang konsisten.
        </p>
        <div class="grid sm:grid-cols-2 gap-4 max-w-[500px] mx-auto">
            <a href="{{ url('/') }}#program" class="inline-flex items-center justify-center bg-[#06122B] text-white font-bold py-4 px-8 rounded-xl hover:bg-black transition-colors shadow-sm">
                Lihat Program Kami
            </a>
            <a href="{{ config('app.url_karir') }}" class="inline-flex items-center justify-center bg-white border border-ky-border/80 text-[#06122B] font-bold py-4 px-8 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                Karir MINDSIA
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
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


<script type="importmap">
{
  "imports": {
    "react": "https://esm.sh/react@18.2.0",
    "react-dom/client": "https://esm.sh/react-dom@18.2.0/client",
    "react/jsx-runtime": "https://esm.sh/react@18.2.0/jsx-runtime",
    "framer": "data:text/javascript,export const addPropertyControls = () => {}; export const ControlType = {};"
  }
}
</script>
<script type="module">
    import React from 'react';
    import { createRoot } from 'react-dom/client';
    import CommitGrid from 'https://framerusercontent.com/modules/3njtMRTKlChgENjtAgDZ/R4vTLQzV9Ev0EaTosl3o/CommitGrid_1.js';

    const container = document.getElementById('commit-grid-container');
    if (container) {
        const root = createRoot(container);
        // Render it with default props, it uses 100% width/height naturally in Framer
        root.render(React.createElement(CommitGrid, {
            baseColor: "#FBBF24",
            maxOpacity: 0.9,
            pattern: "twinkle",
            cellSize: 8,
            gap: 4,
            speed: 2.5
        }));
    }
</script>

{{-- D3.js peta Indonesia --}}
<script src="https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js"
        integrity="sha384-CjloA8y00+1SDAUkjs099PVfnY2KmDC2BZnws9kh8D/lX1s46w6EPhpXdqMfjK6i"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/topojson-client@3/dist/topojson-client.min.js"
        integrity="sha384-Ukv1p/xTma6P4/2bY5KzWBw+ydSpXmhCMtyciIQVDJ1RmOxtCYNMF1uXT9T63H67"
        crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', async function () {
    try {
        const res = await fetch('/api/branches-map');
        const branches = await res.json();

        const branchMap = {};
        branches.forEach(b => { branchMap[b.province] = b; });
        const MINDSIA_PROVINCES = Object.keys(branchMap);

        const svg = d3.select('#indonesia-map');
        const width = svg.node().getBoundingClientRect().width || 1100;
        const height = 480;
        svg.attr('viewBox', `0 0 ${width} ${height}`);

        const g = svg.append('g');

        const topology = await d3.json('https://raw.githubusercontent.com/denyherianto/indonesia-geojson-topojson-maps-with-38-provinces/main/TopoJSON/indonesia-38-provinces.topo.json');
        const features = topojson.feature(topology, topology.objects['indonesia-38-prov-topo']).features;

        const projection = d3.geoMercator().fitSize([width, height], { type: 'FeatureCollection', features });
        const path = d3.geoPath().projection(projection);

        const tooltip = d3.select('body').append('div')
            .style('position', 'fixed')
            .style('background', '#fff')
            .style('color', '#06122B')
            .style('padding', '10px 14px')
            .style('font-size', '13px')
            .style('border', '1px solid #E2E8F0')
            .style('box-shadow', '0 10px 25px -5px rgba(0,0,0,0.1)')
            .style('pointer-events', 'none')
            .style('opacity', 0)
            .style('z-index', 1000)
            .style('transition', 'opacity 0.1s');

        g.selectAll('path')
            .data(features)
            .join('path')
            .attr('d', path)
            .attr('fill', d => {
                const prov = d.properties.PROVINSI || '';
                return MINDSIA_PROVINCES.some(p => prov.toLowerCase().includes(p.toLowerCase())) ? '#FBBF24' : 'rgba(255,255,255,0.05)';
            })
            .attr('stroke', '#06122B')
            .attr('stroke-width', 0.8)
            .on('mouseover', function (event, d) {
                const prov = d.properties.PROVINSI || '';
                const data = branches.find(b => prov.toLowerCase().includes(b.province.toLowerCase()));
                if (data) {
                    tooltip.style('opacity', 1)
                           .html(`<strong class="text-[14px] font-sans font-bold block mb-1">${data.province}</strong><span class="text-[#06122B]">${data.total} cabang</span>`);
                    d3.select(this)
                      .attr('fill', '#F59E0B')
                      .attr('stroke', '#fff')
                      .attr('stroke-width', 1);
                }
            })
            .on('mousemove', event => {
                tooltip.style('left', (event.clientX + 16) + 'px').style('top', (event.clientY - 30) + 'px');
            })
            .on('mouseout', function (event, d) {
                const prov = d.properties.PROVINSI || '';
                const isActive = MINDSIA_PROVINCES.some(p => prov.toLowerCase().includes(p.toLowerCase()));
                d3.select(this)
                  .attr('fill', isActive ? '#FBBF24' : 'rgba(255,255,255,0.05)')
                  .attr('stroke', '#06122B')
                  .attr('stroke-width', 0.8);
                tooltip.style('opacity', 0);
            });

        const list = document.getElementById('branch-list');
        branches.forEach(b => {
            const citiesHtml = b.cities.map(c => `<span class="text-white/60">${c}</span>`).join(' <span class="text-white/20 mx-1.5">•</span> ');
            list.innerHTML += `
                <div class="py-4 border-t border-white/10">
                    <div class="flex justify-between items-baseline mb-3">
                        <div class="font-sans font-bold text-white text-lg tracking-wide">${b.province}</div>
                        <div class="text-gold text-xs font-bold tracking-widest uppercase">${b.total} Cabang</div>
                    </div>
                    <div class="text-sm leading-relaxed text-white/50">
                        ${citiesHtml}
                    </div>
                </div>`;
        });
    } catch(err) {
        console.error("Map rendering error: ", err);
    }
});
</script>
@endpush
