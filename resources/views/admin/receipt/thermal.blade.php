<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        @page { margin: 0; }
        body { 
            margin: 0; 
            padding: 10px 5px 20px 5px; /* Atas Kanan Bawah Kiri */
            font-family: 'Courier New', Courier, monospace; /* Font struk standar */
            font-size: 9pt; 
            color: #000;
        }
        
        .container { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        
        /* Header Sekolah */
        .school-name { font-size: 11pt; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .school-address { font-size: 7pt; margin-bottom: 5px; line-height: 1.2; }
        
        /* Garis Pemisah */
        .divider { border-top: 1px dashed #000; margin: 5px 0; width: 100%; }
        .divider-double { border-top: 2px dashed #000; margin: 5px 0; width: 100%; }

        /* Tabel Info Transaksi */
        .info-table { width: 100%; margin-bottom: 5px; }
        .info-table td { padding: 1px 0; vertical-align: top; }
        .label { width: 35%; }
        
        /* Tabel Rincian Item */
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table td { padding: 2px 0; vertical-align: top; }
        
        .item-name { font-weight: bold; }
        .item-sub { font-size: 7pt; color: #333; font-style: italic; }
        
        /* Total Section */
        .total-section { margin-top: 5px; font-size: 11pt; }
        
        /* Footer */
        .footer { font-size: 7pt; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="text-center">
            <div class="school-name">{{ $settings['nama_sekolah'] ?? 'PONDOK PESANTREN' }}</div>
            <div class="school-address">{{ $settings['alamat_sekolah'] ?? 'Alamat Belum Diatur' }}</div>
        </div>

        <div class="divider-double"></div>

        <table class="info-table">
            <tr>
                <td class="label">No. TRX</td>
                <td>: {{ $transaction->kode_transaksi }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td>: {{ date('d/m/y H:i', strtotime($transaction->created_at)) }}</td>
            </tr>
            <tr>
                <td class="label">Santri</td>
                <td>: {{ substr($transaction->candidate->nama_lengkap, 0, 18) }}</td>
            </tr>
            <tr>
                <td class="label">Jenjang</td>
                <td>: {{ $transaction->candidate->jenjang }}</td>
            </tr>
            <tr>
                <td class="label">Kasir</td>
                <td>: {{ substr($transaction->admin->name ?? 'Admin', 0, 15) }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="items-table">
            @foreach($transaction->details as $detail)
            <tr>
                <td colspan="2" class="item-name">
                    {{ $detail->bill->payment_type->nama_pembayaran }}
                </td>
            </tr>
            <tr>
                <td class="text-left item-sub">
                    @if($detail->bill->sisa_tagihan <= 0)
                        [LUNAS]
                    @else
                        Sisa: Rp{{ number_format($detail->bill->sisa_tagihan, 0, ',', '.') }}
                    @endif
                </td>
                <td class="text-right bold">
                    Rp {{ number_format($detail->nominal, 0, ',', '.') }}
                </td>
            </tr>
            <tr><td colspan="2" style="height: 3px;"></td></tr> @endforeach
        </table>

        <div class="divider"></div>

        <table style="width: 100%">
            <tr class="total-section">
                <td class="text-left bold">TOTAL</td>
                <td class="text-right bold">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="text-center footer">
            <div>Terima Kasih</div>
            <div>"Semoga Berkah & Bermanfaat"</div>
            <br>
            <div>- Simpan struk ini sebagai bukti sah -</div>
        </div>
        
        <div class="text-center">.</div> 

    </div>

</body>
</html>