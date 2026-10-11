@extends('layouts.dashboard')

@section('title', 'Kompensasi Pegawai - ' . $employee->full_name . ' - MINDSIA')
@section('header_title', 'Kompensasi Pegawai')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto space-y-6"
     x-data="{
        searchQuery: '',
        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmActionUrl: '',
        confirmMethod: 'DELETE',
        confirmActionText: 'Hapus'
     }">

    {{-- Page Header Pola B3 dengan Chip Back Button --}}
    <div>
        <a href="{{ route('payroll.components.index', ['tab' => 'compensation']) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Kelola Komponen Gaji
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Gaji & Bonus</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">
            Kompensasi Pegawai: {{ $employee->full_name }}
        </h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Kode Pegawai: <span class="font-mono font-semibold" style="color: var(--dash-text-heading);">{{ $employee->employee_code }}</span> - Tetapkan dan kelola komponen gaji serta nominal kompensasi khusus untuk pegawai ini.
        </p>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <div class="flex items-center gap-2 mb-2 font-semibold">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card Form Tambah / Perbarui Kompensasi --}}
    @canany(['payroll.employee_compensation.create', 'payroll.employee_compensation.update'])
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="px-6 py-4 flex items-center gap-2"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">add_circle</span>
            <div>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Tambah / Perbarui Kompensasi</h2>
                <p class="text-xs" style="color: var(--dash-text-muted);">
                    Jika komponen sudah pernah ditetapkan untuk pegawai ini, nominal akan diperbarui secara otomatis.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('employees.payroll.compensations.store', $employee) }}" class="p-6">
            @csrf
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Komponen Gaji <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <select name="payroll_component_id" required
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih komponen...</option>
                        @foreach ($components as $comp)
                            <option value="{{ $comp->id }}" @selected(old('payroll_component_id') == $comp->id)>
                                {{ $comp->component_name }} ({{ $comp->component_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Nilai (Rp) <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="number" name="value" value="{{ old('value') }}" required min="0" step="0.01"
                           placeholder="0"
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all font-mono"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Catatan
                    </label>
                    <input type="text" name="notes" value="{{ old('notes') }}"
                           placeholder="Catatan opsional"
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>

            <div class="mt-5 pt-4 flex justify-end"
                 style="border-top: 1px solid var(--dash-card-border);">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Kompensasi
                </button>
            </div>
        </form>
    </div>
    @endcanany

    {{-- Card Table Kompensasi Aktif --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div>
                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Daftar Kompensasi Aktif</h3>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                    Total {{ $compensations->count() }} komponen terkonfigurasi untuk pegawai ini.
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="relative w-full sm:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[16px]"
                      style="color: var(--dash-text-muted);">search</span>
                <input type="text" x-model="searchQuery" placeholder="Cari komponen..."
                       class="w-full pr-3 py-1.5 text-xs rounded-lg focus:outline-none transition-all"
                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Komponen</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Tipe</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Nilai</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Catatan</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($compensations as $comp)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            x-show="!searchQuery || '{{ strtolower($comp->payrollComponent->component_name . ' ' . $comp->payrollComponent->component_code) }}'.includes(searchQuery.toLowerCase())"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $comp->payrollComponent->component_name }}</p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">{{ $comp->payrollComponent->component_code }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($comp->payrollComponent->component_type === 'earning')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary); border: 1px solid var(--dash-card-border);">
                                        <span class="material-symbols-outlined text-[13px]">arrow_upward</span>
                                        Pendapatan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); color: var(--dash-danger); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent);">
                                        <span class="material-symbols-outlined text-[13px]">arrow_downward</span>
                                        Potongan
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right font-semibold font-mono" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($comp->value, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-xs" style="color: var(--dash-text-muted);">
                                {{ $comp->notes ?: '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                @canany(['payroll.employee_compensation.delete'])
                                    <button type="button"
                                            @click="showConfirmModal = true;
                                                    confirmTitle = 'Hapus Kompensasi Pegawai';
                                                    confirmMessage = 'Hapus penetapan komponen {{ addslashes($comp->payrollComponent->component_name) }} untuk {{ addslashes($employee->full_name) }}?';
                                                    confirmActionUrl = '{{ route('employees.payroll.compensations.destroy', [$employee, $comp]) }}';
                                                    confirmMethod = 'DELETE';
                                                    confirmActionText = 'Hapus';"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: var(--dash-danger);">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        Hapus
                                    </button>
                                @endcanany
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl block mx-auto mb-2"
                                      style="color: var(--dash-text-faint);">money_off</span>
                                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada kompensasi untuk pegawai ini.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                                    Gunakan formulir di atas untuk menetapkan komponen gaji pegawai.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>

            <div x-show="showConfirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: color-mix(in srgb, var(--dash-danger) 15%, transparent); color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[24px]">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showConfirmModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-danger);">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        <span x-text="confirmActionText"></span>
                    </button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
