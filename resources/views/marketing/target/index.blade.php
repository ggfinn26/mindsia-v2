@extends('layouts.dashboard')

@section('title', 'Kelola Marketing Target')
@section('header_title', 'Kelola Marketing Target')

@section('content')
@php
    $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
@endphp
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto" x-data="targetPage()">

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Sales & Service</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Kelola Marketing Target</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Target kelas dan omzet per pegawai / posisi.</p>
        </div>

        {{-- Period filter --}}
        <form method="GET" action="{{ route('marketing-target.index') }}" class="flex items-center gap-2 shrink-0">
            <select name="month" onchange="this.form.submit()"
                    class="rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                @foreach ($monthNames as $num => $name)
                    <option value="{{ $num }}" {{ $month === $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" onchange="this.form.submit()"
                    class="rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                @foreach (range(now()->year - 1, now()->year + 1) as $y)
                    <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="mb-6" style="border-bottom: 1px solid var(--dash-card-border);">
        <nav class="flex gap-6">
            <button @click="tab = 'global'"
                    class="py-3 text-sm font-medium border-b-2 focus:outline-none transition-colors"
                    :style="tab === 'global'
                        ? 'border-color: var(--dash-primary); color: var(--dash-primary-text);'
                        : 'border-color: transparent; color: var(--dash-text-muted);'">
                Default Global
                <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-700 rounded">Berlaku Seterusnya</span>
            </button>
            <button @click="tab = 'position'"
                    class="py-3 text-sm font-medium border-b-2 focus:outline-none transition-colors"
                    :style="tab === 'position'
                        ? 'border-color: var(--dash-primary); color: var(--dash-primary-text);'
                        : 'border-color: transparent; color: var(--dash-text-muted);'">
                Target Per Posisi
            </button>
            <button @click="tab = 'employee'"
                    class="py-3 text-sm font-medium border-b-2 focus:outline-none transition-colors"
                    :style="tab === 'employee'
                        ? 'border-color: var(--dash-primary); color: var(--dash-primary-text);'
                        : 'border-color: transparent; color: var(--dash-text-muted);'">
                Target Per Pegawai
                <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded">Override</span>
            </button>
        </nav>
    </div>

    {{-- Tab: Default Global --}}
    <div x-show="tab === 'global'" x-cloak>
        <p class="text-sm mb-4" style="color: var(--dash-text-muted);">
            Default target berlaku untuk semua periode yang tidak memiliki target spesifik. Fallback terakhir sebelum "Auto".
        </p>
        @forelse ($positions as $position)
            @php $globalExisting = $globalDefaults[$position->id] ?? null; @endphp
            <div class="rounded-xl mb-3 px-5 py-4"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <form method="POST" action="{{ route('marketing-target.global-default') }}"
                    @submit.prevent="confirmSave($el)">
                    @csrf
                    <input type="hidden" name="position_id" value="{{ $position->id }}">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[160px]">
                            <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $position->position_name }}</p>
                            @if($globalExisting)
                                <p class="text-xs mt-0.5" style="color: var(--dash-primary);">Tersimpan — berlaku semua periode</p>
                            @else
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Belum diatur</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs mb-1" style="color: var(--dash-text-muted);">Kelas Target</label>
                            <input type="number" name="classes_target" min="0"
                                value="{{ $globalExisting?->classes_target ?? 0 }}"
                                class="rounded-lg px-3 py-2 text-sm w-28 focus:outline-none transition-colors text-center"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <div x-data="{ rawOmzet: '{{ $globalExisting?->omzet_target ?? 0 }}' }">
                            <label class="block text-xs mb-1" style="color: var(--dash-text-muted);">Omzet Target</label>
                            <input type="hidden" name="omzet_target" x-model="rawOmzet">
                            <input type="text"
                                :value="formatRupiah(rawOmzet)"
                                @input="rawOmzet = parseRupiah($event.target.value)"
                                placeholder="Rp 0"
                                class="rounded-lg px-3 py-2 text-sm w-44 focus:outline-none transition-colors"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <p class="text-sm" style="color: var(--dash-text-faint);">Tidak ada posisi ditemukan.</p>
        @endforelse
    </div>

    {{-- Tab: Target Per Posisi --}}
    <div x-show="tab === 'position'" x-cloak>
        <p class="text-sm mb-4" style="color: var(--dash-text-muted);">
            Default target untuk semua marketer dengan posisi ini di periode <strong style="color: var(--dash-text-heading);">{{ $monthNames[$month] }} {{ $year }}</strong>.
        </p>
        @forelse ($positions as $position)
            @php $existing = $positionTargets[$position->id] ?? null; @endphp
            <div class="rounded-xl mb-3 px-5 py-4"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <form method="POST" action="{{ route('marketing-target.position-default') }}"
                    @submit.prevent="confirmSave($el)">
                    @csrf
                    <input type="hidden" name="position_id" value="{{ $position->id }}">
                    <input type="hidden" name="period_month" value="{{ $month }}">
                    <input type="hidden" name="period_year" value="{{ $year }}">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[160px]">
                            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $position->position_name }}</p>
                            @if ($existing)
                                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-faint);">Diperbarui: {{ $existing->updated_at->format('d M Y') }}</p>
                            @else
                                <p class="text-[11px] mt-0.5" style="color: #d97706;">Belum diset — fallback ke auto</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Target Kelas/Bulan</label>
                            <input type="number" name="classes_target" min="0"
                                value="{{ old('classes_target', $existing?->classes_target ?? 0) }}"
                                class="w-28 rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors text-center"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <div x-data="{ rawOmzet: '{{ old('omzet_target', $existing?->omzet_target ?? 0) }}' }">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Target Omzet</label>
                            <input type="hidden" name="omzet_target" x-model="rawOmzet">
                            <input type="text"
                                :value="formatRupiah(rawOmzet)"
                                @input="rawOmzet = parseRupiah($event.target.value)"
                                placeholder="Rp 0"
                                class="w-44 rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <div class="rounded-xl px-5 py-10 text-center"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada posisi terdaftar.</p>
            </div>
        @endforelse
    </div>

    {{-- Tab: Target Per Pegawai --}}
    <div x-show="tab === 'employee'" x-cloak>
        <p class="text-sm mb-4" style="color: var(--dash-text-muted);">
            Override target individu untuk periode <strong style="color: var(--dash-text-heading);">{{ $monthNames[$month] }} {{ $year }}</strong>.
            Kosongkan jika ingin mengikuti default posisi.
        </p>

        {{-- Filter Pegawai --}}
        <form method="GET" action="{{ route('marketing-target.index') }}"
              class="mb-4 flex flex-wrap gap-3 items-end p-4 rounded-2xl"
              style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Cari Pegawai</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama pegawai..."
                       class="w-full rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'">
            </div>
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Cabang</label>
                <select name="branch_id" onchange="this.form.submit()"
                        class="rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Cabang</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-3 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">Filter</button>
                @if(request()->hasAny(['search', 'branch_id']))
                    <a href="{{ route('marketing-target.index', ['month' => $month, 'year' => $year]) }}"
                       class="px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                       style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Reset</a>
                @endif
            </div>
        </form>

        <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Posisi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Target Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Target Omzet (Rp)</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Sumber</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        @php
                            $existing = $employeeTargets[$employee->id] ?? null;
                            $positionName = $employee->currentStatus?->position?->position_name ?? '-';
                            $source = $existing ? 'employee' : ($positionTargets[$employee->currentStatus?->position_id ?? 0] ?? null ? 'position' : 'auto');
                            $sourceLabel = match($source) {
                                'employee' => ['Override', 'bg-indigo-100 text-indigo-700'],
                                'position' => ['Posisi',   'bg-blue-100 text-blue-700'],
                                default    => ['Auto',     'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-muted)]'],
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'"
                            x-data="{ rawOmzet: '{{ $existing?->omzet_target ?? '' }}' }">
                            <td class="px-4 py-3 font-medium" style="color: var(--dash-text-heading);">{{ $employee->full_name }}</td>
                            <td class="px-4 py-3 text-xs" style="color: var(--dash-text-muted);">{{ $positionName }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('marketing-target.employee') }}"
                                    @submit.prevent="confirmSave($el)" class="flex items-center gap-2 justify-center">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                    <input type="hidden" name="period_month" value="{{ $month }}">
                                    <input type="hidden" name="period_year" value="{{ $year }}">
                                    <input type="number" name="classes_target" min="0"
                                        value="{{ $existing?->classes_target ?? '' }}"
                                        placeholder="—"
                                        class="w-20 rounded-lg text-sm px-2 py-1.5 focus:outline-none transition-colors text-center"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                            </td>
                            <td class="px-4 py-3">
                                    <input type="hidden" name="omzet_target" x-model="rawOmzet">
                                    <input type="text"
                                        :value="formatRupiah(rawOmzet)"
                                        @input="rawOmzet = parseRupiah($event.target.value)"
                                        placeholder="—"
                                        class="w-40 rounded-lg text-sm px-3 py-1.5 focus:outline-none transition-colors"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $sourceLabel[1] }}">
                                    {{ $sourceLabel[0] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: var(--dash-primary);">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm" style="color: var(--dash-text-faint);">
                                Belum ada pegawai aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($employees->hasPages())
                <div class="px-4 py-3" style="border-top: 1px solid var(--dash-card-border);">
                    {{ $employees->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Confirm modal --}}
    <div x-show="confirm.open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
         @keydown.escape.window="confirm.open = false">
        <div class="rounded-2xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-4xl mb-3 block" style="color: var(--dash-primary);">save</span>
            <h3 class="text-base font-bold mb-1" style="color: var(--dash-text-heading);">Simpan Target?</h3>
            <p class="text-sm mb-5" style="color: var(--dash-text-muted);">Data target akan diperbarui untuk periode yang dipilih.</p>
            <div class="flex gap-3 justify-center">
                <button @click="confirm.open = false"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </button>
                <button @click="submitConfirmed()"
                        class="px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    Ya, Simpan
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function targetPage() {
    const params = new URLSearchParams(window.location.search);
    const initTab = (params.has('search') || params.has('branch_id') || params.has('page')) ? 'employee' : 'position';
    return {
        tab: initTab,
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
        formatRupiah(value) {
            if (value === null || value === '') return '';
            let val = value.toString().replace(/[^0-9]/g, '');
            if (!val) return '';
            return 'Rp ' + parseInt(val, 10).toLocaleString('id-ID');
        },
        parseRupiah(value) {
            return value.toString().replace(/[^0-9]/g, '');
        }
    };
}
</script>
@endsection
