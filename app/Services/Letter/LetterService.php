<?php

namespace App\Services\Letter;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\LetterTemplate;
use App\Models\OutLetterViaGenerate;
use App\Services\TelegramStorageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;

class LetterService
{
    private const AUTO_FILL_KEYS = [
        'generated' => [
            'nomor_surat', 'tanggal_surat', 'kota_cabang',
            'nama_penandatangan', 'jabatan_penandatangan', 'email_penandatangan', 'ttd_penandatangan',
            'nama_pegawai', 'jabatan_pegawai', 'divisi_pegawai', 'agama_pegawai',
            'alamat_pegawai', 'email_pegawai', 'wa_pegawai', 'nomor_identitas_pegawai',
            'tempat_tanggal_lahir_pegawai', 'laki_laki_atau_perempuan',
            'tanggal_mulai_kontrak', 'tanggal_berakhir_kontrak', 'ttd_pegawai',
        ],
        'marketing' => [
            'nomor_surat', 'tanggal_surat', 'kota_cabang',
            'nama_penandatangan', 'jabatan_penandatangan', 'email_penandatangan', 'ttd_penandatangan',
            'nama_pegawai', 'jabatan_pegawai', 'divisi_pegawai', 'agama_pegawai',
            'alamat_pegawai', 'email_pegawai', 'wa_pegawai', 'nomor_identitas_pegawai',
            'tempat_tanggal_lahir_pegawai', 'laki_laki_atau_perempuan',
            'tanggal_mulai_kontrak', 'tanggal_berakhir_kontrak', 'ttd_pegawai',
            'institusi',
        ],
        'announcement' => [
            'nomor_surat', 'tanggal_surat', 'kota_cabang',
            'nama_penandatangan', 'jabatan_penandatangan', 'email_penandatangan', 'ttd_penandatangan',
            'nama_pegawai', 'jabatan_pegawai',
        ],
        'member' => [
            'nomor_surat', 'tanggal_surat',
            'nama_member', 'alamat_member', 'tanggal_lahir_member', 'nama_instansi',
            'wa_member', 'instagram_member',
            'nama_ayah', 'pekerjaan_ayah', 'wa_ayah',
            'nama_ibu', 'pekerjaan_ibu', 'wa_ibu',
            'nomor_registrasi', 'tanggal_registrasi',
            'nama_program', 'harga_program', 'harga_promo', 'status_promo',
            'uang_muka', 'total_pembayaran', 'biaya_pelunasan',
        ],
    ];

    public function __construct(
        private readonly LetterNumberingService $numbering,
        private readonly ConvertApiService $convertApi,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    /**
     * Create a draft letter from template + manual vars.
     * Fills Word template placeholders and uploads draft DOCX to Telegram.
     */
    public function generate(
        int $templateId,
        array $ids,
        array $manualVars = [],
        int $createdByEmployeeId = 0
    ): OutLetterViaGenerate {
        $template = LetterTemplate::findOrFail($templateId);

        return DB::transaction(function () use ($template, $ids, $manualVars, $createdByEmployeeId) {
            $context = array_merge(
                $this->buildContext($template->letter_category, $ids),
                $manualVars,
                ['tanggal_surat' => now()->translatedFormat('d F Y')]
            );

            $letter = OutLetterViaGenerate::create([
                'letter_template_id' => $template->id,
                'branch_id' => $ids['branch_id'],
                'letter_type' => $template->template_code,
                'letter_date' => now()->toDateString(),
                'signer_employee_id' => $ids['signer_employee_id'] ?? null,
                'status' => 'draft',
                'payload' => $context,
                'created_by_employee_id' => $createdByEmployeeId,
            ]);

            $telegramFileId = $this->fillAndUploadDocx($template, $context, 'draft_'.$letter->id);
            if ($telegramFileId) {
                $letter->update(['telegram_file_id' => $telegramFileId]);
            }

            return $letter;
        });
    }

    /**
     * Publish: generate letter number, embed signature, convert DOCX→PDF, upload to Telegram.
     */
    public function publish(OutLetterViaGenerate $letter, int $publishedByEmployeeId): OutLetterViaGenerate
    {
        return DB::transaction(function () use ($letter, $publishedByEmployeeId) {
            $template = $letter->template;
            $branch = $letter->branch;
            $signer = $letter->signer;

            $letterNumber = null;
            if ($template->letter_number_format) {
                $seq = $this->numbering->nextSequence(
                    $letter->letter_type,
                    $letter->branch_id,
                    now()->year,
                    now()->month
                );
                $letterNumber = $this->numbering->format($template->letter_number_format, [
                    'branch' => $branch?->code_branches ?? '',
                    'year' => now()->year,
                    'month' => now()->month,
                    'type' => $letter->letter_type,
                    'seq' => $seq,
                ]);
            }

            $context = array_merge($letter->payload ?? [], [
                'nomor_surat' => $letterNumber ?? '',
                'nama_penandatangan' => $signer?->full_name ?? '',
                'jabatan_penandatangan' => $signer?->currentStatus?->position?->position_name ?? '',
            ]);

            $pdfTelegramFileId = $this->fillConvertAndUploadPdf($template, $context, $letter->id);

            $letter->update([
                'status' => 'published',
                'letter_number' => $letterNumber,
                'telegram_file_id' => $pdfTelegramFileId ?? $letter->telegram_file_id,
                'signer_name_snapshot' => $signer?->full_name,
                'signer_title_snapshot' => $signer?->currentStatus?->position?->position_name,
                'published_by_employee_id' => $publishedByEmployeeId,
                'published_at' => now(),
            ]);

            return $letter;
        });
    }

    /**
     * Extract manual-fill placeholder keys from a Word template (via alt text).
     * Returns keys that are not in the auto-fill list for the given category.
     */
    public function extractManualPlaceholders(LetterTemplate $template): array
    {
        if (! $template->telegram_file_id) {
            return [];
        }

        $tmpPath = sys_get_temp_dir().'/lt_'.$template->id.'_'.time().'.docx';

        try {
            $content = $this->telegramStorage->downloadFile($template->telegram_file_id);
            file_put_contents($tmpPath, $content);

            $processor = new TemplateProcessor($tmpPath);
            $placeholders = $processor->getVariables();
            $autoKeys = self::AUTO_FILL_KEYS[$template->letter_category] ?? [];

            return array_values(array_diff($placeholders, $autoKeys));
        } catch (\Throwable $e) {
            Log::warning('extractManualPlaceholders failed: '.$e->getMessage());

            return [];
        } finally {
            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }
        }
    }

