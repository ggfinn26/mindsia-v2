@extends('layouts.dashboard')

@section('title', 'Pre-Test Placement - ' . $classroom->class_name)
@section('header_title', 'Pre-Test Placement')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('classrooms.show', $classroom) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Detail Kelas
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Akademik</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Pre-Test Placement</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            {{ $classroom->class_name }} &middot; {{ $classroom->program?->program_name }} &middot; {{ $classroom->branch?->branch_name }}
        </p>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Schedule Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">calendar_month</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Jadwal Pre-Test</h2>
        </div>
        <div class="p-6">
            @if ($classroom->pre_test_date)
                <p class="text-sm mb-4" style="color: var(--dash-text-body);">
                    Terjadwal:
                    <span class="font-semibold" style="color: var(--dash-text-heading);">
                        {{ $classroom->pre_test_date->translatedFormat('d F Y') }}
                    </span>
                </p>
            @else
                <p class="text-sm mb-4" style="color: var(--dash-text-muted);">Belum dijadwalkan.</p>
            @endif

            <form action="{{ route('classrooms.placement-test.schedule', $classroom) }}" method="POST"
                  class="flex flex-col sm:flex-row sm:items-end gap-4">
                @csrf
                <div class="flex-1 max-w-sm">
                    <label for="pre_test_date" class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        {{ $classroom->pre_test_date ? 'Ubah Tanggal' : 'Jadwalkan' }}
                    </label>
                    <input type="date" id="pre_test_date" name="pre_test_date" required
                           value="{{ old('pre_test_date', $classroom->pre_test_date?->format('Y-m-d')) }}"
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('pre_test_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">event_available</span>
                    Simpan Jadwal
                </button>
            </form>
        </div>
    </div>

    {{-- Score Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">edit_note</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Input Hasil Pre-Test</h2>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium"
                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                {{ $classroom->memberClasses->count() }} member
            </span>
        </div>

        @php
            $members = $classroom->memberClasses->filter(
                fn ($mc) => $mc->memberRegistration?->memberData !== null
            );
        @endphp

        @if ($members->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">group_off</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada member di kelas ini.</p>
            </div>
        @else
            <form action="{{ route('classrooms.placement-test.store', $classroom) }}" method="POST">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                    style="color: var(--dash-text-muted);">Nama Siswa</th>
                                <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                                    style="color: var(--dash-text-muted);">Writing (Hadir & Nilai)</th>
                                <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                                    style="color: var(--dash-text-muted);">Speaking (Hadir & Nilai)</th>
                                <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                                    style="color: var(--dash-text-muted);">Nilai Akhir</th>
                                <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                                    style="color: var(--dash-text-muted);">Level</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: var(--dash-card-border);" id="placement-table">
                            @foreach ($members as $mc)
                                @php
                                    $memberData = $mc->memberRegistration->memberData;
                                    $existing   = $tests[$memberData->id] ?? null;
                                @endphp
                                <tr data-row="{{ $memberData->id }}"
                                    style="border-bottom: 1px solid var(--dash-card-border);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <td class="px-4 py-3 whitespace-nowrap font-medium"
                                        style="color: var(--dash-text-heading);">
                                        {{ $memberData->full_name }}
                                    </td>

                                    {{-- Writing --}}
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-2">
                                            <label class="inline-flex items-center gap-1 text-xs cursor-pointer select-none" style="color: var(--dash-text-muted);" title="Hadir Writing">
                                                <input type="hidden"
                                                       name="tests[{{ $memberData->id }}][attended_writing]"
                                                       value="0" />
                                                <input type="checkbox"
                                                       name="tests[{{ $memberData->id }}][attended_writing]"
                                                       value="1"
                                                       {{ $existing?->attended_writing ? 'checked' : '' }}
                                                       class="attended-writing h-4 w-4 rounded cursor-pointer"
                                                       style="accent-color: var(--dash-primary);"
                                                       data-member="{{ $memberData->id }}" />
                                                <span>Hadir</span>
                                            </label>
                                            <input type="number"
                                                   name="tests[{{ $memberData->id }}][score_writing]"
                                                   value="{{ old("tests.{$memberData->id}.score_writing", $existing?->score_writing) }}"
                                                   min="0" max="100"
                                                   class="score-writing w-20 px-2 py-1.5 rounded-lg text-sm text-center focus:outline-none transition-all"
                                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                                   onblur="this.style.borderColor='var(--dash-input-border)'"
                                                   data-member="{{ $memberData->id }}"
                                                   placeholder="0 - 100" />
                                        </div>
                                    </td>

                                    {{-- Speaking --}}
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-2">
                                            <label class="inline-flex items-center gap-1 text-xs cursor-pointer select-none" style="color: var(--dash-text-muted);" title="Hadir Speaking">
                                                <input type="hidden"
                                                       name="tests[{{ $memberData->id }}][attended_speaking]"
                                                       value="0" />
                                                <input type="checkbox"
                                                       name="tests[{{ $memberData->id }}][attended_speaking]"
                                                       value="1"
                                                       {{ $existing?->attended_speaking ? 'checked' : '' }}
                                                       class="attended-speaking h-4 w-4 rounded cursor-pointer"
                                                       style="accent-color: var(--dash-primary);"
                                                       data-member="{{ $memberData->id }}" />
                                                <span>Hadir</span>
                                            </label>
                                            <input type="number"
                                                   name="tests[{{ $memberData->id }}][score_speaking]"
                                                   value="{{ old("tests.{$memberData->id}.score_speaking", $existing?->score_speaking) }}"
                                                   min="0" max="100"
                                                   class="score-speaking w-20 px-2 py-1.5 rounded-lg text-sm text-center focus:outline-none transition-all"
                                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                                   onblur="this.style.borderColor='var(--dash-input-border)'"
                                                   data-member="{{ $memberData->id }}"
                                                   placeholder="0 - 100" />
                                        </div>
                                    </td>

                                    {{-- Final (live preview) --}}
                                    <td class="px-4 py-3 text-center text-sm font-semibold whitespace-nowrap"
                                        style="color: var(--dash-text-heading);">
                                        <span class="final-score"
                                              data-member="{{ $memberData->id }}">
                                            {{ $existing ? number_format($existing->final_score, 1) : '-' }}
                                        </span>
                                    </td>

                                    {{-- Level badge --}}
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @php
                                            $levelStyles = [
                                                'basic'        => 'background-color: #fef9c3; color: #854d0e; border: 1px solid #fef08a;',
                                                'intermediate' => 'background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;',
                                                'advanced'     => 'background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;',
                                            ];
                                            $levelLabels = [
                                                'basic'        => 'Basic',
                                                'intermediate' => 'Intermediate',
                                                'advanced'     => 'Advanced',
                                            ];
                                        @endphp
                                        <span class="level-badge px-2.5 py-1 inline-flex text-xs font-semibold rounded-md"
                                              style="{{ $levelStyles[$existing?->level ?? ''] ?? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);' }}"
                                              data-member="{{ $memberData->id }}">
                                            {{ $levelLabels[$existing?->level ?? ''] ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 flex items-center justify-end"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Semua Hasil
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function computeLevel(score) {
        if (score <= 51) return { label: 'Basic', style: 'background-color: #fef9c3; color: #854d0e; border: 1px solid #fef08a;' };
        if (score <= 81) return { label: 'Intermediate', style: 'background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;' };
        return { label: 'Advanced', style: 'background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;' };
    }

    function recalcRow(memberId) {
        const row        = document.querySelector(`tr[data-row="${memberId}"]`);
        const chkW       = row.querySelector('.attended-writing');
        const chkS       = row.querySelector('.attended-speaking');
        const inpW       = row.querySelector('.score-writing');
        const inpS       = row.querySelector('.score-speaking');
        const finalEl    = row.querySelector('.final-score');
        const badgeEl    = row.querySelector('.level-badge');

        const attendedW  = chkW.checked;
        const attendedS  = chkS.checked;
        const scoreW     = attendedW ? (parseFloat(inpW.value) || 0) : 0;
        const scoreS     = attendedS ? (parseFloat(inpS.value) || 0) : 0;
        const divisor    = (attendedW ? 1 : 0) + (attendedS ? 1 : 0);

        if (divisor === 0) {
            finalEl.textContent = '-';
            badgeEl.textContent = '-';
            badgeEl.setAttribute('style', 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);');
            return;
        }

        const finalScore = (scoreW + scoreS) / divisor;
        const level      = computeLevel(finalScore);

        finalEl.textContent = finalScore.toFixed(1);
        badgeEl.textContent = level.label;
        badgeEl.setAttribute('style', level.style);
    }

    document.querySelectorAll('#placement-table tr[data-row]').forEach(function (row) {
        const memberId = row.dataset.row;
        const inputs   = row.querySelectorAll('input');
        inputs.forEach(function (inp) {
            inp.addEventListener('change', function () { recalcRow(memberId); });
            inp.addEventListener('input',  function () { recalcRow(memberId); });
        });
    });
}());
</script>
@endpush
