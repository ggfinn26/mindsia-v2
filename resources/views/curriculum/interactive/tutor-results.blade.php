@extends('layouts.dashboard')

@section('title', 'Hasil Latihan — ' . $item->item_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Hasil Latihan</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $item->item_name }} — {{ $classroom->class_name }}</p>
        </div>
        <a href="{{ route('curriculum-items.interactive.show', $item) }}"
            class="btn bg-white border border-slate-200 text-slate-600 text-sm">Edit Modul</a>
    </div>

    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Nama Member</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Percobaan</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Skor Terakhir</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Terakhir Latihan</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($results as $r)
                    @php
                        $member = $r['member_class']->registration->memberData;
                        $latest = $r['latest_attempt'];
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $member->full_name }}</td>
                        <td class="px-4 py-3 text-center text-slate-500">{{ $r['attempt_count'] }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($latest)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $latest->score >= 70 ? 'bg-green-100 text-green-700' : ($latest->score >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                                    {{ $latest->score }}%
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">Belum</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-slate-500 text-xs">
                            {{ $latest ? $latest->completed_at->format('d M Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($r['attempt_count'] > 0)
                                <a href="{{ route('curriculum.interactive.results.member', [$classroom, $item, $r['member_class']]) }}"
                                    class="text-indigo-600 hover:underline text-xs">Lihat Detail</a>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada member di kelas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
