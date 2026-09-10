<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
        }
        .kop-container {
            position: relative;
            width: 100%;
            min-height: 120px;
            border-bottom: 3px solid #000;
            margin-bottom: 20px;
            padding: 10px 0;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        .kop-top-left {
            flex-shrink: 0;
            height: 80px;
            display: flex;
            align-items: center;
        }
        .kop-top-left img {
            height: 80px;
            width: auto;
        }
        .kop-top-right {
            position: absolute;
            top: 5px;
            right: 10px;
            width: 60px;
            height: 80px;
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
            width: 50px;
            height: 50px;
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
            margin: 5px 0;
        }
        .kop-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .kop-center {
            flex: 1;
            text-align: center;
            padding: 10px 20px 0 0;
        }
        .kop-company-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .kop-branch {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .kop-address {
            font-size: 11pt;
            line-height: 1.3;
        }
        .letter-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 16pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .letter-number {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 20px;
            font-weight: normal;
        }
        .section {
            margin-bottom: 15px;
            text-align: justify;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        td {
            padding: 3px;
        }
        .label-col {
            width: 25%;
        }
        .colon-col {
            width: 5%;
        }
        .value-col {
            width: 70%;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-box {
            width: 35%;
            float: right;
            text-align: center;
        }
        .signature-date {
            margin-bottom: 50px;
            font-size: 11pt;
        }
        .signature-image {
            height: 60px;
            margin-bottom: 5px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
        }
        .signature-title {
            font-size: 11pt;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    @if($usekop ?? true)
    <div class="kop-container">
        <div class="kop-top-left">
            <img src="{{ $logoUrl }}" alt="Mindsia Logo">
        </div>
        <div class="kop-top-right">
            <img src="{{ $topRightUrl }}" alt="Top Right">
        </div>
        <div class="kop-bottom-left">
            <img src="{{ $bottomLeftUrl }}" alt="Bottom Left">
        </div>
        <div class="kop-bottom-right">
            <div class="kop-icon"><img src="{{ $igUrl }}" alt="Instagram"></div>
            <div class="kop-icon"><img src="{{ $waUrl }}" alt="WhatsApp"></div>
            <div class="kop-icon"><img src="{{ $webUrl }}" alt="Website"></div>
        </div>
        <div class="kop-center">
            <div class="kop-company-name">{{ $companyName }}</div>
            <div class="kop-branch">CABANG {{ $branch }}</div>
            <div class="kop-address">
                {{ $branchAddress }}, {{ $branchCity }}, {{ $branchProvince }}<br>
                Telp: {{ $branchPhone }} | WA: {{ $branchWhatsapp }} | IG: {{ $branchInstagram }}
            </div>
        </div>
    </div>
    @endif

    <div class="letter-title">{{ $letterTitle }}</div>

    @if($letterNumber)
    <div class="letter-number">Nomor: {{ $letterNumber }}</div>
    @endif

    <div class="section">Yang bertanda tangan di bawah ini:</div>

    <table>
        <tr>
            <td class="label-col">Nama</td>
            <td class="colon-col">:</td>
            <td class="value-col" style="font-weight: bold;">{{ $signerName }}</td>
        </tr>
        <tr>
            <td class="label-col">Jabatan</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $signerTitle }}</td>
        </tr>
        <tr>
            <td class="label-col">Perusahaan</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $companyName }}</td>
        </tr>
    </table>

    <div class="section">Menerangkan dengan sesungguhnya bahwa:</div>

    <table>
        <tr>
            <td class="label-col">Nama</td>
            <td class="colon-col">:</td>
            <td class="value-col" style="font-weight: bold;">{{ $employeeName }}</td>
        </tr>
        <tr>
            <td class="label-col">Jabatan</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $employeePosition }}</td>
        </tr>
        <tr>
            <td class="label-col">Tanggal Bergabung</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $employeeJoinDate }}</td>
        </tr>
    </table>

    <div class="section">
        Adalah benar karyawan kami di {{ $companyName }} cabang {{ $branch }}, dan masih aktif bekerja hingga saat surat ini dikeluarkan.
    </div>

    <div class="section">
        Surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.
    </div>

    <div class="signature-section clearfix">
        <div class="signature-box">
            <div class="signature-date">
                {{ $branchCity }}, {{ $signatureDate }}
            </div>
            @if($signatureImageUrl)
            <img src="{{ $signatureImageUrl }}" alt="Signature" class="signature-image">
            @else
            <div style="height: 60px;"></div>
            @endif
            <div class="signature-name">{{ $signerName }}</div>
            <div class="signature-title">{{ $signerTitle }}</div>
        </div>
    </div>
</body>
</html>
