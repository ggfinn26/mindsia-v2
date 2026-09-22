<?php

namespace App\Mail;

use App\Models\MemberPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberPaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly MemberPayment $payment,
        private readonly string $pdfBinary,
    ) {}

    public function envelope(): Envelope
    {
        $installment = $this->payment->installment_number;
        $program = $this->payment->registration->receipt_program_name;

        return new Envelope(
            subject: "Kwitansi Pembayaran Cicilan ke-{$installment} – {$program}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.member.payment-receipt',
            with: ['payment' => $this->payment],
        );
    }

    public function attachments(): array
    {
        $filename = 'kwitansi-cicilan-'.$this->payment->installment_number.'.pdf';

        return [
            Attachment::fromData(
                fn () => $this->pdfBinary,
                $filename,
            )->withMime('application/pdf'),
        ];
    }
}
