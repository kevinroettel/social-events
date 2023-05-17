<?php

namespace App\Http\Controllers;

use App\Models\Tag;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    public function getTags() {
        return Tag::all();
    }

    public function createTag(Request $request) {
        $request = $request->validate([
            'name' => 'required|string|unique:tags'
        ]);

        $tag = Tag::create([
            'name' => $request['name']
        ]);

        return $tag;
    }

    public static function createTagFromArtist($tag) {
        $validator = Validator::make(
            ['name' => $tag],
            ['name' => 'required|unique:tags|string']
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $tag = Tag::create([
            'name' => $validated['name']
        ]);

        return $tag->id;
    }
}
