@extends('layouts.dashboard')

@section('title', 'Registrasi Member Baru')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Registrasi Member Baru</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $member->full_name }}</p>
        </div>
        <a href="{{ route('members.show', $member) }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 text-sm">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-xl border border-gray-200"
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
                <label for="program_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Program / Course <span class="text-red-500">*</span>
                </label>
                <select id="program_id" name="program_id" required
                        x-model="programId"
                        @change="fetchPlans($event.target.value)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Program --</option>
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
                <label for="payment_plan_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Paket Pembayaran <span class="text-red-500">*</span>
                </label>
                <div x-show="loading" class="text-sm text-gray-400 py-2">Memuat paket...</div>
                <select id="payment_plan_id" name="payment_plan_id"
                        x-show="!loading"
                        @change="selectPlan($event.target.value)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Paket Pembayaran --</option>
                    <template x-for="plan in plans" :key="plan.id">
                        <option :value="plan.id" x-text="plan.label + ' (' + (plan.type === 'FULL' ? 'Lunas' : 'Cicilan') + ')'"></option>
                    </template>
                </select>
                <p x-show="!loading && plans.length === 0" class="text-sm text-gray-400 mt-1">
                    Tidak ada paket aktif untuk program ini.
                </p>
                @error('payment_plan_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror

                <!-- Plan Detail Card -->
                <div x-show="selectedPlan"
                     class="mt-3 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm space-y-1">
                    <p class="font-semibold text-blue-800" x-text="selectedPlan?.label"></p>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-blue-700 mt-1">
                        <div>
                            <span class="text-gray-500">Tipe:</span>
                            <span x-text="selectedPlan?.type === 'FULL' ? 'Lunas' : 'Cicilan'"></span>
                        </div>
                        <div x-show="selectedPlan?.installment_count">
                            <span class="text-gray-500">Jumlah cicilan:</span>
                            <span x-text="selectedPlan?.installment_count + 'x'"></span>
                        </div>
                        <div x-show="selectedPlan?.dp_amount">
                            <span class="text-gray-500">DP / Bayar pertama:</span>
                            <span x-text="formatRp(selectedPlan?.dp_amount)"></span>
                        </div>
                        <div x-show="selectedPlan?.per_installment_amount">
                            <span class="text-gray-500">Per cicilan:</span>
                            <span x-text="formatRp(selectedPlan?.per_installment_amount)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discount Code -->
            <div>
                <label for="discount_code" class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Diskon <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <input type="text" id="discount_code" name="discount_code"
                       value="{{ old('discount_code') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase"
                       placeholder="Masukkan kode diskon jika ada" />
                @error('discount_code')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('members.show', $member) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Daftarkan Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
