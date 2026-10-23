@extends('layouts.dashboard')

@section('title', 'Surat Masuk')
@section('header_title', 'Surat Masuk')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Surat Masuk</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Arsip digital surat yang diterima dari pihak eksternal.</p>
        </div>
        @can('letter.in.create')
            <a href="{{ route('in-letters.create') }}"
               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">upload</span>
                Arsipkan Surat
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="rounded-2xl border overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Perihal &amp; Nomor</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Pengirim &amp; Tanggal Terima</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $letters = \App\Models\InLetter::latest('receive_date')->paginate(20); @endphp
                    @forelse ($letters as $letter)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $letter->subject }}</p>
                                <p class="font-mono text-xs mt-0.5" style="color: var(--dash-text-faint);">{{ $letter->letter_number ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <p style="color: var(--dash-text-body);">{{ $letter->sender_name }}</p>
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Diterima {{ $letter->receive_date?->format('d M Y') }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('in-letters.show', $letter) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">inbox</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada arsip surat masuk.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($letters->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">{{ $letters->links() }}</div>
        @endif
    </div>
</div>
@endsection
