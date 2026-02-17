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
    try {

        $order = Order::findOrFail($orderId);

        if ($order->status === 'paid') {
            return back()->with('error', 'Order already paid.');
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => 'littlepay',
            'transaction_reference' => strtoupper(Str::random(20)),
            'amount' => $order->total,
            'status' => 'pending',
        ]);

        $url = "https://pay.little.africa/api/payments/" .
               env('LITTLEPAY_TOKEN_ID') . "/pay";

        $response = Http::withBasicAuth(
            env('LITTLEPAY_CLIENT_ID'),
            env('LITTLEPAY_CLIENT_SECRET')
        )->post($url, [
            "amount" => $payment->amount,
            "currency" => "KES",
            "description" => "Order #" . $order->id,
            "callbackUrl" => env('NGROK_URL') . "/payment/webhook",
            "key" => $payment->transaction_reference,
            "returnUrl" => route('orders.show', $order->id),
            "payload" => [
                "billingAddress" => [
                    "firstName" => "Customer",
                    "lastName" => "Name",
                    "email" => "customer@example.com",
                    "phoneNumber" => "+254712345678"
                ]
            ]
        ]);

        // 🔴 If API call fails
        if ($response->failed()) {

            Log::error('LittlePay API Failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return back()->with('error', 'Payment gateway error. Please try again.');
        }

        $responseData = $response->json();

        // ✅ Try multiple possible structures safely
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

        // ✅ FINAL REDIRECT
        return redirect()->away($checkoutUrl);

    } catch (\Exception $e) {

        Log::error('Payment Initiation Failed: ' . $e->getMessage());

        return back()->with('error', 'Payment initiation failed. Please try again.');
    }
}




    public function webhook(Request $request)
{
    Log::info('LittlePay Callback:', $request->all());

      // ✅ Save log FIRST
    SystemLog::create([
        'type' => 'littlepay_callback',
        'payload' => json_encode($request->all())
    ]);
    try {

        // ✅ Use key, not reference
        $key = $request->key;
        $status = $request->status;

        if (!$key) {
            Log::error('Missing key in callback');
            return response()->json(['error' => 'Invalid callback'], 400);
        }

        $payment = Payment::where('transaction_reference', $key)->first();

        if (!$payment) {
            Log::error('Payment not found for key: ' . $key);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Prevent double processing
        if ($payment->status === 'success') {
            return response()->json(['message' => 'Already processed']);
        }

        // ✅ Check for COMPLETED (uppercase)
        if ($status !== 'COMPLETED') {
            $payment->update(['status' => 'failed']);
            return response()->json(['message' => 'Payment failed']);
        }

        DB::transaction(function () use ($payment) {

            $order = $payment->order()->with('items.product')->first();

            if (!$order) {
                throw new \Exception('Order not found for payment');
            }

            foreach ($order->items as $item) {
                $item->product->decrement('quantity', $item->quantity);
            }

            $payment->update(['status' => 'success']);
            $order->update(['status' => 'paid']);
        });

        return response()->json(['message' => 'Payment processed']);

    } catch (\Exception $e) {

        Log::error('Webhook Error: ' . $e->getMessage());

        return response()->json(['error' => 'Server error'], 500);
    }



}

}
