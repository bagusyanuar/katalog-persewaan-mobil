<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rent_id',
        'account_name',
        'account_bank',
        'attachment',
        'status',
        'description'
    ];

    public function rent()
    {
        return $this->belongsTo(Rent::class, 'rent_id');
    }
}
