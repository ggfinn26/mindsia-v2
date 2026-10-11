@extends('layouts.dashboard')

@section('title', 'Edit Test TOEFL')
@section('header_title', 'Edit Test TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('toefl-tests.show', $toeflTest) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Detail Test
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">TOEFL</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Test TOEFL</h1>
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

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Data Test</h2>
        </div>
        <form action="{{ route('toefl-tests.update', $toeflTest) }}" method="POST" class="px-6 py-5 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="test_name" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Nama Test <span class="text-red-500">*</span>
                </label>
                <input id="test_name" name="test_name" type="text"
                       value="{{ old('test_name', $toeflTest->test_name) }}" required
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                @error('test_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Password Test
                    <span class="text-xs font-normal ml-1" style="color: var(--dash-text-faint);">(kosongkan untuk hapus password)</span>
                </label>
                <input id="password" name="password" type="text"
                       placeholder="Min. 4 karakter - kosongkan jika tidak berubah"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">Biarkan kosong untuk mempertahankan password saat ini.</p>
                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="level" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Level <span class="text-xs font-normal ml-1" style="color: var(--dash-text-faint);">(opsional)</span>
                    </label>
                    <select id="level" name="level"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Level ...</option>
                        <option value="beginner" {{ old('level', $toeflTest->level) === 'beginner' ? 'selected' : '' }}>Beginner (skor &lt; 450)</option>
                        <option value="intermediate" {{ old('level', $toeflTest->level) === 'intermediate' ? 'selected' : '' }}>Intermediate (skor 450-500)</option>
                        <option value="advanced" {{ old('level', $toeflTest->level) === 'advanced' ? 'selected' : '' }}>Advanced (skor &gt; 500)</option>
                    </select>
                    @error('level')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center gap-3 pt-6">
                    <input id="is_trial" name="is_trial" type="checkbox" value="1"
                           class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5 cursor-pointer"
                           {{ old('is_trial', $toeflTest->is_trial) ? 'checked' : '' }} />
                    <label for="is_trial" class="text-sm font-medium cursor-pointer select-none"
                           style="color: var(--dash-text-body);">
                        Test Trial
                        <span class="text-xs font-normal ml-1" style="color: var(--dash-text-faint);">(akses publik tanpa login member)</span>
                    </label>
                </div>
            </div>

            <div class="pt-4" style="border-top: 1px solid var(--dash-card-border);">
                <p class="text-sm font-medium mb-3" style="color: var(--dash-text-body);">Durasi per Section (menit)</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Listening</label>
                        <input name="listening_time_limit" type="number" min="1"
                               value="{{ old('listening_time_limit', $toeflTest->listening_time_limit) }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Structure</label>
                        <input name="structure_time_limit" type="number" min="1"
                               value="{{ old('structure_time_limit', $toeflTest->structure_time_limit) }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Reading</label>
                        <input name="reading_time_limit" type="number" min="1"
                               value="{{ old('reading_time_limit', $toeflTest->reading_time_limit) }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-4" style="border-top: 1px solid var(--dash-card-border);">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>Simpan Perubahan
                </button>
                <a href="{{ route('toefl-tests.show', $toeflTest) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
