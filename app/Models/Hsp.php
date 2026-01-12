<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hsp extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_plafon';
    protected $keyType = 'string';
    protected $primaryKey = 'kode_kendaraan';
    public $incrementing = false;
    public $timestamps = false;
}
