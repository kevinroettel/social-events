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
        // $events = Event::where('date', '>=', Carbon::now()->toDateTimeString())->with('artists')->get();
        $events = Event::with(['artists', 'watchlists'])->get();
        
        foreach ($events as $index => $event) {
            foreach ($event->artists as $index => $artist) {
                $event->artists[$index] = $artist->id;
            }

            foreach ($event->watchlists as $index => $watchlist) {
                $event->watchlists[$index] = $watchlist->only(['user_id', 'status']);
            }

            $event->location = Location::find($event->location_id);
        }

        return $events;
    }

    public function createEvent(Request $request) {
        $request['artists'] = json_decode($request['artists']);

        $request = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'flyer' => 'nullable|image',
            'date' => 'required|date',
            'doors' => 'nullable|string',
            'begin' => 'required|string',
            'ticketLink' => 'nullable|string',
            'location' => 'required|integer|min:1',
            'artists' => 'required|array|min:1',
        ]);

        $name = null;

        if (isset($request['flyer'])) {
            $file = $request['flyer'];
            $name = "/flyers/flyer-" . $request['name'] . "." . $file->extension();
            $file->storePubliclyAs('public', $name);
        }

        $event = Event::create([
            'name' => $request['name'],
            'description' => (array_key_exists('description', $request) ? $request['description'] : null),
            'flyer' => $name,
            'date' => $request['date'],
            'doors' => (array_key_exists('doors', $request) ? $request['doors'] : null),
            'begin' => $request['begin'],
            'ticketLink' => (array_key_exists('ticketLink', $request) ? $request['ticketLink'] : null),
            'location_id' => $request['location']
        ]);

        // User kann hierdurch eine Quick-Create Möglichkeit nutzen um schneller einen neuen Künstler anzulegen
        foreach ($request['artists'] as $artist) {
            if (is_numeric($artist)) {
                $event->artists()->attach($artist);
            } else {
                $newArtist = ArtistController::createArtistByName($artist);
                $event->artists()->attach($newArtist->id);
            }
        }

        return Event::where('id', $event->id)->with(['artists'])->first();
    }

    public function updateEvent(Request $request, $eventId) {
        $event = Event::find($eventId);

        if (empty($event)) return false;

        $rules = [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'doors' => 'nullable|string',
            'begin' => 'required|string',
            'ticketLink' => 'nullable|string',
            'location' => 'required|numeric|min:1'
        ];

        $isFlyerPathString = is_string($request['flyer']);
        if ($isFlyerPathString) $rules['flyer'] = 'nullable|string';
        else $rules['flyer'] = 'nullable|image';

        $request = $request->validate($rules);

        if (!$isFlyerPathString) {
            $oldImage = public_path() . '/storage' . $event->flyer;
            unlink($oldImage);

            $file = $request['flyer'];
            $name = "/flyers/flyer-" . $request['name'] . "." . $file->extension();
            $file->storePubliclyAs('public', $name);
            $request['flyer'] = $name;
        }

        $event->name = $request['name'];
        $event->description = (array_key_exists('description', $request) ? $request['description'] : null);
        $event->flyer = $request['flyer'];
        $event->date = $request['date'];
        $event->doors = (array_key_exists('doors', $request) ? $request['doors'] : null);
        $event->begin = $request['begin'];
        $event->ticketLink = (array_key_exists('ticketLink', $request) ? $request['ticketLink'] : null);
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
