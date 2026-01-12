<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $connection = 'sqlsrv';
    protected $table = 'TBL_USER';
    protected $keyType = 'string';
    protected $primaryKey = 'USER_ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'USER_ID',
        'USER_NAME',
        'USER_PASSWORD'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    public function isSpv()
    {
        if ($this->LEVEL_USER === "gde_super, gde_admin, gde_kapos, gde_kacab") return true;
        return false;
    }

     public function getAuthPassword()
     {
        return $this->USER_PASSWORD;
     }

    public function history()
    {
        return $this->hasMany(History::class, 'user_id');
    }
}
