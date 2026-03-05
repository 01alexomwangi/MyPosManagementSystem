<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ✅ DAILY REPORT
    public function daily()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        $query = Order::with(['items.product', 'user', 'location', 'payments'])
                      ->whereDate('created_at', $today);

        $orders = $this->applyRoleFilter($query, $user)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'success'    => true,
            'period'     => 'daily',
            'date'       => $today->toDateString(),
            'total_orders'  => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'orders'     => $this->formatOrders($orders)
        ]);
    }

    // ✅ WEEKLY REPORT
    public function weekly()
    {
        $user        = Auth::user();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        $query = Order::with(['items.product', 'user', 'location', 'payments'])
                      ->whereBetween('created_at', [
                          $startOfWeek->startOfDay(),
                          $endOfWeek->endOfDay()
                      ]);

        $orders = $this->applyRoleFilter($query, $user)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'success'       => true,
            'period'        => 'weekly',
            'from'          => $startOfWeek->toDateString(),
            'to'            => $endOfWeek->toDateString(),
            'total_orders'  => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'orders'        => $this->formatOrders($orders)
        ]);
    }

    // ✅ MONTHLY REPORT
    public function monthly(Request $request)
    {
        $user  = Auth::user();
        $year  = $request->year  ?? date('Y');
        $month = $request->month ?? date('m');

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        $query = Order::with(['items.product', 'user', 'location', 'payments'])
                      ->whereBetween('created_at', [
                          $startOfMonth->startOfDay(),
                          $endOfMonth->endOfDay()
                      ]);

        $orders = $this->applyRoleFilter($query, $user)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'success'       => true,
            'period'        => 'monthly',
            'from'          => $startOfMonth->toDateString(),
            'to'            => $endOfMonth->toDateString(),
            'total_orders'  => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'orders'        => $this->formatOrders($orders)
        ]);
    }

    // ✅ CUSTOM REPORT
    public function custom(Request $request)
    {
        $user = Auth::user();
        $from = $request->from;
        $to   = $request->to;

        $query = Order::with(['items.product', 'user', 'location', 'payments']);

        if ($from && !$to) {
            $query->whereDate('created_at', $from);
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        }

        $orders = $this->applyRoleFilter($query, $user)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'success'       => true,
            'period'        => 'custom',
            'from'          => $from,
            'to'            => $to,
            'total_orders'  => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'orders'        => $this->formatOrders($orders)
        ]);
    }

    // ✅ SUMMARY REPORT
    public function summary()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        // Today
        $todayQuery = Order::whereDate('created_at', $today);
        $todayOrders = $this->applyRoleFilter($todayQuery, $user)->get();

        // This week
        $weekQuery = Order::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
        $weekOrders = $this->applyRoleFilter($weekQuery, $user)->get();

        // This month
        $monthQuery = Order::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ]);
        $monthOrders = $this->applyRoleFilter($monthQuery, $user)->get();

        // All time
        $allQuery  = Order::query();
        $allOrders = $this->applyRoleFilter($allQuery, $user)->get();

        return response()->json([
            'success' => true,
            'summary' => [
                'today' => [
                    'orders'  => $todayOrders->count(),
                    'revenue' => $todayOrders->sum('total'),
                ],
                'this_week' => [
                    'orders'  => $weekOrders->count(),
                    'revenue' => $weekOrders->sum('total'),
                ],
                'this_month' => [
                    'orders'  => $monthOrders->count(),
                    'revenue' => $monthOrders->sum('total'),
                ],
                'all_time' => [
                    'orders'  => $allOrders->count(),
                    'revenue' => $allOrders->sum('total'),
                ],
            ]
        ]);
    }

    // ✅ ROLE FILTER — same as web
    private function applyRoleFilter($query, $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where('location_id', $user->location_id);
    }

    // ✅ FORMAT ORDERS
    private function formatOrders($orders)
    {
        return $orders->map(function($order) {
            return [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'status'       => $order->status,
                'source'       => $order->source,
                'total'        => $order->total,
                'location'     => $order->location->name ?? 'N/A',
                'cashier'      => $order->user->name ?? 'N/A',
                'payment'      => $order->payments->first() ? [
                    'method' => $order->payments->first()->method,
                    'status' => $order->payments->first()->status,
                ] : null,
                'items_count'  => $order->items->count(),
                'created_at'   => $order->created_at->format('Y-m-d H:i'),
            ];
        });
    }
}