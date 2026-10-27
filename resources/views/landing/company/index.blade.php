@extends('layouts.landing')

@section('title', 'Tentang MINDSIA - Company Profile')
@section('description', 'Kenali lebih dalam visi, misi, filosofi, dan perjalanan MINDSIA English Course sejak 2019.')

@section('content')

@php
    // Halaman program ada di landing apex (home), bukan subdomain company.
    $programUrl = apex_url('#program');
@endphp

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
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 pt-12 border-t border-white/10">
            <div>
                <div class="text-xs font-semibold text-white/40 tracking-widest uppercase mb-2">Berdiri Sejak</div>
                <div class="text-4xl font-bold text-white">2019</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-white/40 tracking-widest uppercase mb-2">Cabang pada Tahun 2026</div>
                <div class="text-4xl font-bold text-white" data-count-to="35">35</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-white/40 tracking-widest uppercase mb-2">Alumni</div>
                <div class="text-4xl font-bold text-white" data-count-to="10000" data-count-suffix="+">10.000+</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-white/40 tracking-widest uppercase mb-2">Sistem Pembelajaran</div>
                <div class="text-4xl font-bold text-gold">24 HESA</div>
            </div>
            <div class="flex items-center md:justify-end">
                <a href="{{ $programUrl }}" class="group inline-flex items-center gap-3 text-white font-semibold hover:text-gold transition-colors">
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
                        MINDSIA English Course lahir dari kebutuhan nyata: meruntuhkan metode hafalan pasif dan membangun ekosistem praktik berbahasa yang hidup.
                    </p>
                    <div class="grid sm:grid-cols-2 gap-10 mb-10">
                        <div>
                            <h4 class="font-bold text-[#06122B] mb-2">Perkembangan Pesat</h4>
                            <p class="text-base">Berdiri sejak 2019, pada tahun 2026 kami memiliki 35 cabang yang tersebar di Banten, Jawa Barat, dan Jawa Tengah.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#06122B] mb-2">Sistem 24 HESA</h4>
                            <p class="text-base">24 Hours English Service Area memadukan English House, English Cafe, dan English Point untuk membangun kebiasaan berbahasa Inggris sepanjang waktu.</p>
                        </div>
                    </div>
                    <div class="p-8 bg-white rounded-2xl shadow-[0_10px_30px_-8px_rgba(6,18,43,0.18)]">
                        <h4 class="font-bold text-xs tracking-widest uppercase text-ky-primary mb-4">Pilar Belajar MINDSIA</h4>
                        <div class="flex flex-wrap gap-2.5">
                            <span class="px-3.5 py-1.5 bg-slate-50 text-[#06122B] text-sm font-medium rounded-lg">Fun Learning</span>
                            <span class="px-3.5 py-1.5 bg-slate-50 text-[#06122B] text-sm font-medium rounded-lg">Interactive</span>
                            <span class="px-3.5 py-1.5 bg-slate-50 text-[#06122B] text-sm font-medium rounded-lg">Flexible</span>
                            <span class="px-3.5 py-1.5 bg-slate-50 text-[#06122B] text-sm font-medium rounded-lg">Relevant</span>
                            <span class="px-3.5 py-1.5 bg-slate-50 text-[#06122B] text-sm font-medium rounded-lg">Confident</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VISI & MISI --}}
