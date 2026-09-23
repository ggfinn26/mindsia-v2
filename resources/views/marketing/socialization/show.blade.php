@extends('layouts.dashboard')

@section('title', 'Detail Sosialisasi')

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
    $feeStatusColors = [
        'none'    => 'bg-gray-100 text-gray-600',
        'pending' => 'bg-yellow-100 text-yellow-700',
        'paid'    => 'bg-green-100 text-green-700',
    ];
    $feeStatusLabels = ['none' => 'Tidak Ada', 'pending' => 'Belum Bayar', 'paid' => 'Sudah Bayar'];
@endphp
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showAssignModal: false, showFeeModal: false, showCancelModal: false }">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('socializations.index') }}" class="text-gray-500 hover:text-gray-700">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Sosialisasi</h1>
        </div>
        <div class="flex gap-2">
            @can('marketing.socialization.update')
                @if ($socialization->status === 'draft')
                    <a href="{{ route('socializations.edit', $socialization) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Edit
                    </a>
                    <form action="{{ route('socializations.schedule', $socialization) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            Jadwalkan
                        </button>
                    </form>
                @endif
                @if (in_array($socialization->status, ['draft', 'scheduled']))
                    <button @click="showCancelModal = true" class="px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                        Batalkan
                    </button>
                @endif
            @endcan
            @can('marketing.socialization.delete')
                @if ($socialization->status === 'draft')
                    <form action="{{ route('socializations.destroy', $socialization) }}" method="POST" class="inline"
                          onsubmit="return confirm('Yakin hapus sosialisasi ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-md hover:bg-red-100">
                            Hapus
                        </button>
                    </form>
                @endif
            @endcan
        </div>
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

    {{-- Info utama --}}
    <div class="bg-white shadow rounded-lg p-6 mb-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Lokasi</p>
                <p class="text-sm font-semibold text-gray-900">{{ $socialization->location_name }}</p>
                @if ($socialization->institution)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $socialization->institution->institution_name }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Cabang / Area</p>
                <p class="text-sm text-gray-800">{{ $socialization->branch->branch_name ?? '-' }}
                    @if ($socialization->area) <span class="text-gray-400">·</span> {{ $socialization->area->name }} @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</p>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$socialization->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $statusLabels[$socialization->status] ?? $socialization->status }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Dibuat oleh</p>
                <p class="text-sm text-gray-800">{{ $socialization->createdBy->full_name ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Jadwal --}}
    <div class="bg-white shadow rounded-lg p-6 mb-5">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Jadwal Pelaksanaan</h2>
        @if ($socialization->schedules->isEmpty())
            <p class="text-sm text-gray-500">Belum ada jadwal. Jadwal dapat ditambah saat backend selesai.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="py-2 pr-4">Tanggal</th>
                        <th class="py-2 pr-4">Mulai</th>
                        <th class="py-2">Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($socialization->schedules as $schedule)
                        <tr>
                            <td class="py-2 pr-4">{{ $schedule->schedule_date->format('d M Y') }}</td>
                            <td class="py-2 pr-4">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                            <td class="py-2">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Marketer Ditugaskan --}}
    <div class="bg-white shadow rounded-lg p-6 mb-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-base font-semibold text-gray-800">Marketer Ditugaskan</h2>
            @can('marketing.socialization.assign')
                @if ($socialization->status !== 'completed' && $socialization->status !== 'cancelled')
                    <button @click="showAssignModal = true" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        + Tugaskan Marketer
                    </button>
                @endif
            @endcan
        </div>
        @if ($socialization->employeeSocializations->isEmpty())
            <p class="text-sm text-gray-500">Belum ada marketer ditugaskan.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="py-2 pr-4">Marketer</th>
                        <th class="py-2 pr-4">Status Penugasan</th>
                        <th class="py-2">Kelas Didapat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($socialization->employeeSocializations as $es)
                        <tr>
                            <td class="py-2 pr-4">{{ $es->employee->full_name ?? '-' }}</td>
                            <td class="py-2 pr-4">
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $es->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($es->status) }}
                                </span>
                            </td>
                            <td class="py-2">{{ $es->classes_obtained }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Lead / Prospek --}}
    <div class="bg-white shadow rounded-lg p-6 mb-5">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Lead / Prospek Member</h2>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $socialization->prospectiveMembers->count() }}</p>
                <p class="text-xs text-gray-500">lead tercatat</p>
            </div>
            @can('marketing.prospective_member.create')
                <a href="{{ route('prospective-members.create') }}?socialization_id={{ $socialization->id }}"
                   class="px-3 py-2 text-sm font-medium text-indigo-600 border border-indigo-300 rounded-md hover:bg-indigo-50">
                    + Tambah Lead
                </a>
            @endcan
        </div>
        @if ($socialization->prospectiveMembers->isNotEmpty())
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="py-2 pr-4">Nama</th>
                            <th class="py-2 pr-4">WhatsApp</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($socialization->prospectiveMembers->take(10) as $lead)
                            @php
                                $leadColors = [
                                    'ALMOST' => 'bg-yellow-100 text-yellow-700',
                                    'YES'    => 'bg-blue-100 text-blue-700',
                                    'FIXED'  => 'bg-green-100 text-green-700',
                                    'NO'     => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <tr>
                                <td class="py-2 pr-4 font-medium">{{ $lead->full_name }}</td>
                                <td class="py-2 pr-4 text-gray-600">{{ $lead->whatsapp_number }}</td>
                                <td class="py-2 pr-4">
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $leadColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $lead->status }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    <a href="{{ route('prospective-members.show', $lead) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($socialization->prospectiveMembers->count() > 10)
                    <p class="text-xs text-gray-500 mt-2">... dan {{ $socialization->prospectiveMembers->count() - 10 }} lead lainnya</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Fee Partner --}}
    @if ($socialization->partner_fee_amount)
        <div class="bg-white shadow rounded-lg p-6 mb-5">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-base font-semibold text-gray-800 mb-3">Fee Partner</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Jumlah</p>
                            <p class="font-semibold">Rp {{ number_format($socialization->partner_fee_amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Tenggat</p>
                            <p>{{ $socialization->partner_fee_due_date?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Status</p>
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $feeStatusColors[$socialization->partner_fee_status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $feeStatusLabels[$socialization->partner_fee_status] ?? $socialization->partner_fee_status }}
                            </span>
                        </div>
                        @if ($socialization->partner_fee_paid_at)
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Dibayar</p>
                                <p>{{ $socialization->partner_fee_paid_at->format('d M Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @can('marketing.socialization.partner_fee.update')
                    <button @click="showFeeModal = true" class="text-sm text-gray-600 hover:text-gray-800 border border-gray-300 px-3 py-1.5 rounded-md hover:bg-gray-50">
                        Update Status
                    </button>
                @endcan
            </div>
        </div>
    @endif

    {{-- Modal: Tugaskan Marketer --}}
    <div x-show="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div @click="showAssignModal = false" class="absolute inset-0 bg-gray-500 opacity-75"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 z-10">
            <form action="{{ route('socializations.assign-employee', $socialization) }}" method="POST" class="p-6">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tugaskan Marketer</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Marketer</label>
                    <select name="employee_id" required
                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach (\App\Models\Employee::orderBy('full_name')->get() as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showAssignModal = false" class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Tugaskan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Update Fee Status --}}
    <div x-show="showFeeModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div @click="showFeeModal = false" class="absolute inset-0 bg-gray-500 opacity-75"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 z-10">
            <form action="{{ route('socializations.partner-fee', $socialization) }}" method="POST" class="p-6">
                @csrf @method('PATCH')
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Status Fee Partner</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="partner_fee_status" required
                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="none" {{ $socialization->partner_fee_status === 'none' ? 'selected' : '' }}>Tidak Ada</option>
                        <option value="pending" {{ $socialization->partner_fee_status === 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="paid" {{ $socialization->partner_fee_status === 'paid' ? 'selected' : '' }}>Sudah Bayar</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar (jika sudah bayar)</label>
                    <input type="date" name="partner_fee_paid_at"
                        value="{{ $socialization->partner_fee_paid_at?->format('Y-m-d') }}"
                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showFeeModal = false" class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Konfirmasi Batalkan --}}
    <div x-show="showCancelModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div @click="showCancelModal = false" class="absolute inset-0 bg-gray-500 opacity-75"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 z-10 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Batalkan Sosialisasi?</h3>
            <p class="text-sm text-gray-600 mb-5">Sosialisasi yang sudah dibatalkan tidak bisa dikembalikan ke status sebelumnya.</p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showCancelModal = false" class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">Kembali</button>
                <form action="{{ route('socializations.cancel', $socialization) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700">Ya, Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
