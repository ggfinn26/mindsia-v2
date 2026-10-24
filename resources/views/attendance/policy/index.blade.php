@extends('layouts.dashboard')

@section('title', 'Kebijakan Absensi - MINDSIA')
@section('header_title', 'Kebijakan Absensi')

@section('content')

<div x-data="{ confirmDelete: null }" class="max-w-6xl mx-auto pt-8 sm:pt-16 space-y-4">
    <div class="mb-5 sm:flex sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight text-[color:var(--dash-text-heading)]">Kebijakan Absensi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Atur cakupan lokasi, pengecualian, dan toleransi per cabang/area/region</p>
        </div>
        <a href="{{ route('attendance-policies.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>Tambah Kebijakan
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px] leading-none">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="px-6 py-4 border-b flex items-center gap-2.5"
             style="border-color: var(--dash-card-border);">
            <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Daftar Kebijakan</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Nama Kebijakan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Cakupan</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Bebas Absen</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies as $policy)
                        @php
                            $target = match($policy->attendance_scope) {
                                'branch' => $policy->branch?->branch_name,
                                'area'   => $policy->area?->name,
                                'region' => $policy->region?->name,
                                default  => null,
                            };
                            $scopeLabel = match($policy->attendance_scope) {
                                'branch' => 'Cabang', 'area' => 'Area', 'region' => 'Region',
                                default  => $policy->attendance_scope,
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-6 py-4 font-medium" style="color: var(--dash-text-heading);">{{ $policy->policy_name }}</td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">{{ $scopeLabel }}</span>
                                <span class="ml-2 text-xs">{{ $target ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($policy->is_attendance_exempt)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color:#fef3c7;color:#b45309;">Dikecualikan</span>
                                @else
                                    <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($policy->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color:#f0fdf4;color:#059669;">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: var(--dash-card-border); color: var(--dash-text-muted);">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('attendance-policies.edit', $policy) }}"
                                       class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                                    <button type="button"
                                            @click="confirmDelete = { id: {{ $policy->id }}, name: '{{ addslashes($policy->policy_name) }}' }"
                                            class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium text-white transition-colors"
                                            style="background-color: #dc2626;"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <span class="material-symbols-outlined text-[48px] md:text-[48px]" style="color: var(--dash-text-faint);">rule</span>
                                <p class="text-sm font-semibold mt-2" style="color: var(--dash-text-muted);">Belum ada kebijakan absensi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($policies->hasPages())
        <div>{{ $policies->links() }}</div>
    @endif

    {{-- Delete confirmation modal (Alpine, no native dialog) --}}
    <div x-show="confirmDelete" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(0,0,0,0.5);" @click.self="confirmDelete = null">
        <div class="w-full max-w-md rounded-2xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl border-b" style="border-color: var(--dash-card-border);">
                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Kebijakan</h3>
            </div>
            <div class="px-6 py-5">
                <p class="text-sm" style="color: var(--dash-text-body);">Yakin hapus kebijakan "<span class="font-semibold" x-text="confirmDelete?.name"></span>"? Konfigurasi jadwal kerja, sesi, dan aturan terkait ikut terhapus.</p>
            </div>
            <div class="px-6 py-4 rounded-b-2xl border-t flex items-center justify-end gap-3" style="border-color: var(--dash-card-border);">
                <button type="button" @click="confirmDelete = null"
                        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium"
                        style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">Batal</button>
                <form x-show="confirmDelete" method="POST" :action="`{{ url('attendance-policies') }}/${confirmDelete?.id}`">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white"
                            style="background-color: #dc2626;"><span class="material-symbols-outlined text-[18px]">delete</span>Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
