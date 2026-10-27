@extends('layouts.landing')

@section('title', $program->program_name.' - Program MINDSIA')
@section('description', $program->tagline ?? 'Detail program '.$program->program_name.' di MINDSIA.')

@section('content')

@php
    $waText = rawurlencode('Halo MINDSIA, saya ingin konsultasi dan tanya info kuota serta pendaftaran program '.$program->program_name.'.');
    $waUrl = 'https://wa.me/6289518494953?text='.$waText;
    $registerUrl = route('member.register', ['program' => $program->id]);
@endphp

{{-- BREADCRUMB --}}
<div class="border-b border-slate-200 bg-slate-50 pt-24 md:pt-28">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 text-xs text-slate-500 overflow-x-auto whitespace-nowrap" aria-label="Breadcrumb">
        <a href="{{ apex_url('/') }}" class="hover:text-ky-primary transition-colors">Beranda</a>
        <span>/</span>
        <a href="{{ apex_url('#program') }}" class="hover:text-ky-primary transition-colors">Program Kursus</a>
        <span>/</span>
        <span class="font-semibold text-[#06122B] truncate max-w-[200px] sm:max-w-md">{{ $program->program_name }}</span>
    </nav>
</div>

{{-- HERO --}}
<section class="relative overflow-hidden bg-slate-950 text-white py-10 sm:py-12 lg:py-16" id="hero">
    <div class="absolute inset-0 z-0">
        <img src="{{ $program->image_url }}" alt="" class="w-full h-full object-cover object-center opacity-25">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/90 to-slate-950/60"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-slate-950/40"></div>
        <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-ky-primary/20 blur-3xl pointer-events-none"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-12 lg:items-center">
        <div class="lg:col-span-7 space-y-5 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-0.5 text-[11px] font-bold uppercase tracking-wider">Kelas Program MINDSIA</span>
                @if($program->badge)
                    <span class="inline-flex items-center rounded-full border border-gold/40 bg-gold/10 px-3 py-0.5 text-[11px] font-bold uppercase tracking-wider text-gold">{{ $program->badge }}</span>
                @endif
                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-400/40 bg-amber-400/10 px-3 py-0.5 text-[11px] font-bold uppercase tracking-wider text-amber-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Kuota Terbatas Tiap Batch
                </span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight break-words">{{ $program->program_name }}</h1>
            @if($program->tagline)
                <p class="text-base sm:text-lg text-white/75 leading-relaxed max-w-2xl">{{ $program->tagline }}</p>
            @endif
            <div class="flex flex-wrap gap-x-8 gap-y-3 text-sm">
                @if($program->target_level)
                    <div><span class="text-white/50">Level:</span> <span class="font-bold">{{ $program->target_level }}</span></div>
                @endif
                @if($program->duration)
                    <div><span class="text-white/50">Durasi:</span> <span class="font-bold">{{ $program->duration }}</span></div>
                @endif
            </div>
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <a href="#rincian" class="inline-flex items-center justify-center gap-2 bg-gold text-[#06122B] font-bold py-3.5 px-7 rounded-xl hover:-translate-y-0.5 transition-all shadow-lg">
                    <span class="material-symbols-outlined text-[20px]">menu_book</span>
                    Lihat Rincian Program
                </a>
                <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 border-2 border-white/30 font-bold py-3.5 px-7 rounded-xl hover:border-white hover:bg-white/10 transition-all">
                    <span class="material-symbols-outlined text-[20px]">chat</span>
                    Tanya Admin / Cek Kuota
                </a>
            </div>
        </div>
        <div class="lg:col-span-5">
            <div class="rounded-3xl overflow-hidden border border-white/15 bg-white/5 backdrop-blur shadow-2xl">
                <div class="aspect-[16/10] bg-slate-900">
                    <img src="{{ $program->image_url }}" alt="{{ $program->program_name }}" class="w-full h-full object-cover">
                </div>
                <div class="p-5 sm:p-6 space-y-3.5">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-amber-400/20 text-amber-300 border border-amber-400/30">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        Kuota Terbatas Tiap Batch
                    </div>
                    <div class="text-lg font-black text-white">Bimbingan Intensif &amp; Terfokus</div>
                    <p class="text-xs text-white/70 leading-relaxed">Slot peserta dibatasi setiap angkatan demi menjaga kualitas bimbingan tutor. Silakan hubungi admin untuk informasi jadwal dan sisa kuota.</p>
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 w-full bg-gold text-[#06122B] font-bold py-3 rounded-xl hover:opacity-90 transition-opacity text-sm shadow-md">
                        <span class="material-symbols-outlined text-[20px]">chat</span>
                        Hubungi Admin via WhatsApp
                    </a>
                    <a href="{{ $registerUrl }}" class="flex items-center justify-center gap-2 w-full border border-white/25 text-white/80 font-semibold py-2 rounded-xl hover:bg-white/10 transition-colors text-xs">
                        <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                        Daftar Mandiri Langsung
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- RINCIAN --}}
<section class="bg-ky-surface py-10 sm:py-14" id="rincian">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-[#06122B]">Rincian &amp; Kurikulum {{ $program->program_name }}</h2>
            <p class="text-sm text-slate-500 mt-1">Informasi materi, fasilitas, dan jadwal pembelajaran lengkap {{ $program->program_name }}.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-12 items-start">
            {{-- MAIN CONTENT --}}
            <div class="lg:col-span-8 space-y-6 min-w-0">
                @if($program->program_description)
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm" id="ikhtisar">
                    <div class="px-5 py-4 sm:px-8 border-b border-slate-200 bg-slate-50 rounded-t-3xl">
                        <h3 class="text-base sm:text-lg font-bold text-[#06122B]">Ikhtisar Program</h3>
                    </div>
                    <div class="p-5 sm:p-8 rich-content prose max-w-none text-ky-text/80 leading-relaxed">{!! clean($program->program_description) !!}</div>
                </div>
                @endif

                @if(!empty($program->facilities) || !empty($program->benefits) || $program->duration)
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm" id="fasilitas">
                    <div class="px-5 py-4 sm:px-8 border-b border-slate-200 bg-slate-50 rounded-t-3xl">
                        <h3 class="text-base sm:text-lg font-bold text-[#06122B]">Informasi Belajar &amp; Fasilitas</h3>
                    </div>
                    <div class="p-5 sm:p-8 space-y-5">
                        @if($program->duration)
                        <div class="rounded-2xl border border-ky-primary/20 bg-ky-primary/5 px-4 py-3.5 flex items-center gap-3">
                            <span class="material-symbols-outlined text-ky-primary">schedule</span>
                            <div>
                                <div class="text-[11px] font-bold uppercase tracking-wider text-ky-primary">Durasi Pembelajaran</div>
                                <div class="text-sm font-semibold text-[#06122B]">{{ $program->duration }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach(['Fasilitas Kelas' => $program->facilities, 'Keunggulan Program' => $program->benefits] as $label => $list)
                                @if(!empty($list))
                                <div class="rounded-2xl border border-slate-200 p-4 sm:p-5 space-y-3">
                                    <div class="text-xs font-bold uppercase tracking-wider text-ky-primary">{{ $label }}</div>
                                    <ul class="space-y-2">
                                        @foreach($list as $item)
                                        <li class="flex items-start gap-2 text-xs text-slate-600">
                                            <span class="material-symbols-outlined text-gold-d text-[16px] shrink-0">check_circle</span>
                                            <span class="break-words">{{ $item }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty($program->schedule))
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm" id="jadwal">
                    <div class="px-5 py-4 sm:px-8 border-b border-slate-200 bg-slate-50 rounded-t-3xl">
                        <h3 class="text-base sm:text-lg font-bold text-[#06122B]">Pilihan Jadwal Belajar</h3>
                    </div>
                    <div class="p-5 sm:p-8 grid gap-3 sm:grid-cols-2">
                        @foreach($program->schedule as $slot)
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:border-ky-primary/40 hover:bg-ky-primary/5 transition-colors">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-ky-primary/10 text-ky-primary">
                                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                            </div>
                            <span class="text-sm font-semibold text-[#06122B]">{{ $slot['day'] ?? '' }}@if(!empty($slot['start'])) ({{ $slot['start'] }} - {{ $slot['end'] ?? '' }})@endif</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($photos->isNotEmpty())
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm" id="galeri">
                    <div class="px-5 py-4 sm:px-6 border-b border-slate-200 bg-slate-50 rounded-t-3xl flex items-center justify-between gap-3">
                        <h3 class="text-base sm:text-lg font-bold text-[#06122B]">Suasana Kelas &amp; Komunitas Belajar</h3>
                        <span class="text-xs font-semibold text-ky-primary bg-ky-primary/10 px-3 py-1 rounded-full whitespace-nowrap">{{ $photos->count() }} Foto</span>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($photos as $photo)
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100">
                            <img src="{{ $photo->photo_url }}" alt="{{ $photo->title ?? 'Suasana belajar '.$program->program_name }}" loading="lazy" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($program->faqs))
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm" id="faq">
                    <div class="px-5 py-4 sm:px-8 border-b border-slate-200 bg-slate-50 rounded-t-3xl">
                        <h3 class="text-base sm:text-lg font-bold text-[#06122B]">Tanya Jawab (FAQ) Program</h3>
                    </div>
                    <div class="p-5 sm:p-8 space-y-3">
                        @foreach($program->faqs as $faq)
                        <div x-data="{ open: false }" class="rounded-2xl border border-slate-200">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-4 text-left px-5 py-4">
                                <span class="font-bold text-sm text-[#06122B]">{{ $faq['q'] ?? '' }}</span>
                                <span class="material-symbols-outlined text-ky-primary transition-transform shrink-0" :class="open && 'rotate-180'">expand_more</span>
                            </button>
                            <div x-show="open" x-collapse class="px-5 pb-4 text-sm text-slate-600 leading-relaxed">{{ $faq['a'] ?? '' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- SIDEBAR: KUOTA TERBATAS & HUBUNGI ADMIN --}}
            <aside class="lg:col-span-4 lg:sticky lg:top-28 space-y-4 min-w-0">
                <div class="rounded-3xl bg-navy text-white p-6 shadow-xl space-y-5">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-gold/15 text-gold border border-gold/30">
                            <span class="w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                            Pendaftaran Dibuka
                        </div>
                        <div class="text-2xl font-black text-white mt-3">Kuota Sangat Terbatas</div>
                        @if($program->duration)
                            <div class="text-xs text-white/60 mt-1 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-gold">schedule</span>
                                {{ $program->duration }}
                            </div>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-white/15 bg-white/5 p-4 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gold">
                            <span class="material-symbols-outlined text-[18px]">verified</span>
                            Informasi &amp; Konsultasi Slot
                        </div>
                        <ul class="space-y-2.5 text-xs text-white/80">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-gold text-[16px] shrink-0">check_circle</span>
                                <span>Cek sisa kuota batch tatap muka &amp; virtual terdekat</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-gold text-[16px] shrink-0">check_circle</span>
                                <span>Konsultasi gratis penentuan level &amp; target belajar</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-gold text-[16px] shrink-0">check_circle</span>
                                <span>Informasi promo dan skema pendaftaran batch ini</span>
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-2.5 pt-1">
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 w-full bg-gold text-[#06122B] font-bold py-3.5 rounded-xl hover:-translate-y-0.5 transition-all shadow-lg text-sm">
                            <span class="material-symbols-outlined text-[20px]">chat</span>
                            Hubungi Admin via WhatsApp
                        </a>
                        <a href="{{ $registerUrl }}" class="flex items-center justify-center gap-2 w-full border border-white/25 font-semibold py-2.5 rounded-xl hover:bg-white/10 transition-colors text-xs text-white/90">
                            <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                            Daftar Mandiri Langsung
                        </a>
                    </div>

                    <div class="text-[11px] text-center text-white/50 leading-snug">
                        Respon cepat via WhatsApp Admin MINDSIA
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- PROGRAM LAIN --}}
@if($otherPrograms->isNotEmpty())
<section class="py-14 md:py-20 px-4 sm:px-6 lg:px-8 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto">
        <h2 class="font-sans font-bold text-2xl md:text-3xl text-[#06122B] mb-8">Program Lainnya</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($otherPrograms as $other)
            <a href="{{ route('program.show', $other->slug) }}" class="group block p-6 rounded-2xl border border-slate-200 bg-ky-surface hover:border-ky-primary/40 hover:shadow-lg transition-all">
                <div class="text-[11px] font-bold text-ky-primary uppercase tracking-widest mb-2">{{ $other->badge }}</div>
                <h3 class="font-sans font-bold text-xl text-[#06122B] mb-2 group-hover:text-ky-primary transition-colors">{{ $other->program_name }}</h3>
                @if($other->tagline)
                    <p class="text-sm text-ky-text/60 leading-relaxed">{{ \Illuminate\Support\Str::limit($other->tagline, 90) }}</p>
                @endif
                <div class="mt-4 pt-4 border-t border-slate-200 flex items-center justify-between text-xs">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-amber-700 bg-amber-50 border border-amber-200/60 px-2.5 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Kuota Terbatas
                    </span>
                    <span class="font-bold text-ky-primary group-hover:translate-x-0.5 transition-transform flex items-center gap-1">
                        Detail Program
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
