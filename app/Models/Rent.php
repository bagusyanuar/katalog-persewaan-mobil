<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'total',
        'date_rent',
        'date_return',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'rent_id');
    }

    public function rent_driver()
    {
        return $this->hasMany(RentDriver::class,'rent_id');
    }
}
