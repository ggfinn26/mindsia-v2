@extends('layouts.dashboard')

@section('title', 'Registrasi Member Baru')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('members.show', $member) }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight text-[var(--dash-text-heading)] truncate">Registrasi Member Baru</h1>
            <p class="text-sm text-[var(--dash-text-muted)] mt-1 truncate">{{ $member->full_name }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[var(--dash-card-border)] overflow-hidden"
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
                     ? 'Lunas — ' + this.formatRp(plan.dp_amount)
                     : 'Cicilan ' + plan.installment_count + 'x — DP ' + this.formatRp(plan.dp_amount) + ' + ' + this.formatRp(plan.per_installment_amount) + '/bulan';
             }
         }">
        <form action="{{ route('members.registrations.store', $member) }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Program Selector -->
            <div>
                <label for="program_id" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                    Program / Course <span class="text-red-500">*</span>
                </label>
                <select id="program_id" name="program_id" required
                        x-model="programId"
                        @change="fetchPlans($event.target.value)"
                        class="w-full bg-[var(--dash-page-bg)] text-[var(--dash-text-heading)] px-4 py-2 border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] outline-none transition-colors">
                    <option value="">Pilih Program</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Plan Selector -->
            <div x-show="programId" x-cloak>
                <label for="payment_plan_id" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                    Paket Pembayaran <span class="text-red-500">*</span>
                </label>
                <div x-show="loading" class="text-sm text-[var(--dash-text-faint)] py-2">Memuat paket...</div>
                <select id="payment_plan_id" name="payment_plan_id"
                        x-show="!loading"
                        @change="selectPlan($event.target.value)"
                        class="w-full bg-[var(--dash-page-bg)] text-[var(--dash-text-heading)] px-4 py-2 border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] outline-none transition-colors">
                    <option value="">Pilih Paket Pembayaran</option>
                    <template x-for="plan in plans" :key="plan.id">
                        <option :value="plan.id" x-text="plan.label + ' (' + (plan.type === 'FULL' ? 'Lunas' : 'Cicilan') + ')'"></option>
                    </template>
                </select>
                <p x-show="!loading && plans.length === 0" class="text-sm text-[var(--dash-text-faint)] mt-1">
                    Tidak ada paket aktif untuk program ini.
                </p>
                @error('payment_plan_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror

                <!-- Plan Detail Card -->
                <div x-show="selectedPlan"
                     class="mt-3 bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 rounded-lg px-4 py-3 text-sm space-y-1">
                    <p class="font-semibold text-emerald-800 [.mode-dark_&]:text-emerald-400" x-text="selectedPlan?.label"></p>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-emerald-700 [.mode-dark_&]:text-emerald-400 mt-1">
                        <div>
                            <span class="text-[var(--dash-text-muted)]">Tipe:</span>
                            <span x-text="selectedPlan?.type === 'FULL' ? 'Lunas' : 'Cicilan'"></span>
                        </div>
                        <div x-show="selectedPlan?.installment_count">
                            <span class="text-[var(--dash-text-muted)]">Jumlah cicilan:</span>
                            <span x-text="selectedPlan?.installment_count + 'x'"></span>
                        </div>
                        <div x-show="selectedPlan?.dp_amount">
                            <span class="text-[var(--dash-text-muted)]">DP / Bayar pertama:</span>
                            <span x-text="formatRp(selectedPlan?.dp_amount)"></span>
                        </div>
                        <div x-show="selectedPlan?.per_installment_amount">
                            <span class="text-[var(--dash-text-muted)]">Per cicilan:</span>
                            <span x-text="formatRp(selectedPlan?.per_installment_amount)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discount Code -->
            <div>
                <label for="discount_code" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                    Kode Diskon <span class="text-[var(--dash-text-faint)] font-normal">(opsional)</span>
                </label>
                <input type="text" id="discount_code" name="discount_code"
                       value="{{ old('discount_code') }}"
                       class="w-full bg-[var(--dash-page-bg)] text-[var(--dash-text-heading)] px-4 py-2 border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] outline-none transition-colors uppercase"
                       placeholder="Masukkan kode diskon jika ada" />
                @error('discount_code')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-2 border-t border-[var(--dash-card-border)]">
                <a href="{{ route('members.show', $member) }}"
                   class="px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] border border-[var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-page-bg)] inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-[var(--dash-primary)] hover:opacity-90 rounded-lg transition-colors inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                    Daftarkan Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
