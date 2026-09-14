@extends('layouts.dashboard')

@section('title', 'Jadwal Kerja & Hari Libur')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-8">

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- ── Aturan Jadwal Kerja ── --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Aturan Jadwal Kerja</h2>
                <p class="text-sm text-gray-500 mt-0.5">Aturan jam kerja yang bisa di-assign per role / posisi / pegawai.</p>
            </div>
            @can('attendance.schedule_rule.manage')
                <a href="{{ route('work-schedule-rules.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Aturan
                </a>
            @endcan
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            @if ($rules->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <span class="material-symbols-outlined text-[48px] mb-2 block">schedule</span>
                    <p class="font-medium">Belum ada aturan jadwal kerja.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Aturan</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Kerja</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Toleransi Telat</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Wajib</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($rules as $rule)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $rule->setting_name }}</td>
                                    <td class="px-5 py-3.5 text-gray-600">
                                        {{ \Carbon\Carbon::parse($rule->start_time)->format('H:i') }} –
                                        {{ \Carbon\Carbon::parse($rule->end_time)->format('H:i') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600">{{ $rule->late_tolerance_minutes }} menit</td>
                                    <td class="px-5 py-3.5">
                                        @if ($rule->is_required)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Wajib</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Opsional</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right space-x-2">
                                        <a href="{{ route('work-schedule-rules.show', $rule) }}" class="text-blue-600 hover:underline text-xs font-medium">Detail</a>
                                        @can('attendance.schedule_rule.manage')
                                            <a href="{{ route('work-schedule-rules.edit', $rule) }}" class="text-yellow-600 hover:underline text-xs font-medium">Edit</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($rules->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">{{ $rules->links() }}</div>
                @endif
            @endif
        </div>
    </div>

    {{-- ── Manajemen Hari Libur ── --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Hari Libur</h2>
                <p class="text-sm text-gray-500 mt-0.5">Hari libur nasional dan khusus yang mempengaruhi perhitungan hari kerja.</p>
            </div>
            @can('attendance.holiday.manage')
                <a href="{{ route('holidays.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Hari Libur
                </a>
            @endcan
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            @if ($holidays->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <span class="material-symbols-outlined text-[48px] mb-2 block">event_busy</span>
                    <p class="font-medium">Belum ada hari libur terdaftar.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Hari Libur</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Durasi</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($holidays as $holiday)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $holiday->holiday_name }}</td>
                                    <td class="px-5 py-3.5 text-gray-600">{{ $holiday->holiday_start_date->format('d M Y') }}</td>
                                    <td class="px-5 py-3.5 text-gray-600">{{ $holiday->holiday_end_date->format('d M Y') }}</td>
                                    <td class="px-5 py-3.5 text-gray-600">
                                        @php $days = $holiday->holiday_start_date->diffInDays($holiday->holiday_end_date) + 1 @endphp
                                        {{ $days }} hari
                                    </td>
                                    <td class="px-5 py-3.5 text-right space-x-2">
                                        @can('attendance.holiday.manage')
                                            <a href="{{ route('holidays.edit', $holiday) }}" class="text-yellow-600 hover:underline text-xs font-medium">Edit</a>
                                            <form action="{{ route('holidays.destroy', $holiday) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Hapus hari libur ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-xs font-medium">Hapus</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($holidays->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">{{ $holidays->appends(request()->except('hpage'))->links() }}</div>
                @endif
            @endif
        </div>
    </div>

</div>
@endsection
