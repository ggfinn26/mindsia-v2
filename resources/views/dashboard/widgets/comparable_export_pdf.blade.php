<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Komparasi Cabang</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #1E293B; line-height: 1.4; margin: 0; padding: 20px; }
        h2 { font-size: 16pt; margin-bottom: 5px; color: #1E293B; text-transform: uppercase; letter-spacing: 1px; }
        .meta { color: #555; font-size: 9pt; margin-bottom: 30px; border-bottom: 2px solid #dbb940; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; padding: 10px; border-bottom: 1px solid #1E293B; font-weight: bold; font-size: 9pt; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #cfc6af; font-size: 10pt; }
        .row-striped { background-color: #f9f9f8; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .footer { margin-top: 50px; font-size: 8pt; color: #777; text-align: center; border-top: 1px solid #cfc6af; padding-top: 10px; }
    </style>
</head>
<body>
    <h2>Laporan Komparasi Cabang</h2>
    <div class="meta">
        Periode: {{ $data['period_label'] }}<br>
        Dicetak oleh: {{ Auth::user()->name ?? 'System' }} pada {{ now()->format('d M Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Metrik</th>
                <th class="text-right" style="width: 30%;">{{ $data['branch_a']['branch_name'] ?? 'Cabang A' }}</th>
                <th class="text-right" style="width: 30%;">{{ $data['branch_b']['branch_name'] ?? 'Cabang B' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Total Member Baru</strong></td>
                <td class="text-right font-mono">{{ number_format($data['data_a']['members']) }}</td>
                <td class="text-right font-mono">{{ number_format($data['data_b']['members']) }}</td>
            </tr>
            <tr class="row-striped">
                <td><strong>Total Revenue</strong></td>
                <td class="text-right font-mono">Rp {{ number_format($data['data_a']['revenue'], 0, ',', '.') }}</td>
                <td class="text-right font-mono">Rp {{ number_format($data['data_b']['revenue'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top: 20px; border-bottom: none;">
                    <strong>Program Teratas (Berdasarkan Jumlah Member)</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 0; border: none;">
                    <table style="margin: 0; border: none;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; border-right: 1px solid #cfc6af;">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach($data['data_a']['programs'] as $p)
                                        <li>{{ $p->name }} ({{ $p->cnt }})</li>
                                    @endforeach
                                    @if(empty($data['data_a']['programs']))
                                        <li><em>Tidak ada data</em></li>
                                    @endif
                                </ul>
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach($data['data_b']['programs'] as $p)
                                        <li>{{ $p->name }} ({{ $p->cnt }})</li>
                                    @endforeach
                                    @if(empty($data['data_b']['programs']))
                                        <li><em>Tidak ada data</em></li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem MINDSIA.
    </div>
</body>
</html>
