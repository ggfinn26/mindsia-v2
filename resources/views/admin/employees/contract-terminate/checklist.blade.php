@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Checklist Terminasi Kontrak</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Data Karyawan</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">Nama</p>
                <p class="font-semibold">{{ $status->employee->full_name }}</p>
            </div>
            <div>
                <p class="text-gray-600">Status</p>
                <p class="font-semibold text-orange-600">Terminated</p>
            </div>
        </div>
    </div>

    <form action="{{ route('contract-terminate.checklist.update', $status) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <h2 class="text-lg font-semibold mb-4">Daftar Kewajiban</h2>

        @if($checklist->count() === 0)
            <p class="text-gray-500">Belum ada checklist item.</p>
        @else
            <div class="space-y-3">
                @foreach($checklist as $item)
                    <div class="flex items-center gap-4 p-4 border rounded">
                        <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">

                        <label class="flex items-center gap-3 flex-1 cursor-pointer">
                            <input type="checkbox" name="items[{{ $loop->index }}][is_completed]" value="1"
                                {{ $item->is_completed ? 'checked' : '' }}
                                class="w-5 h-5">
                            <span>{{ $item->item_name }}</span>
                        </label>

                        @if($item->is_completed)
                            <span class="text-sm text-gray-600">
                                Selesai: {{ $item->completed_at->format('d M Y H:i') }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded border border-blue-200">
                <p class="text-sm text-blue-900">
                    Progress: <strong>{{ $completedCount }}/{{ $checklist->count() }}</strong> item selesai
                </p>
            </div>
        @endif

        <div class="flex gap-4 mt-6">
            <a href="{{ route('employees.show', $status->employee) }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Kembali</a>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan Progress</button>
        </div>
    </form>
</div>
@endsection
