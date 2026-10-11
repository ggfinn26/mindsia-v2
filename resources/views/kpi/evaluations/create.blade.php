@extends('layouts.dashboard')

@section('title', 'Buat Evaluasi KPI')
@section('header_title', 'Buat Evaluasi KPI')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <a href="{{ route('kpi.evaluations.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Evaluasi
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">KPI</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Buat Evaluasi KPI</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Indikator bertipe otomatis akan diisi dari data sistem sesuai periode.</p>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="POST" action="{{ route('kpi.evaluations.store') }}" class="p-6 space-y-5">
            @csrf

            {{-- Pegawai --}}
            <div>
                <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                    Pegawai yang dievaluasi <span style="color: #dc2626;">*</span>
                </label>
                <select name="employee_id" required
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    <option value="">Pilih pegawai ...</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Template KPI --}}
            <div>
                <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                    Template KPI <span style="color: #dc2626;">*</span>
                </label>
                <select name="kpi_template_id" required
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    <option value="">Pilih template ...</option>
                    @foreach ($templates as $t)
                        <option value="{{ $t->id }}" @selected(old('kpi_template_id') == $t->id)>{{ $t->template_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tipe periode --}}
            <div>
                <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                    Tipe periode <span style="color: #dc2626;">*</span>
                </label>
                <select name="period_type" required
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @foreach (['monthly' => 'Bulanan', 'quarterly' => 'Kuartalan', 'yearly' => 'Tahunan'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('period_type') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal periode --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Mulai periode <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="date" name="period_start_date" value="{{ old('period_start_date') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Akhir periode <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="date" name="period_end_date" value="{{ old('period_end_date') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2" style="border-top: 1px solid var(--dash-card-border);">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Buat Evaluasi
                </button>
                <a href="{{ route('kpi.evaluations.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
