@extends('layouts.dashboard')

@section('title', 'Riwayat Registrasi - MINDSIA')
@section('header_title', 'Riwayat Registrasi')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold font-jakarta text-[color:var(--dash-text-heading)]">Riwayat Registrasi</h2>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">Daftar program yang pernah Anda daftarkan.</p>
        </div>
    </div>

    @if($registrations->isEmpty())
        <div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-10 flex flex-col items-center justify-center text-center">
            <div class="h-20 w-20 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#5586DB] text-4xl">history_edu</span>
            </div>
            <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-2">Belum Ada Riwayat Registrasi</h3>
            <p class="text-[color:var(--dash-text-muted)] max-w-md mx-auto mb-6">Anda belum pernah mendaftar ke program apa pun.</p>
            <a href="{{ route('member.program.browse') }}" class="px-5 py-2.5 bg-[#5586DB] text-white font-medium rounded-lg hover:bg-[#4370BC] transition-colors shadow-sm">
                Lihat Katalog Program
            </a>
        </div>
    @else
        <div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] overflow-hidden">
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Program</th>
                        <th class="px-6 py-3">Skema Bayar</th>
                        <th class="px-6 py-3">Total Tagihan</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                    @foreach($registrations as $registration)
                        <tr class="hover:bg-gray-50/70">
                            <td class="px-6 py-4 font-medium text-[color:var(--dash-text-heading)]">
                                {{ $registration->program?->program_name ?? $registration->receipt_program_name }}
                            </td>
                            <td class="px-6 py-4 text-[color:var(--dash-text-body)]">
                                {{ $registration->receipt_payment_plan_label ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-[color:var(--dash-text-heading)]">
                                Rp {{ number_format($registration->final_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $registration->status_color }}">
                                    {{ $registration->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[color:var(--dash-text-muted)]">
                                {{ $registration->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
