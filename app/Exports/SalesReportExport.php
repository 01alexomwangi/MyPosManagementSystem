<?php

namespace App\Exports;

use App\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, WithHeadings
{
    protected $from;
    protected $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    public function collection()
    {
        $query = Order::with(['user', 'location']);

        if ($this->from) {
            $query->whereDate('created_at', '>=', $this->from);
        }

        if ($this->to) {
            $query->whereDate('created_at', '<=', $this->to);
        }

        return $query->get()->map(function ($order) {
            return [
                'ID'       => $order->id,
                'Date'     => $order->created_at->format('Y-m-d H:i'),
                'Cashier'  => optional($order->user)->name ?? '-',
                'Location' => optional($order->location)->name ?? '-',
                'Total'    => $order->total,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Date', 'Cashier', 'Location', 'Total'];
    }
}
