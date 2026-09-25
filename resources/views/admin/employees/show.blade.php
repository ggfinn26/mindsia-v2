@extends('layouts.dashboard')

@section('title', 'Detail Pegawai')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-end mb-8">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center shrink-0 border border-indigo-200">
                <span class="text-2xl font-bold text-indigo-700">{{ substr($employee->full_name, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-900 font-bold tracking-tight">{{ $employee->full_name }}</h1>
                <div class="flex items-center gap-3 mt-1.5">
                    <p class="text-sm text-gray-500 font-medium">{{ $employee->employee_code }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                        {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="mt-6 sm:mt-0 flex gap-3">
            <a href="{{ route('employees.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                Kembali
            </a>
            <a href="{{ route('employees.edit', $employee->id) }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                </svg>
                Edit Data
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-md bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-base font-bold text-gray-900">Informasi Pribadi & Kontak</h3>
                    <p class="mt-1 text-sm text-gray-500">Biodata dasar dan informasi kontak pegawai.</p>
                </div>
                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $employee->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Pegawai</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $employee->employee_code }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Kelamin</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $employee->gender === 'L' ? 'Laki-Laki' : ($employee->gender === 'P' ? 'Perempuan' : '-') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Lahir</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $employee->birthdate ? $employee->birthdate->format('d F Y') : '-' }}</dd>
                        </div>
                        <div class="sm:col-span-2 pt-4 border-t border-gray-100 mt-2"></div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">No. WhatsApp</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $employee->whatsapp_number ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Utama</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $employee->email ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-base font-bold text-gray-900">Penempatan & Jabatan</h3>
                </div>
                <div class="px-6 py-5">
                    <dl class="space-y-6">
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tingkat Penempatan</dt>
                            <dd>
                                @if($employee->is_hq)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                        <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M14.5 2A1.5 1.5 0 0013 3.5v13a1.5 1.5 0 001.5 1.5h2a1.5 1.5 0 001.5-1.5v-13A1.5 1.5 0 0016.5 2h-2zM5.5 6A1.5 1.5 0 004 7.5v9A1.5 1.5 0 005.5 18h2A1.5 1.5 0 009 16.5v-9A1.5 1.5 0 007.5 6h-2zM10.5 4A1.5 1.5 0 009 5.5v11A1.5 1.5 0 0010.5 18h2a1.5 1.5 0 001.5-1.5v-11A1.5 1.5 0 0012.5 4h-2z" clip-rule="evenodd" />
                                        </svg>
                                        Headquarters (Pusat)
                                    </span>
                                @elseif($employee->branch)
                                    <div class="text-sm font-medium text-gray-900">Cabang</div>
                                    <div class="text-sm text-gray-500 mt-0.5">{{ $employee->branch->branch_name }}</div>
                                @elseif($employee->area)
                                    <div class="text-sm font-medium text-gray-900">Area</div>
                                    <div class="text-sm text-gray-500 mt-0.5">{{ $employee->area->name }}</div>
                                @elseif($employee->region)
                                    <div class="text-sm font-medium text-gray-900">Region</div>
                                    <div class="text-sm text-gray-500 mt-0.5">{{ $employee->region->name }}</div>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </dd>
                        </div>
                        <div class="pt-4 border-t border-gray-100">
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Posisi Saat Ini</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">
                                {{ $employee->currentStatus?->position?->position_name ?? 'Belum ada jabatan' }}
                            </dd>
                            @if($employee->currentStatus)
                                <dd class="mt-1 text-xs text-gray-500 flex items-center">
                                    <svg class="mr-1.5 h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                                    </svg>
                                    Sejak {{ $employee->currentStatus->start_date ? $employee->currentStatus->start_date->format('d M Y') : '-' }}
                                </dd>
                            @endif
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
