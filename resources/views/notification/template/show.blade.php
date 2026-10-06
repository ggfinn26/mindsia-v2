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
            <a href="{{ route('notification-templates.index') }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Template
            </a>
            <h1 class="mt-2 font-jakarta text-2xl font-bold tracking-tight text-slate-900">Detail Template Notifikasi</h1>
        </div>
        @can('notification.template.update')
        <a href="{{ route('notification-templates.edit', $template) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
            <span class="material-symbols-outlined text-[16px]">edit</span> Edit
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <!-- Info template -->
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Template Key</p>
                <code class="mt-1 block rounded bg-slate-100 px-3 py-1.5 text-sm text-slate-700 font-mono">{{ $template->template_key }}</code>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tipe</p>
                @php $typeColors = ['email' => 'bg-blue-100 text-blue-800', 'telegram' => 'bg-sky-100 text-sky-800', 'in-app' => 'bg-violet-100 text-violet-800']; @endphp
                <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$template->type] ?? 'bg-slate-100 text-slate-700' }}">{{ $template->type }}</span>
            </div>
        </div>

        @if($template->subject)
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Subject (Email)</p>
            <p class="mt-1 text-sm text-slate-700">{{ $template->subject }}</p>
        </div>
        @endif

        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Body</p>
            <pre class="mt-1 rounded-lg bg-slate-50 border border-slate-100 p-4 text-sm text-slate-700 whitespace-pre-wrap font-mono">{{ $template->body }}</pre>
        </div>
    </div>

    <!-- Variabel -->
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800">Dokumentasi Variabel</h2>
            @can('notification.variable.create')
            <button @click="showVarForm = !showVarForm; editVarId = null; varName=''; varType=''; varDesc=''"
                class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200 transition-colors">
                <span class="material-symbols-outlined text-[14px]">add</span> Tambah Variabel
            </button>
            @endcan
        </div>

        @can('notification.variable.create')
        <div x-show="showVarForm" class="border-b border-slate-100 bg-slate-50 px-6 py-4">
            <form action="{{ route('notification-templates.variables.store', $template) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input name="variable_name" type="text" placeholder="nama_variabel" value="{{ old('variable_name') }}"
                    class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] font-mono" required />
                <input name="variable_type" type="text" placeholder="string / date / number" value="{{ old('variable_type') }}"
                    class="w-40 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required />
                <input name="variable_description" type="text" placeholder="Deskripsi (opsional)" value="{{ old('variable_description') }}"
                    class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                <button type="submit" class="rounded-lg bg-[#215aac] px-4 py-2 text-sm font-bold text-white hover:bg-[#194a91] transition-colors whitespace-nowrap">Simpan</button>
            </form>
        </div>
        @endcan

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Nama Variabel</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($template->variables as $var)
                    <tr>
                        <td class="px-6 py-3"><code class="rounded bg-slate-100 px-2 py-0.5 text-xs font-mono text-slate-700">@php echo e('{{' . $var->variable_name . '}}'); @endphp</code></td>
                        <td class="px-6 py-3 text-slate-500">{{ $var->variable_type }}</td>
                        <td class="px-6 py-3 text-slate-500">{{ $var->variable_description ?: '-' }}</td>
                        <td class="px-6 py-3 text-right">
                            @can('notification.variable.delete')
                            <form method="POST" action="{{ route('notification-templates.variables.destroy', [$template, $var]) }}" onsubmit="return confirm('Hapus variabel ini?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-slate-400">Belum ada variabel terdokumentasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
