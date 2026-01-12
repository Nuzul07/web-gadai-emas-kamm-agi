<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 5mm;
        }

        body {
            margin: 0;
            padding: 0;
            color: #333;
            font-family: 'Times New Roman', Times, serif;
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

        .header h2 {
            padding: 0 18px;
            font-size: 15px;
            color: #333;
        }

        .section {
            padding: 10px;
            margin: 0;
            text-align: left;
        }

        .section h3 {
            padding: 5px;
            margin: 0;
            color: white;
            font-size: 12px;
            text-transform: uppercase;
        }

        .section h4 {
            margin: 0;
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
            margin: 0;
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
            <h4 style="text-align:center; text-decoration:underline; margin:0;">PERMOHONAN PEMBELIAN BARANG LELANG - GADAI EMAS</h4>
            <div class="section">
                <table>
                    <tr>
                        <td>
                            <h5>No Pengajuan : {{ $data1->NOMOR_LELANG }}</h5>
                        </td>

                    </tr>
                    <tr>
                        <td>
                            <h5>Nama Pembeli : {{ $data1->NAMA_PEMBELI }}</h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5>No HP : {{ $data1->NO_HP }}</h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5>Alamat Lengkap : {{ $data1->ALAMAT_PEMBELI }}</h5>
                        </td>
                    </tr>
                </table>
                <!-- <table class="two-column-table">
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td>
                                        <h5>No Pengajuan : 287482748927</h5>
                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <h5>Nama Pembeli : Testeerrrr</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>No HP : 48298784</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Alamat Lengkap : gvwbfhewyfewinfnuifg</h5>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table> -->
                <table border="1" style="padding: 0 12px;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="text-align: center;">
                                <h5>No</h5>
                            </th>
                            <th rowspan="2" style="text-align: center;">
                                <h5>No SBG</h5>
                            </th>
                            <th rowspan="2" style="text-align: center;">
                                <h5>Tgl Transaksi</h5>
                            </th>
                            <th rowspan="2" style="text-align: center;">
                                <h5>Tgl Jatuh Tempo</h5>
                            </th>
                            <th rowspan="2" style="text-align: center;">
                                <h5>Nama</h5>
                            </th>
                            <th rowspan="2" style="text-align: center;">
                                <h5>Kode Barang</h5>
                            </th>
                            <th rowspan="2" style="text-align: center;">
                                <h5>Jenis Emas</h5>
                            </th>
                            <th rowspan="2" style="text-align: center;">
                                <h5>Tenor</h5>
                            </th>
                            <th colspan="3" style="text-align: center;">Nilai Cair</th>
                            <th rowspan="2" style="text-align: center;">
                                <h5>Harga Penawaran</h5>
                            </th>
                            <th rowspan="2" style="text-align: center;">
                                <h5>Keterangan</h5>
                            </th>
                        </tr>
                        <tr>
                            <th style="text-align: center;">Taksiran</th>
                            <th style="text-align: center;">Denda</th>
                            <th style="text-align: center;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data2 as $index => $dt)
                        <tr>
                            <td style="text-align: center;">
                                <h5>{{ $index + 1 }}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ $dt->No_sbg }}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ \Carbon\Carbon::parse($dt->TANGGAL_PINJAM)->format('d-m-Y') }}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ \Carbon\Carbon::parse($dt->perGadaiOffline->Tgl_jtempo)->format('d-m-Y') }}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ $dt->nasabah->Nama}}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ $dt->Kode_barang }}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ $dt->NAMA_BARANG}}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ $dt->LAMA_PINJAM}}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ number_format($dt->barang->Taksiran, 0, ',', '.') }}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ number_format($dt->DENDA, 0, ',', '.') }}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ number_format($dt->NILAI_BUKU, 0, ',', '.') }}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ number_format($dt->NILAI_LELANG, 0, ',', '.') }}</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>{{ $dt->KETERANGAN }}</h5>
                            </td>
                        </tr>
                        @endforeach
                        <tr style="border-bottom: 1px solid;">
                            <td colspan="8" style="text-align:center;">
                                <h5>Total</h5>
                            </td>
                            <td style="text-align:center;">
                                <h5>{{ number_format($totalTaksiran, 0, ',', '.') }}</h5>
                            </td>
                            <td style="text-align:center;">
                                <h5>{{ number_format($totalDenda, 0, ',', '.') }}</h5>
                            </td>
                            <td style="text-align:center;">
                                <h5>{{ number_format($totalNilaiBuku, 0, ',', '.') }}</h5>
                            </td>
                            <td style="text-align:center;">
                                <h5>{{ number_format($totalNilaiLelang, 0, ',', '.') }}</h5>
                            </td>
                            <td style="text-align:center;">
                            </td>
                        </tr>
                    </tbody>
                    <!-- <tbody>
                        <tr>
                            <td style="text-align: center;">
                                <h5>1</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>2409080002</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>2024-09-19</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>2024-09-19</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>Nezura</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>6669793</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>Logam Mulya</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>120</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>12.000.000</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>4.000.000</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>16.000.000</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>17.000.000</h5>
                            </td>
                            <td style="text-align: center;">
                                <h5>fhewbfuewfbuewfew</h5>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid;">
                            <td colspan="8" style="text-align:center;">
                                <h5>Total</h5>
                            </td>
                            <td style="text-align:center;">
                                <h5>5.426.738</h5>
                            </td>
                            <td style="text-align:center;">
                                <h5>5.426.738</h5>
                            </td>
                            <td style="text-align:center;">
                                <h5>5.426.738</h5>
                            </td>
                            <td style="text-align:center;">
                                <h5>5.678.449</h5>
                            </td>
                            <td style="text-align:center;">
                            </td>
                        </tr>
                    </tbody> -->
                </table>
                <br>
                <h5 style="font-weight:normal; text-transform:uppercase; padding: 0 15px; margin:0;">Jati Makmur, {{ date('d/m/Y') }}</h5>
                <table style="padding: 0 15px;">
                    <thead>
                    </thead>
                    <tbody>
                        <tr style="line-height: 30px;">
                            <td style="text-align: center;">
                                <span>Diajukan Oleh,</span>
                                <br>
                                <br>
                                <br>
                                <span>(...........................)
                                    <hr style="width: 50%;">
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span>Mengetahui</span>
                                <br>
                                <br>
                                <br>
                                <span>Ronni
                                    <hr style="width: 50%;">
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span>Menyetujui</span>
                                <br>
                                <br>
                                <br>
                                <span>Bpk Budi Susanto
                                    <hr style="width: 50%;">
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>