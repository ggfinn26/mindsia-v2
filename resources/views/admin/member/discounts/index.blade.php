@extends('layouts.dashboard')

@section('title', 'Kelola Diskon Member')
@section('header_title', 'Kelola Diskon Member')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Kelola Diskon Member</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Diskon kode promo untuk pendaftaran member</p>
        </div>
        @can('member.manage')
        <button onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Buat Diskon Baru
        </button>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kode Diskon</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tipe & Nilai</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kuota</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Periode Valid</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($discounts as $discount)
                <tr style="border-bottom: 1px solid var(--dash-card-border);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <td class="px-5 py-3.5">
                        <span class="font-mono font-semibold" style="color: var(--dash-text-heading);">{{ $discount->discount_code }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        @if ($discount->discount_type === 'percentage')
                            <span class="inline-flex items-center gap-1 text-purple-700 bg-purple-50 px-2 py-0.5 rounded text-xs font-medium">% Persentase</span>
                            <span class="ml-1 font-semibold" style="color: var(--dash-text-heading);">{{ $discount->discount_nominal }}%</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-blue-700 bg-blue-50 px-2 py-0.5 rounded text-xs font-medium">Rp Fixed</span>
                            <span class="ml-1 font-semibold" style="color: var(--dash-text-heading);">Rp {{ number_format($discount->discount_nominal, 0, ',', '.') }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        @if ($discount->discount_quota)
                            <span style="color: {{ $discount->registrations_count >= $discount->discount_quota ? '#dc2626' : 'var(--dash-text-body)' }}; font-weight: {{ $discount->registrations_count >= $discount->discount_quota ? '600' : '400' }}">
                                {{ $discount->registrations_count }} / {{ $discount->discount_quota }}
                            </span>
                        @else
                            <span class="text-xs" style="color: var(--dash-text-faint);">Tidak terbatas</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-xs" style="color: var(--dash-text-body);">
                        @if ($discount->starts_at || $discount->expired_at)
                            {{ $discount->starts_at?->format('d/m/Y') ?? '—' }}
                            <span style="color: var(--dash-text-faint);" class="mx-1">s/d</span>
                            {{ $discount->expired_at?->format('d/m/Y') ?? '—' }}
                        @else
                            <span style="color: var(--dash-text-faint);">Tidak ada batas</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        @if ($discount->is_active)
                            <span class="inline-flex items-center gap-1 text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="color: var(--dash-text-muted); background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--dash-text-muted);"></span> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('discounts.show', $discount) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>Lihat
                            </a>
                            @can('member.manage')
                            <form action="{{ route('discounts.toggle-active', $discount) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: {{ $discount->is_active ? '#ea580c' : '#16a34a' }};"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">{{ $discount->is_active ? 'pause_circle' : 'play_circle' }}</span>
                                    {{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form id="delete-form-{{ $discount->id }}" action="{{ route('discounts.destroy', $discount) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="openDeleteModal('{{ $discount->discount_code }}', 'delete-form-{{ $discount->id }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #dc2626;">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-sm" style="color: var(--dash-text-faint);">
                        Belum ada diskon yang dibuat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@can('member.manage')
<!-- Create Discount Modal -->
<div id="create-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="rounded-2xl shadow-xl max-w-md w-full mx-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="text-base font-semibold" style="color: var(--dash-text-heading);">Buat Diskon Baru</h3>
        </div>
        <form id="create-form" action="{{ route('discounts.store') }}" method="POST" class="px-6 py-5 space-y-4" novalidate>
            @csrf
            <div id="create-errors" class="hidden rounded-lg px-3 py-2 text-sm"
                 style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;"></div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Kode Diskon <span class="text-red-500">*</span></label>
                <input type="text" name="discount_code" id="create-code"
                       class="w-full px-3 py-2 rounded-lg text-sm font-mono uppercase focus:outline-none focus:ring-2 transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading); focus-border-color: var(--dash-primary);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                       placeholder="PROMO2026" />
                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Huruf kapital otomatis. Contoh: LEBARAN25, BACK2SCHOOL</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Tipe Diskon <span class="text-red-500">*</span></label>
                    <select name="discount_type" id="create-type" onchange="onTypeChange(this.value)"
                            class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed_amount">Fixed (Rp)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Nilai <span id="type-unit" style="color: var(--dash-text-faint);" class="font-normal">(%)</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="discount_nominal" id="create-nominal" min="0"
                           class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           placeholder="0" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Kuota Slot</label>
                <input type="number" name="discount_quota" id="create-quota" min="0"
                       class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                       placeholder="Kosongkan = tidak terbatas" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Berlaku Mulai</label>
                    <input type="date" name="starts_at"
                           class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Berlaku Sampai</label>
                    <input type="date" name="expired_at"
                           class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeCreateModal()"
                        class="flex-1 px-4 py-2 text-sm rounded-lg transition-colors inline-flex items-center justify-center gap-1.5"
                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                <button type="submit" id="create-submit"
                        class="flex-1 px-4 py-2 text-sm text-white rounded-lg hover:opacity-90 font-semibold transition-opacity disabled:opacity-60 inline-flex items-center justify-center gap-1.5"
                        style="background-color: var(--dash-primary);"><span class="material-symbols-outlined text-[16px]">save</span>Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('create-form').reset();
    document.getElementById('create-errors').classList.add('hidden');
    document.getElementById('create-errors').innerHTML = '';
    document.getElementById('type-unit').textContent = '(%)';
    document.getElementById('create-modal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('create-modal').classList.add('hidden');
}

function onTypeChange(val) {
    document.getElementById('type-unit').textContent = val === 'percentage' ? '(%)' : '(Rp)';
}

document.getElementById('create-code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

document.getElementById('create-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('create-submit');
    const errEl = document.getElementById('create-errors');
    btn.disabled = true;
    errEl.classList.add('hidden');

    try {
        const res = await fetch(this.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(this),
        });

        if (res.status === 422) {
            const data = await res.json();
            const msgs = Object.values(data.errors ?? {}).flat();
            errEl.innerHTML = '<ul class="list-disc list-inside space-y-1">' + msgs.map(m => `<li>${m}</li>`).join('') + '</ul>';
            errEl.classList.remove('hidden');
            return;
        }

        window.location.reload();
    } catch {
        errEl.innerHTML = '<p>Terjadi kesalahan koneksi. Coba lagi.</p>';
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
    }
});

document.getElementById('create-modal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeCreateModal(); closeDeleteModal(); }
});
</script>

<!-- Delete Confirm Modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="rounded-2xl shadow-xl max-w-sm w-full mx-4 p-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-red-600 text-[20px]">delete</span>
            </div>
            <div>
                <h3 class="font-semibold text-base" style="color: var(--dash-text-heading);">Hapus Diskon</h3>
                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                    Hapus <span id="delete-code" class="font-mono font-semibold" style="color: var(--dash-text-heading);"></span>?
                    Diskon yang sudah dipakai dalam registrasi tidak bisa dihapus.
                </p>
            </div>
        </div>
        <div class="flex gap-3 mt-5 justify-end">
            <button onclick="closeDeleteModal()"
                    class="px-4 py-2 text-sm rounded-lg transition-colors inline-flex items-center gap-1.5"
                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
            <button id="delete-confirm-btn" onclick="submitDeleteForm()"
                    class="px-4 py-2 text-sm bg-[#dc2626] text-white rounded-lg hover:opacity-90 font-semibold inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">delete</span>Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
let _deleteFormId = null;

function openDeleteModal(code, formId) {
    _deleteFormId = formId;
    document.getElementById('delete-code').textContent = code;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    _deleteFormId = null;
}

function submitDeleteForm() {
    if (_deleteFormId) document.getElementById(_deleteFormId).submit();
}

document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endcan
@endsection
