<?php

namespace App\Http\Controllers;

use App\Models\Friend;

use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function updateFriendStatus($userId, $friendId, $status) {
        if ($status == 'request') {
            $friend = Friend::where('user_id', '=', $userId)
                ->where('friend_id', '=', $friendId)
                ->first();

            if (!empty($friend)) {
                return response()->json(['errors' => "Freundschaftsanfrage wurde bereits versendet, aber noch nicht bestätigt."]);
            }

            $friend = Friend::create([
                'user_id' => $userId,
                'friend_id' => $friendId,
                'accepted' => false
            ]);

            return response()->json(['success' => "Die Freundschaftsanfrage wurde versendet."]);
        }
        
        if ($status == 'accept') {
            $friend = Friend::where('user_id', '=', $friendId)
                ->where('friend_id', '=', $userId)
                ->first();

            if (empty($friend)) return response()->json(['errors' => "Diese Freundschaftsanfrage existiert nicht."]);
            $friend->accepted = true;
            $friend->save();
            return response()->json(['success' => "Die Freundschaftsanfrage wurde akzeptiert."]);
        }

        if ($status == 'remove' || $status == 'deny') {
            $friend = Friend::where(function ($query) use ($userId, $friendId) {
                $query->where('user_id', '=', $userId)
                    ->orWhere('user_id', '=', $friendId);
            })->where(function ($query) use ($userId, $friendId) {
                $query->where('friend_id', '=', $userId)
                    ->orWhere('friend_id', '=', $friendId);
            })->delete();

            return response()->json(['success' => "Die Freundschaftsverbindung/-anfrage würde gelöscht."]);
        }
    }
}
