<?php

namespace App\Http\Controllers;

use App\Models\Post;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function createPost($eventId, Request $request) {
        $request = $request->validate([
            'user_id' => 'required|integer|min:1',
            'event_id' => 'required|integer|min:1',
            'content' => 'required|string',
            'answerTo' => 'nullable|integer'
        ]);

        $post = Post::create([
            'user_id' => $request['user_id'],
            'event_id' => $request['event_id'],
            'content' => $request['content'],
            'answerTo' => $request['answerTo'],
        ]);

        return $post;
    }

    public function getEventPosts($eventId) {
        $posts = Post::where('event_id', '=', $eventId)
            ->where('answerTo', '=', null)
            ->with('comments')
            ->latest()
            ->get();

        return $posts;
    }
}
