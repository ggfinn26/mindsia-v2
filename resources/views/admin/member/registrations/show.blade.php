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

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('members.show', $member) }}"
               class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider mb-2 hover:opacity-70 transition-opacity"
               style="color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Member & Marketing
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">
                Detail Registrasi
            </h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">{{ $member->full_name }}</p>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
            @if($activationStatus === ActivationStatus::Ditangguhkan)
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-2.5 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">block</span> Ditangguhkan
                </span>
            @elseif($isActive && $activationStatus === ActivationStatus::Aktif)
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">check_circle</span> Akun Aktif
                </span>
            @else
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-100 px-2.5 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">pending</span> Menunggu Pembayaran
                </span>
            @endif

            @can('member.payment.manage')
                @if($activationStatus === ActivationStatus::Aktif)
                    <form method="POST" action="{{ route('members.suspend', $member) }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Tangguhkan akun member ini?')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #dc2626;">
                            <span class="material-symbols-outlined text-[14px]">block</span> Tangguhkan
                        </button>
                    </form>
                @elseif($activationStatus === ActivationStatus::Ditangguhkan)
                    <form method="POST" action="{{ route('members.activate', $member) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #059669;">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span> Aktifkan
                        </button>
                    </form>
                @endif
            @endcan
        </div>
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
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Identitas Member ── --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-semibold" style="color: var(--dash-text-heading);">Identitas Member</h3>
        </div>
        <dl>
            @foreach([
                'Nama'      => $member->full_name,
                'Email'     => $member->account?->email ?? $member->email ?? '—',
                'WhatsApp'  => $member->whatsapp_number ?? '—',
                'Cabang'    => $member->branch?->branch_name ?? '—',
            ] as $label => $value)
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <dt class="font-medium" style="color: var(--dash-text-muted);">{{ $label }}</dt>
                <dd class="col-span-2" style="color: var(--dash-text-heading);">{{ $value }}</dd>
            </div>
            @endforeach
        </dl>
    </div>

    {{-- ── Program & Harga ── --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-semibold" style="color: var(--dash-text-heading);">Program & Pembayaran</h3>
        </div>
        <dl>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm" style="border-bottom: 1px solid var(--dash-card-border);">
                <dt class="font-medium" style="color: var(--dash-text-muted);">Program</dt>
                <dd class="col-span-2 font-semibold" style="color: var(--dash-text-heading);">{{ $registration->receipt_program_name }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm" style="border-bottom: 1px solid var(--dash-card-border);">
                <dt class="font-medium" style="color: var(--dash-text-muted);">Skema Bayar</dt>
                <dd class="col-span-2" style="color: var(--dash-text-heading);">{{ $registration->receipt_payment_plan_label ?? '—' }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm" style="border-bottom: 1px solid var(--dash-card-border);">
                <dt class="font-medium" style="color: var(--dash-text-muted);">Harga Normal</dt>
                <dd class="col-span-2" style="color: var(--dash-text-heading);">Rp {{ number_format($registration->original_price, 0, ',', '.') }}</dd>
            </div>
            @if($registration->discount_amount > 0)
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm" style="border-bottom: 1px solid var(--dash-card-border);">
                <dt class="font-medium" style="color: var(--dash-text-muted);">Diskon</dt>
                <dd class="col-span-2 text-green-600 font-medium">
                    − Rp {{ number_format($registration->discount_amount, 0, ',', '.') }}
                    @if($registration->discount_code)
                        <span class="ml-1 text-xs" style="color: var(--dash-text-faint);">({{ $registration->discount_code }})</span>
                    @endif
                </dd>
            </div>
            @endif
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm" style="border-bottom: 1px solid var(--dash-card-border);">
                <dt class="font-medium" style="color: var(--dash-text-muted);">Total Tagihan</dt>
                <dd class="col-span-2 font-bold text-base" style="color: var(--dash-text-heading);">Rp {{ number_format($registration->final_price, 0, ',', '.') }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm" style="border-bottom: 1px solid var(--dash-card-border);">
                <dt class="font-medium" style="color: var(--dash-text-muted);">Status Bayar</dt>
                <dd class="col-span-2">
                    @php
                        $statusMap = [
                            'unpaid'       => ['label' => 'Belum Bayar',      'class' => 'bg-red-100 text-red-700'],
                            'installments' => ['label' => 'Cicilan Berjalan', 'class' => 'bg-blue-100 text-blue-700'],
                            'paid_full'    => ['label' => 'Lunas',             'class' => 'bg-green-100 text-green-700'],
                            'paid'         => ['label' => 'Lunas',             'class' => 'bg-green-100 text-green-700'],
                        ];
                        $st = $statusMap[$registration->payment_status] ?? ['label' => $registration->payment_status, 'class' => 'bg-gray-100 text-gray-700'];
                    @endphp
                    <span class="inline-flex text-xs font-semibold px-2.5 py-1 rounded-full {{ $st['class'] }}">{{ $st['label'] }}</span>
                </dd>
            </div>
            @if($registration->employee)
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium" style="color: var(--dash-text-muted);">Dibuat oleh</dt>
                <dd class="col-span-2" style="color: var(--dash-text-heading);">{{ $registration->employee->full_name }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- ── Cicilan ── --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-semibold" style="color: var(--dash-text-heading);">Cicilan Pembayaran</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Aktivasi akun otomatis saat cicilan pertama dikonfirmasi.</p>
        </div>

        <div>
            @forelse($registration->payments->sortBy('installment_number') as $payment)
            <div class="px-6 py-4 flex items-start gap-4 flex-wrap sm:flex-nowrap"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                {{-- Status icon --}}
                <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5"
                     style="background-color: {{ $payment->payment_status === 'paid' ? 'rgb(220 252 231)' : 'var(--dash-nav-hover-bg)' }};">
                    <span class="material-symbols-outlined text-[16px]"
                          style="color: {{ $payment->payment_status === 'paid' ? '#16a34a' : 'var(--dash-text-faint)' }};">
                        {{ $payment->payment_status === 'paid' ? 'check_circle' : 'radio_button_unchecked' }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $payment->installment_number === 1 ? 'DP / Cicilan 1' : 'Cicilan ' . $payment->installment_number }}
                        <span class="ml-2 font-bold" style="color: var(--dash-primary);">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </p>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap"
                         x-data="{ editDue: false }">
                        <p class="text-xs" style="color: var(--dash-text-faint);">
                            Jatuh tempo: <span x-show="!editDue">{{ $payment->due_date?->format('d M Y') ?? '—' }}</span>
                        </p>
                        @can('member.payment.manage')
                        <button type="button" x-show="!editDue" @click="editDue = true"
                                class="text-[10px] font-medium hover:underline" style="color: var(--dash-primary);">Ubah</button>
                        <form x-show="editDue" x-cloak
                              action="{{ route('member-payments.update', $payment) }}" method="POST"
                              class="flex items-center gap-1.5">
                            @csrf @method('PATCH')
                            <input type="date" name="due_date"
                                   value="{{ $payment->due_date?->format('Y-m-d') }}"
                                   class="text-xs rounded px-2 py-0.5"
                                   style="border: 1px solid var(--dash-input-border); background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'">
                            <button type="submit"
                                    class="text-[10px] font-semibold text-white px-2 py-1 rounded hover:opacity-90"
                                    style="background-color: var(--dash-primary);">Simpan</button>
                            <button type="button" @click="editDue = false"
                                    class="text-[10px] font-medium hover:opacity-70"
                                    style="color: var(--dash-text-muted);">Batal</button>
                        </form>
                        @endcan
                        @if($payment->paid_at)
                            <p class="text-xs" style="color: var(--dash-text-faint);">· Dibayar: {{ $payment->paid_at->format('d M Y') }}</p>
                        @endif
                    </div>
                    @if($payment->notes)
                        <p class="text-xs mt-1 italic" style="color: var(--dash-text-muted);">{{ $payment->notes }}</p>
                    @endif
                </div>

                {{-- Action --}}
                <div class="shrink-0">
                    @if($payment->payment_status === 'paid')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1.5 rounded-full">
                            <span class="material-symbols-outlined text-[13px]">check</span> Terkonfirmasi
                        </span>
                    @else
                        @can('member.payment.manage')
                        <form action="{{ route('member-payments.update', $payment) }}" method="POST"
                              x-data="{
                                  open: false,
                                  methodOpen: false,
                                  method: '',
                                  methods: [
                                      { value: 'transfer_bank', label: 'Transfer Bank' },
                                      { value: 'cash',          label: 'Cash' },
                                      { value: 'qris',          label: 'QRIS' },
                                      { value: 'e_wallet',      label: 'E-Wallet (GoPay/OVO/Dana)' },
                                      { value: 'lainnya',       label: 'Lainnya' },
                                  ],
                                  get methodLabel() {
                                      const m = this.methods.find(x => x.value === this.method);
                                      return m ? m.label : 'Pilih metode...';
                                  }
                              }" class="relative">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="payment_status" value="paid">
                            <input type="hidden" name="payment_method" :value="method">

                            <button type="button" @click="open = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[14px]">upload_file</span>
                                Konfirmasi Bayar
                            </button>

                            {{-- Modal konfirmasi --}}
                            <div x-show="open" x-cloak
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                                <div class="rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-4" @click.stop
                                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                    <h4 class="font-semibold" style="color: var(--dash-text-heading);">Konfirmasi Pembayaran</h4>
                                    <p class="text-sm" style="color: var(--dash-text-muted);">
                                        {{ $payment->installment_number === 1 ? 'DP / Cicilan 1' : 'Cicilan ' . $payment->installment_number }}
                                        — <strong style="color: var(--dash-text-heading);">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                    </p>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Metode Bayar</label>
                                            <div class="relative" @click.away="methodOpen = false">
                                                <div @click="methodOpen = !methodOpen"
                                                     class="w-full px-3 py-2 rounded-lg text-sm flex justify-between items-center cursor-pointer transition-colors"
                                                     style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);"
                                                     onmouseover="this.style.borderColor='var(--dash-primary)'"
                                                     onmouseout="this.style.borderColor='var(--dash-input-border)'">
                                                    <span :class="method ? '' : 'opacity-50'"
                                                          x-text="methodLabel"
                                                          style="color: var(--dash-text-heading);"></span>
                                                    <span class="material-symbols-outlined text-[16px] shrink-0" style="color: var(--dash-text-faint);">expand_more</span>
                                                </div>
                                                <div x-show="methodOpen" x-transition x-cloak
                                                     class="absolute z-[60] w-full mt-1 rounded-lg shadow-lg overflow-hidden"
                                                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                    <ul class="py-1">
                                                        <template x-for="m in methods" :key="m.value">
                                                            <li @click="method = m.value; methodOpen = false"
                                                                class="px-4 py-2 text-sm cursor-pointer transition-colors"
                                                                style="color: var(--dash-text-body);"
                                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                onmouseout="this.style.backgroundColor='transparent'"
                                                                x-text="m.label"></li>
                                                        </template>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Catatan</label>
                                            <textarea name="notes" rows="2" placeholder="Opsional"
                                                      class="w-full rounded-lg text-sm px-3 py-2"
                                                      style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                      onfocus="this.style.borderColor='var(--dash-primary)'"
                                                      onblur="this.style.borderColor='var(--dash-input-border)'"></textarea>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" @click="open = false; methodOpen = false; method = ''"
                                                class="px-4 py-2 text-sm font-semibold rounded-xl transition-colors"
                                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            Batal
                                        </button>
                                        <button type="submit"
                                                class="px-4 py-2 text-sm font-semibold text-white rounded-xl hover:opacity-90 transition-opacity"
                                                style="background-color: #059669;">
                                            Konfirmasi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endcan
                    @endif
                </div>
            </div>
            @empty
                <p class="px-6 py-10 text-sm text-center" style="color: var(--dash-text-faint);">Belum ada data cicilan.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
