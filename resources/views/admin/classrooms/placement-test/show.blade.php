@extends('layouts.dashboard')

@section('title', 'Pre-Test — ' . $classroom->class_name)

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[color:var(--dash-text-heading)]">Pre-Test Placement</h1>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">
                {{ $classroom->class_name }}
                &middot; {{ $classroom->program?->program_name }}
                &middot; {{ $classroom->branch?->branch_name }}
            </p>
        </div>
        <a href="{{ route('classrooms.show', $classroom) }}"
           class="text-[color:var(--dash-text-body)] border border-[color:var(--dash-input-border)] px-4 py-2 rounded-lg hover:bg-[var(--dash-nav-hover-bg)] text-sm font-medium">
            Kembali ke Kelas
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    {{-- Schedule card --}}
    <div class="bg-[var(--dash-card-bg)] shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-[color:var(--dash-card-border)]">
            <h2 class="text-lg font-bold text-[color:var(--dash-text-heading)]">📅 Jadwal Pre-Test</h2>
        </div>
        <div class="px-6 py-5">
            @if ($classroom->pre_test_date)
                <p class="text-sm text-[color:var(--dash-text-body)] mb-4">
                    Terjadwal:
                    <span class="font-semibold text-[color:var(--dash-text-heading)]">
                        {{ $classroom->pre_test_date->translatedFormat('d F Y') }}
                    </span>
                </p>
            @else
                <p class="text-sm text-[color:var(--dash-text-muted)] mb-4">Belum dijadwalkan.</p>
            @endif

            <form action="{{ route('classrooms.placement-test.schedule', $classroom) }}" method="POST"
                  class="flex items-end gap-4">
                @csrf
                <div>
                    <label for="pre_test_date" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">
                        {{ $classroom->pre_test_date ? 'Ubah Tanggal' : 'Jadwalkan' }}
                    </label>
                    <input type="date" id="pre_test_date" name="pre_test_date" required
                           value="{{ old('pre_test_date', $classroom->pre_test_date?->format('Y-m-d')) }}"
                           class="px-3.5 py-2.5 border border-[color:var(--dash-card-border)] rounded-xl text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('pre_test_date')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Simpan Jadwal
                </button>
            </form>
        </div>
    </div>

    {{-- Score input --}}
    <div class="bg-[var(--dash-card-bg)] shadow sm:rounded-lg">
        <div class="px-6 py-5 border-b border-[color:var(--dash-card-border)] flex items-center justify-between">
            <h2 class="text-lg font-bold text-[color:var(--dash-text-heading)]">📝 Input Hasil Pre-Test</h2>
            <span class="text-sm text-[color:var(--dash-text-muted)]">
                {{ $classroom->memberClasses->count() }} member
            </span>
        </div>

        @php
            $members = $classroom->memberClasses->filter(
                fn ($mc) => $mc->memberRegistration?->memberData !== null
            );
        @endphp

        @if ($members->isEmpty())
            <div class="px-6 py-8 text-center text-[color:var(--dash-text-muted)] text-sm">
                Belum ada member di kelas ini.
            </div>
        @else
            <form action="{{ route('classrooms.placement-test.store', $classroom) }}" method="POST">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[color:var(--dash-card-border)]">
                        <thead class="bg-[var(--dash-nav-hover-bg)]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[color:var(--dash-text-muted)] uppercase tracking-wider w-48">Nama</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[color:var(--dash-text-muted)] uppercase tracking-wider">Hadir Writing</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[color:var(--dash-text-muted)] uppercase tracking-wider">Nilai Writing</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[color:var(--dash-text-muted)] uppercase tracking-wider">Hadir Speaking</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[color:var(--dash-text-muted)] uppercase tracking-wider">Nilai Speaking</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[color:var(--dash-text-muted)] uppercase tracking-wider">Final</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[color:var(--dash-text-muted)] uppercase tracking-wider">Level</th>
                            </tr>
                        </thead>
                        <tbody class="bg-[var(--dash-card-bg)] divide-y divide-[color:var(--dash-card-border)]" id="placement-table">
                            @foreach ($members as $mc)
                                @php
                                    $memberData = $mc->memberRegistration->memberData;
                                    $existing   = $tests[$memberData->id] ?? null;
                                @endphp
                                <tr data-row="{{ $memberData->id }}">
                                    <td class="px-6 py-4 text-sm font-medium text-[color:var(--dash-text-heading)] whitespace-nowrap">
                                        {{ $memberData->full_name }}
                                    </td>

                                    {{-- Writing --}}
                                    <td class="px-4 py-4 text-center">
                                        <input type="hidden"
                                               name="tests[{{ $memberData->id }}][attended_writing]"
                                               value="0" />
                                        <input type="checkbox"
                                               name="tests[{{ $memberData->id }}][attended_writing]"
                                               value="1"
                                               {{ $existing?->attended_writing ? 'checked' : '' }}
                                               class="attended-writing h-4 w-4 text-blue-600 rounded border-[color:var(--dash-input-border)] focus:ring-blue-500 cursor-pointer"
                                               data-member="{{ $memberData->id }}" />
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <input type="number"
                                               name="tests[{{ $memberData->id }}][score_writing]"
                                               value="{{ old("tests.{$memberData->id}.score_writing", $existing?->score_writing) }}"
                                               min="0" max="100"
                                               class="score-writing w-20 px-2 py-1.5 border border-[color:var(--dash-card-border)] rounded-lg text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               data-member="{{ $memberData->id }}"
                                               placeholder="0–100" />
                                    </td>

                                    {{-- Speaking --}}
                                    <td class="px-4 py-4 text-center">
                                        <input type="hidden"
                                               name="tests[{{ $memberData->id }}][attended_speaking]"
                                               value="0" />
                                        <input type="checkbox"
                                               name="tests[{{ $memberData->id }}][attended_speaking]"
                                               value="1"
                                               {{ $existing?->attended_speaking ? 'checked' : '' }}
                                               class="attended-speaking h-4 w-4 text-blue-600 rounded border-[color:var(--dash-input-border)] focus:ring-blue-500 cursor-pointer"
                                               data-member="{{ $memberData->id }}" />
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <input type="number"
                                               name="tests[{{ $memberData->id }}][score_speaking]"
                                               value="{{ old("tests.{$memberData->id}.score_speaking", $existing?->score_speaking) }}"
                                               min="0" max="100"
                                               class="score-speaking w-20 px-2 py-1.5 border border-[color:var(--dash-card-border)] rounded-lg text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               data-member="{{ $memberData->id }}"
                                               placeholder="0–100" />
                                    </td>

                                    {{-- Final (live preview) --}}
                                    <td class="px-4 py-4 text-center text-sm font-semibold text-[color:var(--dash-text-heading)]">
                                        <span class="final-score"
                                              data-member="{{ $memberData->id }}">
                                            {{ $existing ? number_format($existing->final_score, 1) : '—' }}
                                        </span>
                                    </td>

                                    {{-- Level badge --}}
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $levelColors = [
                                                'basic'        => 'bg-yellow-100 text-yellow-800',
                                                'intermediate' => 'bg-blue-100 text-blue-800',
                                                'advanced'     => 'bg-green-100 text-green-800',
                                            ];
                                            $levelLabels = [
                                                'basic'        => 'Basic',
                                                'intermediate' => 'Intermediate',
                                                'advanced'     => 'Advanced',
                                            ];
                                        @endphp
                                        <span class="level-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                     {{ $levelColors[$existing?->level ?? ''] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-faint)]' }}"
                                              data-member="{{ $memberData->id }}">
                                            {{ $levelLabels[$existing?->level ?? ''] ?? '—' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-[color:var(--dash-card-border)] flex justify-end">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
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
        if (score <= 51) return { label: 'Basic', cls: 'bg-yellow-100 text-yellow-800' };
        if (score <= 81) return { label: 'Intermediate', cls: 'bg-blue-100 text-blue-800' };
        return { label: 'Advanced', cls: 'bg-green-100 text-green-800' };
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
            finalEl.textContent = '—';
            badgeEl.textContent = '—';
            badgeEl.className   = 'level-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-faint)]';
            return;
        }

        const finalScore = (scoreW + scoreS) / divisor;
        const level      = computeLevel(finalScore);

        finalEl.textContent = finalScore.toFixed(1);
        badgeEl.textContent = level.label;
        badgeEl.className   = `level-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${level.cls}`;
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
