<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LittleApiService
{
    // Example: store API key in config/services.php
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.little.api_key'); // make sure this exists
    }

    public function getToken()
    {
        // Return your API token
        return $this->apiKey;
    }

    public function estimate($data)
{
    try {
        $token = $this->getToken();

        $fromLatLng = $data['pickup_latitude'] . ',' . $data['pickup_longitude'];
        $toLatLng   = $data['dropoff_latitude'] . ',' . $data['dropoff_longitude'];

        $response = Http::withToken($token)
            ->get(config('services.little.estimate_url'), [
                'from_latlng' => $fromLatLng,
                'to_latlng'   => $toLatLng,
                'mobile'      => $data['customer_phone'],
            ]);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error'   => $response->body()
            ];
        }

        $body = $response->json();

        // ✅ Parse estimates array
        $estimates = $body['estimates'] ?? [];
        $parcels = collect($estimates)->firstWhere('vehicle', 'PARCELS');

        if (!$parcels) {
            return ['success' => false, 'error' => 'No PARCELS estimate available'];
        }

        // ✅ Get minimum fee from "200 - 210"
        $fee = (int) explode(' - ', $parcels['estimate'])[0];

        return [
            'success' => true,
            'fee'     => $fee,
            'raw'     => $body
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'error'   => $e->getMessage()
        ];
    }
}
}