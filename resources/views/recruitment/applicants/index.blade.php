@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Daftar Pelamar</h1>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-5">
        <form method="GET" action="{{ route('recruitment.applicants.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, email, atau WA..."
                class="form-input text-sm w-64" />
            <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white text-sm">Cari</button>
            @if(request('search'))
                <a href="{{ route('recruitment.applicants.index') }}" class="btn bg-white border-gray-200 text-gray-600 text-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Nama</th>
                        <th class="px-6 py-3 text-left">Email / WA</th>
                        <th class="px-6 py-3 text-left">Kota</th>
                        <th class="px-6 py-3 text-center">CV</th>
                        <th class="px-6 py-3 text-center">Lamaran</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($applicants as $applicant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $applicants->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                {{ $applicant->full_name }}
                                @if($applicant->gender)
                                    <span class="text-xs text-gray-400 ml-1">({{ $applicant->gender === 'male' ? 'L' : 'P' }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-600 text-xs">{{ $applicant->email ?? '—' }}</div>
                                <div class="text-gray-500 text-xs">{{ $applicant->whatsapp_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $applicant->city ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($applicant->cv_path)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Ada</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Belum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-gray-700 font-medium">{{ $applicant->applications_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('recruitment.applicants.show', $applicant) }}"
                                    class="text-indigo-600 hover:text-indigo-900 text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                {{ request('search') ? 'Tidak ditemukan pelamar dengan kata kunci tersebut.' : 'Belum ada data pelamar.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $applicants->links() }}
        </div>
    </div>
</div>
@endsection
