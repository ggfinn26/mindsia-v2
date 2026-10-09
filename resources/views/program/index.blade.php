@extends('layouts.dashboard')

@section('title', 'Program - MINDSIA')
@section('header_title', 'Program')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'POST', confirmActionText: 'Konfirmasi' }">

    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Kurikulum</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Program</h1>
        </div>
        <a href="{{ route('programs.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>Tambah Program
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

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[9%]">
                    <col class="w-[17%]">
                    <col class="w-[11%]">
                    <col class="w-[10%]">
                    <col class="w-[7%]">
                    <col class="w-[13%]">
                    <col class="w-[8%]">
                    <col class="w-[25%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama Program</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Harga</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Biaya Admin</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Template</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kurikulum</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $program)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4 font-mono text-xs" style="color: var(--dash-text-muted);">
                                {{ $program->program_code }}
                            </td>
                            <td class="px-5 py-4 font-medium truncate" style="color: var(--dash-text-heading);">
                                {{ $program->program_name }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums text-xs" style="color: var(--dash-text-body);">
                                Rp {{ number_format($program->program_price, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums text-xs" style="color: var(--dash-text-body);">
                                @if($program->admin_fee > 0)
                                    Rp {{ number_format($program->admin_fee, 0, ',', '.') }}
                                @else
                                    <span style="color: var(--dash-text-faint);">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($program->receipt_template_path)
                                    <span class="inline-flex items-center gap-1 text-xs" style="color: #059669;">
                                        <span class="material-symbols-outlined text-[14px]">description</span>Ada
                                    </span>
                                @else
                                    <span style="color: var(--dash-text-faint);" class="text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs truncate">
                                @if($program->curriculum)
                                    <a href="{{ route('curriculums.show', $program->curriculum) }}"
                                       class="hover:underline" style="color: var(--dash-primary);">
                                        {{ $program->curriculum->curriculum_name }}
                                    </a>
                                @else
                                    <span class="italic" style="color: var(--dash-text-faint);">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($program->is_active)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('programs.show', $program) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[13px]">open_in_new</span>Detail
                                    </a>
                                    <a href="{{ route('programs.edit', $program) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[13px]">edit</span>Edit
                                    </a>
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Nonaktifkan Program'; confirmMessage = 'Program yang masih memiliki registrasi member tidak bisa dinonaktifkan. Lanjutkan?'; confirmActionUrl = '{{ route('programs.deactivate', $program) }}'; confirmActionText = 'Nonaktifkan'"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                            style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                            onmouseover="this.style.backgroundColor='#fecaca'"
                                            onmouseout="this.style.backgroundColor='#fee2e2'">
                                        <span class="material-symbols-outlined text-[13px]">block</span>Nonaktif
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">school</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada program.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Confirm modal --}}
    <div x-show="showConfirmModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="absolute inset-0 bg-black/50"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showConfirmModal = false"></div>
        <div class="relative rounded-2xl p-6 max-w-md w-full shadow-xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-start gap-4 mb-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full" style="background-color: #fee2e2;">
                    <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">warning</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                    <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-1.5"
                        style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Batal
                </button>
                <button type="button"
                        @click="$refs.confirmForm.action = confirmActionUrl; $refs.confirmForm.submit()"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;" x-text="confirmActionText"></button>
            </div>
        </div>
    </div>
    <form x-ref="confirmForm" method="POST" class="hidden">@csrf</form>

</div>
@endsection
