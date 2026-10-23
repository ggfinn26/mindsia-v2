<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Komparasi Cabang - MINDSIA</title>
    <style>
        /* Base / Typography */
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 8pt; color: #1a1c1c; line-height: 1.4; margin: 0; padding: 0; background-color: #ffffff; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .clearfix::after { content: ""; clear: both; display: table; }
        
        /* Header & Meta */
        .doc-head { border-bottom: 2px solid #1E293B; padding-bottom: 12px; margin-bottom: 16px; margin-top: -10px; }
        .head-left { float: left; width: 60%; }
        .head-right { float: right; width: 40%; text-align: right; }
        .logo { height: 26px; }
        .doc-title { font-size: 16pt; font-weight: bold; color: #1E293B; margin: 0 0 3px 0; letter-spacing: -0.5px; text-transform: uppercase; }
        .doc-code { font-size: 7.5pt; color: #7e7663; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 5px; }
        
        /* Section Titles */
        .section-title { font-size: 10pt; font-weight: bold; color: #1E293B; border-left: 3px solid #dbb940; padding-left: 8px; margin: 18px 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Layout Grids */
        .grid-2 { width: 100%; margin-bottom: 16px; }
        .grid-col { float: left; width: 48%; }
        .grid-col:first-child { margin-right: 4%; }
        
        /* Summary Boxes (Sovereign Executive Cards) */
        .summary-box { border: 1px solid #cfc6af; background-color: #f9f9f8; padding: 12px; border-radius: 2px; }
        .summary-label { font-size: 7.5pt; color: #4c4635; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; margin-bottom: 6px; }
        .summary-val { font-size: 15pt; font-weight: bold; color: #1E293B; margin: 0; letter-spacing: -0.5px; }
        
        /* Data Tables */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .data-table th, .data-table td { padding: 7px 6px; border: 1px solid #cfc6af; font-size: 8pt; vertical-align: middle; }
        .data-table th { background-color: #f3f4f3; color: #1E293B; font-weight: bold; text-transform: uppercase; font-size: 7.5pt; letter-spacing: 0.5px; border-bottom: 2px solid #1E293B; }
        .data-table tr:nth-child(even) { background-color: #fdfdfd; }
        
        /* HTML Chart Styles */
        .chart-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .chart-table td { padding: 4px 0; border: none; font-size: 7.5pt; vertical-align: middle; }
        .bar-wrap { width: 100%; background-color: #e5e5e5; height: 12px; border-radius: 1px; overflow: hidden; position: relative; }
        .bar-fill { height: 100%; position: absolute; top: 0; left: 0; }
        
        /* Footer */
        .doc-foot { border-top: 1px solid #cfc6af; padding-top: 8px; margin-top: 25px; font-size: 7pt; color: #7e7663; }
        .foot-left { float: left; width: 70%; }
        .foot-right { float: right; width: 30%; text-align: right; font-weight: bold; }
    </style>
</head>
<body>

@php
    $branchAName = $data['branch_a']['branch_name'] ?? 'Cabang A';
    $branchBName = $data['branch_b']['branch_name'] ?? 'Cabang B';
    
    $memA = $data['data_a']['members'] ?? 0;
    $memB = $data['data_b']['members'] ?? 0;
    $totMem = $memA + $memB;
    $pctMemA = $totMem > 0 ? round(($memA / $totMem) * 100) : 0;
    $pctMemB = $totMem > 0 ? round(($memB / $totMem) * 100) : 0;
    
    $revA = $data['data_a']['revenue'] ?? 0;
    $revB = $data['data_b']['revenue'] ?? 0;
    $totRev = $revA + $revB;
    $pctRevA = $totRev > 0 ? round(($revA / $totRev) * 100) : 0;
    $pctRevB = $totRev > 0 ? round(($revB / $totRev) * 100) : 0;
@endphp

<div class="doc-head">
    <div class="head-left">
        @if(isset($logoSrc) && $logoSrc)
            <img src="{{ $logoSrc }}" alt="MINDSIA Logo" class="logo">
        @else
            <div style="font-size: 16pt; font-weight: bold; color: #1E293B;">MINDSIA</div>
        @endif
        <div style="font-size: 8pt; color: #7e7663; margin-top: 5px;">MANAJEMEN INFORMASI & DATA SEKOLAH INDONESIA</div>
    </div>
    <div class="head-right">
        <div class="doc-title">KOMPARASI CABANG</div>
        <div class="doc-code">{{ $docCode ?? 'DOC/COMP/' . now()->format('Ymd-His') }}</div>
        <div style="font-size: 7.5pt; color: #4c4635; margin-top: 3px;">
            PERIODE: <span style="font-weight:bold;">{{ strtoupper($data['period_label']) }}</span><br>
            @if(!empty($data['program']))
                PROGRAM: <span style="font-weight:bold;">{{ strtoupper($data['program']['program_name']) }}</span><br>
            @endif
            Cetak: {{ $printedAt ?? now()->format('d M Y, H:i') }}
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Objek Analisis A</div>
            <div class="summary-val">{{ $branchAName }}</div>
        </div>
    </div>
    <div class="grid-col">
        <div class="summary-box" style="border-left: 3px solid #dbb940;">
            <div class="summary-label">Objek Analisis B</div>
            <div class="summary-val">{{ $branchBName }}</div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<h2 class="section-title">I. Matriks Komparasi Kinerja</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 40%;">Indikator Strategis</th>
            <th class="text-right" style="width: 30%;">{{ $branchAName }}</th>
            <th class="text-right" style="width: 30%;">{{ $branchBName }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Total Akuisisi Member Baru</strong></td>
            <td class="text-right font-mono" style="font-size: 10pt; font-weight: bold;">{{ number_format($memA) }}</td>
            <td class="text-right font-mono" style="font-size: 10pt; font-weight: bold;">{{ number_format($memB) }}</td>
        </tr>
        <tr>
            <td><strong>Total Realisasi Revenue</strong></td>
            <td class="text-right font-mono" style="color: #00677d; font-size: 10pt; font-weight: bold;">Rp {{ number_format($revA, 0, ',', '.') }}</td>
            <td class="text-right font-mono" style="color: #00677d; font-size: 10pt; font-weight: bold;">Rp {{ number_format($revB, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<h2 class="section-title">II. Visualisasi Proporsi (A vs B)</h2>
<div class="grid-2">
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Proporsi Akuisisi Member</div>
            <table class="chart-table">
                <tr>
                    <td style="width: 25%; color: #4c4635; font-weight: bold;">Cab. A</td>
                    <td style="width: 55%; padding-right: 8px;">
                        <div class="bar-wrap">
                            <div class="bar-fill" style="width:{{ $pctMemA }}%; background-color: #1E293B;"></div>
                        </div>
                    </td>
                    <td style="width: 20%; text-align: right; font-weight: bold; font-family: 'Courier New', Courier, monospace;">
                        {{ $pctMemA }}%
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; color: #4c4635; font-weight: bold;">Cab. B</td>
                    <td style="width: 55%; padding-right: 8px;">
                        <div class="bar-wrap">
                            <div class="bar-fill" style="width:{{ $pctMemB }}%; background-color: #dbb940;"></div>
                        </div>
                    </td>
                    <td style="width: 20%; text-align: right; font-weight: bold; font-family: 'Courier New', Courier, monospace;">
                        {{ $pctMemB }}%
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Proporsi Kontribusi Revenue</div>
            <table class="chart-table">
                <tr>
                    <td style="width: 25%; color: #4c4635; font-weight: bold;">Cab. A</td>
                    <td style="width: 55%; padding-right: 8px;">
                        <div class="bar-wrap">
                            <div class="bar-fill" style="width:{{ $pctRevA }}%; background-color: #00677d;"></div>
                        </div>
                    </td>
                    <td style="width: 20%; text-align: right; font-weight: bold; font-family: 'Courier New', Courier, monospace;">
                        {{ $pctRevA }}%
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; color: #4c4635; font-weight: bold;">Cab. B</td>
                    <td style="width: 55%; padding-right: 8px;">
                        <div class="bar-wrap">
                            <div class="bar-fill" style="width:{{ $pctRevB }}%; background-color: #dbb940;"></div>
                        </div>
                    </td>
                    <td style="width: 20%; text-align: right; font-weight: bold; font-family: 'Courier New', Courier, monospace;">
                        {{ $pctRevB }}%
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<h2 class="section-title">III. Analisis Program Teratas</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 50%;">{{ $branchAName }} (Top Program)</th>
            <th style="width: 50%;">{{ $branchBName }} (Top Program)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="vertical-align: top; padding: 0;">
                <table style="width: 100%; border: none; margin: 0;">
                    @forelse($data['data_a']['programs'] as $p)
                        <tr>
                            <td style="border: none; border-bottom: 1px solid #f3f4f3; padding: 8px;">{{ data_get($p, 'name') }}</td>
                            <td style="border: none; border-bottom: 1px solid #f3f4f3; text-align: right; font-family: 'Courier New'; font-weight: bold;">{{ data_get($p, 'cnt') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" style="border: none; text-align: center; color: #7e7663; padding: 10px;"><em>Tidak ada data</em></td></tr>
                    @endforelse
                </table>
            </td>
            <td style="vertical-align: top; padding: 0;">
                <table style="width: 100%; border: none; margin: 0;">
                    @forelse($data['data_b']['programs'] as $p)
                        <tr>
                            <td style="border: none; border-bottom: 1px solid #f3f4f3; padding: 8px;">{{ data_get($p, 'name') }}</td>
                            <td style="border: none; border-bottom: 1px solid #f3f4f3; text-align: right; font-family: 'Courier New'; font-weight: bold;">{{ data_get($p, 'cnt') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" style="border: none; text-align: center; color: #7e7663; padding: 10px;"><em>Tidak ada data</em></td></tr>
                    @endforelse
                </table>
            </td>
        </tr>
    </tbody>
</table>

<div class="doc-foot">
    <div class="foot-left">
        Dokumen ini diterbitkan secara otomatis oleh Sistem MINDSIA dan sah untuk keperluan analitik internal perusahaan.
    </div>
    <div class="foot-right">HAL. 1</div>
    <div class="clearfix"></div>
</div>

</body>
</html>
