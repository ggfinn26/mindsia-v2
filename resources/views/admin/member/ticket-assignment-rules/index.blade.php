@extends('layouts.dashboard')

@section('title', 'Aturan Penugasan Tiket - MINDSIA')
@section('header_title', 'Aturan Penugasan Tiket')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
         createModalOpen: false,
         deleteModalOpen: false,
         deleteActionUrl: '',
         deleteDesc: '',
         openDelete(url, desc) {
             this.deleteActionUrl = url;
             this.deleteDesc = desc;
             this.deleteModalOpen = true;
         }
     }">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('support-tickets.index') }}" class="dash-back mb-3 inline-flex items-center gap-1.5 text-xs font-semibold">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Support Ticket
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
                <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Aturan Penugasan Tiket</h1>
                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Konfigurasi penugasan otomatis tiket support kepada posisi karyawan berdasarkan kategori dan cabang member.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="createModalOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Buat Aturan Baru
                </button>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Data Table (No horizontal scroll, fully fitted) --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm text-left">
            <thead>
                <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Kategori Tiket</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Cabang Member</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Ditugaskan Ke (Posisi)</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                    @php
                        $categoryLabel = match($rule->category) {
                            'complaint'  => 'Pengaduan',
                            'suggestion' => 'Saran',
                            'question'   => 'Pertanyaan',
                            default      => ucfirst($rule->category),
                        };
                        $categoryBadgeClass = match($rule->category) {
                            'complaint'  => 'bg-red-100 text-red-700',
                            'suggestion' => 'bg-purple-100 text-purple-700',
                            'question'   => 'bg-blue-100 text-blue-700',
                            default      => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $categoryBadgeClass }}">
                                {{ $categoryLabel }}
                            </span>
                        </td>
                        <td class="px-5 py-4 font-medium" style="color: var(--dash-text-heading);">
                            {{ $rule->branch?->branch_name ?? 'Semua Cabang' }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 font-semibold text-sm" style="color: var(--dash-text-heading);">
                                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary-text);">badge</span>
                                {{ $rule->position?->position_name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button"
                                    @click="openDelete('{{ route('ticket-assignment-rules.destroy', $rule) }}', '{{ $categoryLabel }} - {{ $rule->branch?->branch_name ?? 'Cabang' }}')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: #dc2626;">
                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">rule</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada aturan penugasan tiket.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Aturan (Custom Alpine, NO overflow-hidden on card) --}}
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="createModalOpen = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 py-4 rounded-t-2xl border-b"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Buat Aturan Penugasan Tiket</h3>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Tentukan posisi yang bertanggung jawab merespons tiket</p>
                </div>
                <form action="{{ route('ticket-assignment-rules.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                            Kategori Tiket <span class="text-red-500">*</span>
                        </label>
                        <select name="category" required
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="complaint">Pengaduan</option>
                            <option value="suggestion">Saran</option>
                            <option value="question">Pertanyaan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                            Cabang Member <span class="text-red-500">*</span>
                        </label>
                        <select name="branch_id" required
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Cabang</option>
                            @foreach(\App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get() as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                            Tugaskan Ke (Posisi) <span class="text-red-500">*</span>
                        </label>
                        <select name="position_id" required
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Posisi</option>
                            @foreach(\App\Models\Position::orderBy('position_name')->get() as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 justify-end pt-3 border-t" style="border-color: var(--dash-card-border);">
                        <button type="button" @click="createModalOpen = false"
                                class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan Aturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus (Custom Alpine) --}}
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="deleteModalOpen = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: #fee2e2; color: #dc2626;">
                            <span class="material-symbols-outlined text-[24px]">delete</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Hapus Aturan Penugasan</h3>
                            <div class="mt-2 text-sm" style="color: var(--dash-text-muted);">
                                Yakin ingin menghapus aturan penugasan tiket ini?
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="deleteModalOpen = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <form :action="deleteActionUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #dc2626;">
                            Hapus Aturan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
