@extends('layouts.dashboard')

@section('title', 'Checklist Terminasi — ' . $status->employee->full_name)
@section('header_title', 'Checklist Terminasi')

@section('content')
<div class="max-w-2xl mx-auto">

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 mb-6 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-[color:var(--dash-card-border)] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#5586DB] text-[22px]">checklist</span>
                <h2 class="font-bold text-[color:var(--dash-text-heading)] font-jakarta">Checklist Penyelesaian Kewajiban</h2>
            </div>
            <a href="{{ route('employees.show', $status->employee) }}" class="text-[13px] font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-heading)] flex items-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
            </a>
        </div>

        <div class="px-6 py-4 bg-gray-50/50 border-b border-[color:var(--dash-card-border)]">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[11px] font-bold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Nama Karyawan</p>
                    <p class="text-sm font-semibold text-[color:var(--dash-text-heading)]">{{ $status->employee->full_name }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Status Karyawan</p>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 rounded-lg text-xs font-semibold border border-red-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        Terminated
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('contract-terminate.checklist.update', $status) }}" method="POST" class="px-6 py-5 space-y-6">
            @csrf
            @method('PATCH')

            @if($checklist->count() === 0)
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-gray-300 text-5xl mb-2">assignment</span>
                    <p class="text-[color:var(--dash-text-muted)] text-sm">Belum ada item checklist kewajiban untuk karyawan ini.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($checklist as $item)
                        <div class="relative">
                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                            
                            <label class="flex items-start sm:items-center gap-4 p-4 border border-[color:var(--dash-card-border)] rounded-xl cursor-pointer hover:bg-gray-50/50 transition-colors {{ $item->is_completed ? 'bg-[var(--dash-nav-hover-bg)] border-[color:var(--dash-card-border)] opacity-75' : 'bg-[var(--dash-card-bg)]' }}">
                                <div class="relative flex items-center justify-center mt-0.5 sm:mt-0">
                                    <input type="hidden" name="items[{{ $loop->index }}][is_completed]" value="0">
                                    <input type="checkbox" name="items[{{ $loop->index }}][is_completed]" value="1"
                                        {{ $item->is_completed ? 'checked' : '' }}
                                        class="peer w-5 h-5 appearance-none border-2 border-[color:var(--dash-input-border)] rounded cursor-pointer checked:bg-[#5586DB] checked:border-[#5586DB] transition-all">
                                    <span class="material-symbols-outlined text-white text-[16px] absolute pointer-events-none opacity-0 peer-checked:opacity-100">check</span>
                                </div>

                                <div class="flex-1">
                                    <span class="text-sm font-medium text-[color:var(--dash-text-heading)] {{ $item->is_completed ? 'line-through text-[color:var(--dash-text-muted)]' : '' }}">{{ $item->item_name }}</span>
                                </div>

                                @if($item->is_completed)
                                    <div class="flex items-center gap-1 text-[11px] font-semibold text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)] px-2.5 py-1 rounded-md shrink-0">
                                        <span class="material-symbols-outlined text-[14px]">done_all</span>
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

                <div class="mt-8 p-5 bg-[#F8FAFC] rounded-xl border border-[color:var(--dash-card-border)]">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-bold text-[color:var(--dash-text-heading)]">Progress Penyelesaian</p>
                        <p class="text-sm font-bold {{ $isAllDone ? 'text-green-600' : 'text-[#5586DB]' }}">{{ $completedCount }} / {{ $checklist->count() }} ({{ $percentage }}%)</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full transition-all duration-500 {{ $isAllDone ? 'bg-green-500' : 'bg-[#5586DB]' }}" style="width: {{ $percentage }}%"></div>
                    </div>
                    @if($isAllDone)
                        <p class="text-[12px] font-medium text-green-600 mt-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">verified</span> Seluruh kewajiban telah diselesaikan.
                        </p>
                    @endif
                </div>
            @endif

            <div class="pt-4 mt-6 border-t border-[color:var(--dash-card-border)] flex justify-end gap-3">
                <a href="{{ route('employees.show', $status->employee) }}"
                   class="px-5 py-2.5 text-[13px] font-bold text-[color:var(--dash-text-body)] bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl hover:bg-[var(--dash-nav-hover-bg)] transition-colors">
                    Kembali
                </a>
                @if($checklist->count() > 0)
                    <button type="submit"
                            class="bg-[#5586DB] hover:bg-[#4A78C4] text-white px-6 py-2.5 rounded-xl text-[13px] font-bold shadow-sm transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Progress
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
