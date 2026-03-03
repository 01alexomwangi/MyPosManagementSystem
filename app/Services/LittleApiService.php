<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LittleApiService
{
    public function getToken()
{
    try {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . config('services.little.basic_auth'),
        ])
        ->get(config('services.little.auth_url')); // ✅ GET not POST

        if (!$response->successful()) {
            throw new \Exception('Token fetch failed: ' . $response->body());
        }

        $data  = $response->json();
        $token = $data['token'] ?? null;

        if (!$token) {
            throw new \Exception('Token not found in response: ' . json_encode($data));
        }

        return $token;

    } catch (\Exception $e) {
        Log::error('Little Token Failed: ' . $e->getMessage());
        throw $e;
    }
}

                public function estimate($data)
            {
                $maxRetries = 3;
                $attempt    = 0;

                while ($attempt < $maxRetries) {
                    $attempt++;

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
                            if ($attempt < $maxRetries) {
                                usleep(300000); // wait 0.3s then retry
                                continue;
                            }
                            return ['success' => false, 'error' => $response->body()];
                        }

                        $body      = $response->json();
                        $estimates = $body['estimates'] ?? [];
                        $parcels   = collect($estimates)->firstWhere('vehicle', 'PARCELS');

                        if (!$parcels) {
                            if ($attempt < $maxRetries) {
                                usleep(300000);
                                continue;
                            }
                            return ['success' => false, 'error' => 'No PARCELS estimate available'];
                        }

                        $fee = (int) explode(' - ', $parcels['estimate'])[0];

                        return ['success' => true, 'fee' => $fee, 'raw' => $body];

                    } catch (\Exception $e) {
                        if ($attempt >= $maxRetries) {
                            return ['success' => false, 'error' => $e->getMessage()];
                        }
                        usleep(300000);
                    }
                }

                return ['success' => false, 'error' => 'Estimate failed after ' . $maxRetries . ' attempts'];
            }
    public function requestRide($data)
    {
        try {
            $token = $this->getToken(); // ✅ fresh token every time

            $payload = [
                "type"           => "CORPORATE",
                "driver"         => config('services.little.driver'),
                "upFrontPricing" => "true",
                "callbackUrl"    => env('NGROK_URL') . "/ride/webhook",
                "rider" => [
                    "mobileNumber" => config('services.little.rider_mobile'),
                    "name"         => config('services.little.rider_name'),
                    "email"        => config('services.little.rider_email'),
                    "picture"      => config('services.little.rider_picture'),
                ],
                "skipDrivers" => ["test@gmail.com"],
                "vehicle" => [
                    "type" => "PARCELS",
                    "details" => [
                        "itemCarried"      => "Order #" . $data['order_id'],
                        "size"             => "1",
                        "recipientName"    => $data['recipient_name'],
                        "recipientMobile"  => $data['recipient_mobile'],
                        "recipientAddress" => $data['dropoff_address'],
                        "contactPerson"    => config('services.little.rider_mobile'),
                        "deliveryNotes"    => $data['delivery_notes'] ?? '',
                        "typeOfAddress"    => "Delivery"
                    ]
                ],
                "pickUp" => [
                    "latlng"  => $data['pickup_latitude'] . ',' . $data['pickup_longitude'],
                    "address" => $data['pickup_address'],
                ],
                "dropOff" => [
                    "latlng"  => $data['dropoff_latitude'] . ',' . $data['dropoff_longitude'],
                    "address" => $data['dropoff_address'],
                ],
                "dropOffs"  => [],
                "corporate" => [
                    "corporateId" => config('services.little.corporate_id')
                ]
            ];

            //dd($payload);
            
            $response = Http::withToken($token) // ✅ fresh token
                ->post(config('services.little.ride_url'), $payload);

            if (!$response->successful()) {
                return ['success' => false, 'error' => $response->body()];
            }

            return ['success' => true, 'raw' => $response->json()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}