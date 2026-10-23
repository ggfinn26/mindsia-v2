@extends('layouts.dashboard')

@section('title', 'Edit Calon Anggota - MINDSIA')
@section('header_title', 'Edit Calon Anggota')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('prospective-members.show', $lead) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Detail Calon Anggota
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Pemasaran</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Calon Anggota</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui data profil dan informasi kontak calon anggota.
        </p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prospective-members.update', $lead) }}" method="POST"
          class="rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf
        @method('PUT')

        <div class="px-6 py-4 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Data Calon Anggota</h2>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label for="full_name" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $lead->full_name) }}" required
                    class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="whatsapp_number" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Nomor WhatsApp <span class="text-red-500">*</span>
                </label>
                <div class="flex rounded-lg overflow-hidden"
                     style="border: 1px solid var(--dash-input-border);">
                    <span class="inline-flex items-center px-3 text-sm font-bold shrink-0"
                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border-right: 1px solid var(--dash-input-border);">+62</span>
                    <input type="text" name="whatsapp_number" id="whatsapp_number"
                        value="{{ old('whatsapp_number', preg_replace('/^(\+?62|0)/', '', $lead->whatsapp_number ?? '')) }}" required
                        placeholder="8xxxxxxxxxx" minlength="8" maxlength="15"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                        class="flex-1 min-w-0 px-3 py-2.5 text-sm focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                        onfocus="this.closest('div').style.borderColor='var(--dash-primary)'; this.closest('div').style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.closest('div').style.borderColor='var(--dash-input-border)'; this.closest('div').style.boxShadow='none'">
                </div>
                @error('whatsapp_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Email (opsional)
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $lead->email) }}"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="instagram" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Instagram (Username)
                    </label>
                    <input type="text" name="instagram" id="instagram" value="{{ old('instagram', $lead->instagram) }}"
                        placeholder="@username"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('instagram') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Jenis Kelamin
                </label>
                <select name="gender" id="gender"
                    class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih ...</option>
                    <option value="L" {{ old('gender', $lead->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender', $lead->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="px-6 py-4 rounded-b-2xl flex items-center justify-end gap-3"
             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <a href="{{ route('prospective-members.show', $lead) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
