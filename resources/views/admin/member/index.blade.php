@extends('layouts.dashboard')

@section('title', 'Data Member')

@section('content')
@php use App\Enums\Member\ActivationStatus; @endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Member & Marketing</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Data Member</h2>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('members.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">person_add</span> Registrasi Member
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('members.index') }}" class="flex flex-wrap items-end gap-3">
        <div x-data="adminInstitutionFilter()" class="relative z-20">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Institusi</label>
            
            <div @click="open = !open" @click.away="open = false" 
                 class="border border-slate-300 bg-white px-3 py-2 text-sm focus-within:border-[#215aac] focus-within:ring-1 focus-within:ring-[#215aac] min-w-[200px] max-w-[250px] flex justify-between items-center cursor-pointer h-[38px]">
                <span x-text="selectedName" class="truncate block pr-4"></span>
                <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
            </div>
            
            <div x-show="open" x-cloak 
                 class="absolute left-0 w-[300px] mt-1 bg-white border border-gray-200 shadow-lg max-h-[300px] flex flex-col z-50">
                <div class="p-2 bg-gray-50 border-b border-gray-100">
                    <input type="text" x-model="search" placeholder="Cari institusi..." 
                           class="w-full px-2 py-1.5 text-xs border border-gray-300 focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                </div>
                <ul class="overflow-y-auto flex-1 py-1 max-h-[250px]">
                    <li @click="select('', 'Semua')" class="px-3 py-2 text-xs text-gray-700 hover:bg-[#215aac] hover:text-white cursor-pointer">Semua</li>
                    <template x-for="inst in filtered" :key="inst.id">
                        <li @click="select(inst.id, inst.institution_name)" class="px-3 py-2 text-xs text-gray-700 hover:bg-[#215aac] hover:text-white cursor-pointer" x-text="inst.institution_name"></li>
                    </template>
                    <li x-show="filtered.length === 0" class="px-3 py-2 text-xs text-gray-500 text-center">Tidak ditemukan</li>
                </ul>
            </div>
            <input type="hidden" name="institution_id" :value="selectedId">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Program</label>
            <select name="program_id" class="border border-slate-300 bg-white px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                <option value="">Semua</option>
                @foreach(\App\Models\Program::where('is_active', true)->orderBy('program_name')->get() as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Email / WA" class="border border-slate-300 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
        </div>
        <button type="submit" class="inline-flex min-h-10 items-center gap-1 border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <span class="material-symbols-outlined text-[16px]">search</span> Filter
        </button>
        <a href="{{ route('members.index') }}" class="inline-flex min-h-10 items-center px-3 py-2 text-sm text-slate-500 hover:text-slate-700">Reset</a>
    </form>

    <!-- CSV Import -->
    <form method="POST" action="{{ route('members.import') }}" enctype="multipart/form-data" class="flex items-end gap-3 border border-dashed border-slate-300 bg-slate-50/50 px-5 py-4">
        @csrf
        <div class="flex-1">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Import CSV</label>
            <input type="file" name="file" accept=".csv,.txt" class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#215aac] file:text-white hover:file:bg-[#194a91]" />
        </div>
        <button type="submit" class="inline-flex min-h-10 items-center gap-2 border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <span class="material-symbols-outlined text-[16px]">upload_file</span> Import
        </button>
    </form>

    <!-- Table -->
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">WhatsApp</th>
                        <th class="px-5 py-3">Institusi</th>
                        <th class="px-5 py-3">Program</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Akun</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($members as $member)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $member->full_name }}</p>
                                <p class="mt-0.5 text-xs text-slate-400">{{ $member->gender ?? '—' }} · {{ $member->birthdate?->format('d M Y') ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $member->email ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $member->whatsapp_number ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $member->institution?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $member->program?->program_name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                @php $status = $member->activation_status; @endphp
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $status instanceof ActivationStatus ? $status->badgeClass() : 'bg-slate-100 text-slate-600' }}">
                                    {{ $status instanceof ActivationStatus ? $status->label() : '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($member->account)
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-700">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Ada
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('members.show', $member) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                                <a href="{{ route('members.edit', $member) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50 ml-1">Edit</a>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">group_off</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada data member.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($members->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $members->links() }}</div>
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
                if (!this.selectedId) return 'Semua';
                const found = this.institutions.find(i => i.id == this.selectedId);
                return found ? found.institution_name : 'Semua';
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
