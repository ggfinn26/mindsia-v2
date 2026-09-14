@extends('layouts.landing')

@section('title', 'Karir - MINDSIA')

@section('content')
<!-- Hero Section -->
<section class="relative w-full min-h-[40vh] md:min-h-[50vh] flex items-center justify-center overflow-hidden bg-[#06122B] pt-20">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2084&auto=format&fit=crop" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-t from-[#06122B] via-transparent to-transparent"></div>
    </div>
    <div class="relative z-10 text-center px-6 max-w-3xl mx-auto">
        <span class="inline-block py-1 px-3 border border-gold/30 rounded-full text-gold text-[10px] md:text-[12px] font-bold tracking-[0.2em] uppercase mb-4">Mindsia Career Center</span>
        <h1 class="font-sans font-black text-[40px] md:text-[56px] text-white leading-tight mb-4 tracking-tight">Bangun Karir <br>Bersama Kami</h1>
        <p class="text-[16px] md:text-[18px] text-white/70">Jadilah bagian dari revolusi pendidikan bahasa Inggris dengan sistem 24 HESA bertaraf internasional.</p>
    </div>
</section>

<!-- Values Section -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="font-sans font-black text-[32px] text-[#06122B] mb-2 tracking-tight">Mengapa Bergabung dengan MINDSIA?</h2>
            <div class="w-16 h-1 bg-gold mx-auto"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-8 rounded-2xl bg-ky-surface border border-ky-border/50 text-center group hover:border-gold/50 transition-colors">
                <div class="w-16 h-16 bg-[#06122B] text-gold rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[32px]">school</span>
                </div>
                <h3 class="font-bold text-[20px] text-[#06122B] mb-3">Lingkungan Akademik</h3>
                <p class="text-[14px] text-ky-text/70">Kembangkan diri Anda di lingkungan yang sangat mendukung pertumbuhan intelektual dan profesional.</p>
            </div>
            
            <div class="p-8 rounded-2xl bg-ky-surface border border-ky-border/50 text-center group hover:border-gold/50 transition-colors">
                <div class="w-16 h-16 bg-[#06122B] text-gold rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[32px]">trending_up</span>
                </div>
                <h3 class="font-bold text-[20px] text-[#06122B] mb-3">Jenjang Karir Jelas</h3>
                <p class="text-[14px] text-ky-text/70">Kami menyediakan jalur promosi dan evaluasi KPI yang transparan untuk setiap anggota tim.</p>
            </div>
            
            <div class="p-8 rounded-2xl bg-ky-surface border border-ky-border/50 text-center group hover:border-gold/50 transition-colors">
                <div class="w-16 h-16 bg-[#06122B] text-gold rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[32px]">location_city</span>
                </div>
                <h3 class="font-bold text-[20px] text-[#06122B] mb-3">Fasilitas Lengkap</h3>
                <p class="text-[14px] text-ky-text/70">Nikmati kenyamanan bekerja dengan dukungan fasilitas asrama dan lingkungan kerja bertaraf global.</p>
            </div>
        </div>
    </div>
</section>

<!-- Job Openings Section -->
<main class="flex-grow w-full pt-16 pb-32 bg-ky-surface">
    <div class="max-w-[1000px] mx-auto px-6">
        <!-- Heading -->
        <div class="mb-12 border-l-[4px] border-gold pl-6">
            <h2 class="font-sans font-black text-[32px] md:text-[40px] tracking-tight text-[#06122B] mb-2">Posisi Terbuka</h2>
            <p class="text-[15px] text-ky-text/70">Temukan peran yang cocok dengan keahlian Anda saat ini.</p>
        </div>

        @if($openings->isEmpty())
        <!-- Empty State -->
        <div class="w-full bg-white rounded-2xl shadow-sm border border-ky-border/50 p-16 text-center">
            <div class="text-[48px] text-ky-text/20 mb-6 font-['Material_Symbols_Outlined']">work_off</div>
            <h3 class="text-[20px] font-bold text-[#06122B] mb-2">Belum Ada Lowongan</h3>
            <p class="text-[15px] text-ky-text/60">Saat ini belum ada posisi yang sedang kami cari. Pantau terus halaman ini untuk update terbaru.</p>
        </div>
        @else
        <!-- Job List -->
        <div class="flex flex-col gap-6 w-full">
            @foreach($openings as $req)
            <a href="{{ route('careers.show', $req->id) }}" class="group bg-white rounded-2xl shadow-sm border border-ky-border/50 p-8 hover:border-[#06122B]/20 transition-all duration-300 block hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.1)] relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-ky-surface rounded-full -mr-10 -mt-10 opacity-50 group-hover:scale-150 transition-transform duration-700 ease-out z-0"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-[22px] font-black text-[#06122B] tracking-tight">{{ $req->position }}</h3>
                            <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-wider rounded-full border border-green-200">Terbuka</span>
                        </div>
                        <p class="text-[14px] text-ky-text/60 flex items-center gap-2 font-medium">
                            <span class="material-symbols-outlined text-[16px] text-gold">location_on</span>
                            {{ $req->branch?->name ?? 'MINDSIA Learning Center' }}
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap md:justify-end gap-2">
                        @if($req->education_requirement)
                        <span class="text-[11px] font-bold bg-ky-surface text-ky-text/70 px-3 py-1.5 uppercase tracking-wider rounded-lg border border-ky-border/50 flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">school</span> {{ $req->education_requirement }}</span>
                        @endif
                        @if($req->experience_requirement)
                        <span class="text-[11px] font-bold bg-ky-surface text-ky-text/70 px-3 py-1.5 uppercase tracking-wider rounded-lg border border-ky-border/50 flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">work_history</span> {{ $req->experience_requirement }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="relative z-10 mt-6 pt-6 border-t border-ky-border/50 flex items-center justify-between text-[13px] font-bold text-[#06122B] group-hover:text-gold transition-colors">
                    <span class="uppercase tracking-wider">Lihat Detail & Lamar</span>
                    <div class="w-10 h-10 rounded-full bg-ky-surface group-hover:bg-[#06122B] group-hover:text-gold flex items-center justify-center transition-all shadow-sm">
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</main>
@endsection
