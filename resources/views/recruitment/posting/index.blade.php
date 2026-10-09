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
            <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Job Posting</h1>
        </div>

        <!-- Right: Actions -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            @can('recruitment.posting.create')
            <!-- Normally created from Job Permintaan, so button might not be here, but let's leave it if applicable -->
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-5 flex flex-wrap gap-2 items-center">
        <form method="GET" action="{{ route('recruitment.postings.index') }}" class="flex gap-2">
            <select name="status" class="form-select text-sm border-[color:var(--dash-input-border)] rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
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
                        <th class="px-6 py-3 text-left">Judul Posting</th>
                        <th class="px-6 py-3 text-left">Cabang / Posisi</th>
                        <th class="px-6 py-3 text-left">Publish Date</th>
                        <th class="px-6 py-3 text-left">Closing Date</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm divide-y divide-[color:var(--dash-card-border)]">
                    @forelse($postings as $posting)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-[color:var(--dash-text-heading)]">{{ $posting->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-[color:var(--dash-text-muted)]">
                                {{ $posting->branch->branch_name ?? '-' }} <br>
                                <span class="text-xs">{{ $posting->position->position_name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $posting->publish_date ? $posting->publish_date->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $posting->closing_date ? $posting->closing_date->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]',
                                        'published' => 'bg-green-100 text-green-800',
                                        'closed' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $statusColors[$posting->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ Str::title($posting->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('recruitment.postings.show', $posting->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                @if($posting->status === 'draft')
                                    @can('recruitment.job_posting.update')
                                        <a href="{{ route('recruitment.postings.edit', $posting->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    @endcan
                                    @can('recruitment.job_posting.update')
                                        <button type="button" @click="showConfirmModal = true; confirmTitle = 'Hapus Posting'; confirmMessage = 'Yakin ingin menghapus job posting ini?'; confirmActionUrl = '{{ route('recruitment.postings.destroy', $posting->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'" class="text-red-600 hover:text-red-900">Hapus</button>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-[color:var(--dash-text-muted)] text-center">Belum ada data job posting.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[color:var(--dash-card-border)]">
            {{ $postings->links() }}
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus/Tindakan -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div x-show="showConfirmModal" class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="confirmMethod === 'DELETE' ? 'bg-red-100' : 'bg-yellow-100'">
                            <span class="material-symbols-outlined" :class="confirmMethod === 'DELETE' ? 'text-red-600' : 'text-yellow-600'">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-[color:var(--dash-text-heading)]" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-[color:var(--dash-text-muted)]" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[var(--dash-nav-hover-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="$refs.confirmForm.submit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" :class="confirmMethod === 'DELETE' ? 'bg-red-600 hover:bg-red-700' : 'bg-yellow-600 hover:bg-yellow-700'" x-text="confirmActionText"></button>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-[color:var(--dash-input-border)] shadow-sm px-4 py-2 bg-[var(--dash-card-bg)] text-base font-medium text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
