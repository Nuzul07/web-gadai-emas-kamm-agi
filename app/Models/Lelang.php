<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lelang extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'TB_AJU_LELANG';
    protected $keyType = 'string';
    protected $primaryKey = 'NOMOR_LELANG';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function perGadaiOffline()
    {
        return $this->belongsTo(PerGadaiOffline::class, 'No_sbg');
    }

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'No_kons');
    }

    public function barang()
    {
        return $this->belongsTo(BarangJaminan::class, 'Kode_barang', 'Kode_barang');
        // 'Kode_barang' in Lelang is the local key, 'Kode_barang' in BarangJaminan is the foreign key
    }

}
