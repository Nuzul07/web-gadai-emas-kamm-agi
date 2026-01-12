<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerGadaiOffline extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_transaksi';
    protected $guarded = [];
    protected $keyType = 'string';
    protected $primaryKey = 'No_sbg';
    public $incrementing = false;
    public $timestamps = false;

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'No_kons');
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class, 'Golongan');
    }

    public function bayar()
    {
        return $this->hasMany(Bayar::class, 'No_sbg');
    }

    public function barangEmas()
    {
        return $this->hasMany(BarangJaminan::class, 'No_sbg');
    }

    public function barangMotor()
    {
        return $this->hasMany(JaminanMotor::class, 'No_sbg');
    }

    public function barangMobil()
    {
        return $this->hasMany(JaminanMobil::class, 'No_sbg');
    }

    public function lelang()
    {
        return $this->hasMany(Lelang::class, 'No_sbg');
    }
}
