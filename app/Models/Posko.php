<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posko extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_posko';
    protected $keyType = 'string';
    protected $primaryKey = 'kd_posko';
    public $incrementing = false;
    public $timestamps = false;
}
