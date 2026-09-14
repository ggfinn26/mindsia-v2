<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfTestController extends Controller
{
    public function test()
    {
        return response('Test endpoint works');
    }

    public function testKopSurat()
    {
        $logoPath = public_path('images/mindsia-logo.jpg');
        $topRightPath = public_path('images/kop-top-right.png');
        $bottomLeftPath = public_path('images/kop-bottom-left.png');
        $igPath = public_path('images/icon-ig.png');
        $waPath = public_path('images/icon-wa.png');
        $webPath = public_path('images/icon-web.png');

        $logoUrl = 'data:image/jpeg;base64,'.base64_encode(file_get_contents($logoPath));
        $topRightUrl = 'data:image/png;base64,'.base64_encode(file_get_contents($topRightPath));
        $bottomLeftUrl = 'data:image/png;base64,'.base64_encode(file_get_contents($bottomLeftPath));
        $igUrl = 'data:image/png;base64,'.base64_encode(file_get_contents($igPath));
        $waUrl = 'data:image/png;base64,'.base64_encode(file_get_contents($waPath));
        $webUrl = 'data:image/png;base64,'.base64_encode(file_get_contents($webPath));

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        .kop-container {
            width: 100%;
            position: relative;
            height: 140px;
            background: white;
            border-bottom: 2px solid #333;
        }
        .kop-top-left {
            position: absolute;
            top: 5px;
            left: 10px;
            height: 80px;
        }
        .kop-top-left img {
            height: 80px;
            width: auto;
        }
        .kop-top-right {
            position: absolute;
            top: 5px;
            right: 10px;
            width: 50px;
            height: 70px;
        }
        .kop-top-right img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .kop-bottom-left {
            position: absolute;
            bottom: 5px;
            left: 10px;
            width: 45px;
            height: 45px;
        }
        .kop-bottom-left img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .kop-bottom-right {
            position: absolute;
            bottom: 5px;
            right: 10px;
            width: 40px;
        }
        .kop-icon {
            width: 40px;
            height: 40px;
            margin: 4px 0;
        }
        .kop-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .content {
            padding: 25px 20px;
        }
        .content h2 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .content p {
            margin: 0 0 8px 0;
            font-size: 11px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="kop-container">
        <div class="kop-top-left">
            <img src="'.$logoUrl.'" alt="Mindsia Logo">
        </div>
        <div class="kop-top-right">
            <img src="'.$topRightUrl.'" alt="Top Right">
        </div>
        <div class="kop-bottom-left">
            <img src="'.$bottomLeftUrl.'" alt="Bottom Left">
        </div>
        <div class="kop-bottom-right">
            <div class="kop-icon"><img src="'.$igUrl.'" alt="Instagram"></div>
            <div class="kop-icon"><img src="'.$waUrl.'" alt="WhatsApp"></div>
            <div class="kop-icon"><img src="'.$webUrl.'" alt="Website"></div>
        </div>
    </div>

    <div class="content">
        <h2>Test Dokumen dengan Kop Surat Mindsia</h2>
        <p><strong>Tanggal:</strong> '.date('d M Y').'</p>
        <p>Kop surat dengan logo Mindsia dan elemen dekoratif.</p>
    </div>
</body>
</html>';

        try {
            $options = new Options;
            $options->set([
                'defaultFont' => 'Arial',
                'isPhpEnabled' => false,
                'isRemoteEnabled' => true,
            ]);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return response($dompdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="test-kop.pdf"');
        } catch (\Exception $e) {
            return 'Error: '.$e->getMessage();
        }
    }
}
