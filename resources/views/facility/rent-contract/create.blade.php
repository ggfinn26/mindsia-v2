@extends('layouts.dashboard')

@section('title', 'Buat Kontrak Sewa')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('facility.rent-contracts.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kontrak Sewa Cabang
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Fasilitas</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Buat Kontrak Sewa</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Lengkapi data pemilik, durasi sewa, serta nilai dan termin pembayaran kontrak cabang.
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
    <form method="POST" action="{{ route('facility.rent-contracts.store') }}"
          class="space-y-6"
          x-data="rentContractForm()">
        @csrf

        {{-- Section 1: Informasi Kontrak --}}
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Kontrak</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Detail kepemilikan dan lokasi cabang yang disewa.</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Nama Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}"
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
                            <input type="text" name="owner_phone" value="{{ old('owner_phone') }}"
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
                        Cabang <span class="text-red-500">*</span>
                    </label>
                    <select name="branch_id" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih cabang</option>
                        @foreach (\App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get() as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
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
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" x-model="startDate" @change="recalculatePreview()"
                               required value="{{ old('start_date') }}"
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
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" required value="{{ old('end_date') }}"
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

        {{-- Section 2: Pembayaran & Skema Termin --}}
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Skema Pembayaran & Termin</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Nilai total sewa, uang muka, dan pembagian termin cicilan.</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Total Nilai Sewa --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Total Nilai Sewa <span class="text-red-500">*</span>
                        </label>
                        <div class="flex rounded-lg overflow-hidden"
                             style="border: 1px solid var(--dash-input-border);">
                            <span class="inline-flex items-center px-3 text-sm font-semibold"
                                  style="background-color: var(--dash-nav-hover-bg); border-right: 1px solid var(--dash-input-border); color: var(--dash-text-muted);">
                                Rp
                            </span>
                            <input type="text" :value="formatRupiah(rentAmount)"
                                   @input="rentAmount = parseNumber($event.target.value); $event.target.value = formatRupiah(rentAmount); recalculatePreview()"
                                   required placeholder="0"
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
                            Down Payment (Uang Muka) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex rounded-lg overflow-hidden"
                             style="border: 1px solid var(--dash-input-border);">
                            <span class="inline-flex items-center px-3 text-sm font-semibold"
                                  style="background-color: var(--dash-nav-hover-bg); border-right: 1px solid var(--dash-input-border); color: var(--dash-text-muted);">
                                Rp
                            </span>
                            <input type="text" :value="formatRupiah(downPayment)"
                                   @input="downPayment = parseNumber($event.target.value); $event.target.value = formatRupiah(downPayment); recalculatePreview()"
                                   required placeholder="0"
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Jumlah Termin --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Jumlah Termin <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="termin_count" x-model.number="terminCount" @input="recalculatePreview()"
                               required min="1"
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
                            Periode Sewa <span class="text-red-500">*</span>
                        </label>
                        <select name="rent_period" x-model="rentPeriod" @change="onPeriodChange()"
                                required
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih periode sewa</option>
                            <option value="monthly" {{ old('rent_period', 'monthly') === 'monthly' ? 'selected' : '' }}>Bulanan (Monthly)</option>
                            <option value="yearly" {{ old('rent_period') === 'yearly' ? 'selected' : '' }}>Tahunan (Yearly)</option>
                            <option value="one_time" {{ old('rent_period') === 'one_time' ? 'selected' : '' }}>Sekali Bayar (One Time)</option>
                        </select>
                        @error('rent_period')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Interactive Summary Pill / Box --}}
                <div class="p-4 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <div>
                        <p class="text-xs uppercase tracking-wider font-semibold" style="color: var(--dash-text-muted);">
                            Sisa Pokok Setelah DP
                        </p>
                        <p class="font-jakarta text-lg font-bold mt-0.5" style="color: var(--dash-text-heading);">
                            Rp <span x-text="formatRupiah(remainingPrincipal)"></span>
                        </p>
                    </div>
                    <div class="sm:text-right">
                        <p class="text-xs uppercase tracking-wider font-semibold" style="color: var(--dash-primary-text);">
                            Estimasi Cicilan per Termin
                        </p>
                        <p class="font-jakarta text-lg font-bold mt-0.5" style="color: var(--dash-primary-text);">
                            Rp <span x-text="formatRupiah(installmentPerTermin)"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Live Preview Jadwal Cicilan --}}
        <div x-show="previewRows.length > 0"
             class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 flex items-center justify-between"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div>
                    <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Preview Jadwal Cicilan</h2>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Simulasi jadwal termin yang akan di-generate otomatis oleh sistem.</p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full"
                      style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    <span x-text="previewRows.length"></span> Termin
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Termin</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Estimasi Jatuh Tempo</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Jumlah Cicilan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in previewRows" :key="row.number">
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3 font-semibold" style="color: var(--dash-text-heading);">
                                    Termin ke-<span x-text="row.number"></span>
                                </td>
                                <td class="px-5 py-3" style="color: var(--dash-text-body);" x-text="row.dueDate"></td>
                                <td class="px-5 py-3 text-right font-semibold" style="color: var(--dash-text-heading);">
                                    Rp <span x-text="formatRupiah(row.amount)"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section 4: Catatan --}}
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Catatan Tambahan</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Keterangan khusus terkait syarat pembayaran atau fasilitas sewa (opsional).</p>
            </div>
            <div class="px-6 py-5">
                <textarea name="notes" rows="3"
                          placeholder="Tuliskan catatan tambahan jika ada..."
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('facility.rent-contracts.index') }}"
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
                Simpan Kontrak
            </button>
        </div>
    </form>

