<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .address { font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 5px; }
        
        .item-table { margin-top: 20px; border: 1px solid #000; }
        .item-table th, .item-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .item-table th { background-color: #f0f0f0; }
        
        .total-row td { font-weight: bold; background-color: #f0f0f0; }
        
        .footer { margin-top: 40px; text-align: right; margin-right: 50px; }
        .signature { margin-top: 60px; font-weight: bold; text-decoration: underline; }
        
        .watermark { 
            position: absolute; top: 30%; left: 30%; 
            font-size: 60px; color: rgba(0,0,0,0.05); 
            transform: rotate(-45deg); font-weight: bold; 
            z-index: -1;
        }
    </style>
</head>
<body>

    <div class="watermark">LUNAS</div>

    <div class="header">
        <div class="logo">PONDOK PESANTREN AL-IKHLAS</div>
        <div class="address">Jl. Raya Pesantren No. 99, Tasikmalaya, Jawa Barat<br>Telp: (0265) 123456 | Email: admin@pesantren.com</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">No. Kwitansi</td>
            <td width="35%">: <strong>{{ $transaction->kode_transaksi }}</strong></td>
            <td width="15%">Tanggal</td>
            <td width="35%">: {{ \Carbon\Carbon::parse($transaction->tanggal_bayar)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Telah Terima Dari</td>
            <td>: {{ $transaction->candidate->nama_lengkap }} ({{ $transaction->candidate->no_daftar }})</td>
            <td>Admin</td>
            <td>: {{ $transaction->admin->name ?? 'Admin' }}</td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Keterangan Pembayaran</th>
                <th width="25%" style="text-align: right;">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    {{ $detail->bill->payment_type->nama_pembayaran }}
                    @if($detail->bill->sisa_tagihan == 0)
                        (LUNAS)
                    @else
                        (Cicilan - Sisa: {{ number_format($detail->bill->sisa_tagihan) }})
                    @endif
                </td>
                <td style="text-align: right;">{{ number_format($detail->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">TOTAL DIBAYAR</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    

    <div class="footer">
        <p>Garut, {{ date('d F Y') }}</p>
        <p>Penerima,</p>
        <div class="signature">{{ $transaction->admin->name ?? 'Bendahara' }}</div>
    </div>

</body>
</html>