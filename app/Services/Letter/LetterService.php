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
    private const VAR_TYPE_MAP = [
        'nomor_surat' => ['type' => 'auto',            'label' => 'Nomor Surat',             'note' => 'Di-generate saat publish'],
        'ttd_penandatangan' => ['type' => 'auto',            'label' => 'TTD Penandatangan',        'note' => 'Belum tersedia'],
        'ttd_pegawai' => ['type' => 'auto',            'label' => 'TTD Pegawai',              'note' => 'Belum tersedia'],
        'tanggal_surat' => ['type' => 'date',            'label' => 'Tanggal Surat'],
        'bulan_surat' => ['type' => 'auto',            'label' => 'Bulan Surat',              'note' => 'Derived dari tanggal_surat'],
        'tahun_surat' => ['type' => 'auto',            'label' => 'Tahun Surat',              'note' => 'Derived dari tanggal_surat'],
        'nama_hari' => ['type' => 'auto',            'label' => 'Nama Hari',                'note' => 'Derived dari tanggal_surat'],
        'kota_cabang' => ['type' => 'branch',          'label' => 'Kota / Cabang'],
        'nama_penandatangan' => ['type' => 'signer',          'label' => 'Nama Penandatangan'],
        'jabatan_penandatangan' => ['type' => 'signer_derived',  'label' => 'Jabatan Penandatangan'],
        'email_penandatangan' => ['type' => 'signer_derived',  'label' => 'Email Penandatangan'],
        'nama_pegawai' => ['type' => 'employee',        'label' => 'Nama Pegawai'],
        'jabatan_pegawai' => ['type' => 'employee_derived', 'label' => 'Jabatan Pegawai'],
        'divisi_pegawai' => ['type' => 'employee_derived', 'label' => 'Divisi Pegawai'],
        'agama_pegawai' => ['type' => 'employee_derived', 'label' => 'Agama Pegawai'],
        'alamat_pegawai' => ['type' => 'employee_derived', 'label' => 'Alamat Pegawai'],
        'email_pegawai' => ['type' => 'employee_derived', 'label' => 'Email Pegawai'],
        'wa_pegawai' => ['type' => 'employee_derived', 'label' => 'WA Pegawai'],
        'nomor_identitas_pegawai' => ['type' => 'employee_derived', 'label' => 'Nomor Identitas'],
        'tempat_lahir' => ['type' => 'employee_derived', 'label' => 'Tempat Lahir'],
        'tempat_tanggal_lahir_pegawai' => ['type' => 'employee_derived', 'label' => 'Tempat/Tgl Lahir'],
        'laki_laki_atau_perempuan' => ['type' => 'employee_derived', 'label' => 'Jenis Kelamin'],
        'tanggal_mulai_kontrak' => ['type' => 'employee_derived', 'label' => 'Tgl Mulai Kontrak'],
        'bulan_mulai_kontrak' => ['type' => 'auto',            'label' => 'Bulan Mulai Kontrak',      'note' => 'Derived dari tanggal_mulai_kontrak'],
        'tahun_mulai_kontrak' => ['type' => 'auto',            'label' => 'Tahun Mulai Kontrak',      'note' => 'Derived dari tanggal_mulai_kontrak'],
        'tanggal_berakhir_kontrak' => ['type' => 'employee_derived', 'label' => 'Tgl Akhir Kontrak'],
        'bulan_akhir_kontrak' => ['type' => 'auto',            'label' => 'Bulan Akhir Kontrak',      'note' => 'Derived dari tanggal_berakhir_kontrak'],
        'tahun_akhir_kontrak' => ['type' => 'auto',            'label' => 'Tahun Akhir Kontrak',      'note' => 'Derived dari tanggal_berakhir_kontrak'],
        'signer_name' => ['type' => 'signer_derived',  'label' => 'Nama Penandatangan (alias)'],
    ];

    private const AUTO_FILL_KEYS = [
        'generated' => [
            'nomor_surat', 'tanggal_surat', 'bulan_surat', 'tahun_surat', 'nama_hari', 'kota_cabang',
            'nama_penandatangan', 'jabatan_penandatangan', 'email_penandatangan', 'ttd_penandatangan', 'signer_name',
            'nama_pegawai', 'jabatan_pegawai', 'divisi_pegawai', 'agama_pegawai',
            'alamat_pegawai', 'email_pegawai', 'wa_pegawai', 'nomor_identitas_pegawai',
            'tempat_lahir', 'tempat_tanggal_lahir_pegawai', 'laki_laki_atau_perempuan',
            'tanggal_mulai_kontrak', 'bulan_mulai_kontrak', 'tahun_mulai_kontrak',
            'tanggal_berakhir_kontrak', 'bulan_akhir_kontrak', 'tahun_akhir_kontrak', 'ttd_pegawai',
        ],
        'marketing' => [
            'nomor_surat', 'tanggal_surat', 'bulan_surat', 'tahun_surat', 'nama_hari', 'kota_cabang',
            'nama_penandatangan', 'jabatan_penandatangan', 'email_penandatangan', 'ttd_penandatangan', 'signer_name',
            'nama_pegawai', 'jabatan_pegawai', 'divisi_pegawai', 'agama_pegawai',
            'alamat_pegawai', 'email_pegawai', 'wa_pegawai', 'nomor_identitas_pegawai',
            'tempat_lahir', 'tempat_tanggal_lahir_pegawai', 'laki_laki_atau_perempuan',
            'tanggal_mulai_kontrak', 'bulan_mulai_kontrak', 'tahun_mulai_kontrak',
            'tanggal_berakhir_kontrak', 'bulan_akhir_kontrak', 'tahun_akhir_kontrak', 'ttd_pegawai',
            'institusi',
        ],
        'announcement' => [
            'nomor_surat', 'tanggal_surat', 'bulan_surat', 'tahun_surat', 'kota_cabang',
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
            'signer_name',
        ],
    ];

    public function __construct(
        private readonly LetterNumberingService $numbering,
        private readonly ConvertApiService $convertApi,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    /**
     * Return all template vars with their UI type classification.
     */
    public function classifyTemplateVars(LetterTemplate $template): array
    {
        if (! $template->telegram_file_id) {
            return [];
        }

        $tmp = sys_get_temp_dir().'/tv_'.$template->id.'_'.time().'.docx';

        try {
            file_put_contents($tmp, $this->telegramStorage->downloadFile($template->telegram_file_id));
            $allVars = (new TemplateProcessor($tmp))->getVariables();
        } catch (\Throwable $e) {
            Log::warning('classifyTemplateVars: '.$e->getMessage());

            return [];
        } finally {
            if (file_exists($tmp)) {
                unlink($tmp);
            }
        }

        $result = [];
        $seen = [];

        foreach ($allVars as $var) {
            // Repeatable: key#N → group by base key
            if (preg_match('/^(.+)#\d+$/', $var, $m)) {
                $base = $m[1];
                if (! in_array($base, $seen)) {
                    $seen[] = $base;
                    $result[] = ['key' => $base, 'type' => 'repeatable', 'label' => ucwords(str_replace('_', ' ', $base))];
                }

                continue;
            }

            if (in_array($var, $seen)) {
                continue;
            }
            $seen[] = $var;

            $info = self::VAR_TYPE_MAP[$var] ?? ['type' => 'text', 'label' => ucwords(str_replace('_', ' ', $var))];
            $result[] = array_merge(['key' => $var], $info);
        }

        return $result;
    }

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
            );

            // Format tanggal_surat: accept YYYY-MM-DD from date picker or use today
            if (! empty($context['tanggal_surat']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $context['tanggal_surat'])) {
                $context['tanggal_surat'] = Carbon::parse($context['tanggal_surat'])->translatedFormat('d F Y');
            } elseif (empty($context['tanggal_surat'])) {
                $context['tanggal_surat'] = now()->translatedFormat('d F Y');
            }

            $letter = OutLetterViaGenerate::create([
                'letter_template_id' => $template->id,
                'branch_id' => $ids['branch_id'],
                'recipient_employee_id' => $ids['employee_id'] ?? null,
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
            $this->fillProcessor($processor, $context);
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
            $this->fillProcessor($processor, $context);
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

    private function fillProcessor(TemplateProcessor $processor, array $context): void
    {
        // Scalar values first
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            if (in_array($key, ['ttd_penandatangan', 'ttd_pegawai'])) {
                continue; // handled as image below
            }
            $processor->setValue($key, htmlspecialchars((string) $value));
        }

        // TTD images
        if (! empty($context['ttd_penandatangan'])) {
            $processor->setImageValue('ttd_penandatangan', $context['ttd_penandatangan']);
        }
        if (! empty($context['ttd_pegawai'])) {
            $processor->setImageValue('ttd_pegawai', $context['ttd_pegawai']);
        }

        // Repeatable vars: array values → cloneRow + setValue #N
        foreach ($context as $key => $values) {
            if (! is_array($values)) {
                continue;
            }
            $values = array_values(array_filter($values, fn ($v) => $v !== null && $v !== ''));
            if (empty($values)) {
                continue;
            }
            try {
                $processor->cloneRow($key, count($values));
            } catch (\Throwable) {
                // Already cloned by a paired var in the same row — skip cloneRow, still setValue
            }
            foreach ($values as $i => $val) {
                $processor->setValue("{$key}#".($i + 1), htmlspecialchars((string) $val));
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

    private function dateVars(?Carbon $date = null): array
    {
        $d = $date ?? now();

        return [
            'bulan_surat' => $d->translatedFormat('F'),
            'tahun_surat' => $d->format('Y'),
            'nama_hari' => $d->translatedFormat('l'),
        ];
    }

    private function generatedContext(array $ids): array
    {
        $employee = isset($ids['employee_id']) ? Employee::with(['branch', 'currentStatus.position'])->find($ids['employee_id']) : null;
        $signer = isset($ids['signer_employee_id']) ? Employee::with('currentStatus.position')->find($ids['signer_employee_id']) : null;

        $branch = $employee?->branch ?? ($ids['branch_id'] ? Branch::find($ids['branch_id']) : null);

        $startDate = $employee?->currentStatus?->contract_start_date;
        $endDate = $employee?->currentStatus?->contract_end_date;

        return array_merge($this->branchVars($branch), $this->dateVars(), [
            'nama_penandatangan' => $signer?->full_name ?? '',
            'signer_name' => $signer?->full_name ?? '',
            'jabatan_penandatangan' => $signer?->currentStatus?->position?->position_name ?? '',
            'email_penandatangan' => $signer?->email ?? '',
            'ttd_penandatangan' => '',

            'nama_pegawai' => $employee?->full_name ?? '',
            'jabatan_pegawai' => $employee?->currentStatus?->position?->position_name ?? '',
            'divisi_pegawai' => '',
            'agama_pegawai' => '',
            'alamat_pegawai' => '',
            'email_pegawai' => $employee?->email ?? '',
            'wa_pegawai' => $employee?->whatsapp_number ?? '',
            'nomor_identitas_pegawai' => $employee?->employee_code ?? '',
            'tempat_lahir' => '',
            'tempat_tanggal_lahir_pegawai' => $employee?->birthdate ? Carbon::parse($employee->birthdate)->translatedFormat('d F Y') : '',
            'laki_laki_atau_perempuan' => $employee?->gender === 'L' ? 'Laki-Laki' : 'Perempuan',
            'tanggal_mulai_kontrak' => $startDate ? Carbon::parse($startDate)->translatedFormat('d F Y') : '',
            'bulan_mulai_kontrak' => $startDate ? Carbon::parse($startDate)->translatedFormat('F') : '',
            'tahun_mulai_kontrak' => $startDate ? Carbon::parse($startDate)->format('Y') : '',
            'tanggal_berakhir_kontrak' => $endDate ? Carbon::parse($endDate)->translatedFormat('d F Y') : '',
            'bulan_akhir_kontrak' => $endDate ? Carbon::parse($endDate)->translatedFormat('F') : '',
            'tahun_akhir_kontrak' => $endDate ? Carbon::parse($endDate)->format('Y') : '',
            'ttd_pegawai' => '',
        ]);
    }

    private function marketingContext(array $ids): array
    {
        $employee = isset($ids['employee_id']) ? Employee::with(['branch', 'currentStatus.position'])->find($ids['employee_id']) : null;
        $branch = Branch::find($ids['branch_id']);
        $signer = isset($ids['signer_employee_id']) ? Employee::with('currentStatus.position')->find($ids['signer_employee_id']) : null;

        $startDate = $employee?->currentStatus?->contract_start_date;
        $endDate = $employee?->currentStatus?->contract_end_date;

        return array_merge($this->branchVars($branch), $this->dateVars(), [
            'institusi' => $ids['institution'] ?? '',

            'nama_penandatangan' => $signer?->full_name ?? '',
            'signer_name' => $signer?->full_name ?? '',
            'jabatan_penandatangan' => $signer?->currentStatus?->position?->position_name ?? '',
            'email_penandatangan' => $signer?->email ?? '',
            'ttd_penandatangan' => '',

            'nama_pegawai' => $employee?->full_name ?? '',
            'jabatan_pegawai' => $employee?->currentStatus?->position?->position_name ?? '',
            'divisi_pegawai' => '',
            'agama_pegawai' => '',
            'alamat_pegawai' => '',
            'email_pegawai' => $employee?->email ?? '',
            'wa_pegawai' => $employee?->whatsapp_number ?? '',
            'nomor_identitas_pegawai' => $employee?->employee_code ?? '',
            'tempat_lahir' => '',
            'tempat_tanggal_lahir_pegawai' => $employee?->birthdate ? Carbon::parse($employee->birthdate)->translatedFormat('d F Y') : '',
            'laki_laki_atau_perempuan' => $employee?->gender === 'L' ? 'Laki-Laki' : 'Perempuan',
            'tanggal_mulai_kontrak' => $startDate ? Carbon::parse($startDate)->translatedFormat('d F Y') : '',
            'bulan_mulai_kontrak' => $startDate ? Carbon::parse($startDate)->translatedFormat('F') : '',
            'tahun_mulai_kontrak' => $startDate ? Carbon::parse($startDate)->format('Y') : '',
            'tanggal_berakhir_kontrak' => $endDate ? Carbon::parse($endDate)->translatedFormat('d F Y') : '',
            'bulan_akhir_kontrak' => $endDate ? Carbon::parse($endDate)->translatedFormat('F') : '',
            'tahun_akhir_kontrak' => $endDate ? Carbon::parse($endDate)->format('Y') : '',
            'ttd_pegawai' => '',
        ]);
    }

    private function announcementContext(array $ids): array
    {
        $branch = isset($ids['branch_id']) ? Branch::find($ids['branch_id']) : null;
        $signer = isset($ids['signer_employee_id']) ? Employee::with('currentStatus.position')->find($ids['signer_employee_id']) : null;

        return array_merge($this->branchVars($branch), $this->dateVars(), [
            'nama_penandatangan' => $signer?->full_name ?? '',
            'jabatan_penandatangan' => $signer?->currentStatus?->position?->position_name ?? '',
            'email_penandatangan' => $signer?->email ?? '',
            'ttd_penandatangan' => '',

            'nama_pegawai' => $signer?->full_name ?? '',
            'jabatan_pegawai' => $signer?->currentStatus?->position?->position_name ?? '',
        ]);
    }
}
