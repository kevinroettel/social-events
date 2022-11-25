<?php

namespace App\Http\Controllers;

use App\Models\Location;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getLocations() {
        return Location::all();
    }

    public function createLocation(Request $request) {
        $request = $request->validate([
            'name' => 'required|string',
            'streetAndNumber' => 'nullable|string',
            'city' => 'required|string',
            'website' => 'nullable|string',
            'parking' => 'required|string',
            'barrierFree' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $location = Location::create([
            'name' => $request['name'],
            'streetAndNumber' => $request['streetAndNumber'],
            'city' => $request['city'],
            'website' => $request['website'],
            'parking' => $request['parking'],
            'barrierFree' => $request['barrierFree'],
            'description' => $request['description']
        ]);

        return $location;
    }

    public function updateLocation(Request $request, $locationId) {
        $location = Location::find($locationId)->first();

        if (empty($location)) return false;

        $request = $request->validate([
            'name' => 'required|string',
            'streetAndNumber' => 'nullable|string',
            'city' => 'required|string',
            'website' => 'nullable|string',
            'parking' => 'required|string',
            'barrierFree' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $location->name = $request['name'];
        $location->streetAndNumber = $request['streetAndNumber'];
        $location->city = $request['city'];
        $location->website = $request['website'];
        $location->parking = $request['parking'];
        $location->barrierFree = $request['barrierFree'];
        $location->description = $request['description'];
        $location->save();

        return $location;
    }
}
