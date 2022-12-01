<?php

namespace App\Http\Controllers;

use App\Models\Event;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function getEvents() {
        return Event::all();
    }

    public function createEvent(Request $request) {
        $request = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'flyer' => 'nullable|string',
            'date' => 'required|date',
            'doors' => 'nullable|string',
            'begin' => 'required|string',
            'location' => 'required|integer|min:1',
            'artists' => 'required|array|min:1',
            'artists.*' => 'integer|min:1'
        ]);

        $event = Event::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'flyer' => $request['flyer'],
            'date' => $request['date'],
            'doors' => $request['doors'],
            'begin' => $request['begin'],
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

        $request = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'flyer' => 'nullable|string',
            'date' => 'required|date',
            'doors' => 'nullable|string',
            'begin' => 'required|string',
            'location' => 'required|numeric|min:1'
        ]);

        $event->name = $request['name'];
        $event->description = $request['description'];
        $event->flyer = $request['flyer'];
        $event->date = $request['date'];
        $event->doors = $request['doors'];
        $event->begin = $request['begin'];
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
