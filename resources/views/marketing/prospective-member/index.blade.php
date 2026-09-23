@extends('layouts.dashboard')

@section('title', 'Prospek Member (Lead)')

@section('content')
@php
    $statusColors = [
        'ALMOST' => 'bg-yellow-100 text-yellow-700',
        'YES'    => 'bg-blue-100 text-blue-700',
        'FIXED'  => 'bg-green-100 text-green-700',
        'NO'     => 'bg-red-100 text-red-700',
    ];
@endphp
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Prospek Member (Lead)</h1>
        @can('marketing.prospective_member.create')
        <a href="{{ route('prospective-members.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
            + Tambah Lead
        </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white shadow rounded-lg mb-5 p-4">
        <form method="GET" action="{{ route('prospective-members.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="block border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach (array_keys($statusColors) as $val)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $val }}</option>
                    @endforeach
                </select>
            </div>
            @if (request()->hasAny(['status', 'socialization_id']))
                <a href="{{ route('prospective-members.index') }}" class="px-3 py-2 text-sm text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WhatsApp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asal Sekolah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($leads as $lead)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $lead->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <a href="https://wa.me/{{ ltrim($lead->whatsapp_number, '0') }}" target="_blank"
                                   class="text-green-600 hover:text-green-800">
                                    {{ $lead->whatsapp_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $lead->institution?->institution_name ?? $lead->institution_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $lead->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $lead->capturedBy->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('prospective-members.show', $lead) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data prospek.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leads->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leads->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
