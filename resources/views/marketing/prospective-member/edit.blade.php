@extends('layouts.dashboard')

@section('title', 'Edit Prospective Member')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('prospective-members.show', $lead) }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight text-[var(--dash-text-heading)]">Edit Data Lead</h1>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prospective-members.update', $lead) }}" method="POST" class="bg-[var(--dash-card-bg)] rounded-2xl border border-[var(--dash-card-border)] p-6 md:p-8 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="full_name" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $lead->full_name) }}" required
                class="block w-full bg-[var(--dash-page-bg)] border-[var(--dash-input-border)] text-[var(--dash-text-heading)] rounded-lg text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('full_name') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
            @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="whatsapp_number" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Nomor WhatsApp <span class="text-red-500">*</span>
            </label>
            <div class="flex rounded-lg shadow-sm">
                <span class="inline-flex items-center rounded-l-lg border border-r-0 border-[var(--dash-input-border)] bg-[var(--dash-page-bg)] px-3 text-sm font-bold text-[var(--dash-text-muted)]">+62</span>
                <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $lead->whatsapp_number) }}" required minlength="8" maxlength="15"
                    placeholder="8xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                    class="block w-full min-w-0 flex-1 bg-[var(--dash-page-bg)] border-[var(--dash-input-border)] text-[var(--dash-text-heading)] rounded-none rounded-r-lg text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('whatsapp_number') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
            </div>
            @error('whatsapp_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Email
            </label>
            <input type="email" name="email" id="email" value="{{ old('email', $lead->email) }}"
                class="block w-full bg-[var(--dash-page-bg)] border-[var(--dash-input-border)] text-[var(--dash-text-heading)] rounded-lg text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('email') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="instagram" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Instagram (Username)
            </label>
            <input type="text" name="instagram" id="instagram" value="{{ old('instagram', $lead->instagram) }}"
                class="block w-full bg-[var(--dash-page-bg)] border-[var(--dash-input-border)] text-[var(--dash-text-heading)] rounded-lg text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('instagram') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
            @error('instagram') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="gender" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Jenis Kelamin
            </label>
            <select name="gender" id="gender" class="block w-full bg-[var(--dash-page-bg)] border-[var(--dash-input-border)] text-[var(--dash-text-heading)] rounded-lg text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('gender') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
                <option value="">Tidak Diketahui</option>
                <option value="L" {{ old('gender', $lead->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('gender', $lead->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-[var(--dash-card-border)]">
            <a href="{{ route('prospective-members.show', $lead) }}" class="px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] border border-[var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-page-bg)] focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)] inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[var(--dash-primary)] rounded-lg hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)] inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
