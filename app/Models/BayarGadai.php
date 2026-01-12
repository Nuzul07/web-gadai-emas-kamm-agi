<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BayarGadai extends Model
{
    use HasFactory;

    protected $connection = 'third_sqlsrv';
    protected $table = 'Bayar';
    protected $fillable = [
        'No_bayar',
        'Tgl_byr',
        'No_faktur',
        'Jenis_trans',
        'Posko',
        'Cabang',
        'K_kons',
        'Tgl_due',
        'Pokok',
        'Bunga',
        'Denda',
        'Nilai_tebus',
        'Hasil_diterima',
        'TglTebus1',
        'cad4'
    ];
    protected $keyType = 'string';
    protected $primaryKey = 'No_setor';
    public $incrementing = false;
    public $timestamps = false;
}
