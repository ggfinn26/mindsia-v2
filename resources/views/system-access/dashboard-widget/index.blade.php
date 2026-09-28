@extends('layouts.dashboard')

@section('title', 'Konfigurasi Widget Dashboard')
@section('header_title', 'Konfigurasi Widget Dashboard')

@section('content')
@php
    $categoryLabel = [
        'financial'    => 'Finansial & Bisnis',
        'operations'   => 'Operasional',
        'hr'           => 'HR & Rekrutmen',
        'marketing'    => 'Marketing & Growth',
        'finance_admin'=> 'Finance & Administrasi',
        'personal'     => 'Personal Performance',
        'analytics'    => 'Analitik & Insight',
        'system'       => 'Sistem & Administrasi',
    ];
@endphp

<script>
window._widgetPositions = @json($positions->map(fn($p) => ['id' => $p->id, 'name' => $p->position_name]));
</script>

<div class="max-w-6xl mx-auto space-y-6" x-data="widgetConfig()">

    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Sistem & Akses</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Widget Dashboard</h2>
            <p class="mt-1 text-sm text-slate-500">Atur widget mana yang tampil untuk posisi/jabatan tertentu.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @foreach ($widgetKeys as $category => $keys)
        <div class="overflow-hidden border border-slate-200 bg-white">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="font-jakarta text-xs font-bold uppercase tracking-wider text-slate-500">
                    {{ $categoryLabel[$category] ?? ucfirst($category) }}
                </h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($keys as $wk)
                    @php
                        $assigned = $byWidget->get($wk->value, collect());
                    @endphp
                    <div class="flex items-start gap-4 px-5 py-4">
                        <div class="flex min-w-0 flex-1 items-start gap-3">
                            <span class="material-symbols-outlined mt-0.5 text-[18px] text-slate-400">{{ $wk->icon() }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900">{{ $wk->label() }}</p>
                                <p class="font-mono text-xs text-slate-400">{{ $wk->value }}</p>
                                @if ($assigned->isEmpty())
                                    <p class="mt-1.5 text-xs italic text-slate-400">Belum di-assign ke posisi manapun</p>
                                @else
                                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                                        @foreach ($assigned->sortBy('order') as $cfg)
                                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $cfg->is_enabled ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-500 line-through' }}">
                                                {{ $cfg->position?->position_name ?? '?' }}
                                                <span class="text-[10px] opacity-60">#{{ $cfg->order }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <button type="button"
                            @click="openModal($el)"
                            data-widget-key="{{ $wk->value }}"
                            data-widget-label="{{ $wk->label() }}"
                            data-assigned="{{ htmlspecialchars(json_encode($assigned->map(fn($c) => ['position_id' => $c->position_id, 'order' => $c->order, 'is_enabled' => (bool) $c->is_enabled])->values()), ENT_QUOTES) }}"
                            class="inline-flex shrink-0 min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            <span class="material-symbols-outlined text-[14px] mr-1">tune</span>
                            Kelola
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    {{-- Modal kelola posisi per widget --}}
    <div x-show="activeModal !== null" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="closeModal()"></div>
        <div class="relative w-full max-w-lg bg-white shadow-xl" @click.stop>
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h3 class="font-jakarta text-base font-bold text-slate-900" x-text="modalLabel"></h3>
                    <p class="text-xs text-slate-500">Pilih posisi yang dapat melihat widget ini</p>
                </div>
                <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" :action="'/system/dashboard-widgets/' + modalWidget + '/positions'" x-ref="assignForm">
                @csrf
                @method('PUT')

                <div class="max-h-[50vh] overflow-y-auto px-6 py-4 space-y-3">
                    <template x-if="modalPositions.length === 0">
                        <p class="py-4 text-center text-sm text-slate-400 italic">Belum ada posisi. Klik tombol di bawah untuk menambah.</p>
                    </template>

                    <template x-for="(row, index) in modalPositions" :key="index">
                        <div class="flex items-center gap-2">
                            <select :name="'positions[' + index + '][position_id]'" x-model="row.position_id" required
                                    data-no-enhance
                                    class="flex-1 border-slate-300 rounded-lg text-sm px-2 py-2 focus:border-[#215aac] focus:ring-[#215aac]">
                                <option value="">— Pilih posisi —</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                                @endforeach
                            </select>
                            <div class="flex items-center gap-1">
                                <span class="text-xs text-slate-500">Urutan</span>
                                <input type="number" :name="'positions[' + index + '][order]'" x-model="row.order"
                                    min="1" required class="w-16 border-slate-300 text-sm text-center focus:border-[#215aac] focus:ring-[#215aac]">
                            </div>
                            <div class="flex items-center">
                                <input type="hidden" :name="'positions[' + index + '][is_enabled]'" value="0">
                                <input type="checkbox" :name="'positions[' + index + '][is_enabled]'" value="1"
                                    :checked="row.is_enabled" @change="row.is_enabled = $event.target.checked"
                                    class="h-4 w-4 rounded border-slate-300 text-[#215aac]" title="Aktifkan">
                            </div>
                            <button type="button" @click="removePosition(index)" class="text-red-400 hover:text-red-600">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="border-t border-slate-100 px-6 py-3">
                    <button type="button" @click="addPosition()"
                        class="inline-flex items-center gap-1 text-sm font-semibold text-[#215aac] hover:underline">
                        <span class="material-symbols-outlined text-[16px]">add</span> Tambah posisi
                    </button>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button type="button" @click="closeModal()" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="inline-flex min-h-10 items-center bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('widgetConfig', () => ({
        activeModal: null,
        modalWidget: null,
        modalLabel: '',
        modalPositions: [],
        positions: window._widgetPositions || [],

        openModal(el) {
            const widgetKey = el.dataset.widgetKey;
            this.activeModal = widgetKey;
            this.modalWidget = widgetKey;
            this.modalLabel = el.dataset.widgetLabel;
            const assigned = JSON.parse(el.dataset.assigned);
            this.modalPositions = assigned.map(a => ({
                position_id: a.position_id,
                order: a.order,
                is_enabled: a.is_enabled,
            }));
        },

        addPosition() {
            this.modalPositions.push({ position_id: '', order: this.modalPositions.length + 1, is_enabled: true });
        },

        removePosition(index) {
            this.modalPositions.splice(index, 1);
        },

        closeModal() {
            this.activeModal = null;
            this.modalWidget = null;
            this.modalPositions = [];
        },
    }));
});
</script>
@endsection
