@extends('layouts.dashboard')

@section('title', $program->program_name)
@section('header_title', $program->program_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{ showDeletePlanModal: false, deletePlanUrl: '' }">

    {{-- Page Header (Pola B3) --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('programs.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Program
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Program</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $program->program_name }}</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">{{ $program->program_code }} &middot; Rp {{ number_format($program->program_price, 0, ',', '.') }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            <a href="{{ route('programs.edit', $program) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">edit</span>Edit
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
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

    {{-- Program Info --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Program</h2>
        </div>
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-muted);">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full"
                              style="{{ $program->is_active ? 'background-color: #f0fdf4; color: #15803d;' : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);' }}">
                            {{ $program->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-muted);">Biaya Admin</dt>
                    <dd class="mt-1 text-sm" style="color: var(--dash-text-heading);">
                        @if($program->admin_fee > 0)
                            Rp {{ number_format($program->admin_fee, 0, ',', '.') }}
                            <span class="text-xs ml-1" style="color: var(--dash-text-muted);">
                                ({{ $program->admin_fee_mode === 'per_registration' ? 'per registrasi' : 'per transaksi' }},
                                {{ $program->admin_fee_timing === 'on_registration' ? 'saat registrasi' : 'pembayaran pertama' }})
                            </span>
                        @else
                            <span style="color: var(--dash-text-faint);">Tidak ada</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-muted);">Template Kwitansi</dt>
                    <dd class="mt-1 text-sm">
                        @if($program->receipt_template_path)
                            <span class="inline-flex items-center gap-1" style="color: #15803d;">
                                <span class="material-symbols-outlined text-[16px]">description</span> Tersedia
                            </span>
                        @else
                            <span style="color: var(--dash-text-faint);">Belum ada</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-muted);">Deskripsi</dt>
                    <dd class="mt-1 text-sm" style="color: var(--dash-text-heading);">{{ $program->program_description ?: '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Kurikulum (1:1) --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm flex items-center gap-2" style="color: var(--dash-text-heading);">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">menu_book</span>
                Kurikulum
            </h2>
            @if($program->curriculum)
                <a href="{{ route('curriculums.show', $program->curriculum) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[14px]">visibility</span>Lihat Detail
                </a>
            @else
                <a href="{{ route('curriculums.create') }}?program_id={{ $program->id }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[14px]">add</span>Buat Kurikulum
                </a>
            @endif
        </div>
        <div class="px-6 py-5">
            @if($program->curriculum)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-sm" style="color: var(--dash-text-heading);">{{ $program->curriculum->curriculum_name }}</p>
                        <p class="text-xs mt-1" style="color: var(--dash-text-muted);">{{ $program->curriculum->sessions->count() }} sesi &middot; {{ $program->curriculum->sessions->sum(fn($s) => $s->items->count()) }} item materi</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full"
                          style="{{ $program->curriculum->is_active ? 'background-color: #f0fdf4; color: #15803d;' : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);' }}">
                        {{ $program->curriculum->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada kurikulum untuk program ini. Buat kurikulum untuk mengelola materi dan sesi pertemuan.</p>
            @endif
        </div>
    </div>

    {{-- Kuota Cabang --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm flex items-center gap-2" style="color: var(--dash-text-heading);">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">bar_chart</span>
                Kuota Cabang
            </h2>
            <a href="{{ route('programs.quotas.index', $program) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[14px]">settings</span>Kelola Kuota
            </a>
        </div>

        @if($program->branchQuotas->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">domain</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada kuota cabang.</p>
                <a href="{{ route('programs.quotas.index', $program) }}"
                   class="inline-flex items-center gap-1.5 mt-4 px-3 py-1.5 text-xs font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[14px]">add</span>Tambah Kuota
                </a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Kuota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($program->branchQuotas as $quota)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">{{ $quota->branch->branch_name }}</td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $quota->quota_limit }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Payment Plans --}}
    @can('program.payment_plan.manage')
    <div x-data="{ showForm: false }" class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm flex items-center gap-2" style="color: var(--dash-text-heading);">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">credit_card</span>
                Payment Plans
            </h2>
            <button
                @click="showForm = !showForm"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[14px]">add</span>
                Tambah Plan
            </button>
        </div>

        {{-- Inline add form --}}
        <div x-show="showForm" x-transition x-data="{ type: 'cash' }"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="px-6 py-5">
                <h3 class="text-sm font-semibold mb-4" style="color: var(--dash-text-heading);">Tambah Payment Plan Baru</h3>
                <form method="POST" action="{{ route('programs.payment-plans.store', $program) }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Label <span style="color: #dc2626;">*</span></label>
                            <input
                                type="text"
                                name="label"
                                placeholder="cth. Bayar Lunas, Cicilan 3x"
                                class="w-full rounded-lg px-3 py-2 text-sm"
                                style="border: 1px solid var(--dash-input-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Tipe Pembayaran <span style="color: #dc2626;">*</span></label>
                            <div class="flex gap-4 mt-1">
                                <label class="inline-flex items-center gap-1.5 text-sm cursor-pointer" style="color: var(--dash-text-body);">
                                    <input type="radio" name="type" value="cash" x-model="type" checked> Lunas
                                </label>
                                <label class="inline-flex items-center gap-1.5 text-sm cursor-pointer" style="color: var(--dash-text-body);">
                                    <input type="radio" name="type" value="installment" x-model="type"> Cicilan
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">DP / Uang Muka</label>
                            <input
                                type="number"
                                name="dp_amount"
                                min="0"
                                placeholder="0"
                                class="w-full rounded-lg px-3 py-2 text-sm"
                                style="border: 1px solid var(--dash-input-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                            >
                        </div>

                        <div x-show="type === 'installment'" x-transition>
                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Jumlah Cicilan <span style="color: #dc2626;">*</span></label>
                            <input
                                type="number"
                                name="installment_count"
                                min="1"
                                placeholder="cth. 3"
                                :required="type === 'installment'"
                                class="w-full rounded-lg px-3 py-2 text-sm"
                                style="border: 1px solid var(--dash-input-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                            >
                        </div>

                        <div x-show="type === 'installment'" x-transition>
                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Nominal per Cicilan <span style="color: #dc2626;">*</span></label>
                            <input
                                type="number"
                                name="per_installment_amount"
                                min="0"
                                placeholder="0"
                                :required="type === 'installment'"
                                class="w-full rounded-lg px-3 py-2 text-sm"
                                style="border: 1px solid var(--dash-input-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                            >
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">save</span>Simpan
                        </button>
                        <button type="button" @click="showForm = false"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($program->paymentPlans->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">credit_card</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada payment plan. Klik "Tambah Plan" untuk menambahkan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Label</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Tipe</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Jml Cicilan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">DP</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Per Cicilan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($program->paymentPlans as $plan)
                        <tr class="{{ $plan->is_active ? '' : 'opacity-60' }}"
                            style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">{{ $plan->label }}</td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                {{ $plan->type === 'cash' ? 'Lunas' : 'Cicilan' }}
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                {{ $plan->installment_count ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                Rp {{ number_format($plan->dp_amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                @if($plan->per_installment_amount)
                                    Rp {{ number_format($plan->per_installment_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($plan->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: #f0fdf4; color: #15803d;">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Diarsipkan</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    @if($plan->is_active)
                                        <form method="POST" action="{{ route('programs.payment-plans.update', $plan) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="is_active" value="0">
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">toggle_off</span>Arsip
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('programs.payment-plans.update', $plan) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="is_active" value="1">
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">toggle_on</span>Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button"
                                        @click="deletePlanUrl = '{{ route('programs.payment-plans.destroy', $plan) }}'; showDeletePlanModal = true"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                                        style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                    </button>
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

    {{-- Modal Hapus Payment Plan --}}
    <div x-show="showDeletePlanModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center"
         @keydown.escape.window="showDeletePlanModal = false">
        <div class="absolute inset-0 bg-black/40" @click="showDeletePlanModal = false"></div>
        <div class="relative w-full max-w-sm mx-4 rounded-2xl shadow-xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            {{-- Modal Header --}}
            <div class="px-6 py-4 rounded-t-2xl flex items-center gap-3" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-full" style="background-color: #fee2e2;">
                    <span class="material-symbols-outlined text-[18px]" style="color: #dc2626;">delete</span>
                </div>
                <div>
                    <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Hapus Payment Plan</h3>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Tindakan ini tidak bisa dibatalkan.</p>
                </div>
            </div>
            {{-- Modal Footer --}}
            <div class="px-6 py-4 rounded-b-2xl flex gap-3 justify-end" style="background-color: var(--dash-card-bg);">
                <button type="button" @click="showDeletePlanModal = false"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </button>
                <button type="button"
                        @click="$refs.deletePlanForm.action = deletePlanUrl; $refs.deletePlanForm.submit()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[16px]">delete</span>Hapus
                </button>
            </div>
        </div>
    </div>

    <form x-ref="deletePlanForm" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>

</div>
@endsection
