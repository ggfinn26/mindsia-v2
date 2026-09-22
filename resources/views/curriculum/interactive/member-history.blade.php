@extends('layouts.dashboard')

@section('title', 'Riwayat Latihan')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('class-curriculum.index', $classroom) }}" class="text-sm text-slate-500 hover:text-slate-700">← Kurikulum</a>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Riwayat Latihan — {{ $item->item_name }}</h1>
        </div>
        <a href="{{ route('member.curriculum.interactive.show', [$classroom, $item]) }}"
            class="btn bg-indigo-600 hover:bg-indigo-700 text-white text-sm">Latihan Lagi</a>
    </div>

    @if($attempts->isEmpty())
        <div class="bg-white border border-slate-200 rounded-lg p-8 text-center text-slate-400">
            Belum ada riwayat latihan.
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Durasi</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Skor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($attempts as $i => $attempt)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $attempt->completed_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} menit
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : ($attempt->score >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                                    {{ $attempt->score }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
