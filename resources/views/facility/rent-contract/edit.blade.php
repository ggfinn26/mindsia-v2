@extends('layouts.dashboard')

@section('title', 'Edit Kontrak Sewa')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('facility.rent-contracts.show', $branchRentContract) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Detail Kontrak
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Fasilitas</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Kontrak Sewa</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui data pemilik, status operasional, dan catatan kontrak sewa cabang.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <div class="flex items-center gap-2 mb-1.5 font-semibold">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Terdapat kesalahan pada formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('facility.rent-contracts.update', $branchRentContract) }}"
          class="space-y-6"
          x-data="editRentContractForm()">
        @csrf
        @method('PUT')

        {{-- Section 1: Informasi Kontrak --}}
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Kontrak</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Detail data pemilik dan cabang.</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Nama Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="owner_name"
                               value="{{ old('owner_name', $branchRentContract->owner_name) }}"
                               required maxlength="150"
                               placeholder="Nama lengkap pemilik properti"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('owner_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Nomor Telepon Pemilik
                        </label>
                        <div class="flex rounded-lg overflow-hidden"
                             style="border: 1px solid var(--dash-input-border);">
                            <span class="inline-flex items-center px-3 text-sm font-semibold"
                                  style="background-color: var(--dash-nav-hover-bg); border-right: 1px solid var(--dash-input-border); color: var(--dash-text-muted);">
                                +62
                            </span>
                            <input type="text" name="owner_phone"
                                   value="{{ old('owner_phone', $branchRentContract->owner_phone) }}"
                                   placeholder="8123456789"
                                   minlength="8" maxlength="15"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                                   class="w-full px-3.5 py-2.5 text-sm focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                                   onfocus="this.parentElement.style.borderColor='var(--dash-primary)'; this.parentElement.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.parentElement.style.borderColor='var(--dash-input-border)'; this.parentElement.style.boxShadow='none'">
                        </div>
                        @error('owner_phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Cabang
                    </label>
                    <select name="branch_id"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        @foreach (\App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get() as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $branchRentContract->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Tanggal Mulai
                        </label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date', $branchRentContract->start_date?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('start_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Tanggal Selesai
                        </label>
                        <input type="date" name="end_date"
                               value="{{ old('end_date', $branchRentContract->end_date?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('end_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Pembayaran, Termin, dan Status --}}
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Pembayaran & Status Kontrak</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Nilai sewa tercatat dan status keaktifan kontrak.</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Total Nilai Sewa --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Total Nilai Sewa
                        </label>
                        <div class="flex rounded-lg overflow-hidden"
                             style="border: 1px solid var(--dash-input-border);">
                            <span class="inline-flex items-center px-3 text-sm font-semibold"
                                  style="background-color: var(--dash-nav-hover-bg); border-right: 1px solid var(--dash-input-border); color: var(--dash-text-muted);">
                                Rp
                            </span>
                            <input type="text" :value="formatRupiah(rentAmount)"
                                   @input="rentAmount = parseNumber($event.target.value); $event.target.value = formatRupiah(rentAmount)"
                                   placeholder="0"
                                   class="w-full px-3.5 py-2.5 text-sm focus:outline-none transition-all font-semibold"
                                   style="background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                                   onfocus="this.parentElement.style.borderColor='var(--dash-primary)'; this.parentElement.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.parentElement.style.borderColor='var(--dash-input-border)'; this.parentElement.style.boxShadow='none'">
                        </div>
                        <input type="hidden" name="rent_amount" :value="rentAmount">
                        @error('rent_amount')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Down Payment --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Down Payment (Uang Muka)
                        </label>
                        <div class="flex rounded-lg overflow-hidden"
                             style="border: 1px solid var(--dash-input-border);">
                            <span class="inline-flex items-center px-3 text-sm font-semibold"
                                  style="background-color: var(--dash-nav-hover-bg); border-right: 1px solid var(--dash-input-border); color: var(--dash-text-muted);">
                                Rp
                            </span>
                            <input type="text" :value="formatRupiah(downPayment)"
                                   @input="downPayment = parseNumber($event.target.value); $event.target.value = formatRupiah(downPayment)"
                                   placeholder="0"
                                   class="w-full px-3.5 py-2.5 text-sm focus:outline-none transition-all font-semibold"
                                   style="background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                                   onfocus="this.parentElement.style.borderColor='var(--dash-primary)'; this.parentElement.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.parentElement.style.borderColor='var(--dash-input-border)'; this.parentElement.style.boxShadow='none'">
                        </div>
                        <input type="hidden" name="down_payment" :value="downPayment">
                        @error('down_payment')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Jumlah Termin --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Jumlah Termin
                        </label>
                        <input type="number" name="termin_count" min="1"
                               value="{{ old('termin_count', $branchRentContract->termin_count) }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('termin_count')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Periode Sewa --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Periode Sewa
                        </label>
                        <select name="rent_period"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="monthly" {{ old('rent_period', $branchRentContract->rent_period) === 'monthly' ? 'selected' : '' }}>Bulanan (Monthly)</option>
                            <option value="yearly" {{ old('rent_period', $branchRentContract->rent_period) === 'yearly' ? 'selected' : '' }}>Tahunan (Yearly)</option>
                            <option value="one_time" {{ old('rent_period', $branchRentContract->rent_period) === 'one_time' ? 'selected' : '' }}>Sekali Bayar (One Time)</option>
                        </select>
                        @error('rent_period')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Status Kontrak <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="active" {{ old('status', $branchRentContract->status) === 'active' ? 'selected' : '' }}>Active (Aktif)</option>
                            <option value="expired" {{ old('status', $branchRentContract->status) === 'expired' ? 'selected' : '' }}>Expired (Kedaluwarsa)</option>
                            <option value="terminated" {{ old('status', $branchRentContract->status) === 'terminated' ? 'selected' : '' }}>Terminated (Dihentikan)</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Catatan --}}
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Catatan Tambahan</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Keterangan khusus terkait perubahan kontrak atau perjanjian.</p>
            </div>
            <div class="px-6 py-5">
                <textarea name="notes" rows="3"
                          placeholder="Tuliskan catatan tambahan jika ada..."
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('notes', $branchRentContract->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('facility.rent-contracts.show', $branchRentContract) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>

<script>
function editRentContractForm() {
    return {
        rentAmount: {{ (int) old('rent_amount', $branchRentContract->rent_amount) }},
        downPayment: {{ (int) old('down_payment', $branchRentContract->down_payment) }},

        parseNumber(val) {
            let num = String(val).replace(/[^0-9]/g, '');
            return num ? parseInt(num, 10) : 0;
        },

        formatRupiah(val) {
            let num = parseInt(val, 10) || 0;
            return new Intl.NumberFormat('id-ID').format(num);
        }
    };
}
</script>
@endsection