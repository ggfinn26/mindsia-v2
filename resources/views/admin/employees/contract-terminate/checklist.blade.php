@extends('layouts.dashboard')

@section('title', 'Checklist Terminasi - ' . $status->employee->full_name)
@section('header_title', 'Checklist Terminasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('employees.show', $status->employee) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Detail Pegawai
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">SDM & Karyawan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Checklist Kewajiban Terminasi</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Pegawai: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</span> ({{ $status->employee->employee_code }})
        </p>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <div class="flex items-center gap-2 font-semibold mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            Ada kesalahan:
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary);">checklist</span>
                <h2 class="font-bold text-sm font-jakarta" style="color: var(--dash-text-heading);">Checklist Penyelesaian Kewajiban</h2>
            </div>
        </div>

        <div class="px-6 py-4" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Nama Pegawai</p>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Status Kepegawaian</p>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                        Terminated
                    </span>
                </div>
            </div>
        </div>

        <form action="{{ route('contract-terminate.checklist.update', $status) }}" method="POST" class="px-6 py-6 space-y-6">
            @csrf
            @method('PATCH')

            @if($checklist->count() === 0)
                <div class="text-center py-10">
                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-2" style="color: var(--dash-text-faint);">assignment</span>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada item checklist kewajiban untuk pegawai ini.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($checklist as $item)
                        <div class="relative">
                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">

                            <label class="flex items-start sm:items-center gap-4 p-4 rounded-xl cursor-pointer transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: {{ $item->is_completed ? 'var(--dash-nav-hover-bg)' : 'var(--dash-card-bg)' }};"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='{{ $item->is_completed ? 'var(--dash-nav-hover-bg)' : 'var(--dash-card-bg)' }}'">
                                <div class="relative flex items-center justify-center mt-0.5 sm:mt-0">
                                    <input type="hidden" name="items[{{ $loop->index }}][is_completed]" value="0">
                                    <input type="checkbox" name="items[{{ $loop->index }}][is_completed]" value="1"
                                        {{ $item->is_completed ? 'checked' : '' }}
                                        class="h-5 w-5 rounded cursor-pointer transition-all"
                                        style="accent-color: var(--dash-primary);">
                                </div>

                                <div class="flex-1">
                                    <span class="text-sm font-medium {{ $item->is_completed ? 'line-through' : '' }}"
                                          style="color: {{ $item->is_completed ? 'var(--dash-text-muted)' : 'var(--dash-text-heading)' }};">
                                        {{ $item->item_name }}
                                    </span>
                                </div>

                                @if($item->is_completed)
                                    <div class="flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-md shrink-0"
                                         style="background-color: var(--dash-card-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                                        <span class="material-symbols-outlined text-[14px] text-emerald-600">done_all</span>
                                        {{ $item->completed_at->format('d M Y, H:i') }}
                                    </div>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>

                @php
                    $percentage = $checklist->count() > 0 ? round(($completedCount / $checklist->count()) * 100) : 0;
                    $isAllDone = $completedCount === $checklist->count();
                @endphp

                <div class="p-5 rounded-xl"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Progress Penyelesaian</p>
                        <p class="text-sm font-bold {{ $isAllDone ? 'text-emerald-700' : '' }}"
                           style="{{ $isAllDone ? '' : 'color: var(--dash-primary);' }}">
                            {{ $completedCount }} / {{ $checklist->count() }} ({{ $percentage }}%)
                        </p>
                    </div>
                    <div class="w-full rounded-full h-2.5 overflow-hidden" style="background-color: var(--dash-card-border);">
                        <div class="h-2.5 rounded-full transition-all duration-500 {{ $isAllDone ? 'bg-emerald-500' : '' }}"
                             style="width: {{ $percentage }}%; {{ $isAllDone ? '' : 'background-color: var(--dash-primary);' }}"></div>
                    </div>
                    @if($isAllDone)
                        <p class="text-xs font-semibold text-emerald-700 mt-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">verified</span> Seluruh kewajiban telah diselesaikan.
                        </p>
                    @endif
                </div>
            @endif

            <div class="pt-4 flex justify-end gap-3" style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('employees.show', $status->employee) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Kembali
                </a>
                @if($checklist->count() > 0)
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Simpan Progress
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
