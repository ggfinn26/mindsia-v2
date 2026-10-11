@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Session Member TOEFL</h1>
        </div>

        <!-- Right: Actions -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <!-- Filter or Actions could be placed here -->
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-5 flex flex-wrap gap-2 items-center">
        <form method="GET" action="{{ route('toefl.member-session.index') }}" class="flex gap-2">
            <select name="status" class="form-select text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <!-- Table header -->
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Nama Member</th>
                        <th class="px-6 py-3 text-left">Kontak / Akun</th>
                        <th class="px-6 py-3 text-left">Nama Test</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Skor Akhir</th>
                        <th class="px-6 py-3 text-left">Tanggal Tes</th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($sessions as $session)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                {{ optional($session->member)->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                {{ optional($session->member)->email ?? '-' }}<br>
                                <span class="text-xs">{{ optional($session->member)->whatsapp_number ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                {{ optional($session->test)->test_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'expired' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $statusColors[$session->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ Str::title(str_replace('_', ' ', $session->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                {{ $session->score_total ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                @if($session->started_at)
                                    Mulai: {{ $session->started_at->format('d M Y H:i') }} <br>
                                @endif
                                @if($session->completed_at)
                                    <span class="text-xs">Selesai: {{ $session->completed_at->format('d M Y H:i') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data session member.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
@endsection
