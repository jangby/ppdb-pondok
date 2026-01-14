<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        /* Reset margin agar printer tidak memotong konten */
        @page { margin: 0px; }
        body { margin: 0px; padding: 5px; }
        
        /* Font Thermal yang Rapi */
        body, table {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px; /* Ukuran kecil agar muat */
        }
        
        .container { width: 100%; }
        
        .center { text-align: center; }
        .right { text-align: right; }
        .left { text-align: left; }
        
        /* Garis Putus-putus Khas Struk */
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 5px 0;
            width: 100%;
        }

        .bold { font-weight: bold; }
        
        /* Layout Tabel Rincian */
        .item-table { width: 100%; }
        .item-table td { padding: 2px 0; vertical-align: top; }
        
        /* Spacer */
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="center">
            <div class="bold" style="font-size: 12px;">PP. AL-IKHLAS</div>
            <div>Jl. Raya Pesantren No. 99</div>
            <div>Tasikmalaya</div>
        </div>

        <div class="dashed-line"></div>

        <table style="width: 100%">
            <tr>
                <td>No</td>
                <td class="right">{{ $transaction->kode_transaksi }}</td>
            </tr>
            <tr>
                <td>Tgl</td>
                <td class="right">{{ date('d/m/y H:i', strtotime($transaction->created_at)) }}</td>
            </tr>
            <tr>
                <td>Siswa</td>
                <td class="right">{{ substr($transaction->candidate->nama_lengkap, 0, 15) }}</td>
            </tr>
            <tr>
                <td>Admin</td>
                <td class="right">{{ $transaction->admin->name ?? 'Kasir' }}</td>
            </tr>
        </table>

        <div class="dashed-line"></div>

        <table class="item-table">
            @foreach($transaction->details as $detail)
            <tr>
                <td colspan="2" class="bold">
                    {{ $detail->bill->payment_type->nama_pembayaran }}
                </td>
            </tr>
            <tr>
                <td class="left">
                    @if($detail->bill->sisa_tagihan == 0)
                        (Lunas)
                    @else
                        (Sisa: {{ number_format($detail->bill->sisa_tagihan, 0, ',', '.') }})
                    @endif
                </td>
                <td class="right">
                    {{ number_format($detail->nominal, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="dashed-line"></div>

        <table style="width: 100%">
            <tr style="font-size: 12px;">
                <td class="bold">TOTAL BAYAR</td>
                <td class="right bold">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="dashed-line"></div>

        <div class="center mb-2">
            <div>Terima Kasih</div>
            <div>Simpan struk ini sebagai</div>
            <div>bukti pembayaran yang sah</div>
        </div>
        
        <br>
        <div class="center">.</div> 
        </div>

</body>
</html>