<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
</head>

<body>
    <div style="font-family:Arial, Helvetica, sans-serif">
        <table style="border: 2 solid;">
            <thead>
                <tr>
                    <th colspan="3" height="20" valign="middle" style="font-size: 18px;"><b>Gadai Emas</b></th>
                </tr>
                <tr>
                    <th colspan="3" height="20" valign="middle" style="font-size: 15px;"><b>Nasabah Recap</b></th>
                </tr>
                <tr>
                    <th colspan="3"></th>
                </tr>
                <tr>
                    <th colspan="3"><b>Cabang : {{ auth()->user()->CABANG }}</b></th>
                </tr>
                <tr>
                    <th colspan="3"><b>Posko : {{ auth()->user()->POSKO }}</b></th>
                </tr>
                <tr>
                    <th colspan="3"></th>
                </tr>
                <tr>
                    <th colspan="3">
                        <b>Periode tanggal registrasi : &nbsp;
                            @if ($strDate && $endDate)
                            <b>{{ $strDate }} s/d {{ $endDate }}</b>
                            @else
                            <b>Semua Periode</b>
                            @endif
                        </b>
                    </th>
                </tr>
                <tr>
                    <th colspan="3"><b>Jumlah nasabah : {{ $nasabah->count() }} orang</b></th>
                </tr>
                <tr>
                    <th colspan="3"><b>Dibuat oleh user : {{ auth()->user()->USER_ID }}</b></th>
                </tr>
                <tr>
                    <th colspan="3"><b>Laporan ini dibuat tanggal : {{ now()->format('d-m-Y-H:i:s') }}</b></th>
                </tr>
                <tr>
                    <th colspan="3"></th>
                </tr>
                <tr>
                    <th width="5" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>No</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kode Nasabah</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Nama</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Nama Ibu</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>No Ktp</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tempat Lahir</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tanggal Lahir</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Jenis Kelamin</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>No Hp</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>No Tlp Rumah</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Email</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>No Rekening</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Nama Rekening</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Nama Bank</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Alamat Ktp</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Propinsi Ktp</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kabupaten Ktp</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kecamatan Ktp</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kelurahan Ktp</b></th>
                    <th width="50" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kodepos Ktp</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Alamat Domisili</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Propinsi Domisili</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kabupaten Domisili</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kecamatan Domisili</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kelurahan Domisili</b></th>
                    <th width="50" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kodepos Domisili</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Alamat Kerabat</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Propinsi Kerabat</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kabupaten Kerabat</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kecamatan Kerabat</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kelurahan Kerabat</b></th>
                    <th width="50" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kodepos Kerabat</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Posko</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Cabang</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tanggal Registrasi</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($nasabah as $nas)
                <tr>
                    <td style="border: 2; text-align:center;">{{ $loop->iteration }}</td>
                    <td style="border: 2; text-align:center;">="{{ $nas->K_Kons }}"</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Nama }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Nama_Ibu }}</td>
                    <td style="border: 2; text-align:center;">="{{ $nas->No_ktp }}"</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Tempat_Lahir }}</td>
                    <td style="border: 2; text-align:center;">{{ date('d-m-Y', strtotime($nas->Tgl_Lahir)) }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Jenis_Kelamin }}</td>
                    <td style="border: 2; text-align:center;">="{{ $nas->No_tlp_HP }}"</td>
                    <td style="border: 2; text-align:center;">="{{ $nas->No_tlp_rm }}"</td>
                    <td style="border: 2; text-align:center;">{{ $nas->email }}</td>
                    <td style="border: 2; text-align:center;">="{{ $nas->no_rekening }}"</td>
                    <td style="border: 2; text-align:center;">{{ $nas->nama_rekening }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->nama_bank }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Alamat_ktp }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Propinsi_ktp }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->kabupaten_ktp }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Kecamatan_ktp }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Kelurahan_ktp }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Kodepos_ktp }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Alamat_Domisili }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Propinsi_Domisili }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->kabupaten_Domisili }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Kecamatan_Domisili }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Kelurahan_Domisili }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Kodepos_Domisili }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Alamat_Kerabat }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Propinsi_Kerabat }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->kabupaten_Kerabat }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Kecamatan_Kerabat }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Kelurahan_Kerabat }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Kodepos_Kerabat }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Posko }}</td>
                    <td style="border: 2; text-align:center;">{{ $nas->Cabang }}</td>
                    <td style="border: 2; text-align:center;">{{ date('d-m-Y', strtotime($nas->tgl_regis)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>