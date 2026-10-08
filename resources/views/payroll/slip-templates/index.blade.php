@extends('layouts.dashboard')

@section('header_title', 'Template Slip Gaji')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Payroll</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Template Slip Gaji</h1>
        <p class="mt-1 text-sm text-slate-500">Kelola template .docx dan assignment posisi untuk generate slip gaji.</p>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
        <span class="material-symbols-outlined text-[16px]">check_circle</span>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <span class="material-symbols-outlined text-[16px]">error</span>{{ session('error') }}
    </div>
    @endif

    {{-- ═══ SECTION 1: Template yang Ada ═══════════════════════════════ --}}
    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Template Tersedia ({{ count($templates) }})</h2>
        </div>

        @forelse($templates as $tpl)
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-4 px-5 py-4">
                {{-- Icon --}}
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50">
                    <span class="material-symbols-outlined text-[18px] text-[#215aac]">description</span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-slate-900 text-sm">{{ $tpl['label'] }}</p>
                        @if(!$tpl['is_base'])
                        <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-bold text-purple-700">CUSTOM</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5 font-mono">slip_{{ $tpl['type'] }}.docx &middot; {{ $tpl['size'] }} &middot; {{ $tpl['modified'] }}</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('payroll.slip-templates.download', $tpl['type']) }}"
                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        <span class="material-symbols-outlined text-[13px]">download</span> Download
                    </a>
                    <a href="{{ route('payroll.slip-templates.test', $tpl['type']) }}"
                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        <span class="material-symbols-outlined text-[13px]">science</span> Tes
                    </a>

                    {{-- Replace --}}
                    <label class="inline-flex items-center gap-1 rounded-lg border border-[#215aac] px-3 py-1.5 text-xs font-medium text-[#215aac] hover:bg-blue-50 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[13px]">drive_file_rename_outline</span> Ganti
                        <input type="file" accept=".docx" class="hidden"
                               onchange="uploadTemplate('{{ $tpl['type'] }}', '{{ route('payroll.slip-templates.upload', $tpl['type']) }}', this)">
                    </label>

                    @if(!$tpl['is_base'])
                    <form method="POST" action="{{ route('payroll.slip-templates.destroy', $tpl['type']) }}"
                          onsubmit="return confirm('Hapus template {{ $tpl['label'] }}? Assignment posisi ke template ini akan direset.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                            <span class="material-symbols-outlined text-[13px]">delete</span> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center text-sm text-slate-400">
            Belum ada template. Tambah template di bawah.
        </div>
        @endforelse
    </section>

    {{-- ═══ SECTION 2: Tambah Template Baru ═══════════════════════════ --}}
    <section class="space-y-3">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Tambah Template Baru</h2>

        <form action="{{ route('payroll.slip-templates.store') }}" method="POST" enctype="multipart/form-data"
              class="rounded-xl border border-dashed border-slate-300 bg-white p-5">
            @csrf
            @if($errors->has('label') || $errors->has('template'))
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif

            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Nama Template <span class="text-red-500">*</span></label>
                    <input type="text" name="label" value="{{ old('label') }}" placeholder="Contoh: Board & Executive"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] focus:outline-none"
                           required>
                    <p class="text-xs text-slate-400 mt-1">Nama ini jadi label di dropdown assignment.</p>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">File .docx <span class="text-red-500">*</span></label>
                    <label class="flex items-center gap-2 cursor-pointer rounded-lg border border-slate-300 hover:border-[#215aac] px-3 py-2 transition-colors"
                           id="new-file-label">
                        <span class="material-symbols-outlined text-slate-400 text-[16px]">attach_file</span>
                        <span class="text-sm text-slate-400 truncate" id="new-file-name">Pilih file .docx…</span>
                        <input type="file" name="template" accept=".docx" class="hidden" required
                               onchange="document.getElementById('new-file-name').textContent = this.files[0]?.name || 'Pilih file .docx…'">
                    </label>
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#215aac] px-5 py-2 text-sm font-bold text-white hover:bg-[#194a91] transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[16px]">add</span> Tambah
                </button>
            </div>
        </form>
    </section>

    {{-- ═══ SECTION 3: Assignment per Posisi ══════════════════════════ --}}
    <section class="space-y-3">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Assignment per Posisi</h2>
        <p class="text-xs text-slate-500"><strong>Auto-detect</strong> = sistem tebak dari nama posisi (Official/Regular Tutor → template tutor, lainnya → marketing).</p>

        <form action="{{ route('payroll.slip-templates.assign.update') }}" method="POST"
              class="rounded-xl border border-slate-200 bg-white shadow-sm">
            @csrf
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Posisi</th>
                        <th class="px-5 py-3 text-left">Template</th>
                        <th class="px-5 py-3 text-left hidden sm:table-cell">Auto-detect</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($positions as $position)
                    @php
                        $autoType = match(true) {
                            str_contains(strtolower($position->position_name), 'official tutor') => 'official_tutor',
                            str_contains(strtolower($position->position_name), 'regular tutor')  => 'regular_tutor',
                            default => 'marketing',
                        };
                        $current = $position->slip_template_type ?? '';
                    @endphp
                    <tr class="{{ $current ? 'bg-blue-50/30' : '' }}">
                        <td class="px-5 py-2.5 font-medium text-slate-800">
                            {{ $position->position_name }}
                            @if($current)
                            <span class="ml-1 text-[10px] font-bold text-[#215aac] uppercase">override</span>
                            @endif
                        </td>
                        <td class="px-5 py-2.5">
                            <select name="assignments[{{ $position->id }}]"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 focus:border-[#215aac] focus:outline-none focus:ring-1 focus:ring-[#215aac]">
                                <option value="" {{ !$current ? 'selected' : '' }}>— Auto-detect —</option>
                                @foreach($typeLabels as $type => $label)
                                <option value="{{ $type }}" {{ $current === $type ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-5 py-2.5 text-xs text-slate-400 hidden sm:table-cell">
                            {{ $typeLabels[$autoType] ?? $autoType }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#215aac] px-5 py-2 text-sm font-bold text-white hover:bg-[#194a91] transition-colors">
                    <span class="material-symbols-outlined text-[16px]">save</span> Simpan Assignment
                </button>
            </div>
        </form>
    </section>
</div>

{{-- Upload ganti template via hidden form --}}
<form id="upload-form" method="POST" enctype="multipart/form-data" style="display:none">
    @csrf
    <input type="file" id="upload-input" name="template" accept=".docx">
</form>

<script>
function uploadTemplate(type, url, input) {
    if (!input.files[0]) return;
    const form = document.getElementById('upload-form');
    form.action = url;
    const dt = new DataTransfer();
    dt.items.add(input.files[0]);
    document.getElementById('upload-input').files = dt.files;
    form.submit();
}
</script>
@endsection
