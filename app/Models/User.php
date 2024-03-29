<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'profile_picture',
        'password',
        'address',
        'city'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function watchlists() {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Die ganze Freundes-Relations sind etwas komplizierter.
     * Da doppelte Einträge keinen Sinn machen, müssen hier Datensätze aus verschiedenen Abfragen zusammengefügt werden.
     * Zudem ist alles recht kleinschrittig aufgeteilt, damit man im nachhinein noch zusätzliche Freundesfunktionalitäten hinzufügen kann.
     */

    public function friendsTo() {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->withPivot('accepted')
            ->withTimestamps();
    }

    public function friendsFrom() {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
            ->withPivot('accepted')
            ->withTimestamps();
    }

    public function pendingFriendsTo() {
        return $this->friendsTo()->wherePivot('accepted', false);
    }

    public function pendingFriendsFrom() {
        return $this->friendsFrom()->wherePivot('accepted', false);
    }

    public function acceptedFriendsTo() {
        return $this->friendsTo()->wherePivot('accepted', true);
    }

    public function acceptedFriendsFrom() {
        return $this->friendsFrom()->wherePivot('accepted', true);
    }

    public function friends($userAttributes) {
        return $this->acceptedFriendsFrom
            ->merge($this->acceptedFriendsTo)
            ->transform(function ($friend, $key) use ($userAttributes) {
                return $friend->only($userAttributes);
            });
    }
}