    private function fillAndUploadDocx(LetterTemplate $template, array $context, string $label): ?string
    {
        if (! $template->telegram_file_id) {
            return null;
        }

        $tmpSrc = sys_get_temp_dir().'/lt_src_'.$label.'_'.time().'.docx';
        $tmpOut = sys_get_temp_dir().'/lt_out_'.$label.'_'.time().'.docx';

        try {
            $content = $this->telegramStorage->downloadFile($template->telegram_file_id);
            file_put_contents($tmpSrc, $content);

            $processor = new TemplateProcessor($tmpSrc);
            foreach ($context as $key => $value) {
                $processor->setValue($key, htmlspecialchars((string) $value));
            }

            if (! empty($context['ttd_penandatangan'])) {
                $processor->setImageValue('ttd_penandatangan', $context['ttd_penandatangan']);
            }
            if (! empty($context['ttd_pegawai'])) {
                $processor->setImageValue('ttd_pegawai', $context['ttd_pegawai']);
            }

            $processor->saveAs($tmpOut);

            $uploaded = $this->telegramStorage->uploadFile($tmpOut, $label.'.docx', 'letter_draft', null);

            return $uploaded['file_id'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('fillAndUploadDocx failed: '.$e->getMessage());

            return null;
        } finally {
            foreach ([$tmpSrc, $tmpOut] as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }

    private function fillConvertAndUploadPdf(LetterTemplate $template, array $context, int $letterId): ?string
    {
        if (! $template->telegram_file_id) {
            return null;
        }

        $tmpSrc = sys_get_temp_dir().'/lt_src_pub_'.$letterId.'_'.time().'.docx';
        $tmpOut = sys_get_temp_dir().'/lt_out_pub_'.$letterId.'_'.time().'.docx';

        try {
            $content = $this->telegramStorage->downloadFile($template->telegram_file_id);
            file_put_contents($tmpSrc, $content);

            $processor = new TemplateProcessor($tmpSrc);
            foreach ($context as $key => $value) {
                $processor->setValue($key, htmlspecialchars((string) $value));
            }

            if (! empty($context['ttd_penandatangan'])) {
                $processor->setImageValue('ttd_penandatangan', $context['ttd_penandatangan']);
            }
            if (! empty($context['ttd_pegawai'])) {
                $processor->setImageValue('ttd_pegawai', $context['ttd_pegawai']);
            }

            $processor->saveAs($tmpOut);

            $pdfBinary = $this->convertApi->docxToPdf($tmpOut);

            if (! $pdfBinary) {
                throw new \RuntimeException("ConvertApi gagal untuk surat #{$letterId}. Cek CONVERSION_API di .env atau coba lagi.");
            }

            $tmpPdf = sys_get_temp_dir().'/lt_pdf_'.$letterId.'_'.time().'.pdf';
            file_put_contents($tmpPdf, $pdfBinary);

            $uploaded = $this->telegramStorage->uploadFile($tmpPdf, 'surat_'.$letterId.'.pdf', 'letter_published', $letterId);

            unlink($tmpPdf);

            return $uploaded['file_id'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('fillConvertAndUploadPdf failed: '.$e->getMessage());

            return null;
        } finally {
            foreach ([$tmpSrc, $tmpOut] as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }

    private function buildContext(string $category, array $ids): array
    {
        return match ($category) {
            'generated' => $this->generatedContext($ids),
            'marketing' => $this->marketingContext($ids),
            'announcement' => $this->announcementContext($ids),
            default => [],
        };
    }

    private function branchVars(?Branch $branch): array
    {
        return [
            'kota_cabang' => $branch?->branch_name ?? '',
        ];
    }

    private function generatedContext(array $ids): array
    {
        $employee = Employee::with(['branch', 'currentStatus.position'])->findOrFail($ids['employee_id']);
        $signer = isset($ids['signer_employee_id']) ? Employee::with('currentStatus.position')->find($ids['signer_employee_id']) : null;

        return array_merge($this->branchVars($employee->branch), [
            'nama_penandatangan' => $signer?->full_name ?? '',
            'jabatan_penandatangan' => $signer?->currentStatus?->position?->position_name ?? '',
            'email_penandatangan' => $signer?->email ?? '',
            'ttd_penandatangan' => '', // $signer?->signature_image

            'nama_pegawai' => $employee->full_name ?? '',
            'jabatan_pegawai' => $employee->currentStatus?->position?->position_name ?? '',
            'divisi_pegawai' => '', // $employee->division
            'agama_pegawai' => '', // $employee->religion
            'alamat_pegawai' => '', // $employee->address
            'email_pegawai' => $employee->email ?? '',
            'wa_pegawai' => $employee->whatsapp_number ?? '',
            'nomor_identitas_pegawai' => $employee->employee_code ?? '',
            'tempat_tanggal_lahir_pegawai' => $employee->birthdate ? Carbon::parse($employee->birthdate)->translatedFormat('d F Y') : '',
            'laki_laki_atau_perempuan' => $employee->gender == 'L' ? 'Laki-Laki' : 'Perempuan',
            'tanggal_mulai_kontrak' => $employee->currentStatus?->join_date ? Carbon::parse($employee->currentStatus->join_date)->translatedFormat('d F Y') : '',
            'tanggal_berakhir_kontrak' => $employee->currentStatus?->contract_end_date ? Carbon::parse($employee->currentStatus->contract_end_date)->translatedFormat('d F Y') : '',
            'ttd_pegawai' => '', // $employee->signature_image
        ]);
    }

    private function marketingContext(array $ids): array
    {
        // For marketing contract, it's essentially a contract letter, so we also load employee data
        $employee = isset($ids['employee_id']) ? Employee::with(['branch', 'currentStatus.position'])->find($ids['employee_id']) : null;
        $branch = Branch::find($ids['branch_id']);
        $signer = isset($ids['signer_employee_id']) ? Employee::with('currentStatus.position')->find($ids['signer_employee_id']) : null;

        return array_merge($this->branchVars($branch), [
            'institusi' => $ids['institution'] ?? '',

            'nama_penandatangan' => $signer?->full_name ?? '',
            'jabatan_penandatangan' => $signer?->currentStatus?->position?->position_name ?? '',
            'email_penandatangan' => $signer?->email ?? '',
            'ttd_penandatangan' => '', // $signer?->signature_image

            'nama_pegawai' => $employee?->full_name ?? '',
            'jabatan_pegawai' => $employee?->currentStatus?->position?->position_name ?? '',
            'divisi_pegawai' => '', // $employee->division
            'agama_pegawai' => '', // $employee->religion
            'alamat_pegawai' => '', // $employee->address
            'email_pegawai' => $employee?->email ?? '',
            'wa_pegawai' => $employee?->whatsapp_number ?? '',
            'nomor_identitas_pegawai' => $employee?->employee_code ?? '',
            'tempat_tanggal_lahir_pegawai' => $employee?->birthdate ? Carbon::parse($employee->birthdate)->translatedFormat('d F Y') : '',
            'laki_laki_atau_perempuan' => $employee?->gender == 'L' ? 'Laki-Laki' : 'Perempuan',
            'tanggal_mulai_kontrak' => $employee?->currentStatus?->join_date ? Carbon::parse($employee->currentStatus->join_date)->translatedFormat('d F Y') : '',
            'tanggal_berakhir_kontrak' => $employee?->currentStatus?->contract_end_date ? Carbon::parse($employee->currentStatus->contract_end_date)->translatedFormat('d F Y') : '',
            'ttd_pegawai' => '', // $employee->signature_image
        ]);
    }

    private function announcementContext(array $ids): array
    {
        $branch = isset($ids['branch_id']) ? Branch::find($ids['branch_id']) : null;
        $signer = isset($ids['signer_employee_id']) ? Employee::with('currentStatus.position')->find($ids['signer_employee_id']) : null;

        return array_merge($this->branchVars($branch), [
            'nama_penandatangan' => $signer?->full_name ?? '',
            'jabatan_penandatangan' => $signer?->currentStatus?->position?->position_name ?? '',
            'email_penandatangan' => $signer?->email ?? '',
            'ttd_penandatangan' => '', // $signer?->signature_image

            'nama_pegawai' => $signer?->full_name ?? '', // sender for announcement
            'jabatan_pegawai' => $signer?->currentStatus?->position?->position_name ?? '',
        ]);
    }
}