<section class="py-16 md:py-24 px-6 bg-white border-t border-slate-200" id="visi-misi">
    <div class="max-w-[1100px] mx-auto">
        <div class="mb-24 text-center max-w-[850px] mx-auto">
            <span class="material-symbols-outlined text-ky-primary/20 text-5xl mb-6">visibility</span>
            <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-4">Visi Perusahaan</h2>
            <p class="font-sans font-bold text-2xl md:text-3xl lg:text-4xl text-[#06122B] leading-snug">
                "Menghadirkan wadah pembelajaran Bahasa Inggris yang membentuk kebiasaan positif dan kepercayaan diri, untuk melahirkan generasi Indonesia yang cerdas, bermimpi besar, dan berprestasi di tingkat global."
            </p>
        </div>
        
        <div class="border-t border-slate-200 pt-20">
            <div class="grid md:grid-cols-12 gap-12">
                <div class="md:col-span-4">
                    <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Misi Perusahaan</h2>
                    <h3 class="font-sans font-bold text-3xl text-[#06122B] tracking-tight">Langkah Konkret Kami</h3>
                </div>
                <div class="md:col-span-8 grid sm:grid-cols-2 gap-8">
                    <div class="p-8 bg-white rounded-2xl shadow-[0_10px_30px_-8px_rgba(6,18,43,0.18)]">
                        <div class="text-5xl font-black text-ky-primary/15 mb-6">01</div>
                        <h4 class="font-bold text-xl text-[#06122B] mb-3">Ekspansi Jaringan</h4>
                        <p class="text-ky-text/75 leading-relaxed text-lg">Mewujudkan <strong>100 cabang</strong> MINDSIA di seluruh Indonesia pada tahun 2030.</p>
                    </div>
                    <div class="p-8 bg-white rounded-2xl shadow-[0_10px_30px_-8px_rgba(6,18,43,0.18)]">
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
<section class="py-16 md:py-24 px-6 bg-ky-surface border-t border-slate-200" id="filosofi">
    <div class="max-w-[1100px] mx-auto">
        <div class="grid md:grid-cols-12 gap-16 items-center">
            <div class="md:col-span-5 flex justify-center p-12 bg-white rounded-[2rem] shadow-[0_10px_30px_-8px_rgba(6,18,43,0.18)]">
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
<section class="py-16 md:py-24 px-6 bg-white border-t border-slate-200" id="kode-etik">
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
<section class="py-16 md:py-24 px-6 bg-ky-surface border-t border-slate-200" id="struktur">
    <div class="max-w-[1100px] mx-auto">
        <div class="mb-16 text-center">
            <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Manajemen</h2>
            <h3 class="font-sans font-bold text-3xl md:text-4xl text-[#06122B] tracking-tight">Struktur Kepemimpinan</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @forelse($leaders as $leader)
            <div class="bg-white p-6 rounded-2xl text-center shadow-[0_10px_30px_-8px_rgba(6,18,43,0.18)] hover:shadow-lg transition-shadow flex flex-col h-full">
                <div class="w-full mb-4 flex-grow flex items-center justify-center rounded-xl bg-slate-50 overflow-hidden">
                    @if($leader->photo_url)
                        <img src="{{ $leader->photo_url }}" alt="{{ $leader->name }}" class="w-full h-auto object-contain block">
                    @else
                        <div class="w-full aspect-[3/4] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[48px] text-ky-primary/50">person</span>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="text-[#06122B] font-bold text-sm leading-snug mb-1">{{ $leader->name }}</h4>
                    <div class="font-bold text-ky-primary/70 text-[11px] uppercase tracking-widest">{{ $leader->title }}</div>
                </div>
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
            <div id="indonesia-map" class="w-full rounded-2xl overflow-hidden" style="height: 480px; z-index: 0;"></div>
        </div>
        
        <div id="branch-list" class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start"></div>
    </div>

    {{-- Modal embed Google Maps per cabang --}}
    <div id="branch-modal" class="fixed inset-0 z-[1100] hidden items-center justify-center p-4">
        <div id="branch-modal-backdrop" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl">
            <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-200 rounded-t-2xl">
                <div>
                    <div class="text-[11px] font-bold text-ky-primary uppercase tracking-widest">Cabang MINDSIA</div>
                    <h4 id="branch-modal-title" class="font-sans font-bold text-xl text-[#06122B]"></h4>
                </div>
                <button type="button" id="branch-modal-close" aria-label="Tutup" class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center text-[#06122B] hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="w-full h-[460px] bg-gray-100">
                <iframe id="branch-modal-iframe" class="w-full h-full border-0" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 rounded-b-2xl flex justify-end">
                <a id="branch-modal-link" href="#" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-[#06122B] text-white font-bold py-2.5 px-5 rounded-lg hover:bg-black transition-colors text-sm">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    Buka di Google Maps
                </a>
            </div>
        </div>
    </div>
</section>

{{-- GALERI --}}
@include('landing.partials.gallery-carousel', [
    'items'     => $photos,
    'sectionId' => 'galeri',
    'bg'        => 'bg-white',
    'eyebrow'   => 'Dokumentasi',
    'title'     => 'Galeri MINDSIA',
    'subtitle'  => 'Jejak kegiatan dan pencapaian MINDSIA.',
])

{{-- OUR HEADQUARTER --}}
<section class="py-16 md:py-24 px-6 bg-white border-t border-slate-200" id="headquarter">
    <div class="max-w-[1100px] mx-auto">
        <div class="grid md:grid-cols-12 gap-12 items-center">
            <div class="md:col-span-5">
                <h2 class="font-sans font-black text-sm text-ky-primary tracking-widest uppercase mb-3">Location</h2>
                <h3 class="font-sans font-bold text-3xl md:text-4xl text-[#06122B] tracking-tight mb-6">Our Headquarter</h3>
                <p class="text-ky-text/75 text-lg leading-relaxed mb-10">
                    Kunjungi kantor pusat kami untuk berdiskusi langsung mengenai program unggulan, peluang kemitraan, maupun informasi karir secara komprehensif.
                </p>
                <div class="flex items-start gap-5 p-6 bg-white rounded-2xl shadow-[0_10px_30px_-8px_rgba(6,18,43,0.18)]">
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
                <div class="w-full h-[450px] rounded-[2rem] overflow-hidden shadow-[0_10px_30px_-8px_rgba(6,18,43,0.18)] bg-gray-100">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d729.7438572271869!2d107.71931406687877!3d-6.925367717518738!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68dd35656d3575%3A0x6ceefa4593eacbca!2sMINDSIA%20BANDUNG%20I.%20CIPADUNG!5e0!3m2!1sid!2sid!4v1789320988600!5m2!1sid!2sid" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- LEVEL UP PROJECT & SUBDOMAIN --}}
<section class="py-32 px-6 bg-ky-surface border-t border-slate-200 text-center" id="levelup">
    <div class="max-w-[800px] mx-auto">
        <div class="w-16 h-16 mx-auto bg-ky-primary/10 text-ky-primary rounded-2xl flex items-center justify-center mb-8">
            <span class="material-symbols-outlined text-[32px]">rocket_launch</span>
        </div>
        <h2 class="font-sans font-bold text-4xl md:text-5xl text-[#06122B] mb-6 tracking-tight">MINDSIA Level Up Project</h2>
        <p class="text-ky-text/75 text-lg leading-relaxed mb-12">
            Peningkatan mutu fasilitas secara berkelanjutan. Ekspansi cabang baru mengusung rancangan fungsional, arsitektur matang, serta ekosistem wajib bahasa Inggris yang konsisten.
        </p>
        <div class="grid sm:grid-cols-2 gap-4 max-w-[500px] mx-auto">
            <a href="{{ $programUrl }}" class="inline-flex items-center justify-center bg-[#06122B] text-white font-bold py-4 px-8 rounded-xl hover:bg-black transition-colors shadow-sm">
                Lihat Program Kami
            </a>
            <a href="{{ route('careers.index') }}" class="inline-flex items-center justify-center bg-white border border-slate-300 text-[#06122B] font-bold py-4 px-8 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
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

{{-- Leaflet peta cabang (tile OpenStreetMap, tanpa API key) --}}
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', async function () {
    try {
        // Modal embed Google Maps per cabang.
        const modal = document.getElementById('branch-modal');
        // Lepas modal ke <body>: main punya overflow-x-hidden yang meng-clip overlay fixed.
        document.body.appendChild(modal);
        const modalIframe = document.getElementById('branch-modal-iframe');
        const modalTitle = document.getElementById('branch-modal-title');
        const modalLink = document.getElementById('branch-modal-link');

        function openBranch(b) {
            modalTitle.textContent = b.name;
            modalIframe.src = `https://www.google.com/maps?q=${b.lat},${b.lng}&z=16&hl=id&output=embed`;
            modalLink.href = b.gmaps || `https://maps.google.com/?q=${b.lat},${b.lng}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeBranch() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalIframe.src = '';
        }
        document.getElementById('branch-modal-close').addEventListener('click', closeBranch);
        document.getElementById('branch-modal-backdrop').addEventListener('click', closeBranch);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeBranch(); });

        // Daftar cabang per provinsi.
        const branches = await (await fetch('/api/branches-map')).json();
        const list = document.getElementById('branch-list');
        branches.forEach(b => {
            const chips = b.cities.map(c => `<span class="text-white/80 text-sm bg-white/5 border border-white/10 rounded-lg px-3 py-1.5">${c}</span>`).join('');
            list.innerHTML += `
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <h4 class="font-sans font-bold text-white text-xl tracking-wide">${b.province}</h4>
                        <span class="shrink-0 text-gold text-xs font-bold tracking-widest uppercase bg-gold/10 border border-gold/30 rounded-full px-3 py-1">${b.total} Cabang</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        ${chips}
                    </div>
                </div>`;
        });

        // Peta Leaflet + marker tiap cabang.
        const points = (await (await fetch('/api/branches-points')).json())
            .filter(p => p.lat != null && p.lng != null);

        // View awal wajib sebelum tambah layer vektor (geojson/marker), kalau tidak _clipPoints crash. fitBounds menyusul.
        const map = L.map('indonesia-map', { scrollWheelZoom: false }).setView([-7.2, 108.0], 7);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        // Highlight provinsi yang punya cabang (Banten, Jawa Barat, Jawa Tengah).
        const HILITE = ['banten', 'jawa barat', 'jawa tengah'];
        const totalByProv = {};
        branches.forEach(b => { totalByProv[b.province.toLowerCase()] = b.total; });
        try {
            const geo = await (await fetch('https://raw.githubusercontent.com/denyherianto/indonesia-geojson-topojson-maps-with-38-provinces/main/GeoJSON/indonesia-38-provinces.geojson')).json();
            geo.features = geo.features.filter(f => HILITE.some(h => (f.properties.PROVINSI || '').toLowerCase().includes(h)));
            L.geoJSON(geo, {
                style: { color: '#FBBF24', weight: 1.5, fillColor: '#FBBF24', fillOpacity: 0.18 },
                onEachFeature: (feature, layer) => {
                    const name = feature.properties.PROVINSI;
                    const total = totalByProv[name.toLowerCase()] || 0;
                    layer.bindTooltip(`<strong>${name}</strong> - ${total} cabang`, { sticky: true });
                    layer.on('mouseover', () => layer.setStyle({ fillOpacity: 0.35 }));
                    layer.on('mouseout', () => layer.setStyle({ fillOpacity: 0.18 }));
                },
            }).addTo(map);
        } catch (e) { console.warn('Highlight provinsi gagal dimuat', e); }

        const markers = points.map(p => {
            const m = L.circleMarker([+p.lat, +p.lng], {
                radius: 6, color: '#06122B', weight: 1.5, fillColor: '#FBBF24', fillOpacity: 1,
            }).addTo(map);
            m.bindTooltip(`${p.name} - ${p.province}`, { direction: 'top' });
            m.on('mouseover', () => m.setStyle({ fillColor: '#F59E0B', radius: 8 }));
            m.on('mouseout', () => m.setStyle({ fillColor: '#FBBF24', radius: 6 }));
            m.on('click', () => openBranch(p));
            return m;
        });

        if (markers.length) {
            map.fitBounds(L.featureGroup(markers).getBounds().pad(0.15));
        } else {
            map.setView([-7.0, 108.0], 7);
        }
    } catch(err) {
        console.error("Map rendering error: ", err);
    }
});
</script>
@endpush
