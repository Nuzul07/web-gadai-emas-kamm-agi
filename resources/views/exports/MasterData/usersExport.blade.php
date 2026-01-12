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
                    <th colspan="3" height="20" valign="middle" style="font-size: 15px;"><b>Users Recap</b></th>
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
                        <b>Periode tanggal dibuat : &nbsp;
                            @if ($strDate && $endDate)
                            <b>{{ $strDate }} s/d {{ $endDate }}</b>
                            @else
                            <b>Semua Periode</b>
                            @endif
                        </b>
                    </th>
                </tr>
                <tr>
                    <th colspan="3"><b>Jumlah Users : {{ $users->count() }} akun</b></th>
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
                    <th width="50" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>User ID</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>User Name</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Level</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Posko</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Cabang</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Status</b></th>
                    <th width="30" bgcolor="#036aa1" height="20" style="border: 1 solid #000000; text-align:center; color:white; vertical-align:middle"><b>Tanggal Dibuat</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $usr)
                <tr>
                    <td style="border: 2; text-align:center;">{{ $loop->iteration }}</td>
                    <td style="border: 2; text-align:center;">{{ $usr->USER_ID }}</td>
                    <td style="border: 2; text-align:center;">{{ $usr->USER_NAME }}</td>
                    <td style="border: 2; text-align:center;">{{ $usr->LEVEL_USER }}</td>
                    <td style="border: 2; text-align:center;">{{ $usr->POSKO }}</td>
                    <td style="border: 2; text-align:center;">{{ $usr->CABANG }}</td>
                    @if ($usr->AKTIF == 1)
                    <td style="border: 2; text-align:center;">Aktif</td>
                    @else
                    <td style="border: 2; text-align:center;">Tidak Aktif</td>
                    @endif
                    <td style="border: 2; text-align:center;">{{ date('d-m-Y', strtotime($usr->CREATE_DATE)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>