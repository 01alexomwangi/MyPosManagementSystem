<?php

namespace App\Http\Controllers;


use App\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;


use Carbon\Carbon;

class ReportController extends Controller
{
       /**
     * Daily Sales Report
     */
    public function dailySales()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $query = Sale::with(['items.product', 'user', 'location'])
                     ->whereDate('created_at', $today)
                     ->orderBy('location_id')
                     ->orderBy('id');

        $sales = $this->applyRoleFilter($query, $user)->get();

        return view('reports.custom', [
    'sales' => $sales,
    'from'  => $today,
    'to'    => null,
      ]);
    }

    /**
     * Weekly Sales Report
     */
    public function weeklySales()
    {
        $user = Auth::user();

        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        $query = Sale::with(['items.product', 'user', 'location'])
                     ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                     ->orderBy('location_id')
                     ->orderBy('id');

        $sales = $this->applyRoleFilter($query, $user)->get();

        return view('reports.custom', [
    'sales' => $sales,
    'from'  => $startOfWeek,
    'to'    => $endOfWeek,
    ]);
    }

    /**
     * Monthly Sales Report
     */
    public function monthlySales($year = null, $month = null)
    {
        $user = Auth::user();

        $year = $year ?? date('Y');
        $month = $month ?? date('m');

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $query = Sale::with(['items.product', 'user', 'location'])
                     ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                     ->orderBy('location_id')
                     ->orderBy('id');

        $sales = $this->applyRoleFilter($query, $user)->get();

        return view('reports.custom', [
    'sales' => $sales,
    'from'  => $startOfMonth->toDateString(),
    'to'    => $endOfMonth->toDateString(),
    ]);
    }

    /**
     * Custom Date Range Sales Report
     */
    public function customReport(Request $request)
    {
        $user = Auth::user();

        $from = $request->from;
        $to   = $request->to;

        $query = Sale::with(['items.product', 'user', 'location']);

        if ($from && !$to) {
            $query->whereDate('created_at', $from);
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }

        $sales = $this->applyRoleFilter($query, $user)
                      ->orderBy('created_at', 'desc')
                      ->get();

        return view('reports.custom', compact('sales', 'from', 'to'));
    }

    /**
     * Apply role-based filter for security
     */
    private function applyRoleFilter($query, $user)
    {
        if ($user->isAdmin()) {
            return $query; // Admin sees everything
        } elseif ($user->isManager()) {
            return $query->where('location_id', $user->location_id); // Manager sees only branch
        } else {
            return $query->where('user_id', $user->id); // Cashier sees own
        }
    }
     

    public function allReceipts(Request $request)
{
    $user = Auth::user();

    $from = $request->from ?? null;
    $to   = $request->to ?? null;

    $query = Sale::with(['items.product', 'user', 'location']);

    if ($from && $to) {
        $query->whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ]);
    }

    $sales = $this->applyRoleFilter($query, $user)
                  ->orderBy('created_at', 'desc')
                  ->get();

    return view('reports.receipts', compact('sales', 'from', 'to'));
}


     public function exportPdf(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $sales = Sale::with('user', 'location')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->get();

        $pdf = PDF::loadView('reports.pdf', compact('sales', 'from', 'to'));
        return $pdf->download('sales-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new SalesReportExport($request->from, $request->to), 'sales-report.xlsx');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
