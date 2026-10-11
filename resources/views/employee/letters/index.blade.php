@extends('layouts.dashboard')

@section('title', 'Surat Saya')
@section('header_title', 'Surat Saya')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    <div>
        <p class="text-sm font-medium text-[#725c00]">Profil Saya</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Surat Saya</h2>
        <p class="mt-1 text-sm text-slate-500">Surat resmi dan dokumen yang berkaitan dengan kamu.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Surat Peringatan (SP) --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-rose-500">warning</span>
                <h3 class="font-jakarta text-sm font-bold text-slate-900">Surat Peringatan (SP)</h3>
            </div>
            @if ($warningLetters->isNotEmpty())
                <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800">{{ $warningLetters->count() }}</span>
            @endif
        </div>
        @if ($warningLetters->isEmpty())
            <div class="px-5 py-10 text-center">
                <span class="material-symbols-outlined text-3xl text-slate-300">task_alt</span>
                <p class="mt-2 text-sm font-semibold text-slate-600">Tidak ada surat peringatan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Level</th>
                            <th class="px-5 py-3">Nomor Surat</th>
                            <th class="px-5 py-3">Tanggal Terbit</th>
                            <th class="px-5 py-3">Berlaku Hingga</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Unduh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($warningLetters as $sp)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-800">SP {{ $sp->sp_level }}</span>
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-slate-700">{{ $sp->letter_number ?? '—' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $sp->issued_at?->format('d M Y') ?? '—' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $sp->expires_at?->format('d M Y') ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    @if ($sp->is_active)
                                        <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-800">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($sp->telegram_file_id)
                                        <a href="{{ route('employee.my-letters.warning.download', $sp) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            <span class="material-symbols-outlined text-[14px]">download</span> PDF
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Surat Keluar (Generate) --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#215aac]">mail</span>
                <h3 class="font-jakarta text-sm font-bold text-slate-900">Surat Resmi</h3>
            </div>
            @if ($generatedLetters->isNotEmpty())
                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800">{{ $generatedLetters->count() }}</span>
            @endif
        </div>
        @if ($generatedLetters->isEmpty())
            <div class="px-5 py-10 text-center">
                <span class="material-symbols-outlined text-3xl text-slate-300">mail</span>
                <p class="mt-2 text-sm font-semibold text-slate-600">Belum ada surat resmi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Nomor / Perihal</th>
                            <th class="px-5 py-3">Jenis</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3 text-right">Unduh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($generatedLetters as $letter)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $letter->subject ?? $letter->letter_type }}</p>
                                    <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $letter->letter_number ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $letter->template?->template_name ?? $letter->letter_type }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $letter->published_at?->format('d M Y') ?? $letter->letter_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-5 py-4 text-right">
                                    @if ($letter->telegram_file_id)
                                        <a href="{{ route('out-letters-generate.download', $letter) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            <span class="material-symbols-outlined text-[14px]">download</span> PDF
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Surat Kontrak --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">description</span>
                <h3 class="font-jakarta text-sm font-bold text-slate-900">Surat Kontrak</h3>
            </div>
            @if ($contracts->isNotEmpty())
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">{{ $contracts->count() }}</span>
            @endif
        </div>
        @if ($contracts->isEmpty())
            <div class="px-5 py-10 text-center">
                <span class="material-symbols-outlined text-3xl text-slate-300">description</span>
                <p class="mt-2 text-sm font-semibold text-slate-600">Belum ada dokumen kontrak tersimpan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Tipe Kontrak</th>
                            <th class="px-5 py-3">Mulai</th>
                            <th class="px-5 py-3">Berakhir</th>
                            <th class="px-5 py-3 text-right">File</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($contracts as $contract)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4 font-semibold text-slate-900">
                                    {{ match($contract->type_employment) {
                                        'permanent' => 'Tetap (Permanent)',
                                        'contract' => 'Kontrak',
                                        default => ucfirst($contract->type_employment ?? '—'),
                                    } }}
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $contract->contract_start_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $contract->contract_end_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-5 py-4 text-right">
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-700">
                                        <span class="material-symbols-outlined text-[14px]">description</span> Ada
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
