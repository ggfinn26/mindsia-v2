@extends('layouts.dashboard')

@section('title', 'Detail Prospective Member')

@section('content')
@php
    $statusColors = [
        'ALMOST' => 'bg-yellow-100 text-yellow-700 [.mode-dark_&]:bg-yellow-500/10 [.mode-dark_&]:text-yellow-400',
        'YES'    => 'bg-blue-100 text-blue-700 [.mode-dark_&]:bg-blue-500/10 [.mode-dark_&]:text-blue-400',
        'FIXED'  => 'bg-green-100 text-green-700 [.mode-dark_&]:bg-green-500/10 [.mode-dark_&]:text-green-400',
        'NO'     => 'bg-red-100 text-red-700 [.mode-dark_&]:bg-red-500/10 [.mode-dark_&]:text-red-400',
    ];
    $statusDescriptions = [
        'ALMOST' => 'Hampir — masih perlu follow-up',
        'YES'    => 'Berminat — sedang proses',
        'FIXED'  => 'Pasti daftar',
        'NO'     => 'Tidak jadi',
    ];
@endphp
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start gap-4">
        <a href="{{ route('prospective-members.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight text-[var(--dash-text-heading)] truncate">Detail Lead</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Profil prospective member dan log follow-up.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #a7f3d0; background-color: #ecfdf5; color: #065f46;">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Profil Prospective Member --}}
        <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl p-5 md:col-span-1 space-y-4">
            <div class="flex items-center justify-between border-b border-[var(--dash-card-border)] pb-3">
                <h2 class="text-base font-semibold text-[var(--dash-text-heading)]">Profil Lead</h2>
                <a href="{{ route('prospective-members.edit', $lead) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[14px]">edit</span> Edit
                </a>
            </div>

            <div>
                <p class="text-xs text-[var(--dash-text-muted)]">Nama</p>
                <p class="text-sm font-semibold text-[var(--dash-text-heading)]">{{ $lead->full_name }}</p>
            </div>
            <div x-data="{
                open: false,
                templates: [],
                selected: null,
                leadName: {{ Illuminate\Support\Js::from($lead->full_name) }},
                marketerName: {{ Illuminate\Support\Js::from(auth()->user()->employee?->full_name ?? auth()->user()->name) }},
                waNumber: {{ Illuminate\Support\Js::from(preg_replace('/[^0-9]/', '', ltrim($lead->whatsapp_number ?? '', '0'))) }},
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
                <p class="text-xs text-[var(--dash-text-muted)]">WhatsApp</p>
                <p class="text-sm text-[var(--dash-text-body)] font-medium mb-1">{{ $lead->whatsapp_number }}</p>
                <button @click="open = !open"
                    class="inline-flex items-center gap-1 text-xs px-2 py-1 bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 text-emerald-700 [.mode-dark_&]:text-emerald-400 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 rounded-lg hover:bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[14px]">chat</span>
                    Hubungi via WA
                </button>
                <div x-show="open" @click.outside="open = false" class="mt-2 p-3 bg-[var(--dash-page-bg)] rounded-lg border border-[var(--dash-card-border)] space-y-2" style="display:none">
                    <template x-if="templates.length === 0">
                        <p class="text-xs text-[var(--dash-text-faint)] italic">Belum ada template. <a href="{{ route('wa-templates.index') }}" class="text-[var(--dash-primary)] underline">Buat template</a></p>
                    </template>
                    <template x-if="templates.length > 0">
                        <div class="space-y-2">
                            <select @change="selected = templates.find(t => t.id == $event.target.value) || null"
                                class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:outline-none">
                                <option value="">Pilih Template...</option>
                                <template x-for="t in templates" :key="t.id">
                                    <option :value="t.id" x-text="t.name"></option>
                                </template>
                            </select>
                            <p x-show="selected" x-text="renderedBody()" class="text-xs text-[var(--dash-text-muted)] bg-[var(--dash-card-bg)] p-2 rounded border border-[var(--dash-card-border)] whitespace-pre-wrap"></p>
                        </div>
                    </template>
                    <button @click="openWa()"
                        class="w-full text-xs px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 inline-flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">chat</span>
                        Buka WhatsApp
                    </button>
                </div>
            </div>
            @if ($lead->email)
                <div>
                    <p class="text-xs text-[var(--dash-text-muted)]">Email</p>
                    <p class="text-sm text-[var(--dash-text-heading)]">{{ $lead->email }}</p>
                </div>
            @endif
            @if ($lead->instagram)
                <div>
                    <p class="text-xs text-[var(--dash-text-muted)]">Instagram</p>
                    <p class="text-sm text-[var(--dash-text-heading)]">{{ $lead->instagram }}</p>
                </div>
            @endif
            <div>
                <p class="text-xs text-[var(--dash-text-muted)]">Jenis Kelamin</p>
                <p class="text-sm text-[var(--dash-text-heading)]">{{ $lead->gender === 'L' ? 'Laki-laki' : ($lead->gender === 'P' ? 'Perempuan' : '-') }}</p>
            </div>
            <div>
                <p class="text-xs text-[var(--dash-text-muted)]">Sekolah</p>
                <p class="text-sm text-[var(--dash-text-heading)]">
                    {{ $lead->institution?->institution_name ?? $lead->institution_name ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-[var(--dash-text-muted)]">Status</p>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$lead->status] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-muted)]' }}">
                    {{ $lead->status }}
                </span>
                <p class="text-xs text-[var(--dash-text-muted)] mt-1">{{ $statusDescriptions[$lead->status] ?? '' }}</p>
            </div>
            <div>
                <p class="text-xs text-[var(--dash-text-muted)]">PIC</p>
                <p class="text-sm text-[var(--dash-text-heading)]">{{ $lead->capturedBy->full_name ?? '-' }}</p>
            </div>
            @if ($lead->socialization)
                <div>
                    <p class="text-xs text-[var(--dash-text-muted)]">Dari Sosialisasi</p>
                    <a href="{{ route('socializations.show', $lead->socialization) }}" class="text-sm text-[var(--dash-primary)] hover:text-[var(--dash-primary)] hover:opacity-80">
                        {{ $lead->socialization->location_name }}
                    </a>
                </div>
            @endif
            @if ($lead->member)
                <div class="pt-2 border-t border-[var(--dash-card-border)]">
                    <p class="text-xs text-emerald-700 [.mode-dark_&]:text-emerald-400 font-medium">Sudah terdaftar sebagai Member</p>
                </div>
            @endif

            {{-- Update status --}}
            @canany(['marketing.prospective_member.update', 'marketing.prospective.view_area', 'marketing.prospective.view_national'])
                <div class="pt-3 border-t border-[var(--dash-card-border)]" x-data="{ showStatusForm: false }">
                    <button @click="showStatusForm = !showStatusForm"
                        class="inline-flex items-center justify-center gap-1.5 w-full px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                        style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-primary)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-body)'">
                        <span class="material-symbols-outlined text-[18px]">sync</span>
                        Update Status
                    </button>
                    <form x-show="showStatusForm" action="{{ route('prospective-members.update-status', $lead) }}" method="POST"
                          class="mt-3 space-y-3" style="display: none;">
                        @csrf @method('PATCH')
                        <select name="status" required
                            class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:outline-none">
                            @foreach (array_keys($statusColors) as $val)
                                <option value="{{ $val }}" {{ $lead->status === $val ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="change_reason" placeholder="Alasan perubahan (opsional)"
                            class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:outline-none">
                        <button type="submit" class="w-full px-3 py-2 text-sm text-white bg-[var(--dash-primary)] rounded-lg hover:opacity-90 inline-flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan
                        </button>
                    </form>
                </div>
            @endcanany
        </div>

        {{-- Log Follow-Up --}}
        <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl p-6 md:col-span-2 flex flex-col max-h-[600px]">
            <h2 class="text-base font-semibold text-[var(--dash-text-heading)] border-b pb-2 mb-4">Log Follow-Up</h2>

            <div class="flex-1 overflow-y-auto space-y-3 mb-4 pr-1">
                @forelse ($lead->statusHistories->sortByDesc('created_at') as $history)
                    <div class="bg-[var(--dash-page-bg)] rounded-lg p-3">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-xs text-[var(--dash-text-muted)]">{{ $history->created_at->format('d M Y H:i') }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $statusColors[$history->new_status] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-muted)]' }}">
                                {{ $history->new_status }}
                            </span>
                        </div>
                        @if ($history->change_reason)
                            <p class="text-sm text-[var(--dash-text-body)]">{{ $history->change_reason }}</p>
                        @endif
                        @if ($history->changed_by_employee_id)
                            <p class="text-xs text-[var(--dash-text-faint)] mt-1">oleh {{ $history->changedBy->full_name ?? '-' }}</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-[var(--dash-text-muted)] text-sm py-8">Belum ada log follow-up.</div>
                @endforelse
            </div>

            {{-- Form tambah catatan --}}
            <div class="border-t border-[var(--dash-card-border)] pt-4">
                <form action="{{ route('prospective-members.follow-ups.store', $lead) }}" method="POST" class="space-y-3">
                    @csrf
                    <textarea name="note" rows="3" required placeholder="Catatan hasil follow-up..."
                        class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:outline-none"></textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-[var(--dash-primary)] rounded-lg hover:opacity-90 inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan Catatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
