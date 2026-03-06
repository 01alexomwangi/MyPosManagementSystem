<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Location;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name')->get();

        return response()->json([
            'success'   => true,
            'locations' => $locations->map(function($location) {
                return [
                    'id'   => $location->id,
                    'name' => $location->name,
                ];
            })
        ]);
    }
}