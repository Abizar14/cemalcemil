<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Laporan Booth</title>
        <style>
            body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
            h1, h2 { margin: 0 0 8px; }
            p { margin: 0 0 6px; }
            .muted { color: #6b7280; }
            .section { margin-top: 24px; }
            .header { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
            .header td { vertical-align: middle; }
            .logo-wrap { width: 84px; }
            .logo-box { width: 68px; height: 68px; border: 1px solid #e5e7eb; border-radius: 16px; padding: 6px; background: #fffaf5; }
            .logo-box img { width: 100%; height: 100%; object-fit: contain; }
            .summary { width: 100%; border-collapse: collapse; margin-top: 12px; }
            .summary td, .summary th { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; }
            .summary th { background: #f3f4f6; text-align: left; }
            .cards { width: 100%; border-collapse: separate; border-spacing: 8px; margin-top: 8px; }
            .cards td { border: 1px solid #e5e7eb; padding: 10px; background: #fafafa; }
        </style>
    </head>
    <body>
        <table class="header">
            <tr>
                <td class="logo-wrap">
                    <div class="logo-box">
                        <img src="{{ public_path($booth['logo']) }}" alt="Logo {{ $booth['name'] }}">
                    </div>
                </td>
                <td>
                    <h1>{{ $booth['name'] }}</h1>
                    <p class="muted">{{ $booth['address'] }}, {{ $booth['city'] }} | {{ $booth['phone'] }}</p>
                    <p class="muted">Laporan periode {{ $dateFrom->format('d M Y') }} - {{ $dateTo->format('d M Y') }}</p>
                </td>
            </tr>
        </table>

        <table class="cards">
            <tr>
                <td><strong>Penjualan tercatat</strong><br>Rp{{ number_format($summary['gross_sales'], 0, ',', '.') }}</td>
                <td><strong>QRIS pending</strong><br>Rp{{ number_format($summary['pending_qris_total'], 0, ',', '.') }}</td>
                <td><strong>Net operasional</strong><br>Rp{{ number_format($summary['net_amount'], 0, ',', '.') }}</td>
                <td><strong>Nilai minusan</strong><br>Rp{{ number_format($summary['minus_amount'], 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="section">
            <h2>Rekap Harian</h2>
            <table class="summary">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Penjualan</th>
                        <th>Realisasi</th>
                        <th>Kas Masuk</th>
                        <th>Kas Keluar</th>
                        <th>Net</th>
                        <th>Minus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row['date']->format('d M Y') }}</td>
                            <td>Rp{{ number_format($row['gross_sales'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($row['realized_sales'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($row['cash_in'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($row['cash_out'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($row['net_amount'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($row['minus_amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Rekap per Shift</h2>
            <table class="summary">
                <thead>
                    <tr>
                        <th>Kasir</th>
                        <th>Shift</th>
                        <th>Transaksi</th>
                        <th>Cash</th>
                        <th>QRIS</th>
                        <th>Kas In/Out</th>
                        <th>Estimasi Tutup</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shiftRows as $row)
                        <tr>
                            <td>{{ $row['shift']->user->name }}</td>
                            <td>{{ $row['shift']->opened_at->format('d M H:i') }}{{ $row['shift']->closed_at ? ' - '.$row['shift']->closed_at->format('d M H:i') : '' }}</td>
                            <td>{{ $row['transactions_count'] }}</td>
                            <td>Rp{{ number_format($row['cash_sales'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($row['qris_sales'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($row['cash_in'], 0, ',', '.') }} / Rp{{ number_format($row['cash_out'], 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($row['expected_closing_cash'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Belum ada data shift pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </body>
</html>
