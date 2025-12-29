<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:6px; font-size:12px; }
        th { background:#eee; }
    </style>
</head>
<body>

<h3>Riwayat Transaksi</h3>
<p>
    Filter:
    {{ $filterLabel ?? 'Semua' }}
    <br>
    Tanggal:
    {{ $dateFrom ?? '-' }} s/d {{ $dateTo ?? '-' }}
</p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Jenis</th>
            <th>Nomor</th>
            <th>Pelanggan</th>
            <th>Tanggal</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($riwayat as $i => $row)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $row['type'] }}</td>
            <td>{{ $row['nomor'] }}</td>
            <td>{{ $row['customer'] }}</td>
            <td>{{ $row['tanggal'] }}</td>
            <td>Rp {{ number_format($row['grand_total'],0,',','.') }}</td>
            <td>{{ $row['status'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
