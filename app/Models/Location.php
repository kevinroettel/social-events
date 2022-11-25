<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'streetAndNumber',
        'city',
        'website',
        'parking',
        'barrierFree',
        'description'
    ];

    public function events() {
        return $this->belongsToMany(Event::class);
    }
}
