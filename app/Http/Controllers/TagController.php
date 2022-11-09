<?php

namespace App\Http\Controllers;

use App\Models\Tag;

use Illuminate\Http\Request;

class TagController extends Controller
{
    public function getTags() {
        return Tag::all();
    }

    public function createTag(Request $request) {
        $request = $request->validate([
            'name' => 'required|string'
        ]);

        $tag = Tag::create([
            'name' => $request['name']
        ]);

        return $tag;
    }
}
