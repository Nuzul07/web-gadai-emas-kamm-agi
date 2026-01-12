<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_kons';
    protected $keyType = 'string';
    protected $primaryKey = 'K_Kons';
    public $incrementing = false;
    public $timestamps = false;

    public function perGadaiOffline()
    {
        return $this->hasMany(PerGadaiOffline::class, 'No_kons');
    }

    public function lelang()
    {
        return $this->hasMany(Lelang::class, 'No_kons');
    }
}
