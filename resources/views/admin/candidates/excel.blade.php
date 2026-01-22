<table>
    <thead>
        {{-- KOP SURAT --}}
        <tr>
            <th colspan="25" style="text-align: center; font-weight: bold; font-size: 14px; height: 30px; vertical-align: middle;">
                DATA PENERIMAAN SANTRI BARU - {{ isset($school_name) ? strtoupper($school_name) : 'PONDOK PESANTREN' }}
            </th>
        </tr>
        <tr>
            <th colspan="25" style="text-align: center; font-weight: bold; font-size: 12px; vertical-align: middle;">
                JENIS KELAMIN: {{ $gender_label ?? '-' }} | TANGGAL EXPORT: {{ $date ?? date('d-m-Y') }}
            </th>
        </tr>
        <tr><th colspan="25"></th></tr>

        {{-- GROUP HEADERS --}}
        <tr>
            <th colspan="12" style="background-color: #4ade80; font-weight: bold; text-align: center; border: 1px solid #000000;">DATA PRIBADI</th>
            <th colspan="5" style="background-color: #60a5fa; font-weight: bold; text-align: center; border: 1px solid #000000;">ALAMAT DOMISILI</th>
            <th colspan="8" style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000000;">DATA ORANG TUA</th>
        </tr>

        {{-- COLUMN HEADERS --}}
        <tr style="background-color: #e5e7eb;">
            {{-- 1-12: PRIBADI --}}
            <th style="border: 1px solid #000000; font-weight: bold; width: 5px;">No</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">No Pendaftaran</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 30px;">Nama Lengkap</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 15px;">NISN</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">NIK</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">No KK</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 10px;">JK</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 15px;">Tempat Lahir</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 15px;">Tgl Lahir</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 10px;">Jenjang</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 25px;">Asal Sekolah</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 15px;">Status</th>

            {{-- 13-17: ALAMAT --}}
            <th style="border: 1px solid #000000; font-weight: bold; width: 30px;">Jalan/Dusun</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">Desa</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">Kecamatan</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">Kabupaten</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">Provinsi</th>

            {{-- 18-25: ORTU --}}
            <th style="border: 1px solid #000000; font-weight: bold; width: 25px;">Nama Ayah</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 15px;">Pekerjaan Ayah</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">HP Ayah</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">Gaji Ayah</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 25px;">Nama Ibu</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 15px;">Pekerjaan Ibu</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">HP Ibu</th>
            <th style="border: 1px solid #000000; font-weight: bold; width: 20px;">Gaji Ibu</th>
        </tr>
    </thead>
    <tbody>
        @foreach($candidates as $index => $c)
        <tr>
            {{-- DATA PRIBADI --}}
            <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000;">{{ $c->no_daftar }}</td>
            <td style="border: 1px solid #000000; font-weight: bold;">{{ $c->nama_lengkap }}</td>
            <td style="border: 1px solid #000000;">'{{ $c->nisn }}</td>
            <td style="border: 1px solid #000000;">'{{ $c->nik }}</td>
            <td style="border: 1px solid #000000;">'{{ $c->no_kk }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $c->jenis_kelamin }}</td>
            <td style="border: 1px solid #000000;">{{ $c->tempat_lahir }}</td>
            <td style="border: 1px solid #000000;">
                @if(!empty($c->tanggal_lahir))
                    {{ \Carbon\Carbon::parse($c->tanggal_lahir)->format('d-m-Y') }}
                @else - @endif
            </td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $c->jenjang }}</td>
            <td style="border: 1px solid #000000;">{{ $c->asal_sekolah }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $c->status }}</td>

            {{-- DATA ALAMAT (Cek apakah relasi address ada) --}}
            @if($c->address)
                <td style="border: 1px solid #000000;">{{ $c->address->alamat }}, RT {{ $c->address->rt }}/RW {{ $c->address->rw }}</td>
                <td style="border: 1px solid #000000;">{{ $c->address->desa }}</td>
                <td style="border: 1px solid #000000;">{{ $c->address->kecamatan }}</td>
                <td style="border: 1px solid #000000;">{{ $c->address->kabupaten }}</td>
                <td style="border: 1px solid #000000;">{{ $c->address->provinsi }}</td>
            @else
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">-</td>
            @endif

            {{-- DATA ORANG TUA (Cek apakah relasi parent ada) --}}
            @if($c->parent)
                <td style="border: 1px solid #000000;">{{ $c->parent->nama_ayah }}</td>
                <td style="border: 1px solid #000000;">{{ $c->parent->pekerjaan_ayah }}</td>
                <td style="border: 1px solid #000000;">'{{ $c->parent->no_hp_ayah }}</td>
                <td style="border: 1px solid #000000;">{{ $c->parent->penghasilan_ayah }}</td>
                
                <td style="border: 1px solid #000000;">{{ $c->parent->nama_ibu }}</td>
                <td style="border: 1px solid #000000;">{{ $c->parent->pekerjaan_ibu }}</td>
                <td style="border: 1px solid #000000;">'{{ $c->parent->no_hp_ibu }}</td>
                <td style="border: 1px solid #000000;">{{ $c->parent->penghasilan_ibu }}</td>
            @else
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">0</td>
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">-</td>
                <td style="border: 1px solid #000000;">0</td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>