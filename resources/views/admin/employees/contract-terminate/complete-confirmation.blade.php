@extends('layouts.dashboard')
@section('title', 'Konfirmasi Terminasi — ' . $status->employee->full_name)
@section('header_title', 'Konfirmasi Terminasi')

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
        
        <div class="bg-red-50/50 border-b border-red-100 px-6 py-5 flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
            </div>
            <div>
                <h1 class="text-lg font-bold text-red-700 font-jakarta mb-1">Konfirmasi Hard Delete Akun</h1>
                <p class="text-sm text-red-600 mb-3">Anda akan menghapus akun karyawan secara permanen. Tindakan ini memiliki konsekuensi sebagai berikut:</p>
                <ul class="list-disc list-inside text-sm text-red-600/80 space-y-1">
                    <li>Karyawan tidak bisa login lagi ke sistem</li>
                    <li>Tindakan ini <strong>tidak bisa dibatalkan</strong> (irreversible)</li>
                    <li>Data histori karyawan tetap tersimpan untuk keperluan audit trail</li>
                </ul>
            </div>
        </div>

        <div class="px-6 py-5">
            <h2 class="text-[12px] font-bold text-[color:var(--dash-text-heading)] uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-[color:var(--dash-text-faint)]">person</span> Data yang Akan Dihapus
            </h2>
            
            <div class="bg-[#F8FAFC] border border-[color:var(--dash-card-border)] rounded-2xl overflow-hidden">
                <div class="flex flex-col sm:flex-row justify-between px-4 py-3 border-b border-[color:var(--dash-card-border)]">
                    <span class="text-[12px] font-bold text-[color:var(--dash-text-muted)] uppercase tracking-wider">Nama Karyawan</span>
                    <span class="text-sm font-semibold text-[color:var(--dash-text-heading)] mt-1 sm:mt-0">{{ $status->employee->full_name }}</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between px-4 py-3 border-b border-[color:var(--dash-card-border)]">
                    <span class="text-[12px] font-bold text-[color:var(--dash-text-muted)] uppercase tracking-wider">Kode Karyawan</span>
                    <span class="text-sm font-semibold text-[color:var(--dash-text-heading)] mt-1 sm:mt-0">{{ $status->employee->employee_code }}</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between px-4 py-3 border-b border-[color:var(--dash-card-border)]">
                    <span class="text-[12px] font-bold text-[color:var(--dash-text-muted)] uppercase tracking-wider">Email</span>
                    <span class="text-sm font-semibold text-[color:var(--dash-text-heading)] mt-1 sm:mt-0">{{ $status->employee->email }}</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between px-4 py-3">
                    <span class="text-[12px] font-bold text-[color:var(--dash-text-muted)] uppercase tracking-wider">Status Pekerjaan</span>
                    <span class="text-sm font-semibold text-[color:var(--dash-text-heading)] mt-1 sm:mt-0">{{ $status->type_employment }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('contract-terminate.complete', $status) }}" method="POST" class="px-6 py-5 border-t border-[color:var(--dash-card-border)]">
            @csrf

            <label class="flex items-start gap-3 p-4 bg-[var(--dash-nav-hover-bg)] border border-[color:var(--dash-card-border)] rounded-xl cursor-pointer hover:bg-gray-100/50 transition-colors">
                <div class="relative flex items-center justify-center mt-0.5">
                    <input type="checkbox" name="confirm" value="on" required class="peer w-5 h-5 appearance-none border-2 border-[color:var(--dash-input-border)] rounded cursor-pointer checked:bg-red-500 checked:border-red-500 transition-all">
                    <span class="material-symbols-outlined text-white text-[16px] absolute pointer-events-none opacity-0 peer-checked:opacity-100">check</span>
                </div>
                <span class="text-sm text-[color:var(--dash-text-body)] leading-relaxed">
                    Saya memahami bahwa tindakan ini akan menghapus akun <strong>{{ $status->employee->full_name }}</strong> secara permanen dan tidak dapat dibatalkan. Saya siap melanjutkan.
                </span>
            </label>

            <div class="pt-6 mt-6 border-t border-[color:var(--dash-card-border)] flex justify-end gap-3">
                <a href="{{ route('contract-terminate.checklist', $status) }}"
                   class="px-5 py-2.5 text-[13px] font-bold text-[color:var(--dash-text-body)] bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-lg hover:bg-[var(--dash-nav-hover-bg)] transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-lg text-[13px] font-bold shadow-sm transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                    Hard Delete Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
