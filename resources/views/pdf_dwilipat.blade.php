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
            opacity: 0.3;
            z-index: 0;
            width: 100%;
            height: 100%;
            background: url('storage/image/pawnXpert-background-logo.png') no-repeat center;
            background-size: contain;
        }

        .content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="watermark"></div>
        <div class="content">
            <div class="header">
                <img src="storage/image/logo_agi.png" alt="">
                @if (Auth::user()->LEVEL_USER == 'pusat')
                <h2>Kantor Pusat Yang Mencetak</h2>
                @else
                <h2>Kantor Cabang : {{ $cabang->ket_cabang }}</h2>
                @endif
                <h2>Surat Duplikat Keperluan Arsip</h2>
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
                                        <h6>Tanggal: {{ $data->Tgl_transaksi }}</h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Uang Pinjaman: Rp. {{ number_format($data->Pokok_pinjaman, 0, ',', '.') }}</h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Administrasi: Rp. {{ number_format($data->By_admin, 0, ',', '.') }}</h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <h6>Jumlah Diterima:
                                            Rp. {{ number_format(($data->Pokok_pinjaman - $data->By_admin), 0, ',', '.') }}</h6>
                                    </th>
                                </tr>
                                <tr>
                                    @php
                                    use App\Helpers\NumberToWords;
                                    $text = NumberToWords::convertToWords($data->Pokok_pinjaman - $data->By_admin);
                                    @endphp
                                    <th>
                                        <h6>Terbilang: {{$text}}</h6>
                                    </th>
                                </tr>
                                @if ($data->Produk_gadai === 'EMAS')
                                <tr>
                                    <th>
                                        <h6>Sewa Modal: {{ $data->Sewa_modal * 100 }} % / 15 Hari</h6>
                                    </th>
                                </tr>
                                @elseif ($data->Produk_gadai === 'MOTOR' || $data->Produk_gadai === 'MOBIL')
                                <tr>
                                    <th>
                                        <h6>Bunga: {{ $data->Sewa_modal * 100 }} % | {{ number_format($data->Bunga, 0, ',', '.') }}</h6>
                                    </th>
                                </tr>
                                @endif
                                <tr>
                                    <th>
                                        <h6>Tanggal Jatuh Tempo: {{ $data->Tgl_jtempo }}</h6>
                                    </th>
                                </tr>
                            </table>
                            <h3>Data Nasabah</h3>
                            <table>
                                <tr>
                                    <td>
                                        <h5>Kode Nasabah : {{ $data->No_kons }}</h5>
                                    </td>
                                    <td>
                                        <h5>No KTP : {{$data->nasabah->No_ktp}}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Nama Lengkap : {{ $data->nasabah->Nama }}</h5>
                                    </td>
                                    <td>
                                        <h5>Email : {{ $data->nasabah->email }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5>Alamat Lengkap : {{ $data->nasabah->Alamat_Domisili }}</h5>
                                    </td>
                                    <td>
                                        <h5>No Telp : {{ $data->nasabah->No_tlp_HP }}</h5>
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
                                        <h4>{{ $data->No_sbg }}</h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Uang Pinjaman</h4>
                                        <h4>Rp. {{ number_format($data->Pokok_pinjaman, 0, ',', '.') }}</h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Tanggal Transaksi Pinjaman</h4>
                                        <h4>{{ $data->Tgl_transaksi }}</h4>
                                    </th>
                                </tr>
                                @if ($data->Produk_gadai === 'EMAS')
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Sewa Modal per 15 Hari</h4>
                                        <h4>{{ $data->Sewa_modal * 100 }} %</h4>
                                    </th>
                                </tr>
                                @elseif ($data->Produk_gadai === 'MOTOR' || $data->Produk_gadai === 'MOBIL')
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Bunga</h4>
                                        <h4>{{ $data->Sewa_modal * 100 }} %</h4>
                                    </th>
                                </tr>
                                @endif
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Tanggal Jatuh Tempo</h4>
                                        <h4>{{ $data->Tgl_jtempo }}</h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Jangka Waktu Pinjaman</h4>
                                        <h4>{{ $data->Tenor }} Hari</h4>
                                    </th>
                                </tr>
                                <tr>
                                    @if ($data->Produk_gadai === 'EMAS')
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Golongan</h4>
                                        <h4>{{ $data->Golongan }}</h4>
                                    </th>
                                    @endif
                                </tr>
                                <tr>
                                    <th style="text-align: center; font-size: 12px;">
                                        <h4>Nilai Taksiran</h4>
                                        <h4>Rp. {{ number_format($data->Total_taksiran, 0, ',', '.') }}</h4>
                                    </th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <h3>Deskripsi Barang Jaminan</h3>
                @if ($data->Produk_gadai === 'EMAS')
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
                <table>
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
                <table>
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
                                    <span>{{ $data->nasabah->Nama }}</span>
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
                                <span>{{ $data->nasabah->Nama }}</span>
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
                                <h5>Nomor SBG : {{ $data->No_sbg }}</h5>
                                @if ($data->Produk_gadai === 'EMAS')
                                <h5>Rubrik : {{ $data->golongan->RUBRIK }}</h5>
                                @elseif ($data->Produk_gadai === 'MOTOR')
                                <h5>Rubrik : MOTOR</h5>
                                @elseif ($data->Produk_gadai === 'MOBIL')
                                <h5>Rubrik : MOBIL</h5>
                                @endif
                                <h5>Nama Nasabah : {{ $data->nasabah->Nama }}</h5>
                                <h5>Tanggal Kredit : {{ $data->Tgl_transaksi }}</h5>
                                <h5>Tanggal Jatuh Tempo : {{ $data->Tgl_jtempo }}</h5>
                                @if ($data->Produk_gadai === 'EMAS')
                                <h5>Golongan : {{ $data->Golongan }}</h5>
                                @endif
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