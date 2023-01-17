<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    public function events() {
        return $this->belongsToMany(Event::class)->as('lineup');
    }
}
