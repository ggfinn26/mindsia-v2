<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Analitik Member & Program MINDSIA — {{ $data['period'] ?? '-' }}</title>
    <style>
        @page { size: A4 portrait; margin: 2.5cm 2cm; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #1a1c1c;
            background-color: #f9f9f8;
            margin: 0; padding: 0; line-height: 1.5;
        }
        h1, h2, h3, h4 {
            font-family: "Times New Roman", Times, serif;
            margin-top: 0; margin-bottom: 0.5rem;
            color: #1a1c1c; font-weight: bold;
        }

        /* Header / Kop */
        .header-table { width: 100%; border-bottom: 2pt solid #1E293B; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .logo-img { height: 65px; width: auto; display: block; }
        .logo-fallback { display: inline-block; background: #1E293B; color: #dbb940; font-weight: bold; font-size: 18pt; padding: 8px 16px; font-family: "Times New Roman", Times, serif; }
        .header-title-box { text-align: center; }
        .header-title-box h1 { font-size: 18pt; margin-bottom: 0.2rem; text-transform: uppercase; letter-spacing: 0.02em; }
        .header-title-box .subtitle { font-size: 10pt; font-style: italic; color: #4c4635; font-family: "Times New Roman", Times, serif; }
        .doc-meta { text-align: right; font-size: 7.5pt; color: #4c4635; }
        .badge-conf { display: inline-block; background: #1E293B; color: #f9f9f8; font-size: 7pt; font-weight: bold; letter-spacing: 0.05em; padding: 2px 6px; margin-bottom: 4px; }

        /* Section Titles */
        .section-title { font-size: 12pt; text-transform: uppercase; border-bottom: 1px solid #cfc6af; padding-bottom: 0.2rem; margin-top: 1.5rem; margin-bottom: 0.8rem; color: #1E293B; letter-spacing: 0.02em; }

        /* Grid */
        .grid-4 { display: table; width: 100%; margin-bottom: 1rem; }
        .grid-col-4 { display: table-cell; width: 25%; padding-right: 0.5rem; }
        .grid-col-4:last-child { padding-right: 0; }
        .grid-2 { display: table; width: 100%; margin-bottom: 1rem; }
        .grid-col { display: table-cell; width: 50%; padding-right: 0.75rem; }
        .grid-col:last-child { padding-right: 0; padding-left: 0.75rem; }

        /* Summary Boxes */
        .summary-box { background-color: #ffffff; border: 1px solid #cfc6af; border-top: 3px solid #dbb940; padding: 0.75rem; margin-bottom: 0.75rem; }
        .summary-label { font-size: 7.5pt; font-weight: bold; text-transform: uppercase; color: #7e7663; letter-spacing: 0.05em; margin-bottom: 0.3rem; }
        .summary-value { font-family: "Courier New", Courier, monospace; font-size: 15pt; font-weight: bold; margin-bottom: 0.2rem; color: #1a1c1c; }
        .summary-detail { font-size: 8pt; color: #4c4635; }
        .val-positive { color: #00677d; font-weight: bold; }
        .val-negative { color: #ba1a1a; font-weight: bold; }

        /* Tables */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; font-size: 8pt; }
        table.data-table th, table.data-table td { border: none; border-bottom: 1px solid #e2e2e2; padding: 0.4rem 0.5rem; text-align: left; }
        table.data-table th { background-color: #f3f4f3; font-weight: bold; color: #4c4635; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.05em; border-top: 1px solid #cfc6af; border-bottom: 1px solid #cfc6af; }
        table.data-table tbody tr:nth-child(even) { background-color: #ffffff; }
        table.data-table tbody tr:nth-child(odd) { background-color: #f9f9f8; }
        .td-numeric { font-family: "Courier New", Courier, monospace; font-weight: bold; font-size: 8.5pt; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .note { font-size: 7.5pt; font-style: italic; color: #7e7663; margin-top: -0.5rem; margin-bottom: 1rem; }

        /* Bar chart */
        .chart-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
        .chart-table td { padding: 4px 0; border: none; border-bottom: 1px solid #f3f4f3; }
        .bar-wrap { background-color: #e2e2e2; height: 8px; width: 100%; }
        .bar-fill { height: 8px; }

        /* Badges */
        .badge { display: inline-block; padding: 2px 6px; font-size: 6.5pt; font-weight: bold; border-radius: 2px; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-rank1 { background-color: #ffe07e; color: #564500; }
        .badge-rank2 { background-color: #e2e2e2; color: #4c4635; }
        .badge-rank3 { background-color: #f5ddb0; color: #6b4400; }

        /* Footer */
        .doc-foot { margin-top: 2rem; font-size: 7.5pt; border-top: 1px solid #cfc6af; padding-top: 0.5rem; color: #7e7663; }
        .foot-left { float: left; }
        .foot-right { float: right; font-weight: bold; color: #1E293B; }
        .clearfix::after { content: ""; clear: both; display: table; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

@php
    $nat      = $data['national'] ?? [];
    $byArea   = $data['by_area'] ?? [];
    $byBranch = $data['by_branch'] ?? [];
    $byProg   = $data['by_program'] ?? [];
    $maxBranch = max(array_column($byBranch, 'this_month') ?: [1]);
    $maxProg   = max(array_column($byProg, 'member_count') ?: [1]);
@endphp

{{-- KOP --}}
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
            <h1>Laporan Analitik Member & Program</h1>
            <div class="subtitle">Periode: {{ $data['period'] ?? '-' }}</div>
        </td>
        <td style="width: 20%;" class="doc-meta">
            <div><span class="badge-conf">DOKUMEN RESMI</span></div>
            <div>Kode: <strong>{{ $docCode }}</strong></div>
            <div style="margin-top: 2px;">Cetak: {{ $printedAt }}</div>
        </td>
    </tr>
</table>

{{-- I. RINGKASAN NASIONAL --}}
<h2 class="section-title">I. Ringkasan Nasional</h2>
<div class="grid-4">
    <div class="grid-col-4">
        <div class="summary-box">
            <div class="summary-label">Member Bulan Ini</div>
            <div class="summary-value">{{ number_format($nat['this_month'] ?? 0) }}</div>
            <div class="summary-detail">Kumulatif: <strong>{{ number_format($nat['cumulative'] ?? 0) }}</strong></div>
        </div>
    </div>
    <div class="grid-col-4">
        <div class="summary-box">
            <div class="summary-label">vs Bulan Lalu (MoM)</div>
            @php $mom = $nat['mom_pct'] ?? 0; @endphp
            <div class="summary-value {{ $mom >= 0 ? 'val-positive' : 'val-negative' }}">{{ $mom >= 0 ? '+' : '' }}{{ $mom }}%</div>
            <div class="summary-detail">Bln lalu: <strong>{{ number_format($nat['last_month'] ?? 0) }}</strong> member</div>
        </div>
    </div>
    <div class="grid-col-4">
        <div class="summary-box">
            <div class="summary-label">vs Tahun Lalu (YoY)</div>
            @php $yoy = $nat['yoy_pct'] ?? 0; @endphp
            <div class="summary-value {{ $yoy >= 0 ? 'val-positive' : 'val-negative' }}">{{ $yoy >= 0 ? '+' : '' }}{{ $yoy }}%</div>
            <div class="summary-detail">Thn lalu: <strong>{{ number_format($nat['yoy'] ?? 0) }}</strong> member</div>
        </div>
    </div>
    <div class="grid-col-4">
        <div class="summary-box">
            <div class="summary-label">Jumlah Area</div>
            <div class="summary-value">{{ count($byArea) }}</div>
            <div class="summary-detail">Mencakup <strong>{{ count($byBranch) }}</strong> cabang aktif</div>
        </div>
    </div>
</div>

{{-- II. PER PROGRAM --}}
<h2 class="section-title">II. Ranking Program — {{ $data['period'] ?? '-' }}</h2>
<div class="grid-2">
    <div class="grid-col">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Program</th>
                    <th class="text-right">Member</th>
                    <th class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($byProg as $i => $prog)
                    <tr>
                        <td class="text-center">
                            @if ($i === 0)
                                <span class="badge badge-rank1">1</span>
                            @elseif ($i === 1)
                                <span class="badge badge-rank2">2</span>
                            @elseif ($i === 2)
                                <span class="badge badge-rank3">3</span>
                            @else
                                <span style="color:#7e7663; font-size:7.5pt;">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td>{{ $prog['program'] }}</td>
                        <td class="text-right td-numeric">{{ number_format($prog['member_count']) }}</td>
                        <td class="text-right td-numeric">{{ $prog['revenue'] > 0 ? 'Rp '.number_format($prog['revenue'] / 1000000, 1).'Jt' : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center" style="color:#7e7663;">Tidak ada data program.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="grid-col">
        <div class="summary-box" style="padding: 0.6rem;">
            <div class="summary-label" style="margin-bottom: 0.5rem;">Distribusi Member per Program</div>
            <table class="chart-table">
                @foreach (array_slice($byProg, 0, 8) as $prog)
                    @php $pct = $maxProg > 0 ? max(2, round(($prog['member_count'] / $maxProg) * 100)) : 2; @endphp
                    <tr>
                        <td style="width: 40%; color: #4c4635; font-weight: bold; padding-right: 4px;">{{ \Illuminate\Support\Str::limit($prog['program'], 18) }}</td>
                        <td style="width: 40%; padding-right: 6px;">
                            <div class="bar-wrap"><div class="bar-fill" style="width:{{ $pct }}%; background-color:#1E293B;"></div></div>
                        </td>
                        <td style="width: 20%; text-align: right; font-family: 'Courier New', Courier, monospace; font-weight: bold; color: #1a1c1c;">{{ $prog['member_count'] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

{{-- III. PER AREA --}}
<h2 class="section-title">III. Analitik per Area</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th>Area</th>
            <th style="width: 8%;" class="text-center">Cabang</th>
            <th class="text-right">Member Bln Ini</th>
            <th class="text-center">MoM</th>
            <th class="text-center">YoY</th>
            <th class="text-right">Revenue</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($byArea as $i => $area)
            <tr>
                <td class="text-center" style="color:#7e7663; font-family:'Courier New',monospace;">{{ $i + 1 }}</td>
                <td><strong>{{ $area['name'] }}</strong></td>
                <td class="text-center td-numeric">{{ $area['branch_count'] }}</td>
                <td class="text-right td-numeric">{{ number_format($area['this_month']) }}</td>
                <td class="text-center td-numeric {{ ($area['mom_pct'] ?? 0) >= 0 ? 'val-positive' : 'val-negative' }}">
                    {{ ($area['mom_pct'] ?? 0) >= 0 ? '+' : '' }}{{ $area['mom_pct'] ?? 0 }}%
                </td>
                <td class="text-center td-numeric {{ ($area['yoy_pct'] ?? 0) >= 0 ? 'val-positive' : 'val-negative' }}">
                    {{ ($area['yoy_pct'] ?? 0) >= 0 ? '+' : '' }}{{ $area['yoy_pct'] ?? 0 }}%
                </td>
                <td class="text-right td-numeric">{{ $area['revenue'] > 0 ? 'Rp '.number_format($area['revenue'] / 1000000, 1).'Jt' : '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center" style="color:#7e7663;">Tidak ada data area.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- IV. PER CABANG --}}
<h2 class="section-title">IV. Analitik per Cabang</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;">No</th>
            <th>Cabang</th>
            <th>Area</th>
            <th class="text-right">Member Bln Ini</th>
            <th class="text-center">MoM</th>
            <th class="text-center">YoY</th>
            <th class="text-right">Revenue</th>
            <th style="width: 20%;">Volume</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($byBranch as $i => $branch)
            @php
                $pct = $maxBranch > 0 ? max(2, round(($branch['this_month'] / $maxBranch) * 100)) : 2;
                $momPos = ($branch['mom_pct'] ?? 0) >= 0;
                $yoyPos = ($branch['yoy_pct'] ?? 0) >= 0;
            @endphp
            <tr>
                <td class="text-center" style="color:#7e7663; font-family:'Courier New',monospace;">{{ $i + 1 }}</td>
                <td><strong>{{ $branch['name'] }}</strong></td>
                <td style="color:#4c4635;">{{ $branch['area_name'] }}</td>
                <td class="text-right td-numeric">{{ number_format($branch['this_month']) }}</td>
                <td class="text-center td-numeric {{ $momPos ? 'val-positive' : 'val-negative' }}">{{ $momPos ? '+' : '' }}{{ $branch['mom_pct'] }}%</td>
                <td class="text-center td-numeric {{ $yoyPos ? 'val-positive' : 'val-negative' }}">{{ $yoyPos ? '+' : '' }}{{ $branch['yoy_pct'] }}%</td>
                <td class="text-right td-numeric">{{ $branch['revenue'] > 0 ? 'Rp '.number_format($branch['revenue'] / 1000000, 1).'Jt' : '-' }}</td>
                <td style="padding-right: 4px;">
                    <div class="bar-wrap" style="height: 7px;">
                        <div class="bar-fill" style="width:{{ $pct }}%; background-color:#1E293B; height:7px;"></div>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center" style="color:#7e7663;">Tidak ada data cabang.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="note">*Member dihitung berdasarkan registrasi baru periode {{ $data['period'] ?? '-' }}. Revenue berdasarkan pembayaran lunas periode yang sama.</div>

{{-- FOOTER --}}
<div class="doc-foot">
    <div class="foot-left">Dokumen ini diterbitkan secara digital oleh Sistem MINDSIA dan sah untuk keperluan internal perusahaan.</div>
    <div class="foot-right">HAL. 1</div>
    <div class="clearfix"></div>
</div>

</body>
</html>
