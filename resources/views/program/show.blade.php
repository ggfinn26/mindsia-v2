@extends('layouts.dashboard')

@section('title', $program->program_name . ' - MINDSIA')
@section('header_title', 'Detail Program')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showDeletePlanModal: false, deletePlanUrl: '' }">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('programs.index') }}"
               class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                        style="color: var(--dash-text-heading);">{{ $program->program_name }}</h1>
                    @if($program->is_active)
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                              style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                    @else
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Nonaktif</span>
                    @endif
                </div>
                <p class="mt-0.5 font-mono text-sm" style="color: var(--dash-text-muted);">
                    {{ $program->program_code }} · Rp {{ number_format($program->program_price, 0, ',', '.') }}
                </p>
            </div>
        </div>
        <a href="{{ route('programs.edit', $program) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors shrink-0"
           style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[14px]">edit</span>Edit
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Info card --}}
    <div class="rounded-2xl p-5"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Biaya Admin</p>
                @if($program->admin_fee > 0)
                    <p class="font-medium" style="color: var(--dash-text-heading);">
                        Rp {{ number_format($program->admin_fee, 0, ',', '.') }}
                        <span class="text-xs font-normal" style="color: var(--dash-text-muted);">
                            ({{ $program->admin_fee_mode === 'per_registration' ? 'per registrasi' : 'per transaksi' }},
                            {{ $program->admin_fee_timing === 'on_registration' ? 'saat registrasi' : 'pembayaran pertama' }})
                        </span>
                    </p>
                @else
                    <p style="color: var(--dash-text-faint);">Tidak ada</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Template Kwitansi</p>
                @if($program->receipt_template_path)
                    <span class="inline-flex items-center gap-1 text-sm" style="color: #059669;">
                        <span class="material-symbols-outlined text-[16px]">description</span>Tersedia
                    </span>
                @else
                    <span style="color: var(--dash-text-faint);">Belum ada</span>
                @endif
            </div>
            @if($program->program_description)
                <div class="col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Deskripsi</p>
                    <p style="color: var(--dash-text-body);">{{ $program->program_description }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Kurikulum --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">menu_book</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Kurikulum</h2>
            </div>
            @if($program->curriculum)
                <a href="{{ route('curriculums.show', $program->curriculum) }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold hover:underline"
                   style="color: var(--dash-primary);">
                    Lihat Detail <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            @else
                <a href="{{ route('curriculums.create') }}?program_id={{ $program->id }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                   style="background-color: #059669;">
                    <span class="material-symbols-outlined text-[13px]">add</span>Buat Kurikulum
                </a>
            @endif
        </div>
        <div class="px-5 py-4">
            @if($program->curriculum)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium" style="color: var(--dash-text-heading);">
                            {{ $program->curriculum->curriculum_name }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                            {{ $program->curriculum->sessions->count() }} sesi ·
                            {{ $program->curriculum->sessions->sum(fn($s) => $s->items->count()) }} item materi
                        </p>
                    </div>
                    @if($program->curriculum->is_active)
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                              style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                    @else
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Nonaktif</span>
                    @endif
                </div>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">
                    Belum ada kurikulum untuk program ini.
                </p>
            @endif
        </div>
    </div>

    {{-- Kuota Cabang --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">bar_chart</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Kuota Cabang</h2>
            </div>
            <a href="{{ route('programs.quotas.index', $program) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[13px]">settings</span>Kelola Kuota
            </a>
        </div>
        @if($program->branchQuotas->isEmpty())
            <div class="px-5 py-5 flex items-center justify-between">
                <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada kuota cabang.</p>
                <a href="{{ route('programs.quotas.index', $program) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[13px]">add</span>Tambah Kuota
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full table-fixed text-sm">
                    <colgroup><col class="w-2/3"><col class="w-1/3"></colgroup>
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Cabang</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kuota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($program->branchQuotas as $quota)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor=''">
                                <td class="px-5 py-3 font-medium" style="color: var(--dash-text-heading);">{{ $quota->branch->branch_name }}</td>
                                <td class="px-5 py-3 text-right" style="color: var(--dash-text-body);">{{ $quota->quota_limit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Payment Plans --}}
    @can('program.payment_plan.manage')
    <div x-data="{ showForm: false, planType: 'cash' }"
         class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">credit_card</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Payment Plans</h2>
            </div>
            <button @click="showForm = !showForm"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[14px]">add</span>Tambah Plan
            </button>
        </div>

        {{-- Inline add form --}}
        <div x-show="showForm" x-transition class="px-5 py-5"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="text-sm font-semibold mb-4" style="color: var(--dash-text-heading);">Tambah Payment Plan Baru</h3>
            <form method="POST" action="{{ route('programs.payment-plans.store', $program) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Label <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="label" placeholder="cth. Bayar Lunas, Cicilan 3x" required
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Tipe Pembayaran <span style="color:#dc2626;">*</span>
                        </label>
                        <div class="flex gap-4 mt-1">
                            <label class="inline-flex items-center gap-1.5 text-sm cursor-pointer" style="color: var(--dash-text-body);">
                                <input type="radio" name="type" value="cash" x-model="planType" checked> Lunas
                            </label>
                            <label class="inline-flex items-center gap-1.5 text-sm cursor-pointer" style="color: var(--dash-text-body);">
                                <input type="radio" name="type" value="installment" x-model="planType"> Cicilan
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">DP / Uang Muka</label>
                        <input type="number" name="dp_amount" min="0" placeholder="0"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    </div>
                    <div x-show="planType === 'installment'" x-transition>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Jumlah Cicilan <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="number" name="installment_count" min="1" placeholder="3"
                               :required="planType === 'installment'"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    </div>
                    <div x-show="planType === 'installment'" x-transition>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Nominal per Cicilan <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="number" name="per_installment_amount" min="0" placeholder="0"
                               :required="planType === 'installment'"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">Simpan</button>
                    <button type="button" @click="showForm = false"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                            style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">Batal</button>
                </div>
            </form>
        </div>

        @if($program->paymentPlans->isEmpty())
            <p class="px-5 py-6 text-sm" style="color: var(--dash-text-muted);">Belum ada payment plan. Klik "Tambah Plan" untuk menambahkan.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full table-fixed text-sm">
                    <colgroup>
                        <col class="w-[20%]"><col class="w-[10%]"><col class="w-[10%]">
                        <col class="w-[14%]"><col class="w-[14%]"><col class="w-[10%]"><col class="w-[22%]">
                    </colgroup>
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Label</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tipe</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Jml Cicilan</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">DP</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Per Cicilan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($program->paymentPlans as $plan)
                            <tr style="border-bottom: 1px solid var(--dash-card-border); {{ !$plan->is_active ? 'opacity: 0.5;' : '' }}"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor=''">
                                <td class="px-5 py-3 font-medium truncate" style="color: var(--dash-text-heading);">{{ $plan->label }}</td>
                                <td class="px-5 py-3" style="color: var(--dash-text-body);">{{ $plan->type === 'cash' ? 'Lunas' : 'Cicilan' }}</td>
                                <td class="px-5 py-3 text-right" style="color: var(--dash-text-body);">{{ $plan->installment_count ?? '—' }}</td>
                                <td class="px-5 py-3 text-right tabular-nums text-xs" style="color: var(--dash-text-body);">
                                    Rp {{ number_format($plan->dp_amount ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-right tabular-nums text-xs" style="color: var(--dash-text-body);">
                                    @if($plan->per_installment_amount)
                                        Rp {{ number_format($plan->per_installment_amount, 0, ',', '.') }}
                                    @else
                                        <span style="color: var(--dash-text-faint);">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if($plan->is_active)
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                              style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Arsip</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5 flex-wrap">
                                        @if($plan->is_active)
                                            <form method="POST" action="{{ route('programs.payment-plans.update', $plan) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="is_active" value="0">
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg transition-colors text-xs font-semibold"
                                                        style="background-color: #fef3c7; color: #d97706; border: 1px solid #fcd34d;"
                                                        onmouseover="this.style.backgroundColor='#fde68a'"
                                                        onmouseout="this.style.backgroundColor='#fef3c7'">
                                                    <span class="material-symbols-outlined text-[13px]">archive</span>Arsip
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('programs.payment-plans.update', $plan) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="is_active" value="1">
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg transition-colors text-xs font-semibold"
                                                        style="background-color: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;"
                                                        onmouseover="this.style.backgroundColor='#dcfce7'"
                                                        onmouseout="this.style.backgroundColor='#f0fdf4'">
                                                    <span class="material-symbols-outlined text-[13px]">unarchive</span>Aktifkan
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button"
                                                @click="deletePlanUrl = '{{ route('programs.payment-plans.destroy', $plan) }}'; showDeletePlanModal = true"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg transition-colors text-xs font-semibold"
                                                style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                                onmouseover="this.style.backgroundColor='#fecaca'"
                                                onmouseout="this.style.backgroundColor='#fee2e2'">
                                            <span class="material-symbols-outlined text-[13px]">delete</span>Hapus
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

    {{-- Modal hapus payment plan --}}
    <div x-show="showDeletePlanModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none;">
        <div class="absolute inset-0 bg-black/50"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showDeletePlanModal = false"></div>
        <div class="relative rounded-2xl p-6 max-w-sm w-full shadow-xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-start gap-4 mb-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full" style="background-color: #fee2e2;">
                    <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">delete</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold" style="color: var(--dash-text-heading);">Hapus Payment Plan</h3>
                    <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">Tindakan ini tidak bisa dibatalkan.</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" @click="showDeletePlanModal = false"
                        class="px-4 py-2 rounded-xl text-sm font-medium"
                        style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                    Batal
                </button>
                <button type="button"
                        @click="$refs.deletePlanForm.action = deletePlanUrl; $refs.deletePlanForm.submit()"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;">Hapus</button>
            </div>
        </div>
    </div>
    <form x-ref="deletePlanForm" method="POST" class="hidden">@csrf @method('DELETE')</form>

</div>
@endsection
