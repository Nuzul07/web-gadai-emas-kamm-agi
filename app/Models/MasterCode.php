<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCode extends Model
{
    use HasFactory;

    protected $connection = 'second_sqlsrv';
    protected $table = 'tmaster_code';
    protected $keyType = 'string';
    protected $primaryKey = 'Code1';
    public $incrementing = false;
}
