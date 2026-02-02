<?php

namespace App\Http\Controllers;


use App\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class ReportController extends Controller
{
    public function dailySales()
    {
        $user = Auth::user();
        $today = date('Y-m-d');

        $query = Sale::with(['items.product', 'user', 'location'])
                     ->whereDate('created_at', $today)
                     ->orderBy('location_id')
                     ->orderBy('id');

        $sales = $this->applyRoleFilter($query, $user)->get();

        return view('reports.daily-sales', compact('sales', 'today'));
    }

    public function weeklySales()
    {
        $user = Auth::user();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $query = Sale::with(['items.product','user','location'])
                     ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                     ->orderBy('location_id')
                     ->orderBy('id');

        $sales = $this->applyRoleFilter($query, $user)->get();

        return view('reports.weekly-sales', compact('sales', 'startOfWeek', 'endOfWeek'));
    }

    public function monthlySales($year = null, $month = null)
    {
        $user = Auth::user();

        $year = $year ?? date('Y');
        $month = $month ?? date('m');

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $query = Sale::with(['items.product','user','location'])
                     ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                     ->orderBy('location_id')
                     ->orderBy('id');

        $sales = $this->applyRoleFilter($query, $user)->get();

        return view('reports.monthly-sales', compact('sales', 'year', 'month'));
    }

    // Helper to filter based on user role
    private function applyRoleFilter($query, $user)
    {
        if ($user->isAdmin()) {
            return $query; // Admin sees all
        } elseif ($user->isManager()) {
            return $query->where('location_id', $user->location_id); // Manager sees only branch
        } else {
            return $query->where('user_id', $user->id); // Cashier sees own
        }
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
