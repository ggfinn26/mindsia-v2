<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Document Signature Types
    |--------------------------------------------------------------------------
    |
    | Tipe dokumen yang menggunakan DocumentSignatureSetting untuk mengisi
    | nama dan jabatan penandatangan institusional.
    |
    | Tambahkan entry baru di sini saat membuat service baru yang query-nya
    | DocumentSignatureSetting::where('document_type', '<type>').
    |
    | Tipe aktif dan consumer-nya:
    |   SP       → WarningLetterGenerationService
    |   kwitansi → MemberReceiptService, PayrollSlipService
    |
    */
    'signature_types' => [
        'SP' => 'Surat Peringatan (SP)',
        'kwitansi' => 'Kwitansi / Slip Pembayaran',
    ],
];
