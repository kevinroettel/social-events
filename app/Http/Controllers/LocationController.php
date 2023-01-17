<?php

namespace App\Http\Controllers;

use App\Models\Location;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function getLocations() {
        return Location::all();
    }

    private $rules = [
        'name' => 'required|string',
        'streetAndNumber' => 'nullable|string',
        'city' => 'required|string',
        'website' => 'nullable|string',
        'parking' => 'required|string',
        'barrierFree' => 'required|string',
        'description' => 'nullable|string'
    ];

    public function createLocation(Request $request) {
        $validator = Validator::make($request->all(), $this->rules);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $location = Location::create([
            'name' => $validated['name'],
            'streetAndNumber' => $validated['streetAndNumber'],
            'city' => $validated['city'],
            'website' => $validated['website'],
            'parking' => $validated['parking'],
            'barrierFree' => $validated['barrierFree'],
            'description' => $validated['description']
        ]);

        return $location;
    }

    public function updateLocation(Request $request, $locationId) {
        $location = Location::find($locationId)->first();

        if (empty($location)) return false;

        $validator = Validator::make($request->all(), $this->rules);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $location->name = $validated['name'];
        $location->streetAndNumber = $validated['streetAndNumber'];
        $location->city = $validated['city'];
        $location->website = $validated['website'];
        $location->parking = $validated['parking'];
        $location->barrierFree = $validated['barrierFree'];
        $location->description = $validated['description'];
        $location->save();

        return $location;
    }
}
