@extends('layouts.dashboard')

@section('title', 'Buat Tiket - MINDSIA')
@section('header_title', 'Buat Tiket Bantuan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('member.tickets.index') }}"
           class="dash-back flex items-center justify-center w-9 h-9 rounded-lg transition-colors cursor-pointer shrink-0"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
           title="Kembali ke Daftar Tiket">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-0.5"
               style="color: var(--dash-primary-text);">Pusat Bantuan</p>
            <h1 class="font-jakarta text-xl sm:text-2xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Buat Tiket Bantuan</h1>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-2xl p-4 shadow-sm text-sm"
             style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5">error</span>
                <ul class="list-disc pl-5 font-semibold space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('member.tickets.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">
                    Subjek <span style="color: var(--dash-status-danger-text);">*</span>
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    placeholder="Tulis subjek tiket Anda..."
                    class="block w-full rounded-xl px-3.5 py-2.5 text-sm transition-colors focus:outline-none"
                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">
                        Kategori <span style="color: var(--dash-status-danger-text);">*</span>
                    </label>
                    <select name="category" required
                        class="block w-full rounded-xl px-3.5 py-2.5 text-sm transition-colors focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                        <option value="" disabled selected>Pilih Kategori ...</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">
                        Prioritas <span style="color: var(--dash-status-danger-text);">*</span>
                    </label>
                    <select name="priority" required
                        class="block w-full rounded-xl px-3.5 py-2.5 text-sm transition-colors focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                        <option value="low" @selected(old('priority', 'low') === 'low')>Rendah</option>
                        <option value="medium" @selected(old('priority') === 'medium')>Sedang</option>
                        <option value="high" @selected(old('priority') === 'high')>Tinggi</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">
                    Pesan <span style="color: var(--dash-status-danger-text);">*</span>
                </label>
                <textarea name="message" rows="6" required minlength="10"
                    placeholder="Jelaskan masalah atau pertanyaan Anda secara detail..."
                    class="block w-full rounded-xl px-3.5 py-2.5 text-sm resize-none transition-colors focus:outline-none"
                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">{{ old('message') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6" style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('member.tickets.index') }}"
                   class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-xs font-semibold transition-colors cursor-pointer"
                   style="background-color: var(--dash-page-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-page-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-xs font-semibold text-white transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
