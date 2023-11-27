<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'deposit'
    ];
    protected $primaryKey='wallet_id';
    public $timestamps = false;

    public function user() {
        return $this->hasOne('App\Models\Wallet','user_id','user_id');
    }

}
