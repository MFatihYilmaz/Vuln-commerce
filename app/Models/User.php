<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;
    
    protected $fillable = [
        'user_id',
        'user_name',
        'name',
        'surname',
        'password',
    ];
    protected $primaryKey='user_name';
    public $timestamps = false;

    public function getJWTIdentifier()
    {
         return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [

        ];
    }   

    public function basket()
    {
        return $this->hasMany('App\Models\Basket','user_id','user_id');
    }
    public function wallet() {
        return $this->hasOne('App\Models\Wallet','user_id','user_id');
    }
    public function token()
    {
        return $this->hasMany('App\Models\Token','user_name','user_name');
    }
  
 
  
}
