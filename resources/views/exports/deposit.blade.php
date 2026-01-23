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

    {{-- KOP LAPORAN --}}
    <table>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-weight: bold; font-size: 16px; height: 30px;">
                YAYASAN PONDOK PESANTREN AL-IKHLAS
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-style: italic; color: #555;">
                Jl. Raya Pesantren No. 123, Kota Santri, Indonesia | Telp: (021) 1234567
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colSpan }}" align="center" style="font-weight: bold; text-decoration: underline; font-size: 12px; height: 25px;">
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
                <th width="20" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #e0e0e0;">NO REGISTRASI</th>
                <th width="35" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #e0e0e0;">NAMA LENGKAP</th>
                
                {{-- Dynamic Header Item --}}
                @foreach($paymentTypes as $item)
                    <th width="20" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d1fae5;">{{ strtoupper($item['name']) }}</th>
                @endforeach

                <th width="25" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #3b82f6; color: #ffffff;">TOTAL</th>
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
                <td colspan="{{ 3 + count($paymentTypes) }}" style="border: 1px solid #000; font-weight: bold; text-align: right; height: 25px;">TOTAL SETORAN:</td>
                <td style="border: 1px solid #000; font-weight: bold; background-color: #3b82f6; color: #ffffff; text-align: right;">{{ $grandTotal }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- FORMAT TANDA TANGAN --}}
    <table>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="2" align="center">
                Kota Santri, {{ $tanggal }}
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">Mengetahui,</td>
            <td colspan="2" align="center">Penyetor / Admin,</td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="height: 60px;"></td> {{-- Spasi TTD --}}
            <td colspan="2" align="center" style="height: 60px;"></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-weight: bold; text-decoration: underline;">BENDAHARA YAYASAN</td>
            <td colspan="2" align="center" style="font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'ADMINISTRATOR' }}</td>
        </tr>
    </table>
</html>