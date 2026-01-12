<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_golongan';
    protected $keyType = 'string';
    protected $primaryKey = 'GOLONGAN';
    public $incrementing = false;
    public $timestamps = false;

    public function perGadaiOffline()
    {
        return $this->hasMany(PerGadaiOffline::class, 'Golongan');
    }
}
