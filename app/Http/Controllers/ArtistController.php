<?php

namespace App\Http\Controllers;

use App\Models\Artist;

use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function getArtists() {
        return Artist::all();
    }

    public function createArtist(Request $request) {
        $request = $request->validate([
            'name' => 'required|string',
            'website' => 'nullable|string'
        ]);

        $artist = Artist::create([
            'name' => $request['name'],
            'website' => $request['website']
        ]);

        return $artist;
    }

    public static function createArtistFromNewEvent($artistName) {
        $artist = Artist::create(['name' => $artistName]);
        return $artist;
    }

    public function updateArtist(Request $request, $artistId) {
        $artist = Artist::find($artistId)->first();
        
        if (empty($artist)) return false;

        $request = $request->validate([
            'name' => 'required|string',
            'website' => 'nullable|string'
        ]);

        $artist->name = $request['name'];
        $artist->website = $request['website'];
        $artist->save();

        return $artist;
    }

    public function addTagToArtist($artistId, $tagId) {
        $artist = Artist::find($artistId)->first();
        $artist->tags()->attach($tagId);
    }

    public function removeTagFromArtist($artistId, $tagId) {
        $artist = Artist::find($artistId)->first();
        $artist->tags()->detach($tagId);
    }
}
