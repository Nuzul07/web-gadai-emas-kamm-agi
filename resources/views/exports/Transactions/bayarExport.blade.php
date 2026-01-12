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
                    <th colspan="3" height="20" valign="middle" style="font-size: 15px;"><b>Pembayaran Recap</b></th>
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
                    <th colspan="3"><b>Jumlah pembayaran : {{ $bayar->count() }} item</b></th>
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
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>No Bayar</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>No SBG</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Pokok Pinjaman</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Sewa Modal</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Denda Pinjaman</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Biaya Lain</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Total Pembayaran</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Cara Bayar</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Jenis Pembayaran</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Jenis Transaksi</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>User Input</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tanggal Bayar</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($bayar as $byr)
                <tr>
                    <td style="border: 2; text-align:center;">{{ $loop->iteration }}</td>
                    <td style="border: 2; text-align:center;">="{{ $byr->No_bayaran }}"</td>
                    <td style="border: 2; text-align:center;">="{{ $byr->No_sbg }}"</td>
                    <td style="border: 2; text-align:center;">Rp. {{ number_format($byr->Pokok_pinjaman, 0, ',', '.') }}</td>
                    <td style="border: 2; text-align:center;">="{{ $byr->Sewa_modal * 100 }} %"</td>
                    <td style="border: 2; text-align:center;">Rp. {{ number_format($byr->Denda_pinjaman, 0, ',', '.') }}</td>
                    <td style="border: 2; text-align:center;">Rp. {{ number_format($byr->By_lain, 0, ',', '.') }}</td>
                    <td style="border: 2; text-align:center;">Rp. {{ number_format($byr->Total_pembayaran, 0, ',', '.') }}</td>
                    <td style="border: 2; text-align:center;">{{ $byr->Cara_bayar }}</td>
                    <td style="border: 2; text-align:center;">{{ $byr->Jenis_pembayaran }}</td>
                    <td style="border: 2; text-align:center;">{{ $byr->Jenis_transaksi }}</td>
                    <td style="border: 2; text-align:center;">{{ $byr->User_input }}</td>
                    <td style="border: 2; text-align:center;">{{ date('d-m-Y', strtotime($byr->Tgl_pelunasan)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>