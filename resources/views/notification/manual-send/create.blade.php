@extends('layouts.dashboard')

@section('header_title', 'Kirim Notifikasi Manual')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{
    recipientType: '{{ old('recipient_type', 'employee') }}',
    templateKey: '{{ old('template_key') }}'
}">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Notifikasi</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Kirim Notifikasi Manual</h1>
        <p class="mt-1 text-sm text-slate-500">Kirim notifikasi langsung ke penerima tertentu tanpa melalui routing otomatis.</p>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <form action="{{ route('notification-manual-send.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Template <span class="text-red-500">*</span></label>
                <select name="template_key" x-model="templateKey"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required>
                    <option value="">Pilih template...</option>
                    @foreach($templates as $tpl)
                    <option value="{{ $tpl->template_key }}" {{ old('template_key') === $tpl->template_key ? 'selected' : '' }}>
                        {{ $tpl->template_key }} ({{ $tpl->type }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe Penerima <span class="text-red-500">*</span></label>
                    <select name="recipient_type" x-model="recipientType"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required>
                        <option value="employee">Pegawai</option>
                        <option value="member">Member</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ID Penerima <span class="text-red-500">*</span></label>
                    <input name="recipient_id" type="number" value="{{ old('recipient_id') }}" min="1"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                        placeholder="Masukkan ID..." required />
                    <p class="mt-1 text-xs text-slate-400" x-text="recipientType === 'employee' ? 'ID dari tabel employees' : 'ID dari tabel members_data'"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Payload Variabel <span class="text-slate-400 text-xs font-normal">(opsional)</span></label>
                <p class="text-xs text-slate-400 mb-2">Isi nilai untuk placeholder di template. Format: key=value per baris.</p>
                <div id="payload-container" class="space-y-2">
                    <div class="flex gap-2">
                        <input name="payload[nama_pegawai]" type="text" placeholder="nama_pegawai"
                            class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm font-mono focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-400">Variabel yang tidak diisi akan tetap sebagai placeholder di pesan.</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#215aac] px-5 py-2 text-sm font-bold text-white hover:bg-[#194a91] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">send</span> Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
