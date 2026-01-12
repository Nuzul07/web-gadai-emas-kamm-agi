<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_kodep';
    protected $keyType = 'string';
    protected $primaryKey = 'KODE_POS';
    public $incrementing = false;
}
