@extends('layouts.dashboard')

@section('title', 'Detail Registrasi - MINDSIA')
@section('header_title', 'Detail Registrasi')

@section('content')
@php
    use App\Enums\Member\ActivationStatus;
    $member = $registration->memberData;
    $isActive = $member->account?->is_active;
    $activationStatus = $member->activation_status;
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'POST', confirmActionText: 'Konfirmasi' }">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('members.show', $member) }}" class="dash-back mb-3 inline-flex items-center gap-1.5 text-xs font-semibold">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Profil {{ $member->full_name }}
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
                <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Detail Registrasi Program</h1>
                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Informasi program keikutsertaan, rincian tagihan, dan jadwal cicilan.</p>
            </div>
        </div>
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
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- Identitas Member Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b flex items-center justify-between flex-wrap gap-3"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Identitas Member</h2>
            <div class="flex items-center gap-2 flex-wrap">
                @if($activationStatus === ActivationStatus::Ditangguhkan)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-2.5 py-0.5 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">block</span> Ditangguhkan
                    </span>
                @elseif($isActive && $activationStatus === ActivationStatus::Aktif)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Akun Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-100 px-2.5 py-0.5 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">pending</span> Menunggu Pembayaran
                    </span>
                @endif

                @can('member.payment.manage')
                    @if($activationStatus === ActivationStatus::Aktif)
                        <button type="button"
                                @click="showConfirmModal = true; confirmTitle = 'Tangguhkan Akun'; confirmMessage = 'Yakin ingin menangguhkan akun member {{ addslashes($member->full_name) }}? Akses login portal akan dinonaktifkan.'; confirmActionUrl = '{{ route('members.suspend', $member) }}'; confirmMethod = 'POST'; confirmActionText = 'Tangguhkan'"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #dc2626;">
                            <span class="material-symbols-outlined text-[14px]">block</span> Tangguhkan
                        </button>
                    @elseif($activationStatus === ActivationStatus::Ditangguhkan)
                        <button type="button"
                                @click="showConfirmModal = true; confirmTitle = 'Aktifkan Akun'; confirmMessage = 'Yakin ingin mengaktifkan kembali akun member {{ addslashes($member->full_name) }}?'; confirmActionUrl = '{{ route('members.activate', $member) }}'; confirmMethod = 'POST'; confirmActionText = 'Aktifkan'"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span> Aktifkan
                        </button>
                    @endif
                @endcan
            </div>
        </div>
        <dl class="divide-y" style="border-color: var(--dash-card-border);">
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama Lengkap</dt>
                <dd class="sm:col-span-2 text-sm font-medium" style="color: var(--dash-text-heading);">{{ $member->full_name }}</dd>
            </div>
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Email</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->account?->email ?? $member->email ?? '-' }}</dd>
            </div>
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">WhatsApp</dt>
                <dd class="sm:col-span-2 text-sm font-mono" style="color: var(--dash-text-body);">{{ $member->whatsapp_number ?? '-' }}</dd>
            </div>
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Cabang</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->branch?->branch_name ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Program & Harga Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Program & Rincian Pembayaran</h2>
        </div>
        <dl class="divide-y" style="border-color: var(--dash-card-border);">
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Program</dt>
                <dd class="sm:col-span-2 text-sm font-bold" style="color: var(--dash-text-heading);">{{ $registration->receipt_program_name }}</dd>
            </div>
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Skema Bayar</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $registration->receipt_payment_plan_label ?? '-' }}</dd>
            </div>
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Harga Normal</dt>
                <dd class="sm:col-span-2 text-sm font-mono" style="color: var(--dash-text-body);">Rp {{ number_format($registration->original_price, 0, ',', '.') }}</dd>
            </div>
            @if($registration->discount_amount > 0)
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Diskon</dt>
                <dd class="sm:col-span-2 text-sm font-mono text-emerald-700">
                    - Rp {{ number_format($registration->discount_amount, 0, ',', '.') }}
                    @if($registration->discount_code)
                        <span class="ml-1 text-xs" style="color: var(--dash-text-muted);">({{ $registration->discount_code }})</span>
                    @endif
                </dd>
            </div>
            @endif
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Tagihan</dt>
                <dd class="sm:col-span-2 text-base font-bold font-mono" style="color: var(--dash-text-heading);">Rp {{ number_format($registration->final_price, 0, ',', '.') }}</dd>
            </div>
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status Bayar</dt>
                <dd class="sm:col-span-2">
                    @php
                        $statusMap = [
                            'unpaid'       => ['label' => 'Belum Bayar',     'class' => 'bg-red-100 text-red-700'],
                            'installments' => ['label' => 'Cicilan Berjalan','class' => 'bg-blue-100 text-blue-700'],
                            'paid_full'    => ['label' => 'Lunas',            'class' => 'bg-emerald-100 text-emerald-700'],
                            'paid'         => ['label' => 'Lunas',            'class' => 'bg-emerald-100 text-emerald-700'],
                        ];
                        $st = $statusMap[$registration->payment_status] ?? ['label' => $registration->payment_status, 'class' => 'bg-slate-100 text-slate-700'];
                    @endphp
                    <span class="inline-flex text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $st['class'] }}">{{ $st['label'] }}</span>
                </dd>
            </div>
            @if($registration->employee)
            <div class="px-6 py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Didaftarkan Oleh</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $registration->employee->full_name }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Cicilan Pembayaran Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Cicilan Pembayaran</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Aktivasi akun portal member otomatis aktif setelah cicilan pertama dikonfirmasi.</p>
        </div>

        <div class="divide-y" style="border-color: var(--dash-card-border);">
            @forelse($registration->payments->sortBy('installment_number') as $payment)
            <div class="px-6 py-4 flex items-start gap-4 flex-wrap sm:flex-nowrap"
                 style="background-color: transparent;"
                 onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                 onmouseout="this.style.backgroundColor='transparent'">
                {{-- Status icon --}}
                <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5"
                     style="background-color: {{ $payment->payment_status === 'paid' ? '#dcfce7' : 'var(--dash-nav-hover-bg)' }};">
                    <span class="material-symbols-outlined text-[18px]"
                          style="color: {{ $payment->payment_status === 'paid' ? '#059669' : 'var(--dash-text-muted)' }};">
                        {{ $payment->payment_status === 'paid' ? 'check_circle' : 'radio_button_unchecked' }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $payment->installment_number === 1 ? 'DP / Cicilan 1' : 'Cicilan ' . $payment->installment_number }}
                        <span class="ml-2 font-bold font-mono" style="color: var(--dash-primary-text);">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </p>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap" x-data="{ editDue: false }">
                        <p class="text-xs" style="color: var(--dash-text-muted);">
                            Jatuh tempo: <span x-show="!editDue">{{ $payment->due_date?->format('d M Y') ?? '-' }}</span>
                        </p>
                        @can('member.payment.manage')
                        <button type="button" x-show="!editDue" @click="editDue = true"
                                class="text-[11px] font-semibold hover:underline" style="color: var(--dash-primary-text);">Ubah</button>
                        <form x-show="editDue" x-cloak
                              action="{{ route('member-payments.update', $payment) }}" method="POST"
                              class="flex items-center gap-1.5">
                            @csrf @method('PATCH')
                            <input type="date" name="due_date"
                                   value="{{ $payment->due_date?->format('Y-m-d') }}"
                                   class="text-xs rounded-lg px-2 py-1 focus:outline-none"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                            <button type="submit"
                                    class="text-[11px] font-semibold text-white px-2.5 py-1 rounded-lg hover:opacity-90"
                                    style="background-color: var(--dash-primary);">Simpan</button>
                            <button type="button" @click="editDue = false"
                                    class="text-[11px] font-medium" style="color: var(--dash-text-muted);">Batal</button>
                        </form>
                        @endcan
                        @if($payment->paid_at)
                            <p class="text-xs" style="color: var(--dash-text-muted);">· Dibayar: {{ $payment->paid_at->format('d M Y') }}</p>
                        @endif
                    </div>
                    @if($payment->notes)
                        <p class="text-xs mt-1 italic" style="color: var(--dash-text-muted);">{{ $payment->notes }}</p>
                    @endif
                </div>

                {{-- Action --}}
                <div class="shrink-0">
                    @if($payment->payment_status === 'paid')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">
                            <span class="material-symbols-outlined text-[13px]">check</span> Terkonfirmasi
                        </span>
                    @else
                        @can('member.payment.manage')
                        <div x-data="{
                                open: false,
                                method: 'transfer_bank',
                                methods: [
                                    { value: 'transfer_bank', label: 'Transfer Bank' },
                                    { value: 'cash',          label: 'Cash / Tunai' },
                                    { value: 'qris',          label: 'QRIS' },
                                    { value: 'e_wallet',      label: 'E-Wallet (GoPay/OVO/Dana)' },
                                    { value: 'lainnya',       label: 'Lainnya' },
                                ]
                            }">
                            <button type="button" @click="open = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[14px]">payments</span>
                                Konfirmasi Bayar
                            </button>

                            {{-- Modal konfirmasi pembayaran kustom (NO overflow-hidden on card) --}}
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                                <div class="w-full max-w-sm rounded-2xl shadow-2xl text-left"
                                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                                     @click.stop>
                                    <div class="px-6 py-4 rounded-t-2xl border-b"
                                         style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Konfirmasi Pembayaran</h3>
                                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                            {{ $payment->installment_number === 1 ? 'DP / Cicilan 1' : 'Cicilan ' . $payment->installment_number }}
                                            - <strong class="font-mono">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                        </p>
                                    </div>
                                    <form action="{{ route('member-payments.update', $payment) }}" method="POST" class="p-6 space-y-4">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="paid">

                                        <div>
                                            <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Metode Pembayaran</label>
                                            <select name="payment_method" x-model="method"
                                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                                <template x-for="m in methods" :key="m.value">
                                                    <option :value="m.value" x-text="m.label"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Catatan Pembayaran</label>
                                            <textarea name="notes" rows="2" placeholder="Catatan transaksi (opsional)..."
                                                      class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                                      style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                      onfocus="this.style.borderColor='var(--dash-primary)'"
                                                      onblur="this.style.borderColor='var(--dash-input-border)'"></textarea>
                                        </div>

                                        <div class="flex gap-2 justify-end pt-2">
                                            <button type="button" @click="open = false"
                                                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors"
                                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                    style="background-color: var(--dash-primary);">
                                                <span class="material-symbols-outlined text-[14px]">check</span>
                                                Konfirmasi
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endcan
                    @endif
                </div>
            </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">receipt_long</span>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data cicilan pembayaran.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Konfirmasi Aksi (ZERO BROWSER CONFIRM) --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showConfirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: #fee2e2; color: #dc2626;">
                            <span class="material-symbols-outlined text-[24px]">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showConfirmModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.actionConfirmForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            :style="confirmActionText === 'Tangguhkan' ? 'background-color: #dc2626;' : 'background-color: var(--dash-primary);'"
                            x-text="confirmActionText">
                    </button>
                </div>
                <form x-ref="actionConfirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
