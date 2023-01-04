<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'content',
        'answerTo'
    ];

    public function event() {
        return $this->belongsTo(Event::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function post() {
        return $this->belongsTo(Post::class, 'answerTo');
    }

    public function comments() {
        return $this->hasMany(Post::class, 'answerTo');
    }
}
