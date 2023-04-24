<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Location;
use App\Http\Controllers\ArtistController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function getEvents() {
        $events = Event::with(['artists', 'watchlists'])->get();
        
        foreach ($events as $event_index => $event) {
            $event_tags = [];
            
            foreach ($event->artists as $artist_index => $artist) {
                
                foreach ($artist->tags as $tag_index => $tag) {
                    array_push($event_tags, $tag->name);
                }
                
                $event->artists[$artist_index] = $artist->id;
            }

            foreach ($event->watchlists as $index => $watchlist) {
                $event->watchlists[$index] = $watchlist->only(['user_id', 'status']);
            }

            $event->tags = $event_tags;

            $event->location = Location::find($event->location_id);
        }

        return $events;
    }

    private function rules($isUpdate = false, $isFlyerPathString = false) {
        $rules = [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'flyer' => 'nullable|image',
            'date' => 'required|date',
            'doors' => 'nullable|string',
            'begin' => 'required|string',
            'ticketLink' => 'nullable|string',
            'location' => 'required|integer|min:1',
            'artists' => 'required|array|min:1',
        ];

        if ($isUpdate) unset($rules['artists']);
        if ($isFlyerPathString) $rules['flyer'] = 'nullable|string';

        return $rules;
    }
    
    public function createEvent(Request $request) {
        $request['artists'] = json_decode($request['artists']);

        $request = $request->validate($this->rules());

        $name = null;

        if (isset($request['flyer'])) {
            $file = $request['flyer'];
            $name = "/flyers/flyer-" . $request['name'] . "." . $file->extension();
            $file->storePubliclyAs('public', $name);
        }

        $event = Event::create([
            'name' => $request['name'],
            'description' => ($request['description'] ?? null),
            'flyer' => $name,
            'date' => $request['date'],
            'doors' => ($request['doors'] ?? null),
            'begin' => $request['begin'],
            'ticketLink' => ($request['ticketLink'] ?? null),
            'location_id' => $request['location']
        ]);

        foreach ($request['artists'] as $artist) {
            if (is_numeric($artist)) {
                $event->artists()->attach($artist);
            } else {
                $newArtist = ArtistController::createArtistByName($artist);
                $event->artists()->attach($newArtist->id);
            }
        }

        return Event::where('id', $event->id)->with(['artists', 'watchlists'])->first();
    }

    public function updateEvent(Request $request, $eventId) {
        $event = Event::find($eventId);

        if (empty($event)) return false;

        $isFlyerPathString = is_string($request['flyer']);

        $request = $request->validate($this->rules(true, $isFlyerPathString));

        if (isset($request['flyer']) && !$isFlyerPathString) {
            if ($event->flyer != null) {
                $oldImage = public_path() . '/storage' . $event->flyer;
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }

            $file = $request['flyer'];
            $name = "/flyers/flyer-" . $request['name'] . "." . $file->extension();
            $file->storePubliclyAs('public', $name);
            $request['flyer'] = $name;
        }

        $event->name = $request['name'];
        $event->description = ($request['description'] ?? null);
        $event->flyer = ($request['flyer'] ?? null);
        $event->date = $request['date'];
        $event->doors = ($request['doors'] ?? null);
        $event->begin = $request['begin'];
        $event->ticketLink = ($request['ticketLink'] ?? null);
        $event->location_id = $request['location'];
        $event->save();

        return Event::where('id', $eventId)->with(['artists'])->first();
    }

    public function addArtistToEvent($eventId, $artistId) {
        $event = Event::find($eventId);
        $event->artists()->attach($artistId);
    }

    public function removeArtistFromEvent($eventId, $artistId) {
        $event = Event::find($eventId);
        $event->artists()->detach($artistId);
    }
}
