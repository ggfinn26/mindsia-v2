@extends('layouts.dashboard')

@section('title', 'Survey Saya - MINDSIA')
@section('header_title', 'Survey Saya')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold font-jakarta text-gray-900">Survey Saya</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar survei yang ditugaskan kepada Anda untuk evaluasi program.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-green-500 mt-0.5">check_circle</span>
            <div>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($surveys->isEmpty())
        <!-- Placeholder State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 flex flex-col items-center justify-center text-center">
            <div class="h-20 w-20 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#5586DB] text-4xl">assignment</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-2">Belum Ada Survei</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-6">Belum ada survei yang ditugaskan kepada Anda saat ini. Kami akan memberi tahu Anda jika ada survei baru.</p>
            <a href="{{ route('member.dashboard') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                Kembali ke Dashboard
            </a>
        </div>
    @else
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                            <th class="px-6 py-4">Nama Survey</th>
                            <th class="px-6 py-4">Status Pengisian</th>
                            <th class="px-6 py-4">Waktu Penyelesaian</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @foreach($surveys as $memberSurvey)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900 font-jakarta">{{ $memberSurvey->survey->title }}</p>
                                    @if($memberSurvey->survey->description)
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $memberSurvey->survey->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($memberSurvey->answers_exists)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Sudah Diisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                            Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    @if($memberSurvey->answers_exists)
                                        {{ $memberSurvey->updated_at->format('d M Y, H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($memberSurvey->answers_exists)
                                        <a href="{{ route('member.surveys.show', $memberSurvey) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm text-xs">
                                            Lihat Jawaban
                                        </a>
                                    @else
                                        <a href="{{ route('member.surveys.show', $memberSurvey) }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#5586DB] text-white font-medium rounded-lg hover:bg-[#4370BC] transition-colors shadow-sm text-xs">
                                            Mulai Isi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
