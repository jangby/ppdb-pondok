<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        @page { margin: 0; }
        body { 
            margin: 0; 
            padding: 10px 10px 30px 10px; 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 9pt; 
            color: #000;
        }
        .container { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .school-name { font-size: 10pt; font-weight: bold; text-transform: uppercase; }
        .school-address { font-size: 7pt; margin-bottom: 5px; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .info-table, .items-table { width: 100%; border-collapse: collapse; }
        .item-sub { font-size: 7pt; font-style: italic; }
        .qrcode-container { margin: 15px 0; text-align: center; }
        .qrcode-container img { width: 90px; height: 90px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center">
            <div class="school-name">{{ $settings['nama_sekolah'] ?? 'PONDOK PESANTREN' }}</div>
            <div class="school-address">{{ $settings['alamat_sekolah'] ?? '' }}</div>
        </div>

        <div class="divider"></div>

        <table class="info-table">
            <tr><td>No. TRX</td><td>: {{ $transaction->kode_transaksi }}</td></tr>
            <tr><td>Santri</td><td>: {{ substr($transaction->candidate->nama_lengkap, 0, 20) }}</td></tr>
            <tr><td>Tanggal</td><td>: {{ $transaction->created_at->format('d/m/y H:i') }}</td></tr>
        </table>

        <div class="divider"></div>

        <table class="items-table">
            @foreach($transaction->details as $detail)
            <tr>
                <td class="bold">{{ $detail->bill->payment_type->nama_pembayaran }}</td>
                <td class="text-right bold">Rp{{ number_format($detail->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="bold" style="padding-top: 5px;">TOTAL</td>
                <td class="text-right bold" style="padding-top: 5px;">Rp{{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="text-center" style="font-size: 7pt;">
            *** TERIMA KASIH ***<br>
            Simpan struk ini sebagai bukti sah
        </div>
    </div>
</body>
</html>