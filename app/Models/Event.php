<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'flyer',
        'date',
        'doors',
        'begin',
        'ticketLink',
        'location_id'
    ];

    public function location() {
        return $this->hasOne(Location::class);
    }

    public function watchlists() {
        return $this->hasMany(Watchlist::class);
    }

    public function artists() {
        return $this->belongsToMany(Artist::class);
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }
}
