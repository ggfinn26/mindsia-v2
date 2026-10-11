@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Pengaturan Potongan Izin / Sakit / Cuti</h1>
        <p class="mt-1 text-sm text-gray-500">Default: semua tipe izin <strong>dibayar penuh</strong> jika tidak ada pengaturan untuk posisi tersebut. BOARD selalu dibayar penuh.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 overflow-x-auto">
        <table class="table-auto w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 w-1/3">Posisi</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Izin</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Sakit</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Cuti</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($positions as $position)
                    @php
                        $types = [
                            'permission' => 'Izin',
                            'sick'       => 'Sakit',
                            'leave'      => 'Cuti',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-700">
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
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border transition-colors
                                            {{ $isPaid
                                                ? 'bg-green-50 border-green-200 text-green-700 hover:bg-green-100'
                                                : 'bg-red-50 border-red-200 text-red-700 hover:bg-red-100' }}"
                                        title="Klik untuk ubah ke {{ $isPaid ? 'tidak dibayar' : 'dibayar' }}">
                                        @if($isPaid)
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Dibayar
                                        @else
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
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

    <p class="mt-3 text-xs text-gray-400">Klik badge untuk toggle. Perubahan berlaku mulai periode payroll berikutnya.</p>
</div>
@endsection
