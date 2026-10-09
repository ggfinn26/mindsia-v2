@extends('layouts.dashboard')

@section('title', 'Edit Prospective Member')

@section('content')
<div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('prospective-members.show', $lead) }}" class="text-[var(--dash-text-muted)] hover:text-[var(--dash-text-body)] focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)] rounded-sm">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-[var(--dash-text-heading)]">Edit Data Lead</h1>
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

    <form action="{{ route('prospective-members.update', $lead) }}" method="POST" class="bg-[var(--dash-card-bg)] shadow rounded-lg p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="full_name" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $lead->full_name) }}" required
                class="block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('full_name') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
            @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="whatsapp_number" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Nomor WhatsApp <span class="text-red-500">*</span>
            </label>
            <div class="flex rounded-md shadow-sm">
                <span class="inline-flex items-center rounded-l-md border border-r-0 border-[var(--dash-input-border)] bg-[var(--dash-page-bg)] px-3 text-sm font-bold text-[var(--dash-text-muted)]">+62</span>
                <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $lead-  minlength="8" maxlength="15">whatsapp_number) }}" required
                    placeholder="8xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                    class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-[var(--dash-input-border)] text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('whatsapp_number') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
            </div>
            @error('whatsapp_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Email
            </label>
            <input type="email" name="email" id="email" value="{{ old('email', $lead->email) }}"
                class="block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('email') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="instagram" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Instagram (Username)
            </label>
            <input type="text" name="instagram" id="instagram" value="{{ old('instagram', $lead->instagram) }}"
                class="block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('instagram') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
            @error('instagram') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="gender" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                Jenis Kelamin
            </label>
            <select name="gender" id="gender" class="block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('gender') border-red-300 [.mode-dark_&]:border-red-500/20 @enderror">
                <option value="">Tidak Diketahui</option>
                <option value="L" {{ old('gender', $lead->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('gender', $lead->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-[var(--dash-card-border)]">
            <a href="{{ route('prospective-members.show', $lead) }}" class="px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] border border-[var(--dash-input-border)] rounded-md hover:bg-[var(--dash-page-bg)] focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)]">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[var(--dash-primary)] rounded-md hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)]">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
