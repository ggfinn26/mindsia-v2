@extends('layouts.dashboard')

@section('title', 'Offboarding Pegawai')
@section('header_title', 'Offboarding Pegawai')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('resign-requests.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Permintaan Resign
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Offboarding — {{ $employee->full_name }}</h1>
        <p class="mt-1 text-sm text-[color:var(--dash-text-muted)]">Lengkapi proses offboarding sebelum menonaktifkan akun pegawai.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @if (isset($checklist) && $checklist->isNotEmpty())
        <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
            <div class="border-b border-[color:var(--dash-card-border)] px-5 py-3">
                <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Checklist Offboarding</h3>
            </div>
            <ul class="divide-y divide-[color:var(--dash-card-border)] text-sm">
                @foreach ($checklist as $item)
                    <li class="flex items-center gap-3 px-5 py-3">
                        <span class="material-symbols-outlined text-[18px] {{ $item['done'] ? 'text-emerald-500' : 'text-slate-300' }}">
                            {{ $item['done'] ? 'check_circle' : 'radio_button_unchecked' }}
                        </span>
                        <span class="{{ $item['done'] ? 'line-through text-[color:var(--dash-text-faint)]' : 'text-[color:var(--dash-text-body)]' }}">{{ $item['label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ showConfirm: false }">
    <form method="POST" action="{{ route('off-boarding.store', $employee) }}" class="border border-red-100 bg-[var(--dash-card-bg)] p-6 space-y-4" id="offboarding-form">
        @csrf
        <div class="flex items-start gap-3 rounded border border-red-200 bg-red-50 p-4">
            <span class="material-symbols-outlined text-red-600">warning</span>
            <div class="text-sm text-red-800">
                <p class="font-semibold">Tindakan ini tidak dapat dibatalkan.</p>
                <p>Akun login pegawai akan dihapus dan status kepegawaian dinonaktifkan.</p>
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Catatan offboarding</label>
            <textarea name="reason_off_boarding" rows="3" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" placeholder="Catatan tambahan (opsional)...">{{ old('reason_off_boarding') }}</textarea>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Tanggal offboarding</label>
            <input type="date" name="off_boarding_date" value="{{ old('off_boarding_date', today()->toDateString()) }}" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="button" @click="showConfirm = true"
                    class="inline-flex min-h-10 items-center gap-2 bg-red-600 px-5 text-sm font-semibold text-white hover:bg-red-700">
                <span class="material-symbols-outlined text-[18px]">logout</span> Proses Offboarding
            </button>
            <a href="{{ route('resign-requests.index') }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</a>
        </div>
    </form>

    {{-- Modal Konfirmasi Offboarding --}}
    <template x-teleport="body">
        <div x-show="showConfirm" class="fixed inset-0 z-[9999] flex items-center justify-center" style="display:none">
            <div class="fixed inset-0 bg-slate-900/50" @click="showConfirm = false"></div>
            <div class="relative w-full max-w-md border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-xl">
                <div class="px-6 py-5">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-red-600">warning</span>
                        <div>
                            <h3 class="font-jakarta text-base font-bold text-[color:var(--dash-text-heading)]">Proses Offboarding?</h3>
                            <p class="mt-1 text-sm text-[color:var(--dash-text-body)]">Akun login <strong>{{ $employee->full_name }}</strong> akan dihapus permanen dan status kepegawaian dinonaktifkan. Tindakan ini tidak dapat diurungkan.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-[color:var(--dash-card-border)] px-6 py-4">
                    <button type="button" @click="showConfirm = false" class="inline-flex min-h-9 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</button>
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
