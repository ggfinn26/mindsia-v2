@extends('layouts.dashboard')

@section('title', 'Data Member - MINDSIA')
@section('header_title', 'Data Member')

@section('content')
@php use App\Enums\Member\ActivationStatus; @endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Data Member</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Kelola data anggota, riwayat institusi, program keikutsertaan, dan status akun.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('members.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Registrasi Member
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filters Card --}}
    <form method="GET" action="{{ route('members.index') }}"
          class="flex flex-wrap items-end gap-3 p-4 sm:p-5 rounded-2xl shadow-sm"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div x-data="adminInstitutionFilter()" class="relative z-20">
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Institusi</label>
            
            <div @click="open = !open" @click.away="open = false" 
                 class="px-3 py-2 text-sm rounded-lg min-w-[200px] max-w-[250px] flex justify-between items-center cursor-pointer h-[38px] transition-all"
                 style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                <span x-text="selectedName" class="truncate block pr-2"></span>
                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-muted);">expand_more</span>
            </div>
            
            <div x-show="open" x-cloak 
                 class="absolute left-0 w-[300px] mt-1 rounded-xl shadow-lg max-h-[300px] flex flex-col z-50 overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-2 border-b" style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <input type="text" x-model="search" placeholder="Cari institusi..." 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <ul class="overflow-y-auto flex-1 py-1 max-h-[250px]">
                    <li @click="select('', 'Semua')"
                        class="px-3 py-2 text-xs cursor-pointer transition-colors"
                        style="color: var(--dash-text-body);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        Semua Institusi
                    </li>
                    <template x-for="inst in filtered" :key="inst.id">
                        <li @click="select(inst.id, inst.institution_name)"
                            class="px-3 py-2 text-xs cursor-pointer transition-colors"
                            style="color: var(--dash-text-body);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'"
                            x-text="inst.institution_name"></li>
                    </template>
                    <li x-show="filtered.length === 0" class="px-3 py-2 text-xs text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                </ul>
            </div>
            <input type="hidden" name="institution_id" :value="selectedId">
        </div>

        <div>
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Program</label>
            <select name="program_id"
                    class="h-[38px] px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Program</option>
                @foreach(\App\Models\Program::where('is_active', true)->orderBy('program_name')->get() as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="relative">
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Cari</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                      style="color: var(--dash-text-muted);">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Email / WA..."
                       class="h-[38px] text-sm rounded-lg focus:outline-none transition-all"
                       style="padding-left: 2.25rem !important; padding-right: 0.75rem; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 h-[38px] px-4 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['institution_id', 'program_id', 'search']))
                <a href="{{ route('members.index') }}"
                   class="inline-flex items-center gap-1.5 h-[38px] px-3 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- CSV Import Card --}}
    <form method="POST" action="{{ route('members.import') }}" enctype="multipart/form-data"
          class="flex flex-col sm:flex-row sm:items-end gap-3 p-4 sm:p-5 rounded-2xl shadow-sm border border-dashed"
          style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        @csrf
        <div class="flex-1">
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Import CSV</label>
            <input type="file" name="file" accept=".csv,.txt"
                   class="block w-full text-sm file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:text-white hover:file:opacity-90 cursor-pointer"
                   style="color: var(--dash-text-body);" />
        </div>
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors shrink-0"
                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[18px]">upload_file</span>
            Import Data
        </button>
    </form>

    {{-- Data Table (Consolidated columns for zero horizontal scroll) --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm text-left">
            <thead>
                <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Member</th>
                    <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Kontak</th>
                    <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Institusi & Program</th>
                    <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Status & Akun</th>
                    <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $member)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        {{-- Member Col: Name + Bio info --}}
                        <td class="px-4 py-3">
                            <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $member->full_name }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                {{ $member->gender === 'L' ? 'Laki-laki' : ($member->gender === 'P' ? 'Perempuan' : '-') }}
                                · {{ $member->birthdate?->format('d M Y') ?? '-' }}
                            </p>
                        </td>

                        {{-- Kontak Col: Email + WA --}}
                        <td class="px-4 py-3">
                            @php
                                $waNum = preg_replace('/[^0-9]/', '', $member->whatsapp_number ?? '');
                                if (str_starts_with($waNum, '0')) {
                                    $waNum = '62'.substr($waNum, 1);
                                } elseif ($waNum && ! str_starts_with($waNum, '62')) {
                                    $waNum = '62'.$waNum;
                                }
                            @endphp
                            <p class="text-xs break-all" style="color: var(--dash-text-heading);">{{ $member->email ?? '-' }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs font-mono" style="color: var(--dash-text-muted);">{{ $member->whatsapp_number ?? '-' }}</span>
                                @if ($waNum)
                                    <div class="relative" x-data="waPicker({
                                        wa: '{{ $waNum }}',
                                        nama: @js($member->full_name),
                                        institusi: @js($member->institution?->name ?? $member->institution?->institution_name ?? ''),
                                        sosialisasi: '',
                                        status: @js($member->activation_status instanceof ActivationStatus ? $member->activation_status->label() : (string) $member->activation_status)
                                    })" @click.away="open = false">
                                        <button type="button" @click="open = !open"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-full hover:bg-emerald-50 focus:outline-none transition-colors"
                                            title="{{ $member->whatsapp_number }}">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 362" fill="none" aria-label="WhatsApp"><path fill="#25D366" fill-rule="evenodd" d="M307.546 52.566C273.709 18.684 228.706.017 180.756 0 81.951 0 1.538 80.404 1.504 179.235c-.017 31.594 8.242 62.432 23.928 89.609L0 361.736l95.024-24.925c26.179 14.285 55.659 21.805 85.655 21.814h.077c98.788 0 179.21-80.413 179.244-179.244.017-47.898-18.608-92.926-52.454-126.807v-.008Zm-126.79 275.788h-.06c-26.73-.008-52.952-7.194-75.831-20.765l-5.44-3.231-56.391 14.791 15.05-54.981-3.542-5.638c-14.912-23.721-22.793-51.139-22.776-79.286.035-82.14 66.867-148.973 149.051-148.973 39.793.017 77.198 15.53 105.328 43.695 28.131 28.157 43.61 65.596 43.593 105.398-.035 82.149-66.867 148.982-148.982 148.982v.008Zm81.719-111.577c-4.478-2.243-26.497-13.073-30.606-14.568-4.108-1.496-7.09-2.243-10.073 2.243-2.982 4.487-11.568 14.577-14.181 17.559-2.613 2.991-5.226 3.361-9.704 1.117-4.477-2.243-18.908-6.97-36.02-22.226-13.313-11.878-22.304-26.54-24.916-31.027-2.613-4.486-.275-6.91 1.959-9.136 2.011-2.011 4.478-5.234 6.721-7.847 2.244-2.613 2.983-4.486 4.478-7.469 1.496-2.991.748-5.603-.369-7.847-1.118-2.243-10.073-24.289-13.812-33.253-3.636-8.732-7.331-7.546-10.073-7.692-2.613-.13-5.595-.155-8.586-.155-2.991 0-7.839 1.118-11.947 5.604-4.108 4.486-15.677 15.324-15.677 37.361s16.047 43.344 18.29 46.335c2.243 2.991 31.585 48.225 76.51 67.632 10.684 4.615 19.029 7.374 25.535 9.437 10.727 3.412 20.49 2.931 28.208 1.779 8.604-1.289 26.498-10.838 30.228-21.298 3.73-10.46 3.73-19.433 2.613-21.298-1.117-1.865-4.108-2.991-8.586-5.234l.008-.017Z" clip-rule="evenodd"/></svg>
                                        </button>
                                        <div x-show="open" x-transition
                                            class="absolute left-0 top-8 z-20 w-56 rounded-xl shadow-xl py-1.5"
                                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                            <button type="button" @click="send(null)"
                                                class="w-full text-left px-4 py-2 text-xs font-medium transition-colors"
                                                style="color: var(--dash-text-body);"
                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                onmouseout="this.style.backgroundColor='transparent'">
                                                Kirim langsung (tanpa template)
                                            </button>
                                            <template x-if="templates.length > 0">
                                                <div>
                                                    <p class="px-4 py-1 text-[10px] font-semibold uppercase tracking-wider border-t mt-1"
                                                       style="color: var(--dash-text-faint); border-color: var(--dash-card-border);">Template Pesan</p>
                                                    <template x-for="t in templates" :key="t.id">
                                                        <button type="button" @click="send(t)"
                                                            class="w-full text-left px-4 py-2 text-xs font-medium transition-colors"
                                                            style="color: var(--dash-text-heading);"
                                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                            onmouseout="this.style.backgroundColor='transparent'"
                                                            x-text="t.name">
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>
                                            <p x-show="templates.length === 0"
                                                class="px-4 py-2 text-[10px] border-t mt-1"
                                                style="color: var(--dash-text-faint); border-color: var(--dash-card-border);">
                                                Belum ada template. Buat di pengaturan WA.
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- Institusi & Program Col --}}
                        <td class="px-4 py-3">
                            <p class="text-xs font-medium" style="color: var(--dash-text-heading);">
                                {{ $member->institution?->name ?? $member->institution?->institution_name ?? '-' }}
                            </p>
                            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                                {{ $member->program?->program_name ?? '-' }}
                            </p>
                        </td>

                        {{-- Status & Akun Col --}}
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1 items-start">
                                @php $status = $member->activation_status; @endphp
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $status instanceof ActivationStatus ? $status->badgeClass() : 'bg-slate-100 text-slate-600' }}">
                                    {{ $status instanceof ActivationStatus ? $status->label() : '-' }}
                                </span>
                                @if($member->account)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700">
                                        <span class="material-symbols-outlined text-[13px]">check_circle</span> Akun Aktif
                                    </span>
                                @else
                                    <span class="text-[11px]" style="color: var(--dash-text-faint);">Belum ada akun</span>
                                @endif
                            </div>
                        </td>

                        {{-- Aksi Col --}}
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <a href="{{ route('members.show', $member) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Detail
                                </a>
                                <a href="{{ route('members.edit', $member) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">group_off</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data member.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($members->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminInstitutionFilter', () => ({
            open: false,
            search: '',
            selectedId: '{{ request('institution_id') }}',
            institutions: @json(\App\Models\Institution::select('id', 'institution_name')->orderBy('institution_name')->get()),
            
            get selectedName() {
                if (!this.selectedId) return 'Semua Institusi';
                const found = this.institutions.find(i => i.id == this.selectedId);
                return found ? found.institution_name : 'Semua Institusi';
            },
            
            get filtered() {
                if (this.search.trim() === '') {
                    return this.institutions.slice(0, 100);
                }
                const q = this.search.toLowerCase();
                return this.institutions.filter(i => i.institution_name.toLowerCase().includes(q)).slice(0, 100);
            },
            
            select(id, name) {
                this.selectedId = id;
                this.open = false;
                this.search = '';
            }
        }));
    });
</script>
@endsection
