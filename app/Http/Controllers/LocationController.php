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
            'address' => 'required|string',
            'website' => 'nullable|string',
            'parking' => 'required|boolean',
            'barrierFree' => 'required|boolean',
            'description' => 'nullable|string'
        ]);

        $location = Location::create([
            'name' => $request['name'],
            'address' => $request['address'],
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
            'address' => 'required|string',
            'website' => 'nullable|string',
            'parking' => 'required|boolean',
            'barrierFree' => 'required|boolean',
            'description' => 'nullable|string'
        ]);

        $location->name = $request['name'];
        $location->address = $request['address'];
        $location->website = $request['website'];
        $location->parking = $request['parking'];
        $location->barrierFree = $request['barrierFree'];
        $location->description = $request['description'];
        $location->save();

        return $location;
    }
}
