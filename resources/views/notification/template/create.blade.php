@extends('layouts.dashboard')

@section('title', 'Buat Template Notifikasi')
@section('header_title', 'Buat Template Notifikasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('notification-templates.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Template Notifikasi
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Notifikasi</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Buat Template Notifikasi</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Daftarkan template pesan baru untuk pengiriman notifikasi otomatis maupun manual.
        </p>
    </div>

    {{-- Error Summary --}}
    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <div class="flex items-center gap-2 font-semibold mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <span>Terdapat kesalahan pada input form:</span>
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('notification-templates.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            {{-- Template Key --}}
            <div>
                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                    Template Key <span class="text-red-500">*</span>
                </label>
                <input name="template_key" type="text" value="{{ old('template_key') }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none font-mono transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       placeholder="contoh: payslip_ready_inapp"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                       required />
                <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">
                    Unik, format snake_case. Konvensi penamaan: <code class="px-1.5 py-0.5 rounded text-[11px] font-mono" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">event_channel</code> (mis. leave_approved_email).
                </p>
                @error('template_key')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipe / Channel --}}
            <div>
                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                    Tipe / Channel <span class="text-red-500">*</span>
                </label>
                <select name="type"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                        required>
                    <option value="">Pilih tipe...</option>
                    <option value="in-app" {{ old('type') === 'in-app' ? 'selected' : '' }}>In-App</option>
                    <option value="email" {{ old('type') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="telegram" {{ old('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject --}}
            <div>
                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                    Subject <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(wajib untuk Email)</span>
                </label>
                <input name="subject" type="text" value="{{ old('subject') }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       placeholder="Contoh: Slip Gaji Bulan @{{bulan}} Telah Tersedia"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                @error('subject')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Body / Isi Pesan --}}
            <div>
                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                    Body / Isi Pesan <span class="text-red-500">*</span>
                </label>
                <textarea name="body" rows="8"
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all font-sans leading-relaxed"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          placeholder="Halo @{{nama_pegawai}}, slip gaji Anda untuk bulan @{{bulan}} telah tersedia."
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                          required>{{ old('body') }}</textarea>
                <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">
                    Gunakan placeholder dinamis dengan format <code class="px-1.5 py-0.5 rounded text-[11px] font-mono" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">@{{nama_variabel}}</code>.
                </p>
                @error('body')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t"
                 style="border-color: var(--dash-card-border);">
                <a href="{{ route('notification-templates.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shadow-sm"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Template
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
