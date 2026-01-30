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

        // Today's date
        $today = date('Y-m-d');

        if ($user->isAdmin()) {
            // Admin sees all sales today
            $sales = Sale::with('user', 'location', 'items.product')
                        ->whereDate('created_at', $today)
                        ->orderBy('location_id')
                        ->orderBy('id')
                        ->get();
        } else {
            // Regular user sees only their sales today
            $sales = Sale::with('items.product')
                        ->where('user_id', $user->id)
                        ->whereDate('created_at', $today)
                        ->orderBy('id')
                        ->get();
        }

        return view('reports.daily-sales', compact('sales', 'today'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
