@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="{
    showConfirmModal: false,
    confirmTitle: '',
    confirmMessage: '',
    confirmActionUrl: '',
    confirmMethod: 'POST',
    confirmActionText: 'Konfirmasi'
}">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Offering & Onboarding</h1>
        </div>

        <!-- Right: Actions -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <!-- Usually generated automatically from offering acceptance -->
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-5 flex flex-wrap gap-2 items-center">
        <form method="GET" action="{{ route('recruitment.onboarding.index') }}" class="flex gap-2">
            <select name="status" class="form-select text-sm border-[color:var(--dash-input-border)] rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)]">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <!-- Table header -->
                <thead class="text-xs font-semibold uppercase text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)] border-t border-b border-[color:var(--dash-card-border)]">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Nama Kandidat</th>
                        <th class="px-6 py-3 text-left">Cabang / Posisi</th>
                        <th class="px-6 py-3 text-left">Tipe Karyawan</th>
                        <th class="px-6 py-3 text-left">Tanggal Mulai</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm divide-y divide-[color:var(--dash-card-border)]">
                    @forelse($onboardings as $onboarding)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-[color:var(--dash-text-heading)]">
                                {{ optional(optional($onboarding->application)->applicant)->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[color:var(--dash-text-muted)]">
                                {{ optional($onboarding->branch)->name ?? '-' }} <br>
                                <span class="text-xs">{{ optional($onboarding->position)->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize">{{ str_replace('_', ' ', $onboarding->employment_type ?? '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $onboarding->start_date ? $onboarding->start_date->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]',
                                        'pending_review' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $statusColors[$onboarding->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ str_replace('_', ' ', Str::title($onboarding->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('recruitment.onboarding.show', $onboarding->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-[color:var(--dash-text-muted)] text-center">Belum ada data onboarding.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[color:var(--dash-card-border)]">
            {{ $onboardings->links() }}
        </div>
    </div>
</div>
@endsection
