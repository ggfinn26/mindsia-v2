@extends('layouts.dashboard')

@section('title', 'Buat Tiket - MINDSIA')
@section('header_title', 'Buat Tiket Bantuan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('member.tickets.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-[#215aac] transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Daftar Tiket
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900 mt-2">Buat Tiket Bantuan</h2>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] text-red-600 mt-0.5">error</span>
                <ul class="list-disc pl-5 font-medium space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <form action="{{ route('member.tickets.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Subjek <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    placeholder="Tulis subjek tiket Anda..."
                    class="block w-full rounded-lg border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#215aac] focus:ring-[#215aac] shadow-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" required class="block w-full rounded-lg border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#215aac] focus:ring-[#215aac] shadow-sm">
                        <option value="" disabled selected>Pilih kategori...</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Prioritas <span class="text-red-500">*</span></label>
                    <select name="priority" required class="block w-full rounded-lg border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#215aac] focus:ring-[#215aac] shadow-sm">
                        <option value="low" @selected(old('priority', 'low') === 'low')>Rendah</option>
                        <option value="medium" @selected(old('priority') === 'medium')>Sedang</option>
                        <option value="high" @selected(old('priority') === 'high')>Tinggi</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Pesan <span class="text-red-500">*</span></label>
                <textarea name="message" rows="6" required minlength="10"
                    placeholder="Jelaskan masalah atau pertanyaan Anda secara detail..."
                    class="block w-full rounded-lg border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#215aac] focus:ring-[#215aac] shadow-sm">{{ old('message') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-6">
                <a href="{{ route('member.tickets.index') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-lg border border-slate-200 bg-white px-5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[#215aac] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">send</span> Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
