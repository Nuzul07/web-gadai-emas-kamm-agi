<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tcab';
    protected $keyType = 'string';
    protected $primaryKey = 'Kode_Cabang';
    public $incrementing = false;
}
