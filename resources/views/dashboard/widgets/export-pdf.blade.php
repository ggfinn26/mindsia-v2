<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #333; margin: 20px; }
        h1 { font-size: 14pt; margin-bottom: 4px; color: #111; }
        .meta { font-size: 8pt; color: #888; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f5f5f5; text-align: left; padding: 6px 8px; font-size: 9pt; border-bottom: 2px solid #ddd; }
        td { padding: 5px 8px; font-size: 9pt; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #fafafa; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="meta">Diekspor pada: {{ $exportedAt }}</p>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($headers as $header)
                        <td>{{ $row[$header] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
