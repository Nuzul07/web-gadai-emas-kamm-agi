<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JaminanMobil extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_jaminan_mobil';
    protected $keyType = 'string';
    protected $primaryKey = 'No_sbg';
    public $incrementing = false;
    public $timestamps = false;

    public function perGadaiOffline()
    {
        return $this->belongsTo(PerGadaiOffline::class, 'No_sbg');
    }
}