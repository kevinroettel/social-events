<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;

use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function updateEntryStatus($userId, $eventId, $status) {
        $entry = Watchlist::where('user_id', $userId)
            ->where('event_id', $eventId)
            ->first();

        if (empty($entry)) {
            $newEntry = Watchlist::create([
                'user_id' => $userId,
                'event_id' => $eventId,
                'status' => $status
            ]);

            return $newEntry;
        } else if ($status == 'delete') {
            return $entry->delete();
        } else {
            $entry->status = $status;
            $entry->save();
            return $entry;
        }
    }
}
