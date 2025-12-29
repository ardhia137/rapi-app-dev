<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>Laporan Batch Produk</h2>
<p>
    Filter: <b>{{ ucfirst(str_replace('_', ' ', $filter)) }}</b><br>
    @if($start && $end)
        Periode: <b>{{ $start }} s/d {{ $end }}</b>
    @endif
</p>

<table>
    <thead>
        <tr>
            <th>Produk</th>
            <th>Kode Batch</th>
            <th>Tanggal Masuk</th>
            <th>Tanggal Kadaluwarsa</th>
            <th>Sisa Stok</th>
            <th>Awal Stok</th>
            <th>Supplier</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($batches as $b)
            <tr>
                <td>{{ $b->product->nama_produk }}</td>
                <td>{{ $b->batch_number }}</td>
                <td>{{ $b->tanggal_masuk }}</td>
                <td>{{ $b->tanggal_expired }}</td>
                <td>{{ $b->quantity_sekarang }}</td>
                <td>{{ $b->quantity_masuk }}</td>
                <td>{{ $b->supplier }}</td>
                <td>
                    {{ match($b->status) {
                        'active'   => 'Aktif',
                        'sold_out' => 'Habis',
                        'expired'  => 'Kadaluwarsa',
                        default    => '-',
                    } }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
