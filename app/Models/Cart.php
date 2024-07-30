<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rent_id',
        'product_id',
        'driver_id',
        'price',
        'driver_price',
        'total'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rent()
    {
        return $this->belongsTo(Rent::class, 'rent_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'rent_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
