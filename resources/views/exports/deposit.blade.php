<html>
    {{-- STYLE DEFAULT --}}
    <style>
        .header-title { font-weight: bold; font-size: 14px; text-align: center; }
        .header-address { font-style: italic; font-size: 10px; text-align: center; color: #555555; }
        .table-header { font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d9d9d9; vertical-align: middle; }
        .table-data { border: 1px solid #000000; vertical-align: middle; }
        .amount { text-align: right; border: 1px solid #000000; }
        .total-row { font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000; }
        .grand-total { font-weight: bold; background-color: #4f81bd; color: #ffffff; border: 1px solid #000000; text-align: right; }
    </style>

    {{-- KOP LAPORAN DINAMIS --}}
    <table>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" height="30" valign="center" style="font-weight: bold; font-size: 16px;">
                {{ strtoupper($namaSekolah) }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-style: italic; color: #555;">
                {{ $alamatSekolah }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" height="25" valign="center" style="font-weight: bold; text-decoration: underline; font-size: 12px;">
                REKAPITULASI SETORAN KEUANGAN ({{ strtoupper($sheetName) }})
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-size: 10px;">
                Tanggal Cetak: {{ $tanggal }}
            </td>
        </tr>
        <tr><td></td></tr> {{-- Spasi Kosong --}}
    </table>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th width="5" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #e0e0e0;">NO</th>
                <th width="20" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #e0e0e0;">NO DAFTAR</th>
                <th width="35" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #e0e0e0;">NAMA SANTRI</th>
                
                {{-- Dynamic Header Item (Loop Jenis Pembayaran) --}}
                @foreach($paymentTypes as $item)
                    <th width="20" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d1fae5;">{{ strtoupper($item['name']) }}</th>
                @endforeach

                <th width="25" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #3b82f6; color: #ffffff;">TOTAL BAYAR</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; $no = 1; @endphp
            @foreach($candidates as $c)
                <tr>
                    <td style="border: 1px solid #000; text-align: center;">{{ $no++ }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $c->no_daftar }}</td>
                    <td style="border: 1px solid #000;">{{ $c->nama_lengkap }}</td>
                    
                    @foreach($c->payment_items as $p)
                        <td style="border: 1px solid #000; text-align: right;">
                            {{ $p['amount'] > 0 ? $p['amount'] : '-' }}
                        </td>
                    @endforeach

                    <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #f9fafb;">
                        {{ $c->total_row }}
                    </td>
                </tr>
                @php $grandTotal += $c->total_row; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                {{-- Colspan menyesuaikan jumlah item pembayaran --}}
                <td colspan="{{ 3 + count($paymentTypes) }}" style="border: 1px solid #000; font-weight: bold; text-align: right; height: 25px;">TOTAL KESELURUHAN:</td>
                <td style="border: 1px solid #000; font-weight: bold; background-color: #3b82f6; color: #ffffff; text-align: right;">
                    {{ $grandTotal }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- TANDA TANGAN --}}
    <table>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="2"></td>
            {{-- Offset kolom agar TTD ada di kanan --}}
            <td colspan="{{ max(1, count($paymentTypes)) }}"></td> 
            <td colspan="2" align="center">
                Ditetapkan di: Garut, {{ $tanggal }}
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">Mengetahui,</td>
            <td colspan="{{ max(1, count($paymentTypes)) }}"></td>
            <td colspan="2" align="center">Petugas Administrasi,</td>
        </tr>
        <tr>
            <td colspan="2" align="center" height="60"></td>
            <td colspan="{{ max(1, count($paymentTypes)) }}"></td>
            <td colspan="2" align="center" height="60"></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-weight: bold; text-decoration: underline;">KEPALA PONPES</td>
            <td colspan="{{ max(1, count($paymentTypes)) }}"></td>
            <td colspan="2" align="center" style="font-weight: bold; text-decoration: underline;">{{ strtoupper(auth()->user()->name ?? 'ADMINISTRATOR') }}</td>
        </tr>
    </table>
</html>