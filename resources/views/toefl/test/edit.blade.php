@extends('layouts.dashboard')

@section('title', 'Edit Test TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-[color:var(--dash-text-heading)]">Edit Test TOEFL</h1>
        <a href="{{ route('toefl-tests.show', $toeflTest) }}" class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] hover:bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)] text-sm font-medium transition-colors">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)]">
        <form action="{{ route('toefl-tests.update', $toeflTest) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="test_name" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Nama Test <span class="text-red-500">*</span></label>
                <input id="test_name" name="test_name" type="text"
                    value="{{ old('test_name', $toeflTest->test_name) }}"
                    class="form-input w-full min-h-[44px] @error('test_name') border-red-400 @enderror" required />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">
                    Password Test <span class="text-[color:var(--dash-text-faint)] font-normal">(kosongkan untuk hapus password)</span>
                </label>
                <input id="password" name="password" type="text"
                    class="form-input w-full min-h-[44px] @error('password') border-red-400 @enderror"
                    placeholder="Min. 4 karakter — kosongkan jika tidak berubah" />
                <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Biarkan kosong untuk mempertahankan password saat ini.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="level" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">
                        Level <span class="text-[color:var(--dash-text-faint)] font-normal">(opsional)</span>
                    </label>
                    <select id="level" name="level" class="form-select w-full min-h-[44px] @error('level') border-red-400 @enderror">
                        <option value="">— Pilih Level —</option>
                        <option value="beginner" {{ old('level', $toeflTest->level) === 'beginner' ? 'selected' : '' }}>Beginner (skor &lt; 450)</option>
                        <option value="intermediate" {{ old('level', $toeflTest->level) === 'intermediate' ? 'selected' : '' }}>Intermediate (skor 450–500)</option>
                        <option value="advanced" {{ old('level', $toeflTest->level) === 'advanced' ? 'selected' : '' }}>Advanced (skor &gt; 500)</option>
                    </select>
                </div>
                <div class="flex items-center gap-3 min-h-[44px] pt-6">
                    <input id="is_trial" name="is_trial" type="checkbox" value="1"
                        class="rounded border-[color:var(--dash-input-border)] text-indigo-600 w-5 h-5 cursor-pointer"
                        {{ old('is_trial', $toeflTest->is_trial) ? 'checked' : '' }} />
                    <label for="is_trial" class="text-sm font-medium text-[color:var(--dash-text-body)] cursor-pointer select-none">
                        Test Trial <span class="text-[color:var(--dash-text-faint)] font-normal">(akses publik tanpa login member)</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-[color:var(--dash-card-border)] pt-5">
                <p class="text-sm font-medium text-[color:var(--dash-text-body)] mb-3">Durasi per Section (menit)</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-[color:var(--dash-text-muted)] mb-1">Listening</label>
                        <input name="listening_time_limit" type="number" min="1"
                            value="{{ old('listening_time_limit', $toeflTest->listening_time_limit) }}"
                            class="form-input w-full min-h-[44px]" />
                    </div>
                    <div>
                        <label class="block text-xs text-[color:var(--dash-text-muted)] mb-1">Structure</label>
                        <input name="structure_time_limit" type="number" min="1"
                            value="{{ old('structure_time_limit', $toeflTest->structure_time_limit) }}"
                            class="form-input w-full min-h-[44px]" />
                    </div>
                    <div>
                        <label class="block text-xs text-[color:var(--dash-text-muted)] mb-1">Reading</label>
                        <input name="reading_time_limit" type="number" min="1"
                            value="{{ old('reading_time_limit', $toeflTest->reading_time_limit) }}"
                            class="form-input w-full min-h-[44px]" />
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 mt-2 border-t border-[color:var(--dash-card-border)]">
                <a href="{{ route('toefl-tests.show', $toeflTest) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] hover:bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)] text-sm font-medium transition-colors">Batal</a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
