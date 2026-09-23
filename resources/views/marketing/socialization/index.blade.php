@extends('layouts.dashboard')

@section('title', 'Sosialisasi & Lead')

@section('content')
@php
    $statusColors = [
        'draft'     => 'bg-gray-100 text-gray-700',
        'scheduled' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $statusLabels = [
        'draft'     => 'Draft',
        'scheduled' => 'Terjadwal',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
@endphp
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Sosialisasi & Lead</h1>
        @can('marketing.socialization.create')
        <a href="{{ route('socializations.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
            + Buat Sosialisasi
        </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white shadow rounded-lg mb-5 p-4">
        <form method="GET" action="{{ route('socializations.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="block border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach ($statusLabels as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if (request()->hasAny(['status', 'branch_id', 'area_id']))
                <a href="{{ route('socializations.index') }}" class="px-3 py-2 text-sm text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi / Institusi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($socializations as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $s->location_name }}</p>
                                @if ($s->institution)
                                    <p class="text-xs text-gray-500">{{ $s->institution->institution_name }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $s->branch->branch_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $s->schedules->min('schedule_date')?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$s->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$s->status] ?? $s->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $s->prospective_members_count ?? $s->prospectiveMembers->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('socializations.show', $s) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data sosialisasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($socializations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $socializations->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
