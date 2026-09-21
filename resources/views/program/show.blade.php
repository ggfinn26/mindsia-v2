@extends('layouts.dashboard')

@section('title', $program->program_name)

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $program->program_name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $program->program_code }} &middot; Rp {{ number_format($program->program_price, 0, ',', '.') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('programs.edit', $program) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Edit</a>
            <a href="{{ route('programs.index') }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">Kembali</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <!-- Program Info -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $program->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $program->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Biaya Admin</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($program->admin_fee > 0)
                            Rp {{ number_format($program->admin_fee, 0, ',', '.') }}
                            <span class="text-xs text-gray-500 ml-1">
                                ({{ $program->admin_fee_mode === 'per_registration' ? 'per registrasi' : 'per transaksi' }},
                                {{ $program->admin_fee_timing === 'on_registration' ? 'saat registrasi' : 'pembayaran pertama' }})
                            </span>
                        @else
                            <span class="text-gray-400">Tidak ada</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Template Kwitansi</dt>
                    <dd class="mt-1 text-sm">
                        @if($program->receipt_template_path)
                            <span class="inline-flex items-center gap-1 text-green-700">
                                <span class="material-symbols-outlined text-[16px]">description</span> Tersedia
                            </span>
                        @else
                            <span class="text-gray-400">Belum ada</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $program->program_description ?: '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Curriculum Section (1:1) -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">📚 Kurikulum</h2>
            @if($program->curriculum)
                <a href="{{ route('curriculums.show', $program->curriculum) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Lihat Detail &rarr;</a>
            @else
                <a href="{{ route('curriculums.create') }}?program_id={{ $program->id }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-medium">Buat Kurikulum</a>
            @endif
        </div>
        <div class="px-6 py-4">
            @if($program->curriculum)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $program->curriculum->curriculum_name }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $program->curriculum->sessions->count() }} sesi &middot; {{ $program->curriculum->sessions->sum(fn($s) => $s->items->count()) }} item materi</p>
                    </div>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $program->curriculum->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $program->curriculum->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            @else
                <p class="text-gray-500 text-sm">Belum ada kurikulum untuk program ini. Buat kurikulum untuk mengelola materi dan sesi pertemuan.</p>
            @endif
        </div>
    </div>

    <!-- Branch Quota Section -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">📊 Kuota Cabang</h2>
            <a href="{{ route('programs.quotas.index', $program) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Kelola Kuota &rarr;</a>
        </div>

        @if($program->branchQuotas->isEmpty())
            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                Belum ada kuota cabang. <a href="{{ route('programs.quotas.index', $program) }}" class="text-blue-600 hover:underline">Tambah kuota</a>.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuota</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($program->branchQuotas as $quota)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $quota->branch->branch_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quota->quota_limit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Payment Plans Section -->
    @can('program.payment_plan.manage')
    <div x-data="{ showForm: false }" class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">💳 Payment Plans</h2>
            <button
                @click="showForm = !showForm"
                class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Tambah Plan
            </button>
        </div>

        {{-- Inline add form --}}
        <div x-show="showForm" x-transition x-data="{
            type: 'cash',
        }" class="px-6 py-5 bg-gray-50 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Tambah Payment Plan Baru</h3>
            <form method="POST" action="{{ route('programs.payment-plans.store', $program) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Label <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="label"
                            placeholder="cth. Bayar Lunas, Cicilan 3x"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tipe Pembayaran <span class="text-red-500">*</span></label>
                        <div class="flex gap-4 mt-1">
                            <label class="inline-flex items-center gap-1.5 text-sm cursor-pointer">
                                <input type="radio" name="type" value="cash" x-model="type" class="text-blue-600" checked> Lunas
                            </label>
                            <label class="inline-flex items-center gap-1.5 text-sm cursor-pointer">
                                <input type="radio" name="type" value="installment" x-model="type" class="text-blue-600"> Cicilan
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">DP / Uang Muka</label>
                        <input
                            type="number"
                            name="dp_amount"
                            min="0"
                            placeholder="0"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                        >
                    </div>

                    <div x-show="type === 'installment'" x-transition>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Cicilan <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            name="installment_count"
                            min="1"
                            placeholder="cth. 3"
                            :required="type === 'installment'"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                        >
                    </div>

                    <div x-show="type === 'installment'" x-transition>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nominal per Cicilan <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            name="per_installment_amount"
                            min="0"
                            placeholder="0"
                            :required="type === 'installment'"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                        >
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                        Simpan
                    </button>
                    <button type="button" @click="showForm = false" class="border border-gray-300 text-gray-700 hover:bg-gray-100 px-4 py-2 rounded text-sm font-medium transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>

        @if($program->paymentPlans->isEmpty())
            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                Belum ada payment plan. Klik "Tambah Plan" untuk menambahkan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jml Cicilan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Per Cicilan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($program->paymentPlans as $plan)
                        <tr class="{{ $plan->is_active ? '' : 'opacity-60' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $plan->label }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $plan->type === 'cash' ? 'Lunas' : 'Cicilan' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $plan->installment_count ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                Rp {{ number_format($plan->dp_amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($plan->per_installment_amount)
                                    Rp {{ number_format($plan->per_installment_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($plan->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Diarsipkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    @if($plan->is_active)
                                        <form method="POST" action="{{ route('programs.payment-plans.update', $plan) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="is_active" value="0">
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-xs font-medium hover:underline">
                                                Arsip
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('programs.payment-plans.update', $plan) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="is_active" value="1">
                                            <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium hover:underline">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('programs.payment-plans.destroy', $plan) }}"
                                          onsubmit="return confirm('Hapus payment plan ini? Tidak bisa dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endcan

</div>
@endsection
