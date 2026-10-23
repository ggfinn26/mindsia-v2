@extends('layouts.dashboard')

@section('title', 'Edit Tiket Fasilitas')
@section('header_title', 'Tiket Fasilitas')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('facility.tickets.show', $facilityTicket) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Detail Tiket
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Fasilitas</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Tiket Fasilitas</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">{{ $facilityTicket->ticket_number }} - {{ $facilityTicket->title }}</p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <form method="POST" action="{{ route('facility.tickets.update', $facilityTicket) }}">
        @csrf
        @method('PUT')

        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="rounded-t-2xl px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Tiket</h2>
            </div>
            <div class="px-6 py-5 space-y-5">

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Judul masalah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required maxlength="255"
                           value="{{ old('title', $facilityTicket->title) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Prioritas & Biaya Penanganan --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" required
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="low" @selected(old('priority', $facilityTicket->priority) === 'low')>Rendah</option>
                            <option value="medium" @selected(old('priority', $facilityTicket->priority) === 'medium')>Sedang</option>
                            <option value="high" @selected(old('priority', $facilityTicket->priority) === 'high')>Tinggi</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Biaya Penanganan (Rp)
                        </label>
                        <input type="number" name="cost_amount" min="0" step="0.01"
                               value="{{ old('cost_amount', $facilityTicket->cost_amount) }}"
                               placeholder="0"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('cost_amount')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all resize-none"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('description', $facilityTicket->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="rounded-b-2xl px-6 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('facility.tickets.show', $facilityTicket) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

</div>
@endsection