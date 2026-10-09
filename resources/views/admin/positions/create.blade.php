@extends('layouts.dashboard')

@section('title', 'Tambah Posisi - MINDSIA')
@section('header_title', 'Tambah Posisi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('positions.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Tambah Posisi</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Buat posisi / jabatan baru.
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm mb-6"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('positions.store') }}" method="POST">
            @csrf
            <div class="p-6 sm:p-8 space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nama Posisi --}}
                    <div>
                        <label class="block text-sm font-medium mb-1.5" for="position_name"
                               style="color: var(--dash-text-body);">
                            Nama Posisi <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" id="position_name" name="position_name"
                               value="{{ old('position_name') }}" required
                               placeholder="Contoh: Marketing Associate, Tutor, dll.">
                    </div>

                    {{-- Hierarchy Order --}}
                    <div>
                        <label class="block text-sm font-medium mb-1.5" for="hierarchy_order"
                               style="color: var(--dash-text-body);">
                            Urutan Hierarki <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" id="hierarchy_order" name="hierarchy_order"
                               value="{{ old('hierarchy_order', 0) }}" required min="0">
                        <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">
                            Angka lebih kecil = hierarki lebih tinggi (1 = Direktur).
                        </p>
                    </div>
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-sm font-medium mb-1.5" for="role_id"
                           style="color: var(--dash-text-body);">
                        Role Sistem Terkait <span style="color: #dc2626;">*</span>
                    </label>
                    <select id="role_id" name="role_id" required>
                        <option value="" disabled @if(!old('role_id')) selected @endif>Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                    @if(old('role_id') == $role->id) selected @endif>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="px-6 py-4 flex justify-end"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    Simpan Posisi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
