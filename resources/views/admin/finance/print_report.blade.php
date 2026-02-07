<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11px; }
        
        .info-periode { margin-bottom: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-red { color: #d32f2f; }
        .text-green { color: #1b5e20; }
        .font-bold { font-weight: bold; }
        
        .summary { float: right; width: 40%; border: 1px solid #000; padding: 10px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        
        .footer { margin-top: 50px; text-align: right; }
        .ttd-area { margin-top: 60px; margin-right: 20px; text-align: center; display: inline-block; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN KEUANGAN</h1>
        <h2>{{ $sekolah['nama'] }}</h2>
        <p>{{ $sekolah['alamat'] }}</p>
    </div>

    <div class="info-periode">
        <strong>Periode Laporan:</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} 
        s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 15%">Jenis</th>
                <th style="width: 35%">Keterangan</th>
                <th style="width: 10%">Via</th>
                <th style="width: 20%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td class="text-center">
                    @if($item->jenis == 'Pemasukan')
                        Masuk
                    @else
                        Keluar
                    @endif
                </td>
                <td>{{ $item->keterangan }}</td>
                <td class="text-center">{{ $item->via }}</td>
                <td class="text-right {{ $item->jenis == 'Pemasukan' ? '' : 'text-red' }}">
                    {{ number_format($item->nominal, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="5" class="text-right">Total Pemasukan</td>
                <td class="text-right">{{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="5" class="text-right">Total Pengeluaran</td>
                <td class="text-right text-red">({{ number_format($totalKeluar, 0, ',', '.') }})</td>
            </tr>
            <tr style="background-color: #e0e0e0; font-weight: bold; font-size: 13px;">
                <td colspan="5" class="text-right">Saldo Akhir Periode</td>
                <td class="text-right">{{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="ttd-area">
            <p>{{ \App\Models\Setting::where('key', 'kota_sekolah')->value('value') ?? 'Kota' }}, {{ date('d F Y') }}</p>
            <p>Bendahara,</p>
            <br><br><br>
            <p><strong>{{ Auth::user()->name }}</strong></p>
        </div>
    </div>

</body>
</html>