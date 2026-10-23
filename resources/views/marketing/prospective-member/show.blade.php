@extends('layouts.dashboard')

@section('title', 'Detail Calon Anggota - MINDSIA')
@section('header_title', 'Detail Calon Anggota')

@section('content')
@php
    $statusColors = [
        'ALMOST' => 'bg-yellow-100 text-yellow-700',
        'YES'    => 'bg-blue-100 text-blue-700',
        'FIXED'  => 'bg-emerald-100 text-emerald-700',
        'NO'     => 'bg-red-100 text-red-700',
    ];
    $statusDescriptions = [
        'ALMOST' => 'Hampir - masih perlu follow-up',
        'YES'    => 'Berminat - sedang proses',
        'FIXED'  => 'Pasti daftar',
        'NO'     => 'Tidak jadi',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <a href="{{ route('prospective-members.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Calon Anggota
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Pemasaran</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $lead->full_name }}</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Detail profil calon anggota dan catatan riwayat interaksi follow-up.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('prospective-members.edit', $lead) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[14px]">edit</span>
                Edit Profil
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Profil Prospective Member --}}
        <div class="rounded-2xl md:col-span-1"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl flex items-center justify-between"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Profil Calon</h2>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Nama Lengkap</p>
                    <p class="text-sm font-semibold mt-0.5" style="color: var(--dash-text-heading);">{{ $lead->full_name }}</p>
                </div>

                @php
                    $waNum = preg_replace('/[^0-9]/', '', $lead->whatsapp_number ?? '');
                    if (str_starts_with($waNum, '0')) {
                        $waNum = '62'.substr($waNum, 1);
                    } elseif ($waNum && ! str_starts_with($waNum, '62')) {
                        $waNum = '62'.$waNum;
                    }
                @endphp
                <div x-data="{
                    open: false,
                    templates: [],
                    selected: null,
                    leadName: {{ Illuminate\Support\Js::from($lead->full_name) }},
                    marketerName: {{ Illuminate\Support\Js::from(auth()->user()->employee?->full_name ?? auth()->user()->name) }},
                    waNumber: {{ Illuminate\Support\Js::from($waNum) }},
                    init() {
                        fetch('{{ route('wa-templates.json') }}')
                            .then(r => r.json())
                            .then(data => { this.templates = data; });
                    },
                    renderedBody() {
                        if (!this.selected) return '';
                        return this.selected.body
                            .replace(/\{nama\}/g, this.leadName)
                            .replace(/\{marketer\}/g, this.marketerName);
                    },
                    openWa() {
                        const text = this.renderedBody();
                        const url = 'https://wa.me/' + this.waNumber + (text ? '?text=' + encodeURIComponent(text) : '');
                        window.open(url, '_blank');
                        this.open = false;
                    }
                }">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">WhatsApp</p>
                    <p class="text-sm font-medium mt-0.5" style="color: var(--dash-text-heading);">{{ $lead->whatsapp_number }}</p>
                    <div class="mt-2">
                        <button type="button" @click="open = !open"
                            class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg font-medium transition-colors"
                            style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
                            <span class="material-symbols-outlined text-[16px]">chat</span>
                            Hubungi via WA
                        </button>
                    </div>
                    <div x-show="open" @click.outside="open = false"
                         class="mt-3 p-3.5 rounded-xl space-y-2.5"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); display: none;">
                        <template x-if="templates.length === 0">
                            <p class="text-xs italic" style="color: var(--dash-text-muted);">
                                Belum ada template. <a href="{{ route('wa-templates.index') }}" class="underline font-semibold" style="color: var(--dash-primary);">Buat template</a>
                            </p>
                        </template>
                        <template x-if="templates.length > 0">
                            <div class="space-y-2">
                                <select @change="selected = templates.find(t => t.id == $event.target.value) || null"
                                    class="w-full px-2.5 py-1.5 text-xs rounded-lg focus:outline-none transition-all"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="">Pilih Template ...</option>
                                    <template x-for="t in templates" :key="t.id">
                                        <option :value="t.id" x-text="t.name"></option>
                                    </template>
                                </select>
                                <p x-show="selected" x-text="renderedBody()"
                                   class="text-xs p-2.5 rounded-lg whitespace-pre-wrap leading-relaxed"
                                   style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"></p>
                            </div>
                        </template>
                        <button type="button" @click="openWa()"
                            class="w-full text-xs px-3 py-2 text-white font-semibold rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: #059669;">
                            Buka WhatsApp
                        </button>
                    </div>
                </div>

                @if ($lead->email)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Email</p>
                        <p class="text-sm mt-0.5" style="color: var(--dash-text-body);">{{ $lead->email }}</p>
                    </div>
                @endif

                @if ($lead->instagram)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Instagram</p>
                        <p class="text-sm mt-0.5" style="color: var(--dash-text-body);">{{ $lead->instagram }}</p>
                    </div>
                @endif

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Jenis Kelamin</p>
                    <p class="text-sm mt-0.5" style="color: var(--dash-text-body);">{{ $lead->gender === 'L' ? 'Laki-laki' : ($lead->gender === 'P' ? 'Perempuan' : '-') }}</p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Sekolah / Institusi</p>
                    <p class="text-sm mt-0.5" style="color: var(--dash-text-body);">
                        {{ $lead->institution?->institution_name ?? $lead->institution_name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Status</p>
                    <div class="mt-1">
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $statusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $lead->status }}
                        </span>
                        <p class="text-xs mt-1" style="color: var(--dash-text-muted);">{{ $statusDescriptions[$lead->status] ?? '' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Marketer (PIC)</p>
                    <p class="text-sm mt-0.5" style="color: var(--dash-text-body);">{{ $lead->capturedBy->full_name ?? '-' }}</p>
                </div>

                @if ($lead->socialization)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Dari Sosialisasi</p>
                        <a href="{{ route('socializations.show', $lead->socialization) }}"
                           class="text-sm font-medium hover:underline mt-0.5 block"
                           style="color: var(--dash-primary);">
                            {{ $lead->socialization->location_name }}
                        </a>
                    </div>
                @endif

                @if ($lead->member)
                    <div class="pt-3 border-t" style="border-color: var(--dash-card-border);">
                        <p class="text-xs font-semibold" style="color: #059669;">Sudah terdaftar sebagai Member ✓</p>
                    </div>
                @endif

                {{-- Update status --}}
                @canany(['marketing.prospective_member.update', 'marketing.prospective.view_area', 'marketing.prospective.view_national'])
                    <div class="pt-3 border-t" style="border-color: var(--dash-card-border);" x-data="{ showStatusForm: false }">
                        <button type="button" @click="showStatusForm = !showStatusForm"
                            class="inline-flex items-center gap-1 text-xs font-semibold hover:underline"
                            style="color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[14px]">tune</span>
                            Update Status
                        </button>
                        <form x-show="showStatusForm" action="{{ route('prospective-members.update-status', $lead) }}" method="POST"
                              class="mt-3 space-y-3" style="display: none;">
                            @csrf @method('PATCH')
                            <select name="status" required
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                                @foreach (array_keys($statusColors) as $val)
                                    <option value="{{ $val }}" {{ $lead->status === $val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="change_reason" placeholder="Alasan perubahan (opsional)"
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[16px]">save</span>
                                Simpan
                            </button>
                        </form>
                    </div>
                @endcanany
            </div>
        </div>

        {{-- Log Follow-Up --}}
        <div class="rounded-2xl md:col-span-2 flex flex-col max-h-[650px]"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Log Follow-Up</h2>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-3">
                @forelse ($lead->statusHistories->sortByDesc('created_at') as $history)
                    <div class="rounded-xl p-4 transition-colors"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <div class="flex justify-between items-start gap-2 mb-1">
                            <span class="text-xs font-medium" style="color: var(--dash-text-muted);">
                                {{ $history->created_at->format('d M Y H:i') }}
                            </span>
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full {{ $statusColors[$history->new_status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $history->new_status }}
                            </span>
                        </div>
                        @if ($history->change_reason)
                            <p class="text-sm font-medium mt-1 leading-relaxed" style="color: var(--dash-text-heading);">
                                {{ $history->change_reason }}
                            </p>
                        @endif
                        @if ($history->changed_by_employee_id)
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">
                                oleh {{ $history->changedBy->full_name ?? '-' }}
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12">
                        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">history_edu</span>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada catatan follow-up.</p>
                    </div>
                @endforelse
            </div>

            {{-- Form tambah catatan --}}
            <div class="p-6 rounded-b-2xl border-t" style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                <form action="{{ route('prospective-members.follow-ups.store', $lead) }}" method="POST" class="space-y-3">
                    @csrf
                    <textarea name="note" rows="3" required placeholder="Tuliskan catatan hasil follow-up..."
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all resize-none"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'"></textarea>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">add_comment</span>
                            Simpan Catatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
