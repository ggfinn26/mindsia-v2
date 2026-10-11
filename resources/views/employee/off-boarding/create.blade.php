@extends('layouts.dashboard')

@section('title', 'Offboarding Pegawai')
@section('header_title', 'Offboarding Pegawai')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('resign-requests.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Permintaan Resign
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Offboarding — {{ $employee->full_name }}</h2>
        <p class="mt-1 text-sm text-slate-500">Lengkapi proses offboarding sebelum menonaktifkan akun pegawai.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @if (isset($checklist) && $checklist->isNotEmpty())
        <div class="border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3">
                <h3 class="font-jakarta text-sm font-bold text-slate-900">Checklist Offboarding</h3>
            </div>
            <ul class="divide-y divide-slate-100 text-sm">
                @foreach ($checklist as $item)
                    <li class="flex items-center gap-3 px-5 py-3">
                        <span class="material-symbols-outlined text-[18px] {{ $item['done'] ? 'text-emerald-500' : 'text-slate-300' }}">
                            {{ $item['done'] ? 'check_circle' : 'radio_button_unchecked' }}
                        </span>
                        <span class="{{ $item['done'] ? 'line-through text-slate-400' : 'text-slate-700' }}">{{ $item['label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ showConfirm: false }">
    <form method="POST" action="{{ route('off-boarding.store', $employee) }}" class="border border-red-100 bg-white p-6 space-y-4" id="offboarding-form">
        @csrf
        <div class="flex items-start gap-3 rounded border border-red-200 bg-red-50 p-4">
            <span class="material-symbols-outlined text-red-600">warning</span>
            <div class="text-sm text-red-800">
                <p class="font-semibold">Tindakan ini tidak dapat dibatalkan.</p>
                <p>Akun login pegawai akan dihapus dan status kepegawaian dinonaktifkan.</p>
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan offboarding</label>
            <textarea name="reason_off_boarding" rows="3" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]" placeholder="Catatan tambahan (opsional)...">{{ old('reason_off_boarding') }}</textarea>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal offboarding</label>
            <input type="date" name="off_boarding_date" value="{{ old('off_boarding_date', today()->toDateString()) }}" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="button" @click="showConfirm = true"
                    class="inline-flex min-h-10 items-center gap-2 bg-red-600 px-5 text-sm font-semibold text-white hover:bg-red-700">
                <span class="material-symbols-outlined text-[18px]">logout</span> Proses Offboarding
            </button>
            <a href="{{ route('resign-requests.index') }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>

    {{-- Modal Konfirmasi Offboarding --}}
    <template x-teleport="body">
        <div x-show="showConfirm" class="fixed inset-0 z-[9999] flex items-center justify-center" style="display:none">
            <div class="fixed inset-0 bg-slate-900/50" @click="showConfirm = false"></div>
            <div class="relative w-full max-w-md border border-slate-200 bg-white shadow-xl">
                <div class="px-6 py-5">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-red-600">warning</span>
                        <div>
                            <h3 class="font-jakarta text-base font-bold text-slate-900">Proses Offboarding?</h3>
                            <p class="mt-1 text-sm text-slate-600">Akun login <strong>{{ $employee->full_name }}</strong> akan dihapus permanen dan status kepegawaian dinonaktifkan. Tindakan ini tidak dapat diurungkan.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                    <button type="button" @click="showConfirm = false" class="inline-flex min-h-9 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="button" @click="document.getElementById('offboarding-form').submit()"
                            class="inline-flex min-h-9 items-center gap-2 bg-red-600 px-4 text-sm font-semibold text-white hover:bg-red-700">
                        <span class="material-symbols-outlined text-[16px]">logout</span> Ya, Proses Offboarding
                    </button>
                </div>
            </div>
        </div>
    </template>

    </div>
</div>
@endsection
