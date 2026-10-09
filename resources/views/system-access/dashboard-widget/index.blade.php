@extends('layouts.dashboard')

@section('title', 'Widget Dashboard - MINDSIA')
@section('header_title', 'Widget Dashboard')

@section('content')
@php
    $categoryLabel = [
        'financial'     => 'Finansial & Bisnis',
        'operations'    => 'Operasional',
        'hr'            => 'HR & Rekrutmen',
        'marketing'     => 'Marketing & Growth',
        'finance_admin' => 'Finance & Administrasi',
        'personal'      => 'Personal Performance',
        'analytics'     => 'Analitik & Insight',
        'system'        => 'Sistem & Administrasi',
    ];
@endphp

<script>
window._widgetPositions = @json($positions->map(fn($p) => ['id' => $p->id, 'name' => $p->position_name]));
</script>

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6"
     x-data="widgetConfig()">

    {{-- Page header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Sistem & Akses</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Widget Dashboard</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Atur widget mana yang tampil untuk posisi/jabatan tertentu.
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Widget categories --}}
    @foreach($widgetKeys as $category => $keys)
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-3"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h3 class="font-jakarta text-xs font-bold uppercase tracking-wider"
                    style="color: var(--dash-text-muted);">
                    {{ $categoryLabel[$category] ?? ucfirst($category) }}
                </h3>
            </div>
            <div>
                @foreach($keys as $wk)
                    @php $assigned = $byWidget->get($wk->value, collect()); @endphp
                    <div class="flex items-start gap-4 px-5 py-4"
                         style="border-bottom: 1px solid var(--dash-card-border);"
                         onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                         onmouseout="this.style.backgroundColor=''">
                        <div class="flex min-w-0 flex-1 items-start gap-3">
                            <span class="material-symbols-outlined mt-0.5 text-[18px]"
                                  style="color: var(--dash-text-faint);">{{ $wk->icon() }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                                    {{ $wk->label() }}
                                </p>
                                <p class="font-mono text-xs" style="color: var(--dash-text-faint);">
                                    {{ $wk->value }}
                                </p>
                                @if($assigned->isEmpty())
                                    <p class="mt-1.5 text-xs italic" style="color: var(--dash-text-faint);">
                                        Belum di-assign ke posisi manapun
                                    </p>
                                @else
                                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                                        @foreach($assigned->sortBy('order') as $cfg)
                                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                                  @if($cfg->is_enabled)
                                                      style="background-color: var(--dash-primary-bg); color: var(--dash-primary);"
                                                  @else
                                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); text-decoration: line-through; border: 1px solid var(--dash-card-border);"
                                                  @endif>
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
                                class="inline-flex shrink-0 items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-primary-bg)'; this.style.color='var(--dash-primary)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-body)'">
                            <span class="material-symbols-outlined text-[14px]">tune</span>
                            Kelola
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    {{-- Modal kelola posisi per widget --}}
    <div x-show="activeModal !== null"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none;">
        <div class="absolute inset-0 bg-black/50"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="closeModal()"></div>
        <div class="relative rounded-2xl shadow-xl w-full max-w-lg overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             @click.stop>

            <div class="flex items-center justify-between px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);"
                        x-text="modalLabel"></h3>
                    <p class="text-xs" style="color: var(--dash-text-muted);">
                        Pilih posisi yang dapat melihat widget ini
                    </p>
                </div>
                <button @click="closeModal()"
                        class="flex items-center justify-center w-8 h-8 rounded-lg hover:opacity-75 transition-opacity"
                        style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" :action="'/system/dashboard-widgets/' + modalWidget + '/positions'" x-ref="assignForm">
                @csrf
                @method('PUT')

                <div class="max-h-[50vh] overflow-y-auto px-6 py-4 space-y-3">
                    <template x-if="modalPositions.length === 0">
                        <p class="py-4 text-center text-sm italic" style="color: var(--dash-text-faint);">
                            Belum ada posisi. Klik tombol di bawah untuk menambah.
                        </p>
                    </template>

                    <template x-for="(row, index) in modalPositions" :key="index">
                        <div class="flex items-center gap-2">
                            <select :name="'positions[' + index + '][position_id]'"
                                    x-model="row.position_id"
                                    required
                                    data-no-enhance
                                    class="flex-1">
                                <option value="">Pilih Posisi</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                                @endforeach
                            </select>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-xs" style="color: var(--dash-text-muted);">#</span>
                                <input type="number" :name="'positions[' + index + '][order]'"
                                       x-model="row.order"
                                       min="1" required
                                       class="w-14 text-center">
                            </div>
                            <div class="flex items-center shrink-0">
                                <input type="hidden" :name="'positions[' + index + '][is_enabled]'" value="0">
                                <input type="checkbox" :name="'positions[' + index + '][is_enabled]'" value="1"
                                       :checked="row.is_enabled"
                                       @change="row.is_enabled = $event.target.checked"
                                       title="Aktifkan"
                                       style="accent-color: var(--dash-primary);"
                                       class="h-4 w-4 rounded">
                            </div>
                            <button type="button" @click="removePosition(index)"
                                    class="flex-shrink-0 hover:opacity-75 transition-opacity"
                                    style="color: #dc2626;">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="px-6 py-3" style="border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="addPosition()"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold hover:opacity-75 transition-opacity"
                            style="color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Tambah posisi
                    </button>
                </div>

                <div class="flex justify-end gap-3 px-6 py-4"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="button" @click="closeModal()"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                            style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Simpan
                    </button>
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
