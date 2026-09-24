@extends('layouts.dashboard')

@section('title', 'Kelola Marketing Target')

@section('content')
@php
    $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
@endphp
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="targetPage()">

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Kelola Marketing Target</h1>
            <p class="text-sm text-gray-400 mt-0.5">Target kelas dan omzet per pegawai / posisi.</p>
        </div>

        {{-- Period filter --}}
        <form method="GET" action="{{ route('marketing-target.index') }}" class="flex items-center gap-2">
            <select name="month" onchange="this.form.submit()"
                class="bg-gray-50 border border-gray-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 transition-colors">
                @foreach ($monthNames as $num => $name)
                    <option value="{{ $num }}" {{ $month === $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" onchange="this.form.submit()"
                class="bg-gray-50 border border-gray-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 transition-colors">
                @foreach (range(now()->year - 1, now()->year + 1) as $y)
                    <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6" aria-label="Tabs">
            <button
                @click="tab = 'position'"
                :class="tab === 'position' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-3 text-sm font-medium border-b-2 focus:outline-none transition-colors">
                Target Per Posisi
            </button>
            <button
                @click="tab = 'employee'"
                :class="tab === 'employee' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-3 text-sm font-medium border-b-2 focus:outline-none transition-colors">
                Target Per Pegawai
                <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded">Override</span>
            </button>
        </nav>
    </div>

    {{-- Tab: Target Per Posisi --}}
    <div x-show="tab === 'position'" x-cloak>
        <p class="text-sm text-gray-500 mb-4">
            Default target untuk semua marketer dengan posisi ini di periode <strong>{{ $monthNames[$month] }} {{ $year }}</strong>.
        </p>
        @forelse ($positions as $position)
            @php
                $existing = $positionTargets[$position->id] ?? null;
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-3 px-5 py-4">
                <form method="POST" action="{{ route('marketing-target.position-default') }}"
                    @submit.prevent="confirmSave($el)">
                    @csrf
                    <input type="hidden" name="position_id" value="{{ $position->id }}">
                    <input type="hidden" name="period_month" value="{{ $month }}">
                    <input type="hidden" name="period_year" value="{{ $year }}">

                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[160px]">
                            <p class="text-sm font-semibold text-gray-800">{{ $position->position_name }}</p>
                            @if ($existing)
                                <p class="text-[11px] text-gray-400 mt-0.5">Diperbarui: {{ $existing->updated_at->format('d M Y') }}</p>
                            @else
                                <p class="text-[11px] text-amber-500 mt-0.5">Belum diset — fallback ke auto</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Target Kelas/Bulan</label>
                            <input type="number" name="classes_target" min="0"
                                value="{{ old('classes_target', $existing?->classes_target ?? 0) }}"
                                class="w-28 border border-gray-200 rounded-lg text-sm px-3 py-2 bg-gray-50 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 outline-none transition-colors text-center">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Target Omzet (Rp)</label>
                            <input type="number" name="omzet_target" min="0" step="1000"
                                value="{{ old('omzet_target', $existing?->omzet_target ?? 0) }}"
                                class="w-44 border border-gray-200 rounded-lg text-sm px-3 py-2 bg-gray-50 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 outline-none transition-colors">
                        </div>
                        <div>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none transition-colors">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 px-5 py-10 text-center">
                <p class="text-sm text-gray-500">Belum ada posisi terdaftar.</p>
            </div>
        @endforelse
    </div>

    {{-- Tab: Target Per Pegawai --}}
    <div x-show="tab === 'employee'" x-cloak>
        <p class="text-sm text-gray-500 mb-4">
            Override target individu untuk periode <strong>{{ $monthNames[$month] }} {{ $year }}</strong>.
            Kosongkan jika ingin mengikuti default posisi.
        </p>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Posisi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">Target Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Target Omzet (Rp)</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Sumber</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($employees as $employee)
                        @php
                            $existing = $employeeTargets[$employee->id] ?? null;
                            $positionName = $employee->currentStatus?->position?->position_name ?? '-';
                            $source = $existing ? 'employee' : ($positionTargets[$employee->currentStatus?->position_id ?? 0] ?? null ? 'position' : 'auto');
                            $sourceLabel = match($source) {
                                'employee' => ['Override', 'bg-indigo-100 text-indigo-700'],
                                'position' => ['Posisi', 'bg-blue-100 text-blue-700'],
                                default    => ['Auto', 'bg-gray-100 text-gray-500'],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $employee->full_name }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $positionName }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('marketing-target.employee') }}"
                                    @submit.prevent="confirmSave($el)" class="flex items-center gap-2 justify-center">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                    <input type="hidden" name="period_month" value="{{ $month }}">
                                    <input type="hidden" name="period_year" value="{{ $year }}">
                                    <input type="hidden" name="omzet_target" value="{{ $existing?->omzet_target ?? 0 }}" class="omzet-input-{{ $employee->id }}">
                                    <input type="number" name="classes_target" min="0"
                                        value="{{ $existing?->classes_target ?? '' }}"
                                        placeholder="—"
                                        class="w-20 border border-gray-200 rounded-lg text-sm px-2 py-1.5 bg-gray-50 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 outline-none transition-colors text-center">
                            </td>
                            <td class="px-4 py-3">
                                    <input type="number" name="omzet_target" min="0" step="1000"
                                        value="{{ $existing?->omzet_target ?? '' }}"
                                        placeholder="—"
                                        class="w-40 border border-gray-200 rounded-lg text-sm px-3 py-1.5 bg-gray-50 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 outline-none transition-colors">
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $sourceLabel[1] }}">
                                    {{ $sourceLabel[0] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none transition-colors">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">
                                Belum ada pegawai aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Confirm modal --}}
    <div
        x-show="confirm.open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
        @keydown.escape.window="confirm.open = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop>
            <span class="material-symbols-outlined text-4xl text-indigo-500 mb-3 block">save</span>
            <h3 class="text-base font-bold text-gray-900 mb-1">Simpan Target?</h3>
            <p class="text-sm text-gray-500 mb-5">Data target akan diperbarui untuk periode yang dipilih.</p>
            <div class="flex gap-3 justify-center">
                <button @click="confirm.open = false"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none">
                    Batal
                </button>
                <button @click="submitConfirmed()"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none">
                    Ya, Simpan
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function targetPage() {
    return {
        tab: 'position',
        confirm: { open: false, form: null },
        confirmSave(form) {
            this.confirm.form = form;
            this.confirm.open = true;
        },
        submitConfirmed() {
            this.confirm.open = false;
            if (this.confirm.form) {
                this.confirm.form.submit();
            }
        },
    };
}
</script>
@endsection