</div>

<script>
function rentContractForm() {
    return {
        rentAmount: {{ (int) old('rent_amount', 0) }},
        downPayment: {{ (int) old('down_payment', 0) }},
        terminCount: {{ (int) old('termin_count', 12) }},
        rentPeriod: '{{ old('rent_period', 'monthly') }}',
        startDate: '{{ old('start_date', '') }}',
        previewRows: [],

        init() {
            this.recalculatePreview();
        },

        parseNumber(val) {
            let num = String(val).replace(/[^0-9]/g, '');
            return num ? parseInt(num, 10) : 0;
        },

        formatRupiah(val) {
            let num = parseInt(val, 10) || 0;
            return new Intl.NumberFormat('id-ID').format(num);
        },

        get remainingPrincipal() {
            let remaining = this.rentAmount - this.downPayment;
            return remaining > 0 ? remaining : 0;
        },

        get installmentPerTermin() {
            if (this.terminCount < 1) return 0;
            return Math.floor(this.remainingPrincipal / this.terminCount);
        },

        onPeriodChange() {
            if (this.rentPeriod === 'one_time') {
                this.terminCount = 1;
            }
            this.recalculatePreview();
        },

        recalculatePreview() {
            let count = parseInt(this.terminCount, 10) || 0;
            let amount = this.installmentPerTermin;

            if (count < 1 || this.remainingPrincipal <= 0) {
                this.previewRows = [];
                return;
            }

            let rows = [];
            let baseDate = this.startDate ? new Date(this.startDate) : new Date();

            for (let i = 1; i <= Math.min(count, 36); i++) {
                let dueDate = new Date(baseDate);
                if (this.rentPeriod === 'yearly') {
                    dueDate.setFullYear(dueDate.getFullYear() + i);
                } else {
                    dueDate.setMonth(dueDate.getMonth() + i);
                }

                let formattedDate = dueDate.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });

                rows.push({
                    number: i,
                    dueDate: this.startDate ? formattedDate : 'Bulan ke-' + i,
                    amount: amount
                });
            }

            this.previewRows = rows;
        }
    };
}
</script>
@endsection