@extends('layouts.dashboard')

@section('title', 'Buat Tiket - MINDSIA')
@section('header_title', 'Buat Tiket Bantuan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('member.tickets.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-primary-text,#215aac)] transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Daftar Tiket
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight mt-2" style="color: var(--dash-text-heading);">Buat Tiket Bantuan</h1>
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

    <div class="overflow-hidden rounded-xl border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <form action="{{ route('member.tickets.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-[color:var(--dash-text-body)] mb-1.5">Subjek <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    placeholder="Tulis subjek tiket Anda..."
                    class="block w-full rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)] shadow-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-[color:var(--dash-text-body)] mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" required class="block w-full rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)] shadow-sm">
                        <option value="" disabled selected>Pilih kategori...</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-[color:var(--dash-text-body)] mb-1.5">Prioritas <span class="text-red-500">*</span></label>
                    <select name="priority" required class="block w-full rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)] shadow-sm">
                        <option value="low" @selected(old('priority', 'low') === 'low')>Rendah</option>
                        <option value="medium" @selected(old('priority') === 'medium')>Sedang</option>
                        <option value="high" @selected(old('priority') === 'high')>Tinggi</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-[color:var(--dash-text-body)] mb-1.5">Pesan <span class="text-red-500">*</span></label>
                <textarea name="message" rows="6" required minlength="10"
                    placeholder="Jelaskan masalah atau pertanyaan Anda secara detail..."
                    class="block w-full rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)] shadow-sm">{{ old('message') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 border-t border-[color:var(--dash-card-border)] pt-6">
                <a href="{{ route('member.tickets.index') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-lg border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-5 text-sm font-bold text-[color:var(--dash-text-body)] shadow-sm hover:bg-[var(--dash-nav-hover-bg)] transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[var(--dash-primary,#215aac)] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">send</span> Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
