<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
        }

        return $events;
    }

    public function createEvent(Request $request) {
        $request['artists'] = json_decode($request['artists']);
        
        // @TODO
        // find good method for json decode everything
        // cause null is getting parsed as a string fml

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
            'artists.*' => 'integer|min:1'
        ]);

        $name = null;

        if ($request['flyer'] != null) {
            $file = $request['flyer'];
            $name = "/flyers/flyer-" . $request['name'] . "." . $file->extension();
            $file->storePubliclyAs('public', $name);
        }

        $event = Event::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'flyer' => $name,
            'date' => $request['date'],
            'doors' => $request['doors'],
            'begin' => $request['begin'],
            'ticketLink' => $request['ticketLink'],
            'location_id' => $request['location']
        ]);

        foreach ($request['artists'] as $artist) {
            $event->artists()->attach($artist);
        }

        return $event;
    }

    public function updateEvent(Request $request, $eventId) {
        $event = Event::find($eventId)->first();

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
        $event->description = $request['description'];
        $event->flyer = $request['flyer'];
        $event->date = $request['date'];
        $event->doors = $request['doors'];
        $event->begin = $request['begin'];
        $event->ticketLink = $request['ticketLink'];
        $event->location_id = $request['location'];
        $event->save();

        return $event;
    }

    public function addArtistToEvent($eventId, $artistId) {
        $event = Event::find($eventId)->first();
        $event->artists()->attach($artistId);
    }

    public function removeArtistFromEvent($eventId, $artistId) {
        $event = Event::find($eventId)->first();
        $event->artists()->dettach($artistId);
    }
}
