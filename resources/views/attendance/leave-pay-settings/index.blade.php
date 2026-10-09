@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl text-[var(--dash-text-heading)] font-bold">Pengaturan Potongan Izin / Sakit / Cuti</h1>
        <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Default: semua tipe izin <strong>dibayar penuh</strong> jika tidak ada pengaturan untuk posisi tersebut. BOARD selalu dibayar penuh.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded bg-emerald-50 border border-emerald-200 text-emerald-700 [.mode-dark_&]:bg-emerald-500/10 [.mode-dark_&]:border-emerald-500/20 [.mode-dark_&]:text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-[var(--dash-card-bg)] shadow-sm rounded-lg border border-[var(--dash-card-border)] overflow-x-auto">
        <table class="table-auto w-full text-sm">
            <thead class="bg-[var(--dash-page-bg)] border-b border-[var(--dash-card-border)]">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-[var(--dash-text-muted)] w-1/3">Posisi</th>
                    <th class="px-4 py-3 text-center font-semibold text-[var(--dash-text-muted)]">Izin</th>
                    <th class="px-4 py-3 text-center font-semibold text-[var(--dash-text-muted)]">Sakit</th>
                    <th class="px-4 py-3 text-center font-semibold text-[var(--dash-text-muted)]">Cuti</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--dash-card-border)]">
                @foreach($positions as $position)
                    @php
                        $types = [
                            'permission' => 'Izin',
                            'sick'       => 'Sakit',
                            'leave'      => 'Cuti',
                        ];
                    @endphp
                    <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                        <td class="px-4 py-3 font-medium text-[var(--dash-text-heading)]">
                            {{ $position->position_name }}
                        </td>
                        @foreach($types as $typeKey => $typeLabel)
                            @php
                                $key = $position->id . '_' . $typeKey;
                                $isPaid = isset($settings[$key]) ? $settings[$key]->is_paid : true;
                            @endphp
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('leave-pay-settings.upsert') }}">
                                    @csrf
                                    <input type="hidden" name="position_id" value="{{ $position->id }}">
                                    <input type="hidden" name="leave_type" value="{{ $typeKey }}">
                                    <input type="hidden" name="is_paid" value="{{ $isPaid ? '0' : '1' }}">
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border transition-opacity hover:opacity-80
                                            {{ $isPaid
                                                ? 'bg-emerald-50 border-emerald-200 text-emerald-700 [.mode-dark_&]:bg-emerald-500/10 [.mode-dark_&]:border-emerald-500/20 [.mode-dark_&]:text-emerald-400'
                                                : 'bg-red-50 border-red-200 text-red-700 [.mode-dark_&]:bg-red-500/10 [.mode-dark_&]:border-red-500/20 [.mode-dark_&]:text-red-400' }}"
                                        title="Klik untuk ubah ke {{ $isPaid ? 'tidak dibayar' : 'dibayar' }}">
                                        @if($isPaid)
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            Dibayar
                                        @else
                                            <span class="material-symbols-outlined text-[14px]">cancel</span>
                                            Tidak Dibayar
                                        @endif
                                    </button>
                                </form>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="mt-3 text-xs text-[var(--dash-text-faint)]">Klik badge untuk toggle. Perubahan berlaku mulai periode payroll berikutnya.</p>
</div>
@endsection
