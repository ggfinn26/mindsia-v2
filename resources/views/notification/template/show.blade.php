@extends('layouts.dashboard')

@section('header_title', 'Detail Template Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    showVarForm: false,
    editVarId: null,
    varName: '',
    varType: '',
    varDesc: '',
    openEdit(id, name, type, desc) {
        this.editVarId = id;
        this.varName = name;
        this.varType = type;
        this.varDesc = desc;
        this.showVarForm = false;
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('notification-templates.index') }}" class="text-sm text-[var(--dash-text-muted)] hover:text-[var(--dash-text-body)] flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Template
            </a>
            <h1 class="mt-2 font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Detail Template Notifikasi</h1>
        </div>
        @can('notification.template.update')
        <a href="{{ route('notification-templates.edit', $template) }}" class="inline-flex items-center gap-2 rounded-lg border border-[var(--dash-input-border)] px-4 py-2 text-sm font-medium text-[var(--dash-text-muted)] hover:bg-[var(--dash-page-bg)] transition-colors">
            <span class="material-symbols-outlined text-[16px]">edit</span> Edit
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 px-4 py-3 text-sm text-emerald-700 [.mode-dark_&]:text-emerald-400">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="rounded-lg bg-red-50 [.mode-dark_&]:bg-red-500/10 border border-red-200 [.mode-dark_&]:border-red-500/20 px-4 py-3 text-sm text-red-700 [.mode-dark_&]:text-red-400">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <!-- Info template -->
    <div class="overflow-hidden rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-[var(--dash-text-faint)]">Template Key</p>
                <code class="mt-1 block rounded bg-[var(--dash-input-bg)] px-3 py-1.5 text-sm text-[var(--dash-text-body)] font-mono">{{ $template->template_key }}</code>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-[var(--dash-text-faint)]">Tipe</p>
                @php $typeColors = ['email' => 'bg-blue-100 [.mode-dark_&]:bg-blue-500/10 text-blue-800 [.mode-dark_&]:text-blue-400', 'telegram' => 'bg-sky-100 [.mode-dark_&]:bg-sky-500/10 text-sky-800 [.mode-dark_&]:text-sky-400', 'in-app' => 'bg-violet-100 [.mode-dark_&]:bg-violet-500/10 text-violet-800 [.mode-dark_&]:text-violet-400']; @endphp
                <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$template->type] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">{{ $template->type }}</span>
            </div>
        </div>

        @if($template->subject)
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-[var(--dash-text-faint)]">Subject (Email)</p>
            <p class="mt-1 text-sm text-[var(--dash-text-body)]">{{ $template->subject }}</p>
        </div>
        @endif

        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-[var(--dash-text-faint)]">Body</p>
            <pre class="mt-1 rounded-lg bg-[var(--dash-page-bg)] border border-[var(--dash-card-border)] p-4 text-sm text-[var(--dash-text-body)] whitespace-pre-wrap font-mono">{{ $template->body }}</pre>
        </div>
    </div>

    <!-- Variabel -->
    <div class="overflow-hidden rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[var(--dash-card-border)]">
            <h2 class="font-semibold text-[var(--dash-text-heading)]">Dokumentasi Variabel</h2>
            @can('notification.variable.create')
            <button @click="showVarForm = !showVarForm; editVarId = null; varName=''; varType=''; varDesc=''"
                class="inline-flex items-center gap-1 rounded-lg bg-[var(--dash-input-bg)] px-3 py-1.5 text-xs font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-input-border)] transition-colors">
                <span class="material-symbols-outlined text-[14px]">add</span> Tambah Variabel
            </button>
            @endcan
        </div>

        @can('notification.variable.create')
        <div x-show="showVarForm" class="border-b border-[var(--dash-card-border)] bg-[var(--dash-page-bg)] px-6 py-4">
            <form action="{{ route('notification-templates.variables.store', $template) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input name="variable_name" type="text" placeholder="nama_variabel" value="{{ old('variable_name') }}"
                    class="flex-1 rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)] font-mono" required />
                <input name="variable_type" type="text" placeholder="string / date / number" value="{{ old('variable_type') }}"
                    class="w-40 rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]" required />
                <input name="variable_description" type="text" placeholder="Deskripsi (opsional)" value="{{ old('variable_description') }}"
                    class="flex-1 rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]" />
                <button type="submit" class="rounded-lg bg-[var(--dash-primary)] px-4 py-2 text-sm font-bold text-white hover:opacity-90 transition-colors whitespace-nowrap inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">save</span>Simpan</button>
            </form>
        </div>
        @endcan

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-bold uppercase tracking-wider text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-3">Nama Variabel</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse($template->variables as $var)
                    <tr>
                        <td class="px-6 py-3"><code class="rounded bg-[var(--dash-input-bg)] px-2 py-0.5 text-xs font-mono text-[var(--dash-text-body)]">@php echo e('{{' . $var->variable_name . '}}'); @endphp</code></td>
                        <td class="px-6 py-3 text-[var(--dash-text-muted)]">{{ $var->variable_type }}</td>
                        <td class="px-6 py-3 text-[var(--dash-text-muted)]">{{ $var->variable_description ?: '-' }}</td>
                        <td class="px-6 py-3 text-right">
                            @can('notification.variable.delete')
                            <form method="POST" action="{{ route('notification-templates.variables.destroy', [$template, $var]) }}" onsubmit="return confirm('Hapus variabel ini?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 [.mode-dark_&]:text-red-400 hover:text-red-700 [.mode-dark_&]:text-red-400">Hapus</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-[var(--dash-text-faint)]">Belum ada variabel terdokumentasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
