@extends('layouts.dashboard')

@section('header_title', 'Tambah Routing Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('system.notification-routings.index') }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Routing
        </a>
        <h1 class="mt-2 font-jakarta text-2xl font-bold tracking-tight text-slate-900">Tambah Aturan Routing</h1>
    </div>

    @if($errors->any())
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <form action="{{ route('system.notification-routings.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Event Key <span class="text-red-500">*</span></label>
                <input name="event_key" type="text" value="{{ old('event_key') }}"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-mono focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                    placeholder="contoh: leave_request_created" required />
                <p class="mt-1 text-xs text-slate-400">Nama event yang di-trigger oleh sistem (format snake_case).</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Target Posisi <span class="text-slate-400 text-xs font-normal">(kosong = semua posisi)</span></label>
                <select name="position_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                    <option value="">Semua Posisi</option>
                    @foreach($positions as $pos)
                    <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Channel <span class="text-red-500">*</span></label>
                    <select name="channel" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required>
                        <option value="">Pilih channel...</option>
                        <option value="in-app" {{ old('channel') === 'in-app' ? 'selected' : '' }}>In-App</option>
                        <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="telegram" {{ old('channel') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Scope <span class="text-red-500">*</span></label>
                    <select name="scope" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required>
                        <option value="">Pilih scope...</option>
                        <option value="global" {{ old('scope') === 'global' ? 'selected' : '' }}>Global</option>
                        <option value="branch" {{ old('scope') === 'branch' ? 'selected' : '' }}>Branch</option>
                        <option value="area" {{ old('scope') === 'area' ? 'selected' : '' }}>Area</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi <span class="text-slate-400 text-xs font-normal">(opsional)</span></label>
                <input name="description" type="text" value="{{ old('description') }}"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                    placeholder="Contoh: Notifikasi ke HR saat ada pengajuan cuti baru" />
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-slate-300 text-[#215aac]"
                    {{ old('is_active', '1') ? 'checked' : '' }} />
                <label for="is_active" class="text-sm font-semibold text-slate-700">Aktifkan aturan ini</label>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('system.notification-routings.index') }}" class="rounded-lg border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#215aac] px-5 py-2 text-sm font-bold text-white hover:bg-[#194a91] transition-colors">Simpan Aturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
