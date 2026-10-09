@extends('layouts.dashboard')

@section('title', 'Tambah Routing Notifikasi - MINDSIA')
@section('header_title', 'Tambah Routing Notifikasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Back + header --}}
    <div>
        <a href="{{ route('system.notification-routings.index') }}"
           class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider hover:opacity-75 transition-opacity"
           style="color: var(--dash-text-muted);">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Routing
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Aturan Routing</h1>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5 font-medium">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('system.notification-routings.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">

                {{-- Event Key --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5"
                           style="color: var(--dash-text-body);">
                        Event Key <span style="color: #dc2626;">*</span>
                    </label>
                    <input name="event_key" type="text" value="{{ old('event_key') }}"
                           placeholder="contoh: leave_request_created"
                           class="font-mono" required>
                    <p class="mt-1.5 text-xs" style="color: var(--dash-text-faint);">
                        Nama event yang di-trigger sistem (format snake_case).
                    </p>
                </div>

                {{-- Target Posisi --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5"
                           style="color: var(--dash-text-body);">
                        Target Posisi
                        <span class="text-xs font-normal ml-1" style="color: var(--dash-text-muted);">(kosong = semua posisi)</span>
                    </label>
                    <select name="position_id">
                        <option value="">Semua Posisi</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}"
                                    @if(old('position_id') == $pos->id) selected @endif>
                                {{ $pos->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Channel + Scope --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5"
                               style="color: var(--dash-text-body);">
                            Channel <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="channel" required>
                            <option value="" disabled @if(!old('channel')) selected @endif>Pilih Channel</option>
                            <option value="in-app"    @if(old('channel') === 'in-app') selected @endif>In-App</option>
                            <option value="email"     @if(old('channel') === 'email') selected @endif>Email</option>
                            <option value="telegram"  @if(old('channel') === 'telegram') selected @endif>Telegram</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5"
                               style="color: var(--dash-text-body);">
                            Scope <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="scope" required>
                            <option value="" disabled @if(!old('scope')) selected @endif>Pilih Scope</option>
                            <option value="global" @if(old('scope') === 'global') selected @endif>Global</option>
                            <option value="branch" @if(old('scope') === 'branch') selected @endif>Branch</option>
                            <option value="area"   @if(old('scope') === 'area') selected @endif>Area</option>
                        </select>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5"
                           style="color: var(--dash-text-body);">
                        Deskripsi
                        <span class="text-xs font-normal ml-1" style="color: var(--dash-text-muted);">(opsional)</span>
                    </label>
                    <input name="description" type="text" value="{{ old('description') }}"
                           placeholder="Contoh: Notifikasi ke HR saat ada pengajuan cuti baru">
                </div>

                {{-- Aktif --}}
                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           style="accent-color: var(--dash-primary);"
                           class="h-4 w-4 rounded"
                           @if(old('is_active', '1')) checked @endif>
                    <label for="is_active" class="text-sm font-semibold"
                           style="color: var(--dash-text-body);">Aktifkan aturan ini</label>
                </div>
            </div>

            <div class="px-6 py-4 flex justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('system.notification-routings.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Aturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
