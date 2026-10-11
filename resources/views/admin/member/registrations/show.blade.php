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

<div class="max-w-4xl mx-auto space-y-6">

    <div class="mb-2">
        <a href="{{ route('members.show', $member) }}"
           class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
            <span class="material-symbols-outlined text-[20px] mr-1">arrow_back</span>
            Kembali ke Profil {{ $member->full_name }}
        </a>
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
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-medium">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Identitas Member ── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Identitas Member</h3>
            <div class="flex items-center gap-2 flex-wrap">
                @if($activationStatus === ActivationStatus::Ditangguhkan)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-2.5 py-1 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">block</span> Ditangguhkan
                    </span>
                @elseif($isActive && $activationStatus === ActivationStatus::Aktif)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Akun Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">pending</span> Menunggu Pembayaran
                    </span>
                @endif

                @can('member.payment.manage')
                    @if($activationStatus === ActivationStatus::Aktif)
                        <form method="POST" action="{{ route('members.suspend', $member) }}">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Tangguhkan akun member ini?')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                <span class="material-symbols-outlined text-[14px]">block</span> Tangguhkan
                            </button>
                        </form>
                    @elseif($activationStatus === ActivationStatus::Ditangguhkan)
                        <form method="POST" action="{{ route('members.activate', $member) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Aktifkan
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
        <dl class="divide-y divide-gray-100">
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Nama</dt>
                <dd class="col-span-2 text-gray-900">{{ $member->full_name }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Email</dt>
                <dd class="col-span-2 text-gray-900">{{ $member->account?->email ?? $member->email ?? '—' }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">WhatsApp</dt>
                <dd class="col-span-2 text-gray-900">{{ $member->whatsapp_number ?? '—' }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Cabang</dt>
                <dd class="col-span-2 text-gray-900">{{ $member->branch?->branch_name ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- ── Program & Harga ── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-semibold text-gray-900">Program & Pembayaran</h3>
        </div>
        <dl class="divide-y divide-gray-100">
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Program</dt>
                <dd class="col-span-2 text-gray-900 font-semibold">{{ $registration->receipt_program_name }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Skema Bayar</dt>
                <dd class="col-span-2 text-gray-900">{{ $registration->receipt_payment_plan_label ?? '—' }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Harga Normal</dt>
                <dd class="col-span-2 text-gray-900">Rp {{ number_format($registration->original_price, 0, ',', '.') }}</dd>
            </div>
            @if($registration->discount_amount > 0)
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Diskon</dt>
                <dd class="col-span-2 text-green-700">
                    − Rp {{ number_format($registration->discount_amount, 0, ',', '.') }}
                    @if($registration->discount_code)
                        <span class="ml-1 text-xs text-gray-400">({{ $registration->discount_code }})</span>
                    @endif
                </dd>
            </div>
            @endif
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Total Tagihan</dt>
                <dd class="col-span-2 text-gray-900 font-bold text-base">Rp {{ number_format($registration->final_price, 0, ',', '.') }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Status Bayar</dt>
                <dd class="col-span-2">
                    @php
                        $statusMap = [
                            'unpaid'       => ['label' => 'Belum Bayar',     'class' => 'bg-red-100 text-red-700'],
                            'installments' => ['label' => 'Cicilan Berjalan','class' => 'bg-blue-100 text-blue-700'],
                            'paid_full'    => ['label' => 'Lunas',            'class' => 'bg-green-100 text-green-700'],
                            'paid'         => ['label' => 'Lunas',            'class' => 'bg-green-100 text-green-700'],
                        ];
                        $st = $statusMap[$registration->payment_status] ?? ['label' => $registration->payment_status, 'class' => 'bg-gray-100 text-gray-700'];
                    @endphp
                    <span class="inline-flex text-xs font-semibold px-2.5 py-1 rounded-full {{ $st['class'] }}">{{ $st['label'] }}</span>
                </dd>
            </div>
            @if($registration->employee)
            <div class="px-6 py-3 grid grid-cols-3 gap-4 text-sm">
                <dt class="font-medium text-gray-500">Dibuat oleh</dt>
                <dd class="col-span-2 text-gray-900">{{ $registration->employee->full_name }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- ── Cicilan ── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-semibold text-gray-900">Cicilan Pembayaran</h3>
            <p class="text-xs text-gray-400 mt-0.5">Aktivasi akun otomatis saat cicilan pertama dikonfirmasi.</p>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($registration->payments->sortBy('installment_number') as $payment)
            <div class="px-6 py-4 flex items-start gap-4 flex-wrap sm:flex-nowrap">
                {{-- Status icon --}}
                <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5
                    {{ $payment->payment_status === 'paid' ? 'bg-green-100' : 'bg-gray-100' }}">
                    <span class="material-symbols-outlined text-[16px] {{ $payment->payment_status === 'paid' ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $payment->payment_status === 'paid' ? 'check_circle' : 'radio_button_unchecked' }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $payment->installment_number === 1 ? 'DP / Cicilan 1' : 'Cicilan ' . $payment->installment_number }}
                        <span class="ml-2 font-bold text-[#5586DB]">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </p>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap"
                         x-data="{ editDue: false }">
                        <p class="text-xs text-gray-400">
                            Jatuh tempo: <span x-show="!editDue">{{ $payment->due_date?->format('d M Y') ?? '—' }}</span>
                        </p>
                        @can('member.payment.manage')
                        <button type="button" x-show="!editDue" @click="editDue = true"
                                class="text-[10px] font-medium text-[#5586DB] hover:underline">Ubah</button>
                        <form x-show="editDue" x-cloak
                              action="{{ route('member-payments.update', $payment) }}" method="POST"
                              class="flex items-center gap-1.5">
                            @csrf @method('PATCH')
                            <input type="date" name="due_date"
                                   value="{{ $payment->due_date?->format('Y-m-d') }}"
                                   class="text-xs border border-gray-300 rounded px-2 py-0.5 focus:border-[#5586DB] focus:ring-[#5586DB]">
                            <button type="submit"
                                    class="text-[10px] font-semibold text-white bg-[#5586DB] px-2 py-1 rounded hover:bg-[#4370BC]">Simpan</button>
                            <button type="button" @click="editDue = false"
                                    class="text-[10px] font-medium text-gray-500 hover:text-gray-700">Batal</button>
                        </form>
                        @endcan
                        @if($payment->paid_at)
                            <p class="text-xs text-gray-400">· Dibayar: {{ $payment->paid_at->format('d M Y') }}</p>
                        @endif
                    </div>
                    @if($payment->notes)
                        <p class="text-xs text-gray-500 mt-1 italic">{{ $payment->notes }}</p>
                    @endif
                </div>

                {{-- Action --}}
                <div class="shrink-0">
                    @if($payment->payment_status === 'paid')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">
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
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-[#5586DB] rounded-lg hover:bg-[#4370BC] transition-colors">
                                <span class="material-symbols-outlined text-[14px]">upload_file</span>
                                Konfirmasi Bayar
                            </button>

                            {{-- Modal konfirmasi --}}
                            <div x-show="open" x-cloak
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                                <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4" @click.stop>
                                    <h4 class="font-semibold text-gray-900">Konfirmasi Pembayaran</h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $payment->installment_number === 1 ? 'DP / Cicilan 1' : 'Cicilan ' . $payment->installment_number }}
                                        — <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                    </p>
                                    <div class="space-y-3">

                                        {{-- Custom dropdown metode bayar --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Metode Bayar</label>
                                            <div class="relative" @click.away="methodOpen = false">
                                                <div @click="methodOpen = !methodOpen"
                                                     class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm flex justify-between items-center cursor-pointer hover:border-[#5586DB] transition-colors">
                                                    <span :class="method ? 'text-gray-900' : 'text-gray-400'"
                                                          x-text="methodLabel"></span>
                                                    <span class="material-symbols-outlined text-[16px] text-gray-400 shrink-0">expand_more</span>
                                                </div>
                                                <div x-show="methodOpen" x-transition x-cloak
                                                     class="absolute z-[60] w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                                                    <ul class="py-1">
                                                        <template x-for="m in methods" :key="m.value">
                                                            <li @click="method = m.value; methodOpen = false"
                                                                class="px-4 py-2 text-sm text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer"
                                                                :class="method === m.value ? 'bg-[#EFF6FF] font-semibold' : ''"
                                                                x-text="m.label"></li>
                                                        </template>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                                            <textarea name="notes" rows="2" placeholder="Opsional"
                                                      class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]"></textarea>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" @click="open = false; methodOpen = false; method = ''"
                                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                            Batal
                                        </button>
                                        <button type="submit"
                                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
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
                <p class="px-6 py-8 text-sm text-center text-gray-400">Belum ada data cicilan.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
