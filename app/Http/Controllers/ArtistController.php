<?php

namespace App\Http\Controllers;

use App\Models\Artist;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArtistController extends Controller
{
    public function getArtists() {
        return Artist::with('tags')->get();
    }

    /**
     * @TODO
     * Überprüfen ob der Name bereits vorhanden ist
     * Überprüfen ob ein Vertipper in dem Namen ist und der Künstler eigentlich schon existiert
     */
    public static function createArtistByName($name) {
        $validator = Validator::make(
            ['name' => $name], 
            ['name' => 'required|unique:artists|string']
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $artist = Artist::create([
            'name' => $validated['name']
        ]);

        return $artist;
    }

    public function createArtist(Request $request) {
        $request = $request->validate(['name' => 'required|unique:artists|string']);
        $artist = Artist::create(['name' => $request['name']]);
        return $artist;
    }

    public function updateArtist(Request $request, $artistId) {
        $artist = Artist::find($artistId)->first();
        
        if (empty($artist)) return false;

        $request = $request->validate([
            'name' => 'required|unique:artists|string'
        ]);

        $artist->name = $request['name'];
        $artist->save();

        return $artist;
    }

    public function addTagToArtist($artistId, $tagId) {
        $artist = Artist::find($artistId);
        $artist->tags()->attach($tagId);
    }

    public function removeTagFromArtist($artistId, $tagId) {
        $artist = Artist::find($artistId);
        $artist->tags()->detach($tagId);
    }
}
