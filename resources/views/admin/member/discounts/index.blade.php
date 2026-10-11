@extends('layouts.dashboard')

@section('title', 'Kelola Diskon Member')
@section('header_title', 'Kelola Diskon Member')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Diskon Member</h1>
            <p class="text-sm text-gray-500 mt-1">Diskon kode promo untuk pendaftaran member</p>
        </div>
        @can('member.manage')
        <button onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#5586DB] text-white text-sm font-medium rounded-xl hover:bg-[#4475ca] transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Buat Diskon Baru
        </button>
        @endcan
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode Diskon</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe & Nilai</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kuota</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Periode Valid</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($discounts as $discount)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <span class="font-mono font-semibold text-gray-900">{{ $discount->discount_code }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-700">
                        @if ($discount->discount_type === 'percentage')
                            <span class="inline-flex items-center gap-1 text-purple-700 bg-purple-50 px-2 py-0.5 rounded text-xs font-medium">
                                % Persentase
                            </span>
                            <span class="ml-1 font-semibold">{{ $discount->discount_nominal }}%</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-blue-700 bg-blue-50 px-2 py-0.5 rounded text-xs font-medium">
                                Rp Fixed
                            </span>
                            <span class="ml-1 font-semibold">Rp {{ number_format($discount->discount_nominal, 0, ',', '.') }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        @if ($discount->discount_quota)
                            <span class="{{ $discount->registrations_count >= $discount->discount_quota ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                {{ $discount->registrations_count }} / {{ $discount->discount_quota }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">Tidak terbatas</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600 text-xs">
                        @if ($discount->starts_at || $discount->expired_at)
                            {{ $discount->starts_at?->format('d/m/Y') ?? '—' }}
                            <span class="text-gray-400 mx-1">s/d</span>
                            {{ $discount->expired_at?->format('d/m/Y') ?? '—' }}
                        @else
                            <span class="text-gray-400">Tidak ada batas</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if ($discount->is_active)
                            <span class="inline-flex items-center gap-1 text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-gray-500 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded-full text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('discounts.show', $discount) }}"
                               class="text-xs text-[#5586DB] hover:underline font-medium">Detail</a>
                            @can('member.manage')
                            <form action="{{ route('discounts.toggle-active', $discount) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="text-xs {{ $discount->is_active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' }} font-medium">
                                    {{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form id="delete-form-{{ $discount->id }}" action="{{ route('discounts.destroy', $discount) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="openDeleteModal('{{ $discount->discount_code }}', 'delete-form-{{ $discount->id }}')"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium">Hapus</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">
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
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Buat Diskon Baru</h3>
        </div>
        <form id="create-form" action="{{ route('discounts.store') }}" method="POST" class="px-6 py-5 space-y-4" novalidate>
            @csrf
            <div id="create-errors" class="hidden bg-red-50 border border-red-300 text-red-700 rounded-lg px-3 py-2 text-sm"></div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Diskon <span class="text-red-500">*</span></label>
                <input type="text" name="discount_code" id="create-code"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono uppercase focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20"
                       placeholder="PROMO2026" />
                <p class="text-xs text-gray-400 mt-1">Huruf kapital otomatis. Contoh: LEBARAN25, BACK2SCHOOL</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Diskon <span class="text-red-500">*</span></label>
                    <select name="discount_type" id="create-type" onchange="onTypeChange(this.value)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20">
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed_amount">Fixed (Rp)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nilai <span id="type-unit" class="text-gray-400 font-normal">(%)</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="discount_nominal" id="create-nominal" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20"
                           placeholder="0" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Slot</label>
                <input type="number" name="discount_quota" id="create-quota" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20"
                       placeholder="Kosongkan = tidak terbatas" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Mulai</label>
                    <input type="date" name="starts_at"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai</label>
                    <input type="date" name="expired_at"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20" />
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeCreateModal()"
                        class="flex-1 px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" id="create-submit"
                        class="flex-1 px-4 py-2 text-sm bg-[#5586DB] text-white rounded-lg hover:bg-[#4475ca] font-medium disabled:opacity-60">Simpan</button>
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
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full mx-4 p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-red-600 text-[20px]">delete</span>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 text-base">Hapus Diskon</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Hapus <span id="delete-code" class="font-mono font-semibold text-gray-800"></span>?
                    Diskon yang sudah dipakai dalam registrasi tidak bisa dihapus.
                </p>
            </div>
        </div>
        <div class="flex gap-3 mt-5 justify-end">
            <button onclick="closeDeleteModal()"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
            <button id="delete-confirm-btn" onclick="submitDeleteForm()"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Ya, Hapus</button>
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
