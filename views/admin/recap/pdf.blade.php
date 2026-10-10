<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Fasilitas — Reservus</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
            color: #1f2937;
            margin: 18px;
            background: #ffffff;
        }
        .header {
            border-bottom: 2px solid #0047AB;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        h1 {
            font-size: 16px;
            margin: 0 0 4px;
            color: #00337A;
        }
        .meta {
            color: #4b5563;
            font-size: 9px;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 5px 6px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background: #e8eff9;
            color: #00337A;
            font-weight: 700;
        }
        .num {
            text-align: right;
            white-space: nowrap;
        }
        .total td {
            background: #f3f4f6;
            font-weight: 700;
        }
        .muted {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Fasilitas — {{ $filter['group_by'] === 'location' ? 'per Lokasi' : 'per Fasilitas' }}</h1>
        <div class="meta">
            Periode {{ $filter['from'] }} s.d. {{ $filter['to'] }} ({{ $rekap['jumlah_hari'] }} hari)
            · Dicetak {{ \Illuminate\Support\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
        </div>
    </div>
    <table>
        <thead>
            <tr>
                @foreach ($rekap['header'] as $kolom)
                    <th>{{ $kolom }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rekap['baris'] as $baris)
                <tr>
                    <td>{{ $baris['kelompok'] }}</td>
                    <td>{{ $baris['lokasi'] }}</td>
                    <td class="num">{{ $baris['reservasi'] }}</td>
                    <td class="num">{{ $baris['jam'] }}</td>
                    <td class="num">{{ $baris['persen'] }}</td>
                    <td class="num">{{ $baris['laporan'] }}</td>
                    @foreach (array_keys(\App\Models\Report::CATEGORIES) as $kat)
                        <td class="num">{{ $baris['k_'.$kat] }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr class="total">
                <td>{{ $rekap['totals']['kelompok'] }}</td>
                <td>{{ $rekap['totals']['lokasi'] }}</td>
                <td class="num">{{ $rekap['totals']['reservasi'] }}</td>
                <td class="num">{{ $rekap['totals']['jam'] }}</td>
                <td class="num">{{ $rekap['totals']['persen'] }}</td>
                <td class="num">{{ $rekap['totals']['laporan'] }}</td>
                @foreach (array_keys(\App\Models\Report::CATEGORIES) as $kat)
                    <td class="num">{{ $rekap['totals']['k_'.$kat] }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</body>
</html>