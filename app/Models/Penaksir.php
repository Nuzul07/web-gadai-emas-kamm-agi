<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penaksir extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_penaksir';
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
}
