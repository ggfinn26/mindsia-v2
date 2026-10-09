@extends('layouts.dashboard')

@section('title', 'Kuota Cabang — ' . $program->program_name . ' - MINDSIA')
@section('header_title', 'Kuota Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6">
    <form action="{{ route('programs.quotas.store', $program) }}" method="POST" class="space-y-6">
        @csrf

        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('programs.show', $program) }}"
                   class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
                   style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </a>
                <div>
                    <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                        style="color: var(--dash-text-heading);">Set Kuota Cabang</h1>
                    <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">{{ $program->program_name }}</p>
                </div>
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">save</span>Simpan Semua
            </button>
        </div>

        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
                 style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
                 style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
                <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed text-sm">
                    <colgroup><col class="w-1/2"><col class="w-1/2"></colgroup>
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Cabang</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kuota (Peserta)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branches as $branch)
                            @php $quota = $quotas->get($branch->id); @endphp
                            <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                <td class="px-5 py-4 font-medium" style="color: var(--dash-text-heading);">
                                    {{ $branch->branch_name }}
                                </td>
                                <td class="px-5 py-4">
                                    <input type="number" name="quotas[{{ $branch->id }}]"
                                           value="{{ old('quotas.' . $branch->id, $quota ? $quota->quota_limit : '') }}"
                                           min="0" placeholder="Kosongkan jika tidak ada"
                                           class="block w-full max-w-xs rounded-lg px-3 py-2 text-sm focus:outline-none"
                                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@endsection
