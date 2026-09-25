<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Komparasi Finansial MINDSIA — {{ $data['period'] ?? '-' }}</title>
    <style>
        /* Sovereign Executive Design System for PDF */
        @page {
            size: A4 portrait;
            margin: 2.5cm 2cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #1a1c1c;
            background-color: #f9f9f8;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        h1, h2, h3, h4 {
            font-family: "Times New Roman", Times, serif;
            margin-top: 0;
            margin-bottom: 0.5rem;
            color: #1a1c1c;
            font-weight: bold;
        }

        /* ── Header / Kop ── */
        .header-table {
            width: 100%;
            border-bottom: 2pt solid #1E293B;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .logo-img {
            height: 65px;
            width: auto;
            display: block;
        }
        .logo-fallback {
            display: inline-block;
            background: #1E293B;
            color: #dbb940;
            font-weight: bold;
            font-size: 18pt;
            padding: 8px 16px;
            font-family: "Times New Roman", Times, serif;
        }

        .header-title-box {
            text-align: center;
        }
        .header-title-box h1 {
            font-size: 18pt;
            margin-bottom: 0.2rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .header-title-box .subtitle {
            font-size: 10pt;
            font-style: italic;
            color: #4c4635;
            font-family: "Times New Roman", Times, serif;
        }

        .doc-meta {
            text-align: right;
            font-size: 7.5pt;
            color: #4c4635;
        }
        
        .badge-conf {
            display: inline-block;
            background: #1E293B;
            color: #f9f9f8;
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 0.05em;
            padding: 2px 6px;
            margin-bottom: 4px;
        }

        /* ── Section Titles ── */
        .section-title {
            font-size: 12pt;
            text-transform: uppercase;
            border-bottom: 1px solid #cfc6af;
            padding-bottom: 0.2rem;
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
            color: #1E293B;
            letter-spacing: 0.02em;
        }

        /* ── Grid Layouts ── */
        .grid-2 {
            display: table;
            width: 100%;
            margin-bottom: 1rem;
        }
        .grid-col {
            display: table-cell;
            width: 50%;
            padding-right: 0.75rem;
        }
        .grid-col:last-child {
            padding-right: 0;
            padding-left: 0.75rem;
        }

        /* ── Summary Boxes (Cards) ── */
        .summary-box {
            background-color: #ffffff;
            border: 1px solid #cfc6af;
            border-top: 3px solid #dbb940;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .summary-label {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #7e7663;
            letter-spacing: 0.05em;
            margin-bottom: 0.3rem;
        }

        .summary-value {
            font-family: "Courier New", Courier, monospace;
            font-size: 15pt;
            font-weight: bold;
            margin-bottom: 0.3rem;
            color: #1a1c1c;
            letter-spacing: -0.02em;
        }

        .summary-detail {
            font-size: 8pt;
            color: #4c4635;
        }

        .val-positive { color: #00677d; font-weight: bold; }
        .val-negative { color: #ba1a1a; font-weight: bold; }

        /* ── Tables ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            font-size: 8pt;
        }

        table.data-table th, table.data-table td {
            border: none;
            border-bottom: 1px solid #e2e2e2;
            padding: 0.4rem 0.5rem;
            text-align: left;
        }

        table.data-table th {
            background-color: #f3f4f3;
            font-weight: bold;
            color: #4c4635;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-top: 1px solid #cfc6af;
            border-bottom: 1px solid #cfc6af;
        }
        
        table.data-table tbody tr.area-header td {
            background-color: #f9f9f8;
            color: #725c00;
            font-weight: bold;
            font-size: 7.5pt;
            text-transform: uppercase;
            border-left: 2px solid #dbb940;
        }
        
        table.data-table tbody tr:nth-child(even):not(.area-header) {
            background-color: #ffffff;
        }
        table.data-table tbody tr:nth-child(odd):not(.area-header) {
            background-color: #f9f9f8;
        }
        
        /* Monospace for numbers in tables */
        .td-numeric {
            font-family: "Courier New", Courier, monospace;
            font-weight: bold;
            font-size: 8.5pt;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .note {
            font-size: 7.5pt;
            font-style: italic;
            color: #7e7663;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }

        /* ── Chart Styles ── */
        .chart-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }
        .chart-table td {
            padding: 4px 0;
            border: none;
            border-bottom: 1px solid #f3f4f3;
        }
        .bar-wrap {
            background-color: #e2e2e2;
            height: 10px;
            width: 100%;
        }
        .bar-fill {
            height: 10px;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 6.5pt;
            font-weight: bold;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .badge-top { background-color: #ffe07e; color: #564500; }
        .badge-track { background-color: #b3ebff; color: #004e5f; }
        .badge-base { background-color: #eeeeed; color: #4c4635; }
        .badge-attn { background-color: #ffdad6; color: #93000a; }

        /* ── Footer ── */
        .doc-foot {
            margin-top: 2rem;
            font-size: 7.5pt;
            border-top: 1px solid #cfc6af;
            padding-top: 0.5rem;
            color: #7e7663;
        }
        .foot-left { float: left; }
        .foot-right { float: right; font-weight: bold; color: #1E293B; }
        .clearfix::after { content: ""; clear: both; display: table; }
        
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

@php
    $branches  = $data['branches'] ?? [];
    $areas     = $data['area_contributions'] ?? [];
    $maxRev    = $data['max_revenue'] ?? 1;
    $avgRev    = $data['avg_branch_revenue'] ?? 0;
    $nt        = $data['national_trend'] ?? 0;
    $pt        = $data['profit_trend'] ?? 0;
    $byArea    = [];
    foreach ($branches as $b) { $byArea[$b['area_name']][] = $b; }
    $rowNum = 1;
    
    $statusMap = [
        'Top Tier'       => ['label' => 'TOP TIER',  'class' => 'badge-top'],
        'On Track'       => ['label' => 'ON TRACK',  'class' => 'badge-track'],
        'Baseline'       => ['label' => 'BASELINE',  'class' => 'badge-base'],
        'Need Attention' => ['label' => 'ATTENTION', 'class' => 'badge-attn'],
    ];
@endphp

<table class="header-table">
    <tr>
        <td style="width: 20%;">
            @if (!empty($logoSrc))
                <img src="{{ $logoSrc }}" class="logo-img" alt="MINDSIA">
            @else
                <span class="logo-fallback">M</span>
            @endif
        </td>
        <td style="width: 60%;" class="header-title-box">
            <h1>Laporan Komparasi Kinerja Finansial</h1>
            <div class="subtitle">Periode: {{ $data['period'] ?? '-' }}</div>
        </td>
        <td style="width: 20%;" class="doc-meta">
            <div><span class="badge-conf">DOKUMEN RESMI</span></div>
            <div>Kode: <strong>{{ $docCode }}</strong></div>
            <div style="margin-top: 2px;">Cetak: {{ $printedAt }}</div>
        </td>
    </tr>
</table>

<h2 class="section-title">I. Ringkasan Eksekutif Nasional</h2>
<div class="grid-2">
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Total Revenue Nasional</div>
            <div class="summary-value">Rp {{ number_format($data['national_revenue'] ?? 0, 0, ',', '.') }}</div>
            <div class="summary-detail">
                <span class="{{ $nt >= 0 ? 'val-positive' : 'val-negative' }}">
                    {{ $nt >= 0 ? '+' : '' }}{{ $nt }}%
                </span> MoM
            </div>
        </div>
    </div>
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Total Net Profit Nasional</div>
            <div class="summary-value val-positive">Rp {{ number_format($data['national_profit'] ?? 0, 0, ',', '.') }}</div>
            <div class="summary-detail">
                Margin: <strong>{{ $data['national_margin'] ?? 0 }}%</strong> | 
                <span class="{{ $pt >= 0 ? 'val-positive' : 'val-negative' }}">
                    {{ $pt >= 0 ? '+' : '' }}{{ $pt }}%
                </span> MoM
            </div>
        </div>
    </div>
</div>

<h2 class="section-title">II. Benchmark & Jangkauan Operasional</h2>
<table class="data-table">
    <tr>
        <th style="width: 40%;">Indikator Strategis</th>
        <th>Nilai / Keterangan Kinerja</th>
    </tr>
    <tr>
        <td><strong>Jangkauan Wilayah Aktif</strong></td>
        <td><strong>{{ count($areas) }}</strong> Area Regional mencakup <strong>{{ $data['branch_count'] ?? 0 }}</strong> Cabang</td>
    </tr>
    <tr>
        <td><strong>Distribusi Status Evaluasi</strong></td>
        <td><strong>{{ $data['superior_count'] ?? 0 }}</strong> Superior, <strong>{{ $data['evaluasi_count'] ?? 0 }}</strong> Evaluasi</td>
    </tr>
</table>

<h2 class="section-title">III. Audit Komparasi Kinerja Seluruh Cabang</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th>Entitas Cabang</th>
            <th>Area Operasi</th>
            <th class="text-right">Realisasi Omzet</th>
            <th class="text-right">Laba Bersih</th>
            <th class="text-center">Margin</th>
            <th class="text-right">Deviasi Rerata</th>
            <th class="text-center">Klasifikasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($byArea as $areaName => $areaBranches)
            <tr class="area-header">
                <td colspan="8">{{ $areaName ?: 'Area Tidak Dikategorikan' }} ({{ count($areaBranches) }} Cabang)</td>
            </tr>
            @foreach ($areaBranches as $i => $branch)
                @php
                    $isPos = $branch['vs_national'] >= 0;
                    $profPos = $branch['profit'] >= 0;
                    $sc = $statusMap[$branch['status']] ?? ['label' => $branch['status'], 'class' => 'badge-base'];
                @endphp
                <tr>
                    <td class="text-center" style="color: #7e7663; font-family: 'Courier New', Courier, monospace;">{{ $rowNum++ }}</td>
                    <td><strong>{{ $branch['name'] }}</strong></td>
                    <td style="color: #4c4635;">{{ $branch['area_name'] }}</td>
                    <td class="text-right td-numeric">Rp {{ number_format($branch['revenue'], 0, ',', '.') }}</td>
                    <td class="text-right td-numeric {{ $profPos ? 'val-positive' : 'val-negative' }}">Rp {{ number_format($branch['profit'], 0, ',', '.') }}</td>
                    <td class="text-center td-numeric">{{ $branch['margin'] }}%</td>
                    <td class="text-right td-numeric {{ $isPos ? 'val-positive' : 'val-negative' }}">{{ $isPos ? '+' : '' }}{{ $branch['vs_national'] }}%</td>
                    <td class="text-center"><span class="badge {{ $sc['class'] }}">{{ $sc['label'] }}</span></td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
<div class="note">*Data dihitung berdasarkan laporan kas periode {{ $data['period'] ?? '-' }}.</div>

@php $chartBranches = array_slice($branches, 0, 6); @endphp
@if (!empty($chartBranches) || !empty($areas))
<h2 class="section-title">IV. Visualisasi & Distribusi Performa</h2>
<div class="grid-2">
    <!-- Bar Chart -->
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Top Komparasi Omzet vs Baseline Nasional</div>
            <div style="font-size: 7.5pt; color: #7e7663; margin-bottom: 0.8rem;">
                Baseline: Rp {{ number_format($avgRev / 1000000, 2, ',', '.') }} Juta
            </div>
            <table class="chart-table">
                @foreach ($chartBranches as $branch)
                    @php
                        $pct     = $maxRev > 0 ? max(1, round(($branch['revenue'] / $maxRev) * 100)) : 1;
                        $above   = $branch['revenue'] >= $avgRev;
                        $barCol  = $above ? '#1E293B' : '#cfc6af'; /* Deep Slate for above, muted gold outline var for below */
                        $devPct  = $branch['vs_national'];
                        $devCol  = $devPct >= 0 ? '#00677d' : '#ba1a1a'; /* Teal / Error */
                    @endphp
                    <tr>
                        <td style="width: 35%; color: #4c4635; font-weight: bold;">{{ \Illuminate\Support\Str::limit($branch['name'], 15) }}</td>
                        <td style="width: 35%; padding-right: 8px;">
                            <div class="bar-wrap">
                                <div class="bar-fill" style="width:{{ $pct }}%; background-color:{{ $barCol }};"></div>
                            </div>
                        </td>
                        <td style="width: 15%; text-align: right; font-weight: bold; color: #1a1c1c; font-family: 'Courier New', Courier, monospace;">
                            Rp {{ number_format($branch['revenue'] / 1000000, 1, ',', '.') }}Jt
                        </td>
                        <td style="width: 15%; text-align: right; color: {{ $devCol }}; font-weight: bold; font-family: 'Courier New', Courier, monospace;">
                            {{ $devPct >= 0 ? '+' : '' }}{{ $devPct }}%
                        </td>
                    </tr>
                @endforeach
            </table>
            <div style="font-size: 7pt; color: #7e7663; margin-top: 0.5rem; text-align: right; font-style: italic;">
                *Menampilkan 6 cabang teratas
            </div>
        </div>
    </div>

    <!-- Area Distribution -->
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Distribusi Pangsa Area (100% Realisasi)</div>
            <div style="font-size: 7.5pt; color: #7e7663; margin-bottom: 0.8rem;">
                Total Konsolidasi: Rp {{ number_format($data['national_revenue'] ?? 0 / 1000000, 2, ',', '.') }} Juta
            </div>
            @if (!empty($areas))
                <table class="chart-table">
                    @foreach ($areas as $area)
                        <tr>
                            <td style="width: 5%; padding-right: 4px;">
                                <div style="width: 8px; height: 8px; background-color: {{ $area['color'] }};"></div>
                            </td>
                            <td style="width: 25%; font-weight: bold; color: #1a1c1c;">{{ $area['name'] }}</td>
                            <td style="width: 40%; padding-right: 8px;">
                                <div class="bar-wrap">
                                    <div class="bar-fill" style="width:{{ $area['percentage'] }}%; background-color:{{ $area['color'] }};"></div>
                                </div>
                            </td>
                            <td style="width: 10%; text-align: right; font-weight: bold; color: #1E293B; font-family: 'Courier New', Courier, monospace;">
                                {{ $area['percentage'] }}%
                            </td>
                            <td style="width: 20%; text-align: right; color: #7e7663; font-family: 'Courier New', Courier, monospace;">
                                ({{ number_format($area['revenue'] / 1000000, 1, ',', '.') }}Jt)
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div style="font-size: 7pt; color: #4c4635; margin-top: 0.5rem; text-align: center; border-left: 2px solid #dbb940; padding-left: 4px;">
                    Agregasi dari {{ $data['branch_count'] ?? 0 }} unit cabang aktif
                </div>
            @else
                <p style="font-size: 8pt; color: #7e7663; text-align: center;">Data area tidak tersedia.</p>
            @endif
        </div>
    </div>
</div>
@endif

<div class="doc-foot">
    <div class="foot-left">
        Dokumen ini diterbitkan secara digital oleh Sistem MINDSIA dan sah untuk keperluan internal perusahaan.
    </div>
    <div class="foot-right">HAL. 1</div>
    <div class="clearfix"></div>
</div>

</body>
</html>
