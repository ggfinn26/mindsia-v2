@extends('layouts.dashboard')

@section('title', 'Registrasi Program Member - MINDSIA')
@section('header_title', 'Registrasi Program Member')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('members.show', $member) }}" class="dash-back mb-3 inline-flex items-center gap-1.5 text-xs font-semibold">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Detail Member
        </a>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Registrasi Program Member</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Pilih paket dan skema pembayaran program untuk {{ $member->full_name }}.</p>
        </div>
    </div>

    {{-- Error validation banner --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <div class="flex items-center gap-2 mb-2 font-semibold">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
         x-data="{
             programId: '',
             plans: [],
             selectedPlan: null,
             loading: false,
             init() {
                 const old = '{{ old('program_id') }}';
                 if (old) { this.programId = old; this.fetchPlans(old); }
             },
             fetchPlans(id) {
                 if (!id) { this.plans = []; this.selectedPlan = null; return; }
                 this.loading = true;
                 this.selectedPlan = null;
                 fetch('/api/public/programs/' + id + '/payment-plans')
                     .then(r => r.json())
                     .then(data => { this.plans = data; this.loading = false; })
                     .catch(() => { this.plans = []; this.loading = false; });
             },
             selectPlan(id) {
                 this.selectedPlan = this.plans.find(p => p.id == id) || null;
             },
             formatRp(val) {
                 if (val === null || val === undefined) return '-';
                 return 'Rp ' + Number(val).toLocaleString('id-ID');
             },
             planLabel(plan) {
                 if (!plan) return '';
                 return plan.type === 'FULL'
                     ? 'Lunas - ' + this.formatRp(plan.dp_amount)
                     : 'Cicilan ' + plan.installment_count + 'x - DP ' + this.formatRp(plan.dp_amount) + ' + ' + this.formatRp(plan.per_installment_amount) + '/bulan';
             }
         }">
        <div class="px-6 py-4 rounded-t-2xl border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Formulir Pendaftaran Program</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Pilih program pelatihan dan metode rencana pembayaran</p>
        </div>

        <form action="{{ route('members.registrations.store', $member) }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Program Selector --}}
            <div>
                <label for="program_id" class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Program / Kursus <span class="text-red-500">*</span>
                </label>
                <select id="program_id" name="program_id" required
                        x-model="programId"
                        @change="fetchPlans($event.target.value)"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih Program</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Payment Plan Selector --}}
            <div x-show="programId" x-cloak>
                <label for="payment_plan_id" class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Paket Pembayaran <span class="text-red-500">*</span>
                </label>
                <div x-show="loading" class="text-xs py-2" style="color: var(--dash-text-muted);">Memuat paket...</div>
                <select id="payment_plan_id" name="payment_plan_id"
                        x-show="!loading"
                        @change="selectPlan($event.target.value)"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih Paket Pembayaran</option>
                    <template x-for="plan in plans" :key="plan.id">
                        <option :value="plan.id" x-text="plan.label + ' (' + (plan.type === 'FULL' ? 'Lunas' : 'Cicilan') + ')'"></option>
                    </template>
                </select>
                <p x-show="!loading && plans.length === 0" class="text-xs mt-1" style="color: var(--dash-text-faint);">
                    Tidak ada paket aktif untuk program ini.
                </p>
                @error('payment_plan_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror

                {{-- Plan Detail Card --}}
                <div x-show="selectedPlan"
                     class="mt-3 rounded-xl p-4 text-xs space-y-2 border"
                     style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                    <p class="font-bold text-sm" style="color: var(--dash-text-heading);" x-text="selectedPlan?.label"></p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1.5 pt-1">
                        <div>
                            <span style="color: var(--dash-text-muted);">Tipe:</span>
                            <span class="font-semibold ml-1" style="color: var(--dash-text-heading);" x-text="selectedPlan?.type === 'FULL' ? 'Lunas' : 'Cicilan'"></span>
                        </div>
                        <div x-show="selectedPlan?.installment_count">
                            <span style="color: var(--dash-text-muted);">Jumlah cicilan:</span>
                            <span class="font-semibold ml-1" style="color: var(--dash-text-heading);" x-text="selectedPlan?.installment_count + 'x'"></span>
                        </div>
                        <div x-show="selectedPlan?.dp_amount">
                            <span style="color: var(--dash-text-muted);">DP / Bayar pertama:</span>
                            <span class="font-semibold ml-1 font-mono" style="color: var(--dash-text-heading);" x-text="formatRp(selectedPlan?.dp_amount)"></span>
                        </div>
                        <div x-show="selectedPlan?.per_installment_amount">
                            <span style="color: var(--dash-text-muted);">Per cicilan:</span>
                            <span class="font-semibold ml-1 font-mono" style="color: var(--dash-text-heading);" x-text="formatRp(selectedPlan?.per_installment_amount)"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Discount Code --}}
            <div>
                <label for="discount_code" class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Kode Diskon <span style="color: var(--dash-text-faint);">(Opsional)</span>
                </label>
                <input type="text" id="discount_code" name="discount_code"
                       value="{{ old('discount_code') }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all uppercase font-mono"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                       placeholder="Contoh: PROMO10" />
                @error('discount_code')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t" style="border-color: var(--dash-card-border);">
                <a href="{{ route('members.show', $member) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    Daftarkan Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
