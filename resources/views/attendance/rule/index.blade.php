@extends('layouts.dashboard')

@section('title', 'Kelola Sanksi Absen')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showConfirmModal: false, confirmTitle: '', confirmActionUrl: '' }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Kelola Sanksi Absen</h1>
        <a href="{{ route('attendance-rules.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Tambah Aturan</a>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Aturan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Absensi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemicu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi Sanksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rules as $rule)
                        @php
                            $triggerLabels = [
                                'consecutive_absence' => 'Absen Beruntun',
                                'monthly_absence' => 'Absen Bulanan',
                                'monthly_late_count' => 'Telat (hitungan)',
                                'monthly_late_minutes' => 'Telat (menit)',
                                'daily_late' => 'Telat Harian',
                            ];
                            $actionLabels = [
                                'notification' => 'Notifikasi',
                                'warning_letter' => 'Surat Peringatan',
                                'payroll_deduction' => 'Potongan Gaji',
                                'mark_anomaly' => 'Tandai Anomali',
                                'create_follow_up' => 'Buat Tindak Lanjut',
                            ];
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $rule->rule_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $rule->attendance_type === 'work_schedule' ? 'Kerja Harian' : 'Sesi/Kelas' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $triggerLabels[$rule->trigger_type] ?? $rule->trigger_type }}
                                {{ $rule->trigger_operator }} {{ $rule->trigger_value }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($rule->actions->sortBy('action_order') as $action)
                                        <span class="px-1.5 py-0.5 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">
                                            {{ $action->action_order }}. {{ $actionLabels[$action->action_type] ?? $action->action_type }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('attendance-rules.edit', $rule->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <button type="button" @click="showConfirmModal = true; confirmTitle = 'Hapus aturan {{ $rule->rule_name }}?'; confirmActionUrl = '{{ route('attendance-rules.destroy', $rule->id) }}'" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada aturan sanksi absen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rules->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $rules->links() }}</div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showConfirmModal" class="relative z-10" style="display: none;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showConfirmModal = false"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="px-6 py-5">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100 mr-4">
                            <span class="material-symbols-outlined text-red-600 text-xl">warning</span>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900" x-text="confirmTitle"></h3>
                            <p class="text-sm text-gray-500 mt-1">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex flex-row-reverse gap-2 rounded-b-lg">
                    <button type="button" @click="$refs.confirmForm.submit()" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md text-sm">Hapus</button>
                    <button type="button" @click="showConfirmModal = false" class="bg-white border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md text-sm">Batal</button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
