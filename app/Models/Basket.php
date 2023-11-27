<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;
    protected $table='basket';
    protected $fillable=[
        'orders',
        'basket_date',
        'basket_total',
        'user_id',
        'basket_status'
    ];
    protected $guarded=['basket_id'];
    protected $primaryKey='basket_id';
    public $timestamps = false;
    protected $casts = [
        'orders' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
