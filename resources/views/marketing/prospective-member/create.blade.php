@extends('layouts.dashboard')

@section('title', 'Tambah Prospective Member')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ request('socialization_id') ? route('socializations.show', request('socialization_id')) : route('prospective-members.index') }}" class="text-[var(--dash-text-muted)] hover:text-[var(--dash-text-body)]">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-[var(--dash-text-heading)]">Tambah Lead Baru</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 [.mode-dark_&]:bg-red-500/10 border border-red-300 [.mode-dark_&]:border-red-500/20 text-red-700 [.mode-dark_&]:text-red-400 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prospective-members.store') }}" method="POST" class="bg-[var(--dash-card-bg)] shadow rounded-lg p-6 space-y-5">
        @csrf

        @if (request('socialization_id'))
            <input type="hidden" name="socialization_id" value="{{ request('socialization_id') }}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="full_name" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                    class="block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('full_name') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
                @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="whatsapp_number" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                    Nomor WhatsApp <span class="text-red-500">*</span>
                </label>
                <div class="flex rounded-md shadow-sm">
                    <span class="inline-flex items-center rounded-l-md border border-r-0 border-[var(--dash-input-border)] bg-[var(--dash-page-bg)] px-3 text-sm font-bold text-[var(--dash-text-muted)]">+62</span>
                    <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number') }}" required
                        placeholder="8xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                        class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-[var(--dash-input-border)] text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('whatsapp_number') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror" minlength="8" maxlength="15">
                </div>
                @error('whatsapp_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label for="gender" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Jenis Kelamin</label>
                <select name="gender" id="gender" data-no-enhance
                    class="block w-full bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg px-3.5 py-2.5 text-[13px] text-[var(--dash-text-heading)] shadow-sm focus:outline-none focus:border-[var(--dash-primary)] focus:ring-2 focus:ring-[var(--dash-primary)]/30 transition-all">
                    <option value="">Tidak Diketahui</option>
                    <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Status Awal</label>
                <select name="status" id="status" data-no-enhance
                    class="block w-full bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg px-3.5 py-2.5 text-[13px] text-[var(--dash-text-heading)] shadow-sm focus:outline-none focus:border-[var(--dash-primary)] focus:ring-2 focus:ring-[var(--dash-primary)]/30 transition-all">
                    <option value="ALMOST" {{ old('status', 'ALMOST') === 'ALMOST' ? 'selected' : '' }}>Hampir (ALMOST)</option>
                    <option value="YES" {{ old('status') === 'YES' ? 'selected' : '' }}>Berminat (YES)</option>
                    <option value="NO" {{ old('status') === 'NO' ? 'selected' : '' }}>Tidak (NO)</option>
                </select>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Email (opsional)</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="block w-full bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg px-3.5 py-2.5 text-[13px] text-[var(--dash-text-heading)] shadow-sm focus:outline-none focus:border-[var(--dash-primary)] focus:ring-2 focus:ring-[var(--dash-primary)]/30 transition-all">
            </div>
        </div>

        <div>
            <label for="institution_id" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Sekolah (opsional)</label>
            <select name="institution_id" id="institution_id"
                class="block w-full bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg px-3.5 py-2.5 text-[13px] text-[var(--dash-text-heading)] shadow-sm focus:outline-none focus:border-[var(--dash-primary)] focus:ring-2 focus:ring-[var(--dash-primary)]/30 transition-all">
                <option value="">Tidak Ada</option>
                @foreach (\App\Models\Institution::orderBy('institution_name')->get() as $inst)
                    <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                        {{ $inst->institution_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="instagram" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Instagram (opsional)</label>
            <input type="text" name="instagram" id="instagram" value="{{ old('instagram') }}"
                placeholder="@username"
                class="block w-full bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg px-3.5 py-2.5 text-[13px] text-[var(--dash-text-heading)] shadow-sm focus:outline-none focus:border-[var(--dash-primary)] focus:ring-2 focus:ring-[var(--dash-primary)]/30 transition-all">
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Catatan Awal</label>
            <textarea name="notes" id="notes" rows="3"
                class="block w-full bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg px-3.5 py-2.5 text-[13px] text-[var(--dash-text-heading)] shadow-sm focus:outline-none focus:border-[var(--dash-primary)] focus:ring-2 focus:ring-[var(--dash-primary)]/30 transition-all">{{ old('notes') }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ request('socialization_id') ? route('socializations.show', request('socialization_id')) : route('prospective-members.index') }}" class="px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] border border-[var(--dash-input-border)] rounded-md hover:bg-[var(--dash-page-bg)]">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[var(--dash-primary)] rounded-md hover:opacity-90">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
