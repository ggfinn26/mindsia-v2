@component('mail::message')
# Konfirmasi Pembayaran

Halo, **{{ $payment->registration->memberData->full_name }}**!

Pembayaran **Cicilan ke-{{ $payment->installment_number }}** untuk program **{{ $payment->registration->receipt_program_name }}** telah dikonfirmasi.

@component('mail::panel')
**Jumlah Dibayar:** Rp {{ number_format($payment->amount, 0, ',', '.') }}
**Tanggal:** {{ $payment->paid_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
@if($payment->payment_method)
**Metode:** {{ ['transfer_bank' => 'Transfer Bank', 'cash' => 'Cash', 'qris' => 'QRIS', 'e_wallet' => 'E-Wallet (GoPay/OVO/Dana)', 'lainnya' => 'Lainnya'][$payment->payment_method] ?? $payment->payment_method }}
@endif
@endcomponent

Kwitansi lengkap terlampir dalam email ini (format PDF).

@if($payment->installment_number === 1)
Akun Anda kini **aktif** dan dapat mengakses program.

@component('mail::button', ['url' => url('/member/dashboard'), 'color' => 'blue'])
Akses Dashboard
@endcomponent
@endif

Terima kasih telah mempercayakan pendidikan Anda kepada MINDSIA.

@endcomponent
