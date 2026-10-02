@extends('layouts.dashboard')

@section('title', 'Detail Diskon - ' . $discount->discount_code)
@section('header_title', 'Detail Diskon')

@section('content')
@php
    $usedCount = $discount->registrations->count();
    $remaining = $discount->discount_quota ? max(0, $discount->discount_quota - $usedCount) : null;
    $assignedProgramIds = $discount->programs->pluck('program_id')->toArray();
@endphp

<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('discounts.index') }}"
           class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
            <span class="material-symbols-outlined text-[20px] mr-1">arrow_back</span>
            Kembali ke Daftar Diskon
        </a>
        @can('member.manage')
        <div class="flex items-center gap-2">
            <form action="{{ route('discounts.toggle-active', $discount) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg border font-medium transition-colors
                            {{ $discount->is_active
                                ? 'border-orange-200 text-orange-700 bg-orange-50 hover:bg-orange-100'
                                : 'border-green-200 text-green-700 bg-green-50 hover:bg-green-100' }}">
                    <span class="material-symbols-outlined text-[16px]">{{ $discount->is_active ? 'pause_circle' : 'play_circle' }}</span>
                    {{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            </form>
            <form id="delete-form" action="{{ route('discounts.destroy', $discount) }}" method="POST">
                @csrf @method('DELETE')
                <button type="button" onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-600 bg-red-50 hover:bg-red-100 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    Hapus
                </button>
            </form>
        </div>
        @endcan
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Info + Edit --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="font-mono text-xl font-bold text-gray-900">{{ $discount->discount_code }}</span>
                @if ($discount->is_active)
                    <span class="inline-flex items-center gap-1 text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full text-xs font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-gray-500 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded-full text-xs font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif
                    </span>
                @endif
            </div>
            @can('member.manage')
            <button onclick="document.getElementById('edit-section').classList.toggle('hidden')"
                    class="text-sm text-[#5586DB] hover:underline font-medium">Edit</button>
            @endcan
        </div>

        <div class="px-6 py-5 grid grid-cols-2 md:grid-cols-4 gap-5">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Tipe</p>
                <p class="font-medium text-gray-900">
                    {{ $discount->discount_type === 'percentage' ? 'Persentase' : 'Fixed (Rp)' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Nilai Potongan</p>
                <p class="font-semibold text-gray-900 text-lg">
                    @if ($discount->discount_type === 'percentage')
                        {{ $discount->discount_nominal }}%
                    @else
                        Rp {{ number_format($discount->discount_nominal, 0, ',', '.') }}
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Kuota Slot</p>
                @if ($discount->discount_quota)
                    <p class="font-semibold text-gray-900">{{ $usedCount }} / {{ $discount->discount_quota }} terpakai</p>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1.5">
                        <div class="h-1.5 rounded-full {{ $remaining === 0 ? 'bg-red-500' : 'bg-[#5586DB]' }}"
                             style="width: {{ min(100, round($usedCount / $discount->discount_quota * 100)) }}%"></div>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Tidak terbatas</p>
                    @if ($usedCount > 0)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $usedCount }} sudah pakai</p>
                    @endif
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Periode Valid</p>
                @if ($discount->starts_at || $discount->expired_at)
                    <p class="text-sm text-gray-700">
                        {{ $discount->starts_at?->format('d M Y') ?? '—' }}
                        <span class="text-gray-400 mx-1">s/d</span>
                        {{ $discount->expired_at?->format('d M Y') ?? '—' }}
                    </p>
                @else
                    <p class="text-gray-500 text-sm">Tidak ada batas waktu</p>
                @endif
            </div>
        </div>

        @can('member.manage')
        <div id="edit-section" class="hidden px-6 pb-5 border-t border-gray-100 pt-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-4">Edit Diskon</h4>
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm mb-4">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Diskon <span class="text-red-500">*</span></label>
                        <input type="text" name="discount_code"
                               value="{{ old('discount_code', $discount->discount_code) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono uppercase focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Diskon <span class="text-red-500">*</span></label>
                        <select name="discount_type" data-no-enhance
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20">
                            <option value="percentage" {{ old('discount_type', $discount->discount_type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed_amount" {{ old('discount_type', $discount->discount_type) === 'fixed_amount' ? 'selected' : '' }}>Fixed (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai <span class="text-red-500">*</span></label>
                        <input type="number" name="discount_nominal" min="0"
                               value="{{ old('discount_nominal', $discount->discount_nominal) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Slot</label>
                        <input type="number" name="discount_quota" min="0"
                               value="{{ old('discount_quota', $discount->discount_quota) }}"
                               placeholder="Kosongkan = tidak terbatas"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Mulai</label>
                        <input type="date" name="starts_at"
                               value="{{ old('starts_at', $discount->starts_at?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai</label>
                        <input type="date" name="expired_at"
                               value="{{ old('expired_at', $discount->expired_at?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('edit-section').classList.add('hidden')"
                            class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-[#5586DB] text-white rounded-lg hover:bg-[#4475ca] font-medium">Simpan Perubahan</button>
                </div>
            </form>
        </div>
        @endcan
    </div>

    {{-- Program Eligibility --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Eligibilitas Program</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Pilih program yang bisa memakai diskon ini.
                Jika tidak ada yang dipilih, diskon berlaku untuk semua program.
            </p>
        </div>
        <div class="px-6 py-5">
            @if ($programs->isEmpty())
                <p class="text-sm text-gray-400">Belum ada program aktif.</p>
            @else
                @can('member.manage')
                <form action="{{ route('discounts.programs.sync', $discount) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-2">
                        @foreach ($programs as $program)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="program_ids[]" value="{{ $program->id }}"
                                   {{ in_array($program->id, $assignedProgramIds) ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#5586DB] rounded border-gray-300 focus:ring-[#5586DB]" />
                            <span class="text-sm font-medium text-gray-800">{{ $program->program_name }}</span>
                            <span class="text-xs text-gray-400 font-mono ml-auto">{{ $program->program_code }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-xs text-gray-400">
                            @if (count($assignedProgramIds) === 0)
                                Berlaku untuk semua program
                            @else
                                {{ count($assignedProgramIds) }} program dipilih
                            @endif
                        </p>
                        <button type="submit"
                                class="px-4 py-2 text-sm bg-[#5586DB] text-white rounded-lg hover:bg-[#4475ca] font-medium">
                            Simpan Eligibilitas
                        </button>
                    </div>
                </form>
                @else
                <div class="space-y-2">
                    @foreach ($programs as $program)
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100">
                        <span class="w-4 h-4 flex items-center justify-center">
                            @if (in_array($program->id, $assignedProgramIds))
                                <span class="material-symbols-outlined text-[16px] text-green-600">check_circle</span>
                            @else
                                <span class="material-symbols-outlined text-[16px] text-gray-300">radio_button_unchecked</span>
                            @endif
                        </span>
                        <span class="text-sm text-gray-800">{{ $program->program_name }}</span>
                        <span class="text-xs text-gray-400 font-mono ml-auto">{{ $program->program_code }}</span>
                    </div>
                    @endforeach
                </div>
                @endcan
            @endif
        </div>
    </div>

    {{-- Usage History --}}
    @if ($discount->registrations->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Riwayat Penggunaan</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $discount->registrations->count() }} registrasi menggunakan diskon ini</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Member</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. Registrasi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($discount->registrations->take(20) as $reg)
                    <tr>
                        <td class="px-5 py-3 text-gray-700">{{ $reg->memberData?->full_name ?? '—' }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $reg->registration_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $reg->created_at?->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                    @if ($discount->registrations->count() > 20)
                    <tr>
                        <td colspan="3" class="px-5 py-3 text-center text-xs text-gray-400">
                            + {{ $discount->registrations->count() - 20 }} registrasi lainnya
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

@if ($errors->any())
<script>document.getElementById('edit-section')?.classList.remove('hidden');</script>
@endif

<!-- Delete Confirm Modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full mx-4 p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-red-600 text-[20px]">delete</span>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 text-base">Hapus Diskon</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Hapus <span class="font-mono font-semibold text-gray-800">{{ $discount->discount_code }}</span>?
                    Diskon yang sudah dipakai dalam registrasi tidak bisa dihapus.
                </p>
            </div>
        </div>
        <div class="flex gap-3 mt-5 justify-end">
            <button onclick="document.getElementById('delete-modal').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
            <button onclick="document.getElementById('delete-form').submit()"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Ya, Hapus</button>
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
