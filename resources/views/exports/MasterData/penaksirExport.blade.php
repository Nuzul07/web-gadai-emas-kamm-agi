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
                    <th colspan="3" height="20" valign="middle" style="font-size: 15px;"><b>Penaksir Recap</b></th>
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
                        <b>Periode tanggal input : &nbsp;
                            @if ($strDate && $endDate)
                            <b>{{ $strDate }} s/d {{ $endDate }}</b>
                            @else
                            <b>Semua Periode</b>
                            @endif
                        </b>
                    </th>
                </tr>
                <tr>
                    <th colspan="3"><b>Jumlah Penaksir : {{ $penaksir->count() }} orang</b></th>
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
                    <th width="50" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>ID</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Nama</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Cabang</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Posko</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tanggal Input</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($penaksir as $pnk)
                <tr>
                    <td style="border: 2; text-align:center;">{{ $loop->iteration }}</td>
                    <td style="border: 2; text-align:center;">{{ $pnk->id }}</td>
                    <td style="border: 2; text-align:center;">{{ $pnk->name }}</td>
                    <td style="border: 2; text-align:center;">{{ $pnk->cabang }}</td>
                    <td style="border: 2; text-align:center;">{{ $pnk->posko }}</td>
                    <td style="border: 2; text-align:center;">{{ date('d-m-Y', strtotime($pnk->tgl_input)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>