<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bayar extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tb_bayar';
    protected $guarded = [];
    protected $keyType = 'string';
    protected $primaryKey = 'No_bayaran';
    public $incrementing = false;
    public $timestamps = false;

    public function perGadaiOffline()
    {
        return $this->belongsTo(PerGadaiOffline::class, 'No_sbg');
    }
}
