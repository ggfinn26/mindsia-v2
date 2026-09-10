<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentController extends Controller
{
    public function suratKeterangan()
    {
        $templatePath = storage_path('app/templates/surat-keterangan-template.docx');
        $apiKey = env('CONVERSION_API');

        $data = [
            'nomor_surat' => '001/MINDSIA/SK/2026',
            'tanggal' => date('d F Y'),
            'nama_penanda_tangan' => 'Ahmad Wijaya',
            'jabatan_penanda_tangan' => 'Manager Cabang Jakarta',
            'nama_karyawan' => 'Budi Santoso',
            'jabatan_karyawan' => 'Tutor',
            'tanggal_bergabung' => '15 Januari 2024',
            'cabang' => 'Jakarta',
        ];

        try {
            // Generate DOCX from template
            $templateProcessor = new TemplateProcessor($templatePath);
            foreach ($data as $key => $value) {
                $templateProcessor->setValue($key, $value);
            }

            $tempDocPath = storage_path('app/temp/surat-' . time() . '.docx');
            @mkdir(dirname($tempDocPath), 0755, true);
            $templateProcessor->saveAs($tempDocPath);

            // Convert DOCX to PDF via ConvertApi
            $url = "https://v2.convertapi.com/convert/docx/to/pdf?Auth={$apiKey}&StoreFile=true";
            $response = Http::asMultipart()
                ->attach('File', fopen($tempDocPath, 'r'), 'surat.docx')
                ->post($url);

            @unlink($tempDocPath);

            if ($response->successful()) {
                $pdfUrl = $response->json('Files.0.Url');
                $pdfContent = Http::get($pdfUrl)->body();

                return response($pdfContent, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="surat-keterangan.pdf"');
            }

            return "Conversion failed: " . $response->body();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
