<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorySetor extends Model
{
    use HasFactory;

    protected $connection = 'third_sqlsrv';
    protected $table = 'His_setor';
    protected $fillable = [
        'No_setor',
        'No_trans',
        'Tgl_by',
        'Tgl_trans',
        'Nama',
        'Nilai_rp',
        'posko',
        'cabang',
        'Type_trans',
        'Flag',
        'Tgl_setor'
    ];
    protected $keyType = 'string';
    protected $primaryKey = 'No_setor';
    public $incrementing = false;
    public $timestamps = false;
}
