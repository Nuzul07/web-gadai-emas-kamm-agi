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
            font-size: 15px;
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

        .watermark {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 0;
            width: 100%;
            height: 100%;
            background: url('storage/image/pawnXpert-background-logo.png') no-repeat center;
            background-size: cover;
        }

        .content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="co-----ntainer">
        <div class="watermark"></div>
        <div class="content">
            <div class="header">
                <img src="storage/image/logo_agi.png" alt="">
                <h2>Kantor Cabang : </h2>
                <h2>Surat Bukti Gadai</h2>
            </div>
            <div class="section">
                <table class="two-column-table">
                    <tr>
                        <td>
                            <h3>Nota Penerimaan Uang</h3>
                            <table>
                                <tr>
                                    <th>
                                        <h6>Nota Transaksi ini merupakan satu kesatuan yang tidak terpisahkan dari Surat Bukti:</h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Nota transaksi penerimaan Uang - Kredit Baru</h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Tanggal: </h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Uang Pinjaman: </h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Administrasi: </h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Jumlah Diterima: </h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Terbilang: </h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Sewa Modal: </h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Tanggal Jatuh Tempo: </h6>
                                    </th>
                                </tr>
                            </table>
                            <h3>Data Nasabah</h3>
                            <table>
                                <tr>
                                    <td>
                                        <h5>Kode Nasabah : </h5>
                                    </td>
                                    <td>
                                        <h5>No KTP : </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>No Telp : </h5>
                                    </td>

                                    <td>
                                        <h5>Email : </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Nama Lengkap : </h5>
                                    </td>
                                    <td>
                                        <h5>Alamat Lengkap : </h5>
                                    </td>
                                </tr>
                            </table>

                        </td>
                        <td>
                            <h3 style="text-align: center;">Detail Transaksi</h3>
                            <table>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>No. Surat Bukti Gadai</h4>
                                        <h4></h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Uang Pinjaman</h4>
                                        <h4></h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Tanggal Transaksi Pinjaman</h4>
                                        <h4></h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Sewa Modal per 15 Hari</h4>
                                        <h4></h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Tanggal Jatuh Tempo</h4>
                                        <h4></h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Jangka Waktu Pinjaman</h4>
                                        <h4></h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Golongan</h4>
                                        <h4></h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Nilai Taksiran</h4>
                                        <h4></h4>
                                    </th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <h3>Deskripsi Barang Jaminan</h3>
                <table>
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
                    <!-- @foreach($data->barangEmas as $item)
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
                    @endforeach -->
                </table>
                <h3>Tanda Tangan</h3>
                <table>
                    <thead>
                        <tr>
                            <th colspan="2" style="text-align: center; border: 1px solid; vertical-align:middle;">
                                <h5>Surat Kuasa Jual</h5>
                            </th>
                            <th colspan="2" style="text-align: center; border: 1px solid; vertical-align:middle;">
                                <h5>Persetujuan Kredit</h5>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="line-height: 30px;">
                            <td style="text-align: center; border: 1px solid;">
                                <span>Pemberi Kuasa</sp>
                                    <br>
                                    <br>
                                    <span></span>
                            </td>
                            <td style="text-align: center; border: 1px solid;">
                                <span>Penerima Kuasa</span>
                                <br>
                                <br>
                                <span>Pejabat GDM</span>
                            </td>
                            <td style="text-align: center; border: 1px solid;">
                                <span>Nasabah</span>
                                <br>
                                <br>
                                <span></span>
                            </td>
                            <td style="text-align: center; border: 1px solid;">
                                <span>Pimpinan Cabang</span>
                                <br>
                                <br>
                                <span>Pejabat GDM</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <span>...........................................................................................................................................................................................</span>
                <h3>Kitir</h3>
                <table>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid;">
                                <h5>Nomor SBG : </h5>
                                <h5>Rubrik : </h5>
                                <h5>Nama Nasabah : </h5>
                                <h5>Tanggal Kredit : </h5>
                                <h5>Tanggal Jatuh Tempo : </h5>
                                <h5>Golongan : </h5>
                            </td>
                            <td style="text-align:center; vertical-align:top; border: 1px solid;">
                                <h5>Tanda Tangan Nasabah</h5>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>