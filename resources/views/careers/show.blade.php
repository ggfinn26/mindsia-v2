@extends('layouts.landing')

@section('title', 'Detail Pekerjaan - MINDSIA')

@section('content')
<main class="flex-grow w-full pt-32 pb-24 bg-ky-surface">
    <div class="max-w-[800px] mx-auto px-6">
        
        <a href="{{ route('careers.index') }}" class="inline-flex items-center gap-2 text-[13px] font-bold text-ky-text/50 hover:text-[#06122B] uppercase tracking-wider mb-8 transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Daftar Karir
        </a>

        <div class="bg-white rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] border border-ky-border/50 overflow-hidden">
            <!-- Header -->
            <div class="p-8 md:p-12 border-b border-ky-border/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-ky-surface rounded-full -mr-20 -mt-20 opacity-50 z-0"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-wider rounded-full border border-green-200">Terbuka</span>
                        <span class="text-[12px] font-bold text-ky-text/50">Diunggah 2 hari yang lalu</span>
                    </div>
                    
                    <h1 class="font-sans font-black text-[32px] md:text-[40px] text-[#06122B] mb-4 tracking-tight leading-tight">{{ $job->position }}</h1>
                    
                    <div class="flex flex-wrap gap-4 text-[14px] text-ky-text/70 font-medium">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-gold">location_on</span>
                            {{ $job->branch?->name ?? 'MINDSIA Learning Center' }}
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-gold">schedule</span>
                            Full-Time
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8 md:p-12">
                <div class="prose prose-sm md:prose-base max-w-none text-ky-text/80">
                    <h3 class="font-black text-[#06122B] text-[18px] mb-4">Deskripsi Pekerjaan</h3>
                    <p class="mb-8 leading-relaxed">{{ $job->job_description }}</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <div class="bg-ky-surface p-6 rounded-xl border border-ky-border/50">
                            <div class="flex items-center gap-3 mb-4 text-[#06122B]">
                                <span class="material-symbols-outlined text-gold">school</span>
                                <h4 class="font-bold m-0">Kualifikasi Pendidikan</h4>
                            </div>
                            <p class="text-[14px] m-0">{{ $job->education_requirement }}</p>
                        </div>
                        <div class="bg-ky-surface p-6 rounded-xl border border-ky-border/50">
                            <div class="flex items-center gap-3 mb-4 text-[#06122B]">
                                <span class="material-symbols-outlined text-gold">work_history</span>
                                <h4 class="font-bold m-0">Pengalaman</h4>
                            </div>
                            <p class="text-[14px] m-0">{{ $job->experience_requirement }}</p>
                        </div>
                    </div>

                    <h3 class="font-black text-[#06122B] text-[18px] mb-4">Tanggung Jawab Utama</h3>
                    <ul class="space-y-2 mb-8">
                        <li class="flex gap-3"><span class="text-gold font-bold">✓</span> Membimbing siswa dalam program intensif persiapan TOEFL.</li>
                        <li class="flex gap-3"><span class="text-gold font-bold">✓</span> Menyusun laporan perkembangan akademik siswa secara berkala.</li>
                        <li class="flex gap-3"><span class="text-gold font-bold">✓</span> Menjaga budaya berbahasa Inggris di lingkungan asrama 24 HESA.</li>
                    </ul>
                </div>
                
                <div class="mt-12 pt-8 border-t border-ky-border/50 flex justify-end">
                    <a href="{{ route('applicant.register') }}" class="inline-block px-10 py-4 bg-[#06122B] text-white rounded-xl text-[14px] font-bold shadow-lg hover:bg-gold hover:text-[#06122B] transition-all hover:-translate-y-1">Lamar Posisi Ini Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
