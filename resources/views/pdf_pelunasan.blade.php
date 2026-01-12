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
            height: 55px;
            width: 15%;
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
            background-color: #f59f0a;
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
            font-size: 16px;
        }

        .section h5 {
            font-size: 13px;
        }

        .section h6 {
            font-size: 13px;
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

        .watermark {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.3;
            z-index: 0;
            width: 90%;
            height: 100%;
            background: url('storage/image/lunas.png') no-repeat center;
            background-size: contain;
        }

        .watermark-sec {
            position: absolute;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.3;
            z-index: 0;
            width: 90%;
            height: 100%;
            background: url('storage/image/sudah-diambil.png') no-repeat center;
            background-size: contain;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="watermark"></div>
        <div class="content">
            <div class="header">
                <img src="storage/image/logo_agi.png" alt="">
                <h2>Kantor Cabang : {{ $cabang->ket_cabang }}</h2>
            </div>
            <div class="section">
                <table class="two-column-table">
                    <tr>
                        <td>
                            <h3>Bukti Pembayaran Gadai</h3>
                            <table>
                                <tr>
                                    <td>
                                        <h5>Kode Nasabah : {{ $data->No_kons }}</h5>
                                    </td>
                                    <td>
                                        <h5>No Pembayaran : {{$data->bayar[0]->No_bayaran}}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Nama Lengkap : {{ $data->nasabah->Nama }}</h5>
                                    </td>
                                    <td>
                                        <h5>Tanggal Pembayaran : {{ $data->bayar[0]->Tgl_pelunasan }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid;">
                                        <h5>Alamat Lengkap : {{ $data->nasabah->Alamat_Domisili }}</h5>
                                    </td>
                                    <td style="border-bottom: 1px solid;"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>No. SBG : {{ $data->bayar[0]->No_sbg }}</h5>
                                    </td>
                                    <td>
                                        <h5>Pembayaran</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Tanggal Transaksi : {{ $data->Tgl_transaksi }}</h5>
                                    </td>
                                    <td>
                                        <h5>Pokok Pinjaman : Rp. {{ number_format($data->bayar[0]->Pokok_pinjaman, 0, ',', '.') }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Tanggal Jatuh Tempo : {{ $data->Tgl_jtempo }}</h5>
                                    </td>
                                    @if ($data->Produk_gadai === 'EMAS')
                                    <td>
                                        <h5>Sewa Modal : Rp. {{ number_format($data->bayar[0]->Sewa_modal * $data->bayar[0]->Pokok_pinjaman, 0, ',', '.')}} ({{ $data->bayar[0]->Sewa_modal * 100 }})%</h5>
                                    </td>
                                    @elseif ($data->Produk_gadai === 'MOTOR' || $data->Produk_gadai === 'MOBIL')
                                    <td>
                                        <h5>Bunga : Rp. {{ number_format($data->bayar[0]->Sewa_modal * $data->bayar[0]->Pokok_pinjaman, 0, ',', '.')}} ({{ $data->bayar[0]->Sewa_modal * 100 }})%</h5>
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Jumlah Pinjaman : Rp. {{ number_format($data->bayar[0]->Pokok_pinjaman, 0, ',', '.') }}</h5>
                                    </td>
                                    <td>
                                        <h5>Denda Pinjaman : Rp. {{ number_format($data->bayar[0]->Denda_pinjaman, 0, ',', '.') }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td style="border-bottom: 1px solid;">
                                        <h5>Biaya Lain : Rp. {{ number_format($data->bayar[0]->By_lain, 0, ',', '.') }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid;"></td>
                                    <td style=" border-bottom: 1px solid;">
                                        <h5>Total Pembayaran : Rp. {{ number_format($data->bayar[0]->Total_pembayaran, 0, ',', '.') }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    @php
                                    use App\Helpers\NumberToWords;
                                    $text = NumberToWords::convertToWords($data->bayar[0]->Total_pembayaran);
                                    @endphp
                                    <td style="border-bottom: 1px solid;"></td>
                                    <td style=" border-bottom: 1px solid;">
                                        <h5>Terbilang : {{$text}}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid; line-height: 35px; text-align:center;">Petugas <br><br>
                                        (...............................)
                                        <hr style="width: 50%;">
                                    </td>
                                    <td style="border-bottom: 1px solid; line-height: 35px; text-align:center;">Nasabah <br><br>
                                        {{ $data->nasabah->Nama }}
                                        <hr style="width: 50%;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <hr style="border-style: dotted;">
            <div class="watermark-sec"></div>
            <div class="header">
                <img src="storage/image/logo_agi.png" alt="">
                <h2>Kantor Cabang : {{ $cabang->ket_cabang }}</h2>
            </div>
            <div class="section">
                <table class="two-column-table">
                    <tr>
                        <td>
                            <h3>Bukti Pengembalian Barang</h3>
                            <table>
                                <tr>
                                    <td>
                                        <h5>Kode Nasabah : {{ $data->No_kons }}</h5>
                                    </td>
                                    <td>
                                        <h5>No SBG : {{$data->No_sbg}}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Nama Lengkap : {{ $data->nasabah->Nama }}</h5>
                                    </td>
                                    <td>
                                        <h5>Tanggal Transaksi : {{ $data->Tgl_transaksi }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Alamat Lengkap : {{ $data->nasabah->Alamat_Domisili }}</h5>
                                    </td>
                                    <td>
                                        <h5>Tanggal Pengembalian : {{ $data->bayar[0]->Tgl_pelunasan }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Status Pembayaran : Lunas</h5>
                                    </td>
                                    <td>
                                        <h5>Status Barang Pegadaian : Di Keluarkan</h5>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                @if ($data->Produk_gadai === 'EMAS')
                <table style="border: 1px solid;">
                    <tr>
                        <th style="text-align: center;">
                            <h5>Kode Barang</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Jenis</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Kadar (Karat)</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Berat Kotor (Gram)</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Berat Bersih (Gram)</h5>
                        </th>
                    </tr>
                    @foreach($data->barangEmas as $item)
                    <tr>
                        <td style="text-align: center;">
                            <h5>{{ $item->Kode_barang }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Jenis }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Kadar }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ number_format($item->Berat_kotor, 2) }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ number_format($item->Berat_bersih, 2) }}</h5>
                        </td>
                    </tr>
                    @endforeach
                </table>
                @elseif ($data->Produk_gadai === 'MOTOR')
                <table style="border: 1px solid;">
                    <tr>
                        <th style="text-align: center;">
                            <h5>Kode Barang</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Merk</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Tipe</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Nopol</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Tahun</h5>
                        </th>
                    </tr>
                    @foreach($data->barangMotor as $item)
                    <tr>
                        <td style="text-align: center;">
                            <h5>{{ $item->Kode_barang }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Merk }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Tipe }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Nopol }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Tahun }}</h5>
                        </td>
                    </tr>
                    @endforeach
                </table>
                @elseif ($data->Produk_gadai === 'MOBIL')
                <table style="border: 1px solid;">
                    <tr>
                        <th style="text-align: center;">
                            <h5>Kode Barang</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Merk</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Tipe</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Nopol</h5>
                        </th>
                        <th style="text-align: center;">
                            <h5>Tahun</h5>
                        </th>
                    </tr>
                    @foreach($data->barangMobil as $item)
                    <tr>
                        <td style="text-align: center;">
                            <h5>{{ $item->Kode_barang }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Merk }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Tipe }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Nopol }}</h5>
                        </td>
                        <td style="text-align: center;">
                            <h5>{{ $item->Tahun }}</h5>
                        </td>
                    </tr>
                    @endforeach
                </table>
                @endif
                <table class="two-column-table">
                    <tr>
                        <td style="border-bottom: 1px solid; line-height: 35px; text-align:center;">Petugas <br><br>
                            (...............................)
                            <hr style="width: 50%;">
                        </td>
                        <td style="border-bottom: 1px solid; line-height: 35px; text-align:center;">Nasabah <br><br>
                            {{ $data->nasabah->Nama }}
                            <hr style="width: 50%;">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>