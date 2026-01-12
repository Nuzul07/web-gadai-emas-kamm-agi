<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NominatimService
{

    public function reverseGeoCode($latitude, $longtitude)
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->get("https://nominatim.openstreetmap.org/reverse", [
            'lat' => $latitude,
            'lon' => $longtitude,
            'format' => 'json'
        ]);

        return $response->json();
    }
}