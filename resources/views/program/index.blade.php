@extends('layouts.dashboard')

@section('title', 'Program & Kuota')
@section('header_title', 'Program & Kuota')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '' }">

    {{-- Page header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Program</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Program & Kuota</h1>
        </div>
        @can('program.create')
        <a href="{{ route('programs.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>Tambah Program
        </a>
        @endcan
    </div>

    {{-- Flash messages --}}
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

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Kode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Nama Program</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Harga</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Biaya Admin</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Template</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Kurikulum</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programs as $program)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-mono text-xs" style="color: var(--dash-text-muted);">{{ $program->program_code }}</td>
                        <td class="px-5 py-3.5 font-semibold" style="color: var(--dash-text-heading);">{{ $program->program_name }}</td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">Rp {{ number_format($program->program_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                            @if ($program->admin_fee > 0)
                                Rp {{ number_format($program->admin_fee, 0, ',', '.') }}
                            @else
                                <span style="color: var(--dash-text-faint);">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($program->receipt_template_path)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold" style="color: #059669;">
                                    <span class="material-symbols-outlined text-[14px]">description</span>Ada
                                </span>
                            @else
                                <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($program->curriculum)
                                <a href="{{ route('curriculums.show', $program->curriculum) }}"
                                   class="text-xs font-semibold hover:underline"
                                   style="color: var(--dash-primary);">{{ $program->curriculum->curriculum_name }}</a>
                            @else
                                <span class="text-xs italic" style="color: var(--dash-text-faint);">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($program->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                      style="background-color: #dcfce7; color: #166534;">
                                    <span class="material-symbols-outlined text-[12px]">toggle_on</span>Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[12px]">toggle_off</span>Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('programs.show', $program) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>Detail
                                </a>
                                <a href="{{ route('programs.edit', $program) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                </a>
                                <button type="button"
                                    @click="showConfirmModal = true; confirmTitle = 'Nonaktifkan Program'; confirmMessage = 'Program yang masih memiliki registrasi member tidak bisa dinonaktifkan. Lanjutkan?'; confirmActionUrl = '{{ route('programs.deactivate', $program) }}'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: #dc2626;">
                                    <span class="material-symbols-outlined text-[14px]">toggle_off</span>Nonaktifkan
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">school</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada program.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($programs) && method_exists($programs, 'hasPages') && $programs->hasPages())
        <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">{{ $programs->links() }}</div>
        @endif
    </div>

    {{-- Modal Konfirmasi Nonaktifkan --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0" style="background-color: rgba(0,0,0,0.4);" @click="showConfirmModal = false"></div>
        <div class="relative w-full max-w-md rounded-2xl shadow-xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            {{-- Header --}}
            <div class="flex items-center gap-3 px-6 py-4 rounded-t-2xl" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div class="flex h-9 w-9 items-center justify-center rounded-full" style="background-color: #fee2e2;">
                    <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">warning</span>
                </div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
            </div>
            {{-- Body --}}
            <div class="px-6 py-5">
                <p class="text-sm" style="color: var(--dash-text-body);" x-text="confirmMessage"></p>
            </div>
            {{-- Footer --}}
            <div class="flex justify-end gap-3 px-6 py-4 rounded-b-2xl" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">
                <button type="button" @click="showConfirmModal = false"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">close</span>Batal
                </button>
                <button type="button" @click="$refs.confirmForm.action = confirmActionUrl; $refs.confirmForm.submit()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[16px]">toggle_off</span>Nonaktifkan
                </button>
            </div>
            <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>

</div>
@endsection
