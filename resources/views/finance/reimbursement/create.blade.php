@extends('layouts.dashboard')

@section('title', 'Buat Reimbursement')
@section('header_title', 'Buat Reimbursement')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('reimbursements.index') }}" class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Reimbursement Saya
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Buat Reimbursement</h2>
        <p class="mt-1 text-sm text-slate-500">Isi tujuan dan periode reimbursement. Rincian pengeluaran (item) dapat ditambahkan setelah disimpan.</p>
    </div>

    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 p-4 shadow-sm">
            <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
            <div>
                <h3 class="text-sm font-semibold text-red-800">Terdapat Kesalahan</h3>
                <ul class="mt-1 list-disc pl-4 text-sm text-red-700">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('reimbursements.store') }}" class="rounded-xl border border-slate-100 bg-white p-6 shadow-sm space-y-5">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-bold text-slate-800">Tujuan Bisnis <span class="text-red-500">*</span></label>
            <input type="text" name="business_purpose" value="{{ old('business_purpose') }}" required maxlength="255" placeholder="Contoh: Perjalanan dinas kunjungan sekolah bulan Mei" class="block w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-[#215aac] focus:ring-[#215aac] shadow-sm">
        </div>
        
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-bold text-slate-800">Periode Mulai <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="date" name="expense_period_start" value="{{ old('expense_period_start') }}" required class="block w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-[#215aac] shadow-sm">
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-bold text-slate-800">Periode Selesai <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="date" name="expense_period_end" value="{{ old('expense_period_end') }}" required class="block w-full rounded-lg border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-[#215aac] shadow-sm">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-[#215aac] px-6 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-all">
                Simpan & Lanjut Isi Item <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
            <a href="{{ route('reimbursements.index') }}" class="inline-flex min-h-10 items-center rounded-lg border border-slate-200 bg-white px-5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
