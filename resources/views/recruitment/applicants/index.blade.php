@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Daftar Pelamar</h1>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-5">
        <form method="GET" action="{{ route('recruitment.applicants.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, email, atau WA..."
                class="form-input text-sm w-64" />
            <button type="submit" class="btn bg-[var(--dash-primary)] hover:opacity-90 text-white text-sm inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">search</span>Cari</button>
            @if(request('search'))
                <a href="{{ route('recruitment.applicants.index') }}" class="btn bg-[var(--dash-card-bg)] border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] text-sm inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)]">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)] border-t border-b border-[color:var(--dash-card-border)]">
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
                <tbody class="text-sm divide-y divide-[color:var(--dash-card-border)]">
                    @forelse($applicants as $applicant)
                        <tr class="hover:bg-[var(--dash-nav-hover-bg)]">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $applicants->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-[color:var(--dash-text-heading)]">
                                {{ $applicant->full_name }}
                                @if($applicant->gender)
                                    <span class="text-xs text-[color:var(--dash-text-faint)] ml-1">({{ $applicant->gender === 'male' ? 'L' : 'P' }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[color:var(--dash-text-body)] text-xs">{{ $applicant->email ?? '—' }}</div>
                                <div class="text-[color:var(--dash-text-muted)] text-xs">{{ $applicant->whatsapp_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[color:var(--dash-text-body)]">{{ $applicant->city ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($applicant->cv_path)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Ada</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-muted)]">Belum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-[color:var(--dash-text-body)] font-medium">{{ $applicant->applications_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('recruitment.applicants.show', $applicant) }}"
                                    class="text-indigo-600 hover:text-indigo-900 text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-[color:var(--dash-text-muted)]">
                                {{ request('search') ? 'Tidak ditemukan pelamar dengan kata kunci tersebut.' : 'Belum ada data pelamar.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[color:var(--dash-card-border)]">
            {{ $applicants->links() }}
        </div>
    </div>
</div>
@endsection
