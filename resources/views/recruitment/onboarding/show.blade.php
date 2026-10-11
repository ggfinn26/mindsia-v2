@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Onboarding</h1>
        <a href="{{ route('recruitment.onboarding.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-6">

        {{-- Status badge --}}
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Onboarding #{{ $employeeOnboarding->id }}</h2>
            @php
                $colors = ['draft'=>'bg-gray-100 text-gray-800','pending_review'=>'bg-yellow-100 text-yellow-800','approved'=>'bg-blue-100 text-blue-800','completed'=>'bg-green-100 text-green-800','rejected'=>'bg-red-100 text-red-800'];
                $color = $colors[$employeeOnboarding->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $color }}">
                {{ Str::title(str_replace('_', ' ', $employeeOnboarding->status)) }}
            </span>
        </div>

        {{-- Informasi Kandidat --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Informasi Kandidat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Nama Kandidat</span>
                    <p class="font-medium text-gray-900">{{ $employeeOnboarding->application->applicant->full_name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Email</span>
                    <p class="font-medium text-gray-900">{{ $employeeOnboarding->application->applicant->email ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">WhatsApp</span>
                    <p class="font-medium text-gray-900">{{ $employeeOnboarding->application->applicant->whatsapp_number ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Detail Onboarding --}}
        <div class="border-t border-gray-200 pt-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Detail Penempatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Cabang</span>
                    <p class="font-medium text-gray-900">{{ $employeeOnboarding->branch->branch_name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Posisi</span>
                    <p class="font-medium text-gray-900">{{ $employeeOnboarding->position->position_name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Tipe Karyawan</span>
                    <p class="font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $employeeOnboarding->employment_type ?? '-') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Tanggal Mulai</span>
                    <p class="font-medium text-gray-900">{{ $employeeOnboarding->start_date?->format('d M Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Review result (jika sudah di-review) --}}
        @if($employeeOnboarding->reviewed_at)
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Hasil Review</h3>
                <div class="text-sm space-y-1">
                    <p class="text-gray-600">Direview oleh: <span class="font-medium">{{ $employeeOnboarding->reviewedBy->full_name ?? '-' }}</span></p>
                    <p class="text-gray-600">Tanggal: <span class="font-medium">{{ $employeeOnboarding->reviewed_at->format('d M Y H:i') }}</span></p>
                    @if($employeeOnboarding->review_notes)
                        <p class="text-gray-600">Catatan: <span class="font-medium">{{ $employeeOnboarding->review_notes }}</span></p>
                    @endif
                    @if($employeeOnboarding->rejection_reason)
                        <p class="text-red-600">Alasan penolakan: <span class="font-medium">{{ $employeeOnboarding->rejection_reason }}</span></p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Employee baru (jika completed) --}}
        @if($employeeOnboarding->employee)
            <div class="border-t border-gray-200 pt-4 bg-green-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wider mb-2">Employee Baru Dibuat</h3>
                <p class="text-sm font-medium text-green-900">{{ $employeeOnboarding->employee->full_name }} — {{ $employeeOnboarding->employee->employee_code }}</p>
            </div>
        @endif

        {{-- Actions --}}
        <div class="border-t border-gray-200 pt-4 space-y-4">

            {{-- BOARD review form (pending_review) --}}
            @if($employeeOnboarding->status === 'pending_review')
                @can('recruitment.onboarding.review')
                    <div x-data="{ showReject: false }">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Review Onboarding</h3>
                        <form action="{{ route('recruitment.onboarding.review', $employeeOnboarding) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Review</label>
                                <textarea name="review_notes" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                            </div>
                            <div x-show="showReject">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                                <textarea name="rejection_reason" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" name="decision" value="approved" class="btn bg-blue-600 hover:bg-blue-700 text-white">
                                    Approve Onboarding
                                </button>
                                <button type="button" @click="showReject = !showReject" class="btn bg-white border-red-300 text-red-600 hover:bg-red-50">
                                    Tolak
                                </button>
                                <button type="submit" name="decision" value="rejected" x-show="showReject" class="btn bg-red-500 hover:bg-red-600 text-white">
                                    Konfirmasi Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            @endif

            {{-- Complete (approved) --}}
            @if($employeeOnboarding->status === 'approved')
                @can('recruitment.onboarding.update')
                    <form action="{{ route('recruitment.onboarding.complete', $employeeOnboarding) }}" method="POST" onsubmit="return confirm('Selesaikan onboarding dan buat akun employee baru?')">
                        @csrf
                        <button type="submit" class="btn bg-green-600 hover:bg-green-700 text-white">
                            Selesaikan Onboarding — Buat Employee
                        </button>
                    </form>
                @endcan
            @endif

        </div>
    </div>
</div>
@endsection
