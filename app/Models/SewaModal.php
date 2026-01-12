<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewaModal extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_sewamodal';
    protected $keyType = 'string';
    protected $primaryKey = 'type_kendaraan';
    public $incrementing = false;
    public $timestamps = false;
}
