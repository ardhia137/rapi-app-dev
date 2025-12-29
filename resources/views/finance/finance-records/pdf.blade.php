<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - {{ \Carbon\Carbon::parse($periode . '-01')->format('F Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header Section */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2c3e50;
        }

        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .logo-wrapper {
            display: table;
            width: 100%;
        }

        .logo-cell {
            display: table-cell;
            width: 150px;
            vertical-align: middle;
        }

        .company-cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }

        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .logo {
            width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 11px;
            color: #666;
            line-height: 1.5;
        }

        .document-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .report-meta {
            font-size: 12px;
            color: #666;
        }

        .report-meta strong {
            color: #333;
        }

        /* Summary Cards */
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
        }

        .summary-card.target {
            background: #f3e8ff;
            border: 2px solid #c084fc;
        }

        .summary-card.expense {
            background: #fee2e2;
            border: 2px solid #ef4444;
        }

        .summary-card.income {
            background: #dcfce7;
            border: 2px solid #22c55e;
        }

        .summary-card.balance {
            background: #dbeafe;
            border: 2px solid #3b82f6;
        }

        .summary-card-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .summary-card.target .summary-card-title { color: #7c3aed; }
        .summary-card.expense .summary-card-title { color: #dc2626; }
        .summary-card.income .summary-card-title { color: #16a34a; }
        .summary-card.balance .summary-card-title { color: #2563eb; }

        .summary-card-value {
            font-size: 20px;
            font-weight: bold;
        }

        .summary-card.target .summary-card-value { color: #7c3aed; }
        .summary-card.expense .summary-card-value { color: #dc2626; }
        .summary-card.income .summary-card-value { color: #16a34a; }
        .summary-card.balance .summary-card-value { color: #2563eb; }

        .summary-card-note {
            font-size: 9px;
            color: #666;
            margin-top: 5px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #2c3e50;
            color: white;
        }

        th {
            padding: 12px 8px;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
        }

        th.center {
            text-align: center;
        }

        th.right {
            text-align: right;
        }

        td {
            padding: 8px;
            font-size: 11px;
            border-bottom: 1px solid #ddd;
        }

        td.center {
            text-align: center;
        }

        td.right {
            text-align: right;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        /* Type Badge */
        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .type-income {
            background: #dcfce7;
            color: #16a34a;
        }

        .type-expense {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 60px;
            padding-top: 30px;
        }

        .signature-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 60px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 0 auto;
            width: 200px;
            padding-top: 5px;
        }

        .signature-name {
            font-size: 12px;
            font-weight: bold;
        }

        .signature-position {
            font-size: 11px;
            color: #666;
        }

        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div class="logo-wrapper">
                <div class="logo-cell">
                    <img src="{{ public_path('images/logo-rapi.png') }}" alt="Logo" class="logo">
                </div>
                <div class="company-cell">
                    <div class="company-name">RAPI PVC</div>
                </div>
            </div>
            <div class="company-info">
                Jl. Alamat Perusahaan No. 123<br>
                Kota, Provinsi 12345<br>
                Telp: (021) 1234-5678<br>
                Email: info@perusahaan.com<br>
                Website: www.perusahaan.com
            </div>
        </div>
        <div class="header-right">
            <div class="document-title">LAPORAN KEUANGAN</div>
            <div class="report-meta">
                <strong>Periode:</strong> {{ \Carbon\Carbon::parse($periode . '-01')->format('F Y') }}<br>
                <strong>Dicetak:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}<br>
                <strong>Oleh:</strong> {{ Auth::user()->name ?? 'Admin' }}
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card target">
            <div class="summary-card-title">Target Bulanan</div>
            <div class="summary-card-value">Rp {{ number_format($budgetTarget ? $budgetTarget->budget_bulanan : 0, 0, ',', '.') }}</div>
            @if(!$budgetTarget)
            <div class="summary-card-note">Belum ada target</div>
            @endif
        </div>
        <div class="summary-card expense">
            <div class="summary-card-title">Total Pengeluaran</div>
            <div class="summary-card-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card balance" style="width: 100%;">
            <div class="summary-card-title">Saldo Sisa</div>
            <div class="summary-card-value" style="font-size: 24px; color: {{ $saldoSisa >= 0 ? '#2563eb' : '#dc2626' }}">
                Rp {{ number_format($saldoSisa, 0, ',', '.') }}
            </div>
            @if($budgetTarget)
            <div class="summary-card-note">
                {{ $saldoSisa >= 0 ? 'Tersisa' : 'Overbudget' }}
                {{ number_format(abs(($saldoSisa / $budgetTarget->budget_bulanan) * 100), 1) }}%
            </div>
            @endif
        </div>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 12%;">Tanggal</th>
            <th class="center" style="width: 13%;">Tipe</th>
            <th style="width: 15%;">Kategori</th>
            <th class="right" style="width: 18%;">Jumlah</th>
            <th style="width: 27%;">Deskripsi</th>
            <th style="width: 10%;">User</th>
        </tr>
        </thead>
        <tbody>
        @forelse($financeRecords as $index => $fr)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($fr->tanggal)->format('d/m/Y') }}</td>
                <td class="center">
                    @if($fr->tipe === 'income')
                        <span class="type-badge type-income">Pemasukan</span>
                    @else
                        <span class="type-badge type-expense">Pengeluaran</span>
                    @endif
                </td>
                <td>{{ $fr->kategori }}</td>
                <td class="right" style="color: {{ $fr->tipe === 'income' ? '#16a34a' : '#dc2626' }}; font-weight: bold;">
                    Rp {{ number_format($fr->jumlah, 0, ',', '.') }}
                </td>
                <td>{{ $fr->deskripsi ?? '-' }}</td>
                <td style="font-size: 10px;">{{ $fr->user->name ?? 'Unknown' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="center" style="padding: 20px; color: #999;">
                    Belum ada data keuangan untuk periode ini
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                    <div class="signature-title">Mengetahui</div>
                    <div class="signature-line">
                        <div class="signature-name">_______________</div>
                        <div class="signature-position">Manager</div>
                    </div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                    <div class="signature-title">Dibuat Oleh</div>
                    <div class="signature-line">
                        <div class="signature-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="signature-position">Staff Keuangan</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dan sah tanpa tanda tangan basah.</p>
        <p>{{ now()->format('d F Y H:i:s') }}</p>
    </div>
</div>
</body>
</html>
