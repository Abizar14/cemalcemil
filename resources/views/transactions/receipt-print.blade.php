<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Struk {{ $transaction->invoice_number }}</title>
        <style>
            :root {
                color-scheme: light;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                background: #eef2f7;
                color: #0f172a;
                font-family: "Segoe UI", Arial, sans-serif;
            }

            .actions {
                max-width: 820px;
                margin: 20px auto 0;
                padding: 0 16px;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                justify-content: center;
            }

            .actions a,
            .actions button {
                border: 0;
                border-radius: 999px;
                padding: 12px 18px;
                font: inherit;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                text-decoration: none;
            }

            .actions button {
                background: #0f172a;
                color: #fff;
            }

            .actions a {
                background: #fff;
                color: #0f172a;
                border: 1px solid #cbd5e1;
            }

            .page {
                max-width: 820px;
                margin: 20px auto 36px;
                padding: 0 16px;
            }

            .receipt {
                background: #fff;
                border: 1px solid #dbe3ee;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 24px 50px rgba(15, 23, 42, 0.08);
            }

            .header {
                padding: 28px 32px 22px;
                background: linear-gradient(135deg, #0f172a, #1e293b);
                color: #fff;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .brand-logo {
                width: 72px;
                height: 72px;
                border-radius: 22px;
                background: rgba(255, 255, 255, 0.12);
                padding: 8px;
                flex-shrink: 0;
            }

            .brand-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .header h1 {
                margin: 0;
                font-size: 28px;
                line-height: 1.1;
            }

            .header p {
                margin: 8px 0 0;
                color: #dbeafe;
                font-size: 14px;
                line-height: 1.6;
            }

            .meta {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
                padding: 24px 32px 8px;
            }

            .meta-card {
                border: 1px solid #e2e8f0;
                border-radius: 18px;
                padding: 16px 18px;
                background: #f8fafc;
            }

            .meta-label {
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: #64748b;
            }

            .meta-value {
                margin-top: 8px;
                font-size: 16px;
                font-weight: 700;
                color: #0f172a;
            }

            .content {
                padding: 16px 32px 28px;
            }

            .section-title {
                margin: 0 0 14px;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                color: #64748b;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            thead th {
                border-bottom: 1px solid #e2e8f0;
                padding: 0 0 12px;
                font-size: 12px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #64748b;
                text-align: left;
            }

            thead th:last-child,
            tbody td:last-child,
            .summary-row strong,
            .summary-row span:last-child {
                text-align: right;
            }

            tbody td {
                border-bottom: 1px solid #f1f5f9;
                padding: 14px 0;
                vertical-align: top;
                font-size: 14px;
            }

            tbody tr:last-child td {
                border-bottom: 0;
            }

            .muted {
                color: #64748b;
                font-size: 13px;
            }

            .summary {
                margin-top: 24px;
                margin-left: auto;
                width: min(100%, 320px);
                border: 1px solid #e2e8f0;
                border-radius: 20px;
                padding: 18px 20px;
                background: #f8fafc;
            }

            .summary-row {
                display: flex;
                justify-content: space-between;
                gap: 16px;
                padding: 8px 0;
                font-size: 14px;
            }

            .summary-row.total {
                margin-top: 8px;
                padding-top: 14px;
                border-top: 1px solid #cbd5e1;
                font-size: 17px;
                font-weight: 800;
            }

            .notes,
            .footer {
                margin-top: 24px;
                border-radius: 18px;
                padding: 16px 18px;
            }

            .notes {
                background: #fff7ed;
                border: 1px solid #fed7aa;
            }

            .footer {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                text-align: center;
                color: #475569;
                font-size: 13px;
                line-height: 1.7;
            }

            .cancelled {
                margin-top: 18px;
                border-radius: 18px;
                padding: 14px 16px;
                background: #fff1f2;
                border: 1px solid #fecdd3;
                color: #be123c;
                font-size: 13px;
                font-weight: 600;
            }

            @page {
                size: A5 portrait;
                margin: 12mm;
            }

            @media (max-width: 640px) {
                .meta {
                    grid-template-columns: 1fr;
                }

                .header,
                .content,
                .meta {
                    padding-left: 20px;
                    padding-right: 20px;
                }

                thead {
                    display: none;
                }

                tbody tr {
                    display: block;
                    padding: 14px 0;
                    border-bottom: 1px solid #f1f5f9;
                }

                tbody td {
                    display: flex;
                    justify-content: space-between;
                    gap: 12px;
                    padding: 4px 0;
                    border: 0;
                }

                tbody td::before {
                    content: attr(data-label);
                    color: #64748b;
                    font-size: 12px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.08em;
                }
            }

            @media print {
                body {
                    background: #fff;
                }

                .actions {
                    display: none;
                }

                .page {
                    max-width: none;
                    margin: 0;
                    padding: 0;
                }

                .receipt {
                    border: 0;
                    border-radius: 0;
                    box-shadow: none;
                }

                .header {
                    background: #fff;
                    color: #0f172a;
                    border-bottom: 2px solid #0f172a;
                }

                .brand-logo {
                    background: #fff7ed;
                    border: 1px solid #fed7aa;
                }

                .header p {
                    color: #334155;
                }
            }
        </style>
    </head>
    <body onload="window.print()">
        <div class="actions">
            <button type="button" onclick="window.print()">Print Lagi</button>
            <a href="{{ route('transactions.thermal-print', $transaction) }}">Versi Thermal</a>
            <a href="{{ route('transactions.show', $transaction) }}">Kembali</a>
        </div>

        <div class="page">
            <section class="receipt">
                <header class="header">
                    <div class="brand">
                        <div class="brand-logo">
                            <img src="{{ asset($booth['logo']) }}" alt="Logo {{ $booth['name'] }}">
                        </div>
                        <div>
                            <h1>{{ $booth['name'] }}</h1>
                            <p>
                                {{ $booth['address'] }}, {{ $booth['city'] }}<br>
                                Telp: {{ $booth['phone'] }}
                            </p>
                        </div>
                    </div>
                </header>

                <div class="meta">
                    <article class="meta-card">
                        <div class="meta-label">Nomor Invoice</div>
                        <div class="meta-value">{{ $transaction->invoice_number }}</div>
                    </article>
                    <article class="meta-card">
                        <div class="meta-label">Tanggal</div>
                        <div class="meta-value">{{ $transaction->transaction_date->format('d M Y, H:i') }}</div>
                    </article>
                    <article class="meta-card">
                        <div class="meta-label">Kasir</div>
                        <div class="meta-value">{{ $transaction->user->name }}</div>
                    </article>
                    <article class="meta-card">
                        <div class="meta-label">Pembayaran</div>
                        <div class="meta-value">{{ strtoupper($transaction->payment_method) }} / {{ strtoupper($transaction->payment_status) }}</div>
                    </article>
                    @if ($transaction->shift)
                        <article class="meta-card">
                            <div class="meta-label">Shift</div>
                            <div class="meta-value">{{ $transaction->shift->opened_at->format('d M Y, H:i') }}</div>
                        </article>
                    @endif
                    <article class="meta-card">
                        <div class="meta-label">Status Transaksi</div>
                        <div class="meta-value">{{ strtoupper($transaction->transaction_status) }}</div>
                    </article>
                </div>

                <div class="content">
                    <h2 class="section-title">Rincian Pembelian</h2>

                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->details as $detail)
                                <tr>
                                    <td data-label="Produk">
                                        <strong>{{ $detail->product_name }}</strong>
                                    </td>
                                    <td data-label="Qty">{{ $detail->qty }}</td>
                                    <td data-label="Harga">Rp{{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td data-label="Subtotal">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="summary">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <strong>Rp{{ number_format($transaction->subtotal, 0, ',', '.') }}</strong>
                        </div>
                        <div class="summary-row">
                            <span>Dibayar</span>
                            <span>Rp{{ number_format($transaction->paid_amount ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Kembalian</span>
                            <span>Rp{{ number_format($transaction->change_amount ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <strong>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    @if ($transaction->notes)
                        <div class="notes">
                            <strong>Catatan</strong>
                            <div class="muted" style="margin-top: 8px;">
                                {{ $transaction->notes }}
                            </div>
                        </div>
                    @endif

                    @if ($transaction->isCancelled())
                        <div class="cancelled">
                            Transaksi dibatalkan. Alasan: {{ $transaction->cancel_reason ?: '-' }}
                        </div>
                    @endif

                    <div class="footer">
                        {{ $booth['receipt_footer'] }}<br>
                        Simpan struk ini sebagai bukti transaksi.
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
