<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Payment;
use App\SystemLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function verify(Payment $payment)
    {
        try {
            if (!$payment->gateway_reference) {
                return response()->json([
                    'success' => false,
                    'message' => 'No gateway reference found. Payment may not have been processed yet.'
                ], 400);
            }

            if ($payment->status === 'success') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already verified.'
                ], 400);
            }

            $url = "https://pay.little.africa/api/payments-v2/" .
                   $payment->gateway_reference;

            $response = Http::withHeaders([
                'X-API-KEY' => env('LITTLEPAY_API_KEY'),
                'Accept'    => 'application/json'
            ])->get($url);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gateway verification failed.'
                ], 400);
            }

            $data          = $response->json();
            $gatewayStatus = strtoupper($data['data']['status'] ?? '');

            DB::transaction(function () use ($payment, $gatewayStatus) {
                $order = $payment->order()
                                 ->with('items.product')
                                 ->lockForUpdate()
                                 ->first();

                if (!$order) {
                    throw new \Exception('Order not found for this payment.');
                }

                if (in_array($gatewayStatus, ['COMPLETED', 'SUCCESS', 'PAID'])) {
                    if ($order->status !== 'processing') {
                        foreach ($order->items as $item) {
                            $item->product->decrement('quantity', $item->quantity);
                        }
                        $order->update(['status' => 'processing']);
                    }
                    $payment->update(['status' => 'success']);
                } else {
                    $payment->update(['status' => 'failed']);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully.',
                'status'  => $payment->fresh()->status,
            ]);

        } catch (\Exception $e) {
            SystemLog::create([
                'type'    => 'verify_exception',
                'payload' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }
}