<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Thermal {{ $transaction->invoice_number }}</title>
        <style>
            body { margin: 0; background: #f3f4f6; color: #000; font-family: "Courier New", Courier, monospace; font-size: 12px; line-height: 1.45; }
            .receipt { width: {{ ($booth['receipt_paper'] ?? '80') === '58' ? '58mm' : '80mm' }}; margin: 0 auto; background: #fff; padding: 8mm 6mm 10mm; box-sizing: border-box; }
            .center { text-align: center; }
            .divider { border-top: 1px dashed #000; margin: 8px 0; }
            .row { display: flex; justify-content: space-between; gap: 12px; }
            .item { margin-bottom: 8px; }
            .small { font-size: 11px; }
            .logo { width: 56px; height: 56px; object-fit: contain; display: block; margin: 0 auto 8px; }
            .actions { width: {{ ($booth['receipt_paper'] ?? '80') === '58' ? '58mm' : '80mm' }}; margin: 12px auto; display: flex; gap: 8px; justify-content: center; }
            button, a { border: 1px solid #000; background: #fff; color: #000; padding: 8px 12px; text-decoration: none; font: inherit; cursor: pointer; }
            @media print { .actions { display: none; } body { margin: 0; background: #fff; } .receipt { margin: 0; } }
        </style>
    </head>
    <body onload="window.print()">
        <div class="actions">
            <button type="button" onclick="window.print()">Print Lagi</button>
            <a href="{{ route('transactions.show', $transaction) }}">Kembali</a>
        </div>

        <div class="receipt">
            <div class="center">
                <img src="{{ asset($booth['logo']) }}" alt="Logo {{ $booth['name'] }}" class="logo">
                <strong>{{ strtoupper($booth['name']) }}</strong><br>
                <span class="small">{{ $booth['address'] }}, {{ $booth['city'] }}</span><br>
                <span class="small">Telp: {{ $booth['phone'] }}</span>
            </div>

            <div class="divider"></div>

            <div class="small">
                <div>No: {{ $transaction->invoice_number }}</div>
                <div>Tgl: {{ $transaction->transaction_date->format('d/m/Y H:i') }}</div>
                <div>Kasir: {{ $transaction->user->name }}</div>
                <div>Metode: {{ strtoupper($transaction->payment_method) }}</div>
                <div>Status: {{ strtoupper($transaction->payment_status) }}</div>
                @if ($transaction->shift)
                    <div>Shift: {{ $transaction->shift->opened_at->format('d/m H:i') }}</div>
                @endif
            </div>

            <div class="divider"></div>

            @foreach ($transaction->details as $detail)
                <div class="item">
                    <div>{{ $detail->product_name }}</div>
                    <div class="row small">
                        <span>{{ $detail->qty }} x Rp{{ number_format($detail->price, 0, ',', '.') }}</span>
                        <span>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach

            <div class="divider"></div>

            <div class="row"><span>Total</span><strong>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></div>
            <div class="row"><span>Dibayar</span><span>Rp{{ number_format($transaction->paid_amount ?? 0, 0, ',', '.') }}</span></div>
            <div class="row"><span>Kembalian</span><span>Rp{{ number_format($transaction->change_amount ?? 0, 0, ',', '.') }}</span></div>

            @if ($transaction->notes)
                <div class="divider"></div>
                <div class="small">Catatan: {{ $transaction->notes }}</div>
            @endif

            @if ($transaction->isCancelled())
                <div class="divider"></div>
                <div class="small">Dibatalkan: {{ $transaction->cancel_reason ?: '-' }}</div>
            @endif

            <div class="divider"></div>

            <div class="center small">
                {{ $booth['receipt_footer'] }}<br>
                Simpan struk ini sebagai bukti transaksi
            </div>
        </div>
    </body>
</html>
