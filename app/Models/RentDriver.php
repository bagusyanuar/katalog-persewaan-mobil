<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentDriver extends Model
{
    use HasFactory;

    protected $fillable = [
        'rent_id',
        'driver_id',
        'price'
    ];

    public function rent()
    {
        return $this->belongsTo(Rent::class, 'rent_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
