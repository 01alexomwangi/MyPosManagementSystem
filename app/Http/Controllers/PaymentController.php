<?php

namespace App\Http\Controllers;

use App\Order;
use App\Payment;
use App\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
   public function initiate($orderId)
{

    //  dd([
    //     'order_id'    => $orderId,
    //     'order'       => Order::find($orderId),
    // ]);
    try {

        $order = Order::findOrFail($orderId);

        if ($order->payments()->where('status','success')->exists()) {
            return back()->with('error', 'Order already paid.');
        }

       $payment = Payment::firstOrCreate(
        [
            'order_id' => $order->id,
            'method'   => 'littlepay',
            
        ],
        [
            'transaction_reference' => strtoupper(Str::random(20)),
            'status'   => 'pending',
            'amount' => $order->total,
        ]
       );

    $returnUrl = $order->source === 'online'
                 ? route('store.order.success', $order->id)
                 : route('orders.show', $order->id); 

        $url = "https://pay.little.africa/api/payments/" .
               env('LITTLEPAY_TOKEN_ID') . "/pay";


                $response = Http::withBasicAuth(
                        env('LITTLEPAY_CLIENT_ID'),
                        env('LITTLEPAY_CLIENT_SECRET')
                    )
                    ->acceptJson()
                    ->asJson()
                    ->post($url, [
                        "amount" =>(int) $payment->amount,
                        "currency" => "KES",
                        "description" => "Order #" . $order->id,
                        "callbackUrl" => env('NGROK_URL') . "/payment/webhook",
                        "key" => $payment->transaction_reference,
                        "returnUrl" => $returnUrl,
                        "payload" => [
                            "billingAddress" => [
                                "firstName" => "Alex",
                                "lastName" => "Mwangi",
                                "email" => "alexmwangi@gmail.com",
                                "phoneNumber" => "+254712345678",
                                "country" => "KE"
                            ]
                        ]
                    ]);

        if ($response->failed()) {

            Log::error('LittlePay API Failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return back()->with('error', 'Payment gateway error. Please try again.');
        }

        $responseData = $response->json();

        $checkoutUrl =
            $responseData['checkoutUrl']
            ?? $responseData['payment_url']
            ?? ($responseData['data']['checkoutUrl'] ?? null)
            ?? null;

        if (!$checkoutUrl) {

            Log::error('Checkout URL Missing From LittlePay Response', [
                'response' => $responseData
            ]);

            return back()->with('error', 'Invalid payment response from gateway.');
        }

        return redirect()->away($checkoutUrl);

    } catch (\Exception $e) {

        Log::error('Payment Initiation Failed: ' . $e->getMessage());

        return back()->with('error', 'Payment initiation failed. Please try again.');
    }
}



public function webhook(Request $request)
{
    Log::info('LittlePay Callback:', $request->all());

    SystemLog::create([
        'type' => 'littlepay_callback',
        'payload' => json_encode($request->all())
    ]);

    try {

        $data = $request->all();

        $key = $data['key'] ?? null;
        $status = strtoupper($data['status'] ?? '');

        if (!$key) {
            return response()->json(['error' => 'Invalid callback'], 400);
        }

        DB::transaction(function () use ($key, $status, $data) {

            $payment = Payment::where('transaction_reference', $key)
                              ->lockForUpdate()
                              ->first();

            if (!$payment) {
                throw new \Exception('Payment not found');
            }

            // ✅ Save gateway reference (VERY IMPORTANT)
            if (!empty($data['reference'])) {
                $payment->update([
                    'gateway_reference' => $data['reference']
                ]);
            }

            // Already processed
            if ($payment->status === 'success') {
                return;
            }

            if (!in_array($status, ['COMPLETED', 'SUCCESS', 'PAID'])) {
                $payment->update(['status' => 'failed']);
                return;
            }

            $order = $payment->order()
                             ->with('items.product')
                             ->lockForUpdate()
                             ->first();

            // ✅ Prevent double stock deduction
            if (in_array($order->status, ['processing', 'completed'])) {
                return;
            }

            foreach ($order->items as $item) {
                $item->product->decrement('quantity', $item->quantity);
            }

            $payment->update(['status' => 'success']);

            if ($order->source === 'pos') {
                $order->update(['status' => 'completed']);
            } else {
                $order->update(['status' => 'processing']);
            }
        });

        return response()->json(['message' => 'Payment processed']);

    } catch (\Exception $e) {

        Log::error('Webhook Error: ' . $e->getMessage());

        return response()->json(['error' => 'Server error'], 500);
    }
}



public function verify(Payment $payment)
{
    try {

        if ($payment->status === 'success') {
            return back()->with('info', 'Payment already verified.');
        }

        $url = "https://pay.little.africa/api/payments-v2/" .
       $payment->gateway_reference;

        $response = Http::withHeaders([
            'X-API-KEY' => env('LITTLEPAY_API_KEY'),
            'Accept' => 'application/json'
        ])->get($url);

        if ($response->failed()) {

            SystemLog::create([
                'type' => 'verify_failed',
                'payload' => json_encode([
                    'status' => $response->status(),
                    'body' => $response->body()
                ])
            ]);

            return back()->with('error', 'Gateway verification failed.');
        }

        $data = $response->json();

        $gatewayStatus = strtoupper(
            $data['data']['status'] ?? ''
        );

        DB::transaction(function () use ($payment, $gatewayStatus) {

            $order = $payment->order()
                             ->with('items.product')
                             ->lockForUpdate()
                             ->first();

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

        return back()->with('success', 'Payment verified successfully.');

    } catch (\Exception $e) {

        SystemLog::create([
            'type' => 'verify_exception',
            'payload' => $e->getMessage()
        ]);

        return back()->with('error', 'Verification crashed.');
    }
}

}