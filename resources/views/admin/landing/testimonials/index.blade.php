@extends('layouts.dashboard')

@section('title', 'Kelola Testimoni')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8" x-data="{ showModal: false, deleteUrl: '' }" @keydown.escape.window="showModal = false">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-[color:var(--dash-text-heading)]">Testimoni Landing Page</h1>
        @can('landing.testimonial.manage')
        <div class="flex gap-2">
            <a href="{{ route('admin.landing.testimonials.create') }}"
               class="inline-flex items-center gap-2 bg-[var(--dash-primary)] text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-colors">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Testimoni
            </a>
        </div>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @if($testimonials->isEmpty())
    <div class="p-12 border-2 border-dashed border-[color:var(--dash-card-border)] rounded-xl text-center text-[color:var(--dash-text-faint)]">
        Belum ada testimoni. Klik "Tambah Testimoni" untuk memulai.
    </div>
    @else
    <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[var(--dash-nav-hover-bg)] border-b border-[color:var(--dash-card-border)]">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-[color:var(--dash-text-body)]">Tipe</th>
                    <th class="px-4 py-3 text-left font-semibold text-[color:var(--dash-text-body)]">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-[color:var(--dash-text-body)]">Konten</th>
                    <th class="px-4 py-3 text-center font-semibold text-[color:var(--dash-text-body)]">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-[color:var(--dash-text-body)]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                @foreach($testimonials as $testi)
                <tr class="hover:bg-[var(--dash-nav-hover-bg)] transition-colors">
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $testi->type->value === 'image' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $testi->type->value === 'image' ? 'Gambar' : 'Teks' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-medium text-[color:var(--dash-text-heading)]">{{ $testi->name }}</td>
                    <td class="px-4 py-3 text-[color:var(--dash-text-muted)] max-w-xs">
                        @if($testi->isImage())
                            <img src="{{ $testi->image_url }}" alt="{{ $testi->name }}" class="h-10 w-16 object-cover rounded">
                        @else
                            <span class="line-clamp-2">{{ $testi->quote }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $testi->is_active ? 'bg-green-100 text-green-700' : 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-muted)]' }}">
                            {{ $testi->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            @can('landing.testimonial.manage')
                            <a href="{{ route('admin.landing.testimonials.edit', $testi) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors inline-flex items-center gap-1.5 border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"><span class="material-symbols-outlined text-[14px]">edit</span>Edit</a>
                            @endcan
                            @can('landing.testimonial.manage')
                            <button type="button"
                                @click="deleteUrl = '{{ route('admin.landing.testimonials.destroy', $testi) }}'; showModal = true"
                                class="px-3 py-1.5 text-red-500 hover:bg-red-50 rounded text-xs font-medium transition-colors inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">delete</span>Hapus</button>
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
                    <h3 class="font-semibold text-[color:var(--dash-text-heading)]">Hapus Testimoni</h3>
                    <p class="text-sm text-[color:var(--dash-text-muted)]">Tindakan ini tidak dapat dibatalkan.</p>
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
