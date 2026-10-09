@extends('layouts.dashboard')

@section('title', 'Struktur Kepemimpinan')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8" x-data="{ showModal: false, deleteUrl: '', deleteName: '' }" @keydown.escape.window="showModal = false">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-[color:var(--dash-text-heading)]">Struktur Kepemimpinan</h1>
        @can('landing.leader.manage')
        <a href="{{ route('admin.landing.leaders.create') }}"
           class="inline-flex items-center gap-2 bg-[var(--dash-primary)] text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-colors">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Tambah Pemimpin
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @if($leaders->isEmpty())
    <div class="p-12 border-2 border-dashed border-[color:var(--dash-card-border)] rounded-xl text-center text-[color:var(--dash-text-faint)]">
        Belum ada data. Klik "Tambah Pemimpin" untuk memulai.
    </div>
    @else
    <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[var(--dash-nav-hover-bg)] border-b border-[color:var(--dash-card-border)]">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-[color:var(--dash-text-body)]">Foto</th>
                    <th class="px-4 py-3 text-left font-semibold text-[color:var(--dash-text-body)]">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-[color:var(--dash-text-body)]">Jabatan</th>
                    <th class="px-4 py-3 text-center font-semibold text-[color:var(--dash-text-body)]">Urutan</th>
                    <th class="px-4 py-3 text-center font-semibold text-[color:var(--dash-text-body)]">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-[color:var(--dash-text-body)]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                @foreach($leaders as $leader)
                <tr class="hover:bg-[var(--dash-nav-hover-bg)] transition-colors">
                    <td class="px-4 py-3">
                        <div class="w-10 h-10 rounded-full bg-[var(--dash-nav-hover-bg)] overflow-hidden border border-[color:var(--dash-card-border)]">
                            @if($leader->photo_url)
                                <img src="{{ $leader->photo_url }}" alt="{{ $leader->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-[color:var(--dash-text-faint)]">
                                    <span class="material-symbols-outlined text-[20px]">person</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 font-medium text-[color:var(--dash-text-heading)]">{{ $leader->name }}</td>
                    <td class="px-4 py-3 text-[color:var(--dash-text-body)]">{{ $leader->title }}</td>
                    <td class="px-4 py-3 text-center text-[color:var(--dash-text-muted)]">{{ $leader->sort_order }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $leader->is_active ? 'bg-green-100 text-green-700' : 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-muted)]' }}">
                            {{ $leader->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            @can('landing.leader.manage')
                            <a href="{{ route('admin.landing.leaders.edit', $leader) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors inline-flex items-center gap-1.5 border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"><span class="material-symbols-outlined text-[14px]">edit</span>Edit</a>
                            @endcan
                            @can('landing.leader.manage')
                            <button type="button"
                                @click="deleteUrl = '{{ route('admin.landing.leaders.destroy', $leader) }}'; deleteName = '{{ addslashes($leader->name) }}'; showModal = true"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors text-white bg-[#dc2626] hover:opacity-90 inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">delete</span>Hapus</button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none" @click.self="showModal = false">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative bg-[var(--dash-card-bg)] rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-600 text-[20px]">delete</span>
                </div>
                <div>
                    <h3 class="font-semibold text-[color:var(--dash-text-heading)]">Hapus Pemimpin</h3>
                    <p class="text-sm text-[color:var(--dash-text-muted)]">Hapus <span class="font-medium" x-text="deleteName"></span>? Tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showModal = false"
                    class="px-4 py-2 text-sm font-medium text-[color:var(--dash-text-body)] border border-[color:var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-nav-hover-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                <form :action="deleteUrl" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#dc2626] hover:opacity-90 rounded-lg inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">delete</span>Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
