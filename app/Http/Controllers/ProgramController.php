<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Models\MemberRegistration;
use App\Models\Program;
use App\Services\Member\MemberReceiptService;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Mews\Purifier\Facades\Purifier;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\TemplateProcessor;

class ProgramController extends Controller implements HasMiddleware
{
    public function __construct(
        private TelegramStorageService $telegramStorage,
        private MemberReceiptService $receiptService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('can:curriculum.program.create'),
        ];
    }

    public function index(): View
    {
        $programs = Program::with('curriculum')->orderBy('program_name')->get();

        return view('program.index', compact('programs'));
    }

    public function create(): View
    {
        return view('program.create');
    }

    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['program_description'] = $this->cleanDescription($data['program_description'] ?? null);
        $data = $this->sanitizeLandingData($data);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('programs', 'public');
        }

        if ($request->hasFile('receipt_template')) {
            $file = $request->file('receipt_template');
            $uploaded = $this->telegramStorage->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'program_receipt_template',
                null,
            );
            $data['receipt_template_path'] = $uploaded['file_id'];
        }

        $data = $this->applyGallery($data, $request, null);

        Program::create($data);

        return redirect()->route('programs.index')->with('success', 'Program berhasil dibuat.');
    }

    public function show(Program $program): View
    {
        $program->load('curriculum', 'branchQuotas.branch', 'paymentPlans');

        return view('program.show', compact('program'));
    }

    public function edit(Program $program): View
    {
        return view('program.edit', compact('program'));
    }

    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $data = $request->validated();
        $data['program_description'] = $this->cleanDescription($data['program_description'] ?? null);
        $data = $this->sanitizeLandingData($data);

        if ($request->hasFile('image')) {
            if ($program->image_path) {
                Storage::disk('public')->delete($program->image_path);
            }
            $data['image_path'] = $request->file('image')->store('programs', 'public');
        }

        if ($request->hasFile('receipt_template')) {
            $file = $request->file('receipt_template');
            $uploaded = $this->telegramStorage->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'program_receipt_template',
                null,
            );
            $data['receipt_template_path'] = $uploaded['file_id'];
        }

        $data = $this->applyGallery($data, $request, $program);

        $program->update($data);

        return redirect()->route('programs.show', $program)->with('success', 'Program berhasil diperbarui.');
    }

    /**
     * Sanitize rich-text description (allow basic formatting, strip anything unsafe).
     */
    private function cleanDescription(?string $html): ?string
    {
        if ($html === null || trim(strip_tags($html)) === '') {
            return null;
        }

        return Purifier::clean($html);
    }

    /**
     * Drop empty rows from the repeatable landing fields before persisting.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function sanitizeLandingData(array $data): array
    {
        if (array_key_exists('facilities', $data)) {
            $data['facilities'] = array_values(array_filter((array) $data['facilities'], fn ($v) => filled($v)));
        }

        if (array_key_exists('benefits', $data)) {
            $data['benefits'] = array_values(array_filter((array) $data['benefits'], fn ($v) => filled($v)));
        }

        if (array_key_exists('schedule', $data)) {
            $data['schedule'] = array_values(array_filter((array) $data['schedule'], fn ($row) => filled($row['day'] ?? null)));
        }

        if (array_key_exists('faqs', $data)) {
            $data['faqs'] = array_values(array_filter((array) $data['faqs'], fn ($row) => filled($row['q'] ?? null)));
        }

        return $data;
    }

    /**
     * Merge the per-program gallery: keep existing minus removed, append new uploads.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyGallery(array $data, Request $request, ?Program $program): array
    {
        unset($data['gallery_images'], $data['remove_gallery']);

        $remove = (array) $request->input('remove_gallery', []);
        $gallery = $program?->gallery ?? [];

        foreach ($remove as $path) {
            Storage::disk('public')->delete($path);
        }
        $gallery = array_values(array_diff($gallery, $remove));

        foreach ((array) $request->file('gallery_images', []) as $file) {
            $gallery[] = $file->store('programs/gallery', 'public');
        }

        $data['gallery'] = $gallery;

        return $data;
    }

    public function downloadReceiptTemplate(): Response
    {
        $phpWord = new PhpWord;
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'marginTop' => 720,
            'marginBottom' => 720,
            'marginLeft' => 1080,
            'marginRight' => 1080,
        ]);

        // Header
        $section->addText('KWITANSI PEMBAYARAN', ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
        $section->addText('MINDSIA', ['bold' => true, 'size' => 12], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        // Info registrasi
        $infoTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);
        $rows = [
            ['No. Registrasi', '${nomor_registrasi}'],
            ['Tanggal', '${tanggal_registrasi}'],
            ['Program', '${nama_program}'],
        ];
        foreach ($rows as [$label, $value]) {
            $row = $infoTable->addRow();
            $row->addCell(2800)->addText($label, ['bold' => true]);
            $row->addCell(400)->addText(':');
            $row->addCell(5000)->addText($value);
        }

        $section->addTextBreak(1);
        $section->addText('DATA PESERTA', ['bold' => true, 'size' => 11]);
        $section->addLine(['weight' => 1, 'width' => 5000, 'color' => '000000']);

        $memberTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);
        $memberRows = [
            ['Nama', '${nama_member}'],
            ['Tempat Lahir', '${tempat_lahir}'],
            ['Tanggal Lahir', '${tanggal_lahir_member}'],
            ['Alamat', '${alamat_member}'],
            ['Asal Instansi', '${nama_instansi}'],
            ['Kelas / Fakultas', '${member_class_or_faculty}'],
            ['No. WhatsApp', '${wa_member}'],
            ['Instagram', '${instagram_member}'],
        ];
        foreach ($memberRows as [$label, $value]) {
            $row = $memberTable->addRow();
            $row->addCell(2800)->addText($label, ['bold' => true]);
            $row->addCell(400)->addText(':');
            $row->addCell(5000)->addText($value);
        }

        $section->addTextBreak(1);
        $section->addText('DATA ORANG TUA', ['bold' => true, 'size' => 11]);
        $section->addLine(['weight' => 1, 'width' => 5000, 'color' => '000000']);

        $parentTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);
        $parentRows = [
            ['Nama Ayah', '${nama_ayah}'],
            ['Pekerjaan Ayah', '${pekerjaan_ayah}'],
            ['WA Ayah', '${wa_ayah}'],
            ['Nama Ibu', '${nama_ibu}'],
            ['Pekerjaan Ibu', '${pekerjaan_ibu}'],
            ['WA Ibu', '${wa_ibu}'],
        ];
        foreach ($parentRows as [$label, $value]) {
            $row = $parentTable->addRow();
            $row->addCell(2800)->addText($label, ['bold' => true]);
            $row->addCell(400)->addText(':');
            $row->addCell(5000)->addText($value);
        }

        $section->addTextBreak(1);
        $section->addText('RINCIAN PEMBAYARAN', ['bold' => true, 'size' => 11]);
        $section->addLine(['weight' => 1, 'width' => 5000, 'color' => '000000']);

        $payTable = $section->addTable(['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 100]);
        $payHeader = $payTable->addRow();
        $payHeader->addCell(4000)->addText('Keterangan', ['bold' => true]);
        $payHeader->addCell(4000)->addText('Nominal', ['bold' => true]);

        $payRows = [
            ['Harga Program', '${harga_program}'],
            ['Status Promo', '${status_promo}'],
            ['Potongan Promo', '${harga_promo}'],
            ['Uang Muka / DP', '${uang_muka}'],
            ['Sisa Pelunasan', '${biaya_pelunasan}'],
            ['Total Pembayaran', '${total_pembayaran}'],
        ];
        foreach ($payRows as [$label, $value]) {
            $row = $payTable->addRow();
            $row->addCell(4000)->addText($label);
            $row->addCell(4000)->addText($value);
        }

        $section->addTextBreak(2);

        // Tanda tangan
        $ttdTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);
        $ttdRow = $ttdTable->addRow();
        $ttdRow->addCell(5000)->addText('');
        $cell = $ttdRow->addCell(3200);
        $cell->addText('${ttd_penandatangan}', [], ['alignment' => Jc::CENTER]);
        $cell->addTextBreak(3);
        $cell->addText('${signer_name}', ['bold' => true], ['alignment' => Jc::CENTER]);

        $tempPath = tempnam(sys_get_temp_dir(), 'receipt_tpl_').'.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        $content = file_get_contents($tempPath);
        unlink($tempPath);

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="template-kwitansi-mindsia.docx"',
        ]);
    }

    /** @return string[] */
    private static function knownVarKeys(): array
    {
        return [
            'nomor_registrasi', 'tanggal_registrasi', 'nama_program',
            'harga_program', 'status_promo', 'harga_promo',
            'uang_muka', 'biaya_pelunasan', 'total_pembayaran',
            'ttd_penandatangan', 'signer_name',
            'nama_member', 'tempat_lahir', 'tanggal_lahir_member',
            'alamat_member', 'nama_instansi', 'member_class_or_faculty',
            'wa_member', 'instagram_member',
            'nama_ayah', 'pekerjaan_ayah', 'wa_ayah',
            'nama_ibu', 'pekerjaan_ibu', 'wa_ibu',
        ];
    }

    public function receiptTemplateTest(): View
    {
        $sample = MemberRegistration::with(['memberData', 'program'])->latest()->first();

        return view('program.receipt-template-test', [
            'knownVars' => self::knownVarKeys(),
            'result' => session('receipt_test_result'),
            'sampleInfo' => $sample
                ? $sample->memberData->full_name.' — '.$sample->program->program_name.' (ID #'.$sample->id.')'
                : null,
        ]);
    }

    public function receiptTemplateTestProcess(Request $request): RedirectResponse
    {
        $request->validate(['template' => 'required|file|mimes:docx']);

        // Use PHP's upload temp file directly — no need to persist to disk before processing.
        $sourcePath = $request->file('template')->getRealPath();

        $foundVars = [];
        $missingVars = [];
        $extraVars = [];
        $error = null;
        $resultToken = null;
        $dataSource = null;

        try {
            $processor = new TemplateProcessor($sourcePath);
            $foundVars = $processor->getVariables();

            $registration = MemberRegistration::with([
                'memberData.institution',
                'program',
                'paymentPlan',
                'payments',
            ])->latest()->first();

            if ($registration) {
                $dataSource = $registration->memberData->full_name
                    .' — '.$registration->program->program_name
                    .' (ID #'.$registration->id.')';
                $this->receiptService->fill($processor, $registration);
            } else {
                $this->fillFakeReceipt($processor);
            }

            $missingVars = array_values(array_diff(self::knownVarKeys(), $foundVars));
            $extraVars = array_values(array_diff($foundVars, self::knownVarKeys()));

            Storage::disk('local')->makeDirectory('receipt-test-tmp');
            $resultToken = auth()->id().'_'.bin2hex(random_bytes(16)).'.docx';
            $processor->saveAs(storage_path('app/receipt-test-tmp/'.$resultToken));
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        session()->flash('receipt_test_result', [
            'found' => $foundVars,
            'missing' => $missingVars,
            'extra' => $extraVars,
            'error' => $error,
            'resultToken' => $resultToken,
            'dataSource' => $dataSource,
        ]);

        return redirect()->route('programs.receipt-template.test');
    }

    private function fillFakeReceipt(TemplateProcessor $processor): void
    {
        $fake = [
            'nomor_registrasi' => 'XX/CONTOH/102026/0042',
            'tanggal_registrasi' => '15 Oktober 2026',
            'nama_program' => 'TOEFL Intensive (DATA CONTOH)',
            'harga_program' => 'Rp 2.500.000',
            'status_promo' => 'Promo',
            'harga_promo' => 'Rp 250.000',
            'uang_muka' => 'Rp 1.000.000',
            'biaya_pelunasan' => 'Rp 1.250.000',
            'total_pembayaran' => 'Rp 2.250.000',
            'ttd_penandatangan' => 'Direktur Utama',
            'signer_name' => 'Ahmad Fauzi, M.Pd.',
            'nama_member' => 'Siti Rahmawati',
            'tempat_lahir' => 'Makassar',
            'tanggal_lahir_member' => '12 Maret 2003',
            'alamat_member' => 'Jl. Sultan Alauddin No. 55, Makassar',
            'nama_instansi' => 'Universitas Hasanuddin',
            'member_class_or_faculty' => 'Fakultas Hukum / Semester 4',
            'wa_member' => '081234567890',
            'instagram_member' => '@sitirahmawati',
            'nama_ayah' => 'Bapak Contoh',
            'pekerjaan_ayah' => 'Wiraswasta',
            'wa_ayah' => '082345678901',
            'nama_ibu' => 'Ibu Contoh',
            'pekerjaan_ibu' => 'Ibu Rumah Tangga',
            'wa_ibu' => '083456789012',
        ];
        foreach ($fake as $key => $value) {
            $processor->setValue($key, $value);
        }
    }

    public function receiptTemplateTestDownload(Request $request): Response
    {
        $token = $request->query('token');
        abort_unless(
            $token && preg_match('/^\d+_[0-9a-f]{32}\.docx$/', $token)
                && str_starts_with($token, auth()->id().'_'),
            403
        );

        $path = storage_path('app/receipt-test-tmp/'.$token);
        abort_unless(file_exists($path), 404);

        $content = file_get_contents($path);
        @unlink($path);

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="hasil-tes-kwitansi.docx"',
        ]);
    }

    public function deactivate(Program $program): RedirectResponse
    {
        if ($program->registrations()->exists()) {
            return back()->with('error', 'Program tidak bisa dinonaktifkan karena masih ada registrasi member.');
        }

        $program->update(['is_active' => false]);

        return redirect()->route('programs.index')->with('success', 'Program berhasil dinonaktifkan.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        if ($program->classes()->where('status', 'active')->exists()) {
            return back()->with('error', 'Program tidak bisa dinonaktifkan karena masih ada kelas aktif.');
        }

        if ($program->registrations()->exists()) {
            return back()->with('error', 'Program tidak bisa dinonaktifkan karena masih ada registrasi member.');
        }

        $program->update(['is_active' => false]);

        return redirect()->route('programs.index')->with('success', 'Program berhasil dinonaktifkan.');
    }
}
