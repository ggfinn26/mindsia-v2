@extends('layouts.dashboard')

@section('title', 'Job Permintaan')
@section('header_title', 'Job Permintaan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto" x-data="{
    showConfirmModal: false,
    confirmTitle: '',
    confirmMessage: '',
    confirmActionUrl: '',
    confirmMethod: 'POST',
    confirmActionText: 'Konfirmasi'
}">

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Rekrutmen</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Job Permintaan</h1>
        </div>
        @can('recruitment.job_permintaan.create')
        <a href="{{ route('recruitment.permintaan.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Permintaan
        </a>
        @endcan
    </div>

    {{-- Filter --}}
    <div class="mb-5">
        <form method="GET" action="{{ route('recruitment.permintaan.index') }}">
            <select name="status" onchange="this.form.submit()"
                    class="rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Status</option>
                <option value="draft"                {{ request('status') === 'draft'                ? 'selected' : '' }}>Draft</option>
                <option value="pending_hr_review"    {{ request('status') === 'pending_hr_review'    ? 'selected' : '' }}>Pending HR Review</option>
                <option value="pending_ops_approval" {{ request('status') === 'pending_ops_approval' ? 'selected' : '' }}>Pending Ops Approval</option>
                <option value="approved"             {{ request('status') === 'approved'             ? 'selected' : '' }}>Approved</option>
                <option value="rejected"             {{ request('status') === 'rejected'             ? 'selected' : '' }}>Rejected</option>
                <option value="fulfilled"            {{ request('status') === 'fulfilled'            ? 'selected' : '' }}>Fulfilled</option>
            </select>
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm">
            <thead style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Cabang</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Posisi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Jenis Request</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Headcount</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permintaans as $permintaan)
                    @php
                        $statusColors = [
                            'draft'                => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]',
                            'pending_hr_review'    => 'bg-amber-100 text-amber-700',
                            'pending_ops_approval' => 'bg-blue-100 text-blue-700',
                            'approved'             => 'bg-emerald-100 text-emerald-700',
                            'rejected'             => 'bg-red-100 text-red-700',
                            'fulfilled'            => 'bg-purple-100 text-purple-700',
                        ];
                        $color = $statusColors[$permintaan->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]';
                    @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-6 py-4 whitespace-nowrap" style="color: var(--dash-text-muted);">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium" style="color: var(--dash-text-heading);">{{ $permintaan->branch->branch_name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" style="color: var(--dash-text-body);">{{ optional($permintaan->detail)->position->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap capitalize" style="color: var(--dash-text-body);">{{ str_replace('_', ' ', optional($permintaan->detail)->request_type ?? '-') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center" style="color: var(--dash-text-body);">
                            {{ optional($permintaan->detail)->current_headcount ?? 0 }} / {{ optional($permintaan->detail)->headcount ?? 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full {{ $color }}">
                                {{ str_replace('_', ' ', Str::title($permintaan->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('recruitment.permintaan.show', $permintaan->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>Detail
                                </a>
                                @if($permintaan->status === 'draft')
                                    @can('recruitment.job_permintaan.update')
                                    <a href="{{ route('recruitment.permintaan.edit', $permintaan->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                    </a>
                                    @endcan
                                    @can('recruitment.job_permintaan.create')
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Hapus Permintaan'; confirmMessage = 'Yakin ingin menghapus permintaan ini?'; confirmActionUrl = '{{ route('recruitment.permintaan.destroy', $permintaan->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm" style="color: var(--dash-text-faint);">Belum ada data job permintaan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($permintaans->hasPages())
        <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border);">
            {{ $permintaans->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Konfirmasi --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" style="display: none;"
         @keydown.escape.window="showConfirmModal = false">
        <div class="rounded-2xl shadow-xl w-full max-w-sm p-6" @click.stop
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                     :class="confirmMethod === 'DELETE' ? 'bg-red-100' : 'bg-amber-100'">
                    <span class="material-symbols-outlined text-[20px]"
                          :class="confirmMethod === 'DELETE' ? 'text-red-600' : 'text-amber-600'">warning</span>
                </div>
                <div>
                    <h3 class="font-semibold text-base" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                    <p class="text-sm mt-1" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                </div>
            </div>
            <div class="flex gap-3 mt-5 justify-end">
                <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors inline-flex items-center gap-1.5"
                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                <button type="button" @click="$refs.confirmForm.submit()"
                        class="px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        :style="confirmMethod === 'DELETE' ? 'background-color: #dc2626;' : 'background-color: #d97706;'"
                        x-text="confirmActionText"></button>
            </div>
            <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="_method" :value="confirmMethod">
            </form>
        </div>
    </div>

</div>
@endsection
