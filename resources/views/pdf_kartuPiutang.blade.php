<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 potrait;
            margin: -2mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: auto;
            padding: 20px;
            box-sizing: border-box;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .header-table td {
            vertical-align: top;
        }

        .header-table .title {
            text-align: left;
            font-size: 18px;
        }

        .header-table .date {
            font-size: 15px;
            text-align: right;
        }

        .info-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
            /* Reduced padding */
            margin: 0;
            /* Removed margin */
            vertical-align: top;
        }

        .info-table td:first-child,
        .info-table td:nth-child(3) {
            width: 40%;
            white-space: nowrap;
            /* Keeps the label and value together without breaking to the next line */
        }

        .info-table td:nth-child(2),
        .info-table td:nth-child(4) {
            width: 30%;
            margin-left: 10%;
        }

        .info-table td span strong {
            font-weight: bold;
            margin-right: 2px;
            /* Minimal space between label and value */
            display: inline;
            /* Ensures strong and value are on the same line */
        }

        .footer-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border: 1px solid #000;
            text-align: center;
        }

        .footer-table td span {
            display: inline-block;
            /* Keeps label and value on the same line */
            margin: 10px 0;
        }

        .footer-table td span strong {
            font-weight: bold;
            margin-right: 5px;
            /* Adds spacing between label and value */
        }
    </style>
</head>

<body>

    <div class="container">
        <table class="header-table">
            <tr>
                <td class="title">
                    <strong>PT. ANUGRAH GADAI INDONESIA</strong>
                </td>
                <td class=" date">
                    <p>Tgl: {{ date('d/m/Y') }}</p>
                    <p>Jam: {{ date('H:i:s') }}</p>
                </td>
            </tr>
        </table>
        <h1 style="text-align: center; font-size: 35px; margin:0; padding:0;">Kartu Piutang</h1>

        <table class="info-table">
            <tr>
                <td><span><strong>No SBG :</strong> {{ $data->No_sbg }}</span></td>
                @if ($data->Produk_gadai === 'EMAS')
                <td><span><strong>Jenis Emas :</strong> {{ $namaBrg }}</span></td>
                @elseif ($data->Produk_gadai === 'MOTOR')
                <td><span><strong>Jenis Motor :</strong> {{ $namaBrg }}</span></td>
                @elseif ($data->Produk_gadai === 'MOBIL')
                <td><span><strong>Jenis Mobil :</strong> {{ $namaBrg }}</span></td>
                @endif
            </tr>
            <tr>
                <td><span><strong>Tgl Transaksi :</strong> {{ $data->Tgl_transaksi }}</span></td>
                @if ($data->Produk_gadai === 'EMAS')
                <td><span><strong>Kadar :</strong> {{ $kadarBrg }}</span></td>
                @elseif ($data->Produk_gadai !== 'EMAS')
                <td><span><strong>Merk< : </strong> {{ $merk }}</span></td>
                @endif
            </tr>
            <tr>
                <td><span><strong>Jenis :</strong> RAHN</span></td>
                @if ($data->Produk_gadai === 'EMAS')
                <td><span><strong>Berat Kotor (gr) :</strong> {{ $beratK }}</span></td>
                @elseif ($data->Produk_gadai !== 'EMAS')
                <td><span><strong>Tipe< : </strong> {{ $tipe }}</span></td>
                @endif
            </tr>
            <tr>
                <td><span><strong>Posko :</strong> {{ $data->Posko }}</span></td>
                @if ($data->Produk_gadai === 'EMAS')
                <td><span><strong>Berat Bersih (gr) :</strong> {{ $beratB }}</span></td>
                @elseif ($data->Produk_gadai !== 'EMAS')
                <td><span><strong>Tahun< : </strong> {{ $tahun }}</span></td>
                @endif
            </tr>
            <tr>
                <td><span><strong>Kode Kons :</strong> {{ $data->No_kons }}</span></td>
                <td><span><strong>Nilai Cair :</strong> {{ number_format($data->Pokok_pinjaman, 0, ',', '.') }}</span></td>
            </tr>
            <tr>
                <td><span><strong>Nama Kons :</strong> {{ $data->nasabah->Nama }}</span></td>
                <td><span><strong>Bunga :</strong> {{ number_format($data->Bunga, 0, ',', '.') }}</span></td>
            </tr>
            <tr>
                <td><span><strong>Alamat :</strong> {{ $data->nasabah->Alamat_Domisili }}</span></td>
                <td><span><strong>Tenor :</strong> {{ $data->Tenor }} Hari</span></td>
            </tr>
            <tr>
                <td><span><strong>Kelurahan :</strong> {{ $data->nasabah->Kelurahan_Domisili }}</span></td>
                <td><span><strong>Kondisi :</strong> PEMAKAIAN</span></td>
            </tr>
            <tr>
                <td><span><strong>No Telp :</strong> {{ $data->nasabah->No_tlp_rm }}</span></td>
                <td><span><strong>No HP :</strong> {{ $data->nasabah->No_tlp_HP }}</span></td>
            </tr>
        </table>

        <table class="footer-table">
            <tr>
                <td style="border-right: 1px solid;">
                    <span><strong>Tgl JTempo :</strong>{{ $data->Tgl_jtempo }}</span><br>
                    <span><strong>Telat Hari :</strong> {{ $diffDaysBunga }}</span><br>
                    <span><strong>Denda Tertunggak :</strong> {{ number_format($piutang, 0, ',', '.') }}</span>
                </td>
                @if (count($data->bayar) > 0)
                <td style="border-right: 1px solid;">
                    <span><strong>No Bayar :</strong> {{ $data->bayar[0]['No_bayaran'] }}</span><br>
                    <span><strong>Tgl Bayar :</strong> {{ $data->bayar[0]['Tgl_pelunasan'] }}</span><br>
                    <span><strong>Denda Dibayar :</strong> {{ number_format($data->bayar[0]['Denda_pinjaman'], 0, ',', '.') }}</span><br>
                    <span><strong>Nilai Tebus :</strong> {{ number_format($data->bayar[0]['Total_pembayaran'], 0, ',', '.') }}</span><br>
                    <span><strong>Potongan Bunga :</strong> {{ number_format($potonganBunga, 0, ',', '.') }}</span>
                </td>
                @else
                <td style="border-right: 1px solid;">
                    <span><strong>No Bayar :</strong> </span><br>
                    <span><strong>Tgl Bayar :</strong> </span><br>
                    <span><strong>Denda Dibayar :</strong> </span><br>
                    <span><strong>Nilai Tebus :</strong> </span>
                </td>
                @endif
                <td style="border-right: 1px solid;">
                    <span><strong>Keterangan :</strong></span><br>
                    @if (count($data->bayar) > 0)
                    @if($data->bayar[0]['Jenis_transaksi'] === "PELUNASAN")
                    LUNAS
                    @elseif ($data->bayar[0]['Jenis_transaksi'] === "PERPANJANGAN")
                    LUNAS PERPANJANGAN
                    @endif
                    @else
                    <span></span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>

</html>