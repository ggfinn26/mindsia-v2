@extends('layouts.dashboard')

@section('title', 'Surat Masuk')
@section('header_title', 'Surat Masuk')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Surat Masuk</h1>
        </div>
        @can('letter.in.create')
            <a href="{{ route('in-letters.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">upload</span> Arsipkan Surat
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-semibold text-[color:var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3">Nomor / Perihal</th>
                        <th class="px-5 py-3">Pengirim</th>
                        <th class="px-5 py-3">Tgl Terima</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                    @php $letters = \App\Models\InLetter::latest('receive_date')->paginate(20); @endphp
                    @forelse ($letters as $letter)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[color:var(--dash-text-heading)]">{{ $letter->subject }}</p>
                                <p class="font-mono text-xs text-[color:var(--dash-text-faint)]">{{ $letter->letter_number ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">{{ $letter->sender_name }}</td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">{{ $letter->receive_date?->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('in-letters.show', $letter) }}" class="inline-flex min-h-9 items-center border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">inbox</span>
                                <p class="mt-2 font-semibold text-[color:var(--dash-text-body)]">Belum ada arsip surat masuk.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($letters->hasPages())
            <div class="border-t border-[color:var(--dash-card-border)] px-5 py-4">{{ $letters->links() }}</div>
        @endif
    </div>
</div>
@endsection
