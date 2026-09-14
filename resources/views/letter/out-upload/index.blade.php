@extends('layouts.dashboard')

@section('title', 'Arsip Surat Keluar (Upload)')
@section('header_title', 'Surat Keluar Upload')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Surat</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Arsip Surat Keluar</h2>
        </div>
        @can('letter.out.create')
            <a href="{{ route('out-letters-upload.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">upload</span> Upload Surat
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
                        <th class="px-5 py-3">Nomor / Perihal</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Penerima</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $letters = \App\Models\OutLetterViaUpload::latest()->paginate(20); @endphp
                    @forelse ($letters as $letter)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $letter->subject }}</p>
                                <p class="font-mono text-xs text-slate-400">{{ $letter->letter_number ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $letter->letter_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $letter->recipient ?? '—' }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('out-letters-upload.show', $letter) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">outbox</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada arsip surat keluar.</p>
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
