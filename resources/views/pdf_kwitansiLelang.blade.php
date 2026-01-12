<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4;
            margin: 5mm;
        }

        body {
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            padding: 0;
            background: #fff;
            border-radius: 10px;
            margin: 0;
        }

        .header {
            text-align: left;
            color: #333;
        }

        .header img {
            float: right;
            height: 50px;
            width: 10%;
            margin-top: 5px;
            margin-right: 10px;
            padding: 0;
        }

        .header h2 {
            padding: 0 15px;
            margin: 10px 0;
            font-size: 15px;
            color: #333;
        }

        .section {
            margin-top: 10px;
            padding: 10px;
            border-radius: 8px;
            text-align: left;
        }

        .section h3 {
            padding: 5px;
            margin: 5px 0;
            color: white;
            font-size: 12px;
            text-transform: uppercase;
        }

        .section h4 {
            margin: 2px 0;
        }

        .section h5 {
            margin: 0;
        }

        .section h6 {
            margin: 0;
        }

        .section h4 {
            font-size: 12px;
        }

        .section h5 {
            font-size: 12px;
        }

        .section h6 {
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 5px;
        }

        th {
            font-size: 10px;
        }

        .two-column-table td {
            width: 50%;
            vertical-align: top;
        }

        .content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="content">
            <div class="header">
                <h2 style="font-size: large;">PT. GADAI DIGITAL MODERN</h2>
                <h2 style="font-weight: normal;">Posko : {{ $posko->ket_posko }}</h2>
                <h2 style="font-weight: normal;">Tanggal : {{ date('d/m/Y') }}</h2>
            </div>
            <h4 style="text-align:center; text-decoration:underline; margin:0;">KWITANSI LELANG - GADAI EMAS</h4>
            <div class="section">
                <table class="two-column-table">
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td style="width: 120%;">
                                        <h5>No Kwitansi : {{ $data1->TXT_1 }}</h5>
                                    </td>
                                    <td>
                                        <h5>Jenis Transaksi : LELANG</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 120%;">
                                        <h5>Nama Anggota : {{ $nama_anggota }}</h5>
                                    </td>
                                    <td>
                                        <h5>Tanggal / Jam : {{ date('d/m/Y - H:i:s') }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid;"></td>
                                    <td style="border-bottom: 1px solid;"></td>
                                </tr>
                            </table>
                            <table>
                                <thead>
                                    <tr style="border-bottom: 1px solid;">
                                        <th style="text-align: center;">
                                            <h5>No</h5>
                                        </th>
                                        <th style="text-align: center;">
                                            <h5>No SBG</h5>
                                        </th>
                                        <th style="text-align: center;">
                                            <h5>Jenis Emas</h5>
                                        </th>
                                        <th style="text-align: center;">
                                            <h5>Harga</h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data2 as $index => $dt)
                                    <tr style="border-bottom: 1px solid;">
                                        <td style="text-align: center;">
                                            <h5>{{ $index + 1 }}</h5>
                                        </td>
                                        <td style="text-align: center;">
                                            <h5>{{ $dt->No_sbg }}</h5>
                                        </td>
                                        <td style="text-align: center;">
                                            <h5>{{ $dt->NAMA_BARANG }}</h5>
                                        </td>
                                        <td style="text-align: center;">
                                            <h5>{{ number_format($dt->NILAI_LELANG, 0, ',', '.') }}</h5>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <h2 style="font-size:12px;">Total Pembelian : Rp. {{ number_format($totalNilaiLelang, 0, ',', '.') }}</h2>
                            @php
                            use App\Helpers\NumberToWords;
                            $text = NumberToWords::convertToWords($totalNilaiLelang);
                            @endphp
                            <h2 style="font-size:12px;">Terbilang : {{$text}}</h2>
                            <table>
                                <tr>
                                    <td style="line-height: 35px; text-align:center;">Pembeli <br><br><br>
                                        (Tanda Tangan dan Nama Jelas)
                                        <hr style="width: 55%;">
                                    </td>
                                    <td style="line-height: 35px; text-align:center;">Penerima <br><br><br>
                                        (Tanda Tangan dan Nama Jelas)
                                        <hr style="width: 55%;">
                                    </td>
                                </tr>
                            </table>
                            <h2 style="font-weight:normal; font-size:10px;">*Note : Barang Sudah Diterima Dengan Baik</h2>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>