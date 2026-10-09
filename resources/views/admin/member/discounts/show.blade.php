@extends('layouts.dashboard')

@section('title', 'Detail Diskon - ' . $discount->discount_code)
@section('header_title', 'Detail Diskon')

@section('content')
@php
    $usedCount = $discount->registrations->count();
    $remaining = $discount->discount_quota ? max(0, $discount->discount_quota - $usedCount) : null;
    $assignedProgramIds = $discount->programs->pluck('program_id')->toArray();
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('discounts.index') }}"
               class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider mb-2 hover:opacity-70 transition-opacity"
               style="color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Member & Marketing
            </a>
            <div class="flex items-center gap-3">
                <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight font-mono" style="color: var(--dash-text-heading);">{{ $discount->discount_code }}</h1>
                @if ($discount->is_active)
                    <span class="inline-flex items-center gap-1 text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                          style="color: var(--dash-text-muted); background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--dash-text-muted);"></span> Nonaktif
                    </span>
                @endif
            </div>
        </div>
        @can('member.manage')
        <div class="flex items-center gap-2 shrink-0">
            <form action="{{ route('discounts.toggle-active', $discount) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-lg transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: {{ $discount->is_active ? '#ea580c' : '#16a34a' }};"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">{{ $discount->is_active ? 'pause_circle' : 'play_circle' }}</span>
                    {{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            </form>
            <form id="delete-form" action="{{ route('discounts.destroy', $discount) }}" method="POST">
                @csrf @method('DELETE')
                <button type="button" onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    Hapus
                </button>
            </form>
        </div>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Info + Edit --}}
    <div class="rounded-2xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Diskon</h2>
            @can('member.manage')
            <button onclick="document.getElementById('edit-section').classList.toggle('hidden')"
                    class="inline-flex items-center gap-1 text-sm font-medium hover:opacity-70 transition-opacity" style="color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">edit</span> Edit
            </button>
            @endcan
        </div>

        <div class="px-6 py-5 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Tipe</p>
                <p class="font-medium" style="color: var(--dash-text-heading);">
                    {{ $discount->discount_type === 'percentage' ? 'Persentase' : 'Fixed (Rp)' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Nilai Potongan</p>
                <p class="font-bold text-lg" style="color: var(--dash-text-heading);">
                    @if ($discount->discount_type === 'percentage')
                        {{ $discount->discount_nominal }}%
                    @else
                        Rp {{ number_format($discount->discount_nominal, 0, ',', '.') }}
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Kuota Slot</p>
                @if ($discount->discount_quota)
                    <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $usedCount }} / {{ $discount->discount_quota }} terpakai</p>
                    <div class="w-full rounded-full h-1.5 mt-2" style="background-color: var(--dash-card-border);">
                        <div class="h-1.5 rounded-full"
                             style="width: {{ min(100, round($usedCount / $discount->discount_quota * 100)) }}%; background-color: {{ $remaining === 0 ? '#dc2626' : 'var(--dash-primary)' }};"></div>
                    </div>
                @else
                    <p class="text-sm" style="color: var(--dash-text-muted);">Tidak terbatas</p>
                    @if ($usedCount > 0)
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">{{ $usedCount }} sudah pakai</p>
                    @endif
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Periode Valid</p>
                @if ($discount->starts_at || $discount->expired_at)
                    <p class="text-sm" style="color: var(--dash-text-body);">
                        {{ $discount->starts_at?->format('d M Y') ?? '—' }}
                        <span style="color: var(--dash-text-faint);" class="mx-1">s/d</span>
                        {{ $discount->expired_at?->format('d M Y') ?? '—' }}
                    </p>
                @else
                    <p class="text-sm" style="color: var(--dash-text-muted);">Tidak ada batas waktu</p>
                @endif
            </div>
        </div>

        @can('member.manage')
        <div id="edit-section" class="hidden px-6 pb-6 pt-4" style="border-top: 1px solid var(--dash-card-border);">
            <h4 class="text-sm font-semibold mb-4" style="color: var(--dash-text-body);">Edit Diskon</h4>
            @if ($errors->any())
                <div class="rounded-lg px-3 py-2 text-sm mb-4"
                     style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('discounts.update', $discount) }}" method="POST" class="space-y-4" novalidate>
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Kode Diskon <span class="text-red-500">*</span></label>
                        <input type="text" name="discount_code"
                               value="{{ old('discount_code', $discount->discount_code) }}"
                               class="w-full px-3 py-2 rounded-lg text-sm font-mono uppercase focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Tipe Diskon <span class="text-red-500">*</span></label>
                        <select name="discount_type" data-no-enhance
                                class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="percentage" {{ old('discount_type', $discount->discount_type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed_amount" {{ old('discount_type', $discount->discount_type) === 'fixed_amount' ? 'selected' : '' }}>Fixed (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Nilai <span class="text-red-500">*</span></label>
                        <input type="number" name="discount_nominal" min="0"
                               value="{{ old('discount_nominal', $discount->discount_nominal) }}"
                               class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Kuota Slot</label>
                        <input type="number" name="discount_quota" min="0"
                               value="{{ old('discount_quota', $discount->discount_quota) }}"
                               placeholder="Kosongkan = tidak terbatas"
                               class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Berlaku Mulai</label>
                        <input type="date" name="starts_at"
                               value="{{ old('starts_at', $discount->starts_at?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Berlaku Sampai</label>
                        <input type="date" name="expired_at"
                               value="{{ old('expired_at', $discount->expired_at?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('edit-section').classList.add('hidden')"
                            class="px-4 py-2 text-sm rounded-lg transition-colors inline-flex items-center gap-1.5"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    <button type="submit"
                            class="px-4 py-2 text-sm text-white rounded-lg hover:opacity-90 font-semibold transition-opacity inline-flex items-center gap-1.5"
                            style="background-color: var(--dash-primary);"><span class="material-symbols-outlined text-[16px]">save</span>Simpan Perubahan</button>
                </div>
            </form>
        </div>
        @endcan
    </div>

    {{-- Program Eligibility --}}
    <div class="rounded-2xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-semibold" style="color: var(--dash-text-heading);">Eligibilitas Program</h2>
            <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">
                Pilih program yang bisa memakai diskon ini.
                Jika tidak ada yang dipilih, diskon berlaku untuk semua program.
            </p>
        </div>
        <div class="px-6 py-5">
            @if ($programs->isEmpty())
                <p class="text-sm" style="color: var(--dash-text-faint);">Belum ada program aktif.</p>
            @else
                @can('member.manage')
                <form action="{{ route('discounts.programs.sync', $discount) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-2">
                        @foreach ($programs as $program)
                        <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-colors"
                               style="border: 1px solid var(--dash-card-border);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='transparent'">
                            <input type="checkbox" name="program_ids[]" value="{{ $program->id }}"
                                   {{ in_array($program->id, $assignedProgramIds) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-[color:var(--dash-input-border)]"
                                   style="accent-color: var(--dash-primary);" />
                            <span class="text-sm font-medium" style="color: var(--dash-text-body);">{{ $program->program_name }}</span>
                            <span class="text-xs font-mono ml-auto" style="color: var(--dash-text-faint);">{{ $program->program_code }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-xs" style="color: var(--dash-text-faint);">
                            @if (count($assignedProgramIds) === 0)
                                Berlaku untuk semua program
                            @else
                                {{ count($assignedProgramIds) }} program dipilih
                            @endif
                        </p>
                        <button type="submit"
                                class="px-4 py-2 text-sm text-white rounded-lg hover:opacity-90 font-semibold transition-opacity inline-flex items-center gap-1.5"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan Eligibilitas
                        </button>
                    </div>
                </form>
                @else
                <div class="space-y-2">
                    @foreach ($programs as $program)
                    <div class="flex items-center gap-3 p-3 rounded-xl"
                         style="border: 1px solid var(--dash-card-border);">
                        <span class="w-4 h-4 flex items-center justify-center">
                            @if (in_array($program->id, $assignedProgramIds))
                                <span class="material-symbols-outlined text-[16px] text-green-600">check_circle</span>
                            @else
                                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">radio_button_unchecked</span>
                            @endif
                        </span>
                        <span class="text-sm" style="color: var(--dash-text-body);">{{ $program->program_name }}</span>
                        <span class="text-xs font-mono ml-auto" style="color: var(--dash-text-faint);">{{ $program->program_code }}</span>
                    </div>
                    @endforeach
                </div>
                @endcan
            @endif
        </div>
    </div>

    {{-- Usage History --}}
    @if ($discount->registrations->count() > 0)
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-semibold" style="color: var(--dash-text-heading);">Riwayat Penggunaan</h2>
            <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">{{ $discount->registrations->count() }} registrasi menggunakan diskon ini</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Member</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">No. Registrasi</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($discount->registrations->take(20) as $reg)
                <tr style="border-bottom: 1px solid var(--dash-card-border);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <td class="px-5 py-3" style="color: var(--dash-text-body);">{{ $reg->memberData?->full_name ?? '—' }}</td>
                    <td class="px-5 py-3 font-mono text-xs" style="color: var(--dash-text-muted);">{{ $reg->registration_number ?? '—' }}</td>
                    <td class="px-5 py-3 text-xs" style="color: var(--dash-text-muted);">{{ $reg->created_at?->format('d M Y') }}</td>
                </tr>
                @endforeach
                @if ($discount->registrations->count() > 20)
                <tr>
                    <td colspan="3" class="px-5 py-3 text-center text-xs" style="color: var(--dash-text-faint);">
                        + {{ $discount->registrations->count() - 20 }} registrasi lainnya
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

</div>

@if ($errors->any())
<script>document.getElementById('edit-section')?.classList.remove('hidden');</script>
@endif

<!-- Delete Confirm Modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="rounded-2xl shadow-xl max-w-sm w-full mx-4 p-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-red-600 text-[20px]">delete</span>
            </div>
            <div>
                <h3 class="font-semibold text-base" style="color: var(--dash-text-heading);">Hapus Diskon</h3>
                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                    Hapus <span class="font-mono font-semibold" style="color: var(--dash-text-heading);">{{ $discount->discount_code }}</span>?
                    Diskon yang sudah dipakai dalam registrasi tidak bisa dihapus.
                </p>
            </div>
        </div>
        <div class="flex gap-3 mt-5 justify-end">
            <button onclick="document.getElementById('delete-modal').classList.add('hidden')"
                    class="px-4 py-2 text-sm rounded-lg transition-colors inline-flex items-center gap-1.5"
                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
            <button onclick="document.getElementById('delete-form').submit()"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">delete</span>Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') document.getElementById('delete-modal')?.classList.add('hidden');
});
</script>
@endsection
