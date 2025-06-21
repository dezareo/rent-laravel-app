<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'price_per_night',
        'number_of_beds',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
