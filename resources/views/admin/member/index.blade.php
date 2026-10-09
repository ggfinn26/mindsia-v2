@extends('layouts.dashboard')

@section('title', 'Data Member')
@section('header_title', 'Data Member')

@section('content')
@php use App\Enums\Member\ActivationStatus; @endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Data Member</h1>
        </div>
        <a href="{{ route('members.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">person_add</span>Registrasi Member
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="rounded-2xl px-5 py-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('members.index') }}" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="sort" value="{{ $sort }}">

            {{-- Institution searchable dropdown --}}
            <div x-data="adminInstitutionFilter()" class="relative z-20">
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-muted);">Institusi</label>
                <div @click="open = !open" @click.away="open = false"
                     class="rounded-lg px-3 py-2 text-sm flex justify-between items-center cursor-pointer min-w-[180px]"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <span x-text="selectedName" class="truncate block pr-2"></span>
                    <span class="material-symbols-outlined text-[16px] shrink-0" style="color: var(--dash-text-muted);">expand_more</span>
                </div>
                <div x-show="open" x-cloak
                     class="absolute left-0 w-[280px] mt-1 rounded-2xl shadow-xl overflow-hidden z-50"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-2.5 top-2 text-[14px]" style="color: var(--dash-text-muted);">search</span>
                            <input type="text" x-model="search" placeholder="Cari institusi..."
                                   style="padding-left: 2rem !important; background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"
                                   class="w-full pr-2 py-1.5 rounded-lg text-xs focus:outline-none">
                        </div>
                    </div>
                    <ul class="overflow-y-auto max-h-[220px] py-1">
                        <li @click="select('', 'Semua')"
                            class="px-3 py-2 text-xs cursor-pointer hover:bg-blue-500 hover:text-white transition-colors"
                            style="color: var(--dash-text-body);">Semua</li>
                        <template x-for="inst in filtered" :key="inst.id">
                            <li @click="select(inst.id, inst.institution_name)"
                                class="px-3 py-2 text-xs cursor-pointer hover:bg-blue-500 hover:text-white transition-colors"
                                style="color: var(--dash-text-body);"
                                x-text="inst.institution_name"></li>
                        </template>
                        <li x-show="filtered.length === 0" class="px-3 py-2 text-xs text-center"
                            style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                    </ul>
                </div>
                <input type="hidden" name="institution_id" :value="selectedId">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-muted);">Program</label>
                <select name="program_id"
                        class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Semua</option>
                    @foreach(\App\Models\Program::where('is_active', true)->orderBy('program_name')->get() as $prog)
                        <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-muted);">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Email / WA"
                       class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">search</span>Filter
            </button>
            <a href="{{ route('members.index') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg text-sm transition-colors gap-1.5"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset</a>
        </form>
    </div>

    {{-- CSV Import --}}
    <div class="rounded-2xl px-5 py-4"
         style="background-color: var(--dash-card-bg); border: 2px dashed var(--dash-card-border);">
        <form method="POST" action="{{ route('members.import') }}" enctype="multipart/form-data"
              class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-muted);">Import CSV</label>
                <label class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm cursor-pointer"
                       style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);">attach_file</span>
                    <span id="csv-file-name" class="text-sm" style="color: var(--dash-text-muted);">Pilih file .csv</span>
                    <input type="file" name="file" accept=".csv,.txt" class="hidden"
                           onchange="document.getElementById('csv-file-name').textContent = this.files[0]?.name || 'Pilih file .csv'">
                </label>
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shrink-0 border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"
                    style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">upload_file</span>Import
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[20%]">
                <col class="w-[20%]">
                <col class="w-[13%]">
                <col class="w-[14%]">
                <col class="w-[12%]">
                <col class="w-[9%]">
                <col class="w-[6%]">
                <col class="w-[6%]">
            </colgroup>
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    @php
                        $nextNameSort  = $sort === 'name_asc'  ? 'name_desc'  : 'name_asc';
                        $nextEmailSort = $sort === 'email_asc' ? 'email_desc' : 'email_asc';
                    @endphp
                    <th class="px-5 py-3 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $nextNameSort, 'page' => 1]) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity"
                           style="color: var(--dash-text-muted);">
                            Nama
                            <span class="material-symbols-outlined text-[14px]">
                                @if($sort === 'name_asc') arrow_upward
                                @elseif($sort === 'name_desc') arrow_downward
                                @else unfold_more @endif
                            </span>
                        </a>
                    </th>
                    <th class="px-5 py-3 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $nextEmailSort, 'page' => 1]) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity"
                           style="color: var(--dash-text-muted);">
                            Email
                            <span class="material-symbols-outlined text-[14px]">
                                @if($sort === 'email_asc') arrow_upward
                                @elseif($sort === 'email_desc') arrow_downward
                                @else unfold_more @endif
                            </span>
                        </a>
                    </th>
                    @foreach(['WhatsApp','Institusi','Program','Status','Akun',''] as $h)
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor=''">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $member->full_name }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">
                                {{ $member->gender ?? '—' }} · {{ $member->birthdate?->format('d M Y') ?? '—' }}
                            </p>
                        </td>
                        <td class="px-5 py-4 text-sm truncate" style="color: var(--dash-text-muted);">
                            {{ $member->email ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-sm" style="color: var(--dash-text-muted);">
                            {{ $member->whatsapp_number ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-sm truncate" style="color: var(--dash-text-muted);">
                            {{ $member->institution?->institution_name ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-sm truncate" style="color: var(--dash-text-muted);">
                            {{ $member->program?->program_name ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            @php $status = $member->activation_status; @endphp
                            @if($status instanceof ActivationStatus)
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $status->badgeClass() }}">
                                    {{ $status->label() }}
                                </span>
                            @else
                                <span style="color: var(--dash-text-faint);">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($member->account)
                                <span class="inline-flex items-center gap-1 text-xs" style="color: #059669;">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>Ada
                                </span>
                            @else
                                <span style="color: var(--dash-text-faint);">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-4">
                            <div class="flex items-center justify-end gap-1.5 flex-nowrap">
                                <a href="{{ route('members.show', $member) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[12px]">open_in_new</span>Detail
                                </a>
                                <a href="{{ route('members.edit', $member) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[12px]">edit</span>Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">group_off</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data member.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($members->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $members->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminInstitutionFilter', () => ({
            open: false,
            search: '',
            selectedId: '{{ request('institution_id') }}',
            institutions: @json(\App\Models\Institution::select('id', 'institution_name')->orderBy('institution_name')->get()),

            get selectedName() {
                if (!this.selectedId) return 'Semua';
                const found = this.institutions.find(i => i.id == this.selectedId);
                return found ? found.institution_name : 'Semua';
            },

            get filtered() {
                if (this.search.trim() === '') return this.institutions.slice(0, 100);
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
@endpush
