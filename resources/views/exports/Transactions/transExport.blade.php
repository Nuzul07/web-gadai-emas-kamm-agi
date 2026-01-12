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
                    <th colspan="3" height="20" valign="middle" style="font-size: 15px;"><b>Transaksi Recap</b></th>
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
                    <th colspan="3"><b>Jumlah transaksi : {{ $trans->count() }} item</b></th>
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
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>No SBG</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Kode Nasabah</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Penaksir</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tipe Transaksi</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Total Taksiran</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Maks Pinjaman</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Pokok Pinjaman</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Sewa Modal</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tgl Jatuh Tempo</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Biaya Admin</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tenor</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Cara Pencairan</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>No Rek Nasabah</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Referensi No SBG</b></th>
                    <th width="50" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Asal Barang</b></th>
                    <th width="55" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tujuan Transaksi</b></th>
                    <th width="50" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Instrumen Pembayaran</b></th>
                    <th width="50" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Pengembalian Uang Lebih</b></th>
                    <th width="50" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Fungsi Transaksi</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Golongan</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Posko</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Cabang</b></th>
                    <th width="20" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>User Input</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Status</b></th>
                    <th width="35" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tgl Transaksi</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($trans as $trs)
                <tr>
                    <td style="border: 2; text-align:center;">{{ $loop->iteration }}</td>
                    <td style="border: 2; text-align:center;">="{{ $trs->No_sbg }}"</td>
                    <td style="border: 2; text-align:center;">="{{ $trs->No_kons }}"</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Penaksir }}</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Type_transaksi }}</td>
                    <td style="border: 2; text-align:center;">Rp. {{ number_format($trs->Total_taksiran, 0, ',', '.') }}</td>
                    <td style="border: 2; text-align:center;">Rp. {{ number_format($trs->Maks_pinjaman, 0, ',', '.') }}</td>
                    <td style="border: 2; text-align:center;">Rp. {{ number_format($trs->Pokok_pinjaman, 0, ',', '.') }}</td>
                    <td style="border: 2; text-align:center;">="{{ $trs->Sewa_modal * 100 }} %"</td>
                    <td style="border: 2; text-align:center;">{{ date('d-m-Y', strtotime($trs->Tgl_jtempo)) }}</td>
                    <td style="border: 2; text-align:center;">Rp. {{ number_format($trs->By_admin, 0, ',', '.') }}</td>
                    <td style="border: 2; text-align:center;">="{{ $trs->Tenor }}"</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Cara_pencairan }}</td>
                    <td style="border: 2; text-align:center;">="{{ $trs->No_rek_nasabah }}"</td>
                    <td style="border: 2; text-align:center;">="{{ $trs->Referensi_nosbg }}"</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Asal_barang }}</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Tujuan_transaksi }}</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Instrumen_pembayaran }}</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Pengembalian_uang_lebih }}</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Fungsi_transaksi }}</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Golongan }}</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Posko }}</td>
                    <td style="border: 2; text-align:center;">{{ $trs->Cabang }}</td>
                    <td style="border: 2; text-align:center;">{{ $trs->User_input }}</td>
                    @if ($trs->Status == 1)
                    <td style="border: 2; text-align:center;">Aktif</td>
                    @else
                    <td style="border: 2; text-align:center;">Lunas</td>
                    @endif
                    <td style="border: 2; text-align:center;">{{ date('d-m-Y', strtotime($trs->Tgl_transaksi)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>