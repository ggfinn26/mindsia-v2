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
            <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">
                {{ request('status') == 'interview' ? 'Psikotest & Interview' : 'Aplikasi Lowongan' }}
            </h1>
        </div>

        <!-- Right: Actions -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            @can('recruitment.job_application.create')
            <button type="button" @click="$dispatch('open-manual-application-modal')" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                <span class="hidden xs:block ml-2">Tambah Lamaran Manual</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-5 flex flex-wrap gap-2 items-center">
        <form method="GET" action="{{ route('recruitment.application.index') }}" class="flex gap-2">
            <!-- If we are in interview mode, keep it -->
            @if(request()->has('status') && request('status') == 'interview')
                <input type="hidden" name="status" value="interview">
            @else
                <select name="status" class="form-select text-sm border-[color:var(--dash-input-border)] rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="applied" {{ request('status') === 'applied' ? 'selected' : '' }}>Applied</option>
                    <option value="screening" {{ request('status') === 'screening' ? 'selected' : '' }}>Screening</option>
                    <option value="interview" {{ request('status') === 'interview' ? 'selected' : '' }}>Interview / Psikotest</option>
                    <option value="offering" {{ request('status') === 'offering' ? 'selected' : '' }}>Offering</option>
                    <option value="hired" {{ request('status') === 'hired' ? 'selected' : '' }}>Hired</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            @endif

            <select name="source" class="form-select text-sm border-[color:var(--dash-input-border)] rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="this.form.submit()">
                <option value="">Semua Sumber</option>
                <option value="job_posting" {{ request('source') === 'job_posting' ? 'selected' : '' }}>Portal Karir</option>
                <option value="referral" {{ request('source') === 'referral' ? 'selected' : '' }}>Referral</option>
                <option value="walk_in" {{ request('source') === 'walk_in' ? 'selected' : '' }}>Walk-In</option>
                <option value="archive" {{ request('source') === 'archive' ? 'selected' : '' }}>Archive (Reserve)</option>
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
                        <th class="px-6 py-3 text-left">Nama Pelamar</th>
                        <th class="px-6 py-3 text-left">Posisi Dilamar</th>
                        <th class="px-6 py-3 text-left">Tanggal Apply</th>
                        <th class="px-6 py-3 text-left">Sumber</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm divide-y divide-[color:var(--dash-card-border)]">
                    @forelse($applications as $application)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-[color:var(--dash-text-heading)]">
                                {{ $application->applicant->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[color:var(--dash-text-muted)]">
                                {{ $application->posting->title ?? 'Reserve/Manual' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $application->applied_at ? $application->applied_at->format('d M Y H:i') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="capitalize">{{ str_replace('_', ' ', $application->application_source) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'applied' => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]',
                                        'screening' => 'bg-blue-100 text-blue-800',
                                        'interview' => 'bg-yellow-100 text-yellow-800',
                                        'offering' => 'bg-purple-100 text-purple-800',
                                        'hired' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $statusColors[$application->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ Str::title($application->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('recruitment.application.show', $application->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                @can('recruitment.job_application.review')
                                    @if($application->status !== 'rejected' && $application->status !== 'hired')
                                        <button type="button" @click="showConfirmModal = true; confirmTitle = 'Tolak Lamaran'; confirmMessage = 'Yakin ingin menolak pelamar ini?'; confirmActionUrl = '{{ route('recruitment.application.reject', $application->id) }}'; confirmMethod = 'POST'; confirmActionText = 'Tolak'" class="text-red-600 hover:text-red-900">Tolak</button>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-[color:var(--dash-text-muted)] text-center">Belum ada data lamaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[color:var(--dash-card-border)]">
            {{ $applications->links() }}
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus/Tindakan -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div x-show="showConfirmModal" class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST">
                    @csrf
                    <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10 bg-red-100">
                                <span class="material-symbols-outlined text-red-600">warning</span>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-[color:var(--dash-text-heading)]" x-text="confirmTitle"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-[color:var(--dash-text-muted)]" x-text="confirmMessage"></p>
                                </div>
                                <div class="mt-4" x-show="confirmActionText === 'Tolak'">
                                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Alasan Penolakan</label>
                                    <textarea name="reason" rows="3" class="mt-1 block w-full border border-[color:var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[var(--dash-nav-hover-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm bg-red-600 hover:bg-red-700" x-text="confirmActionText"></button>
                        <button type="button" @click="showConfirmModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-[color:var(--dash-input-border)] shadow-sm px-4 py-2 bg-[var(--dash-card-bg)] text-base font-medium text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
