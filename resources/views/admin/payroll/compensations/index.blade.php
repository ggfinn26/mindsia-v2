@extends('layouts.dashboard')

@section('title', 'Kompensasi Pegawai')
@section('header_title', 'Kompensasi Pegawai')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div>
        <p class="text-sm font-medium text-[#725c00]">Payroll · Kompensasi</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $employee->full_name }}</h2>
        <p class="mt-0.5 font-mono text-sm text-slate-400">{{ $employee->employee_code }}</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Add Form --}}
    <form method="POST" action="{{ route('payroll.compensations.store', $employee) }}" class="border border-slate-200 bg-white p-5">
        @csrf
        <div class="mb-4 flex items-start gap-3">
            <span class="material-symbols-outlined mt-0.5 text-[#215aac]">add_circle</span>
            <div>
                <h3 class="font-jakarta text-base font-bold text-slate-900">Tambah / perbarui kompensasi</h3>
                <p class="mt-0.5 text-sm text-slate-500">Jika komponen sudah ada, nilai akan diperbarui.</p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Komponen</label>
                <select name="payroll_component_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    <option value="">Pilih komponen...</option>
                    @foreach ($components as $comp)
                        <option value="{{ $comp->id }}" @selected(old('payroll_component_id') == $comp->id)>
                            {{ $comp->component_name }} ({{ $comp->component_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nilai (Rp)</label>
                <input type="number" name="value" value="{{ old('value') }}" required min="0" step="0.01"
                       placeholder="0" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan</label>
                <input type="text" name="notes" value="{{ old('notes') }}"
                       class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan kompensasi
            </button>
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Kompensasi aktif</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                <tr>
                    <th class="px-5 py-3">Komponen</th>
                    <th class="px-5 py-3">Tipe</th>
                    <th class="px-5 py-3 text-right">Nilai</th>
                    <th class="px-5 py-3">Catatan</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($compensations as $comp)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $comp->payrollComponent->component_name }}</p>
                            <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $comp->payrollComponent->component_code }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $comp->payrollComponent->component_type === 'earning' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $comp->payrollComponent->component_type === 'earning' ? 'Pendapatan' : 'Potongan' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right font-semibold text-slate-900">Rp {{ number_format($comp->value, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-slate-500">{{ $comp->notes ?? '—' }}</td>
                        <td class="px-5 py-4 text-right">
                            <form method="POST" action="{{ route('payroll.compensations.destroy', [$employee, $comp]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus kompensasi ini?')"
                                        class="inline-flex min-h-9 items-center border border-red-200 px-3 text-xs font-semibold text-red-700 hover:bg-red-50">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-3xl text-slate-300">money_off</span>
                            <p class="mt-2 font-semibold text-slate-700">Belum ada kompensasi untuk pegawai ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
