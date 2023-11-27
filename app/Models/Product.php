<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory,Notifiable;
    protected $table='products';
    protected $fillable = [
        'category',
        'product_name',
        'product_price',
        'product_image',
        'product_description'
        
    ];
    public $timestamps = false;
    

}