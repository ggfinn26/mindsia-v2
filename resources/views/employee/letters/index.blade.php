@extends('layouts.dashboard')

@section('title', 'Surat Saya')
@section('header_title', 'Surat Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Profil Saya</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Surat Saya</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Surat resmi dan dokumen yang berkaitan dengan Anda.</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Surat Peringatan (SP) ── --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: #dc2626;">warning</span>
                <h2 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Surat Peringatan (SP)</h2>
            </div>
            @if($warningLetters->isNotEmpty())
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ $warningLetters->count() }}</span>
            @endif
        </div>

        @if($warningLetters->isEmpty())
            <div class="px-5 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">task_alt</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Tidak ada surat peringatan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Level</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Nomor Surat</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Tanggal Terbit</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Berlaku Hingga</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Unduh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($warningLetters as $sp)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-800">SP {{ $sp->sp_level }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-xs" style="color: var(--dash-text-body);">{{ $sp->letter_number ?? '-' }}</td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $sp->issued_at?->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $sp->expires_at?->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                @if($sp->is_active)
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if($sp->telegram_file_id)
                                    <a href="{{ route('employee.my-letters.warning.download', $sp) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">download</span>PDF
                                    </a>
                                @else
                                    <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Surat Resmi ── --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">mail</span>
                <h2 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Surat Resmi</h2>
            </div>
            @if($generatedLetters->isNotEmpty())
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $generatedLetters->count() }}</span>
            @endif
        </div>

        @if($generatedLetters->isEmpty())
            <div class="px-5 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">mail</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada surat resmi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Nomor / Perihal</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Jenis</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Tanggal</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Unduh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($generatedLetters as $letter)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $letter->subject ?? $letter->letter_type }}</p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-faint);">{{ $letter->letter_number ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $letter->template?->template_name ?? $letter->letter_type }}</td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $letter->published_at?->format('d M Y') ?? $letter->letter_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right">
                                @if($letter->telegram_file_id)
                                    <a href="{{ route('out-letters-generate.download', $letter) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">download</span>PDF
                                    </a>
                                @else
                                    <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Surat Kontrak ── --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: #059669;">description</span>
                <h2 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Surat Kontrak</h2>
            </div>
            @if($contracts->isNotEmpty())
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">{{ $contracts->count() }}</span>
            @endif
        </div>

        @if($contracts->isEmpty())
            <div class="px-5 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">description</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada dokumen kontrak tersimpan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Tipe Kontrak</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Mulai</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Berakhir</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5 font-semibold" style="color: var(--dash-text-heading);">
                                {{ match($contract->type_employment) {
                                    'permanent' => 'Tetap (Permanent)',
                                    'contract'  => 'Kontrak',
                                    default     => \App\Support\EnumLabels::get('employment_type', $contract->type_employment),
                                } }}
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $contract->contract_start_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $contract->contract_end_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold"
                                      style="color: #059669;">
                                    <span class="material-symbols-outlined text-[14px]">description</span>Ada
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
