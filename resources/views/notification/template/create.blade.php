@extends('layouts.dashboard')

@section('header_title', 'Buat Template Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('notification-templates.index') }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Template
        </a>
        <h1 class="mt-2 font-jakarta text-2xl font-bold tracking-tight text-slate-900">Buat Template Notifikasi</h1>
    </div>

    @if($errors->any())
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <form action="{{ route('notification-templates.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Template Key <span class="text-red-500">*</span></label>
                <input name="template_key" type="text" value="{{ old('template_key') }}"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] font-mono"
                    placeholder="contoh: payslip_ready_inapp" required />
                <p class="mt-1 text-xs text-slate-400">Unik, format snake_case. Konvensi: event_channel (mis. leave_approved_email)</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe / Channel <span class="text-red-500">*</span></label>
                <select name="type" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required>
                    <option value="">Pilih tipe...</option>
                    <option value="in-app" {{ old('type') === 'in-app' ? 'selected' : '' }}>In-App</option>
                    <option value="email" {{ old('type') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="telegram" {{ old('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Subject <span class="text-slate-400 text-xs font-normal">(wajib untuk Email)</span></label>
                <input name="subject" type="text" value="{{ old('subject') }}"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                    placeholder="Contoh: Slip Gaji Bulan @{{bulan}} Telah Tersedia" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Body / Isi Pesan <span class="text-red-500">*</span></label>
                <textarea name="body" rows="8"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                    placeholder="Halo @{{nama_pegawai}}, slip gaji Anda untuk bulan @{{bulan}} telah tersedia." required>{{ old('body') }}</textarea>
                <p class="mt-1 text-xs text-slate-400">Gunakan <code class="bg-slate-100 px-1 rounded">@{{nama_variabel}}</code> untuk placeholder dinamis.</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('notification-templates.index') }}" class="rounded-lg border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#215aac] px-5 py-2 text-sm font-bold text-white hover:bg-[#194a91] transition-colors">Simpan Template</button>
            </div>
        </form>
    </div>
</div>
@endsection
