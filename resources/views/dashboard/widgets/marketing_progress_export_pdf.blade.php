<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Progress Marketing</title>
    <style>
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 8.5pt; color: #1E293B; margin: -10px; }
        .header { text-align: center; margin-bottom: 1.5rem; border-bottom: 2px solid #1E293B; padding-bottom: 1rem; position: relative; }
        .logo { position: absolute; left: 0; top: -15px; width: 120px; }
        .title { font-size: 14pt; font-weight: bold; margin-bottom: 0.2rem; color: #1E293B; text-transform: uppercase; letter-spacing: 0.05em; }
        .subtitle { font-size: 9pt; color: #7e7663; }
        .section-title { font-size: 10.5pt; font-weight: bold; margin-top: 1.5rem; margin-bottom: 0.8rem; color: #1E293B; border-bottom: 1px solid #cfc6af; padding-bottom: 0.3rem; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; page-break-inside: auto; }
        table.data-table tr { page-break-inside: avoid; page-break-after: auto; }
        table.data-table th { background-color: #f9f9f8; font-size: 7.5pt; text-transform: uppercase; color: #7e7663; font-weight: bold; padding: 0.5rem; text-align: left; border-bottom: 1px solid #cfc6af; }
        table.data-table th.text-right { text-align: right; }
        table.data-table td { padding: 0.5rem; border-bottom: 1px solid #ebebeb; vertical-align: top; }
        table.data-table tbody tr:nth-child(even) { background-color: #fafafa; }
        .text-right { text-align: right; }
        .td-numeric { font-family: "Courier New", Courier, monospace; font-weight: bold; font-size: 9pt; }
        .td-omzet { font-family: "Courier New", Courier, monospace; font-weight: bold; font-size: 9pt; color: #00677d; }
        .td-target { font-family: "Courier New", Courier, monospace; font-size: 7.5pt; color: #7e7663; }
        .td-prospek { font-family: "Courier New", Courier, monospace; font-weight: bold; font-size: 9pt; color: #b8860b; }

        .grid-4 { display: table; width: 100%; margin-bottom: 1rem; }
        .grid-4 .grid-col { display: table-cell; width: 25%; padding-right: 0.5rem; vertical-align: top; }
        .grid-4 .grid-col:last-child { padding-right: 0; }

        .summary-box { background-color: #ffffff; border: 1px solid #cfc6af; border-top: 3px solid #dbb940; padding: 0.75rem; margin-bottom: 0.75rem; }
        .summary-label { font-size: 7.5pt; font-weight: bold; text-transform: uppercase; color: #7e7663; letter-spacing: 0.05em; margin-bottom: 0.3rem; }
        .summary-value { font-family: "Courier New", Courier, monospace; font-size: 13pt; font-weight: bold; margin-bottom: 0.2rem; color: #1a1c1c; }
        .summary-value.val-omzet { color: #00677d; }
        .summary-value.val-prospek { color: #b8860b; }

        .doc-foot { margin-top: 2rem; border-top: 1px solid #cfc6af; padding-top: 0.5rem; font-size: 7pt; color: #7e7663; }
        .foot-left { float: left; }
        .foot-right { float: right; font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    @if(!empty($logoSrc))
        <img src="{{ $logoSrc }}" alt="Logo" class="logo">
    @endif
    <div class="title">Laporan Progress Marketing</div>
    <div class="subtitle">Omzet, Sosialisasi, Member & Prospek • Periode {{ $data['period'] ?? '-' }}</div>
    <div style="font-size: 7.5pt; color: #7e7663; margin-top: 0.2rem; font-family:'Courier New', monospace;">DOC: {{ $docCode ?? '-' }}</div>
</div>

{{-- I. RINGKASAN NASIONAL --}}
<div class="grid-4">
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Total Omzet</div>
            <div class="summary-value val-omzet">Rp {{ number_format(($data['national']['omzet'] ?? 0) / 1000000, 2) }} Juta</div>
        </div>
    </div>
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Sosialisasi</div>
            <div class="summary-value">{{ number_format($data['national']['sosialisasi'] ?? 0) }}</div>
        </div>
    </div>
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Member Baru</div>
            <div class="summary-value">{{ number_format($data['national']['member'] ?? 0) }}</div>
        </div>
    </div>
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Prospek</div>
            <div class="summary-value val-prospek">{{ number_format($data['national']['prospective'] ?? 0) }}</div>
        </div>
    </div>
</div>

{{-- II. RANKING MARKETING (PER EMPLOYEE) --}}
@if(!empty($data['by_employee']))
<div class="section-title">Ranking Marketing</div>
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Cabang</th>
            <th class="text-right">Kelas</th>
            <th class="text-right">Omzet</th>
            <th class="text-right">Member</th>
            <th class="text-right">Prospek</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['by_employee'] as $i => $emp)
        <tr>
            <td class="td-numeric" style="color:#7e7663; width:20px;">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
            <td style="font-weight:bold;">{{ $emp['name'] }}</td>
            <td style="color:#7e7663; font-size:7.5pt;">{{ $emp['branch_name'] }}</td>
            <td class="text-right td-numeric">
                {{ $emp['kelas'] }}<span style="color:#7e7663; font-weight:normal;">/{{ $emp['kelas_target'] }}</span>
            </td>
            <td class="text-right">
                <div class="td-omzet">Rp {{ number_format($emp['omzet'] / 1000000, 2) }} Juta</div>
                <div class="td-target">Target: Rp {{ number_format($emp['omzet_target'] / 1000000, 2) }} Juta</div>
            </td>
            <td class="text-right td-numeric">{{ number_format($emp['member']) }}</td>
            <td class="text-right td-prospek">{{ number_format($emp['prospective']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- III. RINGKASAN PER AREA --}}
@if(!empty($data['by_area']))
<div class="section-title">Ringkasan per Area</div>
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Area</th>
            <th class="text-right">Omzet</th>
            <th class="text-right">Sosialisasi</th>
            <th class="text-right">Member Baru</th>
            <th class="text-right">Prospek</th>
            <th class="text-right">Cabang</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['by_area'] as $i => $area)
        <tr>
            <td class="td-numeric" style="color:#7e7663; width:20px;">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
            <td style="font-weight:bold;">{{ $area['name'] }}</td>
            <td class="text-right td-omzet">Rp {{ number_format($area['omzet'] / 1000000, 2) }} Juta</td>
            <td class="text-right td-numeric">{{ number_format($area['sosialisasi']) }}</td>
            <td class="text-right td-numeric">{{ number_format($area['member']) }}</td>
            <td class="text-right td-prospek">{{ number_format($area['prospective']) }}</td>
            <td class="text-right" style="color:#7e7663;">{{ $area['branch_count'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- IV. RINCIAN PER CABANG --}}
@if(!empty($data['by_branch']))
<div class="section-title">Rincian per Cabang</div>
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Cabang</th>
            <th>Area</th>
            <th class="text-right">Omzet</th>
            <th class="text-right">Sosialisasi</th>
            <th class="text-right">Member Baru</th>
            <th class="text-right">Prospek</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['by_branch'] as $i => $branch)
        <tr>
            <td class="td-numeric" style="color:#7e7663; width:20px;">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
            <td style="font-weight:bold;">{{ $branch['name'] }}</td>
            <td style="color:#7e7663; font-size:7.5pt;">{{ $branch['area_name'] }}</td>
            <td class="text-right td-omzet">Rp {{ number_format($branch['omzet'] / 1000000, 2) }} Juta</td>
            <td class="text-right td-numeric">{{ number_format($branch['sosialisasi']) }}</td>
            <td class="text-right td-numeric">{{ number_format($branch['member']) }}</td>
            <td class="text-right td-prospek">{{ number_format($branch['prospective']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="doc-foot">
    <div class="foot-left">Dicetak: {{ $printedAt ?? now()->format('d M Y, H:i') }} WIB</div>
    <div class="foot-right">MINDSIA v2 — Laporan Progress Marketing</div>
</div>

</body>
</html>
