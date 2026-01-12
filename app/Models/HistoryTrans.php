<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryTrans extends Model
{
    use HasFactory;

    protected $connection = 'third_sqlsrv';
    protected $table = 'HIS_TRANS';
    protected $fillable = [
        'NO_FAKTUR',
        'NO_TRANSAKSI',
        'K_KONS',
        'NO_BAYAR',
        'USERID',
        'TGL_INPUT',
        'JAM_INPUT',
        'SETOR',
        'POSKO',
        'CABANG',
    ];
    protected $keyType = 'string';
    protected $primaryKey = 'NO_FAKTUR';
    public $incrementing = false;
    public $timestamps = false;
}
