@extends('layouts.dashboard')

@section('title', 'Kelola Diskon Member - MINDSIA')
@section('header_title', 'Kelola Diskon Member')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
         createModalOpen: false,
         deleteModalOpen: false,
         deleteCode: '',
         deleteActionUrl: '',
         openDelete(code, url) {
             this.deleteCode = code;
             this.deleteActionUrl = url;
             this.deleteModalOpen = true;
         }
     }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Kelola Diskon Member</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Kelola kode promo dan kuota potongan harga pendaftaran anggota baru.</p>
        </div>
        @can('member.manage')
        <div class="flex items-center gap-3">
            <button type="button" @click="createModalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Diskon Baru
            </button>
        </div>
        @endcan
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Data Table (Zero horizontal scroll, compact combined columns) --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm text-left">
            <thead>
                <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Kode & Status</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Tipe & Nilai</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Kuota & Periode</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($discounts as $discount)
                <tr style="border-bottom: 1px solid var(--dash-card-border);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    {{-- Kode & Status Col --}}
                    <td class="px-5 py-4">
                        <span class="font-mono font-bold text-sm tracking-wide block" style="color: var(--dash-text-heading);">{{ $discount->discount_code }}</span>
                        <div class="mt-1">
                            @if ($discount->is_active)
                                <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full text-[11px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full text-[11px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Tipe & Nilai Col --}}
                    <td class="px-5 py-4">
                        @if ($discount->discount_type === 'percentage')
                            <span class="inline-flex items-center gap-1 text-purple-700 bg-purple-50 px-2 py-0.5 rounded text-xs font-semibold">
                                % Persentase
                            </span>
                            <span class="ml-1.5 font-bold font-mono text-sm" style="color: var(--dash-text-heading);">{{ $discount->discount_nominal }}%</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-blue-700 bg-blue-50 px-2 py-0.5 rounded text-xs font-semibold">
                                Rp Nominal
                            </span>
                            <span class="ml-1.5 font-bold font-mono text-sm" style="color: var(--dash-text-heading);">Rp {{ number_format($discount->discount_nominal, 0, ',', '.') }}</span>
                        @endif
                    </td>

                    {{-- Kuota & Periode Col --}}
                    <td class="px-5 py-4">
                        <p class="text-xs font-medium" style="color: var(--dash-text-heading);">
                            @if ($discount->discount_quota)
                                <span class="{{ $discount->registrations_count >= $discount->discount_quota ? 'text-red-600 font-bold' : '' }}">
                                    {{ $discount->registrations_count }} / {{ $discount->discount_quota }} slot terpakai
                                </span>
                            @else
                                <span style="color: var(--dash-text-muted);">Kuota tidak terbatas</span>
                            @endif
                        </p>
                        <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                            @if ($discount->starts_at || $discount->expired_at)
                                {{ $discount->starts_at?->format('d/m/Y') ?? '-' }} s/d {{ $discount->expired_at?->format('d/m/Y') ?? '-' }}
                            @else
                                Tidak ada batas waktu
                            @endif
                        </p>
                    </td>

                    {{-- Aksi Col --}}
                    <td class="px-5 py-4 text-right">
                        <div class="inline-flex items-center gap-1.5 justify-end">
                            <a href="{{ route('discounts.show', $discount) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                Detail
                            </a>
                            @can('member.manage')
                            <form action="{{ route('discounts.toggle-active', $discount) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">{{ $discount->is_active ? 'toggle_off' : 'toggle_on' }}</span>
                                    {{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <button type="button"
                                    @click="openDelete('{{ $discount->discount_code }}', '{{ route('discounts.destroy', $discount) }}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: #dc2626;">
                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                Hapus
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center">
                        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                              style="color: var(--dash-text-faint);">percent</span>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada diskon yang dibuat.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('member.manage')
    {{-- Modal Buat Diskon Baru (Custom Alpine, NO overflow-hidden on card) --}}
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="createModalOpen = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 py-4 rounded-t-2xl border-b"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Buat Diskon Baru</h3>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Tambahkan kode promo potongan harga member</p>
                </div>
                <form id="create-discount-form" action="{{ route('discounts.store') }}" method="POST" class="p-6 space-y-4" novalidate>
                    @csrf
                    <div id="create-errors" class="hidden rounded-xl p-3 text-xs font-medium"
                         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;"></div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                            Kode Diskon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="discount_code" id="create-code" required
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all uppercase font-mono"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                               placeholder="PROMO2026" />
                        <p class="text-[11px] mt-1" style="color: var(--dash-text-faint);">Format huruf kapital. Contoh: LEBARAN25, BACK2SCHOOL</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                                Tipe Diskon <span class="text-red-500">*</span>
                            </label>
                            <select name="discount_type" id="create-type" onchange="onDiscountTypeChange(this.value)"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed_amount">Fixed (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                                Nilai <span id="type-unit" style="color: var(--dash-text-faint);">(%)</span> <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="discount_nominal" id="create-nominal" min="0" required
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all font-mono"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                                   placeholder="0" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Kuota Slot</label>
                        <input type="number" name="discount_quota" id="create-quota" min="0"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all font-mono"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                               placeholder="Kosongkan jika tidak terbatas" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Berlaku Mulai</label>
                            <input type="date" name="starts_at"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Berlaku Sampai</label>
                            <input type="date" name="expired_at"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        </div>
                    </div>

                    <div class="flex gap-2 justify-end pt-3 border-t" style="border-color: var(--dash-card-border);">
                        <button type="button" @click="createModalOpen = false"
                                class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <button type="submit" id="create-submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan Diskon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus (Custom Alpine) --}}
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="deleteModalOpen = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: #fee2e2; color: #dc2626;">
                            <span class="material-symbols-outlined text-[24px]">delete</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Hapus Diskon</h3>
                            <div class="mt-2 text-sm" style="color: var(--dash-text-muted);">
                                Yakin ingin menghapus diskon <span class="font-mono font-bold" style="color: var(--dash-text-heading);" x-text="deleteCode"></span>?
                                Diskon yang sudah dipakai dalam registrasi tidak dapat dihapus.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="deleteModalOpen = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.discountDeleteForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: #dc2626;">
                        Hapus Diskon
                    </button>
                </div>
                <form x-ref="discountDeleteForm" :action="deleteActionUrl" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <script>
        function onDiscountTypeChange(val) {
            const unit = document.getElementById('type-unit');
            if (unit) unit.textContent = val === 'percentage' ? '(%)' : '(Rp)';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const codeInput = document.getElementById('create-code');
            if (codeInput) {
                codeInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }

            const form = document.getElementById('create-discount-form');
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('create-submit');
                    const errEl = document.getElementById('create-errors');
                    if (btn) btn.disabled = true;
                    if (errEl) errEl.classList.add('hidden');

                    try {
                        const res = await fetch(this.action, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new FormData(this),
                        });

                        if (res.status === 422) {
                            const data = await res.json();
                            const msgs = Object.values(data.errors ?? {}).flat();
                            if (errEl) {
                                errEl.innerHTML = '<ul class="list-disc list-inside space-y-1">' + msgs.map(m => `<li>${m}</li>`).join('') + '</ul>';
                                errEl.classList.remove('hidden');
                            }
                            return;
                        }

                        window.location.reload();
                    } catch {
                        if (errEl) {
                            errEl.innerHTML = '<p>Terjadi kesalahan koneksi. Coba lagi.</p>';
                            errEl.classList.remove('hidden');
                        }
                    } finally {
                        if (btn) btn.disabled = false;
                    }
                });
            }
        });
    </script>
    @endcan

</div>
@endsection
