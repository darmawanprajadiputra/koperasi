<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur {{ $invoiceNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            padding: 30px 40px;
        }

        /* ── Header ───────────────────────────────────────────── */
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 60px;
        }

        .header-left img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            padding-left: 10px;
        }

        .header-right .koperasi-name {
            font-size: 15px;
            font-weight: bold;
            color: #2e7d32;
        }

        .header-right .koperasi-address {
            font-size: 10.5px;
            color: #444;
            margin-top: 2px;
        }

        /* ── Judul Faktur ─────────────────────────────────────── */
        .invoice-title-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .invoice-title-left {
            display: table-cell;
            vertical-align: top;
        }

        .invoice-title-left h1 {
            font-size: 22px;
            letter-spacing: 1px;
            color: #1a1a1a;
        }

        .invoice-title-right {
            display: table-cell;
            text-align: right;
            vertical-align: top;
        }

        .invoice-title-right .inv-number {
            font-size: 18px;
            font-weight: bold;
            color: #2e7d32;
        }

        .invoice-title-right .inv-date {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }

        /* ── Info Pengirim / Penerima ─────────────────────────── */
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 22px;
        }

        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-col .label {
            font-size: 10.5px;
            color: #777;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-col .value {
            font-size: 12.5px;
            font-weight: bold;
        }

        .info-col .sub-value {
            font-size: 11px;
            color: #444;
            margin-top: 2px;
        }

        /* ── Tabel Item ───────────────────────────────────────── */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        table.items thead th {
            background-color: #2e7d32;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            text-align: left;
            padding: 8px 10px;
        }

        table.items thead th.text-right {
            text-align: right;
        }

        table.items tbody td {
            font-size: 11.5px;
            padding: 7px 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        table.items tbody td.text-right {
            text-align: right;
        }

        table.items tbody tr:nth-child(even) {
            background-color: #f7f9f7;
        }

        /* ── Footer Section (Catatan + Total) ────────────────── */
        .footer-row {
            display: table;
            width: 100%;
            margin-top: 14px;
        }

        .footer-left {
            display: table-cell;
            width: 55%;
            vertical-align: top;
        }

        .footer-right {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }

        .note-label {
            font-size: 10.5px;
            color: #777;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .note-text {
            font-size: 11px;
            color: #444;
        }

        table.totals {
            width: 100%;
            border-collapse: collapse;
        }

        table.totals td {
            font-size: 11.5px;
            padding: 6px 0;
        }

        table.totals td.t-label {
            color: #555;
        }

        table.totals td.t-value {
            text-align: right;
            font-weight: 600;
        }

        table.totals tr.total-row td {
            border-top: 1px solid #ccc;
            padding-top: 8px;
            font-weight: bold;
            font-size: 12.5px;
            color: #2e7d32;
        }

        /* ── Pembayaran & Stempel ─────────────────────────────── */
        .payment-row {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .payment-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .payment-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: center;
        }

        .payment-left .pay-title {
            font-size: 11.5px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .bank-list {
            font-size: 11px;
            line-height: 1.6;
        }

        .bank-box {
            display: table;
            width: 280px;
            background-color: #eef6ee;
            border: 1px solid #c8e0c8;
            border-left: 4px solid #2e7d32;
            border-radius: 4px;
            padding: 10px 14px;
            margin-top: 4px;
        }

        .bank-box-content {
            display: table-cell;
            vertical-align: middle;
        }

        .bank-box-title {
            font-size: 12.5px;
            font-weight: bold;
            color: #2e7d32;
            letter-spacing: 0.5px;
        }

        .bank-box-sub {
            font-size: 10.5px;
            color: #444;
            margin-top: 4px;
            line-height: 1.5;
        }

        .cash-box {
            display: table;
            width: 280px;
            background-color: #eef6ee;
            border: 1px solid #c8e0c8;
            border-left: 4px solid #2e7d32;
            border-radius: 4px;
            padding: 10px 14px;
            margin-top: 4px;
        }

        .cash-box-content {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .cash-box-title {
            font-size: 12.5px;
            font-weight: bold;
            color: #2e7d32;
            letter-spacing: 0.5px;
        }

        .cash-box-sub {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .stamp-box {
            width: 90px;
            height: 90px;
            margin: 0 auto 8px auto;
        }

        .stamp-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .signature-label {
            font-size: 11px;
            color: #444;
        }

        .closing-text {
            margin-top: 26px;
            font-size: 11.5px;
            font-style: italic;
            color: #2e7d32;
        }
    </style>
</head>

<body>

    @php
        $banks = [
            'BNI' => ['no' => '2020 1395 70', 'name' => 'NANDANG (Ketua)'],
            'BRI' => ['no' => '4411 0100 4012 507', 'name' => 'ASEP SHOLAHUDIN (Bendahara)'],
            'BCA' => ['no' => '377 0291 479', 'name' => 'LILI JULIANSYAH (Admin)'],
        ];

        $isTransfer = ($order->payment_method ?? null) === 'transfer';
        $selectedBank = null;
        $displayNotes = $order->notes;

        if (
            $isTransfer &&
            !empty($order->notes) &&
            preg_match('/Transfer via Bank (BNI|BRI|BCA)/', $order->notes, $m)
        ) {
            $bankCode = $m[1];
            $selectedBank = array_merge(['code' => $bankCode], $banks[$bankCode]);
            $displayNotes = trim(preg_replace('/^Transfer via Bank ' . $bankCode . '\s*\n?/', '', $order->notes));
        }
    @endphp

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('assets/pictures/koperasi.png') }}" alt="Logo">
        </div>
        <div class="header-right">
            <div class="koperasi-name">Koperasi Bismillah Indonesia Sejahtera</div>
            <div class="koperasi-address">
                Jl. Sukabumi-Babakan RT.03 RW.12 Kel. Sukakarya Kec. Warudoyong, Kota Sukabumi 43135<br>
                081398823223
            </div>
        </div>
    </div>

    <!-- Judul Faktur -->
    <div class="invoice-title-row">
        <div class="invoice-title-left">
            <h1>FAKTUR</h1>
        </div>
        <div class="invoice-title-right">
            <div class="inv-number">{{ $invoiceNumber }}</div>
            <div class="inv-date">{{ $invoiceDate }}</div>
        </div>
    </div>

    <!-- Info Pengirim / Penerima -->
    <div class="info-row">
        <div class="info-col">
            <div class="label">Bayaran Ke</div>
            <div class="value">Koperasi Bismillah Indonesia Sejahtera</div>
            <div class="sub-value">081398823223</div>
        </div>
        <div class="info-col">
            <div class="label">Dikirim Ke</div>
            <div class="value">{{ $order->recipient ?: $order->name_customer }}</div>
            <div class="sub-value">{{ $order->no_telephone }}</div>
        </div>
    </div>

    <!-- Tabel Item -->
    <table class="items">
        <thead>
            <tr>
                <th style="width: 6%;">Nomor</th>
                <th style="width: 38%;">Produk</th>
                <th style="width: 14%;">Qty</th>
                <th style="width: 18%;" class="text-right">Harga</th>
                <th style="width: 24%;" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name_product ?? '-' }}</td>
                    <td>{{ $item->total_item }} {{ $item->product->unit->name_unit ?? '' }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total_amount, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer: Catatan + Total -->
    <div class="footer-row">
        <div class="footer-left">
            <div class="note-label">Catatan</div>
            <div class="note-text">{{ $displayNotes ?: '-' }}</div>
        </div>
        <div class="footer-right">
            <table class="totals">
                <tr>
                    <td class="t-label">Total</td>
                    <td class="t-value">Rp {{ number_format($totalAmount, 2, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="t-label">Total Keseluruhan</td>
                    <td class="t-value">Rp {{ number_format($totalAmount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="t-label">Saldo</td>
                    <td class="t-value">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Pembayaran & Stempel -->
    <div class="payment-row">
        <div class="payment-left">
            @if ($selectedBank)
                <div class="pay-title">Metode Pembayaran</div>
                <div class="bank-box">
                    <div class="bank-box-content">
                        <div class="bank-box-title">TRANSFER BANK {{ $selectedBank['code'] }}</div>
                        <div class="bank-box-sub">
                            {{ $selectedBank['no'] }}<br>
                            A/N {{ $selectedBank['name'] }}
                        </div>
                    </div>
                </div>
            @else
                <div class="pay-title">Metode Pembayaran</div>
                <div class="cash-box">
                    <div class="cash-box-content">
                        <div class="cash-box-title">CASH / TUNAI</div>
                        <div class="cash-box-sub">Pembayaran telah dilakukan secara tunai saat barang diterima</div>
                    </div>
                </div>
            @endif
        </div>
        <div class="payment-right">
            <div class="stamp-box">
                <img src="{{ public_path('assets/pictures/koperasi.png') }}" alt="Stempel">
            </div>
            <div class="signature-label">Tanda Tangan</div>
        </div>
    </div>

    <div class="closing-text">Jazakumullahu ahsanal jaza</div>

</body>

</html>