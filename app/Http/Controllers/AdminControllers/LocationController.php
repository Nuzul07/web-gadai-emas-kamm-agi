<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Services\NominatimService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $nominatimService;

    public function __construct(NominatimService $nominatimService) {
        $this->nominatimService = $nominatimService;
    }

    public function getAddress(Request $request)
    {
        $location = $request->location;

        [$latitude, $longtitude] = explode(',', $location);

        $response = $this->nominatimService->reverseGeoCode($latitude, $longtitude);
        // dd($response);

        if (isset($response['address'])) {
            return response()->json($response['address']);
        }

        return response()->json(['error' => 'Unable to fetch address'], 400);
    }
}
