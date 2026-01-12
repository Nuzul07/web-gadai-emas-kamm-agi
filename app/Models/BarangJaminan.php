<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangJaminan extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_transaksisub';
    protected $keyType = 'string';
    protected $primaryKey = 'No_sbg';
    public $incrementing = false;
    public $timestamps = false;

    public function perGadaiOffline()
    {
        return $this->belongsTo(PerGadaiOffline::class, 'No_sbg');
    }

    public function lelang()
    {
        return $this->hasMany(Lelang::class, 'Kode_barang', 'Kode_barang');
        // 'Kode_barang' in BarangJaminan references 'Kode_barang' in Lelang
    }
}
