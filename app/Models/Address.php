<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'address',
        'address_header'
    ];
    protected $hidden = [
        'user_id'
    ];
    public $timestamps = false;
}
