<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurs extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_kurs';
    protected $keyType = 'date';
    protected $primaryKey = 'Tgl_input';
    public $incrementing = false;
    public $timestamps = false;
}
