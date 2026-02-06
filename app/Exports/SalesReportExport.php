<?php

namespace App\Exports;

use App\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, WithHeadings
{
    protected $from;
    protected $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $query = Sale::with('user', 'location');

        if ($this->from) {
            $query->whereDate('created_at', '>=', $this->from);
        }

        if ($this->to) {
            $query->whereDate('created_at', '<=', $this->to);
        }

        return $query->get()->map(function($sale){
            return [
                'ID' => $sale->id,
                'Date' => $sale->created_at->format('Y-m-d H:i'),
                'Cashier' => $sale->user->name,
                'Location' => $sale->location->name,
                'Total' => $sale->total,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Date', 'Cashier', 'Location', 'Total'];
    }
}
