<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAILY ORDERS
    |--------------------------------------------------------------------------
    */
    public function dailyOrders()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        $query = Order::with(['items.product', 'user', 'location'])
            ->whereDate('created_at', $today);

        $orders = $this->applyRoleFilter($query, $user)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.custom', [
            'orders' => $orders,
            'from'   => $today->toDateString(),
            'to'     => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | WEEKLY ORDERS
    |--------------------------------------------------------------------------
    */
    public function weeklyOrders()
    {
        $user = Auth::user();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        $query = Order::with(['items.product', 'user', 'location'])
            ->whereBetween('created_at', [
                $startOfWeek->startOfDay(),
                $endOfWeek->endOfDay()
            ]);

        $orders = $this->applyRoleFilter($query, $user)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.custom', [
            'orders' => $orders,
            'from'   => $startOfWeek->toDateString(),
            'to'     => $endOfWeek->toDateString(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MONTHLY ORDERS
    |--------------------------------------------------------------------------
    */
    public function monthlyOrders($year = null, $month = null)
    {
        $user = Auth::user();

        $year  = $year  ?? date('Y');
        $month = $month ?? date('m');

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        $query = Order::with(['items.product', 'user', 'location'])
            ->whereBetween('created_at', [
                $startOfMonth->startOfDay(),
                $endOfMonth->endOfDay()
            ]);

        $orders = $this->applyRoleFilter($query, $user)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.custom', [
            'orders' => $orders,
            'from'   => $startOfMonth->toDateString(),
            'to'     => $endOfMonth->toDateString(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOM ORDER REPORT
    |--------------------------------------------------------------------------
    */
    public function customReport(Request $request)
    {
        $user = Auth::user();

        $from = $request->from;
        $to   = $request->to;

        $query = Order::with(['items.product', 'user', 'location']);

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

        return view('reports.custom', [
            'orders' => $orders,
            'from'   => $from,
            'to'     => $to,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RECEIPTS (COMPLETED ORDERS ONLY)
    |--------------------------------------------------------------------------
    */
    public function allReceipts(Request $request)
    {
        $user = Auth::user();

        $from = $request->from;
        $to   = $request->to;

        $query = Order::with(['items.product', 'user', 'location', 'payment'])
            ->where('status', 'completed'); // FIXED HERE

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

        return view('reports.receipts', [
            'orders' => $orders,
            'from'   => $from,
            'to'     => $to,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE FILTER
    |--------------------------------------------------------------------------
    */
    private function applyRoleFilter($query, $user)
    {
        switch ($user->role) {

            case 'admin':
                return $query;

            case 'manager':
                return $query->where('location_id', $user->location_id);

            case 'cashier':
                return $query->where('location_id', $user->location_id);

            default:
                return $query->where('location_id', $user->location_id);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        $from = $request->from;
        $to   = $request->to;

        $query = Order::with(['user', 'location']);

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

        $pdf = PDF::loadView('reports.pdf', compact('orders', 'from', 'to'));

        return $pdf->download('order-report.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */
    public function exportExcel(Request $request)
    {
        $from = $request->from;
        $to   = $request->to;

        return Excel::download(
            new SalesReportExport($from, $to),
            'order-report.xlsx'
        );
    }
}