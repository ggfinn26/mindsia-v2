<?php

namespace App\Services\Member;

use App\Models\DocumentSignatureSetting;
use App\Models\MemberRegistration;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class MemberReceiptService
{
    public function generate(MemberRegistration $registration): string
    {
        $registration->loadMissing([
            'memberData.institution',
            'program',
            'paymentPlan',
            'payments',
            'employee.branch',
        ]);

        $templatePath = storage_path(
            'app/private/'.$registration->program->receipt_template_path
        );

        if (! file_exists($templatePath)) {
            throw new \RuntimeException(
                'Template kwitansi tidak ditemukan untuk program '.$registration->program->program_name
            );
        }

        $processor = new TemplateProcessor($templatePath);

        $this->fill($processor, $registration);

        $outPath = storage_path('app/private/program-templates/receipt-preview-'.uniqid().'.docx');
        $processor->saveAs($outPath);

        return $outPath;
    }

    public function fill(TemplateProcessor $processor, MemberRegistration $registration): void
    {
        $member = $registration->memberData;
        $payments = $registration->payments->sortBy('installment_number');
        $sigSetting = DocumentSignatureSetting::where('document_type', 'kwitansi')->first();

        // DP / uang muka = cicilan pertama
        $dp = $payments->first()?->amount ?? 0;
        // Pelunasan = total - DP
        $pelunasan = $registration->final_price - $dp;

        $processor->setValue('nomor_registrasi', $registration->formatted_number);
        $processor->setValue('tanggal_registrasi', Carbon::parse($registration->created_at)->translatedFormat('d F Y'));
        $processor->setValue('nama_program', $registration->receipt_program_name);
        $processor->setValue('harga_program', 'Rp '.number_format($registration->original_price, 0, ',', '.'));
        $processor->setValue('status_promo', $registration->discount_amount > 0 ? 'Promo' : '-');
        $processor->setValue('harga_promo', $registration->discount_amount > 0
            ? 'Rp '.number_format($registration->discount_amount, 0, ',', '.')
            : '-'
        );
        $processor->setValue('uang_muka', 'Rp '.number_format($dp, 0, ',', '.'));
        $processor->setValue('biaya_pelunasan', 'Rp '.number_format($pelunasan, 0, ',', '.'));
        $processor->setValue('total_pembayaran', 'Rp '.number_format($registration->final_price, 0, ',', '.'));
        $processor->setValue('ttd_penandatangan', $sigSetting?->signer_title ?? '');
        $processor->setValue('signer_name', $sigSetting?->signer_name ?? '');

        // Data member
        $processor->setValue('nama_member', $member->full_name);
        $processor->setValue('tempat_lahir', $member->birth_place ?? '-');
        $processor->setValue('tanggal_lahir_member', $member->birthdate ? Carbon::parse($member->birthdate)->translatedFormat('d F Y') : '-');
        $processor->setValue('alamat_member', $member->address);
        $processor->setValue('nama_instansi', $member->institution?->institution_name ?? $member->institution_name_custom ?? '-');
        $processor->setValue('member_class_or_faculty', $member->class_or_faculty ?? '-');
        $processor->setValue('wa_member', $member->whatsapp_number);
        $processor->setValue('instagram_member', $member->instagram ?? '-');

        // Data orang tua
        $processor->setValue('nama_ayah', $member->father_name);
        $processor->setValue('pekerjaan_ayah', $member->father_occupation);
        $processor->setValue('wa_ayah', $member->father_whatsapp);
        $processor->setValue('nama_ibu', $member->mother_name);
        $processor->setValue('pekerjaan_ibu', $member->mother_occupation);
        $processor->setValue('wa_ibu', $member->mother_whatsapp);
    }
}
