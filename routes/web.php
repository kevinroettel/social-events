<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/register', function () { return view('auth.register'); });
Route::post('/register', [UserController::class, 'register'])->name('register');

Route::get('/login', function () { return view('auth.login'); });
Route::post('/login', [LoginController::class, 'authentication'])->name('login');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/', function () { return view('app'); });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/artists', [ArtistController::class, 'getArtists']);
    Route::post('/artists', [ArtistController::class, 'createArtist']);
    Route::post('/artists/{artistId}', [ArtistController::class, 'updateArtist']);
    Route::patch('/artists/{artistId}/tags/{tagId}', [ArtistController::class, 'addTagToArtist']);
    Route::delete('/artists/{artistId}/tags/{tagId}', [ArtistController::class, 'removeTagFromArtist']);
    
    Route::get('/events', [EventController::class, 'getEvents']);
    Route::post('/events', [EventController::class, 'createEvent']);
    Route::post('/events/{eventId}', [EventController::class, 'updateEvent']);
    Route::patch('/events/{eventId}/artists/{artistId}', [EventController::class, 'addArtistToEvent']);
    Route::delete('/events/{eventId}/artists/{artistId}', [EventController::class, 'removeArtistFromEvent']);
    
    Route::get('/locations', [LocationController::class, 'getLocations']);
    Route::post('/locations', [LocationController::class, 'createLocation']);
    Route::post('/locations/{locationId}', [LocationController::class, 'updateLocation']);
    
    Route::get('/tags', [TagController::class, 'getTags']);
    Route::post('/tags', [TagController::class, 'createTag']);
    
    Route::post('/users/{userId}', [UserController::class, 'updateProfile']);
    Route::patch('/users/{userId}/watchlists/{eventId}/{status}', [WatchlistController::class, 'updateEntryStatus']);
});