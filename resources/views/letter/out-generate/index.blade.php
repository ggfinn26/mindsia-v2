@extends('layouts.dashboard')

@section('title', 'Surat Keluar (Generate)')
@section('header_title', 'Surat Keluar')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Surat</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Surat Keluar (Generate)</h2>
        </div>
        @can('letter.generate.create')
            <a href="{{ route('out-letters-generate.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">draw</span> Buat Surat
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nomor / Subjek</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Penerima</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $letters = \App\Models\OutLetterViaGenerate::with('template')->latest()->paginate(20); @endphp
                    @forelse ($letters as $letter)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $letter->subject ?? $letter->letter_type }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $letter->letter_number ?? 'Draft' }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $letter->letter_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $letter->recipient ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $letter->isDraft() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $letter->isDraft() ? 'Draft' : 'Terbit' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('out-letters-generate.show', $letter) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">send</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada surat keluar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($letters->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $letters->links() }}</div>
        @endif
    </div>
</div>
@endsection
