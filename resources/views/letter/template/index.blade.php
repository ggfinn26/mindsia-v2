@extends('layouts.dashboard')

@section('title', 'Template Surat')
@section('header_title', 'Template Surat')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Template Surat</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Upload file Word (.docx) sebagai template. Placeholder menggunakan alt text pada elemen.</p>
        </div>
        @can('letter.template.create')
            <a href="{{ route('letter-templates.create') }}"
               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Upload Template
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="rounded-2xl border overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Template</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Kategori</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Format Nomor</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">File</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $tpl)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $tpl->template_name }}</p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-faint);">{{ $tpl->template_code }}</p>
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $tpl->letter_category ?? '-' }}</td>
                            <td class="px-5 py-3.5 font-mono text-xs" style="color: var(--dash-text-body);">{{ $tpl->letter_number_format ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                @if ($tpl->telegram_file_id)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700">
                                        <span class="material-symbols-outlined text-[14px]">description</span> Ada
                                    </span>
                                @else
                                    <span class="text-xs" style="color: var(--dash-text-faint);">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $tpl->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $tpl->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('letter-templates.show', $tpl) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span> Lihat
                                    </a>
                                    @can('letter.template.update')
                                        <form method="POST" action="{{ route('letter-templates.toggle-active', $tpl) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                    style="border: 1px solid {{ $tpl->is_active ? '#fcd34d' : '#86efac' }}; background-color: {{ $tpl->is_active ? '#fef3c7' : '#f0fdf4' }}; color: {{ $tpl->is_active ? '#92400e' : '#166534' }};">
                                                <span class="material-symbols-outlined text-[14px]">{{ $tpl->is_active ? 'toggle_off' : 'toggle_on' }}</span>
                                                {{ $tpl->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">article</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada template surat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($templates, 'hasPages') && $templates->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
