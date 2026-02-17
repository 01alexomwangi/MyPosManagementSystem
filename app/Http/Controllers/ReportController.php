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
    public function dailySales()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $query = Order::with(['items.product', 'user', 'location'])
            ->whereDate('created_at', $today)
            ->orderBy('location_id')
            ->orderBy('id');

        $orders = $this->applyRoleFilter($query, $user)->get();

        return view('reports.custom', [
            'sales' => $orders, // keep blade unchanged
            'from'  => $today,
            'to'    => null,
        ]);
    }

    public function weeklySales()
    {
        $user = Auth::user();

        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek   = Carbon::now()->endOfWeek()->toDateString();

        $query = Order::with(['items.product', 'user', 'location'])
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->orderBy('location_id')
            ->orderBy('id');

        $orders = $this->applyRoleFilter($query, $user)->get();

        return view('reports.custom', [
            'sales' => $orders,
            'from'  => $startOfWeek,
            'to'    => $endOfWeek,
        ]);
    }

    public function monthlySales($year = null, $month = null)
    {
        $user = Auth::user();

        $year  = $year ?? date('Y');
        $month = $month ?? date('m');

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        $query = Order::with(['items.product', 'user', 'location'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('location_id')
            ->orderBy('id');

        $orders = $this->applyRoleFilter($query, $user)->get();

        return view('reports.custom', [
            'sales' => $orders,
            'from'  => $startOfMonth->toDateString(),
            'to'    => $endOfMonth->toDateString(),
        ]);
    }

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

        return view('reports.custom', compact('orders', 'from', 'to'));
    }

    private function applyRoleFilter($query, $user)
    {
        if ($user->role == 'admin') {
            return $query;
        }

        if ($user->role == 'manager') {
            return $query->where('location_id', $user->location_id);
        }

        return $query->where('user_id', $user->id);
    }
}
